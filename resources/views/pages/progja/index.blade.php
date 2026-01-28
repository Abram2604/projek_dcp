@extends('layouts.app')

@section('title', 'Program Kerja & RAPBO')
@section('header_title', 'Program Kerja & Anggaran')
@section('header_subtitle', 'Manajemen rencana kerja dan rancangan anggaran.')

@section('content')

<!-- Notifikasi -->
@if(session('success'))
    <div class="alert alert-success d-flex align-items-center rounded-3 mb-4" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
    </div>
@endif

<!-- FILTER UNTUK BPH -->
@if($levelAkses === 'BPH')
    <div class="card border-0 shadow-sm mb-4 no-print">
        <div class="card-body py-3 d-flex align-items-center justify-content-between">
            <span class="text-muted small fw-bold">FILTER DIVISI:</span>
            <form action="{{ route('progja.index') }}" method="GET" class="d-flex align-items-center gap-2">
                <!-- Pertahankan tab saat filter -->
                <input type="hidden" name="tab" value="{{ $activeTab }}">
                <select name="divisi_id" class="form-select form-select-sm" style="width: 250px;" onchange="this.form.submit()">
                    <option value="">-- Semua Divisi --</option>
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

<!-- NAVIGASI TAB -->
<ul class="nav nav-pills mb-4 gap-2" id="pills-tab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link rounded-pill fw-bold px-4 {{ $activeTab == 'progja' ? 'active' : '' }}" 
                id="pills-progja-tab" data-bs-toggle="pill" data-bs-target="#pills-progja" type="button">
            <i class="fa-solid fa-list-check me-2"></i> Program Kerja
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link rounded-pill fw-bold px-4 {{ $activeTab == 'rapbo' ? 'active' : '' }}" 
                id="pills-rapbo-tab" data-bs-toggle="pill" data-bs-target="#pills-rapbo" type="button">
            <i class="fa-solid fa-file-invoice-dollar me-2"></i> RAPBO
        </button>
    </li>
</ul>

