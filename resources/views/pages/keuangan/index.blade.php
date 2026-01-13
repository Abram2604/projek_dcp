@extends('layouts.app')
@section('title', 'Keuangan')
@section('header_title', 'Keuangan Organisasi')
@section('header_subtitle', 'Monitoring arus kas dan anggaran.')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card bg-primary text-white p-4">
            <h6 class="text-white-50">Saldo Kas Saat Ini</h6>
            <h1 class="fw-bold">Rp 45.250.000</h1>
            <div class="mt-3">
                <span class="badge bg-white text-primary"><i class="fa-solid fa-arrow-up"></i> +12% Pemasukan</span>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card p-4 h-100 d-flex flex-column justify-content-center">
            <div class="d-flex justify-content-between mb-2">
                <span>Pemasukan Bulan Ini</span>
                <span class="fw-bold text-success">Rp 5.000.000</span>
            </div>
            <div class="d-flex justify-content-between">
                <span>Pengeluaran Bulan Ini</span>
                <span class="fw-bold text-danger">Rp 1.200.000</span>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white fw-bold py-3">Transaksi Terakhir</div>
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4">Tanggal</th>
                    <th>Keterangan</th>
                    <th>Jenis</th>
                    <th class="text-end pe-4">Nominal</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="ps-4">12 Jan</td>
                    <td>Iuran Anggota PUK A</td>
                    <td><span class="badge bg-success bg-opacity-10 text-success">Masuk</span></td>
                    <td class="text-end fw-bold pe-4 text-success">+ Rp 500.000</td>
                </tr>
                <tr>
                    <td class="ps-4">10 Jan</td>
                    <td>Konsumsi Rapat BPH</td>
                    <td><span class="badge bg-danger bg-opacity-10 text-danger">Keluar</span></td>
                    <td class="text-end fw-bold pe-4 text-danger">- Rp 150.000</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection