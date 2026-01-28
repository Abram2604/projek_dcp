<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. TAMBAH KOLOM STATUS (Soft Delete Flag)
        // Default 1 = Aktif, 0 = Terhapus/Non-Aktif
        
        if (!Schema::hasColumn('Data_PUK', 'status_aktif')) {
            Schema::table('Data_PUK', function (Blueprint $table) {
                $table->tinyInteger('status_aktif')->default(1)->after('manual_total_anggota');
            });
        }

        if (!Schema::hasColumn('Program_Kerja', 'status_aktif')) {
            Schema::table('Program_Kerja', function (Blueprint $table) {
                $table->tinyInteger('status_aktif')->default(1)->after('persen_progress');
            });
        }

        // 2. UPDATE SP PUK (Data Anggota)
        
        // A. Update List agar hanya muncul yang AKTIF
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_puk_list");
        DB::unprepared("
            CREATE PROCEDURE sp_puk_list (IN p_search VARCHAR(200))
            BEGIN
                SELECT * FROM Data_PUK
                WHERE status_aktif = 1  -- Filter Aktif
                AND (
                   p_search IS NULL OR p_search = '' 
                   OR nama_perusahaan LIKE CONCAT('%', p_search, '%')
                   OR nama_ketua LIKE CONCAT('%', p_search, '%')
                )
                ORDER BY nama_perusahaan ASC;
            END
        ");

        // B. Update Delete agar jadi SOFT DELETE (Update Status)
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_puk_delete");
        DB::unprepared("
            CREATE PROCEDURE sp_puk_delete(IN p_id INT) 
            BEGIN 
                UPDATE Data_PUK 
                SET status_aktif = 0, diupdate_pada = NOW() 
                WHERE id = p_id; 
            END
        ");


        // 3. UPDATE SP PROGJA (Program Kerja)

        // A. Update List agar hanya muncul yang AKTIF
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
                WHERE pk.status_aktif = 1 -- Filter Aktif
                AND (p_id_divisi IS NULL OR pk.id_divisi = p_id_divisi)
                AND (p_search IS NULL OR pk.nama_program LIKE CONCAT('%', p_search, '%'))
                ORDER BY pk.dibuat_pada DESC;
            END
        ");

        // B. Update Statistik agar yang dihapus TIDAK TERHITUNG
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
                WHERE status_aktif = 1 -- Filter Aktif
                AND (p_id_divisi IS NULL OR id_divisi = p_id_divisi);
            END
        ");

        // C. Update Delete agar jadi SOFT DELETE
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_progja_delete");
        DB::unprepared("
            CREATE PROCEDURE sp_progja_delete (IN p_id INT)
            BEGIN
                UPDATE Program_Kerja 
                SET status_aktif = 0 
                WHERE id = p_id;
            END
        ");
    }

    public function down(): void
    {
        // Rollback: Hapus kolom status
        if (Schema::hasColumn('Data_PUK', 'status_aktif')) {
            Schema::table('Data_PUK', function (Blueprint $table) {
                $table->dropColumn('status_aktif');
            });
        }
        if (Schema::hasColumn('Program_Kerja', 'status_aktif')) {
            Schema::table('Program_Kerja', function (Blueprint $table) {
                $table->dropColumn('status_aktif');
            });
        }
        
        // Note: SP Rollback tidak disertakan demi ringkasnya kode, 
        // tapi kolom akan dihapus jika migrate:rollback
    }
};