<div class="tab-content" id="pills-tabContent">
    
    <!-- ================= TAB 1: PROGJA (KONTEN LAMA) ================= -->
    <div class="tab-pane fade {{ $activeTab == 'progja' ? 'show active' : '' }}" id="pills-progja" role="tabpanel">
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
                            $badgeClass = match($p->status_proker) {
                                'SELESAI' => 'bg-success-subtle text-success',
                                'BERJALAN' => 'bg-primary-subtle text-primary',
                                'TERKENDALA' => 'bg-danger-subtle text-danger',
                                default => 'bg-secondary-subtle text-secondary'
                            };
                            $progressColor = match($p->status_proker) {
                                'SELESAI' => 'bg-success',
                                'TERKENDALA' => 'bg-danger',
                                default => 'bg-indigo' 
                            };
                        @endphp
                        <div class="card border border-light shadow-sm rounded-4 hover-shadow transition-all">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <span class="badge {{ $badgeClass }} rounded-pill px-3 py-2 fw-bold text-uppercase" style="font-size: 10px;">{{ $p->status_proker }}</span>
                                        <h5 class="fw-bold text-dark mt-2 mb-1">{{ $p->nama_program }}</h5>
                                        @if($levelAkses === 'BPH')
                                            <small class="text-muted"><i class="fa-solid fa-building me-1"></i> {{ $p->nama_divisi }}</small>
                                        @endif
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-light btn-sm rounded-circle" data-bs-toggle="dropdown"><i class="fa-solid fa-ellipsis"></i></button>
                                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow">
                                            <li>
                                                <button class="dropdown-item" onclick="openModalEdit('{{ $p->id }}', '{{ $p->nama_program }}', '{{ $p->persen_progress }}', '{{ $p->status_proker }}')">
                                                    <i class="fa-solid fa-pen-to-square me-2 text-primary"></i> Update Progress
                                                </button>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('progja.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Hapus program ini?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger"><i class="fa-solid fa-trash me-2"></i> Hapus</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="bg-light p-3 rounded-3 mb-3 small border border-light-subtle">
                                    <div class="mb-2">
                                        <span class="fw-bold text-primary text-uppercase" style="font-size: 0.7rem;">Target:</span>
                                        <p class="m-0 text-muted mt-1">{{ $p->target ?? '-' }}</p>
                                    </div>
                                    <hr class="my-2 border-dashed">
                                    <div>
                                        <span class="fw-bold text-success text-uppercase" style="font-size: 0.7rem;">Action:</span>
                                        <p class="m-0 text-muted mt-1">{{ $p->action ?? '-' }}</p>
                                    </div>
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-6"><small class="text-muted"><i class="fa-regular fa-calendar text-primary me-1"></i> Target: <span class="fw-semibold text-dark">{{ \Carbon\Carbon::parse($p->tanggal_selesai)->isoFormat('MMM Y') }}</span></small></div>
                                    <div class="col-6"><small class="text-muted"><i class="fa-solid fa-bullseye text-danger me-1"></i> Budget: <span class="fw-semibold text-dark">Rp {{ number_format($p->anggaran_rencana, 0, ',', '.') }}</span></small></div>
                                </div>
                                <div class="progress rounded-pill" style="height: 8px;"><div class="progress-bar {{ $progressColor }} rounded-pill" role="progressbar" style="width: {{ $p->persen_progress }}%"></div></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted"><img src="https://illustrations.popsy.co/gray/success.svg" width="150" class="opacity-50 mb-3"><p>Belum ada program kerja.</p></div>
                    @endforelse
                </div>
            </div>

            <!-- KOLOM KANAN: STATISTIK & EVALUASI (FIXED) -->
            <div class="col-lg-4">
                <div class="d-flex flex-column gap-4">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <h6 class="fw-bold text-dark mb-4">Ringkasan Statistik</h6>
                            <div class="d-flex flex-column gap-3">
                                <div class="d-flex justify-content-between"><span class="text-muted small">Total Program</span><span class="fw-bold fs-5">{{ $stats->total }}</span></div><hr class="my-1 border-light">
                                <div class="d-flex justify-content-between"><span class="text-success small fw-bold">Selesai</span><span class="fw-bold text-success">{{ $stats->selesai }}</span></div>
                                <div class="d-flex justify-content-between"><span class="text-primary small fw-bold">Sedang Berjalan</span><span class="fw-bold text-primary">{{ $stats->berjalan }}</span></div>
                                <div class="d-flex justify-content-between"><span class="text-danger small fw-bold">Terkendala</span><span class="fw-bold text-danger">{{ $stats->terkendala }}</span></div>
                                <div class="d-flex justify-content-between"><span class="text-secondary small fw-bold">Rencana</span><span class="fw-bold text-secondary">{{ $stats->rencana }}</span></div>
                            </div>
                        </div>
                    </div>
                    <div class="card border-0 shadow rounded-4 text-white position-relative overflow-hidden" style="background: linear-gradient(135deg, #1e1b4b, #312e81);">
                        <div class="card-body p-4 position-relative z-1">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h6 class="fw-bold d-flex align-items-center gap-2 m-0"><i class="fa-solid fa-circle-check text-success"></i> Evaluasi BPH</h6>
                                @if($levelAkses === 'BPH')
                                    <button class="btn btn-sm btn-white bg-white bg-opacity-25 text-white border-0 rounded-circle" data-bs-toggle="modal" data-bs-target="#modalEvaluasi" title="Tulis Evaluasi"><i class="fa-solid fa-pen"></i></button>
                                @endif
                            </div>
                            @if(isset($evaluasi) && $evaluasi)
                                <p class="small text-white-50 fst-italic mb-4" style="line-height: 1.6;">"{{ $evaluasi->isi_evaluasi }}"</p>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle bg-white bg-opacity-10 d-flex align-items-center justify-content-center fw-bold text-uppercase" style="width: 35px; height: 35px; font-size: 12px;">{{ substr($evaluasi->nama_penulis ?? 'AD', 0, 2) }}</div>
                                    <div class="lh-1">
                                        <div class="fw-bold small">{{ $evaluasi->nama_penulis ?? 'Admin' }}</div>
                                        <small class="text-white-50" style="font-size: 10px;">{{ $evaluasi->jabatan_penulis ?? 'Pengurus' }} • {{ \Carbon\Carbon::parse($evaluasi->tanggal_evaluasi)->isoFormat('D MMM Y') }}</small>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-4 text-white-50"><i class="fa-regular fa-comment-dots fa-2x mb-2 opacity-50"></i><p class="small m-0">Belum ada catatan evaluasi.</p></div>
                            @endif
                        </div>
                        <i class="fa-solid fa-quote-right position-absolute text-white opacity-10" style="font-size: 100px; bottom: -20px; right: -20px;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= TAB 2: RAPBO (UPDATED) ================= -->
    <div class="tab-pane fade {{ $activeTab == 'rapbo' ? 'show active' : '' }}" id="pills-rapbo" role="tabpanel">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <!-- HEADER KARTU (TEMPAT TOMBOL BARU) -->
            <div class="card-header bg-white p-3 d-flex flex-wrap justify-content-between align-items-center">
                <div>
                    <h6 class="fw-bold m-0 text-dark">Rancangan Anggaran (RAPBO)</h6>
                    <small class="text-muted">Tahun Periode: {{ date('Y') }} - {{ date('Y')+1 }}</small>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('rapbo.export', ['divisi_id' => $filterDivisi]) }}" class="btn btn-danger btn-sm rounded-pill px-3 fw-bold">
                        <i class="fa-solid fa-file-pdf me-1"></i> PDF
                    </a>
                    <button class="btn btn-primary btn-sm rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#modalRapbo">
                        <i class="fa-solid fa-plus me-1"></i> Tambah Item
                    </button>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle mb-0" style="min-width: 800px;">
                        <thead class="bg-warning text-dark text-center align-middle">
                            <tr>
                                <th rowspan="2" width="5%">NO</th>
                                <th rowspan="2" width="35%">PROGRAM KERJA BIDANG</th>
                                <th colspan="3">FREKUENSI</th>
                                <th rowspan="2" width="15%">NOMINAL</th>
                                <th rowspan="2" width="15%">BUDGET / TH</th>
                                <th rowspan="2" width="15%">SERAPAN (Real)</th>
                                <th rowspan="2" width="10%" class="no-print">AKSI</th>
                            </tr>
                            <tr><th>MP</th><th>Thn</th><th>Frek</th></tr>
                        </thead>
                        <tbody>
                            @php 
                                $currentDivisi = ''; 
                                $no = 1;
                                $grandTotal = 0;
                            @endphp
                            @forelse($rapboList as $item)
                                @if($currentDivisi != $item->nama_divisi)
                                    <tr class="table-secondary fw-bold"><td class="text-center">{{ chr(64 + $loop->iteration) }}</td><td colspan="8">{{ $item->nama_divisi }}</td></tr>
                                    @php $currentDivisi = $item->nama_divisi; $no = 1; @endphp
                                @endif
                                <tr>
                                    <td class="text-center">{{ $no++ }}</td><td>{{ $item->uraian_kegiatan }}</td><td class="text-center">{{ $item->mp }}</td><td class="text-center">{{ $item->thn }}</td><td class="text-center">{{ $item->frek }}</td>
                                    <td class="text-end">{{ number_format($item->nominal_satuan, 0, ',', '.') }}</td><td class="text-end fw-bold">{{ number_format($item->total_budget, 0, ',', '.') }}</td><td class="text-end text-muted">-</td> 
                                    <td class="text-center no-print">
                                        <button class="btn btn-sm btn-link text-primary p-0 me-2" onclick='editRapbo(@json($item))'><i class="fa-solid fa-pen-to-square"></i></button>
                                        <form action="{{ route('rapbo.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus item ini?')">@csrf @method('DELETE')<button class="btn btn-sm btn-link text-danger p-0"><i class="fa-solid fa-trash"></i></button></form>
                                    </td>
                                </tr>
                                @php $grandTotal += $item->total_budget; @endphp
                            @empty
                                <tr><td colspan="9" class="text-center py-4 text-muted">Belum ada data RAPBO.</td></tr>
                            @endforelse
                        </tbody>
                        @if($rapboList->count() > 0)
                        <tfoot class="bg-light fw-bold"><tr><td colspan="6" class="text-end px-3">TOTAL ANGGARAN</td><td class="text-end text-primary">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td><td colspan="2"></td></tr></tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODALS UNTUK PROGJA -->
