<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithColumnWidths; // Tambahan ini
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Illuminate\Contracts\View\View;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class DataAnggotaExport implements FromView, WithStyles, WithDrawings, WithColumnWidths
{
    protected $dataPuk;
    protected $totalAnggota;
    protected $ttd;

    public function __construct($dataPuk, $totalAnggota, $ttd)
    {
        $this->dataPuk = $dataPuk;
        $this->totalAnggota = $totalAnggota;
        $this->ttd = $ttd;
    }

    public function view(): View
    {
        return view('exports.excel_data_anggota', [
            'dataPuk' => $this->dataPuk,
            'totalAnggota' => $this->totalAnggota,
            'ttd' => $this->ttd
        ]);
    }

    // 1. ATUR LEBAR KOLOM (BIAR RAPI KAYAK PDF)
    public function columnWidths(): array
    {
        return [
            'A' => 5,   // NO
            'B' => 35,  // NAMA FEDERASI
            'C' => 30,  // NO BUKTI FEDERASI
            'D' => 40,  // SERIKAT PEKERJA
            'E' => 30,  // NO BUKTI PUK
            'F' => 12,  // JML
            'G' => 12,  // HASIL
            'H' => 12,  // TOTAL
            'I' => 15,  // AFILIASI
            'J' => 25,  // KETUA
            'K' => 25,  // SEKRETARIS
        ];
    }

    // 2. STYLING (BORDER & ALIGNMENT)
    public function styles(Worksheet $sheet)
    {
        $lastRow = count($this->dataPuk) + 6; 

        // a. Border untuk Tabel Data (Dari A4 sampai K-Akhir)
        $sheet->getStyle('A4:K' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true, // Agar teks panjang turun ke bawah
            ],
            'font' => [
                'name' => 'Arial',
                'size' => 10
            ]
        ]);

        // b. Header Tabel (Bold, Center, Abu-abu)
        $sheet->getStyle('A4:K5')->applyFromArray([
            'font' => ['bold' => true, 'size' => 9],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'F2F2F2'],
            ],
        ]);
        
        // c. Baris Induk (Row 6) - Bold
        $sheet->getStyle('A6:K6')->getFont()->setBold(true);

        // d. Footer Total - Bold & Abu-abu
        $sheet->getStyle('A'.$lastRow.':K'.$lastRow)->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'F2F2F2'],
            ],
        ]);

        return [];
    }

    // 3. POSISI GAMBAR TANDA TANGAN
    public function drawings()
    {
        $drawings = [];
        $lastRow = count($this->dataPuk) + 6;
        
        // Jarak baris dari tabel ke tanda tangan (sesuai view blade)
        // Di blade ada 3 baris kosong (<br>) sebelum gambar
        $ttdRow = $lastRow + 6; 

        // Helper function
        $addDrawing = function($path, $col, $row) use (&$drawings) {
            if ($path && file_exists(public_path('storage/' . $path))) {
                $drawing = new Drawing();
                $drawing->setName('TTD');
                $drawing->setPath(public_path('storage/' . $path));
                $drawing->setHeight(70); // Tinggi disesuaikan
                $drawing->setCoordinates($col . $row);
                $drawing->setOffsetX(30); // Geser dikit biar tengah
                $drawings[] = $drawing;
            }
        };

        // Gambar Kadis (Di Kolom B / C)
        $addDrawing($this->ttd->path_ttd_kadis, 'B', $ttdRow);

        // Gambar Ketua (Di Kolom J)
        $addDrawing($this->ttd->path_ttd_ketua, 'J', $ttdRow);

        // Gambar Sekretaris (Di Kolom K)
        $addDrawing($this->ttd->path_ttd_sekretaris, 'K', $ttdRow);

        return $drawings;
    }
}