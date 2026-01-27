@extends('layouts.app')

@section('title', 'Rekapitulasi')
@section('header_title', 'Rekapitulasi')
@section('header_subtitle', 'Data absensi dan perhitungan insentif.')

@section('content')

<!-- CSS STYLE -->
<style>
    /* Button Colors */
    .btn-info, .btn-primary, .btn-success, .btn-danger, .bg-indigo { color: #ffffff !important; }
    
    /* Table Styles (Compact) */
    .table-rekap { border-collapse: separate; border-spacing: 0; width: 100%; border: 1px solid #e5e7eb; font-size: 0.8rem; }
    .table-rekap td { border: 1px solid #e5e7eb; padding: 6px 4px; text-align: center; vertical-align: middle; }
    
    /* FIX: HEADER TABEL SOLID & STICKY */
    .table-rekap th, .thead-grey th { 
        background-color: #f3f4f6 !important; /* Warna Solid Abu-abu */
        color: #374151; 
        font-weight: 700; 
        text-transform: uppercase; 
        font-size: 0.75rem; 
        padding: 8px 4px;
        text-align: center;
        vertical-align: middle;
        border: 1px solid #e5e7eb;
        position: sticky;
        top: 0;
        z-index: 100; /* Pastikan di atas konten tbody */
        box-shadow: 0 1px 0 #d1d5db; /* Garis bawah header */
    }

    .table-responsive { max-height: 70vh; overflow: auto; }
    
    /* Sticky First Columns (Untuk Kolom Nama/No) */
    .sticky-col { 
        position: sticky; 
        left: 0; 
        background-color: #ffffff !important; /* Solid White */
        z-index: 101 !important; /* Lebih tinggi dari header biasa tapi di bawah header sticky */
        border-right: 2px solid #e5e7eb !important;
    }
    
    /* Header Pojok Kiri Atas (Pertemuan Sticky Top & Sticky Left) */
    .thead-grey th.sticky-col {
        z-index: 105 !important; /* Paling atas */
        background-color: #f3f4f6 !important;
    }

    /* Colors */
    .row-holiday td { background-color: #fef2f2 !important; color: #dc2626 !important; font-weight: bold; border-color: #fca5a5; }
    .cell-dinas { background-color: #dcfce7 !important; color: #166534 !important; font-weight: 700; }
    .cell-lembur { background-color: #fef9c3 !important; color: #854d0e !important; font-weight: 700; }
    .cell-hadir { color: #111827; }
    
    /* Unified Nav Style (Sama dengan Index) */
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
</style>

{{-- 1. TAB MENU UTAMA --}}
<div class="mb-4">
    <div class="nav-container">
        <ul class="nav nav-pills nav-pills-custom">
            <li class="nav-item">
                <a href="{{ route('absensi.index', ['tab' => 'qr']) }}" class="nav-link">
                    <i class="fa-solid fa-qrcode"></i> Scan QR
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('absensi.index', ['tab' => 'dinas']) }}" class="nav-link">
                    <i class="fa-solid fa-briefcase"></i> Absen Dinas
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('absensi.index', ['tab' => 'izin']) }}" class="nav-link">
                    <i class="fa-solid fa-file-medical"></i> Izin/Sakit
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link active">
                    <i class="fa-solid fa-chart-pie"></i> Rekap (BPH)
                </a>
            </li>
        </ul>
    </div>
</div>

{{-- 2. NAVIGASI BULAN & SUB-MENU TAMPILAN --}}
<div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
    <!-- Navigasi Bulan -->
    <div class="d-flex align-items-center bg-white rounded-pill shadow-sm border px-2 py-1">
        <a href="{{ route('absensi.rekap', ['bulan' => $prevDate->month, 'tahun' => $prevDate->year, 'view' => request('view')]) }}" 
           class="btn btn-sm btn-light rounded-circle text-muted">
            <i class="fa-solid fa-chevron-left"></i>
        </a>
        <span class="fw-bold text-dark mx-3 fs-7 text-uppercase" style="min-width: 100px; text-align: center;">{{ $startDate->isoFormat('MMMM Y') }}</span>
        <a href="{{ route('absensi.rekap', ['bulan' => $nextDate->month, 'tahun' => $nextDate->year, 'view' => request('view')]) }}" 
           class="btn btn-sm btn-light rounded-circle text-muted">
            <i class="fa-solid fa-chevron-right"></i>
        </a>
    </div>

    <!-- View Switcher -->
    <div class="bg-light p-1 rounded-pill d-inline-flex border border-gray-200">
        <a href="{{ route('absensi.rekap', ['view' => 'matrix', 'bulan' => $startDate->month, 'tahun' => $startDate->year]) }}" 
           class="btn btn-sm px-3 rounded-pill fw-bold {{ $currView == 'matrix' ? 'bg-white text-indigo shadow-sm' : 'text-muted' }}">
            Absensi Harian
        </a>
        <a href="{{ route('absensi.rekap', ['view' => 'incentive', 'bulan' => $startDate->month, 'tahun' => $startDate->year]) }}" 
           class="btn btn-sm px-3 rounded-pill fw-bold {{ $currView == 'incentive' ? 'bg-white text-indigo shadow-sm' : 'text-muted' }}">
            Insentif Bulanan
        </a>
        <a href="{{ route('absensi.rekap', ['view' => 'monitoring', 'bulan' => $startDate->month, 'tahun' => $startDate->year]) }}" 
           class="btn btn-sm px-3 rounded-pill fw-bold {{ $currView == 'monitoring' ? 'bg-white text-indigo shadow-sm' : 'text-muted' }}">
            Monitoring Izin
        </a>
    </div>
</div>

@if($currView == 'matrix')
    <!-- === VIEW 1: MATRIX HARIAN === -->
    <div class="card border-0 shadow-sm rounded-2xl overflow-hidden animate-in fade-in">
        <div class="card-header bg-white p-3 border-bottom d-flex justify-content-between align-items-center">
            <div>
                <h6 class="fw-bold text-dark text-uppercase mb-0 fs-7">Matriks Absensi</h6>
                <small class="text-muted fs-8">Rekap Harian • {{ $startDate->isoFormat('MMMM Y') }}</small>
            </div>
            <div class="d-flex gap-2">
                <!-- EXPORT ABSENSI (TYPE = ABSENSI) -->
                <a href="{{ route('absensi.download_excel', ['bulan' => $startDate->month, 'tahun' => $startDate->year, 'type' => 'absensi']) }}" 
                    class="btn btn-success btn-sm fw-bold px-3 rounded-3 shadow-sm text-white">
                    <i class="fa-solid fa-file-excel me-1"></i> Excel Absensi
                </a>
                <a href="{{ route('absensi.download_pdf_rekap', ['bulan' => $startDate->month, 'tahun' => $startDate->year]) }}" 
                    class="btn btn-danger btn-sm fw-bold px-3 rounded-3 shadow-sm text-white">
                    <i class="fa-solid fa-file-pdf me-1"></i> PDF
                </a>
            </div>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table-rekap mb-0">
                    <thead class="thead-grey sticky-top">
                        <tr>
                            <th class="sticky-col" style="min-width: 40px;">No</th>
                            <th class="sticky-col" style="min-width: 50px; left: 40px !important;">Tgl</th>
                            <th class="sticky-col" style="min-width: 80px; left: 90px !important;">Hari</th>
                            @foreach($anggota as $usr)
                                <th style="min-width: 40px;" title="{{ $usr['nama'] }}">{{ $usr['inisial'] }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dates as $index => $date)
                            @php
                                $dateString = $date->toDateString();
                                $isWeekend = $date->isSunday();
                                $isHoliday = array_key_exists($dateString, $hariLibur);
                                $holidayName = $hariLibur[$dateString] ?? '';
                                $rowClass = ($isWeekend || $isHoliday) ? 'row-holiday' : '';
                            @endphp

                            <tr class="{{ $rowClass }}">
                                <td style="position: sticky; left: 0; background: inherit; z-index: 10;">{{ $date->day }}</td>
                                <td style="position: sticky; left: 40px; background: inherit; z-index: 10;" class="text-start ps-3">{{ $date->isoFormat('dddd') }}</td>
                                <td style="position: sticky; left: 120px; background: inherit; z-index: 10;">{{ $date->format('d M Y') }}</td>

                                @if($isHoliday)
                                    <td colspan="{{ count($anggota) }}" class="text-center text-uppercase" style="letter-spacing: 2px;">{{ $holidayName }}</td>
                                @else
                                    @foreach($anggota as $usr)
                                        @php
                                            $status = $matrixData[$dateString][$usr['id']] ?? '';
                                            $cellClass = ''; $display = '';
                                            
                                            if ($status == 'H') { $cellClass = 'cell-hadir'; $display = '1'; }
                                            elseif ($status == 'O') { $cellClass = 'cell-lembur'; $display = '1'; }
                                            elseif ($status == 'DL') { $cellClass = 'cell-dinas bg-success'; $display = '1'; } 
                                            elseif ($status == 'DM') { $cellClass = 'cell-dinas bg-primary'; $display = '1'; }
                                        @endphp
                                        <td class="{{ $cellClass }}">{{ $display }}</td>
                                    @endforeach
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="card-footer bg-light border-top p-3">
            <div class="d-flex flex-wrap gap-3 small text-muted">
                <span><span class="badge bg-white border text-dark me-1">1</span> Hadir</span>
                <span><span class="badge bg-warning text-dark me-1">1</span> Lembur</span>
                <span><span class="badge bg-success text-white me-1">1</span> Dinas</span>
                <span><span class="badge bg-danger text-white me-1">LIBUR</span> Hari Libur</span>
            </div>
        </div>
    </div>

@elseif($currView == 'incentive')
    <!-- === VIEW 2: TABEL INSENTIF DETAIL === -->
    <div class="card border-0 shadow-sm rounded-2xl overflow-hidden mb-5 animate-in fade-in">
        <div class="card-header bg-white p-3 d-flex justify-content-between align-items-center border-bottom">
            <div>
                <h6 class="fw-bold text-uppercase mb-0 text-dark fs-7">Rangkuman Insentif Bulanan</h6>
                <small class="text-muted fw-bold fs-8">PERIODE: {{ $startDate->isoFormat('MMMM Y') }}</small>
            </div>
            
            <!-- EXPORT INSENTIF (TYPE = INSENTIF) -->
            <a href="{{ route('absensi.download_excel', ['bulan' => $startDate->month, 'tahun' => $startDate->year, 'type' => 'insentif']) }}"
                class="btn btn-primary btn-sm fw-bold px-3 rounded-3 shadow-sm text-white">
                <i class="fa-solid fa-file-invoice-dollar me-1"></i> Export Insentif
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered mb-0 align-middle" style="font-size: 0.75rem; border-color: #e5e7eb;">
                <thead class="align-middle bg-light text-dark fw-bold text-center text-uppercase">
                    <tr>
                        <th rowspan="2" style="min-width: 180px;" class="bg-white sticky-col start-0">NAMA PENGURUS</th>
                        
                        <!-- 1. Hadir -->
                        <th colspan="3" class="bg-white border-bottom-0">KEHADIRAN (Rp 100rb)</th>
                        
                        <!-- 2. Lembur -->
                        <th colspan="3" style="background-color: #fff7ed;">LEMBUR (>21.00) (Rp 50rb)</th>
                        
                        <!-- 3. Dinas Luar -->
                        <th colspan="3" style="background-color: #ecfdf5;">DINAS LUAR (Rp 150rb)</th>

                        <!-- 4. Dinas Menginap -->
                        <th colspan="3" style="background-color: #eff6ff;">DINAS MENGINAP (Rp 300rb)</th>

                        <th rowspan="2" class="bg-indigo text-white border-0" style="min-width: 110px;">TOTAL TERIMA</th>
                        <th rowspan="2" class="bg-white" style="min-width: 50px;">Slip</th>
                    </tr>
                    <tr class="small text-muted">
                        <!-- Sub Columns -->
                        <th width="40">Vol</th> <th width="70">Rate</th> <th width="90">Jml</th>
                        <th width="40" style="background-color: #fff7ed;">Vol</th> <th width="70" style="background-color: #fff7ed;">Rate</th> <th width="90" style="background-color: #fff7ed;">Jml</th>
                        <th width="40" style="background-color: #ecfdf5;">Vol</th> <th width="70" style="background-color: #ecfdf5;">Rate</th> <th width="90" style="background-color: #ecfdf5;">Jml</th>
                        <th width="40" style="background-color: #eff6ff;">Vol</th> <th width="70" style="background-color: #eff6ff;">Rate</th> <th width="90" style="background-color: #eff6ff;">Jml</th>
                    </tr>
                </thead>
                <tbody class="text-dark bg-white">
                    @foreach($rekapInsentif as $uid => $row)
                    <tr>
                        <td class="px-3 fw-bold sticky-col start-0 bg-white">{{ $row['nama'] }}</td>

                        <!-- 1. Hadir -->
                        <td class="text-center">{{ $row['jml_hadir'] }}</td>
                        <td class="text-end text-muted">100k</td>
                        <td class="text-end fw-bold">Rp {{ number_format($row['nominal_hadir'], 0, ',', '.') }}</td>

                        <!-- 2. Lembur -->
                        <td class="text-center" style="background-color: #fff7ed;">{{ $row['jml_lembur'] }}</td>
                        <td class="text-end text-muted" style="background-color: #fff7ed;">50k</td>
                        <td class="text-end fw-bold text-warning" style="background-color: #fff7ed;">Rp {{ number_format($row['nominal_lembur'], 0, ',', '.') }}</td>

                        <!-- 3. Dinas Luar -->
                        <td class="text-center" style="background-color: #ecfdf5;">{{ $row['jml_dl'] }}</td>
                        <td class="text-end text-muted" style="background-color: #ecfdf5;">150k</td>
                        <td class="text-end fw-bold text-success" style="background-color: #ecfdf5;">Rp {{ number_format($row['nominal_dl'], 0, ',', '.') }}</td>

                        <!-- 4. Dinas Menginap -->
                        <td class="text-center" style="background-color: #eff6ff;">{{ $row['jml_dm'] }}</td>
                        <td class="text-end text-muted" style="background-color: #eff6ff;">300k</td>
                        <td class="text-end fw-bold text-primary" style="background-color: #eff6ff;">Rp {{ number_format($row['nominal_dm'], 0, ',', '.') }}</td>

                        <!-- TOTAL -->
                        <td class="text-end fw-bold text-white bg-indigo fs-6">
                            Rp {{ number_format($row['total_terima'], 0, ',', '.') }}
                        </td>
                        <td class="text-center">
                             <a href="{{ route('absensi.download_pdf_slip', $uid) }}?bulan={{ $startDate->month }}&tahun={{ $startDate->year }}" target="_blank" class="btn btn-sm btn-light border text-danger py-0 px-2" title="Cetak Slip">
                                <i class="fa-solid fa-file-pdf"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@elseif($currView == 'monitoring')
    <!-- === VIEW 3: MONITORING IZIN (APPROVAL SYSTEM) === -->
    <div class="row g-4 animate-in fade-in">
        
        <!-- 1. DETAIL HARI INI & PENDING APPROVAL -->
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-2xl overflow-hidden">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold m-0 text-dark fs-7">
                        <i class="fa-solid fa-list-check me-2 text-indigo"></i> Permintaan Persetujuan & Monitoring Hari Ini
                    </h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered mb-0 align-middle" style="font-size: 0.8rem;">
                        <thead class="bg-light text-muted text-uppercase small">
                            <tr>
                                <th class="ps-4 py-3 bg-white">Tanggal</th>
                                <th class="py-3 bg-white">Nama Pengurus</th>
                                <th class="py-3 text-center bg-white">Tipe</th>
                                <th class="py-3 bg-white" style="width: 30%;">Keterangan</th>
                                <th class="py-3 text-center bg-white">Bukti</th>
                                <th class="py-3 text-center bg-white">Status</th>
                                <th class="py-3 text-center bg-white" style="width: 150px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white text-dark">
                            @forelse($monitoringHariIni as $row)
                            <tr class="hover-bg-light">
                                <td class="ps-4 text-muted">{{ \Carbon\Carbon::parse($row->tanggal)->format('d M Y') }}</td>
                                <td class="fw-bold">{{ $row->nama_lengkap }}</td>
                                <td class="text-center">
                                    <span class="badge bg-warning text-dark">{{ $row->status_kehadiran }}</span>
                                </td>
                                <td class="text-muted small">{{ $row->keterangan_tambahan }}</td>
                                <td class="text-center">
                                    @if($row->url_bukti)
                                        <a href="{{ route('absensi.view_bukti', $row->id) }}" target="_blank" class="btn btn-sm btn-light text-primary border rounded-3 px-2">
                                            <i class="fa-solid fa-paperclip me-1"></i>
                                        </a>
                                    @else - @endif
                                </td>
                                <td class="text-center">
                                    @if($row->status_validasi == 'PENDING')
                                        <span class="badge bg-secondary text-white">MENUNGGU</span>
                                    @elseif($row->status_validasi == 'APPROVED')
                                        <span class="badge bg-success text-white">DISETUJUI</span>
                                    @else
                                        <span class="badge bg-danger text-white">DITOLAK</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($row->status_validasi == 'PENDING')
                                        <div class="d-flex justify-content-center gap-1">
                                            <form action="{{ route('absensi.update_status', $row->id) }}" method="POST" class="form-approve">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="status" value="APPROVED">
                                                <button type="submit" class="btn btn-sm btn-success text-white shadow-sm"><i class="fa-solid fa-check"></i></button>
                                            </form>
                                            <form action="{{ route('absensi.update_status', $row->id) }}" method="POST" class="form-approve">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="status" value="REJECTED">
                                                <button type="submit" class="btn btn-sm btn-danger text-white shadow-sm"><i class="fa-solid fa-xmark"></i></button>
                                            </form>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">Tidak ada data approval.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 2. HISTORY TERAKHIR -->
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-2xl overflow-hidden">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold m-0 text-dark fs-7"><i class="fa-solid fa-clock-rotate-left me-2 text-indigo"></i> Riwayat Terakhir</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered mb-0 align-middle" style="font-size: 0.8rem;">
                        <thead class="bg-light text-muted text-uppercase small">
                            <tr>
                                <th class="ps-4 py-3 bg-white" style="width: 120px;">Tanggal</th>
                                <th class="py-3 bg-white">Nama Pengurus</th>
                                <th class="py-3 text-center bg-white" style="width: 100px;">Status</th>
                                <th class="py-3 text-center bg-white" style="width: 80px;">Bukti</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white text-dark">
                            @forelse($historyIzinDinas as $hist)
                            <tr class="hover-bg-light">
                                <td class="ps-4 text-muted">{{ \Carbon\Carbon::parse($hist->tanggal)->format('d/m/Y') }}</td>
                                <td class="fw-bold">{{ $hist->nama_lengkap }}</td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border">{{ $hist->status_kehadiran }}</span>
                                </td>
                                <td class="text-center">
                                    @if($hist->url_bukti)
                                        <a href="{{ route('absensi.view_bukti', $hist->id) }}" target="_blank"><i class="fa-solid fa-eye text-primary"></i></a>
                                    @else - @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">Belum ada riwayat.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const approveForms = document.querySelectorAll('.form-approve');
        approveForms.forEach(form => {
            form.addEventListener('submit', function() {
                const btn = this.querySelector('button');
                if(btn) { btn.disabled = true; }
            });
        });
    });
</script>

@endsection