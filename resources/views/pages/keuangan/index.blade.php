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
                    @if($isFinanceAdmin)
                        <button class="btn btn-success btn-sm ms-auto text-white" data-bs-toggle="modal" data-bs-target="#modal-pemasukan">
                            <i class="fa-solid fa-plus"></i>
                        </button>
                    @endif
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

<div class="row g-4 mb-4">
    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h6 class="fw-bold mb-0">Riwayat Pemasukan Terbaru</h6>
                <button class="btn btn-link text-success fw-bold text-decoration-none" data-bs-toggle="modal" data-bs-target="#modal-pemasukan-list">
                    <i class="fa-solid fa-list me-1"></i> Lihat Semua
                </button>
            </div>
            <div class="card-body">
                @forelse($recentPemasukan as $log)
                    <div class="d-flex flex-wrap align-items-center justify-content-between p-3 border rounded-3 mb-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-success bg-opacity-10 text-success px-2 py-1 rounded-3 small fw-bold">
                                {{ \Carbon\Carbon::parse($log->tanggal_transaksi)->format('d M Y') }}
                            </div>
                            <div>
                                <div class="fw-bold">{{ $log->sumber_dana }}</div>
                                <small class="text-muted">
                                    {{ $log->kategori_pemasukan ?? 'Pemasukan' }} - {{ $log->nama_divisi }}
                                </small>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold text-success">+ Rp {{ number_format($log->jumlah_rupiah, 0, ',', '.') }}</div>
                            <small class="text-muted">Dana masuk</small>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted">Belum ada pemasukan tercatat.</div>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h6 class="fw-bold mb-0">Riwayat Pengeluaran Terbaru (Global)</h6>
                <button class="btn btn-link text-primary fw-bold text-decoration-none" data-bs-toggle="modal" data-bs-target="#modal-pengeluaran-list">
                    <i class="fa-solid fa-list me-1"></i> Lihat Semua
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
    </div>
</div>

