<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RekapAbsensiExport;
use App\Exports\RekapInsentifExport; 
use App\Services\AbsensiService;
use Symfony\Component\HttpFoundation\Response;

class AbsensiController extends Controller
{
    protected $absensiService;

    public function __construct(AbsensiService $absensiService)
    {
        $this->absensiService = $absensiService;
    }

    // =========================================================================
    // MAIN FEATURES (WEB VIEW & ACTIONS)
    // =========================================================================

    public function index()
    {
        $user = Auth::user();

        // 1. GENERATE QR STATIS
        if (empty($user->string_kode_qr)) {
            $qrString = 'SPSI-' . date('Y') . '-' . str_pad($user->id, 3, '0', STR_PAD_LEFT) . '-' . strtoupper(Str::random(4));
            DB::table('Anggota')->where('id', $user->id)->update(['string_kode_qr' => $qrString]);
            $user->string_kode_qr = $qrString; 
        }

        // 2. Ambil Status Absen Hari Ini
        $absenHariIni = DB::table('Riwayat_Absensi')
            ->where('id_anggota', $user->id)
            ->where('tanggal', Carbon::now()->toDateString())
            ->first();
        
        // 3. Ambil Riwayat Pribadi (5 Terakhir)
        $riwayatPribadi = DB::table('Riwayat_Absensi')
            ->where('id_anggota', $user->id)
            ->orderBy('tanggal', 'desc')
            ->limit(5)
            ->get();

        return view('pages.absensi.index', compact('user', 'absenHariIni', 'riwayatPribadi'));
    }

    public function storeHadir(Request $request)
    {
        if (Carbon::now()->isSunday()) {
            return back()->withErrors(['msg' => 'Absensi ditiadakan pada hari Minggu (Libur).']);
        }

        $user = Auth::user();
        $today = Carbon::now()->toDateString();
        $now   = Carbon::now()->toTimeString();
        $type  = $request->input('tipe'); 

        try {
            if ($type == 'masuk') {
                DB::statement('CALL sp_absen_masuk(?, ?, ?, ?)', [
                    $user->id,
                    $today,
                    $now,
                    'INPUT_MANUAL' 
                ]);
                return redirect()->route('absensi.index', ['tab' => 'qr'])
                    ->with('success', 'Berhasil Absen Masuk pada ' . $now);
            } elseif ($type == 'pulang') {
                DB::statement('CALL sp_absen_pulang(?, ?, ?)', [
                    $user->id,
                    $today,
                    $now
                ]);
                return redirect()->route('absensi.index', ['tab' => 'qr'])
                    ->with('success', 'Berhasil Absen Pulang pada ' . $now);
            }
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            if (str_contains($msg, 'User sudah melakukan absen')) { $msg = 'Anda sudah absen masuk hari ini.'; }
            if (str_contains($msg, 'Gagal Pulang')) { $msg = 'Anda belum absen masuk hari ini.'; }
            if (str_contains($msg, 'sudah melakukan absen pulang')) { $msg = 'Anda sudah absen pulang sebelumnya.'; }

            return back()->withErrors(['msg' => 'Gagal: ' . $msg]);
        }
        return back();
    }

    // =========================================================================
    // FITUR KIOSK / TERMINAL SCANNER
    // =========================================================================

    public function kiosk()
    {
        return view('pages.absensi.kiosk');
    }

    public function processScan(Request $request)
    {
        $qrString = $request->input('qr_code');

        if (!$qrString) {
            return response()->json(['status' => 'error', 'message' => 'QR Code tidak terbaca.']);
        }

        $anggota = DB::table('Anggota')->where('string_kode_qr', $qrString)->first();

        if (!$anggota) {
            return response()->json(['status' => 'error', 'message' => 'QR Code tidak dikenali / User tidak ditemukan.']);
        }

        if ($anggota->status_aktif != 1) {
            return response()->json(['status' => 'error', 'message' => 'Akun anggota non-aktif.']);
        }

        $today = Carbon::now()->toDateString();
        $now   = Carbon::now()->toTimeString();

        $riwayat = DB::table('Riwayat_Absensi')
            ->where('id_anggota', $anggota->id)
            ->where('tanggal', $today)
            ->first();

        try {
            if (!$riwayat) {
                if (Carbon::now()->isSunday()) {
                    return response()->json(['status' => 'error', 'message' => 'Absensi Libur Hari Minggu.']);
                }

                DB::statement('CALL sp_absen_masuk(?, ?, ?, ?)', [
                    $anggota->id,
                    $today,
                    $now,
                    'QR_MESIN'
                ]);

                return response()->json([
                    'status' => 'success',
                    'type' => 'MASUK',
                    'nama' => $anggota->nama_lengkap,
                    'waktu' => $now,
                    'message' => 'Selamat Datang, ' . $anggota->nama_lengkap
                ]);
            }

            if ($riwayat && $riwayat->status_kehadiran == 'HADIR' && $riwayat->jam_pulang == null) {
                DB::statement('CALL sp_absen_pulang(?, ?, ?)', [
                    $anggota->id,
                    $today,
                    $now
                ]);

                return response()->json([
                    'status' => 'success',
                    'type' => 'PULANG',
                    'nama' => $anggota->nama_lengkap,
                    'waktu' => $now,
                    'message' => 'Hati-hati di jalan, ' . $anggota->nama_lengkap
                ]);
            }

            if ($riwayat && $riwayat->jam_pulang != null) {
                return response()->json(['status' => 'error', 'message' => 'Anda sudah absen pulang hari ini.']);
            }

            if ($riwayat && $riwayat->status_kehadiran != 'HADIR') {
                return response()->json(['status' => 'error', 'message' => 'Status Anda hari ini: ' . $riwayat->status_kehadiran]);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'System Error: ' . $e->getMessage()]);
        }
    }

    // =========================================================================
    // FITUR DINAS & IZIN (INPUT WEB)
    // =========================================================================

    public function storeDinas(Request $request)
    {
        if (Carbon::now()->isSunday()) {
            return back()->withErrors(['msg' => 'Input Dinas tidak dapat dilakukan pada hari Minggu.']);
        }

        $request->validate([
            'jenis_dinas' => 'required',
            'lokasi_tujuan' => 'required',
            'keterangan' => 'required',
            'bukti_foto' => 'required|image|mimes:jpeg,png,jpg|max:4096',
        ]);

        try {
            $user = Auth::user();
            $pathBukti = null;

            if ($request->hasFile('bukti_foto')) {
                $file = $request->file('bukti_foto');
                $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $pathBukti = $file->storeAs('private/bukti_dinas', $filename, 'local');
            }

            $keteranganLengkap = $request->jenis_dinas . ': ' . $request->lokasi_tujuan . ' (' . $request->keterangan . ')';

            DB::statement('CALL sp_absen_dinas(?, ?, ?, ?, ?, ?)', [
                $user->id,
                Carbon::now()->toDateString(),
                Carbon::now()->toTimeString(),
                Carbon::now()->addHours(8)->toTimeString(), 
                $keteranganLengkap,
                $pathBukti
            ]);

            return redirect()->route('absensi.index', ['tab' => 'dinas'])
                ->with('success', 'Laporan Dinas berhasil dikirim (Menunggu Approval).');
        } catch (\Exception $e) {
            return back()->withErrors(['msg' => 'System Error: ' . $e->getMessage()]);
        }
    }

    public function storeIzin(Request $request)
    {
        $request->validate([
            'kategori' => 'required',
            'mulai_tanggal' => 'required|date',
            'sampai_tanggal' => 'required|date|after_or_equal:mulai_tanggal',
            'alasan' => 'required',
            'bukti_izin' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        $user = Auth::user();
        $period = CarbonPeriod::create($request->mulai_tanggal, $request->sampai_tanggal);
        
        $status = ($request->kategori == 'Sakit') ? 'SAKIT' : 'IZIN';
        $keterangan = $request->kategori . ': ' . $request->alasan;

        $pathBukti = null;

        try {
            if ($request->hasFile('bukti_izin')) {
                $file = $request->file('bukti_izin');
                $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $pathBukti = $file->storeAs('private/bukti_izin', $filename, 'local');
            }

            DB::beginTransaction();

            foreach ($period as $date) {
                if ($date->isSunday()) {
                    continue;
                }

                DB::statement('CALL sp_absen_izin(?, ?, ?, ?, ?)', [
                    $user->id,
                    $date->toDateString(),
                    $status,
                    $keterangan,
                    $pathBukti
                ]);
            }

            DB::commit();
            return redirect()->route('absensi.index', ['tab' => 'izin'])
                ->with('success', 'Pengajuan berhasil disimpan (Menunggu Approval).');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['msg' => 'Gagal: ' . $e->getMessage()]);
        }
    }

    public function viewBukti($id)
    {
        $absen = DB::table('Riwayat_Absensi')->where('id', $id)->first();

        if (!$absen || empty($absen->url_bukti)) {
            abort(404, 'File tidak ditemukan.');
        }

        $isOwner = $absen->id_anggota == Auth::id();
        $isBPH   = session('user_level') == 'BPH';

        if (!$isOwner && !$isBPH) {
            abort(403, 'Akses ditolak.');
        }

        if (!Storage::disk('local')->exists($absen->url_bukti)) {
            if (Str::startsWith($absen->url_bukti, ['http', '/storage'])) {
                return redirect($absen->url_bukti);
            }
            abort(404, 'File fisik tidak ditemukan di server.');
        }

        return Storage::disk('local')->response($absen->url_bukti);
    }

    // =========================================================================
    // REKAP & EXPORT (ADMIN BPH)
    // =========================================================================

    public function rekap(Request $request)
    {
        $reqBulan = $request->input('bulan', Carbon::now()->month);
        $reqTahun = $request->input('tahun', Carbon::now()->year);
        $viewType = $request->input('view', 'matrix'); // 'matrix', 'incentive', 'monitoring'

        // Logic perhitungan ada di Service
        $data = $this->absensiService->getDataRekap($reqBulan, $reqTahun);

        // Tambahkan navigasi bulan
        $currentDate = Carbon::createFromDate($reqTahun, $reqBulan, 1);
        $data['prevDate'] = $currentDate->copy()->subMonth();
        $data['nextDate'] = $currentDate->copy()->addMonth();

        // --- [FIX] KIRIM VARIABEL currView KE VIEW ---
        $data['currView'] = $viewType;
        // ---------------------------------------------

        // Data tambahan untuk view 'monitoring'
        $data['monitoringHariIni'] = [];
        $data['historyIzinDinas'] = [];

        if ($viewType == 'monitoring') {
            $data['monitoringHariIni'] = DB::table('Riwayat_Absensi')
                ->join('Anggota', 'Riwayat_Absensi.id_anggota', '=', 'Anggota.id')
                ->join('Jabatan', 'Anggota.id_jabatan', '=', 'Jabatan.id')
                ->select('Riwayat_Absensi.*', 'Anggota.nama_lengkap', 'Jabatan.nama_jabatan')
                ->where('Riwayat_Absensi.tanggal', Carbon::now()->toDateString())
                ->whereIn('Riwayat_Absensi.status_kehadiran', ['DINAS', 'IZIN', 'SAKIT'])
                ->orderBy('Riwayat_Absensi.id', 'desc')
                ->get();

            $data['historyIzinDinas'] = DB::table('Riwayat_Absensi')
                ->join('Anggota', 'Riwayat_Absensi.id_anggota', '=', 'Anggota.id')
                ->select('Riwayat_Absensi.*', 'Anggota.nama_lengkap')
                ->whereIn('Riwayat_Absensi.status_kehadiran', ['DINAS', 'IZIN', 'SAKIT'])
                ->orderBy('Riwayat_Absensi.tanggal', 'desc')
                ->limit(20)
                ->get();
        }

        return view('pages.absensi.rekap', $data);
    }

    public function updateStatus(Request $request, $id)
    {
        if (session('user_level') !== 'BPH') {
            abort(403);
        }

        $request->validate(['status' => 'required|in:APPROVED,REJECTED']);

        try {
            DB::statement('CALL sp_update_status_absensi(?, ?, ?)', [
                $id,
                $request->status,
                Auth::id()
            ]);
            $msg = $request->status == 'APPROVED' ? 'Data disetujui.' : 'Data ditolak.';
            return back()->with('success', $msg);
        } catch (\Exception $e) {
            return back()->withErrors(['msg' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function downloadExcel(Request $request)
    {
        $reqBulan = $request->input('bulan', Carbon::now()->month);
        $reqTahun = $request->input('tahun', Carbon::now()->year);
        $type     = $request->input('type', 'absensi'); // Default absensi

        $data = $this->absensiService->getDataRekap($reqBulan, $reqTahun);
        
        // Logika Pemisahan Export
        if ($type === 'insentif') {
            $filename = 'Laporan_Insentif_' . $data['startDate']->format('F_Y') . '.xlsx';
            return Excel::download(new RekapInsentifExport($data), $filename);
        } else {
            // Default: Rekap Absensi Matrix
            $filename = 'Rekap_Absensi_' . $data['startDate']->format('F_Y') . '.xlsx';
            return Excel::download(new RekapAbsensiExport($data), $filename);
        }
    }

    public function downloadPdfRekap(Request $request)
    {
        $reqBulan = $request->input('bulan', Carbon::now()->month);
        $reqTahun = $request->input('tahun', Carbon::now()->year);
        $data = $this->absensiService->getDataRekap($reqBulan, $reqTahun);
        $pdf = Pdf::loadView('exports.pdf_rekap', $data)->setPaper('a4', 'landscape');
        return $pdf->download('Rekap_Absensi_' . $data['startDate']->format('F_Y') . '.pdf');
    }

    public function downloadPdfSlip($userId, Request $request)
    {
        $reqBulan = $request->input('bulan', Carbon::now()->month);
        $reqTahun = $request->input('tahun', Carbon::now()->year);
        $allData = $this->absensiService->getDataRekap($reqBulan, $reqTahun);

        if (!isset($allData['rekapInsentif'][$userId])) {
            return back()->withErrors(['msg' => 'Data user tidak ditemukan.']);
        }
        $slipData = $allData['rekapInsentif'][$userId];
        $periode  = $allData['startDate'];
        
        $pdf = Pdf::loadView('exports.pdf_slip', compact('slipData', 'periode', 'userId'))->setPaper('a5', 'landscape');
        return $pdf->download('Slip_Gaji_' . Str::slug($slipData['nama']) . '.pdf');
    }

    public function downloadIdCard()
    {
        $user = Auth::user();

        if (empty($user->string_kode_qr)) {
            return back()->withErrors(['msg' => 'QR Code belum digenerate.']);
        }

        $jabatan = DB::table('Jabatan')->where('id', $user->id_jabatan)->value('nama_jabatan');
        $divisi  = DB::table('Divisi')->where('id', $user->id_divisi)->value('nama_divisi');

        $data = [
            'user' => $user,
            'jabatan' => $jabatan,
            'divisi' => $divisi ?? '-'
        ];

        // Ukuran ID Card (85mm x 54mm) dalam points
        $pdf = Pdf::loadView('exports.pdf_id_card', $data)
            ->setPaper([0, 0, 240, 153], 'landscape'); 

        return $pdf->download('ID_Card_' . $user->username . '.pdf');
    }
}