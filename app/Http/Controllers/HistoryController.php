<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\AgendaPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class HistoryController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LIST RIWAYAT LAPORAN LAPANGAN
    |--------------------------------------------------------------------------
    */

    public function historyIndex(Request $request)
{
    $user = auth()->user();
    
    // 1. Tambahkan filter activity_type_id = 1 (Lapangan)
    // Gunakan with() untuk eager loading agar aplikasi tidak lambat
    $query = Agenda::with(['assignee', 'activityType', 'creator', 'assignee.team'])
                ->where('activity_type_id', 1) 
                ->where('status_laporan', 'Selesai');

    // 2. Logika Hak Akses (Role)
    if ($user->role == 'Katim') {
        // Katim melihat laporannya sendiri + laporan yang dia buat untuk anggotanya
        $query->where(function($q) use ($user) {
            $q->where('assigned_to', $user->id)
              ->orWhere('user_id', $user->id);
        });
    } elseif ($user->role == 'Pegawai') {
        // Pegawai hanya melihat laporannya sendiri
        $query->where('assigned_to', $user->id);
    }
    // Admin tidak difilter (bisa lihat semua)

    // 3. Logika Pencarian
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('title', 'like', '%' . $search . '%')
              ->orWhere('location', 'like', '%' . $search . '%')
              ->orWhereHas('assignee', function($userQuery) use ($search) {
                  $userQuery->where('nama_lengkap', 'like', '%' . $search . '%');
              });
        });
    }

    // 4. Urutkan berdasarkan yang terbaru selesai & gunakan Paginate
    // Pakai paginate(10) supaya rapi ada pembagian halaman di tabel
    $riwayat = $query->latest('updated_at')->paginate(10);

    return view('history.index', compact('riwayat'));
}

    public function historyDetail($id)
    {
        $agenda = Agenda::with(['assignee', 'activityType', 'photos'])->findOrFail($id);
        return view('history.detail', compact('agenda'));
    }

    public function historyEdit($id)
    {
        $agenda = Agenda::with(['activityType', 'photos'])->findOrFail($id);
        if (auth()->user()->role == 'Pegawai' && $agenda->assigned_to != auth()->id()) {
            return redirect()->route('history.index')->with('error', 'Akses ditolak.');
        }
        return view('history.edit', compact('agenda'));
    }

    public function historyUpdate(Request $request, $id)
    {
        $agenda = Agenda::findOrFail($id);
        $request->validate([
            'tanggal_pelaksanaan' => 'required|date',
            'responden' => 'required',
            'aktivitas' => 'required',
            'permasalahan' => 'required',
            'solusi_antisipasi' => 'required',
            'fotos.*' => 'image|mimes:jpeg,png,jpg|max:10240',
        ]);

        try {
            DB::beginTransaction();
            $agenda->update($request->only(['tanggal_pelaksanaan', 'responden', 'aktivitas', 'permasalahan', 'solusi_antisipasi']));
            if ($request->hasFile('fotos')) {
                foreach ($request->file('fotos') as $foto) {
                    $path = $foto->store('dokumentasi', 'public');
                    AgendaPhoto::create(['agenda_id' => $agenda->id, 'photo_path' => $path]);
                }
            }
            DB::commit();
            return redirect()->route('history.index')->with('success', 'Laporan diperbarui!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function exportPDF($id) 
    {
        Carbon::setLocale('id');
        $agenda = Agenda::with(['assignee', 'activityType', 'photos'])->findOrFail($id);
        $pdf = Pdf::loadView('history.pdf', compact('agenda'))->setPaper('a4', 'portrait');
        return $pdf->download('Laporan_BPS_' . $agenda->id . '.pdf');
    }
    /*
    |--------------------------------------------------------------------------
    | PRIVATE: CEK AKSES ROLE
    |--------------------------------------------------------------------------
    */

    private function canAccess($agenda): bool
    {
        $user = Auth::user();

        if ($user->role === 'Admin') {
            return true;
        }

        if ($user->role === 'Katim') {
            return $agenda->assigned_to == $user->id
                || $agenda->user_id == $user->id;
        }

        if ($user->role === 'Pegawai') {
            return $agenda->assigned_to == $user->id;
        }

        return false;
    }
}