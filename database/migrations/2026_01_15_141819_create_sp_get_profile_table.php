<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Hapus SP lama jika ada (untuk menghindari error 'already exists')
        DB::unprepared("DROP PROCEDURE IF EXISTS `sp_get_profile`");

        // 2. Buat SP Baru
        DB::unprepared("
CREATE PROCEDURE `sp_get_profile` (
    IN p_id_anggota INT
)
BEGIN
    SELECT 
        a.id,
        a.nama_lengkap,
        a.username,
        a.email,
        a.nomor_hp,
        a.foto_profil,
        a.status_aktif,
        a.dibuat_pada, -- Untuk 'Bergabung Sejak'
        COALESCE(j.nama_jabatan, 'Anggota') AS nama_jabatan,
        COALESCE(d.nama_divisi, '-') AS nama_divisi,
        j.level_akses
    FROM Anggota a
    LEFT JOIN Jabatan j ON a.id_jabatan = j.id
    LEFT JOIN Divisi d ON a.id_divisi = d.id
    WHERE a.id = p_id_anggota
    LIMIT 1;
END
        ");
    }

    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS `sp_get_profile`");
    }
};