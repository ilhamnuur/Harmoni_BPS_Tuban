<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\AssignmentNotification;
use App\Models\Agenda;
use App\Models\Absensi;
use App\Models\ActivityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barrier\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class AssignmentController extends Controller
{
    /**
     * DAFTAR PENUGASAN (Monitoring)
     */
    public function assignmentIndex()
    {
        $user = Auth::user();
        $query = Agenda::with(['assignee', 'activityType']);

        if ($user->role !== 'Kepala' && $user->role !== 'Admin') {
            $query->where('user_id', $user->id);
        }

        $assignments = $query->orderBy('event_date', 'asc')
            ->get()
            ->groupBy(function($item) {
                return $item->title . $item->event_date . $item->nomor_surat_tugas;
            });

        return view('assignment.index', compact('assignments'));
    }

public function assignmentCreate()
{
    // 1. Ambil semua Jenis Kegiatan
    $types = ActivityType::all();

    // 2. Ambil semua daftar Tim (Wajib dipanggil agar variabel $teams ada di Blade)
    $teams = \App\Models\Team::orderBy('nama_tim', 'asc')->get();

    // 3. Ambil Akun Khusus (Username: ketua.tim)
    $akunKhusus = User::where('username', 'ketua.tim')->first();

    // 4. Ambil data pegawai berdasarkan role (Order by nama)
    $kepalas = User::where('role', 'Kepala')->orderBy('nama_lengkap', 'asc')->get();
    
    // Katim: Ambil semua role Katim KECUALI yang usernamenya ketua.tim (biar gak double)
    $katims = User::where('role', 'Katim')
                  ->where('username', '!=', 'ketua.tim')
                  ->orderBy('nama_lengkap', 'asc')
                  ->get();

    $pegawais = User::where('role', 'Pegawai')->orderBy('nama_lengkap', 'asc')->get();

    // 5. Masukkan SEMUA variabel ke dalam compact (Termasuk 'teams')
    return view('assignment.create', compact(
        'kepalas', 
        'katims', 
        'pegawais', 
        'types', 
        'teams', 
        'akunKhusus'
    ));
}

/**
 * SIMPAN PENUGASAN (Support Backdate & Multi-User)
 */

public function assignmentStore(Request $request)
{
    $type = $request->activity_type_id;

    // 1. Validasi Rules
    $rules = [
        'team_id'           => 'required|exists:teams,id',
        'activity_type_id'  => 'required|exists:activity_types,id',
        'title'             => 'required|string|max:255',
        'assigned_to'       => 'required|array|min:1',
        'event_date'        => 'required|date',
        'report_target'     => 'required|integer|min:1',
        'mode_surat'        => 'required|in:generate,upload',
        'print_mode'        => 'nullable|in:perorang,kolektif',
        'menimbang'         => 'nullable|string',
        'mengingat'         => 'nullable|string',
        'location'          => 'nullable|string|max:255', // Tambahkan ke validasi
        'yth'               => 'nullable|string|max:255',      // Tambahkan ke validasi
    ];

    if ($request->mode_surat === 'upload') {
        $rules['nomor_surat_tugas'] = 'required|string';
        $rules['surat_tugas'] = 'required|file|mimes:pdf|max:20480';
    } else {
        $rules['content_surat'] = 'required|string';
        $rules['approver_id'] = 'required|exists:users,id';
        $rules['nomor_surat_tugas'] = 'nullable|string'; 
    }

    if ($type == 1 || $type == 3) {
        $rules['end_date'] = 'required|date|after_or_equal:event_date';
    } elseif ($type == 2) {
        $rules['notulis_id'] = 'required|exists:users,id';
        $rules['start_time'] = 'required';
    }

    $request->validate($rules);

    $stPath = null;
    if ($request->hasFile('surat_tugas')) {
        $stPath = $request->file('surat_tugas')->store('dokumen_tugas', 'public');
    }

    try {
        DB::transaction(function () use ($request, $stPath, $type) {
            
            if ($request->mode_surat === 'upload') {
                $statusApproval = 'Approved';
            } else {
                $statusApproval = (auth()->user()->role === 'Kepala') ? 'Approved' : 'Pending';
            }

            foreach ($request->assigned_to as $pegawai_id) {
                // 1. Simpan Agenda
                $agenda = Agenda::create([
                    'user_id'           => auth()->id(),
                    'team_id'           => $request->team_id,
                    'assigned_to'       => $pegawai_id,
                    'activity_type_id'  => $type,
                    'title'             => $request->title,
                    'description'       => $request->description,
                    'nomor_surat_tugas' => $request->nomor_surat_tugas ?? '-',
                    'event_date'        => $request->event_date,
                    'end_date'          => ($type == 1 || $type == 3) ? $request->end_date : $request->event_date,
                    'start_time'        => $request->start_time,
                    'notulis_id'        => ($type == 2) ? $request->notulis_id : null,
                    'surat_tugas_path'  => $stPath,
                    'status_laporan'    => 'Pending',
                    'report_target'     => $request->report_target,
                    'mode_surat'        => $request->mode_surat,
                    'status_approval'   => $statusApproval,
                    'approver_id'       => $request->approver_id,
                    'reviewer_id'       => $request->reviewer_id, 
                    'print_mode'        => $request->print_mode ?? 'perorang',
                    'menimbang'         => $request->menimbang,
                    'mengingat'         => $request->mengingat,
                    'content_surat'     => $request->content_surat,
                    'location'          => $request->location,
                    'yth'               => $request->yth,
                    'approved_at'       => ($statusApproval === 'Approved') ? now() : null,
                ]);

                // 2. KIRIM NOTIFIKASI EMAIL
                $userPegawai = User::find($pegawai_id);
                if ($userPegawai && $userPegawai->email) {
                    // Memicu pengiriman email melalui class AssignmentNotification
                    $userPegawai->notify(new AssignmentNotification($agenda));
                }
            }
        });

        return redirect()->route('assignment.index')->with('success', 'Penugasan berhasil disimpan dan notifikasi email telah dikirim!');
    } catch (\Exception $e) {
        if ($stPath) Storage::disk('public')->delete($stPath);
        return back()->withInput()->with('error', 'Gagal simpan: ' . $e->getMessage());
    }
}

/**
 * FORM EDIT (Mendukung Deteksi Anggota Rangkaian)
 */
public function assignmentEdit($id)
{
    $assignment = Agenda::with(['assignee', 'activityType'])->findOrFail($id);
    
    // Proteksi Akses
    if ($assignment->user_id !== Auth::id() && !in_array(Auth::user()->role, ['Admin', 'Kepala'])) {
        return back()->with('error', 'Akses ditolak.');
    }

    // Ambil daftar petugas yang ada di rangkaian yang sama (berdasarkan Judul, Tgl, dan No ST)
    $allAssignedIds = Agenda::where('title', $assignment->title)
        ->where('event_date', $assignment->event_date)
        ->where('nomor_surat_tugas', $assignment->nomor_surat_tugas)
        ->pluck('assigned_to')
        ->toArray();

    $types = ActivityType::all();
    $kepalas = User::where('role', 'Kepala')->orderBy('nama_lengkap', 'asc')->get();
    $katims = User::where('role', 'Katim')->orderBy('nama_lengkap', 'asc')->get();
    $pegawais = User::where('role', 'Pegawai')->orderBy('nama_lengkap', 'asc')->get();

    return view('assignment.edit', compact('assignment', 'types', 'kepalas', 'katims', 'pegawais', 'allAssignedIds'));
}

/**
 * UPDATE PENUGASAN (Sync Massal & Backdate Support)
 */
public function assignmentUpdate(Request $request, $id)
{
    $assignment = Agenda::findOrFail($id);
    $type = $request->activity_type_id;

    $rules = [
        'activity_type_id'  => 'required|exists:activity_types,id',
        'title'             => 'required|string|max:255',
        'assigned_to'       => 'required|array|min:1',
        'event_date'        => 'required|date',
        'report_target'     => 'required|integer|min:1',
        'mode_surat'        => 'required|in:generate,upload',
    ];

    if ($request->mode_surat === 'upload') {
        $rules['nomor_surat_tugas'] = 'required|string';
        $rules['surat_tugas'] = 'nullable|file|mimes:pdf|max:20480';
    } else {
        $rules['content_surat'] = 'required|string';
        $rules['approver_id'] = 'required|exists:users,id';
        
        // TAMBAHKAN VALIDASI LOKASI UNTUK MODE KETIK (RAPAT/DL)
        if ($type != 1) {
            $rules['location'] = 'required|string|max:255';
        }
    }

    if ($type == 1 || $type == 3) $rules['end_date'] = 'required|date|after_or_equal:event_date';
    if ($type == 2) $rules['notulis_id'] = 'required';

    $request->validate($rules);

    try {
        DB::transaction(function () use ($request, $assignment, $type) {
            $oldRecords = Agenda::where('title', $assignment->getOriginal('title'))
                                ->where('event_date', $assignment->getOriginal('event_date'))
                                ->where('nomor_surat_tugas', $assignment->getOriginal('nomor_surat_tugas'))
                                ->get();

            $stPath = $assignment->surat_tugas_path;
            if ($request->hasFile('surat_tugas')) {
                if ($stPath) Storage::disk('public')->delete($stPath);
                $stPath = $request->file('surat_tugas')->store('dokumen_tugas', 'public');
            }

            foreach ($oldRecords as $rec) { $rec->delete(); }

            $statusApproval = (auth()->user()->role === 'Kepala') ? 'Approved' : 'Pending';

            foreach ($request->assigned_to as $pegawai_id) {
                Agenda::create([
                    'user_id'           => auth()->id(),
                    'assigned_to'       => $pegawai_id,
                    'activity_type_id'  => $type,
                    'title'             => $request->title,
                    'description'       => $request->description,
                    'nomor_surat_tugas' => $request->nomor_surat_tugas ?? '-',
                    'event_date'        => $request->event_date,
                    'end_date'          => ($type == 1 || $type == 3) ? $request->end_date : $request->event_date,
                    'start_time'        => $request->start_time,
                    'notulis_id'        => ($type == 2) ? $request->notulis_id : null,
                    'surat_tugas_path'  => $stPath,
                    'status_laporan'    => 'Pending',
                    'report_target'     => $request->report_target,
                    'mode_surat'        => $request->mode_surat,
                    'content_surat'     => $request->content_surat,
                    
                    // --- TAMBAHKAN DUA BARIS INI AGAR LOKASI TERSIMPAN ---
                    'location'          => ($type != 1) ? $request->location : null,
                    'yth'               => ($type != 1) ? $request->yth : null,
                    // ----------------------------------------------------

                    'status_approval'   => $statusApproval,
                    'approver_id'       => $request->approver_id,
                    'approved_at'       => ($statusApproval === 'Approved') ? now() : null,
                ]);
            }
        });

        return redirect()->route('assignment.index')->with('success', 'Data penugasan berhasil diperbarui!');
    } catch (\Exception $e) {
        return back()->with('error', 'Gagal update: ' . $e->getMessage())->withInput();
    }
}

        public function approvalIndex()
{
    $user = auth()->user();
    
    // Ambil data dasar
    $query = Agenda::with(['creator', 'activityType', 'assignee'])
                    ->where('mode_surat', 'generate')
                    ->where('status_approval', 'Pending');

    if ($user->role === 'Kepala') {
        // Kepala hanya melihat tugas yang approver_id-nya adalah DIA
        $query->where('approver_id', $user->id);
    } elseif ($user->role === 'Katim') {
        // Katim hanya melihat tugas yang reviewer_id-nya adalah DIA 
        // DAN belum dia review (reviewed_at masih null)
        $query->where('reviewer_id', $user->id)
              ->whereNull('reviewed_at');
    } else {
        abort(403);
    }

    // KUNCI BIAR GAK DOUBLE: Group berdasarkan title atau nomor surat
    // Karena saat store kamu pakai foreach, maka datanya banyak. Kita cukup tampilkan satu per "surat".
    $approvals = $query->latest()
                       ->get()
                       ->unique('title'); // Mengambil satu data unik per judul tugas

    return view('assignment.approvals', compact('approvals')); // Pakai tanda kutip, tanpa $
}

public function approvalAction(Request $request, $id)
{
    // 1. Cari data yang diklik
    $currentAgenda = Agenda::findOrFail($id);
    $user = auth()->user();
    $action = $request->action; // 'approve' atau 'reject'

    // 2. KUNCI UTAMA: Cari semua baris yang punya Judul & Pembuat yang sama
    // Ini supaya semua pegawai dalam satu SPT kena update sekaligus
    $relatedAgendas = Agenda::where('title', $currentAgenda->title)
                            ->where('user_id', $currentAgenda->user_id)
                            ->where('event_date', $currentAgenda->event_date)
                            ->get();

    foreach ($relatedAgendas as $agenda) {
        if ($action === 'approve') {
            if ($user->role === 'Kepala') {
                // Jika Kepala yang klik, status SAH (Approved)
                $agenda->update([
                    'status_approval' => 'Approved',
                    'approved_at' => now()
                ]);
            } elseif ($user->role === 'Katim') {
                // Jika Katim yang klik, isi reviewed_at tapi status tetap Pending
                // supaya nanti muncul di akun Kepala
                $agenda->update([
                    'reviewed_at' => now()
                ]);
            }
        } else {
            // Jika ditolak, semua baris pegawai statusnya jadi Rejected
            $agenda->update([
                'status_approval' => 'Rejected'
            ]);
        }
    }

    $statusMsg = ($action === 'approve') ? 'disetujui' : 'ditolak';
    return redirect()->route('assignment.approvals.index')
                     ->with('success', "Seluruh baris penugasan berhasil $statusMsg!");
}

public function downloadSPT(Request $request, $id)
{
    // 1. Ambil data agenda utama
    $agenda = Agenda::with(['assignee', 'approver', 'creator'])->findOrFail($id);
    $user = auth()->user();

    // --- PROTEKSI AKSES ---
    $isOwner = ($agenda->user_id === $user->id);
    $isAssignee = ($agenda->assigned_to === $user->id);
    $isAdminAtauKepala = in_array($user->role, ['Admin', 'Kepala', 'Ketua Tim']);

    if (!$isOwner && !$isAssignee && !$isAdminAtauKepala) {
        abort(403, 'Anda tidak memiliki akses ke dokumen ini.');
    }

    // --- CEK STATUS APPROVAL ---
    if ($agenda->status_approval !== 'Approved') {
        return back()->with('error', 'SPT belum disetujui, tanda tangan belum muncul.');
    }

    // --- LOGIKA MODE CETAK (DIAMBIL DARI DATABASE) ---
    // Cek dulu dari URL, kalau tidak ada baru ambil dari database. 
    // Kalau dua-duanya kosong, baru default 'perorang'.
    $mode = $request->query('mode') ?? ($agenda->print_mode ?? 'perorang');

    // --- AMBIL SEMUA PETUGAS (GRUP PENUGASAN) ---
    $grupPetugas = Agenda::with('assignee')
    ->where('nomor_surat_tugas', $agenda->nomor_surat_tugas)
    ->where('title', $agenda->title) // Tambahkan ini agar tidak nyampur judul lain
    ->where('event_date', $agenda->event_date) // Tambahkan ini agar tidak nyampur tanggal lain
    ->orderBy('id', 'asc')
    ->get();

    // Load PDF
    $pdf = \Pdf::loadView('assignment.pdf_spt', [
        'agenda' => $agenda,
        'grupPetugas' => $grupPetugas,
        'mode' => $mode // Pastikan variabel ini terlempar ke Blade
    ]);

    $fileName = 'SPT-' . \Str::slug($agenda->title) . '-' . $mode . '.pdf';
    return $pdf->stream($fileName); 
}

    /**
     * HAPUS RANGKAIAN TUGAS
     */
    public function assignmentDestroy($id)
    {
        $assignment = Agenda::findOrFail($id);
        
        try {
            $group = Agenda::where('title', $assignment->title)
                           ->where('event_date', $assignment->event_date)
                           ->where('nomor_surat_tugas', $assignment->nomor_surat_tugas)
                           ->get();

            foreach ($group as $item) {
                if ($item->surat_tugas_path) Storage::disk('public')->delete($item->surat_tugas_path);
                $item->delete();
            }
            return back()->with('success', 'Seluruh rangkaian penugasan dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus.');
        }
    }

    /**
     * CHECK AVAILABILITY AJAX
     */
   public function checkAvailability(Request $request)
{
    try {
        $start = $request->start_date;
        $end = $request->end_date;

        // 1. Ambil Agenda per Petugas (Untuk badge kuning di samping nama)
        $agendas = Agenda::where(function($q) use ($start, $end) {
                $q->where('event_date', '<=', $end)->where('end_date', '>=', $start);
            })->get();

        $details = [];
        $busy_ids = [];
        foreach($agendas as $a) {
            $uId = (int)$a->assigned_to;
            $busy_ids[] = $uId;
            $details[$uId] = [
                'title' => $a->title,
                'date' => Carbon::parse($a->event_date)->format('d M Y') . ($a->event_date != $a->end_date ? ' s/d '.Carbon::parse($a->end_date)->format('d M Y') : '')
            ];
        }

        // 2. Fitur Baru: Cek Irisan Kegiatan Lapangan secara Global (Tanpa cek user)
        // Kita ambil kegiatan unik berdasarkan Judul agar notifikasi tidak duplikat
        $intersecting_activities = Agenda::where('activity_type_id', 1) // Khusus Tugas Lapangan
            ->where(function($q) use ($start, $end) {
                $q->where('event_date', '<=', $end)->where('end_date', '>=', $start);
            })
            ->select('title', 'event_date', 'end_date')
            ->distinct()
            ->get()
            ->map(function($item) {
                return [
                    'title' => $item->title,
                    'range' => Carbon::parse($item->event_date)->format('d M') . ' - ' . Carbon::parse($item->end_date)->format('d M Y')
                ];
            });

        // 3. Ambil Data CUTI
        $leave_users = Absensi::where(function($q) use ($start, $end) {
                $q->where('start_date', '<=', $end)->where('end_date', '>=', $start);
            })->pluck('user_id')->toArray();

        return response()->json([
            'busy_users' => array_values(array_unique(array_map('intval', $busy_ids))),
            'details' => $details,
            'leave_users' => array_values(array_unique(array_map('intval', $leave_users))),
            'global_conflicts' => $intersecting_activities // Data baru untuk notifikasi irisan
        ]);

    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}
}