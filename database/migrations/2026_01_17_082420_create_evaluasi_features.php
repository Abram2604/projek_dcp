<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Buat Tabel Evaluasi
        Schema::create('Evaluasi_Divisi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_divisi'); // Divisi yang dievaluasi
            $table->unsignedBigInteger('dibuat_oleh'); // BPH yang nulis (Ketua/Sekre)
            $table->text('isi_evaluasi');
            $table->date('tanggal_evaluasi');
            $table->dateTime('dibuat_pada')->useCurrent();

            $table->foreign('id_divisi')->references('id')->on('Divisi')->onDelete('cascade');
            $table->foreign('dibuat_oleh')->references('id')->on('Anggota');
        });

        // 2. SP: Tambah Evaluasi (Khusus BPH)
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_evaluasi_create");
        DB::unprepared("
            CREATE PROCEDURE sp_evaluasi_create (
                IN p_id_divisi INT,
                IN p_user_id INT,
                IN p_isi TEXT,
                IN p_tanggal DATE
            )
            BEGIN
                INSERT INTO Evaluasi_Divisi (id_divisi, dibuat_oleh, isi_evaluasi, tanggal_evaluasi)
                VALUES (p_id_divisi, p_user_id, p_isi, p_tanggal);
            END
        ");

        // 3. SP: Ambil Evaluasi Terakhir (Untuk Ditampilkan di Card)
        // Logika: Ambil 1 evaluasi paling baru berdasarkan tanggal
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_evaluasi_get_latest");
        DB::unprepared("
            CREATE PROCEDURE sp_evaluasi_get_latest (IN p_id_divisi INT)
            BEGIN
                SELECT 
                    e.*,
                    a.nama_lengkap as nama_penulis,
                    j.nama_jabatan as jabatan_penulis
                FROM Evaluasi_Divisi e
                JOIN Anggota a ON e.dibuat_oleh = a.id
                JOIN Jabatan j ON a.id_jabatan = j.id
                WHERE (p_id_divisi IS NULL OR e.id_divisi = p_id_divisi)
                ORDER BY e.tanggal_evaluasi DESC, e.id DESC
                LIMIT 1;
            END
        ");
    }

    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_evaluasi_create");
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_evaluasi_get_latest");
        Schema::dropIfExists('Evaluasi_Divisi');
    }
};