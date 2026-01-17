<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ==========================================
        // 1. SP: sp_progja_list
        // ==========================================
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_progja_list");
        DB::unprepared("
            CREATE PROCEDURE sp_progja_list (
                IN p_id_divisi INT, 
                IN p_search VARCHAR(100)
            )
            BEGIN
                SELECT 
                    pk.*,
                    d.nama_divisi
                FROM Program_Kerja pk
                JOIN Divisi d ON pk.id_divisi = d.id
                WHERE (p_id_divisi IS NULL OR pk.id_divisi = p_id_divisi)
                AND (p_search IS NULL OR pk.nama_program LIKE CONCAT('%', p_search, '%'))
                ORDER BY pk.dibuat_pada DESC;
            END
        ");

        // ==========================================
        // 2. SP: sp_progja_stats
        // ==========================================
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_progja_stats");
        DB::unprepared("
            CREATE PROCEDURE sp_progja_stats (IN p_id_divisi INT)
            BEGIN
                SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status_proker = 'SELESAI' THEN 1 ELSE 0 END) as selesai,
                    SUM(CASE WHEN status_proker = 'BERJALAN' THEN 1 ELSE 0 END) as berjalan,
                    SUM(CASE WHEN status_proker = 'TERKENDALA' THEN 1 ELSE 0 END) as terkendala,
                    SUM(CASE WHEN status_proker = 'RENCANA' THEN 1 ELSE 0 END) as rencana
                FROM Program_Kerja
                WHERE (p_id_divisi IS NULL OR id_divisi = p_id_divisi);
            END
        ");

        // ==========================================
        // 3. SP: sp_progja_create
        // ==========================================
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_progja_create");
        DB::unprepared("
            CREATE PROCEDURE sp_progja_create (
                IN p_id_divisi INT,
                IN p_nama VARCHAR(200),
                IN p_target DATE,
                IN p_anggaran DECIMAL(15,2),
                IN p_user_id INT
            )
            BEGIN
                INSERT INTO Program_Kerja (
                    id_divisi, nama_program, tanggal_selesai, anggaran_rencana, 
                    status_proker, persen_progress, dibuat_oleh, dibuat_pada
                ) VALUES (
                    p_id_divisi, p_nama, p_target, p_anggaran, 
                    'RENCANA', 0, p_user_id, NOW()
                );
            END
        ");

        // ==========================================
        // 4. SP: sp_progja_update_progress
        // ==========================================
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_progja_update_progress");
        DB::unprepared("
            CREATE PROCEDURE sp_progja_update_progress (
                IN p_id INT,
                IN p_progress TINYINT,
                IN p_status VARCHAR(20)
            )
            BEGIN
                UPDATE Program_Kerja 
                SET persen_progress = p_progress, 
                    status_proker = p_status
                WHERE id = p_id;
            END
        ");

        // ==========================================
        // 5. SP: sp_progja_delete
        // ==========================================
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_progja_delete");
        DB::unprepared("
            CREATE PROCEDURE sp_progja_delete (IN p_id INT)
            BEGIN
                DELETE FROM Program_Kerja WHERE id = p_id;
            END
        ");
    }

    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_progja_list");
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_progja_stats");
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_progja_create");
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_progja_update_progress");
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_progja_delete");
    }
};