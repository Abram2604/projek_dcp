@extends('layouts.app')

@section('title', 'Terminal Absensi')

@section('content')
<div class="container-fluid py-4" style="height: 80vh;">
    <div class="row justify-content-center h-100 align-items-center">
        <div class="col-md-8 col-lg-6">

            <!-- KARTU TERMINAL -->
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden text-center position-relative">
                
                <!-- Loading Overlay (Muncul saat memproses data) -->
                <div id="loadingOverlay" class="position-absolute top-0 start-0 w-100 h-100 bg-white bg-opacity-90 d-none flex-column justify-content-center align-items-center z-3">
                    <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;"></div>
                    <h4 class="fw-bold text-primary animate-pulse">Sedang Memproses...</h4>
                </div>

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

                    <!-- Area Pesan Status -->
                    <div id="statusArea" class="mb-4" style="min-height: 80px;">
                        <!-- Default State -->
                        <div id="defaultState">
                            <h4 class="text-muted fw-bold">Siap Memindai...</h4>
                            <small class="text-muted">Scan QR Code atau Ketik Manual</small>
                        </div>
                    </div>

                    <!-- INPUT FIELD (Hybrid: Bisa Scanner & Manual) -->
                    <div class="position-relative mx-auto" style="max-width: 450px;">
                        <div class="input-group input-group-lg shadow-sm rounded-pill overflow-hidden border border-2">
                            <span class="input-group-text bg-white border-0 ps-4">
                                <i class="fa-solid fa-qrcode text-indigo fs-4"></i>
                            </span>
                            
                            <!-- Input ini menerima inputan dari Scanner maupun Keyboard -->
                            <input type="text" id="qrInput" class="form-control border-0 fw-bold text-dark fs-5"
                                placeholder="Klik di sini & Scan..." 
                                autocomplete="off" autofocus>
                            
                            <!-- Tombol Submit Manual -->
                            <button class="btn btn-indigo px-4" type="button" id="btnManualSubmit">
                                <i class="fa-solid fa-arrow-right"></i>
                            </button>
                        </div>
                        <div class="form-text text-muted mt-2 small">
                            <i class="fa-solid fa-keyboard me-1"></i> Pastikan kursor aktif di dalam kotak input
                        </div>
                    </div>

                </div>
            </div>

            <div class="text-center mt-4">
                <a href="{{ route('dashboard') }}" class="btn btn-light text-muted rounded-pill px-4 shadow-sm">
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
        const btnManual = document.getElementById('btnManualSubmit');
        const statusArea = document.getElementById('statusArea');
        const defaultState = document.getElementById('defaultState');
        const loadingOverlay = document.getElementById('loadingOverlay');
        const liveClock = document.getElementById('liveClock');
        const liveDate = document.getElementById('liveDate');

        let isProcessing = false;

        // 1. JAM DIGITAL REALTIME
        function updateTime() {
            const now = new Date();
            liveClock.innerText = now.toLocaleTimeString('id-ID', { hour12: false });
            liveDate.innerText = now.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        }
        setInterval(updateTime, 1000);
        updateTime();

        // 2. AUTO FOCUS SYSTEM
        // Agar scanner selalu nembak ke input meskipun user tidak sengaja klik di luar
        qrInput.focus();
        
        document.addEventListener('click', function(e) {
            // Jika yang diklik BUKAN elemen interaktif (tombol/link/input), kembalikan fokus ke input QR
            const tag = e.target.tagName;
            if (tag !== 'INPUT' && tag !== 'A' && tag !== 'BUTTON' && tag !== 'I') {
                qrInput.focus();
            }
        });

        // 3. EVENT LISTENER INPUT
        
        // A. Deteksi Tombol ENTER (Scanner biasanya kirim Enter di akhir)
        qrInput.addEventListener('keydown', function(e) {
            if (isProcessing) { e.preventDefault(); return; }

            if (e.key === 'Enter' || e.keyCode === 13) {
                e.preventDefault(); // Mencegah submit form default
                const code = qrInput.value.trim();
                if (code.length > 0) {
                    processScan(code);
                }
            }
        });

        // B. Deteksi Klik Tombol Manual (Panah Kanan)
        btnManual.addEventListener('click', function() {
            if (isProcessing) return;
            const code = qrInput.value.trim();
            if (code.length > 0) {
                processScan(code);
            } else {
                qrInput.focus(); // Balikin fokus kalau kosong
            }
        });

        // 4. LOGIKA AJAX KE SERVER
        function processScan(code) {
            isProcessing = true;

            // UI: Tampilkan Loading & Disable Input
            qrInput.blur(); // Lepas fokus biar keyboard virtual di HP (kalau ada) turun
            loadingOverlay.classList.remove('d-none');
            loadingOverlay.classList.add('d-flex');

            axios.post('{{ route("absensi.process_scan") }}', { qr_code: code })
            .then(function(response) {
                const data = response.data;

                // UI: Sembunyikan default state
                defaultState.classList.add('d-none');

                if (data.status === 'success') {
                    // SUKSES
                    let color = data.type === 'MASUK' ? 'success' : 'warning';
                    let icon = data.type === 'MASUK' ? 'fa-door-open' : 'fa-door-closed';

                    statusArea.innerHTML = `
                        <div class="d-inline-block p-3 rounded-circle bg-${color} text-white mb-2 shadow-sm animate-bounce">
                            <i class="fa-solid ${icon} fa-3x"></i>
                        </div>
                        <h3 class="fw-bold text-${color} mb-0">${data.type} BERHASIL</h3>
                        <p class="fs-4 text-dark mb-0 fw-bold">${data.nama}</p>
                        <small class="text-muted">${data.message}</small>
                    `;
                } else {
                    // ERROR (Misal: Sudah absen, QR salah)
                    statusArea.innerHTML = `
                        <div class="d-inline-block p-3 rounded-circle bg-danger text-white mb-2 shadow-sm animate-shake">
                            <i class="fa-solid fa-triangle-exclamation fa-3x"></i>
                        </div>
                        <h3 class="fw-bold text-danger mb-0">GAGAL</h3>
                        <p class="text-muted fs-5 mb-0">${data.message}</p>
                    `;
                }
            })
            .catch(function(error) {
                console.error(error);
                defaultState.classList.add('d-none');
                statusArea.innerHTML = `
                    <div class="d-inline-block p-3 rounded-circle bg-secondary text-white mb-2 shadow-sm">
                        <i class="fa-solid fa-wifi fa-3x"></i>
                    </div>
                    <h4 class="fw-bold text-secondary">Koneksi Gagal</h4>
                    <p class="text-muted">Periksa jaringan internet server.</p>
                `;
            })
            .finally(function() {
                // Sembunyikan Loading Overlay segera setelah respon diterima
                loadingOverlay.classList.remove('d-flex');
                loadingOverlay.classList.add('d-none');

                // Timer untuk Reset kembali ke tampilan awal (Standby)
                setTimeout(() => {
                    statusArea.innerHTML = ''; // Hapus pesan hasil
                    statusArea.appendChild(defaultState); // Balikin pesan "Siap Scan"
                    defaultState.classList.remove('d-none');
                    
                    // Reset Input
                    qrInput.value = '';
                    qrInput.disabled = false;
                    qrInput.focus();
                    isProcessing = false;
                }, 3500); // Tahan pesan hasil selama 3.5 detik
            });
        }
    });
</script>

<style>
    /* Styling Tambahan Khusus Halaman Ini */
    .btn-indigo { background-color: #4f46e5; color: white; border: none; font-weight: bold; }
    .btn-indigo:hover { background-color: #4338ca; color: white; transform: translateY(-1px); }
    .btn-indigo:active { transform: translateY(0); }
    
    /* Animasi */
    @keyframes pulse { 0% { opacity: 0.6; } 50% { opacity: 1; } 100% { opacity: 0.6; } }
    .animate-pulse { animation: pulse 2s infinite; }
    
    @keyframes bounceIn { 0% { transform: scale(0.3); opacity: 0; } 50% { transform: scale(1.05); opacity: 1; } 70% { transform: scale(0.9); } 100% { transform: scale(1); } }
    .animate-bounce { animation: bounceIn 0.5s; }

    @keyframes shake { 0%, 100% { transform: translateX(0); } 10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); } 20%, 40%, 60%, 80% { transform: translateX(5px); } }
    .animate-shake { animation: shake 0.5s; }
</style>
@endsection