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
                                    <p class="text-muted mb-0 small">{{ $notif->pesan }}</p>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    <div class="p-4">
                        {{ $notifikasi->links() }}
                    </div>

                @else
                    {{-- State Kosong --}}
                    <div class="text-center py-5">
                        <img src="https://cdn-icons-png.flaticon.com/512/11202/11202705.png" alt="Empty" style="width: 150px; opacity: 0.6;">
                        <h6 class="fw-bold mt-3 text-muted">Tidak ada notifikasi</h6>
                        <p class="small text-muted">Semua pemberitahuan akan muncul di sini.</p>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection