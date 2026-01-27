<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Str;

class AbsensiService
{
    private function generateInitials($name)
    {
        $words = explode(' ', $name);
        $initials = '';
        foreach ($words as $w) {
            $initials .= strtoupper(substr($w, 0, 1));
        }
        return substr($initials, 0, 3);
    }

    public function getDataRekap($reqBulan, $reqTahun)
    {
        $currentDate = Carbon::createFromDate($reqTahun, $reqBulan, 1);
        $startDate = $currentDate->copy()->startOfMonth();
        $endDate   = $currentDate->copy()->endOfMonth();

        // 1. Generate Tanggal
        $period = CarbonPeriod::create($startDate, $endDate);
        $dates = [];
        foreach ($period as $date) {
            $dates[] = $date;
        }

        // 2. Ambil Anggota
        $anggotaRaw = DB::table('Anggota')
            ->join('Jabatan', 'Anggota.id_jabatan', '=', 'Jabatan.id')
            ->select('Anggota.id', 'Anggota.nama_lengkap', 'Jabatan.nama_jabatan')
            ->where('Anggota.status_aktif', 1)
            ->orderBy('Anggota.id', 'asc')
            ->get();

        $anggota = [];
        $rekapInsentif = [];

        // Tarif (Configurable)
        $RATE_HADIR = 100000;
        $RATE_LEMBUR = 50000;
        $RATE_DINAS_LUAR = 150000; // Flat
        $RATE_DINAS_MENGINAP = 300000; // Flat

        foreach ($anggotaRaw as $a) {
            $anggota[] = [
                'id' => $a->id,
                'nama' => $a->nama_lengkap,
                'jabatan' => $a->nama_jabatan,
                'inisial' => $this->generateInitials($a->nama_lengkap)
            ];

            // Struktur Data Keuangan Lengkap
            $rekapInsentif[$a->id] = [
                'nama' => $a->nama_lengkap,
                'jabatan' => $a->nama_jabatan,
                
                // 1. Gaji Harian (Pokok)
                'jml_hadir' => 0, 
                'nominal_hadir' => 0,

                // 2. Lembur (Tugas Luar Jam Kerja)
                'jml_lembur' => 0, 
                'nominal_lembur' => 0,

                // 3. Dinas Luar Kota
                'jml_dl' => 0,
                'nominal_dl' => 0,

                // 4. Dinas Menginap
                'jml_dm' => 0,
                'nominal_dm' => 0,

                // Total
                'total_terima' => 0
            ];
        }

        // 3. Ambil Data Absensi
        $absensiRaw = DB::table('Riwayat_Absensi')
            ->whereBetween('tanggal', [$startDate->toDateString(), $endDate->toDateString()])
            ->get();

        $matrixData = [];

        foreach ($absensiRaw as $absen) {
            $dateKey = $absen->tanggal;
            $userId  = $absen->id_anggota;
            
            // Validasi: Hanya hitung yang sudah APPROVED
            if ($absen->status_validasi != 'APPROVED') continue;

            $kodeStatus = '-';

            // === LOGIKA PERHITUNGAN ===
            
            if ($absen->status_kehadiran == 'HADIR') {
                // A. KEHADIRAN BIASA
                $rekapInsentif[$userId]['jml_hadir']++;
                $rekapInsentif[$userId]['nominal_hadir'] += $RATE_HADIR;
                $kodeStatus = 'H';

                // B. CEK LEMBUR (Pulang > 21:00)
                if ($absen->jam_pulang && $absen->jam_pulang >= '21:00:00') {
                    $rekapInsentif[$userId]['jml_lembur']++;
                    $rekapInsentif[$userId]['nominal_lembur'] += $RATE_LEMBUR;
                    $kodeStatus = 'O'; // Overtime
                }

            } elseif ($absen->status_kehadiran == 'DINAS') {
                // Cek Jenis Dinas dari Keterangan (String Matching)
                // Format di DB biasanya: "Dinas Menginap: Jakarta (Rapat...)"
                $ket = strtolower($absen->keterangan_tambahan);

                if (str_contains($ket, 'menginap')) {
                    // C. DINAS MENGINAP (Flat 300rb)
                    $rekapInsentif[$userId]['jml_dm']++;
                    $rekapInsentif[$userId]['nominal_dm'] += $RATE_DINAS_MENGINAP;
                    $kodeStatus = 'DM';
                } else {
                    // D. DINAS LUAR KOTA BIASA (Flat 150rb)
                    $rekapInsentif[$userId]['jml_dl']++;
                    $rekapInsentif[$userId]['nominal_dl'] += $RATE_DINAS_LUAR;
                    $kodeStatus = 'DL';
                }

            } elseif ($absen->status_kehadiran == 'SAKIT') {
                $kodeStatus = 'S';
            } elseif ($absen->status_kehadiran == 'IZIN') {
                $kodeStatus = 'I';
            }

            // Simpan Kode untuk Matrix Tampilan
            $matrixData[$dateKey][$userId] = $kodeStatus;
        }

        // 4. Hitung Grand Total
        foreach ($rekapInsentif as $uid => $val) {
            $rekapInsentif[$uid]['total_terima'] = 
                $val['nominal_hadir'] + 
                $val['nominal_lembur'] + 
                $val['nominal_dl'] + 
                $val['nominal_dm'];
        }

        $hariLibur = [
            $reqTahun . '-12-25' => 'NATAL',
            $reqTahun . '-01-01' => 'TAHUN BARU',
            $reqTahun . '-08-17' => 'HUT RI',
            $reqTahun . '-05-01' => 'BURUH',
        ];

        return compact('dates', 'anggota', 'matrixData', 'rekapInsentif', 'startDate', 'hariLibur');
    }
}