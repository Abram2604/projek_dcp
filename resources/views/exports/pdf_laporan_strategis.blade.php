<!DOCTYPE html>
<html>
<head>
    <title>Laporan Keuangan</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #000; padding: 4px; vertical-align: middle; }
        th { background-color: #eaeaea; text-align: center; font-weight: bold; text-transform: uppercase; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        .section { background-color: #f5f5f5; font-weight: bold; }
        .header { text-align: center; margin-bottom: 20px; }
        
        /* Styling Tanda Tangan */
        .ttd-table { border: none !important; margin-top: 30px; width: 100%; }
        .ttd-table td { border: none !important; text-align: center; vertical-align: bottom; }
        .ttd-img { max-height: 60px; max-width: 100px; object-fit: contain; display: block; margin: 0 auto; }
        .ttd-space { height: 60px; }
        
        /* Helper Warna */
        .bg-success-light { background-color: #d1fae5; }
        .bg-danger-light { background-color: #fee2e2; }
        .bg-yellow-light { background-color: #fef3c7; }
    </style>
</head>
<body>

{{-- PRE-CALCULATION (Agar data konsisten) --}}
@php
    // Helper Format Rupiah
    function rupiah($angka) {
        return number_format((float)$angka, 0, ',', '.');
    }

    // Ambil Data Array
    $incCos = $data['incomeCos'] ?? [];
    $incNon = $data['incomeNonCos'] ?? [];
    $exp    = $data['expenses'] ?? [];
    $ast    = $data['assets'] ?? [];
    $saldoAwal = (float)($data['saldoAwal'] ?? 0);

    // Hitung Total
    $totalCos = ($incCos['kiic']??0) + ($incCos['kim']??0) + ($incCos['kisc']??0) + ($incCos['luar']??0);
    $totalNon = ($incNon['adminBank']??0) + ($incNon['donasi']??0);
    $totalIncome = $totalCos + $totalNon;

    $totalExpense = ($exp['operasional']??0) + ($exp['bidang1']??0) + ($exp['bidang2']??0) + 
                    ($exp['bidang3']??0) + ($exp['bidang4']??0) + ($exp['bidang5']??0) + 
                    ($exp['sekretariat']??0) + ($exp['insentif']??0);

    $totalAsset = ($ast['bni']??0) + ($ast['kas']??0) + ($ast['advSekretariat']??0) + 
                  ($ast['advBph']??0) + ($ast['advLain']??0);

    $surplus = $totalIncome - $totalExpense;
    $saldoAkhir = $saldoAwal + $surplus;
@endphp

{{-- HEADER JUDUL --}}
<div class="header">
    <h3 style="margin:0; text-transform:uppercase; text-decoration: underline;">
        @if($type == 'flow') LAPORAN DANA MASUK DAN KELUAR 
        @elseif($type == 'position') LAPORAN POSISI KEUANGAN 
        @else LAPORAN KEUANGAN ORGANISASI @endif
    </h3>
    <p style="margin:5px 0; font-weight: bold;">BULAN {{ strtoupper($namaBulan) }} {{ $tahun }}</p>
</div>

<table>
    
    {{-- ================= TIPE 1: DANA MASUK / KELUAR (FLOW) ================= --}}
    @if($type == 'flow')
    <thead>
        <tr>
            <th width="5%">No</th>
            <th>Keterangan</th>
            <th width="25%">Jumlah (Rp)</th>
        </tr>
    </thead>
    <tbody>
        <!-- PEMASUKAN COS -->
        <tr class="section"><td colspan="3">PEMASUKAN COS</td></tr>
        <tr><td class="text-center">1</td><td>Zona KIIC</td><td class="text-right">{{ rupiah($incCos['kiic']??0) }}</td></tr>
        <tr><td class="text-center">2</td><td>Zona KIM</td><td class="text-right">{{ rupiah($incCos['kim']??0) }}</td></tr>
        <tr><td class="text-center">3</td><td>Zona KISC</td><td class="text-right">{{ rupiah($incCos['kisc']??0) }}</td></tr>
        <tr><td class="text-center">4</td><td>Zona Luar</td><td class="text-right">{{ rupiah($incCos['luar']??0) }}</td></tr>
        
        <!-- PEMASUKAN NON COS -->
        <tr class="section"><td colspan="3">PEMASUKAN NON COS</td></tr>
        <tr><td class="text-center">1</td><td>Administrasi Bank</td><td class="text-right">{{ rupiah($incNon['adminBank']??0) }}</td></tr>
        <tr><td class="text-center">2</td><td>Dana Konsolidasi / Donasi</td><td class="text-right">{{ rupiah($incNon['donasi']??0) }}</td></tr>

        <!-- TOTAL PENDAPATAN -->
        <tr class="fw-bold bg-success-light">
            <td colspan="2" class="text-right">TOTAL PENDAPATAN</td>
            <td class="text-right">{{ rupiah($totalIncome) }}</td>
        </tr>

        <tr><td colspan="3" style="border:none; height:10px;"></td></tr>

        <!-- PENGELUARAN -->
        <tr class="section"><td colspan="3">PENGELUARAN ORGANISASI</td></tr>
        <tr><td class="text-center">1</td><td>Beban Operasional BPH</td><td class="text-right">{{ rupiah($exp['operasional']??0) }}</td></tr>
        <tr><td class="text-center">2</td><td>Bidang I (Organisasi)</td><td class="text-right">{{ rupiah($exp['bidang1']??0) }}</td></tr>
        <tr><td class="text-center">3</td><td>Bidang II (Advokasi)</td><td class="text-right">{{ rupiah($exp['bidang2']??0) }}</td></tr>
        <tr><td class="text-center">4</td><td>Bidang III (PSDM)</td><td class="text-right">{{ rupiah($exp['bidang3']??0) }}</td></tr>
        <tr><td class="text-center">5</td><td>Bidang IV (Kesejahteraan)</td><td class="text-right">{{ rupiah($exp['bidang4']??0) }}</td></tr>
        <tr><td class="text-center">6</td><td>Bidang V (Publikasi)</td><td class="text-right">{{ rupiah($exp['bidang5']??0) }}</td></tr>
        <tr><td class="text-center">7</td><td>Kesekretariatan</td><td class="text-right">{{ rupiah($exp['sekretariat']??0) }}</td></tr>
        <tr><td class="text-center">8</td><td>Setoran Perangkat & Insentif</td><td class="text-right">{{ rupiah($exp['insentif']??0) }}</td></tr>

        <!-- TOTAL PENGELUARAN -->
        <tr class="fw-bold bg-danger-light">
            <td colspan="2" class="text-right">TOTAL PENGELUARAN</td>
            <td class="text-right">{{ rupiah($totalExpense) }}</td>
        </tr>

        <tr><td colspan="3" style="border:none; height:10px;"></td></tr>

        <!-- HASIL -->
        <tr class="fw-bold bg-yellow-light">
            <td colspan="2" class="text-right">SURPLUS / MINUS</td>
            <td class="text-right">{{ rupiah($surplus) }}</td>
        </tr>
        <tr class="fw-bold">
            <td colspan="2" class="text-right">SALDO AKHIR</td>
            <td class="text-right">{{ rupiah($saldoAkhir) }}</td>
        </tr>
    </tbody>

    {{-- ================= TIPE 2: POSISI KEUANGAN ================= --}}
    @elseif($type == 'position')
    <thead>
        <tr>
            <th colspan="2" style="text-align: left; padding-left: 10px;">ASET</th>
        </tr>
    </thead>
    <tbody>
        <tr class="section"><td colspan="2" style="text-decoration: underline;">DANA ( UANG )</td></tr>
        <tr>
            <td width="70%">Rekening BNI an DPC FSP LEM SPSI Kab Karawang</td>
            <td width="30%" class="text-right">{{ rupiah($ast['bni']??0) }}</td>
        </tr>
        <tr><td>Kas</td><td class="text-right">{{ rupiah($ast['kas']??0) }}</td></tr>
        <tr><td>Advance Kesekretariatan</td><td class="text-right">{{ rupiah($ast['advSekretariat']??0) }}</td></tr>
        <tr><td>Advance Operasional BPH & Bidang</td><td class="text-right">{{ rupiah($ast['advBph']??0) }}</td></tr>
        <tr><td>Advance Proposal / Lainnya</td><td class="text-right">{{ rupiah($ast['advLain']??0) }}</td></tr>
        
        <tr class="fw-bold bg-success-light">
            <td class="text-center">BALANCE (TOTAL ASET)</td>
            <td class="text-right">{{ rupiah($totalAsset) }}</td>
        </tr>
    </tbody>

    {{-- SPACER ANTAR TABEL --}}
    </table>
    <table>

    <thead>
        <tr>
            <th colspan="2" style="text-align: left; padding-left: 10px;">PENGELUARAN DAN MODAL</th>
        </tr>
    </thead>
    <tbody>
        <tr class="section"><td colspan="2" style="text-decoration: underline;">PENGELUARAN</td></tr>
        <tr>
            <td width="70%">Jumlah Pengeluaran</td>
            <td width="30%" class="text-right">{{ rupiah($totalExpense) }}</td>
        </tr>

        <tr class="section"><td colspan="2" style="text-decoration: underline;">MODAL</td></tr>
        <tr><td>Simpanan / Saldo Awal</td><td class="text-right">{{ rupiah($saldoAwal) }}</td></tr>
        <tr><td>Pemasukan (COS & Non COS)</td><td class="text-right">{{ rupiah($totalIncome) }}</td></tr>

        {{-- Perhitungan Saldo Modal (Pasiva) --}}
        @php $totalPasiva = ($saldoAwal + $totalIncome) - $totalExpense; @endphp
        
        <tr class="fw-bold bg-success-light">
            <td class="text-center">SALDO MODAL</td>
            <td class="text-right">{{ rupiah($totalPasiva) }}</td>
        </tr>
    </tbody>

    {{-- ================= TIPE 3: RINGKASAN ORGANISASI ================= --}}
    @else
    <thead>
        <tr>
            <th width="5%">No</th>
            <th>Keterangan</th>
            <th width="30%">Jumlah (Rp)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="text-center fw-bold">1</td>
            <td class="fw-bold">SALDO AWAL</td>
            <td class="text-right">{{ rupiah($saldoAwal) }}</td>
        </tr>
        <tr>
            <td class="text-center fw-bold">2</td>
            <td class="fw-bold">PEMASUKAN</td>
            <td class="text-right">{{ rupiah($totalIncome) }}</td>
        </tr>
        <tr>
            <td class="text-center fw-bold">3</td>
            <td class="fw-bold">PENGELUARAN</td>
            <td class="text-right">{{ rupiah($totalExpense) }}</td>
        </tr>
        <tr class="fw-bold bg-success-light">
            <td class="text-center">4</td>
            <td>SALDO AKHIR</td>
            <td class="text-right">{{ rupiah($saldoAkhir) }}</td>
        </tr>
    </tbody>
    @endif
</table>

{{-- ================= TANDA TANGAN ================= --}}
<table class="ttd-table">
    <tr>
        <td width="33%">
            Mengetahui,<br><b>KETUA</b>
            <div style="height: 60px; display: flex; align-items: flex-end; justify-content: center;">
                @if(!empty($ttd->path_ttd_ketua) && file_exists(public_path('storage/'.$ttd->path_ttd_ketua)))
                    <img src="{{ public_path('storage/'.$ttd->path_ttd_ketua) }}" class="ttd-img">
                @else
                    <div class="ttd-space"></div>
                @endif
            </div>
            <u>{{ $ttd->ketua_nama ?? '.....................' }}</u>
        </td>
        <td width="33%">
            <br><b>SEKRETARIS</b>
            <div style="height: 60px; display: flex; align-items: flex-end; justify-content: center;">
                @if(!empty($ttd->path_ttd_sekretaris) && file_exists(public_path('storage/'.$ttd->path_ttd_sekretaris)))
                    <img src="{{ public_path('storage/'.$ttd->path_ttd_sekretaris) }}" class="ttd-img">
                @else
                    <div class="ttd-space"></div>
                @endif
            </div>
            <u>{{ $ttd->sekretaris_nama ?? '.....................' }}</u>
        </td>
        <td width="33%">
            {{ $ttd->kota_surat ?? 'Karawang' }}, {{ date('d F Y') }}<br><b>BENDAHARA</b>
            <div style="height: 60px; display: flex; align-items: flex-end; justify-content: center;">
                @if(!empty($ttd->path_ttd_bendahara) && file_exists(public_path('storage/'.$ttd->path_ttd_bendahara)))
                    <img src="{{ public_path('storage/'.$ttd->path_ttd_bendahara) }}" class="ttd-img">
                @else
                    <div class="ttd-space"></div>
                @endif
            </div>
            <u>{{ $ttd->bendahara_nama ?? '.....................' }}</u>
        </td>
    </tr>
</table>

</body>
</html>