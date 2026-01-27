<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
DROP PROCEDURE IF EXISTS `sp_absen_izin`;
CREATE PROCEDURE `sp_absen_izin`(
    IN `p_id_anggota` INT,
    IN `p_tanggal` DATE,
    IN `p_status` VARCHAR(20), -- 'SAKIT' atau 'IZIN'
    IN `p_keterangan` VARCHAR(255)
)
BEGIN
    DELETE FROM Riwayat_Absensi 
    WHERE id_anggota = p_id_anggota AND tanggal = p_tanggal;

    INSERT INTO Riwayat_Absensi 
    (id_anggota, tanggal, status_kehadiran, sumber_absensi, keterangan_tambahan, dibuat_pada)
    VALUES 
    (p_id_anggota, p_tanggal, p_status, 'HP_MOBILE', p_keterangan, NOW());
END
SQL
        );
    }

    public function down(): void
    {
        // Logic rollback (jika ada)
        DB::unprepared(<<<'SQL'
DROP PROCEDURE IF EXISTS `sp_absen_izin`;
SQL
        );
    }
};
