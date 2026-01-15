<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $levelAkses = session('user_level', 'ANGGOTA');
        $isBPH = $levelAkses === 'BPH';
        $today = Carbon::today();

        $bulan = $today->month;
        $tahun = $today->year;

        if ($isBPH) {
            $ringkasanSaldo = DB::select('CALL sp_laporan_saldo_bph(?, ?)', [$bulan, $tahun]);
            $divisiList = DB::select('CALL sp_divisi_list()');
            $programKerja = DB::select('CALL sp_program_kerja_by_divisi(?)', [null]);
        } else {
            $ringkasanSaldo = DB::select('CALL sp_laporan_saldo_divisi(?, ?, ?)', [$user->id_divisi, $bulan, $tahun]);
            $divisiList = [];
            $programKerja = DB::select('CALL sp_program_kerja_by_divisi(?)', [$user->id_divisi]);
        }

        $filters = [
            'q' => $request->input('q'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'divisi' => $request->input('divisi'),
        ];

        $laporanList = DB::select('CALL sp_laporan_list(?, ?, ?, ?, ?, ?)', [
            $levelAkses,
            $user->id_divisi,
            $filters['q'],
            $filters['start_date'] ?: null,
            $filters['end_date'] ?: null,
            $filters['divisi'] ?: null,
        ]);

        return view('pages.laporan.index', compact(
            'levelAkses',
            'isBPH',
            'ringkasanSaldo',
            'laporanList',
            'divisiList',
            'programKerja',
            'filters'
        ));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $levelAkses = session('user_level', 'ANGGOTA');
        $isBPH = $levelAkses === 'BPH';

        $rules = [
            'judul_kegiatan' => 'required|string|max:200',
            'tanggal_laporan' => 'required|date',
            'isi_laporan' => 'required|string',
            'id_program_kerja' => 'nullable|integer',
            'lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
            'include_financial' => 'nullable|boolean',
        ];

        if ($isBPH) {
            $rules['id_divisi'] = 'required|integer';
        }

        $request->validate($rules);

        $includeFinancial = $request->boolean('include_financial');
        $items = [];

        $descriptions = $request->input('item_description', []);
        $volumes = $request->input('item_volume', []);
        $units = $request->input('item_unit', []);
        $amounts = $request->input('item_amount', []);

        $count = max(count($descriptions), count($volumes), count($units), count($amounts));
        for ($i = 0; $i < $count; $i++) {
            $desc = trim((string) ($descriptions[$i] ?? ''));
            $amount = (float) ($amounts[$i] ?? 0);
            $volume = (float) ($volumes[$i] ?? 1);
            $unit = trim((string) ($units[$i] ?? 'Unit'));

            if ($desc === '' && $amount <= 0) {
                continue;
            }

            if ($desc === '' || $amount <= 0) {
                continue;
            }

            $items[] = [
                'description' => $desc,
                'volume' => $volume > 0 ? $volume : 1,
                'unit' => $unit !== '' ? $unit : 'Unit',
                'amount' => $amount,
            ];
        }

        if ($includeFinancial && count($items) === 0) {
            return back()
                ->withErrors(['item_description' => 'Rincian pengeluaran wajib diisi jika laporan pengeluaran dicentang.'])
                ->withInput();
        }

        $idDivisi = $isBPH ? (int) $request->input('id_divisi') : (int) $user->id_divisi;
        $idProgram = $request->input('id_program_kerja');
        $idProgram = $idProgram !== null && $idProgram !== '' ? (int) $idProgram : null;

        $tanggal = $request->input('tanggal_laporan');
        $judul = $request->input('judul_kegiatan');
        $isi = $request->input('isi_laporan');

        $lampiranUrl = null;
        if ($request->hasFile('lampiran')) {
            $path = $request->file('lampiran')->store('laporan', 'public');
            $lampiranUrl = Storage::url($path);
        }

        DB::beginTransaction();
        try {
            DB::select('CALL sp_laporan_create(?, ?, ?, ?, ?, ?, ?, ?)', [
                $idDivisi,
                $user->id,
                $idProgram,
                $tanggal,
                $judul,
                $isi,
                $lampiranUrl,
                'DISUBMIT',
            ]);

            if ($includeFinancial && count($items) > 0) {
                $periodeRows = DB::select('CALL sp_periode_keuangan_get(?, ?)', [$idDivisi, $tanggal]);
                if (count($periodeRows) === 0) {
                    throw new \RuntimeException('Periode keuangan belum tersedia untuk bulan laporan.');
                }

                $periodeId = $periodeRows[0]->id;

                foreach ($items as $item) {
                    DB::select('CALL sp_pengeluaran_create(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                        $periodeId,
                        $idProgram,
                        $tanggal,
                        $judul,
                        $item['description'],
                        $item['volume'],
                        $item['unit'],
                        $item['amount'],
                        null,
                        null,
                    ]);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withErrors(['submit' => 'Gagal menyimpan laporan: ' . $e->getMessage()])
                ->withInput();
        }

        return redirect()->route('laporan.index')->with('success', 'Laporan berhasil disimpan.');
    }

    public function show(int $id)
    {
        $user = Auth::user();
        $levelAkses = session('user_level', 'ANGGOTA');
        $isBPH = $levelAkses === 'BPH';

        $detailRows = DB::select('CALL sp_laporan_detail(?)', [$id]);
        if (count($detailRows) === 0) {
            abort(404);
        }

        $laporan = $detailRows[0];

        if (!$isBPH && (int) $laporan->id_divisi !== (int) $user->id_divisi) {
            abort(403);
        }

        $pengeluaran = DB::select('CALL sp_laporan_pengeluaran_detail(?)', [$id]);
        $totalPengeluaran = 0;
        foreach ($pengeluaran as $item) {
            $totalPengeluaran += (float) $item->total_nominal;
        }

        return view('pages.laporan.show', compact('laporan', 'pengeluaran', 'totalPengeluaran', 'isBPH'));
    }
}
