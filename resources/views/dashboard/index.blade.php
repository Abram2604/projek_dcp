@extends('layouts.app')

@section('title', 'Dashboard')
@section('header_title', 'Dashboard')
@section('header_subtitle', 'Ringkasan aktivitas hari ini.')

@section('content')

{{-- Load Library Chart (ApexCharts) --}}
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

@if($levelAkses === 'BPH')
    {{-- ================= TAMPILAN BPH (ADMIN) ================= --}}
    <div class="row g-4 mb-4">
        <!-- Stats Card 1: Kehadiran -->
<!-- Stats Card 1: Kehadiran -->
<div class="col-12 col-md-3">
    <div class="card border-0 shadow-sm h-100">
        <div class="card-body d-flex align-items-center gap-3">
            <div class="p-3 rounded-3 bg-primary bg-opacity-10 text-primary">
                <i class="fa-solid fa-users fa-xl"></i>
            </div>
            <div>
                <small class="text-muted fw-bold">HADIR HARI INI</small>
                
                {{-- PERBAIKAN DI SINI: Gunakan $dataStats --}}
                <h3 class="fw-bold mb-0 text-dark">{{ $dataStats->hadir_hari_ini }}</h3>
                <small class="text-muted" style="font-size: 11px">Dari {{ $dataStats->total_anggota }} Anggota</small>
            
            </div>
        </div>
    </div>
</div>

<!-- Stats Card 2: Laporan -->
<div class="col-12 col-md-3">
    <div class="card border-0 shadow-sm h-100">
        <div class="card-body d-flex align-items-center gap-3">
            <div class="p-3 rounded-3 bg-success bg-opacity-10 text-success">
                <i class="fa-solid fa-file-circle-check fa-xl"></i>
            </div>
            <div>
                <small class="text-muted fw-bold">LAPORAN MASUK</small>
                
                {{-- PERBAIKAN DI SINI --}}
                <h3 class="fw-bold mb-0 text-dark">{{ $dataStats->laporan_masuk }}</h3>
                
                <small class="text-muted" style="font-size: 11px">Laporan Harian</small>
            </div>
        </div>
    </div>
</div>

<!-- Stats Card 3: Dinas (Sementara Hardcode atau update SP nanti) -->
<div class="col-12 col-md-3">
    <div class="card border-0 shadow-sm h-100">
        <div class="card-body d-flex align-items-center gap-3">
            <div class="p-3 rounded-3 bg-info bg-opacity-10 text-info">
                <i class="fa-solid fa-map-location-dot fa-xl"></i>
            </div>
            <div>
                <small class="text-muted fw-bold">ANGGOTA DINAS</small>
                
                {{-- PERBAIKAN: Panggil variabel dinas_hari_ini --}}
                <h3 class="fw-bold mb-0 text-dark">{{ $dataStats->dinas_hari_ini }}</h3> 
                
                <small class="text-muted" style="font-size: 11px">Luar Kantor</small>
            </div>
        </div>
    </div>
</div>

<!-- Stats Card 4: Keuangan -->
<div class="col-12 col-md-3">
    <div class="card border-0 shadow-sm h-100">
        <div class="card-body d-flex align-items-center gap-3">
            <div class="p-3 rounded-3 bg-warning bg-opacity-10 text-warning">
                <i class="fa-solid fa-wallet fa-xl"></i>
            </div>
            <div>
                <small class="text-muted fw-bold">TOTAL SALDO KAS</small>
                
                {{-- PERBAIKAN DI SINI --}}
                <h4 class="fw-bold mb-0 text-dark">Rp {{ number_format($dataStats->saldo_kas / 1000000, 1) }}Jt</h4>
                
                <small class="text-muted" style="font-size: 11px">Bulan Ini</small>
            </div>
        </div>
    </div>
</div>

    <div class="row g-4 mb-4">
        <!-- Chart 1: Pie Chart Kehadiran -->
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="fw-bold m-0 text-dark">Statistik Kehadiran Hari Ini</h6>
                </div>
                <div class="card-body">
                    <div id="chartAbsensi"></div>
                </div>
            </div>
        </div>

        <!-- Chart 2: Bar Chart Laporan -->
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="fw-bold m-0 text-dark">Laporan Kegiatan per Divisi</h6>
                </div>
                <div class="card-body">
                    <div id="chartLaporan"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Keuangan -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="fw-bold m-0 text-dark">Ringkasan Keuangan (Saldo Bulan Ini)</h6>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="bg-light">
                    <tr class="text-muted small text-uppercase">
                        <th class="ps-4">Divisi</th>
                        <th>Saldo Awal</th>
                        <th>Terpakai</th>
                        <th>Sisa</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($keuanganDivisi as $k)
                    <tr>
                        <td class="ps-4 fw-bold">{{ $k->nama_divisi }}</td>
                        <td class="text-muted">Rp {{ number_format($k->saldo_awal, 0, ',', '.') }}</td>
                        <td class="text-danger">Rp {{ number_format($k->total_pengeluaran, 0, ',', '.') }}</td>
                        <td class="fw-bold text-primary">Rp {{ number_format($k->sisa_saldo, 0, ',', '.') }}</td>
                        <td>
                            @if($k->sisa_saldo > 1000000)
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Aman</span>
                            @else
                                <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">Kritis</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">Belum ada data keuangan bulan ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- SCRIPT KHUSUS CHART BPH --}}
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Data dari Controller Laravel
            const labelAbsen = @json($chartAbsenLabel);
            const dataAbsen  = @json($chartAbsenData);
            
            const labelLapor = @json($chartLaporanLabel);
            const dataLapor  = @json($chartLaporanData);

            // 1. Render Pie Chart (Absensi)
            var optionsPie = {
                series: dataAbsen.length > 0 ? dataAbsen : [1], // Dummy jika kosong
                labels: dataAbsen.length > 0 ? labelAbsen : ['Belum Ada Data'],
                chart: { type: 'donut', height: 300 },
                colors: ['#10b981', '#3b82f6', '#f59e0b', '#ef4444'],
                dataLabels: { enabled: false },
                legend: { position: 'bottom' }
            };
            new ApexCharts(document.querySelector("#chartAbsensi"), optionsPie).render();

            // 2. Render Bar Chart (Laporan)
            var optionsBar = {
                series: [{ name: 'Jumlah Laporan', data: dataLapor }],
                chart: { type: 'bar', height: 300, toolbar: { show: false } },
                plotOptions: { bar: { borderRadius: 4, columnWidth: '40%' } },
                dataLabels: { enabled: false },
                xaxis: { categories: labelLapor },
                colors: ['#6366f1']
            };
            new ApexCharts(document.querySelector("#chartLaporan"), optionsBar).render();
        });
    </script>

