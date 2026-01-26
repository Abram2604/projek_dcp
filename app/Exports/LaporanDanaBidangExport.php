<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class LaporanDanaBidangExport implements FromView, ShouldAutoSize, WithTitle, WithStyles, WithColumnFormatting
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('exports.excel_laporan_dana_bidang', $this->data);
    }

    public function title(): string
    {
        return 'Laporan Pengeluaran Dana Bidang';
    }

    /**
     * Styling untuk header dan baris
     */
    public function styles(Worksheet $sheet)
    {
        $detailCount = count($this->data['detailItems'] ?? []);
        $summaryTitleRow = 8;
        $summaryHeaderRow = 9;
        $detailHeaderRow = 16;
        $grandTotalRow = $detailHeaderRow + $detailCount + 2;

        return [
            // Judul utama (baris 1)
            1 => [
                'font' => ['bold' => true, 'size' => 16],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
            // Header ringkasan dana
            $summaryTitleRow => [
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
            $summaryHeaderRow => [
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
            // Header tabel detail
            $detailHeaderRow => [
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => false,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'D9D9D9'],
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ],
            // Grand total
            $grandTotalRow => [
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    /**
     * Format kolom angka untuk total pengeluaran
     */
    public function columnFormats(): array
    {
        return [
            'B' => '#,##0',
            'F' => '#,##0',
        ];
    }
}
