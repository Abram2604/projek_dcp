@extends('layouts.app')

@section('title', 'Profil Saya')
@section('header_title', 'Profil Anggota')
@section('header_subtitle', 'Detail informasi akun dan keanggotaan Anda.')

@section('content')

{{-- Animasi Fade In --}}
<div class="animate__animated animate__fadeInUp">
    
    <!-- Profile Header Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <!-- Banner Gradient -->
        <div style="height: 140px; background: linear-gradient(135deg, #4f46e5, #7e22ce);"></div>
        
        <div class="card-body px-4 px-md-5 pb-5 position-relative">
            <!-- Avatar & Nama -->
            <div class="d-flex flex-column flex-md-row align-items-start align-items-md-end gap-4 mt-n5" style="margin-top: -60px;">
                
                <!-- Avatar Box -->
                <div class="bg-white p-1 rounded-4 shadow-lg">
                    <div class="d-flex align-items-center justify-content-center bg-primary bg-opacity-10 rounded-4" 
                         style="width: 120px; height: 120px;">
                        @if($user->foto_profil)
                            <img src="{{ asset('storage/'.$user->foto_profil) }}" class="w-100 h-100 object-fit-cover rounded-4">
                        @else
                            {{-- Icon Default jika tidak ada foto --}}
                            <i class="fa-regular fa-circle-user text-primary" style="font-size: 60px;"></i>
                        @endif
                    </div>
                </div>

                <!-- Nama & Badge -->
                <div class="mb-3">
                    <h2 class="fw-bold text-dark mb-2">{{ $user->nama_lengkap }}</h2>
                    <div class="d-flex flex-wrap gap-2">
                        <!-- Role Badge -->
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-10 rounded-pill px-3 py-2 text-uppercase">
                            {{ $user->nama_jabatan }}
                        </span>
                        
                        <!-- Status Badge -->
                        @if($user->status_aktif)
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-10 rounded-pill px-3 py-2">
                                <i class="fa-solid fa-circle-check me-1"></i> Akun Aktif
                            </span>
                        @else
                            <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-2">Non-Aktif</span>
                        @endif
                    </div>
                </div>
            </div>

            <hr class="my-4 border-light">

            <!-- Grid Informasi -->
            <div class="row g-4">
                <!-- Kolom Kiri: Kontak -->
                <div class="col-md-6">
                    <h6 class="fw-bold text-muted text-uppercase small mb-3">Informasi Kontak</h6>
                    
                    <div class="d-flex flex-column gap-3">
                        <div class="p-3 bg-light rounded-3 d-flex align-items-center gap-3">
                            <i class="fa-regular fa-envelope text-muted fs-5"></i>
                            <span class="text-dark">{{ $user->email ?? $user->username.'@spsi-karawang.org' }}</span>
                        </div>
                        <div class="p-3 bg-light rounded-3 d-flex align-items-center gap-3">
                            <i class="fa-solid fa-map-pin text-muted fs-5"></i>
                            <span class="text-dark">Karawang, Jawa Barat</span>
                        </div>
                        <div class="p-3 bg-light rounded-3 d-flex align-items-center gap-3">
                            <i class="fa-solid fa-building text-muted fs-5"></i>
                            <span class="text-dark">{{ $user->nama_divisi }}</span>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Keanggotaan -->
                <div class="col-md-6">
                    <h6 class="fw-bold text-muted text-uppercase small mb-3">Detail Keanggotaan</h6>
                    
                    <div class="d-flex flex-column gap-3">
                        <div class="p-3 bg-light rounded-3 d-flex align-items-center gap-3">
                            <i class="fa-solid fa-shield-halved text-muted fs-5"></i>
                            <span>ID: <span class="fw-bold text-dark font-monospace">DPC-2026-{{ str_pad($user->id, 3, '0', STR_PAD_LEFT) }}</span></span>
                        </div>
                        <div class="p-3 bg-light rounded-3 d-flex align-items-center gap-3">
                            <i class="fa-regular fa-calendar text-muted fs-5"></i>
                            <span>Bergabung: <span class="text-dark">{{ \Carbon\Carbon::parse($user->dibuat_pada)->isoFormat('D MMMM Y') }}</span></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Card Bawah -->
    <div class="card border-0 shadow-sm rounded-4 p-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h6 class="fw-bold text-dark d-flex align-items-center gap-2 mb-1">
                    <i class="fa-solid fa-medal text-warning fs-4"></i> Status Keanggotaan
                </h6>
                <p class="text-muted small m-0">Masa berlaku keanggotaan Anda aktif hingga akhir periode kepengurusan.</p>
            </div>
            <div class="text-end d-none d-md-block">
                <p class="text-muted fw-bold small text-uppercase m-0">Periode</p>
                <h4 class="fw-bold text-primary m-0">2023 - 2028</h4>
            </div>
        </div>
    </div>

</div>
@endsection