@else
    {{-- ================= TAMPILAN ANGGOTA / DIVISI ================= --}}
    
    <!-- Hero Section -->
    <div class="card border-0 shadow-sm mb-4 text-white" style="background: linear-gradient(135deg, #4f46e5, #312e81);">
        <div class="card-body p-5">
            <h2 class="fw-bold">Halo, {{ Auth::user()->nama_lengkap }}!</h2>
            <p class="text-white-50 mb-4">Divisi: {{ session('user_divisi') }} | {{ session('user_jabatan') }}</p>
            
            <div class="d-flex flex-wrap gap-3">
                <div class="bg-white bg-opacity-10 border border-white border-opacity-25 rounded-3 px-4 py-2">
                    <small class="text-info d-block mb-1">Absensi Hari Ini</small>
                    
                    {{-- PERBAIKAN LOGIKA PAKAI DATA DARI PROSEDUR --}}
                    @if($dataHeader->jam_masuk)
                        <span class="fw-bold text-white">
                            <i class="fa-solid fa-check me-2"></i> {{ \Carbon\Carbon::parse($dataHeader->jam_masuk)->format('H:i') }}
                        </span>
                    @else
                        <span class="fw-bold text-warning">
                            <i class="fa-solid fa-triangle-exclamation me-2"></i> Belum Absen
                        </span>
                    @endif
                </div>

                <!-- Status Laporan -->
                <div class="bg-white bg-opacity-10 border border-white border-opacity-25 rounded-3 px-4 py-2">
                    <small class="text-info d-block mb-1">Laporan Hari Ini</small>
                    
                    {{-- PERBAIKAN LOGIKA --}}
                    @if($dataHeader->status_lapor > 0)
                        <span class="fw-bold text-white"><i class="fa-solid fa-check me-2"></i> Sudah Submit</span>
                    @else
                        <span class="fw-bold text-danger"><i class="fa-solid fa-xmark me-2"></i> Belum Submit</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Riwayat Kehadiran -->
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="fw-bold m-0 text-dark">Riwayat Kehadiran Terakhir</h6>
                </div>
                <div class="card-body">
                    @forelse($riwayatAbsen as $log)
                    <div class="d-flex justify-content-between align-items-center p-3 mb-2 bg-light rounded-3">
                        <div>
                            <div class="fw-bold text-dark">{{ \Carbon\Carbon::parse($log->tanggal)->isoFormat('dddd, D MMM Y') }}</div>
                            <small class="text-muted">{{ $log->jam_masuk ?? '-' }} - {{ $log->jam_pulang ?? '-' }}</small>
                        </div>
                        @if($log->status_kehadiran == 'HADIR')
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill">HADIR</span>
                        @elseif($log->status_kehadiran == 'DINAS')
                            <span class="badge bg-info bg-opacity-10 text-info rounded-pill">DINAS</span>
                        @else
                            <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill">{{ $log->status_kehadiran }}</span>
                        @endif
                    </div>
                    @empty
                        <p class="text-center text-muted small py-3">Belum ada riwayat absensi.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Laporan Divisi Terbaru -->
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="fw-bold m-0 text-dark">Laporan Terbaru Divisi Anda</h6>
                </div>
                <div class="card-body">
                    @forelse($laporanDivisi as $lap)
                    <div class="p-3 mb-3 border-start border-4 border-primary bg-light rounded-end-3">
                        <div class="fw-bold text-dark mb-1">{{ Str::limit($lap->judul_kegiatan, 40) }}</div>
                        <p class="text-muted small mb-2 lh-sm">{{ Str::limit($lap->isi_laporan, 80) }}</p>
                        <small class="text-primary fw-bold" style="font-size: 10px;">
                            {{ \Carbon\Carbon::parse($lap->dibuat_pada)->diffForHumans() }}
                        </small>
                    </div>
                    @empty
                        <p class="text-center text-muted small py-3">Belum ada laporan dari divisi Anda.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endif

@endsection