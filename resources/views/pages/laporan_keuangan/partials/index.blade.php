@extends('layouts.app')
@section('header_title', 'Laporan Keuangan')
@section('header_subtitle', 'Laporan arus kas, posisi keuangan, dan rekapitulasi.')
@section('content')
<div class="container-fluid" id="appKeuangan">
{{-- Notifikasi --}}
@if(session('success'))
<div class="alert alert-success border-0 shadow-sm mb-4">
<i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
</div>
@endif
<!-- CONTROL BAR -->
<div class="card border-0 shadow-sm mb-4 no-print">
<div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
<!-- Menu Tipe Laporan -->
@php
    // Helper untuk Judul di Dropdown Mobile
    $labels = [
        'flow' => 'Dana Masuk/Keluar',
        'position' => 'Posisi Keuangan',
        'summary' => 'Laporan Organisasi'
    ];
    $currentLabel = $labels[$type] ?? 'Dana Masuk/Keluar';
@endphp

<!-- OPTION 1: TAMPILAN DESKTOP (Muncul di Laptop/Tablet) -->
<div class="d-none d-md-block">
    <div class="btn-group bg-light p-1 rounded-3 w-100">
        <a href="?type=flow&bulan={{$bulan}}&tahun={{$tahun}}"
           class="btn btn-sm {{ $type == 'flow' ? 'btn-white shadow-sm fw-bold text-primary' : 'text-muted' }}">
            Dana Masuk/Keluar
        </a>
        <a href="?type=position&bulan={{$bulan}}&tahun={{$tahun}}"
           class="btn btn-sm {{ $type == 'position' ? 'btn-white shadow-sm fw-bold text-primary' : 'text-muted' }}">
            Posisi Keuangan
        </a>
        <a href="?type=summary&bulan={{$bulan}}&tahun={{$tahun}}"
           class="btn btn-sm {{ $type == 'summary' ? 'btn-white shadow-sm fw-bold text-primary' : 'text-muted' }}">
            Laporan Organisasi
        </a>
    </div>
</div>

<!-- OPTION 2: TAMPILAN MOBILE (Muncul di HP) -->
<div class="d-block d-md-none mb-3">
    <div class="dropdown">
        <button class="btn btn-light w-100 border text-start d-flex justify-content-between align-items-center py-2 px-3 rounded-3 shadow-sm" 
                type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <span class="fw-bold text-dark">
                <i class="fa-solid fa-file-invoice me-2 text-primary"></i> {{ $currentLabel }}
            </span>
            <i class="fa-solid fa-chevron-down text-muted"></i>
        </button>
        <ul class="dropdown-menu w-100 shadow-sm border-0 mt-1 rounded-3">
            <li>
                <a class="dropdown-item py-2 {{ $type == 'flow' ? 'active fw-bold' : '' }}" 
                   href="?type=flow&bulan={{$bulan}}&tahun={{$tahun}}">
                   Dana Masuk/Keluar
                </a>
            </li>
            <li>
                <a class="dropdown-item py-2 {{ $type == 'position' ? 'active fw-bold' : '' }}" 
                   href="?type=position&bulan={{$bulan}}&tahun={{$tahun}}">
                   Posisi Keuangan
                </a>
            </li>
            <li>
                <a class="dropdown-item py-2 {{ $type == 'summary' ? 'active fw-bold' : '' }}" 
                   href="?type=summary&bulan={{$bulan}}&tahun={{$tahun}}">
                   Laporan Organisasi
                </a>
            </li>
        </ul>
    </div>
</div>
<!-- Navigasi Bulan -->
<div class="d-flex align-items-center gap-3 bg-white border rounded-pill px-3 py-1">
<a href="?bulan={{ $bulan == 1 ? 12 : $bulan-1 }}&tahun={{ $bulan == 1 ? $tahun-1 : $tahun }}&type={{$type}}" class="text-indigo"><i class="fa-solid fa-chevron-left"></i></a>
<span class="fw-bold text-uppercase small" style="min-width: 120px; text-align: center;">
{{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }} {{ $tahun }}
</span>
<a href="?bulan={{ $bulan == 12 ? 1 : $bulan+1 }}&tahun={{ $bulan == 12 ? $tahun+1 : $tahun }}&type={{$type}}" class="text-indigo"><i class="fa-solid fa-chevron-right"></i></a>
</div>
<!-- Tombol Aksi -->
<div class="d-flex gap-2">
<button type="button" class="btn btn-primary btn-sm rounded-3 fw-bold text-white px-3" data-bs-toggle="modal" data-bs-target="#modalEditData">
<i class="fa-solid fa-pen-to-square me-1"></i> Edit
</button>
<button type="button" class="btn btn-outline-primary btn-sm rounded-3 fw-bold px-3" data-bs-toggle="modal" data-bs-target="#modalTtd">
<i class="fas fa-signature me-1"></i> Atur TTD
</button>
<a href="{{ route('laporan_keuangan.pdf', ['bulan' => $bulan, 'tahun' => $tahun, 'type' => $type]) }}" target="_blank" class="btn btn-danger btn-sm rounded-3 fw-bold text-white px-3">
<i class="fa-solid fa-file-pdf me-1"></i> PDF
</a>
</div>
</div>
</div>
<!-- AREA LAPORAN (READ ONLY / TAMPILAN SAJA) -->
<div class="card border-0 shadow-sm p-4 p-md-5">
<div class="text-center mb-4">
<h4 class="fw-bold text-uppercase text-decoration-underline mb-1">
@if($type == 'flow') LAPORAN DANA MASUK DAN KELUAR
@elseif($type == 'position') LAPORAN POSISI KEUANGAN
@else LAPORAN KEUANGAN ORGANISASI @endif
</h4>
<p class="fw-bold text-uppercase text-muted small">BULAN {{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }} {{ $tahun }}</p>
</div>
<div class="table-responsive">
<table class="table table-bordered align-middle small table-striped-columns" style="min-width: 700px;">
@if($type == 'flow')
@include('pages.laporan_keuangan.partials.flow', ['readOnly' => true])
@elseif($type == 'position')
@include('pages.laporan_keuangan.partials.position', ['readOnly' => true])
@else
@include('pages.laporan_keuangan.partials.summary', ['readOnly' => true])
@endif
</table>
</div>
<!-- Layout Tanda Tangan -->
<div class="row justify-content-center mt-5 pt-4 text-center">
<div class="col-md-4 align-self-end">
<p class="mb-5 fw-bold">SEKRETARIS</p>
<div class="d-flex justify-content-center" style="height: 60px;">
@if(!empty($ttd->path_ttd_sekretaris))
<img src="{{ asset('storage/'.$ttd->path_ttd_sekretaris) }}" style="max-height: 60px;">
@endif
</div>
<p class="fw-bold text-decoration-underline mt-2 mb-0">{{ $ttd->sekretaris_nama ?? '....................' }}</p>
</div>
<div class="col-md-4"></div>
<div class="col-md-4 align-self-end">
<p class="mb-1">{{ $ttd->kota_surat ?? 'Karawang' }}, {{ date('d F Y') }}</p>
<p class="mb-5 fw-bold">BENDAHARA</p>
<div class="d-flex justify-content-center" style="height: 60px;">
@if(!empty($ttd->path_ttd_bendahara))
<img src="{{ asset('storage/'.$ttd->path_ttd_bendahara) }}" style="max-height: 60px;">
@endif
</div>
<p class="fw-bold text-decoration-underline mt-2 mb-0">{{ $ttd->bendahara_nama ?? '....................' }}</p>
</div>
</div>
<div class="row justify-content-center text-center mt-5">
<div class="col-md-4">
<p class="mb-5 fw-bold">Mengetahui,<br>KETUA</p>
<div class="d-flex justify-content-center" style="height: 60px;">
@if(!empty($ttd->path_ttd_ketua))
<img src="{{ asset('storage/'.$ttd->path_ttd_ketua) }}" style="max-height: 60px;">
@endif
</div>
<p class="fw-bold text-decoration-underline mt-2 mb-0">{{ $ttd->ketua_nama ?? '....................' }}</p>
</div>
</div>
</div>
</div>
<!-- ================= MODAL EDIT DATA (POP-UP) ================= -->
<div class="modal fade" id="modalEditData" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
<div class="modal-dialog modal-xl">
<div class="modal-content border-0 shadow-lg rounded-4">
<form id="formEditKeuangan" action="{{ route('laporan_keuangan.store') }}" method="POST">
@csrf
<input type="hidden" name="bulan" value="{{$bulan}}">
<input type="hidden" name="tahun" value="{{$tahun}}">
<div class="modal-header bg-primary text-white">
<h5 class="modal-title fw-bold"><i class="fa-solid fa-pen-to-square me-2"></i> Edit Data Keuangan</h5>
<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body bg-light" style="max-height: 70vh; overflow-y: auto;">
<div class="table-responsive">
<table class="table table-bordered align-middle small table-striped-columns" style="min-width: 800px;">
@if($type == 'flow')
@include('pages.laporan_keuangan.partials.flow', ['readOnly' => false])
@elseif($type == 'position')
@include('pages.laporan_keuangan.partials.position', ['readOnly' => false])
@else
@include('pages.laporan_keuangan.partials.summary', ['readOnly' => false])
@endif
</table>
</div>
</div>
<div class="modal-footer d-flex justify-content-end gap-2 border-top">
<button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Batal</button>
<button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Simpan Perubahan</button>
</div>
</form>
</div>
</div>
</div>
<!-- ================= MODAL ATUR TTD ================= -->
<div class="modal fade" id="modalTtd" tabindex="-1" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content border-0 shadow-lg rounded-4">
<div class="modal-header border-bottom-0 pb-0">
<h5 class="modal-title fw-bold">Atur Tanda Tangan</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
<form action="{{ route('laporan_keuangan.update_ttd') }}" method="POST" enctype="multipart/form-data">
@csrf
<h6 class="text-primary fw-bold mb-3 mt-2">Pengurus DPC</h6>
<div class="mb-3">
<label class="form-label small fw-bold text-muted">Nama Ketua</label>
<input type="text" name="ketua_nama" class="form-control" value="{{ $ttd->ketua_nama ?? '' }}">
<div class="mt-2">
<label class="form-label small text-muted">Upload TTD Ketua (Opsional)</label>
<input type="file" name="ttd_ketua" class="form-control form-control-sm" accept="image/png, image/jpeg">
</div>
</div>
<div class="mb-3">
<label class="form-label small fw-bold text-muted">Nama Sekretaris</label>
<input type="text" name="sekretaris_nama" class="form-control" value="{{ $ttd->sekretaris_nama ?? '' }}">
<div class="mt-2">
<label class="form-label small text-muted">Upload TTD Sekretaris (Opsional)</label>
<input type="file" name="ttd_sekretaris" class="form-control form-control-sm" accept="image/png, image/jpeg">
</div>
</div>
<div class="mb-3">
<label class="form-label small fw-bold text-muted">Nama Bendahara</label>
<input type="text" name="bendahara_nama" class="form-control" value="{{ $ttd->bendahara_nama ?? '' }}">
<div class="mt-2">
<label class="form-label small text-muted">Upload TTD Bendahara (Opsional)</label>
<input type="file" name="ttd_bendahara" class="form-control form-control-sm" accept="image/png, image/jpeg">
</div>
</div>
<hr>
<div class="mb-3">
<label class="form-label small fw-bold text-muted">Kota Surat</label>
<input type="text" name="kota_surat" class="form-control" value="{{ $ttd->kota_surat ?? 'Karawang' }}">
</div>
<button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold">Simpan</button>
</form>
</div>
</div>
</div>
</div>

