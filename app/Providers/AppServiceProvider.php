<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate; 
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Agenda; // Pastikan Model Agenda sudah dibuat
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // 1. Perbaikan Tampilan Pagination Bootstrap 5
        Paginator::useBootstrapFive();

        // 2. Gate Hak Akses
        Gate::define('access-admin-katim', function (User $user) {
            return in_array($user->role, ['Admin', 'Katim']);
        });

        Gate::define('access-pegawai', function (User $user) {
            return $user->role === 'Pegawai';
        });

        // 3. LOGIKA NOTIFIKASI BADGE (Tugas Lapangan & Rapat)
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $user = Auth::user();
                
                // Hitung Tugas Lapangan (Pending)
                // Asumsi: activity_type_id 1 = Lapangan
                $countLapangan = Agenda::where('assigned_to', $user->id)
                                    ->where('status_laporan', 'Pending')
                                    ->where('activity_type_id', 1) 
                                    ->count();

                // Hitung Agenda Rapat (Belum Absen)
                // Asumsi: activity_type_id 2 = Rapat
                // Kita hitung yang statusnya belum 'Selesai' dan ditugaskan ke user
                $countRapat = Agenda::where('assigned_to', $user->id)
                                    ->where('status_laporan', 'Pending')
                                    ->where('activity_type_id', 2)
                                    ->count();
                
                $view->with([
                    'notifLapanganCount' => $countLapangan,
                    'notifRapatCount'    => $countRapat
                ]);
            }
        });
    }
}