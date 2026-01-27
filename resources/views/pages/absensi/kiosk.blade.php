@extends('layouts.app')

@section('title', 'Terminal Absensi')

@section('content')
<div class="container-fluid py-4" style="height: 80vh;">
    <div class="row justify-content-center h-100 align-items-center">
        <div class="col-md-8 col-lg-6">

            <!-- KARTU TERMINAL -->
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden text-center">
                <!-- Header -->
                <div class="bg-indigo p-4 text-white">
                    <h2 class="fw-bold mb-1">TERMINAL ABSENSI</h2>
                    <p class="mb-0 opacity-75">DPC FSP LEM SPSI KARAWANG</p>
                </div>

                <!-- Body -->
                <div class="card-body p-5">

                    <!-- Jam Digital -->
                    <div class="mb-4">
                        <h1 class="display-3 fw-bold text-dark" id="liveClock">00:00:00</h1>
                        <p class="text-muted text-uppercase fw-bold" id="liveDate">SENIN, 01 JANUARI 2026</p>
                    </div>

                    <!-- Indikator Status (Feedback Visual Besar) -->
                    <div id="statusArea" class="my-5">
                        <div class="spinner-border text-indigo mb-3" role="status" style="width: 3rem; height: 3rem;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <h4 class="text-muted animate-pulse">Silahkan Scan QR Code Anda...</h4>
                    </div>

                    <!-- Input Hidden untuk Scanner -->
                    <!-- Scanner akan mengetik di sini. Dibuat opacity rendah agar debug mudah, tapi tidak mengganggu UI -->
                    <input type="text" id="qrInput" class="form-control text-center opacity-50 mx-auto"
                        style="max-width: 300px; cursor: none;"
                        placeholder="Scan Here..."
                        autocomplete="off" autofocus>

                    <div class="mt-4">
                        <small class="text-muted"><i class="fa-solid fa-keyboard me-1"></i> Pastikan kursor aktif di kolom input</small>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <a href="{{ route('dashboard') }}" class="btn btn-light text-muted rounded-pill px-4">
                    <i class="fa-solid fa-arrow-left me-2"></i> Kembali ke Dashboard
                </a>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const qrInput = document.getElementById('qrInput');
        const statusArea = document.getElementById('statusArea');
        const liveClock = document.getElementById('liveClock');
        const liveDate = document.getElementById('liveDate');

        // 1. JAM DIGITAL REALTIME
        function updateTime() {
            const now = new Date();
            liveClock.innerText = now.toLocaleTimeString('id-ID', {
                hour12: false
            });
            liveDate.innerText = now.toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }
        setInterval(updateTime, 1000);
        updateTime();

        // 2. AUTO FOCUS (Agar scanner selalu mengetik di input)
        qrInput.focus();
        document.body.addEventListener('click', () => qrInput.focus()); // Klik dimanapun fokus ke input

        // 3. HANDLE SCANNER INPUT
        qrInput.addEventListener('change', function() {
            const code = qrInput.value.trim();
            if (code.length > 0) {
                handleScan(code);
            }
            qrInput.value = ''; // Reset input segera
        });

        // 4. LOGIKA AJAX KE SERVER
        function handleScan(code) {
            // Tampilkan Loading UI
            statusArea.innerHTML = `
                <div class="spinner-grow text-primary mb-3" role="status"></div>
                <h4 class="text-primary">Memproses Data...</h4>
            `;

            axios.post('{{ route("absensi.process_scan") }}', {
                    qr_code: code
                })
                .then(function(response) {
                    const data = response.data;

                    if (data.status === 'success') {
                        // TAMPILAN SUKSES
                        let color = data.type === 'MASUK' ? 'success' : 'warning'; // Warning warna kuning (mirip sore hari)
                        let icon = data.type === 'MASUK' ? 'fa-door-open' : 'fa-door-closed';

                        statusArea.innerHTML = `
                        <div class="d-inline-block p-4 rounded-circle bg-${color} text-white mb-3 shadow-sm animate-bounce">
                            <i class="fa-solid ${icon} fa-3x"></i>
                        </div>
                        <h3 class="fw-bold text-${color}">${data.type} BERHASIL</h3>
                        <p class="fs-5 text-dark mb-0">${data.message}</p>
                        <small class="text-muted">${data.waktu}</small>
                    `;
                    } else {
                        // TAMPILAN ERROR
                        showError(data.message);
                    }
                })
                .catch(function(error) {
                    console.error(error);
                    showError("Terjadi kesalahan koneksi server.");
                })
                .finally(function() {
                    // Reset ke mode standby setelah 3 detik
                    setTimeout(resetStandby, 3000);
                });
        }

        function showError(msg) {
            statusArea.innerHTML = `
                <div class="d-inline-block p-3 rounded-circle bg-danger text-white mb-3 shadow-sm">
                    <i class="fa-solid fa-triangle-exclamation fa-2x"></i>
                </div>
                <h4 class="fw-bold text-danger">GAGAL</h4>
                <p class="text-muted">${msg}</p>
            `;
        }

        function resetStandby() {
            statusArea.innerHTML = `
                <div class="spinner-border text-indigo mb-3" role="status" style="width: 3rem; height: 3rem;">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <h4 class="text-muted">Silahkan Scan QR Code Anda...</h4>
            `;
            qrInput.value = '';
            qrInput.focus();
        }
    });
</script>

<style>
    /* Animasi CSS Sederhana */
    @keyframes pulse {
        0% {
            opacity: 0.6;
        }

        50% {
            opacity: 1;
        }

        100% {
            opacity: 0.6;
        }
    }

    .animate-pulse {
        animation: pulse 2s infinite;
    }
</style>
@endsection