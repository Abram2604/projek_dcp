<!DOCTYPE html>
<html>

<head>
    <title>Rekap Absensi</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
        }

        th {
            background-color: #eee;
        }

        .libur {
            background-color: #ddd;
        }

        .dinas {
            background-color: #a7f3d0;
        }

        /* Hijau muda */
        .lembur {
            background-color: #fde68a;
        }

        /* Kuning muda */
    </style>
</head>

<body>
    <h2 style="text-align: center; margin-bottom: 5px;">REKAP ABSENSI PENGURUS DPC FSP LEM SPSI</h2>
    <p style="text-align: center; margin-top: 0;">Periode: {{ $startDate->isoFormat('MMMM Y') }}</p>

    <table>
        <thead>
            <tr>
                <th width="20">No</th>
                <th width="60">Tanggal</th>
                @foreach($anggota as $usr)
                <th>{{ $usr['inisial'] }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($dates as $index => $date)
            @php
            $isHoliday = $date->isSunday() || isset($hariLibur[$date->toDateString()]);
            $rowClass = $isHoliday ? 'libur' : '';
            @endphp
            <tr class="{{ $rowClass }}">
                <td>{{ $index + 1 }}</td>
                <td style="text-align: left;">{{ $date->format('d/m') }} {{ substr($date->isoFormat('ddd'), 0, 2) }}</td>

                @if($isHoliday)
                <td colspan="{{ count($anggota) }}">LIBUR</td>
                @else
                @foreach($anggota as $usr)
                @php
                $status = $matrixData[$date->toDateString()][$usr['id']] ?? '';
                $bg = '';
                if ($status == 'D') $bg = 'dinas';
                if ($status == 'O') $bg = 'lembur';
                @endphp
                <td class="{{ $bg }}">{{ $status }}</td>
                @endforeach
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>