<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
DROP PROCEDURE IF EXISTS `sp_absen_masuk`;
CREATE PROCEDURE `sp_absen_masuk`(
    IN `p_id_anggota` INT,
    IN `p_tanggal` DATE,
    IN `p_jam_masuk` TIME,
    IN `p_sumber` VARCHAR(20)
)
BEGIN
    DECLARE v_exists INT;

    -- Cek apakah sudah ada data hari ini
    SELECT COUNT(*) INTO v_exists 
    FROM Riwayat_Absensi 
    WHERE id_anggota = p_id_anggota AND tanggal = p_tanggal;

    IF v_exists > 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'User sudah melakukan absen masuk hari ini.';
    ELSE
        INSERT INTO Riwayat_Absensi 
        (id_anggota, tanggal, jam_masuk, status_kehadiran, sumber_absensi, dibuat_pada)
        VALUES 
        (p_id_anggota, p_tanggal, p_jam_masuk, 'HADIR', p_sumber, NOW());
    END IF;
END
SQL
        );
    }

    public function down(): void
    {
        // Logic rollback (jika ada)
        DB::unprepared(<<<'SQL'
DROP PROCEDURE IF EXISTS `sp_absen_masuk`;
SQL
        );
    }
};
