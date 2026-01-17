<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
DROP PROCEDURE IF EXISTS `sp_anggota_update`;
CREATE PROCEDURE `sp_anggota_update` (
    IN `p_id` INT,
    IN `p_nama_lengkap` VARCHAR(150),
    IN `p_username` VARCHAR(50),
    IN `p_email` VARCHAR(100),
    IN `p_nomor_hp` VARCHAR(20),
    IN `p_id_divisi` INT,
    IN `p_id_jabatan` INT
)
BEGIN
    UPDATE Anggota
    SET 
        nama_lengkap = p_nama_lengkap,
        username = p_username,
        email = p_email,
        nomor_hp = p_nomor_hp,
        id_divisi = p_id_divisi,
        id_jabatan = p_id_jabatan,
        diupdate_pada = NOW()
    WHERE id = p_id;
END
SQL
        );
    }

    public function down(): void
    {
        // Logic rollback (jika ada)
        DB::unprepared(<<<'SQL'
DROP PROCEDURE IF EXISTS `sp_anggota_update`;
SQL
        );
    }
};
