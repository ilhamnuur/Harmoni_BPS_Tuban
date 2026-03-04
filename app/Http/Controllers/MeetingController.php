<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\User;
use App\Models\MeetingPresence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class MeetingController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LIST MEETING (PENDING)
    |--------------------------------------------------------------------------
    */

    public function listMeeting()
    {
        // Ambil agenda rapat (ID 2) yang statusnya masih Pending
        // Meskipun sudah H+1, rapat tetap muncul di sini sampai Notulis menyelesaikannya
        $meetings = Agenda::with(['creator.team', 'notulis'])
            ->where('activity_type_id', 2)
            ->where('assigned_to', auth()->id())
            ->where('status_laporan', 'Pending')
            ->orderBy('event_date', 'desc')
            ->get();

        return view('meeting.index', compact('meetings'));
    }

    public function destroyHistory($id)
{
    $meeting = Agenda::findOrFail($id);

    // Proteksi: Hanya Notulis rapat tersebut atau Admin yang boleh hapus
    if ($meeting->notulis_id != Auth::id() && Auth::user()->role != 'Admin') {
        return back()->with('error', 'Anda tidak memiliki akses untuk menghapus riwayat ini.');
    }

    try {
        DB::beginTransaction();

        // 1. Cek dan Hapus File Dokumentasi (Jika ada)
        if ($meeting->dokumentasi_path) {
            // Karena kita simpan dalam format JSON (array path), kita decode dulu
            $files = json_decode($meeting->dokumentasi_path, true);

            if (is_array($files)) {
                // Jika isinya array (banyak foto)
                foreach ($files as $file) {
                    if (Storage::disk('public')->exists($file)) {
                        Storage::disk('public')->delete($file);
                    }
                }
            } else {
                // Jika isinya string tunggal (antisipasi data lama)
                if (Storage::disk('public')->exists($meeting->dokumentasi_path)) {
                    Storage::disk('public')->delete($meeting->dokumentasi_path);
                }
            }
        }

        // 2. Update Status atau Hapus Data? 
        // Saran: Karena ini 'Hapus Riwayat', kita hapus data notulensinya 
        // dan kembalikan status ke 'Pending' atau hapus permanen barisnya.
        
        // Opsi: Hapus Permanen (Sesuai permintaan di tampilan tadi)
        // Kita hapus semua baris agenda yang judul dan tanggalnya sama (satu rangkaian rapat)
        Agenda::where('title', $meeting->title)
            ->where('event_date', $meeting->event_date)
            ->where('activity_type_id', 2)
            ->delete();

        DB::commit();
        return redirect()->route('meeting.history')->with('success', 'Riwayat rapat dan dokumen berhasil dihapus permanen.');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
    }
}
    

    public function showPresensiMeeting($id)
    {
        $agenda = Agenda::with('creator')->where('id', $id)->where('assigned_to', auth()->id())->firstOrFail();
        $alreadySigned = MeetingPresence::where('agenda_id', $id)->where('user_id', auth()->id())->exists();
        return view('meeting.presensi', compact('agenda', 'alreadySigned'));
    }

    public function storePresensiMeeting(Request $request)
    {
        $request->validate(['agenda_id' => 'required|exists:agendas,id', 'signature' => 'required']);
        MeetingPresence::create([
            'agenda_id' => $request->agenda_id,
            'user_id'   => auth()->id(),
            'signature_base64' => $request->signature,
            'signed_at' => Carbon::now(),
        ]);
        return redirect()->route('meeting.index')->with('success', 'Tanda tangan kehadiran berhasil disimpan.');
    }

    public function monitoringKehadiran($id)
{
    // 1. Ambil data rapat utama
    $meeting = Agenda::with(['creator', 'notulis'])->findOrFail($id);

    // 2. Ambil SEMUA baris agenda yang tergabung dalam rapat ini (Grup Rapat)
    $allParticipants = Agenda::where('title', $meeting->title)
        ->where('event_date', $meeting->event_date)
        ->where('activity_type_id', 2)
        ->with('assignee.team')
        ->get();

    // Ambil daftar semua ID agenda dari grup ini
    $allAgendaIds = $allParticipants->pluck('id')->toArray();

    // 3. Ambil ID USER yang sudah melakukan presensi berdasarkan SEMUA ID agenda di atas
    // Ini kuncinya, Mail! Kita pakai whereIn bukan where biasa
    $presentUserIds = MeetingPresence::whereIn('agenda_id', $allAgendaIds)
        ->pluck('user_id')
        ->toArray();

    // 4. Hitung Statistik Sederhana
    $stats = [
        'total' => $allParticipants->count(),
        'hadir' => count($presentUserIds),
        'belum' => $allParticipants->count() - count($presentUserIds),
        'persen' => $allParticipants->count() > 0 
                    ? round((count($presentUserIds) / $allParticipants->count()) * 100) 
                    : 0
    ];

    return view('meeting.monitoring', compact('meeting', 'allParticipants', 'presentUserIds', 'stats'));
}


    public function printPresensi($id)
{
    $meeting = Agenda::findOrFail($id);
    $kepala = \App\Models\User::where('role', 'Admin')->first(); 

    // Ambil semua peserta yang satu grup rapat
    $peserta = Agenda::where('title', $meeting->title)
                    ->where('event_date', $meeting->event_date)
                    ->where('activity_type_id', 2)
                    ->with(['assignee.team'])
                    ->get();

    // Ambil semua data presensi untuk grup rapat ini sekaligus
    $allAgendaIds = $peserta->pluck('id')->toArray();
    $dataPresensi = \App\Models\MeetingPresence::whereIn('agenda_id', $allAgendaIds)
                    ->get()
                    ->keyBy('agenda_id'); // Kita index berdasarkan agenda_id agar mudah dicari

    $pdf = Pdf::loadView('meeting.pdf_presensi', [
        'meeting' => $meeting,
        'peserta' => $peserta,
        'kepala'  => $kepala,
        'dataPresensi' => $dataPresensi // Kirim data presensi kolektif
    ]);

    $pdf->setPaper('a4', 'portrait')
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'      => true,
            'chroot'               => public_path(),
        ]);

    return $pdf->stream();
}

        /**
         * Tampilkan Form Notulensi (Hanya Notulis terpilih)
         */
    public function createNotulensi($id)
    {
        // 1. Ambil data rapat utama (baris milik notulis)
        $meeting = Agenda::findOrFail($id);

        // Keamanan: Cek apakah user adalah notulis yang ditunjuk
        if ($meeting->notulis_id != auth()->id()) {
            return redirect()->route('meeting.index')->with('error', 'Anda bukan notulis rapat ini.');
        }

        // 2. Ambil SEMUA baris agenda yang satu grup (Judul & Tanggal sama)
        $semuaPeserta = Agenda::where('title', $meeting->title)
                            ->where('event_date', $meeting->event_date)
                            ->where('activity_type_id', 2)
                            ->with('assignee')
                            ->get();

        // 3. Ambil daftar ID Agenda dalam grup ini
        $allAgendaIds = $semuaPeserta->pluck('id')->toArray();

        // 4. Ambil User ID yang SUDAH TTD berdasarkan daftar ID Agenda di atas
        // Kita gunakan whereIn agar semua TTD teman-teman muncul
        $userSudahHadir = MeetingPresence::whereIn('agenda_id', $allAgendaIds)
                                        ->pluck('user_id')
                                        ->toArray();

        return view('meeting.notulensi', compact('meeting', 'semuaPeserta', 'userSudahHadir'));
    }

    /**
     * Simpan Notulensi & Mass-Update Status Jadi SELESAI
     */
