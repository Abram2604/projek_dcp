@extends('layouts.app')

@section('title', 'Menu Absensi')
@section('header_title', 'Absensi Kehadiran')
@section('header_subtitle', 'Pilih metode absensi sesuai kondisi Anda.')

@section('content')
<div class="row g-4">
    
    <!-- 1. Tombol Generate QR (Untuk discan ke alat dinding) -->
    <div class="col-md-4">
        <div class="card h-100 text-center p-4 border-primary">
            <div class="card-body">
                <div class="icon-box mb-3 text-primary">
                    <i class="fa-solid fa-qrcode fa-4x"></i>
                </div>
                <h5 class="fw-bold">QR Code Masuk/Pulang</h5>
                <p class="text-muted small">Gunakan QR ini untuk scan di perangkat dinding kantor.</p>
                <button class="btn btn-primary w-100 rounded-pill" data-bs-toggle="modal" data-bs-target="#modalQr">
                    Tampilkan QR Saya
                </button>
            </div>
        </div>
    </div>

    <!-- 2. Absen Dinas Luar -->
    <div class="col-md-4">
        <div class="card h-100 text-center p-4">
            <div class="card-body">
                <div class="icon-box mb-3 text-info">
                    <i class="fa-solid fa-briefcase fa-4x"></i>
                </div>
                <h5 class="fw-bold">Absen Dinas Luar</h5>
                <p class="text-muted small">Input kehadiran jika sedang bertugas di luar kantor/kota.</p>
                <button class="btn btn-outline-info w-100 rounded-pill" data-bs-toggle="modal" data-bs-target="#modalDinas">
                    Input Dinas
                </button>
            </div>
        </div>
    </div>

    <!-- 3. Izin / Sakit -->
    <div class="col-md-4">
        <div class="card h-100 text-center p-4">
            <div class="card-body">
                <div class="icon-box mb-3 text-warning">
                    <i class="fa-solid fa-notes-medical fa-4x"></i>
                </div>
                <h5 class="fw-bold">Izin / Sakit</h5>
                <p class="text-muted small">Form pengajuan ketidakhadiran (Approval BPH).</p>
                <button class="btn btn-outline-warning w-100 rounded-pill">
                    Ajukan Izin
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal QR Code -->
<div class="modal fade" id="modalQr" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h6 class="modal-title fw-bold">QR Absensi Anda</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <!-- Nanti diganti Logic QR Code Generator -->
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=USER-123-SECRET" class="img-fluid mb-3">
                <p class="small text-danger fw-bold m-0">Berlaku: 12 Jan 2024</p>
                <p class="small text-muted">Arahkan ke kamera scanner dinding.</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal Input Dinas -->
<div class="modal fade" id="modalDinas" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Form Dinas Luar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label class="form-label">Jenis Dinas</label>
                        <select class="form-select">
                            <option>Dalam Kota (Karawang)</option>
                            <option>Luar Kota</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tujuan / Lokasi</label>
                        <input type="text" class="form-control" placeholder="Contoh: PT. Toyota KIIC">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Upload Bukti (Foto/Surat)</label>
                        <input type="file" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Simpan Absen Dinas</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection