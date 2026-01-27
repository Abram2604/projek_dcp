<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
DROP PROCEDURE IF EXISTS `sp_get_rekap_absensi`;
CREATE PROCEDURE `sp_get_rekap_absensi`(
    IN `p_start_date` DATE,
    IN `p_end_date` DATE
)
BEGIN
    SELECT 
        id_anggota, 
        tanggal, 
        jam_masuk, 
        jam_pulang, 
        status_kehadiran, 
        keterangan_tambahan 
    FROM Riwayat_Absensi
    WHERE tanggal BETWEEN p_start_date AND p_end_date;
END
SQL
        );
    }

    public function down(): void
    {
        // Logic rollback (jika ada)
        DB::unprepared(<<<'SQL'
DROP PROCEDURE IF EXISTS `sp_get_rekap_absensi`;
SQL
        );
    }
};
