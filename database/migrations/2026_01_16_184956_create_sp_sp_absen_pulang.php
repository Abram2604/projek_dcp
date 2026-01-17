<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
DROP PROCEDURE IF EXISTS `sp_absen_pulang`;
CREATE PROCEDURE `sp_absen_pulang`(
    IN `p_id_anggota` INT,
    IN `p_tanggal` DATE,
    IN `p_jam_pulang` TIME
)
BEGIN
    DECLARE v_id INT;
    DECLARE v_jam_pulang TIME;

    -- Ambil ID record hari ini
    SELECT id, jam_pulang INTO v_id, v_jam_pulang
    FROM Riwayat_Absensi 
    WHERE id_anggota = p_id_anggota AND tanggal = p_tanggal
    LIMIT 1;

    IF v_id IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Gagal Pulang: Anda belum melakukan absen masuk.';
    ELSEIF v_jam_pulang IS NOT NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Anda sudah melakukan absen pulang sebelumnya.';
    ELSE
        UPDATE Riwayat_Absensi 
        SET jam_pulang = p_jam_pulang 
        WHERE id = v_id;
    END IF;
END
SQL
        );
    }

    public function down(): void
    {
        // Logic rollback (jika ada)
        DB::unprepared(<<<'SQL'
DROP PROCEDURE IF EXISTS `sp_absen_pulang`;
SQL
        );
    }
};
