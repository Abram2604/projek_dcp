@extends('layouts.app')

@section('title', 'Dashboard')
@section('header_title', 'Dashboard Utama')
@section('header_subtitle', 'Ringkasan aktivitas organisasi hari ini.')

@section('content')

@php
    // Simulasi Role untuk Tampilan (Nanti dari Controller)
    $is_bph = true; // Set false untuk melihat tampilan anggota biasa
@endphp

<div class="row g-4">
    
    <!-- TAMPILAN KHUSUS BPH (Ketua, Sekre, Bendahara) -->
    @if($is_bph)
        <div class="col-12 col-md-3">
            <div class="card h-100 border-start border-4 border-primary">
                <div class="card-body">
                    <small class="text-muted fw-bold text-uppercase">Total Anggota</small>
                    <h2 class="fw-bold text-dark mb-0">145</h2>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card h-100 border-start border-4 border-success">
                <div class="card-body">
                    <small class="text-muted fw-bold text-uppercase">Hadir Hari Ini</small>
                    <h2 class="fw-bold text-dark mb-0">88</h2>
                    <small class="text-success"><i class="fa-solid fa-arrow-up"></i> 12 Org Dinas Luar</small>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card h-100 border-start border-4 border-warning">
                <div class="card-body">
                    <small class="text-muted fw-bold text-uppercase">Laporan Masuk</small>
                    <h2 class="fw-bold text-dark mb-0">34</h2>
                    <small class="text-danger">5 Belum Submit</small>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card h-100 border-start border-4 border-info">
                <div class="card-body">
                    <small class="text-muted fw-bold text-uppercase">Saldo Kas</small>
                    <h3 class="fw-bold text-dark mb-0">Rp 45.2jt</h3>
                </div>
            </div>
        </div>

        <!-- Tabel Monitoring Program Kerja (BPH View) -->
        <div class="col-12 mt-4">
            <div class="card">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold">Monitoring Program Kerja Divisi</h6>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Divisi</th>
                                <th>Nama Program</th>
                                <th>Target</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="badge bg-primary">Advokasi</span></td>
                                <td>Pelatihan Paralegal Angkatan V</td>
                                <td>20 Jan 2024</td>
                                <td><span class="badge bg-warning text-dark">Berjalan</span></td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-success">Organisasi</span></td>
                                <td>Konsolidasi PUK KIIC</td>
                                <td>15 Feb 2024</td>
                                <td><span class="badge bg-secondary">Rencana</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    @else
    <!-- TAMPILAN ANGGOTA / DIVISI BIASA -->
        <div class="col-12 col-md-8">
            <div class="card h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between">
                    <h6 class="m-0 fw-bold">Riwayat Kehadiran Saya</h6>
                    <span class="badge bg-success">Bulan Ini</span>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Jam Masuk</th>
                                <th>Jam Pulang</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>12 Jan 2024</td>
                                <td>07:55</td>
                                <td>17:05</td>
                                <td><span class="badge bg-success rounded-pill">HADIR</span></td>
                            </tr>
                            <tr>
                                <td>11 Jan 2024</td>
                                <td>-</td>
                                <td>-</td>
                                <td><span class="badge bg-info rounded-pill">DINAS LUAR</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card bg-primary text-white h-100" style="background: linear-gradient(45deg, #312e81, #4f46e5);">
                <div class="card-body text-center d-flex flex-column justify-content-center align-items-center">
                    <div class="mb-3 p-3 bg-white bg-opacity-25 rounded-circle">
                        <i class="fa-solid fa-qrcode fa-3x"></i>
                    </div>
                    <h5>Absensi Hari Ini</h5>
                    <p class="small text-white-50">Silahkan scan QR di dinding kantor atau input Dinas.</p>
                    <a href="{{ url('/absensi') }}" class="btn btn-light text-primary fw-bold w-100 rounded-pill">Buka Menu Absen</a>
                </div>
            </div>
        </div>
    @endif

</div>
@endsection