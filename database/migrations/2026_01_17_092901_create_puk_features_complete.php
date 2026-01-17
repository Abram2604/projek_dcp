<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Buat Tabel Pengaturan Tanda Tangan
        Schema::create('Pengaturan_Tanda_Tangan', function (Blueprint $table) {
            $table->id();
            $table->string('kadis_nama', 150);
            $table->string('kadis_nip', 50);
            $table->string('ketua_nama', 150);
            $table->string('sekretaris_nama', 150);
            $table->string('kota_surat', 50)->default('Karawang');
            $table->timestamps();
        });

        // Seed Data Awal TTD (Sesuai PDF)
        DB::table('Pengaturan_Tanda_Tangan')->insert([
            'kadis_nama' => 'ROSMALIA DEWI, S.H., M.H.',
            'kadis_nip' => '19691112 199603 2 002',
            'ketua_nama' => 'ABAS PURNAMA, S.E., M.M.',
            'sekretaris_nama' => 'EKO SUSANTO, S.H.',
            'kota_surat' => 'Karawang'
        ]);

        // 2. SP: Ambil Detail PUK (Untuk Edit)
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_puk_detail");
        DB::unprepared("
            CREATE PROCEDURE sp_puk_detail (IN p_id INT)
            BEGIN
                SELECT * FROM Data_PUK WHERE id = p_id;
            END
        ");

        // 3. SP: Update PUK
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_puk_update");
        DB::unprepared("
            CREATE PROCEDURE sp_puk_update (
                IN p_id INT,
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
                UPDATE Data_PUK SET
                    nama_perusahaan = p_nama_pt,
                    no_pencatatan = p_no_pencatatan,
                    jumlah_anggota = p_jml_anggota,
                    hasil_verifikasi = p_verifikasi,
                    nama_federasi = p_federasi,
                    no_pencatatan_federasi = p_no_federasi,
                    afiliasi = p_afiliasi,
                    nama_ketua = p_ketua,
                    nama_sekretaris = p_sekretaris,
                    diupdate_pada = NOW()
                WHERE id = p_id;
            END
        ");
    }

    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_puk_detail");
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_puk_update");
        Schema::dropIfExists('Pengaturan_Tanda_Tangan');
    }
};