<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // =================================================================
        // 1. SP UNTUK NOTIFIKASI ABSENSI (IZIN/DINAS) -> KE KETUA & SEKRETARIS
        // =================================================================
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_notifikasi_pengajuan_absensi");
        
        $procAbsensi = <<<SQL
        CREATE PROCEDURE sp_notifikasi_pengajuan_absensi (
            IN p_id_pengaju INT,
            IN p_tipe_pengajuan VARCHAR(50), -- CONTOH: 'DINAS_LUAR', 'SAKIT'
            IN p_tanggal DATE
        )
        BEGIN
            DECLARE v_nama_pengaju VARCHAR(150);
            DECLARE v_judul VARCHAR(100);
            DECLARE v_pesan TEXT;
            DECLARE v_link VARCHAR(255);

            -- Ambil nama pengaju
            SELECT nama_lengkap INTO v_nama_pengaju FROM Anggota WHERE id = p_id_pengaju;

            -- Set Judul & Pesan
            SET v_judul = CONCAT('Pengajuan ', REPLACE(p_tipe_pengajuan, '_', ' '));
            SET v_pesan = CONCAT(v_nama_pengaju, ' mengajukan permohonan untuk tanggal ', p_tanggal, '. Menunggu persetujuan.');
            SET v_link = '/absensi/persetujuan'; -- Link halaman approval di web

            -- INSERT KE NOTIFIKASI (Target: Ketua DPC & Sekretaris)
            INSERT INTO Notifikasi (id_anggota, judul, pesan, tipe, link_url, is_read, dibuat_pada)
            SELECT 
                a.id, 
                v_judul, 
                v_pesan, 
                'alert', 
                v_link, 
                0, 
                NOW()
            FROM Anggota a
            JOIN Jabatan j ON a.id_jabatan = j.id
            WHERE j.nama_jabatan IN ('Ketua DPC', 'Sekretaris') 
              AND a.status_aktif = 1;
        END
        SQL;
        DB::unprepared($procAbsensi);


        // =================================================================
        // 2. SP UNTUK NOTIFIKASI SALDO KEUANGAN -> KE SEMUA ANGGOTA DIVISI
        // =================================================================
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_notifikasi_saldo_divisi");
        
        $procKeuangan = <<<SQL
        CREATE PROCEDURE sp_notifikasi_saldo_divisi (
            IN p_id_divisi INT,
            IN p_bulan TINYINT,
            IN p_tahun SMALLINT,
            IN p_jumlah DECIMAL(15,2)
        )
        BEGIN
            DECLARE v_nama_divisi VARCHAR(100);
            DECLARE v_judul VARCHAR(100);
            DECLARE v_pesan TEXT;
            DECLARE v_jumlah_fmt VARCHAR(50);

            -- Ambil nama divisi
            SELECT nama_divisi INTO v_nama_divisi FROM Divisi WHERE id = p_id_divisi;
            
            -- Format Rupiah sederhana (opsional, biar rapi di notif)
            SET v_jumlah_fmt = CONCAT('Rp ', FORMAT(p_jumlah, 0));

            SET v_judul = 'Anggaran Baru Ditetapkan';
            SET v_pesan = CONCAT('Saldo awal ', v_nama_divisi, ' untuk periode ', p_bulan, '-', p_tahun, ' sebesar ', v_jumlah_fmt, ' telah ditambahkan.');

            -- INSERT KE SEMUA ANGGOTA DI DIVISI TERSEBUT
            INSERT INTO Notifikasi (id_anggota, judul, pesan, tipe, link_url, is_read, dibuat_pada)
            SELECT 
                id, 
                v_judul, 
                v_pesan, 
                'success', 
                '/keuangan', 
                0, 
                NOW()
            FROM Anggota
            WHERE id_divisi = p_id_divisi 
              AND status_aktif = 1;
        END
        SQL;
        DB::unprepared($procKeuangan);


        // =================================================================
        // 3. SP UNTUK MENAMPILKAN LIST NOTIF DI NAVBAR (PER USER)
        // =================================================================
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_notifikasi_list");
        
        $procList = <<<SQL
        CREATE PROCEDURE sp_notifikasi_list (
            IN p_id_anggota INT,
            IN p_limit INT
        )
        BEGIN
            SELECT 
                id, 
                judul, 
                pesan, 
                tipe, -- info, success, warning, alert
                link_url, 
                is_read, 
                dibuat_pada
            FROM Notifikasi
            WHERE id_anggota = p_id_anggota
            ORDER BY dibuat_pada DESC
            LIMIT p_limit;
        END
        SQL;
        DB::unprepared($procList);

        
        // =================================================================
        // 4. SP UNTUK TANDAI SUDAH DIBACA (MARK AS READ)
        // =================================================================
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_notifikasi_mark_read");
        
        $procRead = <<<SQL
        CREATE PROCEDURE sp_notifikasi_mark_read (
            IN p_id_notifikasi INT
        )
        BEGIN
            UPDATE Notifikasi 
            SET is_read = 1, diupdate_pada = NOW() 
            WHERE id = p_id_notifikasi;
        END
        SQL;
        DB::unprepared($procRead);
    }

    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_notifikasi_pengajuan_absensi");
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_notifikasi_saldo_divisi");
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_notifikasi_list");
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_notifikasi_mark_read");
    }
};