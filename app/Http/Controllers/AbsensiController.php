<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Absensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    public function __construct()
    {
        config(['app.timezone' => 'Asia/Jakarta']);
        date_default_timezone_set('Asia/Jakarta');
    }

    public function absensiIndex(Request $request)
    {
        if (!$this->isSubbagUmum(Auth::user())) {
            return redirect()->route('dashboard')->with('error', 'Hanya Subbagian Umum yang dapat mengakses menu ini.');
        }

        $view = $request->get('view', 'weekly');
        $now = Carbon::now('Asia/Jakarta');
        $monthParam = $request->get('month', $now->format('Y-m'));
        
        try {
            $currentMonth = Carbon::parse($monthParam . '-01', 'Asia/Jakarta');
        } catch (\Exception $e) {
            $currentMonth = $now->copy()->startOfMonth();
        }

        if ($view == 'monthly') {
            $start = $currentMonth->copy()->startOfMonth();
            $end = $currentMonth->copy()->endOfMonth();
        } else {
            $start = ($monthParam == $now->format('Y-m')) 
                     ? $now->copy()->startOfWeek(Carbon::MONDAY) 
                     : $currentMonth->copy()->startOfMonth();
            $end = $start->copy()->addDays(6);
        }

        $period = \Carbon\CarbonPeriod::create($start->copy()->startOfDay(), $end->copy()->endOfDay());

        $users = User::with('team')->orderBy('nama_lengkap', 'asc')->get();

        // --- PERBAIKAN QUERY (Sangat Penting) ---
        // Mencari semua data yang bersinggungan dengan rentang kalender
        $allCuti = Absensi::where(function($query) use ($start, $end) {
            $s = $start->format('Y-m-d');
            $e = $end->format('Y-m-d');

            $query->where(function($q) use ($s, $e) {
                $q->where('start_date', '<=', $e)
                  ->where('end_date', '>=', $s);
            });
        })->get();

        // DEBUG: Hapus tanda komentar di bawah ini jika masih tidak muncul untuk melihat isi data
        // dd($allCuti->toArray()); 

        $prevMonth = $currentMonth->copy()->subMonth()->format('Y-m');
        $nextMonth = $currentMonth->copy()->addMonth()->format('Y-m');

        return view('absensi.index', compact(
            'users', 
            'currentMonth', 
            'prevMonth', 
            'nextMonth', 
            'period', 
            'allCuti', 
            'view'
        ));
    }

    public function absensiStore(Request $request)
{
    if (!$this->isSubbagUmum(\Auth::user())) {
        return redirect()->back()->with('error', 'Akses ditolak.');
    }

    // Validasi disesuaikan dengan simbol baru (CT, CST1, PD)
    $request->validate([
        'user_id'    => 'required|exists:users,id',
        'start_date' => 'required|date',
        'end_date'   => 'required|date|after_or_equal:start_date',
        'status'     => 'required|in:CT,CST1,PD', // Harus sesuai dengan value di modal/excel
        'keterangan' => 'nullable|string|max:255'
    ], [
        'status.in' => 'Status harus berupa CT, CST1, atau PD.'
    ]);

    try {
        \App\Models\Absensi::create([
            'user_id'    => $request->user_id,
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
            'status'     => $request->status, // Menyimpan CT/CST1/PD
            'keterangan' => $request->keterangan,
            'input_by'   => \Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Data kehadiran berhasil disimpan!');
        
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
    }
}

 public function absensiImport(Request $request)
{
    if (!$this->isSubbagUmum(\Auth::user())) {
        return redirect()->back()->with('error', 'Akses ditolak.');
    }

    $request->validate([
        'file_import' => 'required|mimes:csv,txt,xlsx,xls',
        'year_month' => 'required'
    ]);

    try {
        $file = $request->file('file_import');
        $dataArray = \Maatwebsite\Excel\Facades\Excel::toArray([], $file)[0];

        $count = 0;
        $users = \App\Models\User::all(['id', 'nip']);

        \DB::beginTransaction();

        foreach ($dataArray as $index => $row) {
            // Kita mulai looping dari index 9 (Baris ke-10 di Excel)
            if ($index < 9) continue;

            $rawNip = trim($row[0] ?? '');
            
            if (empty($rawNip) || strlen(preg_replace('/[^0-9]/', '', $rawNip)) < 5) {
                continue;
            }

            if (str_contains(strtoupper($rawNip), 'E+')) {
                $nipFull = (string) sprintf("%.0f", (float) str_replace(',', '.', $rawNip));
            } else {
                $nipFull = preg_replace('/[^0-9]/', '', $rawNip);
            }

            $nip9Digit = substr($nipFull, 0, 9);
            $user = $users->first(function($u) use ($nip9Digit) {
                $cleanDbNip = preg_replace('/[^0-9]/', '', $u->nip);
                return str_starts_with($cleanDbNip, $nip9Digit);
            });

            if (!$user) continue;

            // --- PROSES TANGGAL ---
            for ($tgl = 1; $tgl <= 31; $tgl++) {
                $colIdx = $tgl + 1; // Tanggal 1 = Index 2 (Kolom C)
                
                if (!isset($row[$colIdx])) break;

                $isiSel = trim($row[$colIdx]);
                if (empty($isiSel)) continue;

                // Pecah isi sel (handle jika ada line break)
                $lines = preg_split('/\r\n|\r|\n/', $isiSel);
                $statusRaw = strtoupper(trim(end($lines)));

                // --- PERBAIKAN MAPPER STATUS (SESUAI EXCEL & TIMELINE) ---
                // Kita simpan status kodenya saja agar sinkron dengan CSS .status-pd dll
                $statusMap = [
                    'CT'   => 'CT',   // Cuti Full
                    'CST1' => 'CST1', // Cuti Setengah Hari
                    'CT1'  => 'CST1', // Antisipasi jika di excel tulisannya CT1
                    'PD'   => 'PD',   // Perjalanan Dinas
                    'DL'   => 'PD',   // Antisipasi jika masih ada tulisan DL
                ];

                if (isset($statusMap[$statusRaw])) {
                    $tanggalStr = $request->year_month . '-' . sprintf('%02d', $tgl);
                    
                    $y = (int)substr($request->year_month, 0, 4);
                    $m = (int)substr($request->year_month, 5, 2);

                    if (checkdate($m, $tgl, $y)) {
                        \App\Models\Absensi::updateOrCreate(
                            [
                                'user_id'    => $user->id,
                                'start_date' => $tanggalStr,
                                'end_date'   => $tanggalStr,
                            ],
                            [
                                'status'     => $statusMap[$statusRaw], // Menyimpan CT, CST1, atau PD
                                'keterangan' => 'Import Presensi: ' . $statusRaw,
                                'input_by'   => \Auth::id(),
                            ]
                        );
                        $count++;
                    }
                }
            }
        }

        \DB::commit();
        return redirect()->back()->with('success', "Berhasil sinkronisasi $count data (PD/CT/CST1).");

    } catch (\Exception $e) {
        \DB::rollback();
        return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
    }
}

        public function update(Request $request, $id) {
            $absence = Absensi::findOrFail($id);
            $absence->update($request->all());
            return redirect()->back()->with('success', 'Data berhasil diupdate');
        }

        public function destroy($id) {
            Absensi::findOrFail($id)->delete();
            return redirect()->back()->with('success', 'Data berhasil dihapus');
        }

    private function isSubbagUmum($user): bool
    {
        return $user && $user->team && $user->team->nama_tim === 'Subbagian Umum';
    }
}