<div class="modal fade" id="modal-pemasukan-list" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <div>
                    <h5 class="modal-title fw-bold mb-1">Semua Riwayat Pemasukan</h5>
                    <small class="text-muted">Daftar lengkap transaksi dana masuk.</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-2 align-items-end mb-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label small">Cari pemasukan</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fa-solid fa-magnifying-glass"></i></span>
                            <input type="text" id="pemasukanSearchInput" class="form-control" placeholder="Sumber dana, kategori, atau divisi">
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light text-uppercase small text-muted">
                            <tr>
                                <th class="ps-4">Tanggal</th>
                                <th>Sumber</th>
                                <th>Kategori</th>
                                <th class="text-end">Nominal</th>
                                <th class="text-center">Bukti</th>
                                <th class="text-center pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="pemasukanTableBody" class="small">
                            @forelse($pemasukanList as $item)
                                <tr data-search="{{ strtolower($item->sumber_dana . ' ' . ($item->kategori_pemasukan ?? '') . ' ' . ($item->nama_divisi ?? '')) }}">
                                    <td class="ps-4">{{ \Carbon\Carbon::parse($item->tanggal_transaksi)->format('d M Y') }}</td>
                                    <td>
                                        <div class="fw-bold">{{ $item->sumber_dana }}</div>
                                        <div class="text-muted small">{{ $item->nama_divisi }}</div>
                                    </td>
                                    <td>{{ $item->kategori_pemasukan ?? 'Pemasukan' }}</td>
                                    <td class="text-end fw-bold text-success">+ Rp {{ number_format($item->jumlah_rupiah, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        @if($item->url_bukti)
                                            <a href="{{ $item->url_bukti }}" target="_blank" rel="noopener" class="text-success text-decoration-none">
                                                <i class="fa-regular fa-image me-1"></i> Ada
                                            </a>
                                        @else
                                            <span class="text-muted">Nihil</span>
                                        @endif
                                    </td>
                                    <td class="text-center pe-4">
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-secondary js-detail-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modal-detail-transaksi"
                                            data-type="pemasukan"
                                            data-date="{{ \Carbon\Carbon::parse($item->tanggal_transaksi)->format('d M Y') }}"
                                            data-title="{{ $item->sumber_dana }}"
                                            data-category="{{ $item->kategori_pemasukan ?? 'Pemasukan' }}"
                                            data-amount="{{ $item->jumlah_rupiah }}"
                                            data-description="{{ $item->keterangan ?? '' }}"
                                            data-attachment="{{ $item->url_bukti ?? '' }}"
                                        >
                                            Detail
                                        </button>
                                    </td>
                                </tr>
                            @empty
                            @endforelse
                            <tr id="pemasukanEmptyRow" class="{{ count($pemasukanList) > 0 ? 'd-none' : '' }}">
                                <td colspan="6" class="text-center text-muted py-4">Tidak ada data pemasukan.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-pengeluaran-list" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <div>
                    <h5 class="modal-title fw-bold mb-1">Semua Riwayat Pengeluaran</h5>
                    <small class="text-muted">Daftar lengkap transaksi dana keluar.</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-2 align-items-end mb-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label small">Cari pengeluaran</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fa-solid fa-magnifying-glass"></i></span>
                            <input type="text" id="pengeluaranSearchInput" class="form-control" placeholder="Uraian, kategori, atau divisi">
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light text-uppercase small text-muted">
                            <tr>
                                <th class="ps-4">Tanggal</th>
                                <th>Uraian</th>
                                <th>Kategori</th>
                                <th class="text-end">Nominal</th>
                                <th class="text-center">Bukti</th>
                                <th class="text-center pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="pengeluaranTableBody" class="small">
                            @forelse($pengeluaranList as $item)
                                <tr data-search="{{ strtolower(($item->uraian_pengeluaran ?: $item->nama_kegiatan) . ' ' . ($item->nama_program ?? '') . ' ' . ($item->nama_divisi ?? '')) }}">
                                    <td class="ps-4">{{ \Carbon\Carbon::parse($item->tanggal_transaksi)->format('d M Y') }}</td>
                                    <td>
                                        <div class="fw-bold">{{ $item->uraian_pengeluaran ?: $item->nama_kegiatan }}</div>
                                        <div class="text-muted small">{{ $item->nama_divisi }}</div>
                                    </td>
                                    <td>{{ $item->nama_program ?? 'Operasional' }}</td>
                                    <td class="text-end fw-bold text-danger">- Rp {{ number_format($item->total_nominal, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        @if($item->url_bukti_struk)
                                            <a href="{{ $item->url_bukti_struk }}" target="_blank" rel="noopener" class="text-primary text-decoration-none">
                                                <i class="fa-regular fa-image me-1"></i> Ada
                                            </a>
                                        @else
                                            <span class="text-muted">Nihil</span>
                                        @endif
                                    </td>
                                    <td class="text-center pe-4">
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-secondary js-detail-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modal-detail-transaksi"
                                            data-type="pengeluaran"
                                            data-date="{{ \Carbon\Carbon::parse($item->tanggal_transaksi)->format('d M Y') }}"
                                            data-title="{{ $item->uraian_pengeluaran ?: $item->nama_kegiatan }}"
                                            data-category="{{ $item->nama_program ?? 'Operasional' }}"
                                            data-amount="{{ $item->total_nominal }}"
                                            data-description="{{ $item->keterangan ?: ($item->uraian_pengeluaran ?: '') }}"
                                            data-attachment="{{ $item->url_bukti_struk ?? '' }}"
                                        >
                                            Detail
                                        </button>
                                    </td>
                                </tr>
                            @empty
                            @endforelse
                            <tr id="pengeluaranEmptyRow" class="{{ count($pengeluaranList) > 0 ? 'd-none' : '' }}">
                                <td colspan="6" class="text-center text-muted py-4">Tidak ada data pengeluaran.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-detail-transaksi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <div>
                    <h5 class="modal-title fw-bold mb-1" id="detailTitle">Detail Transaksi</h5>
                    <small class="text-muted" id="detailDate">Tanggal</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <span class="badge bg-secondary mb-3" id="detailCategory">Kategori</span>
                <div class="h4 fw-bold mb-3" id="detailAmount">Rp 0</div>
                <div class="mb-3">
                    <div class="text-muted small mb-1" id="detailLabel">Sumber/Keperluan</div>
                    <div class="fw-bold" id="detailSource">-</div>
                </div>
                <div class="mb-3">
                    <div class="text-muted small mb-1">Keterangan</div>
                    <div class="small" id="detailDescription">-</div>
                </div>
                <div>
                    <div class="text-muted small mb-1">Bukti Transaksi</div>
                    <div class="border rounded-3 p-2 small text-muted" id="detailAttachment">Tidak ada bukti lampiran.</div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($isFinanceAdmin)
    <div class="modal fade" id="modal-pemasukan" tabindex="-1" aria-hidden="true" data-auto-show="{{ old('form_context') === 'pemasukan' ? '1' : '0' }}">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <form method="POST" action="{{ route('keuangan.pemasukan') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="form_context" value="pemasukan">
                    <div class="modal-header bg-light">
                        <div>
                            <h5 class="modal-title fw-bold mb-1">Tambah Pemasukan</h5>
                            <small class="text-muted">Catat dana masuk ke kas organisasi.</small>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        @if($errors->has('pemasukan'))
                            <div class="alert alert-danger d-flex align-items-center" role="alert">
                                <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ $errors->first('pemasukan') }}
                            </div>
                        @endif
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold">Divisi</label>
                                <select name="id_divisi" class="form-select @error('id_divisi') is-invalid @enderror" required>
                                    <option value="">Pilih divisi</option>
                                    @foreach($divisiList as $divisi)
                                        <option value="{{ $divisi->id }}" {{ old('id_divisi') == $divisi->id ? 'selected' : '' }}>
                                            {{ $divisi->nama_divisi }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_divisi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold">Tanggal Terima</label>
                                <input type="date" name="tanggal_transaksi" class="form-control @error('tanggal_transaksi') is-invalid @enderror" value="{{ old('tanggal_transaksi', date('Y-m-d')) }}" required autocomplete="off">
                                @error('tanggal_transaksi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">Sumber Dana</label>
                                <input type="text" name="sumber_dana" class="form-control @error('sumber_dana') is-invalid @enderror" value="{{ old('sumber_dana') }}" placeholder="Contoh: PUK PT. XYZ" required autocomplete="off">
                                @error('sumber_dana')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold">Kategori (Opsional)</label>
                                <input type="text" name="kategori_pemasukan" class="form-control @error('kategori_pemasukan') is-invalid @enderror" value="{{ old('kategori_pemasukan') }}" placeholder="Iuran, donasi, usaha, dll" autocomplete="off">
                                @error('kategori_pemasukan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold">Nominal (Rp)</label>
                                <input type="number" name="jumlah_rupiah" class="form-control @error('jumlah_rupiah') is-invalid @enderror" value="{{ old('jumlah_rupiah') }}" min="10000" step="0" required autocomplete="off">
                                @error('jumlah_rupiah')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">Keterangan (Opsional)</label>
                                <textarea name="keterangan" rows="3" class="form-control @error('keterangan') is-invalid @enderror" placeholder="Catatan tambahan">{{ old('keterangan') }}</textarea>
                                @error('keterangan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">Bukti Transfer / Kuitansi</label>
                                <input type="file" name="bukti" class="form-control @error('bukti') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png" required>
                                @error('bukti')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success text-white">Simpan Pemasukan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

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

<script>
    window.addEventListener('load', function () {
        const pemasukanModal = document.getElementById('modal-pemasukan');
        if (pemasukanModal && pemasukanModal.dataset.autoShow === '1' && window.bootstrap) {
            const modal = new bootstrap.Modal(pemasukanModal);
            modal.show();
        }

        function formatRupiah(value) {
            return new Intl.NumberFormat('id-ID').format(value);
        }

        function renderAttachment(container, url) {
            container.innerHTML = '';
            if (!url) {
                container.textContent = 'Tidak ada bukti lampiran.';
                container.classList.add('text-muted');
                return;
            }

            container.classList.remove('text-muted');
            const lowerUrl = url.toLowerCase();
            if (lowerUrl.endsWith('.jpg') || lowerUrl.endsWith('.jpeg') || lowerUrl.endsWith('.png')) {
                const img = document.createElement('img');
                img.src = url;
                img.alt = 'Bukti Transaksi';
                img.className = 'img-fluid rounded-2';
                container.appendChild(img);
                return;
            }

            const link = document.createElement('a');
            link.href = url;
            link.target = '_blank';
            link.rel = 'noopener';
            link.textContent = 'Lihat Bukti';
            link.className = 'btn btn-sm btn-outline-primary';
            container.appendChild(link);
        }

        const detailTitle = document.getElementById('detailTitle');
        const detailDate = document.getElementById('detailDate');
        const detailCategory = document.getElementById('detailCategory');
        const detailAmount = document.getElementById('detailAmount');
        const detailSource = document.getElementById('detailSource');
        const detailLabel = document.getElementById('detailLabel');
        const detailDescription = document.getElementById('detailDescription');
        const detailAttachment = document.getElementById('detailAttachment');

        document.querySelectorAll('.js-detail-btn').forEach((button) => {
            button.addEventListener('click', function () {
                const type = button.dataset.type || 'pengeluaran';
                const amount = Number(button.dataset.amount || 0);
                const sign = type === 'pemasukan' ? '+' : '-';

                if (detailTitle) {
                    detailTitle.textContent = type === 'pemasukan' ? 'Detail Pemasukan' : 'Detail Pengeluaran';
                }
                if (detailDate) {
                    detailDate.textContent = button.dataset.date || '-';
                }
                if (detailCategory) {
                    detailCategory.textContent = button.dataset.category || '-';
                }
                if (detailAmount) {
                    detailAmount.textContent = sign + ' Rp ' + formatRupiah(amount);
                    detailAmount.classList.remove('text-success', 'text-danger');
                    detailAmount.classList.add(type === 'pemasukan' ? 'text-success' : 'text-danger');
                }
                if (detailSource) {
                    detailSource.textContent = button.dataset.title || '-';
                }
                if (detailLabel) {
                    detailLabel.textContent = type === 'pemasukan' ? 'Sumber Dana' : 'Keperluan';
                }
                if (detailDescription) {
                    detailDescription.textContent = button.dataset.description || '-';
                }
                if (detailAttachment) {
                    renderAttachment(detailAttachment, button.dataset.attachment || '');
                }
            });
        });

        function setupTableFilter(inputId, tableBodyId, emptyRowId) {
            const input = document.getElementById(inputId);
            const tbody = document.getElementById(tableBodyId);
            const emptyRow = document.getElementById(emptyRowId);
            if (!input || !tbody) {
                return;
            }
            input.addEventListener('input', function () {
                const term = input.value.trim().toLowerCase();
                let visibleCount = 0;
                tbody.querySelectorAll('tr[data-search]').forEach((row) => {
                    const text = row.dataset.search || '';
                    const match = term === '' || text.includes(term);
                    row.classList.toggle('d-none', !match);
                    if (match) {
                        visibleCount += 1;
                    }
                });
                if (emptyRow) {
                    emptyRow.classList.toggle('d-none', visibleCount > 0);
                }
            });
        }

        setupTableFilter('pemasukanSearchInput', 'pemasukanTableBody', 'pemasukanEmptyRow');
        setupTableFilter('pengeluaranSearchInput', 'pengeluaranTableBody', 'pengeluaranEmptyRow');

        // Prevent Double Submission (Mencegah input 2 kali)
        document.querySelectorAll('form').forEach(function (form) {
            form.addEventListener('submit', function (event) {
                // Cek validitas form HTML5 (required, dll)
                if (!form.checkValidity()) {
                    return; // Biarkan browser menampilkan error validasi
                }

                const btn = form.querySelector('button[type="submit"]');
                if (btn) {
                    // Simpan lebar/style original agar tidak jumping (opsional), 
                    // tapi di sini kita ubah text dan disable saja plus spinner.
                    const originalText = btn.innerHTML;
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> Menyimpan...';
                    
                    // Fallback (opsional): Jika koneksi putus atau back, 
                    // idealnya page reload. Jika user cancel navigation, button tetap disabled.
                    // Tapi ini perilaku standar yang aman.
                }
            });
        });
    });
</script>
@endsection
