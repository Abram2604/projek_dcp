<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\KeuanganController;

// 1. Route Login & Logout
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// 2. Route Halaman Utama (Wajib Login)
Route::middleware('auth')->group(function () {
    Route::get('/', function () { return redirect('/dashboard'); });
    
    // Perbaikan nama view (Hapus .index)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/absensi', function () { return view('absensi.index'); });
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::post('/laporan', [LaporanController::class, 'store'])->name('laporan.store');
    Route::get('/laporan/{id}', [LaporanController::class, 'show'])->name('laporan.show');
    Route::get('/progja', function () { return view('progja.index'); });
    Route::get('/keuangan', [KeuanganController::class, 'index'])->name('keuangan.index')->middleware('role:BPH');
    Route::post('/keuangan/saldo-awal', [KeuanganController::class, 'storeSaldoAwal'])
        ->name('keuangan.saldo_awal')
        ->middleware('role:BPH');
    Route::get('/users', function () { return view('users.index'); });
    // <--- Cuma BPH yang boleh masuk

    // BPH DAN KORBID (Admin Divisi) BISA AKSES USER
    Route::get('/users', function () {
        return view('pages.users.index');
    })->middleware('role:BPH,KORBID'); // <--- BPH & KORBID boleh masuk
});
