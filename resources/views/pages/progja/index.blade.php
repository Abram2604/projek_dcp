@extends('layouts.app')

@section('title', 'Program Kerja')
@section('header_title', 'Program Kerja (PROGJA)')
@section('header_subtitle', 'Target dan realisasi rencana kerja strategis.')

@section('content')

<!-- Notifikasi -->
@if(session('success'))
    <div class="alert alert-success d-flex align-items-center rounded-3 mb-4" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
    </div>
@endif

<!-- FILTER UNTUK BPH (Agar bisa pindah-pindah divisi) -->
@if($levelAkses === 'BPH')
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-3 d-flex align-items-center justify-content-between">
            <span class="text-muted small fw-bold">FILTER DIVISI:</span>
            <form action="{{ route('progja.index') }}" method="GET" class="d-flex align-items-center gap-2">
                <select name="divisi_id" class="form-select form-select-sm" style="width: 250px;" onchange="this.form.submit()">
                    <option value="">-- Semua Program --</option>
                    @foreach($divisiList as $d)
                        <option value="{{ $d->id }}" {{ $filterDivisi == $d->id ? 'selected' : '' }}>
                            {{ $d->nama_divisi }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>
@endif

<div class="row g-4">
    
    <!-- KOLOM KIRI: DAFTAR PROGJA -->
    <div class="col-lg-8">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold text-dark m-0">Daftar Program</h5>
            <button class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCreate">
                <i class="fa-solid fa-plus me-2"></i> Tambah Progja
            </button>
        </div>

        <div class="d-flex flex-column gap-3">
            @forelse($progja as $p)
                @php
                    // Warna badge status
                    $badgeClass = match($p->status_proker) {
                        'SELESAI' => 'bg-success-subtle text-success',
                        'BERJALAN' => 'bg-primary-subtle text-primary',
                        'TERKENDALA' => 'bg-danger-subtle text-danger',
                        default => 'bg-secondary-subtle text-secondary'
                    };
                    // Warna progress bar
                    $progressColor = match($p->status_proker) {
                        'SELESAI' => 'bg-success',
                        'TERKENDALA' => 'bg-danger',
                        default => 'bg-indigo' // Pastikan class ini ada di CSS atau ganti bg-primary
                    };
                @endphp

                <div class="card border border-light shadow-sm rounded-4 hover-shadow transition-all">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <span class="badge {{ $badgeClass }} rounded-pill px-3 py-2 fw-bold text-uppercase" style="font-size: 10px;">
                                    {{ $p->status_proker }}
                                </span>
                                <h5 class="fw-bold text-dark mt-2 mb-1">{{ $p->nama_program }}</h5>
                                @if($levelAkses === 'BPH')
                                    <small class="text-muted"><i class="fa-solid fa-building me-1"></i> {{ $p->nama_divisi }}</small>
                                @endif
                            </div>
                            
                            <!-- Dropdown Menu -->
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm rounded-circle" data-bs-toggle="dropdown">
                                    <i class="fa-solid fa-ellipsis"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end border-0 shadow">
                                    <li>
                                        <button class="dropdown-item" 
                                            onclick="openModalEdit('{{ $p->id }}', '{{ $p->nama_program }}', '{{ $p->persen_progress }}', '{{ $p->status_proker }}')">
                                            <i class="fa-solid fa-pen-to-square me-2 text-primary"></i> Update Progress
                                        </button>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('progja.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Hapus program ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="fa-solid fa-trash me-2"></i> Hapus
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <div class="d-flex align-items-center gap-2 text-muted small">
                                    <i class="fa-regular fa-calendar text-primary"></i>
                                    <span>Target: <span class="fw-semibold text-dark">{{ \Carbon\Carbon::parse($p->tanggal_selesai)->isoFormat('MMM Y') }}</span></span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center gap-2 text-muted small">
                                    <i class="fa-solid fa-bullseye text-danger"></i>
                                    <span>Budget: <span class="fw-semibold text-dark">Rp {{ number_format($p->anggaran_rencana, 0, ',', '.') }}</span></span>
                                </div>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div>
                            <div class="d-flex justify-content-between text-xs fw-bold mb-1">
                                <span class="text-muted">Realisasi</span>
                                <span class="text-primary">{{ $p->persen_progress }}%</span>
                            </div>
                            <div class="progress rounded-pill" style="height: 8px;">
                                <div class="progress-bar {{ $progressColor }} rounded-pill" role="progressbar" style="width: {{ $p->persen_progress }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5 text-muted">
                    <img src="https://illustrations.popsy.co/gray/success.svg" width="150" class="opacity-50 mb-3">
                    <p>Belum ada program kerja.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- KOLOM KANAN: STATISTIK & EVALUASI -->
    <div class="col-lg-4">
        <div class="d-flex flex-column gap-4">
            
            <!-- Card Statistik -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-dark mb-4">Ringkasan Statistik</h6>
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Total Program</span>
                            <span class="fw-bold fs-5">{{ $stats->total }}</span>
                        </div>
                        <hr class="my-1 border-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-success small fw-bold">Selesai</span>
                            <span class="fw-bold text-success">{{ $stats->selesai }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-primary small fw-bold">Sedang Berjalan</span>
                            <span class="fw-bold text-primary">{{ $stats->berjalan }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-danger small fw-bold">Terkendala</span>
                            <span class="fw-bold text-danger">{{ $stats->terkendala }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-secondary small fw-bold">Rencana</span>
                            <span class="fw-bold text-secondary">{{ $stats->rencana }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Evaluasi BPH (DINAMIS) -->
            <div class="card border-0 shadow rounded-4 text-white position-relative overflow-hidden" style="background: linear-gradient(135deg, #1e1b4b, #312e81);">
                <div class="card-body p-4 position-relative z-1">
                    
                    {{-- Header Card --}}
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h6 class="fw-bold d-flex align-items-center gap-2 m-0">
                            <i class="fa-solid fa-circle-check text-success"></i> Evaluasi BPH
                        </h6>
                        
                        {{-- Tombol Edit Khusus BPH --}}
                        @if($levelAkses === 'BPH')
                            <button class="btn btn-sm btn-white bg-white bg-opacity-25 text-white border-0 rounded-circle" 
                                    data-bs-toggle="modal" data-bs-target="#modalEvaluasi" title="Tulis Evaluasi">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                        @endif
                    </div>

                    {{-- Isi Evaluasi Dinamis --}}
                    @if(isset($evaluasi) && $evaluasi)
                        <p class="small text-white-50 fst-italic mb-4" style="line-height: 1.6;">
                            "{{ $evaluasi->isi_evaluasi }}"
                        </p>
                        <div class="d-flex align-items-center gap-3">
                            {{-- Inisial Penulis --}}
                            <div class="rounded-circle bg-white bg-opacity-10 d-flex align-items-center justify-content-center fw-bold text-uppercase" 
                                 style="width: 35px; height: 35px; font-size: 12px;">
                                {{ substr($evaluasi->nama_penulis ?? 'AD', 0, 2) }}
                            </div>
                            <div class="lh-1">
                                <div class="fw-bold small">{{ $evaluasi->nama_penulis ?? 'Admin' }}</div>
                                <small class="text-white-50" style="font-size: 10px;">
                                    {{ $evaluasi->jabatan_penulis ?? 'Pengurus' }} • {{ \Carbon\Carbon::parse($evaluasi->tanggal_evaluasi)->isoFormat('D MMM Y') }}
                                </small>
                            </div>
                        </div>
                    @else
                        {{-- State Kosong --}}
                        <div class="text-center py-4 text-white-50">
                            <i class="fa-regular fa-comment-dots fa-2x mb-2 opacity-50"></i>
                            <p class="small m-0">Belum ada catatan evaluasi untuk divisi ini.</p>
                        </div>
                    @endif

                </div>
                <!-- Hiasan Background -->
                <i class="fa-solid fa-quote-right position-absolute text-white opacity-10" style="font-size: 100px; bottom: -20px; right: -20px;"></i>
            </div>

        </div>
    </div>
</div>

<!-- MODAL TAMBAH PROGJA -->
<div class="modal fade" id="modalCreate" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Tambah Program Kerja</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('progja.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Nama Program</label>
                        <input type="text" name="nama_program" class="form-control" required placeholder="Contoh: Diklat Paralegal">
                    </div>
                    
                    @if($levelAkses === 'BPH')
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Divisi Pelaksana</label>
                        <select name="id_divisi" class="form-select">
                            @foreach($divisiList as $d)
                                <option value="{{ $d->id }}" {{ (isset($filterDivisi) && $filterDivisi == $d->id) ? 'selected' : '' }}>
                                    {{ $d->nama_divisi }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">Target Selesai</label>
                            <input type="date" name="tanggal_selesai" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">Estimasi Anggaran</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">Rp</span>
                                <input type="number" name="anggaran" class="form-control border-start-0" required placeholder="0">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDIT PROGRESS -->
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header border-bottom-0 pb-0">
                <h6 class="modal-title fw-bold" id="editTitle">Update Progress</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEdit" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Status</label>
                        <select name="status_proker" id="editStatus" class="form-select">
                            <option value="RENCANA">Rencana</option>
                            <option value="BERJALAN">Sedang Berjalan</option>
                            <option value="SELESAI">Selesai</option>
                            <option value="TERKENDALA">Terkendala</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Persentase (%)</label>
                        <input type="number" name="persen_progress" id="editPersen" class="form-control" min="0" max="100">
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL INPUT EVALUASI (HANYA BPH) --}}
@if($levelAkses === 'BPH')
<div class="modal fade" id="modalEvaluasi" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title fw-bold">Input Evaluasi Kinerja</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            {{-- PERBAIKAN: Gunakan route name yang sudah didefinisikan --}}
            <form action="{{ route('progja.store') }}" method="POST"> <!-- Perhatikan Route-nya nanti disesuaikan di Controller -->
                <!-- Kita pakai input hidden untuk membedakan action atau buat route khusus -->
                @csrf
                <input type="hidden" name="is_evaluasi" value="1"> 
                
                {{-- ALTERNATIF: Gunakan route khusus jika sudah dibuat --}}
                {{-- <form action="{{ route('progja.store_evaluasi') }}" method="POST"> --}}
                
                <div class="modal-body">
                    
                    <div class="alert alert-info small d-flex align-items-center gap-2 border-0 bg-info-subtle text-info-emphasis mb-3">
                        <i class="fa-solid fa-circle-info"></i>
                        Evaluasi ini akan tampil di dashboard divisi terkait.
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Tujukan Untuk Divisi</label>
                        <select name="id_divisi" class="form-select" required>
                            @foreach($divisiList as $d)
                                <option value="{{ $d->id }}" {{ (isset($filterDivisi) && $filterDivisi == $d->id) ? 'selected' : '' }}>
                                    {{ $d->nama_divisi }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Isi Catatan / Evaluasi</label>
                        <textarea name="isi_evaluasi" class="form-control" rows="4" placeholder="Contoh: Program rekrutmen perlu ditingkatkan..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <!-- Menggunakan formaction agar mengarah ke method storeEvaluasi -->
                    <button type="submit" class="btn btn-primary rounded-pill px-4" formaction="{{ route('progja.store_evaluasi') ?? '#' }}">Kirim Evaluasi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<script>
    function openModalEdit(id, nama, persen, status) {
        document.getElementById('editTitle').innerText = nama;
        document.getElementById('editPersen').value = persen;
        document.getElementById('editStatus').value = status;
        document.getElementById('formEdit').action = `/progja/${id}`;
        new bootstrap.Modal(document.getElementById('modalEdit')).show();
    }
</script>

<style>
    /* Custom Utility untuk hover effect */
    .hover-shadow:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important;
    }
    .transition-all {
        transition: all 0.3s ease;
    }
    /* Warna progress bar custom */
    .bg-indigo { background-color: #6610f2; }
</style>

@endsection