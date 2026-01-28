<!DOCTYPE html>
<html>
<head>
    <title>RAPBO DPC SPSI</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 5px; }
        th { background-color: #FFFF00; text-align: center; font-weight: bold; text-transform: uppercase; } /* Warna Kuning seperti Excel */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bg-grey { background-color: #f0f0f0; font-weight: bold; }
        .bg-orange { background-color: #F4B084; font-weight: bold; } /* Warna oranye total */
    </style>
</head>
<body>
    
    <div style="text-align: center; margin-bottom: 20px;">
        <h2 style="margin:0; background-color: #FFFF00; padding: 5px; border: 1px solid #000;">
            RANCANGAN ANGGARAN PENDAPATAN & BELANJA ORGANISASI TAHUN {{ $tahun }} - {{ $tahun + 1 }}
        </h2>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" width="5%">NO</th>
                <th rowspan="2">PROGRAM KERJA BIDANG</th>
                <th colspan="3">FREKUENSI</th>
                <th rowspan="2" width="15%">Nominal</th>
                <th rowspan="2" width="15%">Budget / Th {{ $tahun }}-{{ $tahun+1 }}</th>
                <th rowspan="2" width="15%">Serapan Anggaran</th>
            </tr>
            <tr>
                <th>MP</th>
                <th>Thn</th>
                <th>Frek</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; @endphp
            @foreach($data as $namaDivisi => $items)
                <!-- Header Divisi -->
                <tr class="bg-grey">
                    <td class="text-center">{{ chr(64 + $loop->iteration) }}</td>
                    <td colspan="7">{{ $namaDivisi }}</td>
                </tr>

                @php $subTotal = 0; @endphp
                @foreach($items as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $item->uraian_kegiatan }}</td>
                        <td class="text-center">{{ $item->mp }}</td>
                        <td class="text-center">{{ $item->thn }}</td>
                        <td class="text-center">{{ $item->frek }}</td>
                        <td class="text-right">{{ number_format($item->nominal_satuan, 0, ',', '.') }}</td>
                        <td class="text-right fw-bold">{{ number_format($item->total_budget, 0, ',', '.') }}</td>
                        <td></td> <!-- Serapan Kosong -->
                    </tr>
                    @php $subTotal += $item->total_budget; @endphp
                @endforeach

                <!-- Subtotal Per Bidang -->
                <tr class="bg-orange">
                    <td colspan="6" class="text-right">Jumlah plan Budget {{ $namaDivisi }}</td>
                    <td class="text-right">{{ number_format($subTotal, 0, ',', '.') }}</td>
                    <td></td>
                </tr>
                @php $grandTotal += $subTotal; @endphp
            @endforeach
        </tbody>
    </table>

</body>
</html>