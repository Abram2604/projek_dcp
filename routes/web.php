<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// 1. Route Login & Logout
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// 2. Route Halaman Utama (Wajib Login)
Route::middleware('auth')->group(function () {
    Route::get('/', function () { return redirect('/dashboard'); });

    // Perhatikan penamaan view-nya menggunakan titik (.) sebagai pemisah folder
    Route::get('/dashboard', function () { return view('pages.dashboard.index'); })->name('dashboard');
    Route::get('/absensi', function () { return view('pages.absensi.index'); });
    Route::get('/laporan', function () { return view('pages.laporan.index'); });
    Route::get('/progja', function () { return view('pages.progja.index'); });
    Route::get('/keuangan', function () { return view('pages.keuangan.index'); });
    Route::get('/users', function () { return view('pages.users.index'); });
});