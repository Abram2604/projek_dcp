@extends('layouts.app')
@section('title', 'Laporan Harian')
@section('header_title', 'Laporan Kegiatan')
@section('header_subtitle', 'Rekap aktivitas harian anggota dan divisi.')

@section('content')
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h6 class="m-0 fw-bold">Riwayat Laporan</h6>
        <button class="btn btn-primary btn-sm rounded-pill px-3">
            <i class="fa-solid fa-plus me-1"></i> Buat Laporan Baru
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Tanggal</th>
                        <th>Judul Kegiatan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="ps-4">12 Jan 2024</td>
                        <td>Koordinasi dengan PUK Karawang</td>
                        <td><span class="badge bg-success bg-opacity-10 text-success">Disetujui</span></td>
                        <td><button class="btn btn-sm btn-light"><i class="fa-solid fa-eye"></i></button></td>
                    </tr>
                    <!-- Data Dummy -->
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection