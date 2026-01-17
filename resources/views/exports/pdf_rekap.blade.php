<!DOCTYPE html>
<html>
<head>
    <title>Rekap Absensi</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 3px; text-align: center; }
        .head { background-color: #f0f0f0; text-transform: uppercase; font-weight: bold; }
        .libur { background-color: #f8d7da; color: #721c24; }
        .dinas { background-color: #d1e7dd; color: #0f5132; }
        .lembur { background-color: #fff3cd; color: #664d03; }
    </style>
</head>
<body>
    <div style="text-align: center; margin-bottom: 15px;">
        <h2 style="margin: 0;">DPC FSP LEM SPSI KARAWANG</h2>
        <p style="margin: 2px;">REKAPITULASI ABSENSI PENGURUS</p>
        <p style="margin: 2px;">Periode: {{ $startDate->isoFormat('MMMM Y') }}</p>
    </div>

    <table>
        <thead>
            <tr class="head">
                <th width="3%">No</th>
                <th width="8%">Hari</th>
                <th width="12%">Tanggal</th>
                @foreach($anggota as $usr)
                    <th>{{ $usr['inisial'] }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($dates as $index => $date)
                @php
                    $isHoliday = $date->isSunday() || isset($hariLibur[$date->toDateString()]);
                    $holidayName = $hariLibur[$date->toDateString() ?? ''] ?? 'LIBUR MINGGU';
                @endphp
                <tr class="{{ $isHoliday ? 'libur' : '' }}">
                    <td>{{ $index + 1 }}</td>
                    <td style="text-align: left;">{{ $date->isoFormat('dddd') }}</td>
                    <td style="text-align: left;">{{ $date->format('d/m/Y') }}</td>

                    @if($isHoliday)
                        <td colspan="{{ count($anggota) }}" style="font-weight: bold; letter-spacing: 1px; font-size: 9px;">
                            {{ $holidayName }}
                        </td>
                    @else
                        @foreach($anggota as $usr)
                            @php
                                $status = $matrixData[$date->toDateString()][$usr['id']] ?? '';
                                $cls = '';
                                if($status == 'D') $cls = 'dinas';
                                if($status == 'O') $cls = 'lembur';
                            @endphp
                            <td class="{{ $cls }}">
                                {{ ($status == 'H' || $status == 'D' || $status == 'O') ? '1' : '' }}
                            </td>
                        @endforeach
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
