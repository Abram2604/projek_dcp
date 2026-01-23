@extends('layouts.app')

@section('title', 'Attendance')
@section('header_title', 'Absensi & Kehadiran')
@section('header_subtitle', 'Scan QR, input dinas, atau pengajuan izin.')

@section('content')

{{-- CSS Custom Page --}}
<style>
    /* Font Size Optimization */
    .fs-7 { font-size: 0.9rem !important; }
    .fs-8 { font-size: 0.8rem !important; }
    
    /* Card Styling */
    .card-compact { border-radius: 16px; }
    
    /* Navigation Pills Consistent Style */
    .nav-container {
        background-color: #ffffff;
        padding: 0.5rem;
        border-radius: 50rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        border: 1px solid #f3f4f6;
        display: inline-flex;
        gap: 0.5rem;
    }
    .nav-pills-custom .nav-link {
        border-radius: 50rem;
        padding: 0.5rem 1.5rem;
        font-weight: 600;
        font-size: 0.85rem;
        color: #64748b;
        display: flex; align-items: center; gap: 0.5rem;
    }
    .nav-pills-custom .nav-link:hover { background-color: #f1f5f9; color: #334155; }
    .nav-pills-custom .nav-link.active { background-color: #4f46e5; color: white; box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.3); }

    /* Icon Status */
    .status-icon { width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 50%; }
</style>

{{-- ALERT --}}
@if(session('success'))
<div class="alert alert-success border-0 shadow-sm rounded-3 py-2 px-3 mb-3 d-flex align-items-center animate-in fade-in fs-7">
    <i class="fa-solid fa-circle-check me-2"></i> 
    <div class="fw-bold">{{ session('success') }}</div>
    <button type="button" class="btn-close ms-auto btn-sm" data-bs-dismiss="alert"></button>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger border-0 shadow-sm rounded-3 py-2 px-3 mb-3 d-flex align-items-center animate-in fade-in fs-7">
    <i class="fa-solid fa-triangle-exclamation me-2"></i> 
    <div class="fw-bold">{{ $errors->first() }}</div>
    <button type="button" class="btn-close ms-auto btn-sm" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- UNIFIED NAVIGATION BAR --}}
@php $activeTab = request('tab') ?? 'qr'; @endphp

<div class="mb-4">
    <div class="nav-container">
        <ul class="nav nav-pills nav-pills-custom" id="attendanceTab" role="tablist">
            <li class="nav-item">
                <button class="nav-link {{ $activeTab == 'qr' ? 'active' : '' }}" id="qr-tab" data-bs-toggle="pill" data-bs-target="#pills-qr" type="button">
                    <i class="fa-solid fa-qrcode"></i> Scan QR
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link {{ $activeTab == 'dinas' ? 'active' : '' }}" id="dinas-tab" data-bs-toggle="pill" data-bs-target="#pills-dinas" type="button">
                    <i class="fa-solid fa-briefcase"></i> Absen Dinas
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link {{ $activeTab == 'izin' ? 'active' : '' }}" id="izin-tab" data-bs-toggle="pill" data-bs-target="#pills-izin" type="button">
                    <i class="fa-solid fa-file-medical"></i> Izin / Sakit
                </button>
            </li>
            @if(session('user_level') == 'BPH')
            <li class="nav-item">
                <a href="{{ route('absensi.rekap') }}" class="nav-link">
                    <i class="fa-solid fa-chart-pie"></i> Rekap (BPH)
                </a>
            </li>
            @endif
        </ul>
    </div>
</div>

<div class="tab-content" id="pills-tabContent">
    
    <!-- ================= TAB 1: QR & STATUS ================= -->
    <div class="tab-pane fade {{ $activeTab == 'qr' ? 'show active' : '' }}" id="pills-qr" role="tabpanel">
        <div class="row g-3">
            
            <!-- Kolom Kiri: Status -->
            <div class="col-md-7">
                <div class="card border-0 shadow-sm card-compact h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold text-dark m-0 fs-7 text-uppercase ls-1">
                                <i class="fa-regular fa-calendar me-2 text-primary"></i> {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}
                            </h6>
                            @if($absenHariIni)
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Sudah Absen</span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3">Belum Absen</span>
                            @endif
                        </div>

                        @if(!$absenHariIni)
                            <div class="text-center py-4 bg-light rounded-4 border border-dashed">
                                <div class="status-icon bg-white text-muted shadow-sm mx-auto mb-2">
                                    <i class="fa-solid fa-fingerprint fs-5"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1 fs-7">Menunggu Absensi</h6>
                                <p class="text-muted fs-8 mb-0">Scan QR Code di kantor atau input dinas.</p>
                            </div>
                        @elseif($absenHariIni->status_kehadiran == 'HADIR')
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="p-3 bg-success bg-opacity-10 rounded-4 border border-success border-opacity-25 text-center h-100">
                                        <small class="text-success fw-bold text-uppercase fs-8">Masuk</small>
                                        <div class="fw-bold text-dark fs-4">{{ \Carbon\Carbon::parse($absenHariIni->jam_masuk)->format('H:i') }}</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 {{ $absenHariIni->jam_pulang ? 'bg-primary bg-opacity-10 border-primary' : 'bg-light border-secondary' }} rounded-4 border border-opacity-25 text-center h-100">
                                        <small class="{{ $absenHariIni->jam_pulang ? 'text-primary' : 'text-muted' }} fw-bold text-uppercase fs-8">Pulang</small>
                                        <div class="fw-bold {{ $absenHariIni->jam_pulang ? 'text-dark' : 'text-muted' }} fs-4">
                                            {{ $absenHariIni->jam_pulang ? \Carbon\Carbon::parse($absenHariIni->jam_pulang)->format('H:i') : '--:--' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="p-3 bg-warning bg-opacity-10 rounded-4 border border-warning border-opacity-25 text-center">
                                <span class="badge bg-warning text-dark mb-2">{{ $absenHariIni->status_kehadiran }}</span>
                                <p class="text-muted fs-7 mb-0">{{ $absenHariIni->keterangan_tambahan }}</p>
                                <small class="text-muted fst-italic fs-8 mt-1 d-block">Status: {{ $absenHariIni->status_validasi }}</small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: QR Code -->
            <div class="col-md-5">
                <div class="card border-0 shadow-sm card-compact bg-indigo text-white position-relative overflow-hidden h-100">
                    <div class="card-body p-4 text-center position-relative z-1 d-flex flex-column justify-content-center align-items-center">
                        <div class="p-2 bg-white rounded-3 shadow-lg mb-3" style="width: fit-content;">
                            {!! QrCode::size(130)->margin(0)->generate($user->string_kode_qr ?? 'NO-DATA') !!}
                        </div>
                        <div class="fw-bold fs-6 mb-0 text-uppercase">{{ $user->nama_lengkap }}</div>
                        <div class="text-indigo-200 fs-8 mb-3">{{ session('user_jabatan') }}</div>
                        
                        <a href="{{ route('absensi.download_id_card') }}" class="btn btn-sm btn-white text-indigo fw-bold rounded-pill px-4 shadow-sm fs-8">
                            <i class="fa-solid fa-download me-1"></i> ID Card
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= TAB 2: DINAS ================= -->
    <div class="tab-pane fade {{ $activeTab == 'dinas' ? 'show active' : '' }}" id="pills-dinas" role="tabpanel">
        <div class="card border-0 shadow-sm card-compact">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="bg-indigo-50 text-indigo rounded-circle p-2">
                        <i class="fa-solid fa-plane-departure fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold m-0 fs-7">Form Dinas Luar</h6>
                        <p class="text-muted small m-0 fs-8">Pastikan upload bukti surat tugas/foto.</p>
                    </div>
                </div>

                <form action="{{ route('absensi.store_dinas') }}" method="POST" enctype="multipart/form-data" class="form-loading">
                    @csrf
                    
                    <div class="mb-2">
                        <label class="form-label fs-8 fw-bold text-muted text-uppercase">Jenis Perjalanan</label>
                        <select name="jenis_dinas" class="form-select form-select-sm bg-light border-0 fw-bold">
                            <option value="Dinas Luar Kota">Dinas Luar Kota (Rate: 150k)</option>
                            <option value="Dinas Menginap">Dinas Menginap (Rate: 300k)</option>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fs-8 fw-bold text-muted text-uppercase">Lokasi Tujuan</label>
                        <input type="text" name="lokasi_tujuan" class="form-control form-control-sm bg-light border-0" placeholder="Contoh: Jakarta" required>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fs-8 fw-bold text-muted text-uppercase">Keterangan Aktivitas</label>
                        <textarea name="keterangan" class="form-control form-control-sm bg-light border-0 rounded-3" rows="2" placeholder="Jelaskan agenda dinas..." required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fs-8 fw-bold text-muted text-uppercase">Bukti Dokumentasi</label>
                        <input type="file" name="bukti_foto" class="form-control form-control-sm" accept="image/*" required>
                    </div>

                    <button type="submit" class="btn btn-primary btn-sm w-100 py-2 rounded-pill fw-bold shadow-sm">
                        <span class="btn-text">Kirim Laporan Dinas</span>
                        <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- ================= TAB 3: IZIN ================= -->
    <div class="tab-pane fade {{ $activeTab == 'izin' ? 'show active' : '' }}" id="pills-izin" role="tabpanel">
        <div class="card border-0 shadow-sm card-compact">
            <div class="card-body p-4">
                <div class="mb-3 text-center">
                    <h6 class="fw-bold fs-7">Pengajuan Izin / Sakit</h6>
                    <p class="text-muted small fs-8">Pilih kategori ketidakhadiran Anda</p>
                </div>

                <form action="{{ route('absensi.store_izin') }}" method="POST" enctype="multipart/form-data" class="form-loading">
                    @csrf
                    
                    <div class="row g-2 mb-3">
                        <div class="col-4">
                            <input type="radio" class="btn-check" name="kategori" id="cat_sakit" value="Sakit" checked>
                            <label class="btn btn-outline-light border text-center p-2 w-100 h-100 rounded-3 shadow-sm position-relative" for="cat_sakit">
                                <i class="fa-solid fa-user-doctor text-danger fs-5 mb-1 d-block"></i>
                                <span class="fw-bold text-dark fs-8 d-block">Sakit</span>
                            </label>
                        </div>
                        <div class="col-4">
                            <input type="radio" class="btn-check" name="kategori" id="cat_izin" value="Izin Keluarga">
                            <label class="btn btn-outline-light border text-center p-2 w-100 h-100 rounded-3 shadow-sm position-relative" for="cat_izin">
                                <i class="fa-solid fa-house-user text-warning fs-5 mb-1 d-block"></i>
                                <span class="fw-bold text-dark fs-8 d-block">Izin</span>
                            </label>
                        </div>
                        <div class="col-4">
                            <input type="radio" class="btn-check" name="kategori" id="cat_cuti" value="Cuti">
                            <label class="btn btn-outline-light border text-center p-2 w-100 h-100 rounded-3 shadow-sm position-relative" for="cat_cuti">
                                <i class="fa-solid fa-umbrella-beach text-success fs-5 mb-1 d-block"></i>
                                <span class="fw-bold text-dark fs-8 d-block">Cuti</span>
                            </label>
                        </div>
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label fs-8 fw-bold text-muted">Mulai</label>
                            <input type="date" name="mulai_tanggal" class="form-control form-control-sm bg-light border-0" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fs-8 fw-bold text-muted">Sampai</label>
                            <input type="date" name="sampai_tanggal" class="form-control form-control-sm bg-light border-0" required>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fs-8 fw-bold text-muted">Alasan</label>
                        <textarea name="alasan" class="form-control form-control-sm bg-light border-0 rounded-3" rows="2" placeholder="Tuliskan alasan..." required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fs-8 fw-bold text-muted">Lampiran (Surat Dokter/PDF)</label>
                        <input type="file" name="bukti_izin" class="form-control form-control-sm bg-light border-0" accept=".jpg,.jpeg,.png,.pdf">
                    </div>

                    <button type="submit" class="btn btn-warning text-white btn-sm w-100 py-2 rounded-pill fw-bold shadow-sm">
                        <span class="btn-text">Ajukan Permohonan</span>
                        <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- FOOTER HISTORY --}}
