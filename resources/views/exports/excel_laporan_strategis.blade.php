@php
    use Carbon\Carbon;

    // Nama bulan Indonesia
    $namaBulan = Carbon::create()->month($bulan)->translatedFormat('F');

    // Helper angka aman
    $rupiah = function($n){
        return number_format((float)$n, 0, ',', '.');
    };

    // Ambil data
    $incCos = $data['incomeCos'] ?? [];
    $incNon = $data['incomeNonCos'] ?? [];
    $exp    = $data['expenses'] ?? [];
    $ast    = $data['assets'] ?? [];
    $saldoAwal = $data['saldoAwal'] ?? 0;

    // HITUNG TOTAL (biar gak tergantung summary dari controller)
    $totalIncomeCos = array_sum($incCos);
    $totalIncomeNonCos = array_sum($incNon);
    $totalIncome = $totalIncomeCos + $totalIncomeNonCos;
    $totalExpenses = array_sum($exp);
    $surplusDefisit = $totalIncome - $totalExpenses;
    $saldoAkhir = $saldoAwal + $surplusDefisit;
    $totalAssets = array_sum($ast);
@endphp

<table>
    {{-- ================= JUDUL ================= --}}
    <tr>
        <td colspan="3" style="text-align:center; font-weight:bold; font-size:16px;">
            @if($type == 'flow')
                LAPORAN LABA RUGI
            @elseif($type == 'position')
                LAPORAN NERACA
            @else
                LAPORAN ARUS KAS
            @endif
        </td>
    </tr>
    <tr>
        <td colspan="3" style="text-align:center; font-weight:bold;">
            PERIODE {{ strtoupper($namaBulan) }} {{ $tahun }}
        </td>
    </tr>
    <tr></tr>

    {{-- ================= HEADER TABEL ================= --}}
    <tr>
        <th style="text-align:center;">No</th>
        <th>Keterangan</th>
        <th style="text-align:center;">Jumlah (Rp)</th>
    </tr>

    @php $no = 1; @endphp

    {{-- ================= FLOW / LABA RUGI ================= --}}
    @if($type == 'flow')

        <tr><td colspan="3"><b>PENDAPATAN COS</b></td></tr>
        @foreach($incCos as $nama => $nilai)
            <tr>
                <td style="text-align:center;">{{ $no++ }}</td>
                <td>{{ strtoupper($nama) }}</td>
                <td style="text-align:right;">{{ $rupiah($nilai) }}</td>
            </tr>
        @endforeach
        <tr>
            <td></td>
            <td><b>Total Pendapatan COS</b></td>
            <td style="text-align:right;"><b>{{ $rupiah($totalIncomeCos) }}</b></td>
        </tr>

        <tr><td colspan="3"><b>PENDAPATAN NON COS</b></td></tr>
        @foreach($incNon as $nama => $nilai)
            <tr>
                <td style="text-align:center;">{{ $no++ }}</td>
                <td>{{ strtoupper($nama) }}</td>
                <td style="text-align:right;">{{ $rupiah($nilai) }}</td>
            </tr>
        @endforeach
        <tr>
            <td></td>
            <td><b>Total Pendapatan Non COS</b></td>
            <td style="text-align:right;"><b>{{ $rupiah($totalIncomeNonCos) }}</b></td>
        </tr>

        <tr>
            <td></td>
            <td><b>TOTAL PENDAPATAN</b></td>
            <td style="text-align:right;"><b>{{ $rupiah($totalIncome) }}</b></td>
        </tr>

        <tr><td colspan="3"><b>BEBAN / PENGELUARAN</b></td></tr>
        @foreach($exp as $nama => $nilai)
            <tr>
                <td style="text-align:center;">{{ $no++ }}</td>
                <td>{{ strtoupper($nama) }}</td>
                <td style="text-align:right;">{{ $rupiah($nilai) }}</td>
            </tr>
        @endforeach
        <tr>
            <td></td>
            <td><b>Total Beban</b></td>
            <td style="text-align:right;"><b>{{ $rupiah($totalExpenses) }}</b></td>
        </tr>

        <tr>
            <td></td>
            <td><b>SURPLUS / DEFISIT</b></td>
            <td style="text-align:right;"><b>{{ $rupiah($surplusDefisit) }}</b></td>
        </tr>

    {{-- ================= POSITION / NERACA ================= --}}
    @elseif($type == 'position')

        <tr><td colspan="3"><b>ASET</b></td></tr>
        @foreach($ast as $nama => $nilai)
            <tr>
                <td style="text-align:center;">{{ $no++ }}</td>
                <td>{{ strtoupper($nama) }}</td>
                <td style="text-align:right;">{{ $rupiah($nilai) }}</td>
            </tr>
        @endforeach

        <tr>
            <td></td>
            <td><b>TOTAL ASET</b></td>
            <td style="text-align:right;"><b>{{ $rupiah($totalAssets) }}</b></td>
        </tr>

        <tr>
            <td></td>
            <td><b>SALDO AKHIR</b></td>
            <td style="text-align:right;"><b>{{ $rupiah($saldoAkhir) }}</b></td>
        </tr>

    {{-- ================= SUMMARY / ARUS KAS ================= --}}
    @else

        <tr>
            <td style="text-align:center;">1</td>
            <td>Saldo Awal</td>
            <td style="text-align:right;">{{ $rupiah($saldoAwal) }}</td>
        </tr>
        <tr>
            <td style="text-align:center;">2</td>
            <td>Total Pendapatan</td>
            <td style="text-align:right;">{{ $rupiah($totalIncome) }}</td>
        </tr>
        <tr>
            <td style="text-align:center;">3</td>
            <td>Total Beban</td>
            <td style="text-align:right;">{{ $rupiah($totalExpenses) }}</td>
        </tr>
        <tr>
            <td></td>
            <td><b>SALDO AKHIR</b></td>
            <td style="text-align:right;"><b>{{ $rupiah($saldoAkhir) }}</b></td>
        </tr>

    @endif

    <tr></tr>
    <tr></tr>

    {{-- ================= TANDA TANGAN ================= --}}
    <tr>
        <td></td>
        <td style="text-align:center;">Mengetahui,</td>
        <td>{{ $ttd->kota_surat ?? 'Karawang' }}, {{ date('d F Y') }}</td>
    </tr>
    <tr>
        <td style="text-align:center;"><b>SEKRETARIS</b></td>
        <td style="text-align:center;"><b>KETUA</b></td>
        <td style="text-align:center;"><b>BENDAHARA</b></td>
    </tr>
    <tr><td></td><td></td><td></td></tr>
    <tr>
        <td style="text-align:center; text-decoration:underline;"><b>{{ $ttd->sekretaris_nama ?? '.....................' }}</b></td>
        <td style="text-align:center; text-decoration:underline;"><b>{{ $ttd->ketua_nama ?? '.....................' }}</b></td>
        <td style="text-align:center; text-decoration:underline;"><b>{{ $ttd->bendahara_nama ?? '.....................' }}</b></td>
    </tr>
</table>