@include('pages.progja.partials.modals')

<!-- MODAL TAMBAH/EDIT RAPBO -->
<div class="modal fade" id="modalRapbo" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header bg-primary text-white"><h5 class="modal-title fw-bold" id="rapboModalTitle">Tambah Item RAPBO</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
            <form id="formRapbo" action="{{ route('rapbo.store') }}" method="POST">
                @csrf
                <div id="methodPutRapbo"></div>
                <div class="modal-body bg-light">
                    @if($levelAkses === 'BPH')
                        <div class="mb-3"><label class="form-label small fw-bold">Divisi</label><select name="id_divisi" id="rapbo_divisi" class="form-select" required>@foreach($divisiList as $d)<option value="{{ $d->id }}" {{ $filterDivisi == $d->id ? 'selected' : '' }}>{{ $d->nama_divisi }}</option>@endforeach</select></div>
                    @else
                        <input type="hidden" name="id_divisi" value="{{ Auth::user()->id_divisi }}">
                    @endif
                    <div class="mb-3"><label class="form-label small fw-bold">Uraian Kegiatan</label><input type="text" name="uraian_kegiatan" id="rapbo_uraian" class="form-control" required></div>
                    <div class="row g-2"><div class="col-4"><label class="form-label small fw-bold">MP</label><input type="number" name="mp" id="rapbo_mp" class="form-control" value="1" min="1" required></div><div class="col-4"><label class="form-label small fw-bold">Thn</label><input type="number" name="thn" id="rapbo_thn" class="form-control" value="1" min="1" required></div><div class="col-4"><label class="form-label small fw-bold">Frek</label><input type="number" name="frek" id="rapbo_frek" class="form-control" value="1" min="1" required></div></div>
                    <div class="mt-3"><label class="form-label small fw-bold">Nominal Satuan (Rp)</label><input type="number" name="nominal_satuan" id="rapbo_nominal" class="form-control" required></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary fw-bold">Simpan</button></div>
            </form>
        </div>
    </div>
