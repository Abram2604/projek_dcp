<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\CekAksesManajemen;
use App\Http\Controllers\KeuanganController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\ProgjaController;
use App\Http\Controllers\DataAnggotaController; 


// 1. Route Login & Logout
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// 2. Route Halaman Utama (Wajib Login)
Route::middleware('auth')->group(function () {
    Route::get('/', function () { return redirect('/dashboard'); });
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/absensi', function () { return view('absensi.index'); });
     Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::post('/laporan', [LaporanController::class, 'store'])->name('laporan.store');
    Route::get('/laporan/{id}', [LaporanController::class, 'show'])->name('laporan.show');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::get('/progja', [ProgjaController::class, 'index'])->name('progja.index');
    Route::post('/progja', [ProgjaController::class, 'store'])->name('progja.store');
    Route::put('/progja/{id}', [ProgjaController::class, 'update'])->name('progja.update');
    Route::delete('/progja/{id}', [ProgjaController::class, 'destroy'])->name('progja.destroy');
    Route::post('/progja/evaluasi', [ProgjaController::class, 'storeEvaluasi'])
        ->name('progja.store_evaluasi')
        ->middleware('role:BPH'); 

    Route::get('/keuangan', [KeuanganController::class, 'index'])->name('keuangan.index')->middleware('role:BPH');
    Route::post('/keuangan/saldo-awal', [KeuanganController::class, 'storeSaldoAwal'])
        ->name('keuangan.saldo_awal')
        ->middleware('role:BPH');

    Route::middleware(['auth', 'akses.puk'])->group(function () {
        Route::get('/data-anggota', [DataAnggotaController::class, 'index'])->name('data_anggota.index');
        Route::post('/data-anggota', [DataAnggotaController::class, 'store'])->name('data_anggota.store');
        Route::put('/data-anggota/{id}', [DataAnggotaController::class, 'update'])->name('data_anggota.update');
        Route::delete('/data-anggota/{id}', [DataAnggotaController::class, 'destroy'])->name('data_anggota.destroy');
        Route::post('/data-anggota/ttd', [DataAnggotaController::class, 'updateTtd'])->name('data_anggota.ttd');
    });
 

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
