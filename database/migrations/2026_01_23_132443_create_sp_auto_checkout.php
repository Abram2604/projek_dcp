<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS `sp_auto_checkout_harian`");

        DB::unprepared("
            CREATE PROCEDURE `sp_auto_checkout_harian`()
            BEGIN
                -- Update data hari ini yang statusnya HADIR tapi jam_pulang NULL
                UPDATE Riwayat_Absensi
                SET 
                    jam_pulang = '17:00:00', -- Set jam pulang default (Jam pulang kantor)
                    keterangan_tambahan = TRIM(CONCAT(COALESCE(keterangan_tambahan, ''), ' [System: Lupa Absen Pulang]')),
                    diupdate_pada = NOW()
                WHERE 
                    tanggal = CURDATE() 
                    AND status_kehadiran = 'HADIR' 
                    AND jam_pulang IS NULL;
            END
        ");
    }

    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS `sp_auto_checkout_harian`");
    }
};