<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
DROP PROCEDURE IF EXISTS `sp_absen_dinas`;
CREATE PROCEDURE `sp_absen_dinas`(
    IN `p_id_anggota` INT,
    IN `p_tanggal` DATE,
    IN `p_jam_input` TIME,
    IN `p_jam_pulang_estimasi` TIME,
    IN `p_keterangan` VARCHAR(255)
)
BEGIN
    -- Hapus data eksisting (misal sudah absen masuk, tapi ternyata dinas)
    DELETE FROM Riwayat_Absensi 
    WHERE id_anggota = p_id_anggota AND tanggal = p_tanggal;

    -- Insert Data Dinas
    INSERT INTO Riwayat_Absensi 
    (id_anggota, tanggal, jam_masuk, jam_pulang, status_kehadiran, sumber_absensi, keterangan_tambahan, dibuat_pada)
    VALUES 
    (p_id_anggota, p_tanggal, p_jam_input, p_jam_pulang_estimasi, 'DINAS', 'HP_MOBILE', p_keterangan, NOW());
END
SQL
        );
    }

    public function down(): void
    {
        // Logic rollback (jika ada)
        DB::unprepared(<<<'SQL'
DROP PROCEDURE IF EXISTS `sp_absen_dinas`;
SQL
        );
    }
};
