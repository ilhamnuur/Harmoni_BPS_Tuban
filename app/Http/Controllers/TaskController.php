<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\AgendaPhoto;
use App\Models\Absensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TaskController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LIST TASK (HANYA YANG SUDAH HARI-H)
    |--------------------------------------------------------------------------
    */
    public function taskIndex()
{
    $user = auth()->user();
    
    $tugas = Agenda::with(['activityType', 'creator'])
        ->where('assigned_to', $user->id)
        ->where('activity_type_id', 1)
        ->where('status_laporan', 'Pending')
        // TAMBAHKAN LOGIKA INI:
        ->where(function($query) {
            $query->where('mode_surat', 'upload') // Kalau upload PDF, boleh langsung muncul
                  ->orWhere(function($q) {
                      // Kalau ketik surat (generate), WAJIB sudah Approved
                      $q->where('mode_surat', 'generate')
                        ->where('status_approval', 'Approved');
                  });
        })
        ->orderBy('event_date', 'asc')
        ->get();

    return view('task.index', compact('tugas'));
}

    /*
    |--------------------------------------------------------------------------
    | FORM INPUT LAPORAN
    |--------------------------------------------------------------------------
    */
   public function taskCreate($id) 
{
    $user = auth()->user();
    $today = Carbon::now('Asia/Jakarta')->format('Y-m-d');

    // 1. Cek Proteksi Cuti (Tetap Pertahankan)
    $sedangCuti = Absensi::where('user_id', $user->id)
        ->where('status', 'Cuti')
        ->where('start_date', '<=', $today)
        ->where('end_date', '>=', $today)
        ->exists();

    if ($sedangCuti) {
        return redirect()->route('task.index')->with('error', 'Akses lapor ditolak karena Anda sedang dalam masa CUTI.');
    }

    // 2. Ambil Agenda
    // KUNCI: Hapus filter 'event_date' <= $today agar pegawai bisa mencicil laporan 
    // atau setidaknya membuka form begitu tugas diberikan.
    $agenda = Agenda::with(['activityType', 'team'])
        ->where('id', $id)
        ->where('assigned_to', $user->id)
        // ->where('event_date', '<=', $today) // BARIS INI DIHAPUS agar tidak 404
        ->firstOrFail();

    return view('task.create', compact('agenda'));
}

    /*
    |--------------------------------------------------------------------------
    | SIMPAN LAPORAN
    |--------------------------------------------------------------------------
    */
    public function taskStore(Request $request, $id) 
{
    $userId = auth()->id();
    // Pastikan agenda memang milik user yang login
    $agenda = Agenda::where('id', $id)->where('assigned_to', $userId)->firstOrFail();
    
    $request->validate([
        'kecamatan' => ['required', 'string'],
        'desa' => ['required', 'string'],
        'tanggal_pelaksanaan' => ['required', 'date'], // Ini kolom yang kita validasi
        'responden' => ['required', 'string', 'max:255'],
        'aktivitas' => ['required', 'string'],
        'permasalahan' => ['required', 'string'],
        'solusi_antisipasi' => ['required', 'string'],
        'fotos' => ['required', 'array', 'min:1', 'max:6'],
        'fotos.*' => ['image', 'mimes:jpeg,png,jpg', 'max:10240'],
    ]);

    try {
        $tglPilihan = $request->tanggal_pelaksanaan;

        // --- 1. VALIDASI SERVER-SIDE: CEK CUTI ---
        $isCuti = \App\Models\Absensi::where('user_id', $userId)
                    ->where('status', 'Cuti')
                    ->where('start_date', '<=', $tglPilihan)
                    ->where('end_date', '>=', $tglPilihan)
                    ->exists();

        if ($isCuti) {
            return back()->withInput()->with('error', 'Gagal! Anda tidak dapat menginput laporan karena Anda sedang CUTI pada tanggal tersebut.');
        }

        // --- 2. VALIDASI SERVER-SIDE: CEK TANGGAL BENTROK ---
        // Kita cek di tabel 'agendas', apakah user ini sudah punya agenda LAIN 
        // yang 'tanggal_pelaksanaan'-nya sama dengan yang diinput sekarang.
        // --- 2. VALIDASI SERVER-SIDE: CEK TANGGAL BENTROK ---
        $isBentrok = Agenda::where('assigned_to', $userId)
                        ->where('id', '!=', $id) 
                        ->where('tanggal_pelaksanaan', $tglPilihan)
                        ->where('status_laporan', 'Selesai') // TAMBAHKAN INI
                        ->exists();

        if ($isBentrok) {
            return back()->withInput()->with('error', 'Gagal! Anda sudah memiliki laporan tugas lain di tanggal yang sama. Silakan pilih tanggal lain.');
        }

        DB::beginTransaction();

        // Logika Lokasi
        $lokasiLengkap = "Desa " . $request->desa . ", Kec. " . $request->kecamatan;

        // Upload Foto
        if ($request->hasFile('fotos')) {
            foreach ($request->file('fotos') as $foto) {
                if ($foto->isValid()) {
                    $path = $foto->store('dokumentasi_tugas', 'public');
                    \App\Models\AgendaPhoto::create([
                        'agenda_id' => $agenda->id, 
                        'photo_path' => $path
                    ]);
                }
            }
        }

        // Update Data Agenda
        $agenda->update([
            'location' => $lokasiLengkap,
            'tanggal_pelaksanaan' => $tglPilihan,
            'responden' => $request->responden,
            'aktivitas' => $request->aktivitas,
            'permasalahan' => $request->permasalahan,
            'solusi_antisipasi' => $request->solusi_antisipasi,
            'status_laporan' => 'Selesai',
            'updated_at' => now()
        ]);

        DB::commit();
        return redirect()->route('history.index')->with('success', 'Laporan tugas berhasil dikirim!');

    } catch (\Exception $e) {
        DB::rollback();
        return back()->withInput()->with('error', 'Gagal menyimpan laporan: ' . $e->getMessage());
    }
}
}