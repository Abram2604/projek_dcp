@extends('layouts.app')

@section('header_title', 'Notifikasi')
@section('header_subtitle', 'Riwayat aktivitas dan pemberitahuan Anda.')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-0">
                
                @if($notifikasi->count() > 0)
                    <div class="list-group list-group-flush rounded-4">
                        @foreach($notifikasi as $notif)
                        <a href="{{ $notif->link_url ?? '#' }}" class="list-group-item list-group-item-action p-4 border-bottom {{ $notif->is_read == 0 ? 'bg-indigo-50' : '' }}">
                            <div class="d-flex align-items-start gap-3">
                                {{-- Ikon --}}
                                <div class="mt-1 flex-shrink-0">
                                    @if($notif->tipe == 'success')
                                        <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="fa-solid fa-check"></i>
                                        </div>
                                    @elseif($notif->tipe == 'alert')
                                        <div class="bg-danger-subtle text-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="fa-solid fa-bell"></i>
                                        </div>
                                    @elseif($notif->tipe == 'warning')
                                        <div class="bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="fa-solid fa-triangle-exclamation"></i>
                                        </div>
                                    @else
                                        <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="fa-solid fa-info"></i>
                                        </div>
                                    @endif
                                </div>

                                {{-- Konten --}}
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <h6 class="mb-0 fw-bold text-dark">{{ $notif->judul }}</h6>
                                        <small class="text-muted" style="font-size: 0.75rem;">
                                            {{ \Carbon\Carbon::parse($notif->dibuat_pada)->isoFormat('D MMMM Y, HH:mm') }}
                                        </small>
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