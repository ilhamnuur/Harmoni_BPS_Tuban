<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\RekapController;
use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\HistoryController;

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
| Auth Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/monitoring', [DashboardController::class, 'monitoring'])->name('monitoring.index');
    Route::get('/agenda', [DashboardController::class, 'allAgenda'])->name('agenda.all');
    Route::get('/panduan', [DashboardController::class, 'panduanIndex'])->name('panduan.index');


    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


    /*
    |--------------------------------------------------------------------------
    | MODULE: MEETING
    |--------------------------------------------------------------------------
    */
    Route::prefix('meeting')->group(function () {

        Route::get('/', [MeetingController::class, 'listMeeting'])->name('meeting.index');
        Route::get('/history', [MeetingController::class, 'listMeetingHistory'])->name('meeting.history');

        Route::get('/presensi/{id}', [MeetingController::class, 'showPresensiMeeting'])->name('meeting.presensi');
        Route::post('/presensi', [MeetingController::class, 'storePresensiMeeting'])->name('meeting.presensi.store');

        Route::get('/notulensi/{id}', [MeetingController::class, 'createNotulensi'])->name('meeting.notulensi');
        Route::post('/notulensi/{id}', [MeetingController::class, 'storeNotulensi'])->name('meeting.notulensi.store');
        Route::put('/meeting/notulensi/{id}/update', [MeetingController::class, 'updateNotulensi'])->name('meeting.notulensi.update');

        Route::get('/monitoring/{id}', [MeetingController::class, 'monitoringKehadiran'])->name('meeting.monitoring');
        Route::get('/print/{id}', [MeetingController::class, 'printPresensi'])->name('meeting.print_presensi');

        Route::delete('/history/{id}', [MeetingController::class, 'destroyHistory'])->name('meeting.history.destroy');
        Route::get('/history/{id}/detail', [MeetingController::class, 'detailHistory'])->name('meeting.history.detail');
    });


    /*
    |--------------------------------------------------------------------------
    | MODULE: HISTORY
    |--------------------------------------------------------------------------
    */
    Route::prefix('history')->group(function () {

        Route::get('/', [HistoryController::class, 'historyIndex'])->name('history.index');
        Route::get('/detail/{id}', [HistoryController::class, 'historyDetail'])->name('history.detail');
        Route::get('/cetak/{id}', [HistoryController::class, 'exportPDF'])->name('history.export');
        Route::get('/edit/{id}', [HistoryController::class, 'historyEdit'])->name('history.edit');
        Route::put('/update/{id}', [HistoryController::class, 'historyUpdate'])->name('history.update');
    });


    /*
    |--------------------------------------------------------------------------
    | MODULE: REKAP
    |--------------------------------------------------------------------------
    */
    Route::prefix('history')->group(function () {

    Route::get('/rekap_pdf', [RekapController::class, 'exportRekapPDF'])->name('history.pdf_rekap');
    Route::get('/rekap_excel', [RekapController::class, 'exportRekapExcel'])->name('history.excel_rekap');
});


    /*
    |--------------------------------------------------------------------------
    | MODULE: TASK
    |--------------------------------------------------------------------------
    */
    Route::prefix('task')->group(function () {

        Route::get('/', [TaskController::class, 'taskIndex'])->name('task.index');
        Route::get('/isi/{id}', [TaskController::class, 'taskCreate'])->name('task.create');
        Route::post('/simpan/{id}', [TaskController::class, 'taskStore'])->name('task.store');
    });


    /*
    |--------------------------------------------------------------------------
    | MODULE: ABSENSI
    |--------------------------------------------------------------------------
    */
    Route::prefix('absensi')->group(function () {

        Route::get('/', [AbsensiController::class, 'absensiIndex'])->name('absensi.index');
        Route::post('/store', [AbsensiController::class, 'absensiStore'])->name('absensi.store');
    });


    /*
    |--------------------------------------------------------------------------
    | ADMIN & KATIM ONLY
    |--------------------------------------------------------------------------
    */
    Route::middleware('can:access-admin-katim')->group(function () {

        /*
        |--------------------------------------------------------------------------
        | ASSIGNMENT
        |--------------------------------------------------------------------------
        */
        Route::prefix('assignment')->group(function () {

            Route::get('/create', [AssignmentController::class, 'assignmentCreate'])->name('assignment.create');
            Route::post('/store', [AssignmentController::class, 'assignmentStore'])->name('assignment.store');
            Route::get('/check-availability', [AssignmentController::class, 'checkAvailability'])->name('assignment.check-availability');
            Route::delete('/{id}', [AssignmentController::class, 'assignmentDestroy'])->name('assignment.destroy');
        });


        /*
        |--------------------------------------------------------------------------
        | MANAJEMEN ANGGOTA
        |--------------------------------------------------------------------------
        */
        Route::prefix('manajemen/anggota')->group(function () {

            Route::get('/', [AnggotaController::class, 'anggotaIndex'])->name('manajemen.anggota');
            Route::get('/create', [AnggotaController::class, 'anggotaCreate'])->name('manajemen.anggota.create');
            Route::post('/store', [AnggotaController::class, 'anggotaStore'])->name('manajemen.anggota.store');
            Route::get('/{id}/edit', [AnggotaController::class, 'anggotaEdit'])->name('manajemen.anggota.edit');
            Route::put('/{id}', [AnggotaController::class, 'anggotaUpdate'])->name('manajemen.anggota.update');
            Route::delete('/{id}', [AnggotaController::class, 'anggotaDestroy'])->name('manajemen.anggota.destroy');
        });
    });
});