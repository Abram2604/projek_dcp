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

class RekapInsentifExport implements FromView, ShouldAutoSize, WithTitle, WithStyles, WithColumnFormatting
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('exports.excel_insentif', $this->data);
    }

    public function title(): string
    {
        return 'Laporan Insentif';
    }

    /**
     * Styling tambahan agar header rapi (Teks di tengah & Wrap Text)
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Baris 4 (Header Tabel)
            4 => [
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true, // Penting agar teks panjang turun ke bawah
                ],
            ],
            // Set tinggi baris header agar teks wrap terlihat jelas
            4 => ['row_height' => 35],
        ];
    }

    /**
     * Format kolom angka agar ada pemisah ribuan (titik)
     * Kolom D, E, F, G, H berisi nominal uang
     */
    public function columnFormats(): array
    {
        return [
            'H' => '#,##0', // Format Angka (Total Terima)
        ];
    }
}