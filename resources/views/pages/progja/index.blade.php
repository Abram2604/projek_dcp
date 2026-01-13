@extends('layouts.app')
@section('title', 'Program Kerja')
@section('header_title', 'Program Kerja')
@section('header_subtitle', 'Monitoring target dan realisasi program.')

@section('content')
<div class="row g-4">
    <!-- Card Statistik Progja -->
    <div class="col-md-3">
        <div class="card bg-white p-3 h-100 border-start border-4 border-primary">
            <small class="text-muted fw-bold">TOTAL PROGRAM</small>
            <h2 class="fw-bold mb-0">12</h2>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h6 class="fw-bold mb-4">Daftar Program Kerja 2024</h6>
                <!-- Contoh Item Progja -->
                <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
                    <div>
                        <h6 class="fw-bold mb-1">Pelatihan Paralegal Angkatan V</h6>
                        <p class="text-muted small mb-0"><i class="fa-regular fa-calendar me-1"></i> Target: 20 Jan 2024</p>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-warning text-dark mb-1">Berjalan (50%)</span>
                        <div class="progress" style="width: 100px; height: 5px;">
                            <div class="progress-bar bg-warning" style="width: 50%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection