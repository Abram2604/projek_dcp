@extends('layouts.app')

@section('title', 'Attendance')
@section('header_title', 'Attendance')
@section('header_subtitle', 'Kelola kehadiran harian Anda.')

@section('content')

{{-- 1. ALERT NOTIFIKASI --}}
@if(session('success'))
<div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center animate-in fade-in">
    <i class="fa-solid fa-circle-check me-2 fa-lg"></i> 
    <div class="fw-medium">{{ session('success') }}</div>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center animate-in fade-in">
    <i class="fa-solid fa-triangle-exclamation me-2 fa-lg"></i> 
    <div class="fw-medium">{{ $errors->first() }}</div>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- 2. NAVIGASI TAB & TOOLS --}}
@php $activeTab = request('tab') ?? 'qr'; @endphp

<div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
    <!-- Tab Menu -->
    <div class="bg-white p-1 rounded-pill shadow-sm border border-light d-inline-flex">
        <ul class="nav nav-pills nav-pills-custom" id="attendanceTab" role="tablist">
            <li class="nav-item">
                <button class="nav-link rounded-pill {{ $activeTab == 'qr' ? 'active' : '' }}" id="qr-tab" data-bs-toggle="pill" data-bs-target="#pills-qr" type="button">
                    <i class="fa-solid fa-qrcode me-2"></i>Scan QR
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link rounded-pill {{ $activeTab == 'dinas' ? 'active' : '' }}" id="dinas-tab" data-bs-toggle="pill" data-bs-target="#pills-dinas" type="button">
                    <i class="fa-solid fa-briefcase me-2"></i>Dinas
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link rounded-pill {{ $activeTab == 'izin' ? 'active' : '' }}" id="izin-tab" data-bs-toggle="pill" data-bs-target="#pills-izin" type="button">
                    <i class="fa-solid fa-hospital-user me-2"></i>Izin
                </button>
            </li>
            @if(session('user_level') == 'BPH')
            <li class="nav-item">
                <a href="{{ route('absensi.rekap') }}" class="nav-link rounded-pill text-muted">
                    <i class="fa-solid fa-chart-pie me-2"></i>Rekap
                </a>
            </li>
            @endif
        </ul>
    </div>

    <!-- Tombol Khusus BPH -->
    @if(session('user_level') == 'BPH')
    <div>
        <a href="{{ route('absensi.kiosk') }}" target="_blank" class="btn btn-dark text-white shadow-sm rounded-pill px-4 fw-bold">
            <i class="fa-solid fa-desktop me-2"></i> Terminal Scan
        </a>
    </div>
    @endif
</div>

<div class="tab-content" id="pills-tabContent">
    
    <!-- ================= TAB 1: SCAN QR & STATUS DASHBOARD ================= -->
    <div class="tab-pane fade {{ $activeTab == 'qr' ? 'show active' : '' }}" id="pills-qr" role="tabpanel">
        <div class="row g-4">
            
            <!-- KOLOM KIRI: QR CODE -->
            <div class="col-md-5 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden text-center">
                    <div class="bg-indigo p-4 text-white">
                        <h5 class="fw-bold mb-1">ID Card Digital</h5>
                        <p class="small opacity-75 mb-0">Gunakan untuk scan di mesin kantor</p>
                    </div>
                    <div class="card-body p-4 d-flex flex-column align-items-center justify-content-center">
                        <div class="p-3 bg-white rounded-3 border-dashed border-2 border-secondary border-opacity-25 mb-4 shadow-sm">
                            {!! QrCode::size(160)->generate($user->string_kode_qr ?? 'NO-DATA') !!}
                        </div>
                        <a href="{{ route('absensi.download_id_card') }}" class="btn btn-outline-primary rounded-pill px-4 fw-bold w-100">
                            <i class="fa-solid fa-download me-2"></i> Unduh ID Card
                        </a>
                        <p class="text-muted small mt-3 mb-0">Simpan gambar QR atau cetak kartu untuk kemudahan absensi.</p>
                    </div>
                </div>
            </div>

            <!-- KOLOM KANAN: STATUS HARI INI -->
            <div class="col-md-7 col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white py-3 border-bottom-0">
                        <h6 class="fw-bold m-0 text-dark"><i class="fa-regular fa-calendar-check me-2 text-indigo"></i> Status Kehadiran Hari Ini</h6>
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center p-4">
                        
                        @if(!$absenHariIni)
                            <!-- CASE 1: BELUM ABSEN -->
                            <div class="text-center py-4">
                                <div class="bg-light rounded-circle d-inline-flex p-4 mb-3 text-muted">
                                    <i class="fa-solid fa-fingerprint fa-3x"></i>
                                </div>
                                <h4 class="fw-bold text-dark">Belum Ada Data</h4>
                                <p class="text-muted">Silahkan lakukan scan pada mesin di kantor atau input dinas/izin.</p>
                            </div>

                        @elseif($absenHariIni->status_kehadiran == 'HADIR')
                            <!-- CASE 2: HADIR (Masuk/Pulang) -->
                            <div class="w-100">
                                <div class="row g-3">
                                    <!-- Jam Masuk -->
                                    <div class="col-6">
                                        <div class="p-4 rounded-4 bg-success bg-opacity-10 border border-success border-opacity-25 text-center h-100">
                                            <small class="text-uppercase text-success fw-bold ls-1">Jam Masuk</small>
                                            <h2 class="fw-bold text-dark my-2">{{ \Carbon\Carbon::parse($absenHariIni->jam_masuk)->format('H:i') }}</h2>
                                            <span class="badge bg-success"><i class="fa-solid fa-check me-1"></i> Berhasil</span>
                                        </div>
                                    </div>
                                    <!-- Jam Pulang -->
                                    <div class="col-6">
                                        <div class="p-4 rounded-4 {{ $absenHariIni->jam_pulang ? 'bg-primary bg-opacity-10 border-primary' : 'bg-light border-light' }} border border-opacity-25 text-center h-100">
                                            <small class="text-uppercase {{ $absenHariIni->jam_pulang ? 'text-primary' : 'text-muted' }} fw-bold ls-1">Jam Pulang</small>
                                            @if($absenHariIni->jam_pulang)
                                                <h2 class="fw-bold text-dark my-2">{{ \Carbon\Carbon::parse($absenHariIni->jam_pulang)->format('H:i') }}</h2>
                                                <span class="badge bg-primary"><i class="fa-solid fa-check me-1"></i> Selesai</span>
                                            @else
                                                <h2 class="fw-bold text-muted my-2">--:--</h2>
                                                <span class="badge bg-secondary text-white">Belum Scan</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4 text-center">
                                    <p class="text-muted small mb-0"><i class="fa-solid fa-info-circle me-1"></i> Data masuk secara otomatis melalui mesin scanner.</p>
                                </div>
                            </div>

                        @else
                            <!-- CASE 3: IZIN / DINAS / SAKIT -->
                            <div class="text-center w-100 py-3">
                                @php
                                    $bgStatus = 'warning';
                                    $iconStatus = 'fa-clock';
                                    $textStatus = 'MENUNGGU PERSETUJUAN';
                                    
                                    if($absenHariIni->status_validasi == 'APPROVED') {
                                        $bgStatus = 'success'; $iconStatus = 'fa-circle-check'; $textStatus = 'DISETUJUI';
                                    } elseif($absenHariIni->status_validasi == 'REJECTED') {
                                        $bgStatus = 'danger'; $iconStatus = 'fa-circle-xmark'; $textStatus = 'DITOLAK';
                                    }
                                @endphp

                                <div class="d-inline-block p-4 rounded-circle bg-{{ $bgStatus }} text-white mb-3 shadow-sm">
                                    <i class="fa-solid {{ $iconStatus }} fa-3x"></i>
                                </div>
                                
                                <h3 class="fw-bold text-dark mb-1">{{ $absenHariIni->status_kehadiran }}</h3>
                                <span class="badge bg-{{ $bgStatus }} fs-6 px-3 py-2 rounded-pill mb-3">{{ $textStatus }}</span>
                                
                                <div class="bg-light p-3 rounded-3 text-start mx-auto border" style="max-width: 400px;">
                                    <div class="d-flex justify-content-between mb-2">
                                        <small class="text-muted">Keterangan:</small>
                                        <small class="fw-bold text-dark">{{ \Carbon\Carbon::parse($absenHariIni->tanggal)->format('d M Y') }}</small>
                                    </div>
                                    <p class="mb-0 small text-dark fst-italic">"{{ $absenHariIni->keterangan_tambahan }}"</p>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= TAB 2: ABSEN DINAS ================= -->
    <div class="tab-pane fade {{ $activeTab == 'dinas' ? 'show active' : '' }}" id="pills-dinas" role="tabpanel">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-4">
                            <div class="bg-indigo-50 text-indigo rounded-circle d-inline-flex p-3 mb-3">
                                <i class="fa-solid fa-map-location-dot fa-2x"></i>
                            </div>
                            <h5 class="fw-bold">Input Tugas Luar (Dinas)</h5>
                            <p class="text-muted small">Isi formulir ini jika Anda bertugas di luar kantor.</p>
                        </div>
                        
                        <form action="{{ route('absensi.store_dinas') }}" method="POST" enctype="multipart/form-data" class="form-loading">
                            @csrf
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">Jenis Dinas</label>
                                    <select name="jenis_dinas" class="form-select bg-light border-0">
                                        <option value="Dinas Dalam Kota">Dalam Kota</option>
                                        <option value="Dinas Luar Kota">Luar Kota</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">Lokasi Tujuan</label>
                                    <input type="text" name="lokasi_tujuan" class="form-control bg-light border-0" placeholder="Cth: Kantor Disnaker" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Keterangan Kegiatan</label>
                                <textarea name="keterangan" class="form-control bg-light border-0 rounded-3" rows="3" placeholder="Deskripsikan tugas Anda..." required></textarea>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted">Bukti Foto / Surat Tugas</label>
                                <label class="d-block border-dashed border-2 border-secondary border-opacity-25 rounded-4 p-4 text-center cursor-pointer hover-bg-light transition position-relative">
                                    <input type="file" name="bukti_foto" class="d-none" onchange="previewFile(this, 'fileNameDinas')" accept="image/*" required>
                                    <div id="uploadPlaceholderDinas">
                                        <i class="fa-solid fa-camera fa-2x text-muted mb-2"></i>
                                        <p class="small text-muted mb-0">Klik untuk ambil foto / upload</p>
                                    </div>
                                    <div id="filePreviewDinas" class="d-none">
                                        <div class="bg-success bg-opacity-10 text-success rounded-circle d-inline-flex p-2 mb-2"><i class="fa-solid fa-check"></i></div>
                                        <p class="mb-0 fw-bold text-dark small" id="fileNameDinas">image.jpg</p>
                                    </div>
                                </label>
                            </div>

                            <button type="submit" class="btn bg-indigo text-white w-100 py-3 rounded-pill fw-bold shadow-sm hover-opacity-90">
                                <span class="btn-text">Kirim Laporan Dinas</span>
                                <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= TAB 3: IZIN / SAKIT ================= -->
    <div class="tab-pane fade {{ $activeTab == 'izin' ? 'show active' : '' }}" id="pills-izin" role="tabpanel">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-4">
                            <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-inline-flex p-3 mb-3">
                                <i class="fa-solid fa-file-medical fa-2x"></i>
                            </div>
                            <h5 class="fw-bold">Pengajuan Izin / Sakit</h5>
                            <p class="text-muted small">Formulir ketidakhadiran kerja.</p>
                        </div>
                        
                        <form action="{{ route('absensi.store_izin') }}" method="POST" enctype="multipart/form-data" class="form-loading">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Kategori</label>
                                <div class="d-flex gap-2">
                                    <input type="radio" class="btn-check" name="kategori" value="Sakit" id="cat1" checked>
                                    <label class="btn btn-outline-light text-dark border w-100 py-2 rounded-3" for="cat1">Sakit</label>

                                    <input type="radio" class="btn-check" name="kategori" value="Izin Keluarga" id="cat2">
                                    <label class="btn btn-outline-light text-dark border w-100 py-2 rounded-3" for="cat2">Izin</label>

                                    <input type="radio" class="btn-check" name="kategori" value="Cuti" id="cat3">
                                    <label class="btn btn-outline-light text-dark border w-100 py-2 rounded-3" for="cat3">Cuti</label>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <label class="form-label small fw-bold text-muted">Mulai</label>
                                    <input type="date" name="mulai_tanggal" class="form-control bg-light border-0" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small fw-bold text-muted">Sampai</label>
                                    <input type="date" name="sampai_tanggal" class="form-control bg-light border-0" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Alasan</label>
                                <textarea name="alasan" class="form-control bg-light border-0 rounded-3" rows="2" placeholder="Tuliskan alasan..." required></textarea>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted">Dokumen Pendukung</label>
                                <label class="d-block border-dashed border-2 border-secondary border-opacity-25 rounded-4 p-4 text-center cursor-pointer hover-bg-light transition">
                                    <input type="file" name="bukti_izin" class="d-none" onchange="previewFile(this, 'fileNameIzin')" accept=".jpg,.jpeg,.png,.pdf">
                                    <div id="uploadPlaceholderIzin">
                                        <i class="fa-solid fa-cloud-arrow-up fa-2x text-muted mb-2"></i>
                                        <p class="small text-muted mb-0">Upload Surat Dokter / PDF</p>
                                    </div>
                                    <div id="filePreviewIzin" class="d-none">
                                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex p-2 mb-2"><i class="fa-solid fa-file"></i></div>
                                        <p class="mb-0 fw-bold text-dark small" id="fileNameIzin">file.pdf</p>
                                    </div>
                                </label>
                            </div>

                            <button type="submit" class="btn btn-warning text-white w-100 py-3 rounded-pill fw-bold shadow-sm">
                                <span class="btn-text">Kirim Pengajuan</span>
                                <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- 3. RIWAYAT ABSENSI SAYA (Footer) --}}
<div class="row justify-content-center mt-5 mb-5 animate-in fade-in" style="animation-delay: 0.1s;">
    <div class="col-md-10 col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-indigo"></i>
                    <h6 class="fw-bold m-0 text-dark">Riwayat 5 Hari Terakhir</h6>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4 py-3">Tanggal</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                            <th class="text-end pe-4">Validasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayatPribadi as $row)
                        <tr>
                            <td class="ps-4 fw-bold text-dark">{{ \Carbon\Carbon::parse($row->tanggal)->format('d M Y') }}</td>
                            <td>
                                @if($row->status_kehadiran == 'HADIR')
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill border border-success px-3">HADIR</span>
                                @elseif($row->status_kehadiran == 'DINAS')
                                    <span class="badge bg-info bg-opacity-10 text-info rounded-pill border border-info px-3">DINAS</span>
                                @elseif($row->status_kehadiran == 'SAKIT')
                                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill border border-danger px-3">SAKIT</span>
                                @else
                                    <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill border border-warning px-3">{{ $row->status_kehadiran }}</span>
                                @endif
                            </td>
                            <td class="text-muted small">
                                @if($row->status_kehadiran == 'HADIR')
                                    <span class="text-dark fw-medium">{{ \Carbon\Carbon::parse($row->jam_masuk)->format('H:i') }}</span>
                                    - 
                                    <span class="text-dark fw-medium">{{ $row->jam_pulang ? \Carbon\Carbon::parse($row->jam_pulang)->format('H:i') : '--:--' }}</span>
                                @else
                                    {{ Str::limit($row->keterangan_tambahan, 40) }}
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                @if($row->status_validasi == 'APPROVED')
                                    <span class="text-success small fw-bold" title="Disetujui"><i class="fa-solid fa-circle-check me-1"></i> OK</span>
                                @elseif($row->status_validasi == 'REJECTED')
                                    <span class="text-danger small fw-bold" title="Ditolak"><i class="fa-solid fa-circle-xmark me-1"></i> NO</span>
                                @else
                                    <span class="text-warning small fw-bold" title="Menunggu"><i class="fa-solid fa-clock me-1"></i> WAIT</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4 small">Belum ada riwayat absensi.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- 4. SCRIPTS (Preview & Loading) --}}
<script>
    // Preview File Upload
    function previewFile(input, nameId) {
        let type = nameId.includes('Dinas') ? 'Dinas' : 'Izin';
        if (input.files && input.files[0]) {
            document.getElementById('uploadPlaceholder' + type).classList.add('d-none');
            document.getElementById('filePreview' + type).classList.remove('d-none');
            document.getElementById('fileName' + type).innerText = input.files[0].name;
        }
    }

    // UX: Loading State pada Form Submit
    document.addEventListener('DOMContentLoaded', function() {
        const forms = document.querySelectorAll('.form-loading');
        forms.forEach(form => {
            form.addEventListener('submit', function() {
                const btn = this.querySelector('button[type="submit"]');
                const text = btn.querySelector('.btn-text');
                const spinner = btn.querySelector('.spinner-border');
                
                if(btn) {
                    btn.disabled = true; // Disable tombol agar tidak double submit
                    if(text) text.innerText = 'Memproses...';
                    if(spinner) spinner.classList.remove('d-none');
                }
            });
        });
    });
</script>

@endsection