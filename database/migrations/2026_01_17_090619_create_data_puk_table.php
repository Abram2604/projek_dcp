<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Buat Tabel Data_PUK
        Schema::create('Data_PUK', function (Blueprint $table) {
            $table->id();
            $table->string('nama_perusahaan', 200);
            $table->string('no_pencatatan', 100)->nullable();
            $table->integer('jumlah_anggota')->default(0);
            $table->integer('hasil_verifikasi')->default(0);
            
            // Kolom Federasi
            $table->string('nama_federasi', 200)->nullable();
            $table->string('no_pencatatan_federasi', 100)->nullable();
            $table->string('afiliasi', 50)->default('KSPSI');
            
            // Pengurus PUK
            $table->string('nama_ketua', 150)->nullable();
            $table->string('nama_sekretaris', 150)->nullable();
            
            $table->dateTime('dibuat_pada')->useCurrent();
            $table->dateTime('diupdate_pada')->nullable();
        });

        // 2. SP: Ambil List PUK (Dengan Filter Pencarian)
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_puk_list");
        DB::unprepared("
            CREATE PROCEDURE sp_puk_list (IN p_search VARCHAR(200))
            BEGIN
                SELECT * FROM Data_PUK
                WHERE p_search IS NULL OR p_search = '' 
                   OR nama_perusahaan LIKE CONCAT('%', p_search, '%')
                   OR nama_ketua LIKE CONCAT('%', p_search, '%')
                ORDER BY nama_perusahaan ASC;
            END
        ");

        // 3. SP: Tambah PUK
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_puk_create");
        DB::unprepared("
            CREATE PROCEDURE sp_puk_create (
                IN p_nama_pt VARCHAR(200),
                IN p_no_pencatatan VARCHAR(100),
                IN p_jml_anggota INT,
                IN p_verifikasi INT,
                IN p_federasi VARCHAR(200),
                IN p_no_federasi VARCHAR(100),
                IN p_afiliasi VARCHAR(50),
                IN p_ketua VARCHAR(150),
                IN p_sekretaris VARCHAR(150)
            )
            BEGIN
                INSERT INTO Data_PUK (
                    nama_perusahaan, no_pencatatan, jumlah_anggota, hasil_verifikasi,
                    nama_federasi, no_pencatatan_federasi, afiliasi,
                    nama_ketua, nama_sekretaris, dibuat_pada
                ) VALUES (
                    p_nama_pt, p_no_pencatatan, p_jml_anggota, p_verifikasi,
                    p_federasi, p_no_federasi, p_afiliasi,
                    p_ketua, p_sekretaris, NOW()
                );
            END
        ");
        
        // 4. SP: Hapus PUK
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_puk_delete");
        DB::unprepared("CREATE PROCEDURE sp_puk_delete(IN p_id INT) BEGIN DELETE FROM Data_PUK WHERE id = p_id; END");
    }

    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_puk_list");
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_puk_create");
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_puk_delete");
        Schema::dropIfExists('Data_PUK');
    }
};