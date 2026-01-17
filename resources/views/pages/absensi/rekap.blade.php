@extends('layouts.app')

@section('title', 'Rekapitulasi')
@section('header_title', 'Attendance')
@section('header_subtitle', 'Rekapitulasi absensi dan insentif pengurus.')

@section('content')

<!-- CSS IMPROVEMENT -->
<style>
    /* Button Colors Fix */
    .btn-info, .btn-primary, .btn-success, .btn-danger, .bg-indigo { color: #ffffff !important; }
    
    /* Table Styles */
    .table-rekap { border-collapse: separate; border-spacing: 0; width: 100%; border: 1px solid #e5e7eb; font-size: 0.8rem; }
    .table-rekap th, .table-rekap td { border: 1px solid #e5e7eb; padding: 8px 4px; text-align: center; vertical-align: middle; }
    .thead-grey th { background-color: #f3f4f6; color: #374151; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; }
    
    /* UX: Sticky Column untuk Matrix */
    .table-responsive { max-height: 70vh; overflow: auto; }
    .table-rekap thead th { position: sticky; top: 0; z-index: 10; border-top: 0; }
    
    /* Status Colors */
    .row-holiday td { background-color: #ef4444 !important; color: white !important; font-weight: bold; border-color: #dc2626; }
    .cell-dinas { background-color: #10b981 !important; color: white !important; font-weight: 700; }
    .cell-lembur { background-color: #facc15 !important; color: #111827 !important; font-weight: 800; }
    .cell-hadir { color: #374151; font-weight: 500; }
    
    /* Hover */
    .table-rekap tbody tr:hover td { background-color: #f3f4f6; }
    .table-rekap tbody tr:hover td.cell-lembur { background-color: #fbbf24 !important; }
    .table-rekap tbody tr:hover td.cell-dinas { background-color: #059669 !important; }

    /* Legend Box Style */
    .legend-box {
        width: 24px; height: 24px; 
        border-radius: 6px; 
        border: 1px solid #e5e7eb; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        font-weight: 700; 
        font-size: 0.7rem;
        margin-right: 8px;
    }
</style>

{{-- 1. TAB MENU UTAMA --}}
<div class="mb-4">
    <div class="bg-white p-2 rounded-2xl shadow-sm d-inline-block border border-light">
        <ul class="nav nav-pills nav-pills-custom">
            <li class="nav-item"><a href="{{ route('absensi.index', ['tab' => 'qr']) }}" class="nav-link text-muted">Scan QR</a></li>
            <li class="nav-item"><a href="{{ route('absensi.index', ['tab' => 'dinas']) }}" class="nav-link text-muted">Absen Dinas</a></li>
            <li class="nav-item"><a href="{{ route('absensi.index', ['tab' => 'izin']) }}" class="nav-link text-muted">Izin/Sakit</a></li>
            <li class="nav-item"><a href="#" class="nav-link active bg-indigo text-white shadow-sm">Rekap (BPH)</a></li>
        </ul>
    </div>
</div>

{{-- 2. NAVIGASI BULAN & SUB-MENU TAMPILAN --}}
<div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
    <!-- A. Navigasi Bulan -->
    <div class="d-flex align-items-center bg-white rounded-pill shadow-sm border px-1 py-1">
        <a href="{{ route('absensi.rekap', ['bulan' => $prevDate->month, 'tahun' => $prevDate->year, 'view' => request('view')]) }}" 
           class="btn btn-light rounded-circle text-muted" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
            <i class="fa-solid fa-chevron-left"></i>
        </a>
        <span class="fw-bold text-dark mx-4 text-uppercase" style="min-width: 120px; text-align: center;">{{ $startDate->isoFormat('MMMM Y') }}</span>
        <a href="{{ route('absensi.rekap', ['bulan' => $nextDate->month, 'tahun' => $nextDate->year, 'view' => request('view')]) }}" 
           class="btn btn-light rounded-circle text-muted" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
            <i class="fa-solid fa-chevron-right"></i>
        </a>
    </div>

    <!-- B. Sub-Menu Pilihan Tampilan -->
    @php $currView = request('view', 'matrix'); @endphp
    <div class="bg-light p-1 rounded-3xl d-inline-flex border border-gray-200">
        <a href="{{ route('absensi.rekap', ['view' => 'matrix', 'bulan' => $startDate->month, 'tahun' => $startDate->year]) }}" 
           class="btn btn-sm px-3 rounded-3xl fw-bold {{ $currView == 'matrix' ? 'bg-white text-indigo shadow-sm' : 'text-muted' }}">
            Absensi Harian
        </a>
        <a href="{{ route('absensi.rekap', ['view' => 'incentive', 'bulan' => $startDate->month, 'tahun' => $startDate->year]) }}" 
           class="btn btn-sm px-3 rounded-3xl fw-bold {{ $currView == 'incentive' ? 'bg-white text-indigo shadow-sm' : 'text-muted' }}">
            Insentif Bulanan
        </a>
        <a href="{{ route('absensi.rekap', ['view' => 'monitoring', 'bulan' => $startDate->month, 'tahun' => $startDate->year]) }}" 
           class="btn btn-sm px-3 rounded-3xl fw-bold {{ $currView == 'monitoring' ? 'bg-white text-indigo shadow-sm' : 'text-muted' }}">
            Monitoring Izin
        </a>
    </div>
</div>

@if($currView == 'matrix')
    <!-- === VIEW 1: MATRIX HARIAN === -->
    <div class="card border-0 shadow-soft rounded-2xl overflow-hidden animate-in fade-in">
        <div class="card-header bg-white p-4 border-bottom d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold text-dark text-uppercase mb-1">DPC FSP LEM SPSI KARAWANG</h5>
                <small class="text-muted">Rekap Absensi Harian Pengurus • {{ $startDate->isoFormat('MMMM Y') }}</small>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('absensi.download_excel', ['bulan' => $startDate->month, 'tahun' => $startDate->year]) }}" 
                    class="btn btn-success btn-sm fw-bold px-3 rounded-3 shadow-sm text-white">
                    <i class="fa-solid fa-file-excel me-2"></i>Excel
                </a>
                <a href="{{ route('absensi.download_pdf_rekap', ['bulan' => $startDate->month, 'tahun' => $startDate->year]) }}" 
                    class="btn btn-danger btn-sm fw-bold px-3 rounded-3 shadow-sm text-white">
                    <i class="fa-solid fa-file-pdf me-2"></i>PDF
                </a>
            </div>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table-rekap mb-0">
                    <thead class="thead-grey">
                        <tr>
                            <th style="width: 40px; position: sticky; left: 0; z-index: 20;">NO</th>
                            <th style="width: 80px; position: sticky; left: 40px; z-index: 20;">HARI</th>
                            <th style="width: 120px; position: sticky; left: 120px; z-index: 20;">TANGGAL</th>
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
                                <td style="position: sticky; left: 40px; background: inherit; z-index: 10;">{{ $date->isoFormat('dddd') }}</td>
                                <td style="position: sticky; left: 120px; background: inherit; z-index: 10;" class="text-start ps-3">{{ $date->format('d M Y') }}</td>

                                @if($isHoliday)
                                    <td colspan="{{ count($anggota) }}" class="text-center text-uppercase" style="letter-spacing: 2px;">{{ $holidayName }}</td>
                                @else
                                    @foreach($anggota as $usr)
                                        @php
                                            $status = $matrixData[$dateString][$usr['id']] ?? '';
                                            $cellClass = ''; $display = '';
                                            if ($isWeekend) {
                                                if ($status == 'O') { $cellClass = 'cell-lembur'; $display = '1'; }
                                                elseif ($status == 'D') { $cellClass = 'cell-dinas'; $display = '1'; }
                                            } else {
                                                if ($status == 'H') { $cellClass = 'cell-hadir'; $display = '1'; }
                                                elseif ($status == 'O') { $cellClass = 'cell-lembur'; $display = '1'; }
                                                elseif ($status == 'D') { $cellClass = 'cell-dinas'; $display = '1'; }
                                            }
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

        {{-- LEGENDA MATRIX --}}
        <div class="card-footer bg-white border-top p-3">
            <h6 class="fw-bold small text-uppercase text-muted mb-2">Keterangan / Legenda:</h6>
            <div class="d-flex flex-wrap gap-3">
                <div class="d-flex align-items-center">
                    <div class="legend-box cell-hadir">1</div>
                    <span class="small text-muted">Hadir (Kantor)</span>
                </div>
                <div class="d-flex align-items-center">
                    <div class="legend-box cell-lembur">1</div>
                    <span class="small text-muted">Overtime (> 21.00)</span>
                </div>
                <div class="d-flex align-items-center">
                    <div class="legend-box cell-dinas">1</div>
                    <span class="small text-muted">Dinas Luar</span>
                </div>
                <div class="d-flex align-items-center">
                    <div class="legend-box row-holiday">L</div>
                    <span class="small text-muted">Libur/Minggu</span>
                </div>
                <div class="d-flex align-items-center">
                    <div class="legend-box bg-white text-muted">X</div>
                    <span class="small text-muted">Tidak Hadir / Ditolak</span>
                </div>
            </div>
        </div>
    </div>

@elseif($currView == 'incentive')
    <!-- === VIEW 2: TABEL INSENTIF === -->
    <div class="card border-0 shadow-soft rounded-2xl overflow-hidden mb-5 animate-in fade-in">
        <div class="card-header bg-white p-4 d-flex justify-content-between align-items-center border-bottom">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-indigo-50 p-2 rounded-3 text-indigo">
                    <i class="fa-solid fa-wallet fs-4"></i>
                </div>
                <div>
                    <h6 class="fw-bold text-uppercase mb-0 text-dark">Rekap Dana Kehadiran Pengurus</h6>
                    <small class="text-muted fw-bold" style="font-size: 11px;">PERIODE: {{ $startDate->year }}</small>
                </div>
            </div>
            <div>
                <a href="{{ route('absensi.download_excel', ['bulan' => $startDate->month, 'tahun' => $startDate->year]) }}"
                    class="btn btn-success btn-sm fw-bold px-3 rounded-3 shadow-sm text-white">
                    <i class="fa-solid fa-file-excel me-2"></i>Unduh Excel
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered mb-0 align-middle" style="font-size: 0.8rem; border-color: #e5e7eb;">
                <thead class="text-center text-uppercase align-middle bg-light">
                    <tr>
                        <th rowspan="2" class="px-3 py-3 text-start bg-white" style="min-width: 200px;">Nama Pengurus</th>
                        <th colspan="3" class="bg-white">Jumlah</th>
                        <th rowspan="2" class="px-3 bg-white text-end" style="min-width: 120px;">Kehadiran</th>
                        <th rowspan="2" class="px-3 text-end" style="min-width: 120px; background-color: #fcd34d;">Up 21.00 WIB</th>
                        <th rowspan="2" class="px-3 text-end text-white" style="min-width: 120px; background-color: #10b981;">Dinas Luar</th>
                        <th rowspan="2" class="px-3 bg-white text-end fw-bold" style="min-width: 140px;">Total/Orang</th>
                        <th rowspan="2" class="fw-bold" style="background-color: #fcd34d;">Total OT</th>
                        <th rowspan="2" class="fw-bold text-white" style="background-color: #10b981;">Total DL</th>
                        <th rowspan="2" class="bg-light">Aksi</th>
                    </tr>
                    <tr>
                        <th width="40" class="bg-white">H</th>
                        <th width="40" class="bg-white">I</th>
                        <th width="40" class="bg-white">S</th>
                    </tr>
                </thead>
                <tbody class="text-dark bg-white">
                    @foreach($rekapInsentif as $uid => $row)
                    <tr class="hover-bg-light">
                        <td class="px-3 py-3 fw-bold">{{ $row['nama'] }}</td>
                        <td class="text-center">{{ $row['hadir'] }}</td>
                        <td class="text-center">{{ $row['izin'] }}</td>
                        <td class="text-center">{{ $row['sakit'] }}</td>
                        
                        <td class="text-end px-3 font-monospace">Rp {{ number_format($row['nominal_hadir'], 0, ',', '.') }}</td>
                        <td class="text-end px-3 font-monospace" style="background-color: #fef3c7;">Rp 0</td> 
                        <td class="text-end px-3 font-monospace" style="background-color: #d1fae5;">Rp {{ number_format($row['nominal_dinas'], 0, ',', '.') }}</td>
                        <td class="text-end px-3 fw-bold font-monospace">Rp {{ number_format($row['total_terima'], 0, ',', '.') }}</td>
                        
                        <td class="text-center fw-bold" style="background-color: #fef3c7;">0</td>
                        <td class="text-center fw-bold" style="background-color: #d1fae5;">{{ $row['dinas'] }}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-light text-indigo rounded-circle shadow-sm" style="width: 32px; height: 32px;" data-bs-toggle="modal" data-bs-target="#slipModal{{ $uid }}">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    {{-- MODAL SLIP --}}
    @foreach($rekapInsentif as $uid => $row)
    <div class="modal fade" id="slipModal{{ $uid }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
                <div style="height: 8px; background: linear-gradient(to right, #4f46e5, #8b5cf6, #ec4899);"></div>
                <div class="modal-body p-4 p-md-5">
                    <div class="text-center mb-4 border-bottom pb-3 border-secondary border-opacity-10">
                        <img src="{{ asset('img/logo.png') }}" alt="Logo" style="height: 40px;" class="mb-2 opacity-75 grayscale">
                        <h5 class="fw-bold text-uppercase mb-0 text-dark ls-1">Slip Insentif</h5>
                        <small class="text-muted text-uppercase d-block mt-1 fw-bold" style="font-size: 10px;">DPC FSP LEM SPSI KARAWANG</small>
                        <div class="badge bg-light text-dark mt-2 border">Periode: {{ $startDate->format('F Y') }}</div>
                    </div>
                    
                    <div class="row small mb-4">
                        <div class="col-6">
                            <div class="text-muted text-uppercase fw-bold" style="font-size: 9px;">Nama Pengurus</div>
                            <div class="fw-bold text-dark fs-6">{{ $row['nama'] }}</div>
                        </div>
                        <div class="col-6 text-end">
                            <div class="text-muted text-uppercase fw-bold" style="font-size: 9px;">ID Referensi</div>
                            <div class="fw-bold text-dark font-monospace">SLIP-{{ $uid }}-{{ date('mY') }}</div>
                        </div>
                    </div>

                    <div class="table-responsive mb-4 rounded-3 border border-secondary border-opacity-10">
                        <table class="table table-sm small mb-0">
                            <thead class="bg-light text-uppercase text-muted" style="font-size: 9px;">
                                <tr>
                                    <th class="ps-3 py-2 border-bottom-0">Sumber Penghasilan</th>
                                    <th class="text-center py-2 border-bottom-0">Vol</th>
                                    <th class="text-end pe-3 py-2 border-bottom-0">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="text-dark">
                                <tr>
                                    <td class="ps-3 py-2 border-secondary border-opacity-10">
                                        <div class="fw-bold">Uang Hadir / Rapat</div>
                                        <small class="text-muted" style="font-size: 9px;">@ Rp 100.000 / kehadiran</small>
                                    </td>
                                    <td class="text-center py-2 align-middle border-secondary border-opacity-10">{{ $row['hadir'] }}</td>
                                    <td class="text-end pe-3 py-2 align-middle fw-medium border-secondary border-opacity-10">
                                        Rp {{ number_format($row['nominal_hadir'], 0, ',', '.') }}
                                    </td>
                                </tr>
                                @if($row['dinas'] > 0)
                                <tr>
                                    <td class="ps-3 py-2 border-secondary border-opacity-10 border-bottom-0">
                                        <div class="fw-bold">Dinas Luar Kota</div>
                                        <small class="text-muted" style="font-size: 9px;">@ Rp 150.000 / kegiatan</small>
                                    </td>
                                    <td class="text-center py-2 align-middle border-secondary border-opacity-10 border-bottom-0">{{ $row['dinas'] }}</td>
                                    <td class="text-end pe-3 py-2 align-middle fw-medium border-secondary border-opacity-10 border-bottom-0">
                                        Rp {{ number_format($row['nominal_dinas'], 0, ',', '.') }}
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="bg-indigo-50 p-4 rounded-3 d-flex justify-content-between align-items-center border border-indigo-100 position-relative overflow-hidden">
                        <div style="position: absolute; right: -10px; bottom: -20px; font-size: 80px; opacity: 0.05; transform: rotate(-15deg);"><i class="fa-solid fa-money-bill-wave"></i></div>
                        <div style="z-index: 1;">
                            <div class="fw-bold text-indigo text-uppercase small ls-1">Total Diterima</div>
                            <div class="text-muted" style="font-size: 10px;">Via Transfer Bank BJB</div>
                        </div>
                        <div class="fs-2 fw-bold text-indigo" style="z-index: 1;">Rp {{ number_format($row['total_terima'], 0, ',', '.') }}</div>
                    </div>
                    
                    <div class="text-center mt-3">
                        <small class="text-muted fst-italic" style="font-size: 9px;">* Dokumen ini digenerate otomatis oleh sistem dan sah tanpa tanda tangan basah.</small>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 p-3">
                    <button type="button" class="btn btn-white bg-white border flex-fill rounded-3 fw-bold py-2 shadow-sm" data-bs-dismiss="modal">Tutup</button>
                    <a href="{{ route('absensi.download_pdf_slip', ['userId' => $uid, 'bulan' => $startDate->month, 'tahun' => $startDate->year]) }}" 
                    class="btn bg-indigo text-white flex-fill rounded-3 fw-bold shadow-sm py-2 text-decoration-none text-center">
                        <i class="fa-solid fa-print me-2"></i> Cetak / Unduh PDF
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endforeach

@elseif($currView == 'monitoring')
    <!-- === VIEW 3: MONITORING IZIN (APPROVAL SYSTEM) === -->
    <div class="row g-4 animate-in fade-in">
        
        <!-- 1. DETAIL HARI INI & PENDING APPROVAL (Tidak Berubah) -->
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-2xl overflow-hidden">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold m-0 text-dark">
                        <i class="fa-solid fa-list-check me-2 text-indigo"></i> Permintaan Persetujuan & Monitoring Hari Ini
                    </h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered mb-0 align-middle" style="font-size: 0.85rem;">
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
                                    @if($row->status_kehadiran == 'DINAS')
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info rounded-pill px-3">DINAS</span>
                                    @elseif($row->status_kehadiran == 'SAKIT')
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger rounded-pill px-3">SAKIT</span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning rounded-pill px-3">IZIN</span>
                                    @endif
                                </td>
                                <td class="text-muted small">{{ $row->keterangan_tambahan }}</td>
                                <td class="text-center">
                                    @if($row->url_bukti)
                                        <a href="{{ route('absensi.view_bukti', $row->id) }}" target="_blank" class="btn btn-sm btn-light text-primary border rounded-3 px-2">
                                            <i class="fa-solid fa-paperclip me-1"></i> Lihat
                                        </a>
                                    @else
                                        <span class="text-muted small fst-italic">-</span>
                                    @endif
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
                                                <button type="submit" class="btn btn-sm btn-success text-white shadow-sm" title="Setujui">
                                                    <i class="fa-solid fa-check"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('absensi.update_status', $row->id) }}" method="POST" class="form-approve">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="status" value="REJECTED">
                                                <button type="submit" class="btn btn-sm btn-danger text-white shadow-sm" title="Tolak">
                                                    <i class="fa-solid fa-xmark"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <small class="text-muted fst-italic">Selesai</small>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <i class="fa-regular fa-circle-check fa-2x mb-3 d-block opacity-50"></i>
                                    Tidak ada data yang perlu divalidasi hari ini.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 2. HISTORY TERAKHIR (BAGIAN YANG DIUPDATE) -->
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-2xl overflow-hidden">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold m-0 text-dark"><i class="fa-solid fa-clock-rotate-left me-2 text-indigo"></i> Riwayat Terakhir (20 Data)</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered mb-0 align-middle" style="font-size: 0.85rem;">
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
                                <td class="ps-4 text-muted font-monospace">{{ \Carbon\Carbon::parse($hist->tanggal)->format('d/m/Y') }}</td>
                                <td class="fw-bold">{{ $hist->nama_lengkap }}</td>
                                <td class="text-center">
                                    {{-- UPDATE: Menggunakan Badge Lengkap, bukan inisial --}}
                                    @if($hist->status_kehadiran == 'DINAS')
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info rounded-pill px-3">DINAS</span>
                                    @elseif($hist->status_kehadiran == 'SAKIT')
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger rounded-pill px-3">SAKIT</span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning rounded-pill px-3">IZIN</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($hist->url_bukti)
                                        <a href="{{ route('absensi.view_bukti', $hist->id) }}" target="_blank" class="btn btn-sm btn-light text-indigo rounded-circle shadow-sm" style="width: 32px; height: 32px;">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-5">
                                    <i class="fa-regular fa-folder-open fa-2x mb-3 d-block opacity-50"></i>
                                    Belum ada riwayat.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endif

<script>
    // UX: Prevent Double Click on Approval & Show Spinner
    document.addEventListener('DOMContentLoaded', function() {
        const approveForms = document.querySelectorAll('.form-approve');
        approveForms.forEach(form => {
            form.addEventListener('submit', function() {
                const btn = this.querySelector('button');
                if(btn) {
                    btn.disabled = true;
                    // Simpan icon lama (optional, bisa replace content)
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
                }
            });
        });
    });
</script>

@endsection