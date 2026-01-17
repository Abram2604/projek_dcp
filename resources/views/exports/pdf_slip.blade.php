<!DOCTYPE html>
<html>
<head>
    <title>Slip Insentif</title>
    <style>
        body { font-family: sans-serif; color: #333; }
        .container { border: 1px solid #ddd; padding: 20px; }
        .header { text-align: center; border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 20px; }
        .logo { height: 50px; margin-bottom: 10px; }
        .title { font-size: 18px; font-weight: bold; text-transform: uppercase; margin: 0; }
        .subtitle { font-size: 12px; color: #666; margin: 5px 0; }
        
        .info-table { width: 100%; margin-bottom: 20px; font-size: 12px; }
        .info-table td { padding: 4px 0; }
        
        .details-table { width: 100%; border-collapse: collapse; font-size: 12px; margin-bottom: 20px; }
        .details-table th { background-color: #f8f9fa; text-align: left; padding: 8px; border-bottom: 1px solid #ddd; text-transform: uppercase; color: #555; }
        .details-table td { padding: 8px; border-bottom: 1px solid #eee; }
        .total-row td { font-weight: bold; border-top: 2px solid #ddd; font-size: 14px; background-color: #f8f9fa; }
        
        .footer { text-align: center; font-size: 10px; color: #999; margin-top: 30px; font-style: italic; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            {{-- Gunakan public_path untuk gambar di DOMPDF --}}
            <img src="{{ public_path('img/logo.png') }}" class="logo" alt="Logo">
            <h1 class="title">Slip Insentif Kehadiran</h1>
            <p class="subtitle">DPC FSP LEM SPSI KABUPATEN KARAWANG</p>
        </div>

        <table class="info-table">
            <tr>
                <td width="15%"><strong>Nama</strong></td>
                <td width="35%">: {{ $slipData['nama'] }}</td>
                <td width="15%"><strong>Periode</strong></td>
                <td width="35%">: {{ $periode->isoFormat('MMMM Y') }}</td>
            </tr>
            <tr>
                <td><strong>ID Ref</strong></td>
                <td>: SLIP-{{ $userId }}-{{ $periode->format('mY') }}</td>
                <td><strong>Tanggal Cetak</strong></td>
                <td>: {{ date('d/m/Y') }}</td>
            </tr>
        </table>

        <table class="details-table">
            <thead>
                <tr>
                    <th>Keterangan Sumber Penghasilan</th>
                    <th style="text-align: center;">Volume</th>
                    <th style="text-align: right;">Jumlah (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>Uang Hadir / Rapat</strong><br>
                        <span style="font-size: 10px; color: #666;">Insentif kehadiran harian & lembur</span>
                    </td>
                    <td style="text-align: center;">{{ $slipData['hadir'] }}</td>
                    <td style="text-align: right;">{{ number_format($slipData['nominal_hadir'], 0, ',', '.') }}</td>
                </tr>
                @if($slipData['dinas'] > 0)
                <tr>
                    <td>
                        <strong>Dinas Luar Kota</strong><br>
                        <span style="font-size: 10px; color: #666;">Insentif tugas luar kantor</span>
                    </td>
                    <td style="text-align: center;">{{ $slipData['dinas'] }}</td>
                    <td style="text-align: right;">{{ number_format($slipData['nominal_dinas'], 0, ',', '.') }}</td>
                </tr>
                @endif
                
                <tr class="total-row">
                    <td colspan="2" style="text-align: right; padding-right: 20px;">TOTAL DITERIMA</td>
                    <td style="text-align: right; color: #4f46e5;">Rp {{ number_format($slipData['total_terima'], 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <div class="footer">
            Dokumen ini diterbitkan secara otomatis oleh Sistem Informasi DPC FSP LEM SPSI.<br>
            Tidak memerlukan tanda tangan basah.
        </div>
    </div>
</body>
</html>
