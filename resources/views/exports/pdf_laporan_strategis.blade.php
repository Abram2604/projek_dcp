```blade
@php
    use Carbon\Carbon;
    $namaBulan = Carbon::create()->month($bulan)->translatedFormat('F');
    // Helper Format Rupiah "Rp. 10.000"
    $rp = function($n){ return 'Rp. ' . number_format((float)$n, 0, ',', '.'); };
    $rpNoPrefix = function($n){ return number_format((float)$n, 0, ',', '.'); };

    // Data Flow
    $incCos = $data['incomeCos'] ?? [];
    $incNon = $data['incomeNonCos'] ?? [];
    $exp    = $data['expenses'] ?? [];
    $ast    = $data['assets'] ?? [];
    $liab   = $data['liabilities'] ?? [];
    $vols   = $data['volumes'] ?? [];
    
    // Data Summary
    $saldoAwal = $data['saldoAwal'] ?? 0;
    $pemasukanCos = $data['pemasukanCos'] ?? 0;
    $pemasukanNonCos = $data['pemasukanNonCos'] ?? 0;
    $pengeluaranOps = $data['pengeluaranOps'] ?? 0;
    $pengeluaranEvent = $data['pengeluaranEvent'] ?? 0;
    $pengeluaranSekretariat = $data['pengeluaranSekretariat'] ?? 0;
    $pengeluaranInsentif = $data['pengeluaranInsentif'] ?? 0;
    $totalPemasukan = $data['totalPemasukan'] ?? 0;
    $totalPengeluaran = $data['totalPengeluaran'] ?? 0;
    $saldoAkhir = $data['saldoAkhir'] ?? 0;

    // TOTALS FLOW
    $tIncCos = array_sum($incCos);
    $tIncNon = array_sum($incNon);
    $tInc    = $tIncCos + $tIncNon;
    
    // Subtotal Expenses
    $tOps = ($exp['ops_ketua']??0) + ($exp['ops_bidang1']??0) + ($exp['ops_bidang2']??0) + ($exp['ops_bidang3']??0) + ($exp['ops_bidang4']??0) + ($exp['ops_bidang5']??0);
    $tEvt = ($exp['evt_ketua']??0) + ($exp['evt_bidang1']??0) + ($exp['evt_bidang2']??0) + ($exp['evt_bidang3']??0) + ($exp['evt_bidang4']??0) + ($exp['evt_bidang5']??0);
    $tExp = $tOps + $tEvt + ($exp['sekretariat']??0) + ($exp['insentif']??0);
    $surplus = $tInc - $tExp;

    // TOTALS POSITION (MANUAL INPUTS)
    $tAsset = ($ast['bni']??0)+($ast['kas']??0)+($ast['advSekretariat']??0)+($ast['advBph']??0)+($ast['advProposal']??0);
    $tPosExp = ($ast['pos_ops']??0)+($ast['pos_evt']??0)+($ast['pos_sekretariat']??0)+($ast['pos_insentif']??0);
    $tPosModal = ($ast['pos_saldo_awal']??0)+($ast['pos_inc_cos']??0)+($ast['pos_inc_non_cos']??0);
    $tBalance = $tPosModal - $tPosExp;
@endphp

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Keuangan Strategis - {{ strtoupper($namaBulan) }} {{ $tahun }}</title>
    <style>
        /* GLOBAL STYLES */
        body { 
            font-family: 'Arial', 'Helvetica', sans-serif; 
            font-size: 10px; 
            margin: 0; 
            padding: 20px;
            line-height: 1.4;
        }
        
        .header { 
            text-align: center; 
            margin-bottom: 5px; 
        }
        
        .title { 
            font-weight: bold; 
            font-size: 14px; 
            margin-bottom: 3px; 
            text-transform: uppercase; 
            text-decoration: underline;
            letter-spacing: 1px;
        }
        
        .subtitle { 
            font-weight: bold; 
            font-size: 11px;
            margin-bottom: 15px; 
        }
        
        .periode {
            font-size: 9px;
            color: #666;
            margin-bottom: 10px;
        }
        
        /* TABLE STYLES */
        .tbl { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 15px; 
            font-size: 10px;
        }
        
        .tbl th, .tbl td { 
            padding: 5px 8px; 
            vertical-align: top;
            border: 1px solid #000;
        }
        
        .tbl th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        
        .tbl.no-border td {
            border: none;
        }
        
        /* TEXT ALIGNMENT */
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        
        /* FONT WEIGHT */
        .fw-bold { font-weight: bold; }
        .fw-normal { font-weight: normal; }
        
        /* INDENTATION */
        .indent { padding-left: 20px; }
        .indent-sm { padding-left: 10px; }
        
        /* BACKGROUNDS */
        .bg-light { background-color: #f8f9fa; }
        .bg-success { background-color: #d4edda; }
        .bg-warning { background-color: #fff3cd; }
        .bg-danger { background-color: #f8d7da; }
        .bg-info { background-color: #d1ecf1; }
        .bg-yellow { background-color: #ffeb3b; }
        .bg-gray { background-color: #e9ecef; }
        
        /* BORDERS */
        .border-top { border-top: 2px solid #000 !important; }
        .border-bottom { border-bottom: 2px solid #000 !important; }
        .border-top-dashed { border-top: 1px dashed #999; }
        
        /* TTD STYLES */
        .ttd-section { 
            margin-top: 40px; 
            width: 100%; 
        }
        
        .ttd-row { 
            display: table; 
            width: 100%; 
            table-layout: fixed; 
        }
        
        .ttd-col { 
            display: table-cell; 
            vertical-align: top; 
            text-align: center; 
            padding: 0 10px;
        }
        
        .ttd-col-left { width: 40%; }
        .ttd-col-center { width: 20%; }
        .ttd-col-right { width: 40%; }
        
        .ttd-title { 
            font-weight: bold; 
            margin-bottom: 5px; 
            font-size: 10px;
        }
        
        .ttd-name { 
            text-decoration: underline; 
            font-weight: bold; 
            margin-top: 60px;
            font-size: 10px;
        }
        
        .ttd-position {
            margin-top: 5px;
            font-size: 9px;
            font-style: italic;
        }
        
        .ttd-img { 
            max-height: 50px; 
            max-width: 150px;
            display: block; 
            margin: 0 auto 5px auto; 
        }
        
        /* PAGE BREAK */
        .page-break {
            page-break-before: always;
        }
        
        /* FOOTER */
        .footer {
            margin-top: 30px;
            font-size: 8px;
            color: #666;
            text-align: center;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        
        /* VOLUME TABLE */
        .volume-tbl {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 9px;
        }
        
        .volume-tbl th,
        .volume-tbl td {
            border: 1px solid #000;
            padding: 4px 6px;
        }
        
        .volume-tbl th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
    </style>
</head>
<body>

@if($type == 'flow')
    <!-- ================= FLOW REPORT ================= -->
    <div class="header">
        <div class="title">LAPORAN DANA MASUK DAN KELUAR</div>
        <div class="subtitle">DPC FSP LEM SPSI KABUPATEN KARAWANG</div>
        <div class="periode">BULAN {{ strtoupper($namaBulan) }} {{ $tahun }}</div>
    </div>

    <table class="tbl">
        <thead>
            <tr class="bg-light">
                <th width="70%" class="text-left">URAIAN</th>
                <th width="30%" class="text-right">JUMLAH (Rp.)</th>
            </tr>
        </thead>
        <tbody>
            <!-- PEMASUKAN COS -->
            <tr class="fw-bold bg-gray">
                <td colspan="2" class="text-left">A. PEMASUKAN COS</td>
            </tr>
            <tr>
                <td>COS PUK</td>
                <td class="text-right"></td>
            </tr>
            @foreach(['Zona KIIC'=>'kiic','Zona KIM'=>'kim','Zona KISC'=>'kisc','Zona Luar'=>'luar'] as $l=>$k)
            <tr>
                <td class="indent">{{ $l }}</td>
                <td class="text-right">{{ $rpNoPrefix($incCos[$k]??0) }}</td>
            </tr>
            @endforeach
            <tr class="fw-bold border-top">
                <td class="indent">Jumlah Pemasukan COS</td>
                <td class="text-right">{{ $rpNoPrefix($tIncCos) }}</td>
            </tr>
            
            <tr><td colspan="2" class="border-top-dashed">&nbsp;</td></tr>

            <!-- PEMASUKAN NON COS -->
            <tr class="fw-bold bg-gray">
                <td colspan="2" class="text-left">B. PEMASUKAN NON COS</td>
            </tr>
            @foreach(['Administrasi Bank'=>'adminBank','Dana Konsolidasi / Donasi'=>'donasi'] as $l=>$k)
            <tr>
                <td class="indent">{{ $l }}</td>
                <td class="text-right">{{ $rpNoPrefix($incNon[$k]??0) }}</td>
            </tr>
            @endforeach
            <tr class="fw-bold border-top">
                <td class="indent">Jumlah Pemasukan Non COS</td>
                <td class="text-right">{{ $rpNoPrefix($tIncNon) }}</td>
            </tr>

            <tr class="fw-bold border-top bg-yellow">
                <td>TOTAL PENDAPATAN ORGANISASI</td>
                <td class="text-right">{{ $rpNoPrefix($tInc) }}</td>
            </tr>

            <tr><td colspan="2" class="border-top-dashed">&nbsp;</td></tr>

            <!-- PENGELUARAN -->
            <tr class="fw-bold bg-gray">
                <td colspan="2" class="text-left">C. PENGELUARAN (BEBAN) ORGANISASI</td>
            </tr>
            
            <!-- OPS -->
            <tr class="fw-bold">
                <td>1. Operasional Organisasi</td>
                <td></td>
            </tr>
            @foreach([
                'Ketua, Sekretaris, Bendahara' => 'ops_ketua',
                'Bidang I Organisasi' => 'ops_bidang1',
                'Bidang II Advokasi' => 'ops_bidang2',
                'Bidang III Pengembangan SDM' => 'ops_bidang3',
                'Bidang IV Kesejahteraan' => 'ops_bidang4',
                'Bidang V Publikasi & Hubungan Antar Lembaga' => 'ops_bidang5'
            ] as $l=>$k)
            <tr>
                <td class="indent">{{ $l }}</td>
                <td class="text-right">{{ $rpNoPrefix($exp[$k]??0) }}</td>
            </tr>
            @endforeach
            <tr class="fw-bold border-top">
                <td class="indent">Jumlah Operasional</td>
                <td class="text-right">{{ $rpNoPrefix($tOps) }}</td>
            </tr>

            <!-- EVENT -->
            <tr class="fw-bold">
                <td>2. Event Organisasi</td>
                <td></td>
            </tr>
            @foreach([
                'Ketua, Sekretaris, Bendahara' => 'evt_ketua',
                'Bidang I Organisasi' => 'evt_bidang1',
                'Bidang II Advokasi' => 'evt_bidang2',
                'Bidang III Pengembangan SDM' => 'evt_bidang3',
                'Bidang IV Kesejahteraan' => 'evt_bidang4',
                'Bidang V Publikasi & Hubungan Antar Lembaga' => 'evt_bidang5'
            ] as $l=>$k)
            <tr>
                <td class="indent">{{ $l }}</td>
                <td class="text-right">{{ $rpNoPrefix($exp[$k]??0) }}</td>
            </tr>
            @endforeach
            <tr class="fw-bold border-top">
                <td class="indent">Jumlah Event</td>
                <td class="text-right">{{ $rpNoPrefix($tEvt) }}</td>
            </tr>

            <tr>
                <td>3. Kesekretariatan</td>
                <td class="text-right">{{ $rpNoPrefix($exp['sekretariat']??0) }}</td>
            </tr>
            <tr>
                <td>4. Setoran Perangkat & Insentif Pengurus</td>
                <td class="text-right">{{ $rpNoPrefix($exp['insentif']??0) }}</td>
            </tr>

            <tr class="fw-bold border-top bg-yellow">
                <td>TOTAL PENGELUARAN ORGANISASI</td>
                <td class="text-right">{{ $rpNoPrefix($tExp) }}</td>
            </tr>

            <tr><td colspan="2" class="border-top-dashed">&nbsp;</td></tr>

            <tr class="fw-bold border-top border-bottom bg-yellow">
                <td>SURPLUS / (DEFISIT)</td>
                <td class="text-right">{{ $rpNoPrefix($surplus) }}</td>
            </tr>
        </tbody>
    </table>

@elseif($type == 'position')
    <!-- ================= POSITION REPORT ================= -->
    <div class="header">
        <div class="title">LAPORAN POSISI KEUANGAN</div>
        <div class="subtitle">DPC FSP LEM SPSI KABUPATEN KARAWANG</div>
        <div class="periode">BULAN {{ strtoupper($namaBulan) }} {{ $tahun }}</div>
    </div>

    <table class="tbl">
        <thead>
            <tr class="bg-light">
                <th width="70%" class="text-left">URAIAN</th>
                <th width="30%" class="text-right">JUMLAH (Rp.)</th>
            </tr>
        </thead>
        <tbody>
            <!-- ASET -->
            <tr class="fw-bold bg-gray">
                <td colspan="2" class="text-left">ASET</td>
            </tr>
            <tr class="fw-bold">
                <td>Dana (Uang)</td>
                <td></td>
            </tr>
            
            <tr>
                <td class="indent">Rekening BNI a.n. DPC FSP LEM SPSI Kab. Karawang</td>
                <td class="text-right">{{ $rpNoPrefix($ast['bni']??0) }}</td>
            </tr>
            <tr>
                <td class="indent">Kas</td>
                <td class="text-right">{{ $rpNoPrefix($ast['kas']??0) }}</td>
            </tr>
            <tr>
                <td class="indent">Advance Kesekretariatan</td>
                <td class="text-right">{{ $rpNoPrefix($ast['advSekretariat']??0) }}</td>
            </tr>
            <tr>
                <td class="indent">Advance Operasional BPH & Bidang</td>
                <td class="text-right">{{ $rpNoPrefix($ast['advBph']??0) }}</td>
            </tr>
            <tr>
                <td class="indent">Advance Proposal</td>
                <td class="text-right">{{ $rpNoPrefix($ast['advProposal']??0) }}</td>
            </tr>

            <tr class="fw-bold border-top border-bottom bg-success">
                <td>BALANCE (TOTAL ASET)</td>
                <td class="text-right">{{ $rpNoPrefix($tAsset) }}</td>
            </tr>

            <tr><td colspan="2" class="border-top-dashed">&nbsp;</td></tr>

            <!-- PENGELUARAN DAN MODAL -->
            <tr class="fw-bold bg-gray">
                <td colspan="2" class="text-left">PENGELUARAN DAN MODAL</td>
            </tr>
            
            <!-- PENGELUARAN -->
            <tr class="fw-bold">
                <td>PENGELUARAN</td>
                <td></td>
            </tr>
            <tr>
                <td class="indent">Operasional Organisasi</td>
                <td class="text-right">{{ $rpNoPrefix($liab['pos_ops']??0) }}</td>
            </tr>
            <tr>
                <td class="indent">Event Organisasi</td>
                <td class="text-right">{{ $rpNoPrefix($liab['pos_evt']??0) }}</td>
            </tr>
            <tr>
                <td class="indent">Kesekretariatan</td>
                <td class="text-right">{{ $rpNoPrefix($liab['pos_sekretariat']??0) }}</td>
            </tr>
            <tr>
                <td class="indent">Setoran Perangkat & Insentif Pengurus</td>
                <td class="text-right">{{ $rpNoPrefix($liab['pos_insentif']??0) }}</td>
            </tr>
            @php
                $tPosExp = ($liab['pos_ops']??0)+($liab['pos_evt']??0)+($liab['pos_sekretariat']??0)+($liab['pos_insentif']??0);
            @endphp
            <tr class="fw-bold border-top">
                <td class="indent">Jumlah Pengeluaran</td>
                <td class="text-right">{{ $rpNoPrefix($tPosExp) }}</td>
            </tr>

            <tr><td colspan="2" class="border-top-dashed">&nbsp;</td></tr>

            <!-- MODAL -->
            <tr class="fw-bold">
                <td>MODAL</td>
                <td></td>
            </tr>
            <tr>
                <td class="indent">Simpanan / Saldo Awal</td>
                <td class="text-right">{{ $rpNoPrefix($liab['pos_saldo_awal']??0) }}</td>
            </tr>
            <tr>
                <td class="indent">Pemasukan COS</td>
                <td class="text-right">{{ $rpNoPrefix($liab['pos_inc_cos']??0) }}</td>
            </tr>
            <tr>
                <td class="indent">Pemasukan Non COS</td>
                <td class="text-right">{{ $rpNoPrefix($liab['pos_inc_non_cos']??0) }}</td>
            </tr>
            @php
                $tPosModal = ($liab['pos_saldo_awal']??0)+($liab['pos_inc_cos']??0)+($liab['pos_inc_non_cos']??0);
                $tBalance = $tPosModal - $tPosExp;
            @endphp
            <tr class="fw-bold border-top">
                <td class="indent">Jumlah Modal</td>
                <td class="text-right">{{ $rpNoPrefix($tPosModal) }}</td>
            </tr>

            <tr class="fw-bold border-top border-bottom bg-success">
                <td>SALDO MODAL (BALANCE)</td>
                <td class="text-right">{{ $rpNoPrefix($tBalance) }}</td>
            </tr>
        </tbody>
    </table>

@elseif($type == 'summary')
    <!-- ================= SUMMARY REPORT ================= -->
    <div class="header">
        <div class="title">LAPORAN KEUANGAN ORGANISASI (REKAP)</div>
        <div class="subtitle">DPC FSP LEM SPSI KABUPATEN KARAWANG</div>
        <div class="periode">BULAN {{ strtoupper($namaBulan) }} {{ $tahun }}</div>
    </div>

    <table class="tbl">
        <thead>
            <tr class="bg-light">
                <th width="5%" class="text-center">No</th>
                <th width="65%" class="text-left">URAIAN</th>
                <th width="30%" class="text-right">JUMLAH (Rp.)</th>
            </tr>
        </thead>
        <tbody>
            <!-- SALDO AWAL -->
            <tr class="fw-bold bg-light">
                <td class="text-center">1</td>
                <td>SALDO AWAL</td>
                <td class="text-right">{{ $rpNoPrefix($saldoAwal) }}</td>
            </tr>

            <!-- PEMASUKAN -->
            <tr class="fw-bold bg-light">
                <td class="text-center">2</td>
                <td colspan="2">PEMASUKAN</td>
            </tr>
            <tr>
                <td></td>
                <td class="indent">Iuran Anggota (COS)</td>
                <td class="text-right">{{ $rpNoPrefix($pemasukanCos) }}</td>
            </tr>
            <tr>
                <td></td>
                <td class="indent">Pemasukan Non COS</td>
                <td class="text-right">{{ $rpNoPrefix($pemasukanNonCos) }}</td>
            </tr>
            <tr class="fw-bold bg-light">
                <td></td>
                <td class="text-right">JUMLAH PEMASUKAN</td>
                <td class="text-right">{{ $rpNoPrefix($totalPemasukan) }}</td>
            </tr>

            <!-- PENGELUARAN -->
            <tr class="fw-bold bg-light">
                <td class="text-center">3</td>
                <td colspan="2">PENGELUARAN</td>
            </tr>
            <tr>
                <td></td>
                <td class="indent">Operasional Organisasi</td>
                <td class="text-right">{{ $rpNoPrefix($pengeluaranOps) }}</td>
            </tr>
            <tr>
                <td></td>
                <td class="indent">Event Organisasi</td>
                <td class="text-right">{{ $rpNoPrefix($pengeluaranEvent) }}</td>
            </tr>
            <tr>
                <td></td>
                <td class="indent">Kesekretariatan</td>
                <td class="text-right">{{ $rpNoPrefix($pengeluaranSekretariat) }}</td>
            </tr>
            <tr>
                <td></td>
                <td class="indent">Setoran Perangkat & Insentif Pengurus</td>
                <td class="text-right">{{ $rpNoPrefix($pengeluaranInsentif) }}</td>
            </tr>
            <tr class="fw-bold bg-light">
                <td></td>
                <td class="text-right">JUMLAH PENGELUARAN</td>
                <td class="text-right">{{ $rpNoPrefix($totalPengeluaran) }}</td>
            </tr>

            <!-- SALDO AKHIR -->
            <tr class="fw-bold bg-success" style="color: #000000;">
                <td class="text-center">4</td>
                <td>SALDO AKHIR (Saldo Awal + Pemasukan - Pengeluaran)</td>
                <td class="text-right">{{ $rpNoPrefix($saldoAkhir) }}</td>
            </tr>
        </tbody>
    </table>
@endif

<!-- ================= TANDA TANGAN ================= -->
<div class="ttd-section">
    <div style="text-align: right; margin-bottom: 5px; font-size: 10px;">
        {{ $ttd->kota_surat }}, {{ Carbon::now()->translatedFormat('d F Y') }}
    </div>
    
    <div class="ttd-row">
        <!-- KIRI: SEKRETARIS -->
        <div class="ttd-col ttd-col-left">
            <div class="ttd-title">SEKRETARIS</div>
            @if(!empty($ttd->path_ttd_sekretaris))
                <img src="{{ public_path('storage/'.$ttd->path_ttd_sekretaris) }}" class="ttd-img">
            @else
                <div style="height: 50px;"></div>
            @endif
            <div class="ttd-name">{{ $ttd->sekretaris_nama }}</div>
        </div>
        
        <!-- TENGAH: GAP -->
        <div class="ttd-col ttd-col-center"></div>

        <!-- KANAN: BENDAHARA -->
        <div class="ttd-col ttd-col-right">
            <div class="ttd-title">BENDAHARA</div>
            @if(!empty($ttd->path_ttd_bendahara))
                <img src="{{ public_path('storage/'.$ttd->path_ttd_bendahara) }}" class="ttd-img">
            @else
                <div style="height: 50px;"></div>
            @endif
            <div class="ttd-name">{{ $ttd->bendahara_nama }}</div>
        </div>
    </div>
    
    <!-- KETUA -->
    <div style="margin-top: 30px; text-align: center;">
        <div style="margin-bottom: 5px; font-weight: bold;">Mengetahui,</div>
        <div class="ttd-title">KETUA</div>
        @if(!empty($ttd->path_ttd_ketua))
            <img src="{{ public_path('storage/'.$ttd->path_ttd_ketua) }}" class="ttd-img">
        @else
            <div style="height: 50px;"></div>
        @endif
        <div class="ttd-name">{{ $ttd->ketua_nama }}</div>
    </div>
</div>

<!-- FOOTER -->
<div class="footer">
    <div>DPC FSP LEM SPSI Kabupaten Karawang - Laporan Keuangan {{ strtoupper($namaBulan) }} {{ $tahun }}</div>
    <div>Dicetak pada: {{ Carbon::now()->translatedFormat('d F Y H:i:s') }}</div>
</div>

</body>
</html>
```