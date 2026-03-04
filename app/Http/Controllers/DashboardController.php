<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Agenda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | DASHBOARD UTAMA
    |--------------------------------------------------------------------------
    */

    public function index()
{
    $user = Auth::user();
    
    // 1. Persiapan Data Identitas (Pakai eager loading team di Auth jika perlu)
    $data = [
        'nama' => $user->nama_lengkap,
        'role' => $user->role,
        'tim'  => $user->team->nama_tim ?? 'Lintas Tim',
    ];

    // 2. Statistik Berdasarkan Role (Optimasi Query)
    if ($user->role == 'Pegawai') {
        // Gabungkan query count agar lebih efisien
        $stats = Agenda::where('assigned_to', $user->id)
            ->selectRaw("COUNT(CASE WHEN status_laporan = 'Pending' THEN 1 END) as pending")
            ->selectRaw("COUNT(CASE WHEN status_laporan = 'Selesai' THEN 1 END) as selesai")
            ->first();

        $data['tugas_pending'] = $stats->pending;
        $data['tugas_selesai'] = $stats->selesai;
    } else {
        $data['total_pegawai'] = ($user->role == 'Admin') 
            ? User::count() 
            : User::where('team_id', $user->team_id)->count();

        // Optimasi Statistik Admin/Katim
        $agendaStats = Agenda::query();
        if ($user->role == 'Katim') {
            $agendaStats->where('user_id', $user->id);
        }

        $stats = $agendaStats->selectRaw("COUNT(*) as total")
            ->selectRaw("COUNT(CASE WHEN status_laporan = 'Selesai' THEN 1 END) as selesai")
            ->first();

        $data['total_agenda'] = $stats->total;
        $data['tugas_selesai'] = $stats->selesai;
    }

    // 3. Query Agenda Terkini
    // Tips: Gunakan 'simplePaginate' jika data sangat besar agar tidak menghitung total record tiap load
    $agenda_terbaru = Agenda::with(['assignee', 'activityType'])
        ->when($user->role == 'Katim', function($q) use ($user) {
            $q->where(function($sq) use ($user) {
                $sq->where('assigned_to', $user->id)->orWhere('user_id', $user->id);
            });
        })
        ->when($user->role == 'Pegawai', function($q) use ($user) {
            $q->where('assigned_to', $user->id);
        })
        ->orderByRaw("CASE WHEN status_laporan = 'Pending' THEN 1 ELSE 2 END ASC")
        ->orderBy('event_date', 'asc')
        ->paginate(10); 

    return view('dashboard', array_merge($data, ['agenda_terbaru' => $agenda_terbaru]));
}


    public function allAgenda(Request $request)
{
    $user = auth()->user();
    
    // Query Dasar: Admin bisa lihat semua, Katim/Pegawai dibatasi
    $query = Agenda::with(['assignee', 'creator', 'activityType']);

    if ($user->role == 'Katim') {
        $query->where(function($q) use ($user) {
            $q->where('assigned_to', $user->id) // Tugas dia sendiri
              ->orWhere('user_id', $user->id);   // Tugas yang dia buat untuk orang lain
        });
    } elseif ($user->role == 'Pegawai') {
        $query->where('assigned_to', $user->id);
    }

    // FILTER 1: Berdasarkan Pencarian Teks
    if ($request->filled('search')) {
        $query->where('title', 'like', '%' . $request->search . '%');
    }

    // FILTER 2: Status Pengawasan (Sudah/Belum Lapor)
    if ($request->filled('status')) {
        $query->where('status_laporan', $request->status);
    }

    // FILTER 3: Tipe Kegiatan (Lapangan / Rapat)
    if ($request->filled('type')) {
        $query->where('activity_type_id', $request->type);
    }

    $allAgendas = $query->latest('event_date')->paginate(15);

    return view('agenda.all', compact('allAgendas'));
}
    public function monitoring(Request $request)
    {
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));
        $daysInMonth = Carbon::create($year, $month)->daysInMonth;
        
        $users = User::whereIn('role', ['Pegawai', 'Katim'])
            ->orderBy('role', 'asc')
            ->orderBy('nama_lengkap', 'asc')
            ->with(['agendas' => function($q) use ($month, $year) {
                $q->whereMonth('event_date', $month)
                  ->whereYear('event_date', $year)
                  ->with(['creator.team']); 
            }])
            ->get();

        return view('monitoring.index', compact('users', 'daysInMonth', 'month', 'year'));
    }

    public function panduanIndex()
    {
        return view('panduan.index');
    }
}