public function storeNotulensi(Request $request, $id)
{
    // 1. Ambil data rapat utama
    $meeting = Agenda::with('creator')->findOrFail($id);

    // 2. Validasi (Gunakan .* untuk memvalidasi setiap file di dalam array)
    $request->validate([
        'hasil_rapat'          => 'required|string|min:20',
        'foto_dokumentasi'     => 'required|array|min:1', // Pastikan ini array
        'foto_dokumentasi.*'   => 'image|mimes:jpg,jpeg,png|max:5120', // Validasi tiap file
    ], [
        'hasil_rapat.min'             => 'Hasil rapat (notulensi) terlalu singkat.',
        'foto_dokumentasi.required'    => 'Wajib upload minimal 1 foto dokumentasi rapat.',
        'foto_dokumentasi.*.image'     => 'File harus berupa gambar.',
        'foto_dokumentasi.*.max'       => 'Ukuran foto maksimal 5MB per file.',
    ]);

    try {
        DB::beginTransaction();

        // 3. Proses Upload Multiple File
        $paths = [];
        if ($request->hasFile('foto_dokumentasi')) {
            foreach ($request->file('foto_dokumentasi') as $file) {
                // Simpan foto satu per satu ke folder dokumentasi_rapat
                $path = $file->store('dokumentasi_rapat', 'public');
                $paths[] = $path; // Masukkan path ke array
            }
        }

        // 4. Update Masal (Sync Notulensi ke semua peserta rapat yang sama)
        Agenda::where('title', $meeting->title)
            ->where('event_date', $meeting->event_date)
            ->where('activity_type_id', 2)
            ->update([
                'notulensi_hasil'  => $request->hasil_rapat,
                // Simpan array path sebagai JSON agar bisa dibaca di Detail History
                'dokumentasi_path' => json_encode($paths), 
                'status_laporan'   => 'Selesai',
                'updated_at'       => now()
            ]);

        DB::commit();
        return redirect()->route('meeting.history')->with('success', 'Rapat selesai dan diarsipkan!');

    } catch (\Exception $e) {
        DB::rollBack();
        // Hapus file yang terlanjur terupload jika gagal (Opsional)
        return back()->withInput()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
    }
}

    public function updateNotulensi(Request $request, $id)
{
    $meeting = Agenda::findOrFail($id);

    // Proteksi: Hanya notulis atau admin
    if ($meeting->notulis_id != Auth::id() && Auth::user()->role != 'Admin') {
        return back()->with('error', 'Akses ditolak.');
    }

    $request->validate([
        'hasil_rapat'          => 'required|string|min:20',
        'foto_dokumentasi.*'   => 'image|mimes:jpg,jpeg,png|max:5120',
    ]);

    try {
        DB::beginTransaction();

        // Ambil data path lama (JSON)
        $paths = json_decode($meeting->dokumentasi_path, true) ?? [];

        // Jika ada upload foto baru
        if ($request->hasFile('foto_dokumentasi')) {
            // Opsi: Hapus foto lama dari storage kalau mau ganti total
            foreach($paths as $oldFile) {
                if (Storage::disk('public')->exists($oldFile)) {
                    Storage::disk('public')->delete($oldFile);
                }
            }
            
            // Reset array paths untuk diisi foto baru
            $paths = [];
            foreach ($request->file('foto_dokumentasi') as $file) {
                $paths[] = $file->store('dokumentasi_rapat', 'public');
            }
        }

        // Update semua data rapat yang terkait
        Agenda::where('title', $meeting->title)
            ->where('event_date', $meeting->event_date)
            ->where('activity_type_id', 2)
            ->update([
                'notulensi_hasil'  => $request->hasil_rapat,
                'dokumentasi_path' => json_encode($paths),
                'updated_at'       => now()
            ]);

        DB::commit();
        return redirect()->route('meeting.history')->with('success', 'Notulensi berhasil diperbarui!');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Gagal update: ' . $e->getMessage());
    }
}

    public function listMeetingHistory(Request $request)
{
    $user = auth()->user();
    
    // 1. Query dasar: Hanya rapat yang sudah SELESAI
    $query = Agenda::with(['notulis', 'creator'])
                  ->where('activity_type_id', 2)
                  ->where('status_laporan', 'Selesai');

    // 2. Filter berdasarkan ROLE (Keamanan)
    if ($user->role == 'Katim') {
        $query->where(function($q) use ($user) {
            $q->where('assigned_to', $user->id)
              ->orWhere('user_id', $user->id);
        });
    } elseif ($user->role == 'Pegawai') {
        $query->where('assigned_to', $user->id);
    }
    // Jika Admin, biarkan saja (Admin bisa lihat semua)

    // 3. Filter Search
    if ($request->filled('search')) {
        $query->where('title', 'like', '%' . $request->search . '%');
    }

    // 4. Mencegah Duplikasi (Grouping)
    // Kita ambil datanya dulu, lalu kita unikkan berdasarkan Title dan Tanggal
    $historyMeetings = $query->orderBy('event_date', 'desc')
                             ->get()
                             ->unique(function ($item) {
                                 return $item->title . $item->event_date;
                             });

    return view('meeting.history', compact('historyMeetings'));
}

    public function detailHistory($id)
    {
        // 1. Ambil data rapat utama
        $meeting = Agenda::with(['creator', 'notulis'])->findOrFail($id);

        // 2. Ambil semua peserta dalam grup rapat ini
        $semuaPeserta = Agenda::where('title', $meeting->title)
                            ->where('event_date', $meeting->event_date)
                            ->where('activity_type_id', 2)
                            ->with('assignee.team')
                            ->get();

        // 3. Ambil data presensi untuk grup ini
        $userSudahHadir = \App\Models\MeetingPresence::whereIn('agenda_id', $semuaPeserta->pluck('id'))
                                                    ->pluck('user_id')
                                                    ->toArray();

        return view('meeting.detail_history', compact('meeting', 'semuaPeserta', 'userSudahHadir'));
    }

    /*
    |--------------------------------------------------------------------------
    | PRIVATE HELPERS
    |--------------------------------------------------------------------------
    */

    private function getMeetingGroup($meeting)
    {
        return Agenda::where([
                'title' => $meeting->title,
                'event_date' => $meeting->event_date,
                'activity_type_id' => 2
            ])
            ->with('assignee.team')
            ->get();
    }

    private function authorizeNotulisOrAdmin($meeting): bool
    {
        return $meeting->notulis_id === Auth::id()
            || Auth::user()->role === 'Admin';
    }

    private function deleteDocumentationFiles($jsonPath)
    {
        $files = json_decode($jsonPath, true);

        if (!is_array($files)) {
            $files = [$jsonPath];
        }

        foreach ($files as $file) {
            if (Storage::disk('public')->exists($file)) {
                Storage::disk('public')->delete($file);
            }
        }
    }
}