<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanDanaBidangExport;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $levelAkses = session('user_level', 'ANGGOTA');
        $isBPH = $levelAkses === 'BPH';
        $jabatan = session('user_jabatan', '');
        $today = Carbon::today();

        // Cek apakah user adalah pengurus harian (ketua, sekretaris, bendahara)
        $isPengurusHarian = in_array($jabatan, ['Ketua DPC', 'Sekretaris', 'Bendahara']);

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
            'isPengurusHarian',
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
            $rules['id_divisi'] = 'required';
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

        // Handle id_divisi: jika 'KESEK', cari atau buat divisi Kesekretariatan
        $idDivisiInput = $isBPH ? $request->input('id_divisi') : $user->id_divisi;
        if ($idDivisiInput === 'KESEK') {
            // Cari divisi Kesekretariatan
            $divisiKesek = DB::table('Divisi')
                ->where('nama_divisi', 'Bidang Kesekretariatan')
                ->first();
            
            if ($divisiKesek) {
                $idDivisi = (int) $divisiKesek->id;
            } else {
                // Buat divisi baru jika belum ada
                $idDivisi = DB::table('Divisi')->insertGetId([
                    'nama_divisi' => 'Bidang Kesekretariatan',
                    'kode_divisi' => 'KESEK',
                    'deskripsi' => 'Kesekretariatan dan administrasi',
                    'dibuat_pada' => now(),
                ]);
            }
        } else {
            $idDivisi = (int) $idDivisiInput;
        }
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

    public function exportExcel(Request $request)
    {
        // Hanya BPH yang bisa export
        $levelAkses = session('user_level', 'ANGGOTA');
        if ($levelAkses !== 'BPH') {
            abort(403, 'Hanya BPH yang dapat mengakses fitur export.');
        }

        // Ambil bulan dan tahun dari request, default bulan dan tahun saat ini
        $bulan = (int) $request->input('bulan', Carbon::now()->month);
        $tahun = (int) $request->input('tahun', Carbon::now()->year);

        // Validasi bulan dan tahun
        if ($bulan < 1 || $bulan > 12) {
            $bulan = Carbon::now()->month;
        }
        if ($tahun < 2000 || $tahun > 2100) {
            $tahun = Carbon::now()->year;
        }

        $divisiList = DB::select('CALL sp_divisi_list()');
        $divisiInput = $request->input('divisi', 'all');
        $exportAllDivisi = $divisiInput === 'all' || $divisiInput === null || $divisiInput === '';
        $divisiId = $exportAllDivisi ? null : (int) $divisiInput;
        $divisiNama = 'Semua Divisi';
        if (!$exportAllDivisi) {
            foreach ($divisiList as $divisi) {
                if ((int) $divisi->id === $divisiId) {
                    $divisiNama = $divisi->nama_divisi;
                    break;
                }
            }
        }

        // Hitung tanggal awal dan akhir bulan
        $startDate = Carbon::create($tahun, $bulan, 1);
        $endDate = $startDate->copy()->endOfMonth();

        $saldoAwal = 0;
        $totalPengeluaran = 0;
        $sisaSaldo = 0;
        $rekapDivisi = [];
        if ($exportAllDivisi) {
            $rekapDivisi = DB::select('CALL sp_laporan_saldo_bph(?, ?)', [$bulan, $tahun]);
            foreach ($rekapDivisi as $row) {
                $saldoAwal += (float) $row->saldo_awal;
                $totalPengeluaran += (float) $row->total_pengeluaran;
                $sisaSaldo += (float) $row->sisa_saldo;
            }
        } elseif ($divisiId !== null) {
            $saldoRows = DB::select('CALL sp_laporan_saldo_divisi(?, ?, ?)', [$divisiId, $bulan, $tahun]);
            $saldo = $saldoRows[0] ?? null;
            if ($saldo) {
                $saldoAwal = (float) $saldo->saldo_awal;
                $totalPengeluaran = (float) $saldo->total_pengeluaran;
                $sisaSaldo = (float) $saldo->sisa_saldo;
            }
        }

        $danaMasuk = 0;
        $pemasukanRows = DB::select('CALL sp_keuangan_pemasukan_list(?, ?)', [$bulan, $tahun]);
        foreach ($pemasukanRows as $row) {
            $matches = $exportAllDivisi;
            if (!$matches && isset($row->id_divisi)) {
                $matches = (int) $row->id_divisi === $divisiId;
            }
            if (!$matches && isset($row->nama_divisi)) {
                $matches = $row->nama_divisi === $divisiNama;
            }
            if ($matches) {
                $danaMasuk += (float) $row->jumlah_rupiah;
            }
        }

        if ($exportAllDivisi) {
            $detailItems = DB::select('CALL sp_keuangan_pengeluaran_list(?, ?)', [$bulan, $tahun]);
        } elseif ($divisiId !== null) {
            $detailItems = DB::select('CALL sp_keuangan_pengeluaran_detail(?, ?, ?)', [$divisiId, $bulan, $tahun]);
        } else {
            $detailItems = [];
        }

        $detailTotal = 0;
        foreach ($detailItems as $item) {
            $detailTotal += (float) ($item->total_nominal ?? 0);
        }

        if ($detailTotal > 0) {
            $totalPengeluaran = $detailTotal;
        }

        $totalDana = $saldoAwal + $danaMasuk;
        $sisaSaldo = $totalDana - $totalPengeluaran;

        // Siapkan data untuk export
        $data = [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'divisiNama' => $divisiNama,
            'penanggungJawab' => Auth::user()->nama_lengkap ?? '-',
            'saldoAwal' => $saldoAwal,
            'danaMasuk' => $danaMasuk,
            'totalDana' => $totalDana,
            'totalPengeluaran' => $totalPengeluaran,
            'sisaSaldo' => $sisaSaldo,
            'detailItems' => $detailItems,
        ];

        // Generate filename
        $safeDivisi = preg_replace('/[^A-Za-z0-9]+/', '_', $divisiNama);
        $filename = 'Laporan_Pengeluaran_Dana_Bidang_' . $safeDivisi . '_' . $startDate->format('F_Y') . '.xlsx';

        return Excel::download(new LaporanDanaBidangExport($data), $filename);
    }
}
