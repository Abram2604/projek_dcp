<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_progja_create");
        
        DB::unprepared("
            CREATE PROCEDURE sp_progja_create (
                IN p_id_divisi INT,
                IN p_nama VARCHAR(200),
                IN p_target TEXT,        -- Parameter Baru
                IN p_action TEXT,        -- Parameter Baru
                IN p_target_date DATE,
                IN p_anggaran DECIMAL(15,2),
                IN p_user_id INT
            )
            BEGIN
                INSERT INTO Program_Kerja (
                    id_divisi, nama_program, target, action, tanggal_selesai, anggaran_rencana, 
                    status_proker, persen_progress, dibuat_oleh, dibuat_pada, status_aktif
                ) VALUES (
                    p_id_divisi, p_nama, p_target, p_action, p_target_date, p_anggaran, 
                    'RENCANA', 0, p_user_id, NOW(), 1
                );
            END
        ");
    }

    public function down(): void
    {
        // Kembalikan ke versi lama jika rollback (opsional)
    }
};