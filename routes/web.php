<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

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
    Route::get('/laporan', function () { return view('laporan.index'); });
    Route::get('/progja', function () { return view('progja.index'); });
    Route::get('/keuangan', function () { return view('keuangan.index'); });
    Route::get('/users', function () { return view('users.index'); });


      Route::get('/keuangan', function () { 
        return view('pages.keuangan.index'); 
    })->middleware('role:BPH'); // <--- Cuma BPH yang boleh masuk

    // BPH DAN KORBID (Admin Divisi) BISA AKSES USER
    Route::get('/users', function () { 
        return view('pages.users.index'); 
    })->middleware('role:BPH,KORBID'); // <--- BPH & KORBID boleh masuk
});
