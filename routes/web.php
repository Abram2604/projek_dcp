<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\CekAksesManajemen;
use App\Http\Controllers\KeuanganController;
use App\Http\Controllers\AbsensiController;

// 1. Route Login & Logout
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// 2. Route Halaman Utama (Wajib Login)
Route::middleware('auth')->group(function () {
    Route::get('/', function () { return redirect('/dashboard'); });
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // --- MODUL ABSENSI ---
    Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi.index');
    Route::get('/absensi/id-card', [AbsensiController::class, 'downloadIdCard'])->name('absensi.download_id_card');
    
    // Action Buttons (Input User via Web)
    Route::post('/absensi/hadir', [AbsensiController::class, 'storeHadir'])->name('absensi.store_hadir');
    Route::post('/absensi/dinas', [AbsensiController::class, 'storeDinas'])->name('absensi.store_dinas');
    Route::post('/absensi/izin', [AbsensiController::class, 'storeIzin'])->name('absensi.store_izin');
    
    // Secure View File
    Route::get('/absensi/bukti/{id}', [AbsensiController::class, 'viewBukti'])->name('absensi.view_bukti');

    // === FITUR KHUSUS BPH (Admin) ===
    Route::middleware('role:BPH')->group(function() {
        // 1. Rekapitulasi & Approval
        Route::get('/absensi/rekap', [AbsensiController::class, 'rekap'])->name('absensi.rekap');
        Route::put('/absensi/status/{id}', [AbsensiController::class, 'updateStatus'])->name('absensi.update_status');

        // 2. Export Data
        Route::get('/absensi/export/excel', [AbsensiController::class, 'downloadExcel'])->name('absensi.download_excel');
        Route::get('/absensi/export/pdf-rekap', [AbsensiController::class, 'downloadPdfRekap'])->name('absensi.download_pdf_rekap');
        Route::get('/absensi/export/pdf-slip/{userId}', [AbsensiController::class, 'downloadPdfSlip'])->name('absensi.download_pdf_slip');

        // 3. TERMINAL KIOSK (Untuk Mesin Scanner Kantor)
        Route::get('/absensi/terminal-scan', [AbsensiController::class, 'kiosk'])->name('absensi.kiosk');
        Route::post('/absensi/ajax-scan', [AbsensiController::class, 'processScan'])->name('absensi.process_scan');
    });

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
    Route::middleware([CekAksesManajemen::class])->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::put('/users/{id}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
        Route::post('/users/{id}/generate-qr', [UserController::class, 'generateQr'])->name('users.generate-qr');
        Route::put('/users/{id}/activate', [UserController::class, 'activate'])->name('users.activate');
    });
});