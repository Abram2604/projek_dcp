@php
    $namaOrganisasi = 'DPC FPS LEM SPSI Kabupaten Karawang';
    $divisiNama = $divisiNama ?? 'Sekretariat';
    $bulanTahun = $startDate->isoFormat('MMMM Y');
    $penanggungJawab = $penanggungJawab ?? '-';
    $detailItems = $detailItems ?? [];
    $grandTotal = 0;
@endphp

<table style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr>
            <th colspan="7" style="font-weight: bold; text-align: center; font-size: 16px; height: 24px; border: none;">
                LAPORAN PENGELUARAN DANA KEGIATAN BIDANG
            </th>
        </tr>
        <tr>
            <th colspan="7" style="height: 8px; border: none;"></th>
        </tr>
        <tr>
            <th style="text-align: left; border: none; width: 180px;">Nama Organisasi</th>
            <th colspan="6" style="text-align: left; border: none;">{{ $namaOrganisasi }}</th>
        </tr>
        <tr>
            <th style="text-align: left; border: none;">Bidang</th>
            <th colspan="6" style="text-align: left; border: none;">{{ $divisiNama }}</th>
        </tr>
        <tr>
            <th style="text-align: left; border: none;">Bulan / Tahun</th>
            <th colspan="6" style="text-align: left; border: none;">{{ $bulanTahun }}</th>
        </tr>
        <tr>
            <th style="text-align: left; border: none;">Penanggung Jawab</th>
            <th colspan="6" style="text-align: left; border: none;">{{ $penanggungJawab }}</th>
        </tr>
        <tr>
            <th colspan="7" style="height: 8px; border: none;"></th>
        </tr>
        <tr>
            <th colspan="2" style="font-weight: bold; text-align: left; background-color: #d9d9d9; border: 1px solid #000000; padding: 6px;">
                RINGKASAN DANA
            </th>
        </tr>
        <tr>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #d9d9d9; text-align: left; padding: 6px;">Uraian</th>
            <th colspan="1" style="font-weight: bold; border: 1px solid #000000; background-color: #d9d9d9; text-align: left; padding: 6px;">Jumlah (Rp)</th>
        </tr>
        <tr>
            <td style="border: 1px solid #000000; padding: 6px;">Saldo Awal</td>
            <td style="border: 1px solid #000000; padding: 6px; text-align: right;">Rp {{ number_format($saldoAwal ?? 0, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000000; padding: 6px;">Dana Masuk</td>
            <td style="border: 1px solid #000000; padding: 6px; text-align: right;">Rp {{ number_format($danaMasuk ?? 0, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000000; padding: 6px;">Total Dana</td>
            <td style="border: 1px solid #000000; padding: 6px; text-align: right;">Rp {{ number_format($totalDana ?? 0, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000000; padding: 6px;">Total Pengeluaran</td>
            <td style="border: 1px solid #000000; padding: 6px; text-align: right;">Rp {{ number_format($totalPengeluaran ?? 0, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000000; padding: 6px;">Sisa Saldo</td>
            <td style="border: 1px solid #000000; padding: 6px; text-align: right;">Rp {{ number_format($sisaSaldo ?? 0, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th colspan="7" style="height: 8px; border: none;"></th>
        </tr>
        <tr>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #d9d9d9; text-align: center; padding: 6px;">Tanggal</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #d9d9d9; text-align: center; padding: 6px;">Nama Kegiatan</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #d9d9d9; text-align: center; padding: 6px;">Uraian Pengeluaran</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #d9d9d9; text-align: center; padding: 6px;">Volume</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #d9d9d9; text-align: center; padding: 6px;">Satuan</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #d9d9d9; text-align: center; padding: 6px;">Jumlah (Rp)</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #d9d9d9; text-align: center; padding: 6px;">Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @php
            $prevTanggal = null;
            $prevNamaKegiatan = null;
        @endphp
        @forelse($detailItems as $item)
            @php
                $tanggal = $item->tanggal_transaksi ?? ($item->tanggal ?? null);
                $namaKegiatan = $item->nama_kegiatan ?? ($item->nama_program ?? 'Operasional');
                $uraian = $item->uraian_pengeluaran ?: $namaKegiatan;
                $volume = $item->volume ?? 1;
                $satuan = $item->satuan ?? 'Unit';
                $jumlah = (float) ($item->total_nominal ?? 0);
                $keterangan = $item->keterangan ?? '';
                $grandTotal += $jumlah;
                $showTanggal = $tanggal ? \Carbon\Carbon::parse($tanggal)->isoFormat('dddd, MMMM D, Y') : '-';
                $showNamaKegiatan = $namaKegiatan;
                $shouldHideTanggal = $prevTanggal !== null && $prevTanggal === ($tanggal ?? '');
                $shouldHideNama = $prevNamaKegiatan !== null && $prevNamaKegiatan === $namaKegiatan;
                if ($shouldHideTanggal) {
                    $showTanggal = '';
                }
                if ($shouldHideNama) {
                    $showNamaKegiatan = '';
                }
                $prevTanggal = $tanggal ?? '';
                $prevNamaKegiatan = $namaKegiatan;
            @endphp
            <tr>
                <td style="border: 1px solid #000000; padding: 6px;">
                    {{ $showTanggal }}
                </td>
                <td style="border: 1px solid #000000; padding: 6px;">{{ $showNamaKegiatan }}</td>
                <td style="border: 1px solid #000000; padding: 6px;">{{ $uraian }}</td>
                <td style="border: 1px solid #000000; padding: 6px; text-align: center;">{{ $volume }}</td>
                <td style="border: 1px solid #000000; padding: 6px; text-align: center;">{{ $satuan }}</td>
                <td style="border: 1px solid #000000; padding: 6px; text-align: right;">Rp {{ number_format($jumlah, 0, ',', '.') }}</td>
                <td style="border: 1px solid #000000; padding: 6px;">{{ $keterangan }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="7" style="border: 1px solid #000000; padding: 6px; text-align: center;">Tidak ada data pengeluaran.</td>
            </tr>
        @endforelse
        <tr>
            <td colspan="7" style="height: 6px; border: none;"></td>
        </tr>
        <tr>
            <td colspan="5" style="border: 1px solid #000000; font-weight: bold; background-color: #fff000; text-align: center; padding: 6px;">
                GRAN TOTAL
            </td>
            <td colspan="2" style="border: 1px solid #000000; font-weight: bold; background-color: #fff000; text-align: right; padding: 6px;">
                Rp {{ number_format($grandTotal, 0, ',', '.') }}
            </td>
        </tr>
    </tbody>
</table>
