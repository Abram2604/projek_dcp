<table>
    <thead>
        <tr>
            <th colspan="{{ count($anggota) + 3 }}" style="font-weight: bold; text-align: center; font-size: 14px;">
                REKAP ABSENSI DPC FSP LEM SPSI KARAWANG
            </th>
        </tr>
        <tr>
            <th colspan="{{ count($anggota) + 3 }}" style="text-align: center;">
                Periode: {{ $startDate->isoFormat('MMMM Y') }}
            </th>
        </tr>
        <tr></tr>
        <tr>
            <th style="font-weight: bold; border: 1px solid #000000; width: 50px;">NO</th>
            <th style="font-weight: bold; border: 1px solid #000000; width: 100px;">HARI</th>
            <th style="font-weight: bold; border: 1px solid #000000; width: 120px;">TANGGAL</th>
            @foreach($anggota as $usr)
                <th style="font-weight: bold; border: 1px solid #000000; width: 50px; text-align: center;">{{ $usr['inisial'] }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($dates as $index => $date)
            @php
                $dateString = $date->toDateString();
                $isHoliday = $date->isSunday() || isset($hariLibur[$dateString]);
                $holidayName = $hariLibur[$dateString] ?? 'LIBUR';
                // Warna background untuk Excel (ARGB Hex)
                $bgColor = $isHoliday ? 'FFFF9999' : 'FFFFFFFF'; 
            @endphp
            <tr>
                <td style="border: 1px solid #000000; background-color: {{ $bgColor }};">{{ $index + 1 }}</td>
                <td style="border: 1px solid #000000; background-color: {{ $bgColor }};">{{ $date->isoFormat('dddd') }}</td>
                <td style="border: 1px solid #000000; background-color: {{ $bgColor }};">{{ $date->format('d M Y') }}</td>

                @if($isHoliday)
                    <td colspan="{{ count($anggota) }}" style="border: 1px solid #000000; background-color: {{ $bgColor }}; text-align: center; font-weight: bold;">
                        {{ $holidayName }}
                    </td>
                @else
                    @foreach($anggota as $usr)
                        @php
                            $status = $matrixData[$dateString][$usr['id']] ?? '';
                            $cellColor = 'FFFFFFFF';
                            if ($status == 'D') $cellColor = 'FF99CC00'; // Hijau
                            if ($status == 'O') $cellColor = 'FFFFFF00'; // Kuning
                        @endphp
                        <td style="border: 1px solid #000000; background-color: {{ $cellColor }}; text-align: center;">
                            {{ $status == 'H' || $status == 'O' || $status == 'D' ? '1' : '' }}
                        </td>
                    @endforeach
                @endif
            </tr>
        @endforeach
    </tbody>
</table>
