<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\CekAksesManajemen;
use App\Http\Controllers\KeuanganController;

// 1. Route Login & Logout
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// 2. Route Halaman Utama (Wajib Login)
Route::middleware('auth')->group(function () {
    Route::get('/', function () { return redirect('/dashboard'); });
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/absensi', function () { return view('absensi.index'); });
    
    // Laporan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::post('/laporan', [LaporanController::class, 'store'])->name('laporan.store');
    Route::get('/laporan/{id}', [LaporanController::class, 'show'])->name('laporan.show');
    
    Route::get('/progja', function () { return view('progja.index'); });
    Route::get('/keuangan', [KeuanganController::class, 'index'])->name('keuangan.index')->middleware('role:BPH');
    Route::post('/keuangan/saldo-awal', [KeuanganController::class, 'storeSaldoAwal'])
        ->name('keuangan.saldo_awal')
        ->middleware('role:BPH');
 

    // === MANAJEMEN ANGGOTA (Users) ===
    // Menggunakan Middleware CekAksesManajemen yang sudah kita buat
    Route::middleware([CekAksesManajemen::class])->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index'); // <-- INI YANG KITA BUTUHKAN
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::put('/users/{id}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
        Route::post('/users/{id}/generate-qr', [UserController::class, 'generateQr'])->name('users.generate-qr');
        Route::put('/users/{id}/activate', [UserController::class, 'activate'])->name('users.activate');
    });
});