{{-- ================= JAVASCRIPT KALKULATOR REAL-TIME ================= --}}
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Helper: Format angka ke format Rupiah Indonesia
    const formatRupiah = (num) => {
        if (isNaN(num)) return '0';
        return new Intl.NumberFormat('id-ID').format(Math.round(num));
    };
    
    // Helper: Ambil nilai numerik dari input (aman untuk empty/null)
    const getNumVal = (el) => parseFloat(el?.value || 0);
    
    // Fungsi Kalkulasi Utama
    const calculateTotals = () => {
        // ================= FLOW TAB CALCULATION =================
        // Income COS
        let totalIncCos = 0;
        document.querySelectorAll('input[name^="incomeCos["]').forEach(el => {
            totalIncCos += getNumVal(el);
        });
        
        // Income Non-COS
        let totalIncNon = 0;
        document.querySelectorAll('input[name^="incomeNonCos["]').forEach(el => {
            totalIncNon += getNumVal(el);
        });
        
        // Total Income
        const totalIncome = totalIncCos + totalIncNon;
        
        // Expenses Operasional (Semua item dengan prefix 'operasional' atau 'bidang')
        let totalOps = 0;
        ['operasional', 'bidang1', 'bidang2', 'bidang3', 'bidang4', 'bidang5'].forEach(key => {
            const el = document.querySelector(`input[name="expenses[${key}]"]`);
            if (el) totalOps += getNumVal(el);
        });
        
        // Expenses Event (Semua item dengan prefix 'evt_')
        let totalEvt = 0;
        ['evt_ketua', 'evt_bidang1', 'evt_bidang2', 'evt_bidang3', 'evt_bidang4', 'evt_bidang5'].forEach(key => {
            const el = document.querySelector(`input[name="expenses[${key}]"]`);
            if (el) totalEvt += getNumVal(el);
        });
        
        // Expenses Lainnya
        const expSekretariat = getNumVal(document.querySelector('input[name="expenses[sekretariat]"]'));
        const expInsentif = getNumVal(document.querySelector('input[name="expenses[insentif]"]'));
        
        // Total Expenses
        const totalExpense = totalOps + totalEvt + expSekretariat + expInsentif;
        const surplus = totalIncome - totalExpense;
        
        // Update Display Flow
        if (document.getElementById('sum_inc_cos')) 
            document.getElementById('sum_inc_cos').innerText = formatRupiah(totalIncCos);
        if (document.getElementById('sum_inc_non')) 
            document.getElementById('sum_inc_non').innerText = formatRupiah(totalIncNon);
        if (document.getElementById('total_income_display')) 
            document.getElementById('total_income_display').innerText = formatRupiah(totalIncome);
        if (document.getElementById('sum_ops')) 
            document.getElementById('sum_ops').innerText = formatRupiah(totalOps);
        if (document.getElementById('sum_evt')) 
            document.getElementById('sum_evt').innerText = formatRupiah(totalEvt);
        if (document.getElementById('total_expense_display')) 
            document.getElementById('total_expense_display').innerText = formatRupiah(totalExpense);
        if (document.getElementById('surplus_display')) 
            document.getElementById('surplus_display').innerText = formatRupiah(surplus);
        
        // ================= POSITION TAB CALCULATION =================
        // Total Aset
        let totalAsset = 0;
        document.querySelectorAll('input[name^="assets["]').forEach(el => {
            totalAsset += getNumVal(el);
        });
        if (document.getElementById('total_asset_display')) 
            document.getElementById('total_asset_display').innerText = formatRupiah(totalAsset);
        
        // Pengeluaran Manual di Tab Posisi
        let posExp = 0;
        ['pos_ops', 'pos_evt', 'pos_sekretariat', 'pos_insentif'].forEach(key => {
            const el = document.querySelector(`input[name="liabilities[${key}]"]`);
            if (el) posExp += getNumVal(el);
        });
        
        // Modal (Saldo Awal + Pemasukan)
        let posModal = 0;
        ['pos_saldo_awal', 'pos_inc_cos', 'pos_inc_non_cos'].forEach(key => {
            const el = document.querySelector(`input[name="liabilities[${key}]"]`);
            if (el) posModal += getNumVal(el);
        });
        
        const saldoModal = posModal - posExp;
        
        // Update Display Position
        if (document.getElementById('sum_pos_exp')) 
            document.getElementById('sum_pos_exp').innerText = formatRupiah(posExp);
        if (document.getElementById('sum_pos_modal')) 
            document.getElementById('sum_pos_modal').innerText = formatRupiah(posModal);
        if (document.getElementById('total_liability_display')) 
            document.getElementById('total_liability_display').innerText = formatRupiah(saldoModal);
        
        // Update warna Saldo Modal berdasarkan nilai
        const saldoEl = document.getElementById('total_liability_display');
        if (saldoEl) {
            saldoEl.className = saldoModal >= 0 
                ? 'fw-bold text-white' 
                : 'fw-bold text-warning';
        }
    };
    
    // Pasang Event Listener ke SEMUA input uang di modal edit
    document.querySelectorAll('.input-money').forEach(input => {
        input.addEventListener('input', calculateTotals);
        input.addEventListener('change', calculateTotals); // Handle paste
    });
    
    // Trigger kalkulasi awal setelah modal dimuat
    const editModal = document.getElementById('modalEditData');
    if (editModal) {
        editModal.addEventListener('shown.bs.modal', function () {
            // Tunggu sedikit agar DOM sepenuhnya termuat
            setTimeout(calculateTotals, 300);
        });
    }
    
    // Initial calculation (untuk kasus modal sudah terbuka saat load)
    setTimeout(calculateTotals, 500);
});
</script>
@endsection