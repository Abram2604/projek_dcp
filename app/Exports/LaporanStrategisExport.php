<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class LaporanStrategisExport implements FromView, WithStyles, WithColumnWidths, WithTitle
{
    protected $data;
    protected $bulan;
    protected $tahun;
    protected $type;
    protected $ttd;

    /**
     * @param array $data  Data keuangan (income, expenses, assets, dll)
     * @param int $bulan   Angka bulan (1-12)
     * @param int $tahun   Angka tahun
     * @param string $type Jenis laporan ('flow', 'position', 'summary')
     * @param object $ttd  Data tanda tangan dari database
     */
    public function __construct($data, $bulan, $tahun, $type, $ttd)
    {
        $this->data  = $data;
        $this->bulan = $bulan;
        $this->tahun = $tahun;
        $this->type  = $type;
        $this->ttd   = $ttd;
    }

    /**
     * Menghubungkan data ke file Blade template untuk render Excel
     */
    public function view(): View
    {
        // Kita akan menggunakan template blade yang sama untuk konsistensi
        return view('exports.excel_laporan_strategis', [
            'data'  => $this->data,
            'bulan' => $this->bulan,
            'tahun' => $this->tahun,
            'type'  => $this->type,
            'ttd'   => $this->ttd,
            'namaBulan' => Carbon::create()->month($this->bulan)->isoFormat('MMMM')
        ]);
    }

    /**
     * Memberikan Nama pada Sheet Excel
     */
    public function title(): string
    {
        return match($this->type) {
            'flow'     => 'Dana Masuk Keluar',
            'position' => 'Posisi Keuangan',
            'summary'  => 'Ringkasan Keuangan',
            default    => 'Laporan Keuangan'
        };
    }

    /**
     * Mengatur Lebar Kolom (Presisi seperti PDF)
     */
    public function columnWidths(): array
    {
        return [
            'A' => 45,  // Keterangan / Deskripsi
            'B' => 20,  // Nilai Periode I
            'C' => 20,  // Nilai Periode II (Total)
            'D' => 5,   // Spacer
            'E' => 20,  // Untuk layout tanda tangan jika diperlukan
        ];
    }

    /**
     * Mengatur Styling, Border, dan Warna
     */
    public function styles(Worksheet $sheet)
    {
        // 1. Styling Judul Utama (Baris 1 & 2)
        $sheet->mergeCells('A1:C1');
        $sheet->mergeCells('A2:C2');
        $sheet->getStyle('A1:A2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        // 2. Mencari baris terakhir yang berisi data untuk menentukan batas border
        $highestRow = $sheet->getHighestRow();
        
        // Asumsi Tabel dimulai dari baris 4
        $tableRange = 'A4:C' . ($highestRow - 10); // Mengurangi area TTD agar tidak kena border tabel

        // 3. All Borders untuk area Tabel
        $sheet->getStyle($tableRange)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ]
        ]);

        // 4. Header Tabel (Warna Abu-abu & Center)
        $sheet->getStyle('A4:C4')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'F2F2F2'],
            ],
        ]);

        // 5. Memberikan warna khusus pada baris "TOTAL" atau "SALDO AKHIR"
        // Kita cari teks di kolom A, jika mengandung kata 'TOTAL' atau 'SALDO', beri warna background
        foreach ($sheet->getRowIterator() as $row) {
            $cellValue = $sheet->getCell('A' . $row->getRowIndex())->getValue();
            if (str_contains(strtoupper($cellValue), 'TOTAL') || str_contains(strtoupper($cellValue), 'SALDO AKHIR')) {
                $sheet->getStyle('A' . $row->getRowIndex() . ':C' . $row->getRowIndex())->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'E0E7FF'], // Indigo Light
                    ],
                ]);
            }
            
            // Sub Header (Pemasukan / Pengeluaran)
            if (in_array($cellValue, ['PEMASUKAN COS', 'PEMASUKAN NON COS', 'PENGELUARAN ( BEBAN ) ORGANISASI'])) {
                $sheet->getStyle('A' . $row->getRowIndex() . ':C' . $row->getRowIndex())->applyFromArray([
                    'font' => ['bold' => true, 'italic' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'DBEAFE'], // Blue Light
                    ],
                ]);
            }
        }

        // 6. Styling Area Tanda Tangan (Paling Bawah) agar tidak berantakan
        $startTtdRow = $highestRow - 8;
        $sheet->getStyle('A' . $startTtdRow . ':C' . $highestRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 10],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        return [];
    }
}