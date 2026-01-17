<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah Kolom ke Tabel
        Schema::table('Data_PUK', function (Blueprint $table) {
            $table->integer('manual_total_anggota')->nullable()->after('hasil_verifikasi');
        });

        // 2. Update SP Create (Tambah parameter p_total_manual)
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
                IN p_sekretaris VARCHAR(150),
                IN p_total_manual INT -- Parameter Baru
            )
            BEGIN
                INSERT INTO Data_PUK (
                    nama_perusahaan, no_pencatatan, jumlah_anggota, hasil_verifikasi,
                    nama_federasi, no_pencatatan_federasi, afiliasi,
                    nama_ketua, nama_sekretaris, manual_total_anggota, dibuat_pada
                ) VALUES (
                    p_nama_pt, p_no_pencatatan, p_jml_anggota, p_verifikasi,
                    p_federasi, p_no_federasi, p_afiliasi,
                    p_ketua, p_sekretaris, p_total_manual, NOW()
                );
            END
        ");

        // 3. Update SP Update (Tambah parameter p_total_manual)
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
                IN p_sekretaris VARCHAR(150),
                IN p_total_manual INT -- Parameter Baru
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
                    manual_total_anggota = p_total_manual,
                    diupdate_pada = NOW()
                WHERE id = p_id;
            END
        ");
    }

    public function down(): void
    {
        Schema::table('Data_PUK', function (Blueprint $table) {
            $table->dropColumn('manual_total_anggota');
        });
        // Note: Rollback SP ke versi lama tidak disertakan untuk ringkas, 
        // tapi kolom akan dihapus.
    }
};