<div class="card border-0 shadow-sm card-compact mt-3 overflow-hidden">
    <div class="card-header bg-white py-3 border-bottom">
        <h6 class="fw-bold m-0 text-dark fs-8"><i class="fa-solid fa-clock-rotate-left me-2 text-primary"></i> Riwayat Terbaru</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover table-sm align-middle mb-0 text-nowrap">
            <thead class="bg-light">
                <tr>
                    <th class="ps-3 py-2 text-muted">Tanggal</th>
                    <th class="py-2 text-muted">Status</th>
                    <th class="text-end pe-3 py-2 text-muted">Ket</th>
                </tr>
            </thead>
            <tbody>
                @forelse($riwayatPribadi as $row)
                <tr>
                    <td class="ps-3">
                        <div class="fw-bold text-dark">{{ \Carbon\Carbon::parse($row->tanggal)->format('d M') }}</div>
                    </td>
                    <td>
                        @if($row->status_kehadiran == 'HADIR')
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill fs-8">HADIR</span>
                        @elseif($row->status_kehadiran == 'DINAS')
                            <span class="badge bg-info bg-opacity-10 text-info rounded-pill fs-8">DINAS</span>
                        @else
                            <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill fs-8">{{ $row->status_kehadiran }}</span>
                        @endif
                    </td>
                    <td class="text-end pe-3 text-muted small fs-8">
                        @if($row->status_kehadiran == 'HADIR')
                            {{ $row->jam_pulang ? 'Selesai' : 'Belum Plg' }}
                        @else
                            {{ $row->status_validasi == 'APPROVED' ? 'Valid' : 'Pend' }}
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center py-3 text-muted small fs-8">Belum ada riwayat.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
    /* Styling khusus Radio Button Card */
    .btn-check:checked + label {
        background-color: #f8fafc;
        border-color: #4f46e5 !important;
        border-width: 2px !important;
        box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.1);
    }
    .btn-check:checked + label i { transform: scale(1.1); transition: 0.2s; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const forms = document.querySelectorAll('.form-loading');
        forms.forEach(form => {
            form.addEventListener('submit', function() {
                const btn = this.querySelector('button[type="submit"]');
                const text = btn.querySelector('.btn-text');
                const spinner = btn.querySelector('.spinner-border');
                if(btn) { btn.disabled = true; if(text) text.innerText = 'Mengirim...'; if(spinner) spinner.classList.remove('d-none'); }
            });
        });
    });
</script>

@endsection