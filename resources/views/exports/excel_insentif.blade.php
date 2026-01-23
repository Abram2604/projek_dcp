<table>
    <thead>
        <!-- JUDUL LAPORAN -->
        <tr>
            <th colspan="8" style="font-weight: bold; text-align: center; font-size: 14px; height: 30px; vertical-align: middle;">
                LAPORAN INSENTIF PENGURUS DPC FSP LEM SPSI KARAWANG
            </th>
        </tr>
        <tr>
            <th colspan="8" style="text-align: center; height: 25px; vertical-align: middle;">
                Periode: {{ $startDate->isoFormat('MMMM Y') }}
            </th>
        </tr>
        <tr></tr> <!-- Baris Kosong -->

        <!-- HEADER TABEL -->
        <tr>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #eeeeee; text-align: center; vertical-align: middle;">NO</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #eeeeee; text-align: center; vertical-align: middle;">NAMA PENGURUS</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #eeeeee; text-align: center; vertical-align: middle;">JABATAN</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #eeeeee; text-align: center; vertical-align: middle;">HADIR (100k)</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #eeeeee; text-align: center; vertical-align: middle;">LEMBUR (50k)</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #eeeeee; text-align: center; vertical-align: middle;">DINAS LUAR (150k)</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #eeeeee; text-align: center; vertical-align: middle;">DINAS MENGINAP (300k)</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #FFD700; text-align: center; vertical-align: middle;">TOTAL TERIMA (Rp)</th>
        </tr>
    </thead>
    <tbody>
        @php $no = 1; $grandTotal = 0; @endphp
        @foreach($rekapInsentif as $uid => $row)
            @php $grandTotal += $row['total_terima']; @endphp
            <tr>
                <!-- NO -->
                <td style="border: 1px solid #000000; text-align: center; vertical-align: middle;">{{ $no++ }}</td>
                
                <!-- NAMA -->
                <td style="border: 1px solid #000000; vertical-align: middle;">{{ $row['nama'] }}</td>
                
                <!-- JABATAN -->
                <td style="border: 1px solid #000000; vertical-align: middle;">{{ $row['jabatan'] }}</td>
                
                <!-- JUMLAH ITEM (CENTER) -->
                <td style="border: 1px solid #000000; text-align: center; vertical-align: middle;">{{ $row['jml_hadir'] }}</td>
                <td style="border: 1px solid #000000; text-align: center; vertical-align: middle;">{{ $row['jml_lembur'] }}</td>
                <td style="border: 1px solid #000000; text-align: center; vertical-align: middle;">{{ $row['jml_dl'] }}</td>
                <td style="border: 1px solid #000000; text-align: center; vertical-align: middle;">{{ $row['jml_dm'] }}</td>
                
                <!-- TOTAL UANG (RIGHT ALIGN, BOLD, YELLOW BG) -->
                <td style="border: 1px solid #000000; font-weight: bold; text-align: right; background-color: #fff8dc; vertical-align: middle;">
                    {{ $row['total_terima'] }}
                </td>
            </tr>
        @endforeach

        <!-- BARIS GRAND TOTAL -->
        <tr>
            <td colspan="7" style="border: 1px solid #000000; font-weight: bold; text-align: right; background-color: #eeeeee; vertical-align: middle;">GRAND TOTAL</td>
            <td style="border: 1px solid #000000; font-weight: bold; background-color: #FFD700; text-align: right; vertical-align: middle;">
                {{ $grandTotal }}
            </td>
        </tr>
    </tbody>
</table>