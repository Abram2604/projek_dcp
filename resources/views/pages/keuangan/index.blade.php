@extends('layouts.app')
@section('title', 'Keuangan')
@section('header_title', 'Keuangan Organisasi')
@section('header_subtitle', 'Monitoring arus kas dan anggaran.')

@section('content')
@if(session('success'))
    <div class="alert alert-success d-flex align-items-center" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
    </div>
@endif

@if($errors->has('saldo_awal'))
    <div class="alert alert-danger d-flex align-items-center" role="alert">
        <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ $errors->first('saldo_awal') }}
    </div>
@endif

@php
    $totalSaldoAktif = (float) $summary->total_saldo_aktif;
    $pemasukanBulanIni = (float) $summary->pemasukan_bulan_ini;
    $totalPemasukanTahun = (float) $summary->total_pemasukan_tahun;
    $totalPengeluaranTahun = (float) $summary->total_pengeluaran_tahun;
    $persenPengeluaran = $totalPemasukanTahun > 0 ? ($totalPengeluaranTahun / $totalPemasukanTahun) * 100 : 0;
@endphp

<div class="row g-4 mb-4">
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-3">
                        <i class="fa-solid fa-wallet"></i>
                    </div>
                    <h6 class="text-muted fw-bold mb-0">Total Saldo Aktif</h6>
                </div>
                <h3 class="fw-bold mb-1">Rp {{ number_format($totalSaldoAktif, 0, ',', '.') }}</h3>
                <small class="text-success fw-bold">+ Rp {{ number_format($pemasukanBulanIni, 0, ',', '.') }} Bulan ini</small>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="bg-success bg-opacity-10 text-success p-2 rounded-3">
                        <i class="fa-solid fa-arrow-trend-up"></i>
                    </div>
                    <h6 class="text-muted fw-bold mb-0">Total Pemasukan</h6>
                </div>
                <h3 class="fw-bold mb-1">Rp {{ number_format($totalPemasukanTahun, 0, ',', '.') }}</h3>
                <small class="text-muted">Periode Jan - Des {{ $tahun }}</small>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="bg-danger bg-opacity-10 text-danger p-2 rounded-3">
                        <i class="fa-solid fa-arrow-trend-down"></i>
                    </div>
                    <h6 class="text-muted fw-bold mb-0">Total Pengeluaran</h6>
                </div>
                <h3 class="fw-bold mb-1">Rp {{ number_format($totalPengeluaranTahun, 0, ',', '.') }}</h3>
                <small class="text-danger fw-bold">{{ number_format($persenPengeluaran, 1) }}% dari total dana</small>
            </div>
        </div>
    </div>
</div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h6 class="fw-bold mb-0">Alokasi Dana per Bidang</h6>
        @if($isFinanceAdmin)
            <button class="btn btn-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modal-saldo-awal">
                <i class="fa-solid fa-plus me-1"></i> Atur Saldo Awal
            </button>
        @endif
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light text-uppercase small text-muted">
                <tr>
                    <th class="ps-4">Bidang / Divisi</th>
                    <th>Saldo Awal</th>
                    <th>Pengeluaran</th>
                    <th>Sisa Dana</th>
                    <th>Progres</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody class="small">
                @forelse($divisiSummary as $row)
                    @php
                        $saldoAwal = (float) $row->saldo_awal;
                        $pengeluaran = (float) $row->total_pengeluaran;
                        $sisa = (float) $row->sisa_saldo;
                        $percent = $saldoAwal > 0 ? ($pengeluaran / $saldoAwal) * 100 : 0;
                        $progressClass = $percent > 80 ? 'bg-danger' : 'bg-success';
                    @endphp
                    <tr>
                        <td class="ps-4 fw-bold">{{ $row->nama_divisi }}</td>
                        <td>Rp {{ number_format($saldoAwal, 0, ',', '.') }}</td>
                        <td class="text-muted">Rp {{ number_format($pengeluaran, 0, ',', '.') }}</td>
                        <td class="fw-bold text-primary">Rp {{ number_format($sisa, 0, ',', '.') }}</td>
                        <td>
                            <div class="progress" style="height: 6px; width: 100px;">
                                <div class="progress-bar {{ $progressClass }}" role="progressbar" style="width: {{ min(100, $percent) }}%"></div>
                            </div>
                        </td>
                        <td class="text-end pe-4">
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modal-detail-{{ $row->id }}">
                                Lihat Detail <i class="fa-solid fa-chevron-right ms-1"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Belum ada data keuangan bulan ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h6 class="fw-bold mb-0">Riwayat Pengeluaran Terbaru (Global)</h6>
        <button class="btn btn-link text-primary fw-bold text-decoration-none">
            <i class="fa-solid fa-download me-1"></i> Laporan PDF
        </button>
    </div>
    <div class="card-body">
        @forelse($recentPengeluaran as $log)
            <div class="d-flex flex-wrap align-items-center justify-content-between p-3 border rounded-3 mb-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary bg-opacity-10 text-primary px-2 py-1 rounded-3 small fw-bold">
                        {{ \Carbon\Carbon::parse($log->tanggal_transaksi)->format('d M Y') }}
                    </div>
                    <div>
                        <div class="fw-bold">{{ $log->uraian_pengeluaran ?: $log->nama_kegiatan }}</div>
                        <small class="text-muted">
                            {{ $log->nama_program ?? 'Operasional' }} - {{ $log->nama_divisi }}
                        </small>
                    </div>
                </div>
                <div class="text-end">
                    <div class="fw-bold text-danger">- Rp {{ number_format($log->total_nominal, 0, ',', '.') }}</div>
                    <small class="text-muted">Approved by Bendahara</small>
                </div>
            </div>
        @empty
            <div class="text-center text-muted">Belum ada pengeluaran tercatat.</div>
        @endforelse
    </div>
