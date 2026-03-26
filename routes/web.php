<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DiagnosaController;
use App\Http\Controllers\GejalaController;
use App\Http\Controllers\KerusakanController;
use App\Http\Controllers\CaseController;
use App\Http\Controllers\RiwayatController;
use App\Http\Controllers\RetainController;
use App\Http\Controllers\CbrSettingController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\EvaluasiController;

/*
|--------------------------------------------------------------------------
| ROOT & DASHBOARD REDIRECT
|--------------------------------------------------------------------------
*/
Route::get('/', [DashboardController::class, 'root']);
Route::get('/dashboard', [DashboardController::class, 'redirectDashboard'])
    ->middleware('auth')
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| DASHBOARD
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/admin', [DashboardController::class, 'admin'])
        ->middleware('role:admin,teknisi')
        ->name('admin.dashboard');

    Route::post('/admin/export/pdf', [DashboardController::class, 'exportPdf'])
        ->middleware('role:admin,teknisi')
        ->name('admin.export.pdf');

    Route::get('/teknisi', [DashboardController::class, 'teknisi'])
        ->middleware('role:teknisi')
        ->name('teknisi.dashboard');

    Route::get('/user', [DashboardController::class, 'user'])
        ->middleware('role:user')
        ->name('user.dashboard');
});

/*
|--------------------------------------------------------------------------
| DIAGNOSA
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/diagnosa', [DiagnosaController::class, 'index'])->name('diagnosa.form');
    Route::post('/diagnosa', [DiagnosaController::class, 'process'])->name('diagnosa.run');
    Route::get('/diagnosa/result', [DiagnosaController::class, 'result'])->name('diagnosa.result');
});

/*
|--------------------------------------------------------------------------
| RIWAYAT
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat.index');
    Route::get('/riwayat/{id}', [RiwayatController::class, 'show'])->name('riwayat.show');
});

/*
|--------------------------------------------------------------------------
| PUBLIC CASES (READ ONLY)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/cases', [CaseController::class, 'publicIndex'])->name('cases.index');
    Route::get('/cases/{id}', [CaseController::class, 'publicShow'])->name('cases.show');
});

/*
|--------------------------------------------------------------------------
| ADMIN - CASES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin,teknisi'])->group(function () {
    Route::get('/admin/cases', [CaseController::class, 'index'])->name('admin.cases.index');
    Route::get('/admin/cases/create', [CaseController::class, 'create'])->name('admin.cases.create');
    Route::post('/admin/cases', [CaseController::class, 'store'])->name('admin.cases.store');
    Route::get('/admin/cases/{id}/edit', [CaseController::class, 'edit'])->name('admin.cases.edit');
    Route::post('/admin/cases/{id}/update', [CaseController::class, 'update'])->name('admin.cases.update');
    Route::post('/admin/cases/{id}/delete', [CaseController::class, 'delete'])->name('admin.cases.delete');
});

/*
|--------------------------------------------------------------------------
| ADMIN - GEJALA
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin,teknisi'])->group(function () {
    Route::get('/admin/symptoms', [GejalaController::class, 'index'])->name('admin.symptoms.index');
    Route::get('/admin/symptoms/create', [GejalaController::class, 'create'])->name('admin.symptoms.create');
    Route::post('/admin/symptoms', [GejalaController::class, 'store'])->name('admin.symptoms.store');
    Route::get('/admin/symptoms/{id}/edit', [GejalaController::class, 'edit'])->name('admin.symptoms.edit');
    Route::post('/admin/symptoms/{id}/update', [GejalaController::class, 'update'])->name('admin.symptoms.update');
    Route::post('/admin/symptoms/{id}/delete', [GejalaController::class, 'delete'])->name('admin.symptoms.delete');
});

/*
|--------------------------------------------------------------------------
| ADMIN - KERUSAKAN
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin,teknisi'])->group(function () {
    Route::get('/admin/damages', [KerusakanController::class, 'index'])->name('admin.damages.index');
    Route::get('/admin/damages/create', [KerusakanController::class, 'create'])->name('admin.damages.create');
    Route::post('/admin/damages', [KerusakanController::class, 'store'])->name('admin.damages.store');
    Route::get('/admin/damages/{id}/edit', [KerusakanController::class, 'edit'])->name('admin.damages.edit');
    Route::post('/admin/damages/{id}/update', [KerusakanController::class, 'update'])->name('admin.damages.update');
    Route::post('/admin/damages/{id}/delete', [KerusakanController::class, 'delete'])->name('admin.damages.delete');
});

/*
|--------------------------------------------------------------------------
| RETAIN
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin,teknisi'])->group(function () {
    Route::get('/retain', [RetainController::class, 'index'])->name('retain.index');
    Route::get('/retain/{id}', [RetainController::class, 'show'])->name('retain.show');
    Route::post('/retain/{id}/approve', [RetainController::class, 'approve'])->name('retain.approve');
    Route::post('/retain/{id}/reject', [RetainController::class, 'reject'])->name('retain.reject');
});

/*
|--------------------------------------------------------------------------
| USER PENDING
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/user/pending', [RetainController::class, 'userPendingIndex'])->name('user.pending.index');
    Route::get('/user/pending/{id}', [RetainController::class, 'userPendingShow'])->name('user.pending.show');
});

/*
|--------------------------------------------------------------------------
| CBR SETTINGS
|--------------------------------------------------------------------------
*/
Route::middleware(['auth','role:admin,teknisi'])->group(function () {
    Route::get('/settings/cbr', [CbrSettingController::class, 'index'])->name('settings.cbr');
    Route::post('/settings/cbr', [CbrSettingController::class, 'update'])->name('settings.cbr.update');
});

/*
|--------------------------------------------------------------------------
| EVALUASI / VALIDASI PAKAR
|--------------------------------------------------------------------------
*/
Route::middleware(['auth','role:admin,teknisi'])->group(function () {
    Route::get('/evaluasi', [EvaluasiController::class, 'index'])->name('evaluasi.index');
    Route::get('/evaluasi/{id}', [EvaluasiController::class, 'show'])->name('evaluasi.show');
    Route::post('/evaluasi/{id}', [EvaluasiController::class, 'store'])->name('evaluasi.store');
});

require __DIR__.'/auth.php';