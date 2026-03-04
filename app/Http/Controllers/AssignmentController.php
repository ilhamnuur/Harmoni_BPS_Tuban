<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Agenda;
use App\Models\Absensi;
use App\Models\Team;
use App\Models\ActivityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AssignmentController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | FORM BUAT PENUGASAN
    |--------------------------------------------------------------------------
    */

    public function assignmentCreate()
    {
        $user = auth()->user();
        $types = ActivityType::all();

        if ($user->role == 'Admin') {
            $katims = User::where('role', 'Katim')->orderBy('nama_lengkap', 'asc')->get();
            $pegawais = User::where('role', 'Pegawai')->orderBy('nama_lengkap', 'asc')->get();
        } else {
            $katims = User::where('role', 'Katim')->where('id', '!=', $user->id)->orderBy('nama_lengkap', 'asc')->get();
            $pegawais = User::where('role', 'Pegawai')->orderBy('nama_lengkap', 'asc')->get();
        }

        return view('assignment.create', compact('katims', 'pegawais', 'types'));
    }

    public function assignmentStore(Request $request)
    {
        $request->validate([
            'activity_type_id'  => 'required|exists:activity_types,id',
            'title'             => 'required|string|max:255',
            'description'       => 'nullable|string',
            'assigned_to'       => 'required|array|min:1',
            'event_date'        => 'required|date',
            'end_date'          => 'required|date|after_or_equal:event_date',
            'location'          => 'required|string',
            'nomor_surat_tugas' => $request->activity_type_id == 1 ? 'required|string' : 'nullable|string',
            'surat_tugas'       => 'nullable|file|mimes:pdf|max:20480',
            'materi_path'       => 'nullable|file|mimes:pdf,pptx,ppt|max:20480',
            'start_time'        => 'nullable',
            'notulis_id'        => 'nullable|exists:users,id',
        ]);

        $ids = $request->assigned_to;
        $creatorId = auth()->id();

        if (!in_array($creatorId, $ids)) {
            $ids[] = (string)$creatorId;
        }
        
        $ids = array_unique($ids);

        foreach ($ids as $pegawai_id) {
            $isCuti = Absensi::where('user_id', $pegawai_id)
                ->where('status', 'Cuti')
                ->where(function($q) use ($request) {
                    $q->whereBetween('start_date', [$request->event_date, $request->end_date])
                      ->orWhereBetween('end_date', [$request->event_date, $request->end_date]);
                })->exists();

            if ($isCuti) {
                $user = User::find($pegawai_id);
                $pesan = ($pegawai_id == $creatorId) 
                         ? "Gagal! Anda sendiri sedang dalam masa CUTI pada tanggal tersebut."
                         : "Gagal! {$user->nama_lengkap} sedang CUTI di tanggal tersebut.";
                return back()->withInput()->with('error', $pesan);
            }
        }
        
        $stPath = $request->hasFile('surat_tugas') ? $request->file('surat_tugas')->store('surat_tugas', 'public') : null;
        $materiPath = $request->hasFile('materi_path') ? $request->file('materi_path')->store('materi_rapat', 'public') : null;

        try {
            DB::transaction(function () use ($request, $ids, $stPath, $materiPath, $creatorId) {
                foreach ($ids as $pegawai_id) {
                    Agenda::create([
                        'user_id'           => $creatorId,
                        'assigned_to'       => $pegawai_id,
                        'activity_type_id'  => $request->activity_type_id,
                        'title'             => $request->title,
                        'description'       => $request->description,
                        'location'          => $request->location,
                        'nomor_surat_tugas' => $request->nomor_surat_tugas,
                        'event_date'        => $request->event_date,
                        'end_date'          => $request->end_date,
                        'start_time'        => $request->start_time,
                        'notulis_id'        => $request->notulis_id,
                        'materi_path'       => $materiPath,
                        'surat_tugas_path'  => $stPath,
                        'status_laporan'    => 'Pending',
                    ]);
                }
            });

            return redirect()->route('dashboard')->with('success', 'Penugasan berhasil dikirim!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Sistem error: ' . $e->getMessage());
        }
    }

    public function assignmentDestroy($id)
{
    $assignment = Assignment::findOrFail($id);

    // OPTIONAL: Batasi akses (misal hanya pembuat atau Admin)
    if (
        $assignment->user_id !== Auth::id() &&
        Auth::user()->role !== 'Admin'
    ) {
        return back()->with('error', 'Akses ditolak.');
    }

    try {
        $assignment->delete();

        return redirect()
            ->route('assignment.index')
            ->with('success', 'Assignment berhasil dihapus.');

    } catch (\Throwable $e) {
        return back()->with('error', 'Gagal menghapus assignment.');
    }
}

    public function checkAvailability(Request $request)
    {
        $start = $request->start_date;
        $end = $request->end_date;
        $busyUsers = Agenda::where(function($query) use ($start, $end) {
                $query->where('event_date', '<=', $end)->where('end_date', '>=', $start);
            })
            ->where('status_laporan', '!=', 'Selesai') 
            ->pluck('assigned_to')->unique()->toArray();

        return response()->json(['busy_users' => array_values(array_map('intval', $busyUsers))]);
    }

    /*
    |--------------------------------------------------------------------------
    | PRIVATE: CEK KONFLIK CUTI
    |--------------------------------------------------------------------------
    */

    private function checkCutiConflict($pegawaiIds, $start, $end, $creatorId)
    {
        foreach ($pegawaiIds as $pegawaiId) {

            $isCuti = Absensi::where('user_id', $pegawaiId)
                ->where('status', 'Cuti')
                ->where(function ($q) use ($start, $end) {
                    $q->where('start_date', '<=', $end)
                      ->where('end_date', '>=', $start);
                })
                ->exists();

            if ($isCuti) {
                $user = User::find($pegawaiId);

                return $pegawaiId == $creatorId
                    ? "Gagal! Anda sedang dalam masa cuti pada tanggal tersebut."
                    : "Gagal! {$user->nama_lengkap} sedang cuti pada tanggal tersebut.";
            }
        }

        return null;
    }
}