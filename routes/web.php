<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController, DashboardController, ProfileController, 
    MeetingController, TaskController, RekapController, 
    AnggotaController, AssignmentController, AbsensiController, 
    HistoryController, SuperAccessController
};

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'loginAction'])->name('login.action');
});

/*
|--------------------------------------------------------------------------
| Auth Routes (Global - Akses untuk semua Role yang Login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    /* --- Profile & Global --- */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/super-access', [SuperAccessController::class, 'index'])->name('super.access.index');
    Route::get('/panduan', [DashboardController::class, 'panduanIndex'])->name('panduan.index');
    Route::get('/assignment/{id}/download-spt', [AssignmentController::class, 'downloadSPT'])->name('assignment.download-spt');

    Route::prefix('history')->group(function () {
    Route::get('/', [HistoryController::class, 'historyIndex'])->name('history.index');
    Route::get('/detail/{id}', [HistoryController::class, 'historyDetail'])->name('history.detail');
    Route::get('/rekap-pdf', [HistoryController::class, 'exportRekapPDF'])->name('history.pdf_rekap');
    Route::get('/rekap-excel', [HistoryController::class, 'exportRekapExcel'])->name('history.excel_rekap');
    });


    /* | ROUTE HISTORY & DETAIL (Dikeluarkan dari is-pegawai agar Kepala/Super bisa akses)
    |--------------------------------------------------------------------------
    */
    Route::prefix('meeting')->group(function () {
        // List History
        Route::get('/history', [MeetingController::class, 'listMeetingHistory'])->name('meeting.history');
        Route::get('/history-dinas', [MeetingController::class, 'listDinasHistory'])->name('meeting.history.dinas');
        
        // Detail View (Pusat Monitoring Kepala & Super Access)
        Route::get('/history/detail/{id}', [MeetingController::class, 'detailHistory'])->name('meeting.history.detail');
        Route::get('/history/detail-dinas/{id}', [MeetingController::class, 'detailDinasHistory'])->name('meeting.history.detail_dinas');
        
        // Monitoring & Print
        Route::get('/monitoring/{id}', [MeetingController::class, 'monitoringKehadiran'])->name('meeting.monitoring');
        Route::get('/print-presensi/{id}', [MeetingController::class, 'printPresensi'])->name('meeting.print_presensi');
    });

    /* --- ROLE: BUKAN ADMIN (Dashboard & Monitoring) --- */
    Route::middleware('can:is-not-admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/monitoring', [DashboardController::class, 'monitoring'])->name('monitoring.index');
        Route::get('/agenda', [DashboardController::class, 'allAgenda'])->name('agenda.all');
    });

    /* --- ROLE: KEPALA & KATIM (Assignment) --- */
    Route::middleware('can:access-assignment')->group(function () {
        Route::prefix('assignment')->group(function () {
            Route::get('/', [AssignmentController::class, 'assignmentIndex'])->name('assignment.index');
            Route::get('/create', [AssignmentController::class, 'assignmentCreate'])->name('assignment.create');
            Route::post('/store', [AssignmentController::class, 'assignmentStore'])->name('assignment.store');
            Route::get('/{id}/edit', [AssignmentController::class, 'assignmentEdit'])->name('assignment.edit');
            Route::put('/{id}', [AssignmentController::class, 'assignmentUpdate'])->name('assignment.update');
            Route::delete('/{id}', [AssignmentController::class, 'assignmentDestroy'])->name('assignment.destroy');
            Route::get('/check-availability', [AssignmentController::class, 'checkAvailability'])->name('assignment.check-availability');
            Route::get('/approvals', [AssignmentController::class, 'approvalIndex'])->name('assignment.approvals.index');
            Route::post('/approvals/{id}/action', [AssignmentController::class, 'approvalAction'])->name('assignment.approvals.action');
            
        });
    });

    /* --- ROLE: ADMIN & KEPALA (Manajemen User) --- */
    Route::middleware('can:access-manajemen-user')->group(function () {
        Route::prefix('manajemen/anggota')->group(function () {
            Route::get('/', [AnggotaController::class, 'anggotaIndex'])->name('manajemen.anggota');
            
            Route::middleware('can:is-admin')->group(function () {
                Route::get('/create', [AnggotaController::class, 'anggotaCreate'])->name('manajemen.anggota.create');
                Route::post('/store', [AnggotaController::class, 'anggotaStore'])->name('manajemen.anggota.store');
                Route::get('/{id}/edit', [AnggotaController::class, 'anggotaEdit'])->name('manajemen.anggota.edit');
                Route::put('/{id}', [AnggotaController::class, 'anggotaUpdate'])->name('manajemen.anggota.update');
                Route::delete('/{id}', [AnggotaController::class, 'anggotaDestroy'])->name('manajemen.anggota.destroy');
            });
        });
    });

    /* --- ROLE: PEGAWAI ONLY (Pelaksanaan & Input) --- */
    Route::middleware('can:is-pegawai')->group(function () {
        
        /* Task & History Lapangan */
        Route::prefix('task')->group(function () {
            Route::get('/', [TaskController::class, 'taskIndex'])->name('task.index');
            Route::middleware(['cek.cuti'])->group(function () {
                Route::get('/isi/{id}', [TaskController::class, 'taskCreate'])->name('task.create');
                Route::post('/simpan/{id}', [TaskController::class, 'taskStore'])->name('task.store');
            });
        });

        Route::prefix('history')->group(function () {
            Route::get('/edit/{id}', [HistoryController::class, 'historyEdit'])->name('history.edit');
            Route::put('/update/{id}', [HistoryController::class, 'historyUpdate'])->name('history.update');
            Route::get('/export/{id}', [HistoryController::class, 'historyExport'])->name('history.export');
            Route::delete('/task-destroy/{id}', [HistoryController::class, 'taskDestroy'])->name('history.task_destroy');
        });

        /* Meeting & Notulensi (Proses Input/Edit) */
        Route::prefix('meeting')->group(function () {
            Route::get('/', [MeetingController::class, 'listMeeting'])->name('meeting.index');
            
            // Notulensi & Dinas Store/Update
            Route::get('/notulensi/{id}', [MeetingController::class, 'createNotulensi'])->name('meeting.notulensi');
            Route::post('/notulensi/{id}', [MeetingController::class, 'storeNotulensi'])->name('meeting.notulensi.store');
            Route::put('/notulensi/{id}/update', [MeetingController::class, 'updateNotulensi'])->name('meeting.notulensi.update');

            // Presensi
            Route::get('/presensi/{id}', [MeetingController::class, 'showPresensiMeeting'])->name('meeting.presensi');
            Route::post('/presensi/simpan', [MeetingController::class, 'storePresensiMeeting'])->name('meeting.presensi.store');

            // Delete History (Hanya Pegawai/Pemilik)
            Route::delete('/history/delete/{id}', [MeetingController::class, 'destroyHistory'])->name('meeting.history.destroy');

            // Dinas Luar Input
            Route::get('/dinas-luar/{id}', [MeetingController::class, 'createNotulensi'])->name('meeting.dinas.create');
            Route::post('/dinas-luar/{id}/store', [MeetingController::class, 'storeDinasLuar'])->name('meeting.dinas.store');
            Route::put('/dinas-luar/{id}/update', [MeetingController::class, 'updateDinasLuar'])->name('meeting.dinas.update');
        });
    });

    /* --- MODULE: ABSENSI --- */
    Route::middleware('can:access-absensi')->group(function () {
        Route::prefix('absensi')->group(function () {
            Route::get('/', [AbsensiController::class, 'absensiIndex'])->name('absensi.index');
            Route::post('/store', [AbsensiController::class, 'absensiStore'])->name('absensi.store');
            Route::post('/absensi/import', [AbsensiController::class, 'absensiImport'])->name('absensi.import');
            Route::put('/absensi/{id}', [AbsensiController::class, 'update'])->name('absensi.update');
            Route::delete('/absensi/{id}', [AbsensiController::class, 'destroy'])->name('absensi.destroy');
        });
    });
});