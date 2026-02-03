@extends('layouts.app')

@section('header_title', 'Notifikasi')
@section('header_subtitle', 'Riwayat aktivitas dan pemberitahuan Anda.')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-9">

            {{-- 1. Header & Tombol Baca Semua --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold m-0 text-dark">Daftar Notifikasi</h4>
                    <small class="text-muted">Kelola semua pemberitahuan masuk.</small>
                </div>
                
                {{-- Tombol Baca Semua --}}
                @if($notifikasi->where('is_read', 0)->count() > 0)
                    <a href="{{ route('notifikasi.readAll') }}" class="btn btn-white shadow-sm border text-primary fw-bold btn-sm px-3 rounded-pill">
                        <i class="fa-solid fa-check-double me-2"></i>Tandai Semua Dibaca
                    </a>
                @endif
            </div>

            {{-- Alert Pesan Sukses --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-body p-0">
                    
                    @if($notifikasi->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($notifikasi as $notif)
                            
                            {{-- Container Utama (DIV, bukan A) --}}
                            <div class="list-group-item p-4 border-bottom d-flex justify-content-between align-items-center {{ $notif->is_read == 0 ? 'bg-primary-subtle bg-opacity-10' : 'bg-white' }}">
                                
                                {{-- BAGIAN KIRI: Ikon & Teks --}}
                                <div class="d-flex align-items-start gap-3 flex-grow-1">
                                    
                                    {{-- Ikon --}}
                                    <div class="mt-1 flex-shrink-0">
                                        @if($notif->tipe == 'success')
                                            <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                                <i class="fa-solid fa-check fa-lg"></i>
                                            </div>
                                        @elseif($notif->tipe == 'alert')
                                            <div class="bg-danger-subtle text-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                                <i class="fa-solid fa-bell fa-lg"></i>
                                            </div>
                                        @elseif($notif->tipe == 'warning')
                                            <div class="bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                                <i class="fa-solid fa-triangle-exclamation fa-lg"></i>
                                            </div>
                                        @else
                                            <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                                <i class="fa-solid fa-info fa-lg"></i>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Konten Teks (Sudah diganti jadi DIV, tidak bisa diklik) --}}
                                    <div class="flex-grow-1">
                                        <div class="d-flex flex-column">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <h6 class="mb-0 {{ $notif->is_read == 0 ? 'fw-bold text-dark' : 'text-secondary' }}">
                                                    {{ $notif->judul }}
                                                </h6>
                                            </div>
                                            <p class="mb-1 small text-muted lh-sm" style="max-width: 90%;">{{ $notif->pesan }}</p>
                                            <small class="text-xs text-muted">
                                                <i class="fa-regular fa-clock me-1"></i>
                                                {{ \Carbon\Carbon::parse($notif->dibuat_pada)->locale('id')->diffForHumans() }}
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                {{-- BAGIAN KANAN: Tombol Ceklis (Hanya jika belum dibaca) --}}
                                @if($notif->is_read == 0)
                                    <div class="ms-3 ps-3 border-start">
                                        <a href="{{ route('notifikasi.read', $notif->id) }}" 
                                           class="btn btn-white text-success border shadow-sm rounded-circle d-flex align-items-center justify-content-center hover-scale" 
                                           style="width: 40px; height: 40px;"
                                           data-bs-toggle="tooltip" 
                                           title="Tandai Sudah Dibaca">
                                            <i class="fa-solid fa-check"></i>
                                        </a>
                                    </div>
                                @else
                                    {{-- Indikator sudah dibaca (Opsional) --}}
                                    <div class="ms-3 ps-3 border-start opacity-25">
                                        <i class="fa-solid fa-envelope-open-text fa-lg text-secondary"></i>
                                    </div>
                                @endif

                            </div>
                            @endforeach
                        </div>

                        {{-- Pagination --}}
                        <div class="p-4 d-flex justify-content-end bg-light">
                            {{ $notifikasi->links('pagination::bootstrap-5') }}
                        </div>

                    @else
                        {{-- State Kosong --}}
                        <div class="text-center py-5">
                            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 100px; height: 100px;">
                                <i class="fa-regular fa-bell-slash fa-3x text-muted opacity-50"></i>
                            </div>
                            <h5 class="fw-bold text-dark">Tidak ada notifikasi</h5>
                            <p class="text-muted small">Semua pemberitahuan aktivitas Anda akan muncul di sini.</p>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-scale {
        transition: transform 0.2s;
    }
    .hover-scale:hover {
        transform: scale(1.1);
        background-color: #d1e7dd !important; 
    }
</style>
@endsection