</div>

{{-- SCRIPT HELPER --}}
<script>
    // FUNGSI UNTUK PROGJA
    function openModalEdit(id, nama, persen, status) {
        document.getElementById('editTitle').innerText = nama;
        document.getElementById('editPersen').value = persen;
        document.getElementById('editStatus').value = status;
        document.getElementById('formEdit').action = `/progja/${id}`;
        new bootstrap.Modal(document.getElementById('modalEdit')).show();
    }

    // FUNGSI UNTUK RAPBO
    function editRapbo(item) {
        document.getElementById('rapboModalTitle').innerText = 'Edit Item RAPBO';
        document.getElementById('formRapbo').action = `/rapbo/${item.id}`;
        document.getElementById('methodPutRapbo').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        document.getElementById('rapbo_uraian').value = item.uraian_kegiatan;
        document.getElementById('rapbo_mp').value = item.mp;
        document.getElementById('rapbo_thn').value = item.thn;
        document.getElementById('rapbo_frek').value = item.frek;
        document.getElementById('rapbo_nominal').value = item.nominal_satuan;
        if(document.getElementById('rapbo_divisi')) {
            document.getElementById('rapbo_divisi').value = item.id_divisi;
        }
        new bootstrap.Modal(document.getElementById('modalRapbo')).show();
    }
    
    // Reset Modal RAPBO saat ditutup
    const modalRapbo = document.getElementById('modalRapbo');
    if(modalRapbo){
        modalRapbo.addEventListener('hidden.bs.modal', event => {
            document.getElementById('rapboModalTitle').innerText = 'Tambah Item RAPBO';
            document.getElementById('formRapbo').action = "{{ route('rapbo.store') }}";
            document.getElementById('methodPutRapbo').innerHTML = '';
            document.getElementById('formRapbo').reset();
        });
    }
</script>

<style>.hover-shadow:hover {transform: translateY(-3px);box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important;}.transition-all {transition: all 0.3s ease;}.bg-indigo { background-color: #6610f2; }</style>

@endsection