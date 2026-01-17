<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Jabatan Dummy (Jika belum ada)
        $jabatanList = ['Anggota', 'Korlap', 'Staff IT', 'Humas', 'Advokat'];
        foreach($jabatanList as $j) {
            DB::table('Jabatan')->insertOrIgnore([
                'nama_jabatan' => $j,
                'level_akses' => 'ANGGOTA'
            ]);
        }

        // 2. Buat 15 Anggota Dummy
        $faker = \Faker\Factory::create('id_ID');
        $userIds = [];

        for ($i = 0; $i < 15; $i++) {
            $name = $faker->name;
            $username = strtolower(str_replace(' ', '', $name)) . rand(10,99);
            
            $id = DB::table('Anggota')->insertGetId([
                'nama_lengkap' => $name,
                'username' => $username,
                'password_hash' => Hash::make('password'), // Password default
                'id_jabatan' => rand(1, 5), // Asumsi ID jabatan 1-5 ada
                'status_aktif' => 1,
                'string_kode_qr' => $username . '-' . rand(1000,9999),
                'dibuat_pada' => now()
            ]);
            $userIds[] = $id;
        }

        // 3. Isi Absensi Bulan Ini (Untuk Matrix)
        $startDate = Carbon::now()->startOfMonth();
        $endDate   = Carbon::now(); // Sampai hari ini saja
        $period    = CarbonPeriod::create($startDate, $endDate);

        foreach ($period as $date) {
            // Skip Hari Minggu
            if ($date->isSunday()) continue;

            foreach ($userIds as $uid) {
                // Random Status: 80% Hadir, 5% Dinas, 5% Sakit, 10% Bolos (No Data)
                $rand = rand(1, 100);
                
                if ($rand <= 80) { // HADIR
                    // Random jam masuk 07:00 - 09:00
                    $jamMasuk = rand(7, 9) . ':' . rand(0, 59) . ':00';
                    // Random jam pulang 16:00 - 22:00 (Biar ada yg lembur)
                    $jamPulang = rand(16, 22) . ':' . rand(0, 59) . ':00';
                    
                    DB::table('Riwayat_Absensi')->insert([
                        'id_anggota' => $uid,
                        'tanggal' => $date->toDateString(),
                        'jam_masuk' => $jamMasuk,
                        'jam_pulang' => $jamPulang,
                        'status_kehadiran' => 'HADIR',
                        'sumber_absensi' => 'QR_DINDING'
                    ]);
                } 
                elseif ($rand <= 85) { // DINAS
                    DB::table('Riwayat_Absensi')->insert([
                        'id_anggota' => $uid,
                        'tanggal' => $date->toDateString(),
                        'jam_masuk' => '08:00:00',
                        'status_kehadiran' => 'DINAS',
                        'sumber_absensi' => 'HP_MOBILE',
                        'keterangan_tambahan' => 'Kunjungan PUK'
                    ]);
                }
                elseif ($rand <= 90) { // SAKIT
                    DB::table('Riwayat_Absensi')->insert([
                        'id_anggota' => $uid,
                        'tanggal' => $date->toDateString(),
                        'status_kehadiran' => 'SAKIT',
                        'sumber_absensi' => 'HP_MOBILE',
                        'keterangan_tambahan' => 'Demam'
                    ]);
                }
                // Sisanya Alpha (Tidak insert DB)
            }
        }
    }
}