</div>

@foreach($divisiSummary as $row)
    @php
        $detailItems = $detailPerDivisi[$row->id] ?? [];
        $totalDetail = 0;
        foreach ($detailItems as $item) {
            $totalDetail += (float) $item->total_nominal;
        }
    @endphp
    <div class="modal fade" id="modal-detail-{{ $row->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <div>
                        <h5 class="modal-title fw-bold mb-1">Rincian Pengeluaran</h5>
                        <small class="text-muted">Divisi: <span class="fw-bold text-primary">{{ $row->nama_divisi }}</span></small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @if(count($detailItems) > 0)
                        <div class="list-group list-group-flush">
                            @foreach($detailItems as $item)
                                <div class="list-group-item px-0">
                                    <div class="d-flex flex-wrap justify-content-between gap-2">
                                        <div class="d-flex gap-3">
                                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="fa-regular fa-file-lines"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $item->uraian_pengeluaran ?: $item->nama_kegiatan }}</div>
                                                <div class="text-muted small d-flex align-items-center gap-2">
                                                    <span>{{ \Carbon\Carbon::parse($item->tanggal_transaksi)->format('d M Y') }}</span>
                                                    <span class="text-muted">•</span>
                                                    <span class="badge bg-light text-dark">{{ $item->nama_program ?? 'Operasional' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <div class="fw-bold text-danger">- Rp {{ number_format($item->total_nominal, 0, ',', '.') }}</div>
                                            <small class="text-muted">{{ $item->volume }} {{ $item->satuan }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-3 p-3 bg-primary bg-opacity-10 rounded-3">
                            <span class="fw-bold">Total Terpakai</span>
                            <span class="fw-bold text-primary">Rp {{ number_format($totalDetail, 0, ',', '.') }}</span>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">Belum ada data rinci untuk divisi ini.</div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endforeach

@if($isFinanceAdmin)
    @php
        $bulanOptions = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
    @endphp
    <div class="modal fade" id="modal-saldo-awal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <form method="POST" action="{{ route('keuangan.saldo_awal') }}">
                    @csrf
                    <div class="modal-header bg-light">
                        <div>
                            <h5 class="modal-title fw-bold mb-1">Atur Saldo Awal</h5>
                            <small class="text-muted">Set saldo awal per divisi untuk periode tertentu.</small>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Bulan</label>
                                <select name="bulan" class="form-select">
                                    @foreach($bulanOptions as $num => $label)
                                        <option value="{{ $num }}" {{ (int) old('bulan', $bulan) === $num ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tahun</label>
                                <input type="number" name="tahun" class="form-control" value="{{ old('tahun', $tahun) }}" min="2000">
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead class="table-light text-uppercase small text-muted">
                                    <tr>
                                        <th class="ps-3">Divisi</th>
                                        <th>Saldo Awal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($divisiSummary as $row)
                                        <tr>
                                            <td class="ps-3 fw-bold">{{ $row->nama_divisi }}</td>
                                            <td>
                                                <input
                                                    type="number"
                                                    name="saldo_awal[{{ $row->id }}]"
                                                    class="form-control"
                                                    min="0"
                                                    step="1000"
                                                    value="{{ old('saldo_awal.' . $row->id, $row->saldo_awal) }}"
                                                >
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <small class="text-muted">Biarkan kosong jika tidak ingin mengubah saldo divisi tertentu.</small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Saldo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
@endsection
