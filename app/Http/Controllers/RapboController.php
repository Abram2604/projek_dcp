<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class RapboController extends Controller
{
    // Simpan Data Baru
    public function store(Request $request)
    {
        $request->validate([
            'id_divisi' => 'required',
            'uraian_kegiatan' => 'required',
            'mp' => 'required|numeric|min:1',
            'thn' => 'required|numeric|min:1',
            'frek' => 'required|numeric|min:1',
            'nominal_satuan' => 'required|numeric'
        ]);

        $total = $request->mp * $request->thn * $request->frek * $request->nominal_satuan;

        DB::table('RAPBO')->insert([
            'id_divisi' => $request->id_divisi,
            'tahun_anggaran' => date('Y'), // Default tahun ini
            'uraian_kegiatan' => $request->uraian_kegiatan,
            'mp' => $request->mp,
            'thn' => $request->thn,
            'frek' => $request->frek,
            'nominal_satuan' => $request->nominal_satuan,
            'total_budget' => $total,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return back()->with('success', 'Item RAPBO berhasil ditambahkan.');
    }

    // Update Data
    public function update(Request $request, $id)
    {
        $total = $request->mp * $request->thn * $request->frek * $request->nominal_satuan;

        DB::table('RAPBO')->where('id', $id)->update([
            'uraian_kegiatan' => $request->uraian_kegiatan,
            'mp' => $request->mp,
            'thn' => $request->thn,
            'frek' => $request->frek,
            'nominal_satuan' => $request->nominal_satuan,
            'total_budget' => $total,
            'updated_at' => now()
        ]);

        return back()->with('success', 'RAPBO berhasil diperbarui.');
    }

    // Hapus Data
    public function destroy($id)
    {
        DB::table('RAPBO')->delete($id);
        return back()->with('success', 'Item dihapus.');
    }

    // Export PDF
    public function exportPdf(Request $request)
    {
        $idDivisi = $request->input('divisi_id');
        $tahun = date('Y');

        $query = DB::table('RAPBO')
            ->join('Divisi', 'RAPBO.id_divisi', '=', 'Divisi.id')
            ->select('RAPBO.*', 'Divisi.nama_divisi');

        if($idDivisi) {
            $query->where('RAPBO.id_divisi', $idDivisi);
        }

        // Grouping data by Divisi for PDF
        $data = $query->orderBy('Divisi.nama_divisi', 'asc')->get()->groupBy('nama_divisi');
        
        $pdf = Pdf::loadView('exports.pdf_rapbo', compact('data', 'tahun'))
                  ->setPaper('a4', 'landscape'); // Landscape agar muat tabel lebar

        return $pdf->download('RAPBO_DPC_SPSI_'.$tahun.'.pdf');
    }
}