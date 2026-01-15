@extends('layouts.app')
@section('title', 'Detail Laporan')
@section('header_title', 'Detail Laporan')
@section('header_subtitle', 'Rincian kegiatan dan pengeluaran.')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <a href="{{ route('laporan.index') }}" class="btn btn-light">
        <i class="fa-solid fa-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between flex-wrap gap-2">
        <div>
            <h5 class="fw-bold mb-1">{{ $laporan->judul_kegiatan }}</h5>
            <div class="text-muted small">
                <i class="fa-regular fa-calendar me-1"></i>
                {{ \Carbon\Carbon::parse($laporan->tanggal_laporan)->format('d M Y') }}
            </div>
        </div>
        <div class="text-end">
            <span class="badge bg-primary bg-opacity-10 text-primary text-uppercase me-1">{{ $laporan->kode_divisi ?? 'DIV' }}</span>
            @if($laporan->status_laporan === 'DISUBMIT')
                <span class="badge bg-success bg-opacity-10 text-success">Disubmit</span>
            @else
                <span class="badge bg-warning bg-opacity-10 text-warning">Draft</span>
            @endif
        </div>
    </div>
    <div class="card-body">
        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <div class="text-muted small">Divisi</div>
                <div class="fw-bold">{{ $laporan->nama_divisi }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">Pelapor</div>
                <div class="fw-bold">{{ $laporan->nama_lengkap }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">Program Kerja</div>
                <div class="fw-bold">{{ $laporan->nama_program ?? 'Tanpa program kerja' }}</div>
            </div>
        </div>
        <div class="mb-3">
            <div class="text-muted small">Deskripsi / Hasil Kegiatan</div>
            <p class="mb-0">{{ $laporan->isi_laporan }}</p>
        </div>
        @if($laporan->url_lampiran)
            <div>
                <a href="{{ $laporan->url_lampiran }}" target="_blank" class="btn btn-outline-primary btn-sm">
                    <i class="fa-solid fa-paperclip me-1"></i> Lihat Lampiran
                </a>
            </div>
        @endif
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h6 class="fw-bold mb-0">Rincian Pengeluaran</h6>
    </div>
    <div class="card-body p-0">
        @if(count($pengeluaran) > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase small">
                        <tr>
                            <th class="ps-4">Uraian</th>
                            <th>Vol</th>
                            <th>Satuan</th>
                            <th class="text-end">Jumlah</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pengeluaran as $item)
                            <tr>
                                <td class="ps-4">{{ $item->uraian_pengeluaran }}</td>
                                <td>{{ $item->volume }}</td>
                                <td>{{ $item->satuan }}</td>
                                <td class="text-end">Rp {{ number_format($item->jumlah_rupiah, 0, ',', '.') }}</td>
                                <td class="text-end fw-bold">Rp {{ number_format($item->total_nominal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-light">
                            <th colspan="4" class="text-end">Total Pengeluaran</th>
                            <th class="text-end">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="p-4 text-center text-muted">Tidak ada pengeluaran tercatat untuk laporan ini.</div>
        @endif
    </div>
</div>
@endsection
