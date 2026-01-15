<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Hapus SP lama jika ada (untuk menghindari error 'already exists')
        DB::unprepared("DROP PROCEDURE IF EXISTS `sp_anggota_list`");

        // 2. Buat SP Baru
        DB::unprepared("
CREATE PROCEDURE `sp_anggota_list` (
    IN `p_search` VARCHAR(100),
    IN `p_status_aktif` TINYINT
)
BEGIN
    SELECT 
        a.id,
        a.nama_lengkap,
        a.username,
        a.email,
        a.nomor_hp,
        a.string_kode_qr,
        a.status_aktif,
        j.nama_jabatan,
        d.nama_divisi,
        j.level_akses
    FROM Anggota a
    LEFT JOIN Jabatan j ON a.id_jabatan = j.id
    LEFT JOIN Divisi d ON a.id_divisi = d.id
    WHERE 
        (p_status_aktif IS NULL OR a.status_aktif = p_status_aktif)
        AND
        (p_search IS NULL OR p_search = '' OR 
         a.nama_lengkap LIKE CONCAT('%', p_search, '%') OR 
         a.username LIKE CONCAT('%', p_search, '%'))
    ORDER BY a.nama_lengkap ASC;
END
        ");
    }

    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS `sp_anggota_list`");
    }
};
