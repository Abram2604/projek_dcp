<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class RekapAbsensiExport implements FromView, ShouldAutoSize, WithTitle
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        // Kita gunakan view khusus yang strukturnya tabel murni
        return view('exports.excel_rekap', $this->data);
    }

    public function title(): string
    {
        return 'Rekap Absensi';
    }
}