<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
DROP PROCEDURE IF EXISTS `Sp_Notifikasi`;
-- 1. SP NOTIFIKASI QR CODE (Absensi Harian)
-- Mengirim notif sukses ke User & Info ke Ketua/Sekretaris
DROP PROCEDURE IF EXISTS sp_notif_qr_harian;
CREATE PROCEDURE sp_notif_qr_harian (
    IN p_id_anggota INT,
    IN p_waktu DATETIME,
    IN p_status VARCHAR(20)
)
BEGIN
    DECLARE v_nama VARCHAR(150);
    SELECT nama_lengkap INTO v_nama FROM Anggota WHERE id = p_id_anggota;

    -- A. Ke Diri Sendiri
    INSERT INTO Notifikasi (id_anggota, judul, pesan, tipe, link_url, is_read, dibuat_pada)
    VALUES (p_id_anggota, 'Absensi Berhasil', CONCAT('Anda berhasil absen ', p_status, ' pada ', DATE_FORMAT(p_waktu, '%H:%i')), 'success', '/absensi', 0, NOW());

    -- B. Ke Ketua & Sekretaris
    INSERT INTO Notifikasi (id_anggota, judul, pesan, tipe, link_url, is_read, dibuat_pada)
    SELECT a.id, 'Aktivitas Absensi', CONCAT(v_nama, ' telah absen ', p_status), 'info', '/laporan/absensi', 0, NOW()
    FROM Anggota a
    JOIN Jabatan j ON a.id_jabatan = j.id
    WHERE j.nama_jabatan IN ('Ketua DPC', 'Sekretaris') AND a.status_aktif = 1;
END;

-- 2. SP NOTIFIKASI PENGAJUAN (Dinas/Izin/Sakit)
-- Mengirim Alert ke Ketua & Sekretaris saat ada member mengajukan
DROP PROCEDURE IF EXISTS sp_notif_pengajuan_baru;
CREATE PROCEDURE sp_notif_pengajuan_baru (
    IN p_id_pengaju INT,
    IN p_tipe_pengajuan VARCHAR(50),
    IN p_tanggal DATE
)
BEGIN
    DECLARE v_nama VARCHAR(150);
    SELECT nama_lengkap INTO v_nama FROM Anggota WHERE id = p_id_pengaju;

    INSERT INTO Notifikasi (id_anggota, judul, pesan, tipe, link_url, is_read, dibuat_pada)
    SELECT a.id, CONCAT('Pengajuan ', REPLACE(p_tipe_pengajuan, '_', ' ')), 
           CONCAT(v_nama, ' mengajukan permohonan untuk tgl ', p_tanggal), 'alert', '/absensi/persetujuan', 0, NOW()
    FROM Anggota a
    JOIN Jabatan j ON a.id_jabatan = j.id
    WHERE j.nama_jabatan IN ('Ketua DPC', 'Sekretaris') AND a.status_aktif = 1;
END;

-- 3. SP NOTIFIKASI HASIL APPROVAL
-- Mengirim notif ke Anggota apakah disetujui/ditolak
DROP PROCEDURE IF EXISTS sp_notif_hasil_approval;
CREATE PROCEDURE sp_notif_hasil_approval (
    IN p_id_anggota INT,
    IN p_status_akhir VARCHAR(20),
    IN p_tanggal DATE
)
BEGIN
    DECLARE v_judul VARCHAR(100);
    DECLARE v_pesan TEXT;
    DECLARE v_tipe VARCHAR(20);

    IF p_status_akhir = 'DISETUJUI' THEN
        SET v_judul = 'Pengajuan Disetujui';
        SET v_pesan = CONCAT('Permohonan Anda untuk tanggal ', p_tanggal, ' telah DISETUJUI.');
        SET v_tipe = 'success';
    ELSE
        SET v_judul = 'Pengajuan Ditolak';
        SET v_pesan = CONCAT('Maaf, permohonan tanggal ', p_tanggal, ' DITOLAK.');
        SET v_tipe = 'warning';
    END IF;

    INSERT INTO Notifikasi (id_anggota, judul, pesan, tipe, link_url, is_read, dibuat_pada)
    VALUES (p_id_anggota, v_judul, v_pesan, v_tipe, '/absensi/riwayat', 0, NOW());
END;

-- 4. SP NOTIFIKASI KEUANGAN (Saldo Divisi)
-- Mengirim notif ke semua anggota divisi saat saldo ditambahkan
DROP PROCEDURE IF EXISTS sp_notif_saldo_divisi;
CREATE PROCEDURE sp_notif_saldo_divisi (
    IN p_id_divisi INT,
    IN p_bulan INT,
    IN p_tahun INT,
    IN p_jumlah DECIMAL(15,2)
)
BEGIN
    DECLARE v_nama_divisi VARCHAR(100);
    SELECT nama_divisi INTO v_nama_divisi FROM Divisi WHERE id = p_id_divisi;

    INSERT INTO Notifikasi (id_anggota, judul, pesan, tipe, link_url, is_read, dibuat_pada)
    SELECT id, 'Anggaran Baru', 
           CONCAT('Saldo ', v_nama_divisi, ' periode ', p_bulan, '/', p_tahun, ' sebesar Rp ', FORMAT(p_jumlah, 0), ' telah aktif.'), 
           'success', '/keuangan', 0, NOW()
    FROM Anggota
    WHERE id_divisi = p_id_divisi AND status_aktif = 1;
END;

-- 5. SP UNTUK LIST NOTIFIKASI (Opsional jika controller pakai Query Builder)
DROP PROCEDURE IF EXISTS sp_notifikasi_list;
CREATE PROCEDURE sp_notifikasi_list (
    IN p_id_anggota INT,
    IN p_limit INT
)
BEGIN
    SELECT id, judul, pesan, tipe, link_url, is_read, dibuat_pada
    FROM Notifikasi
    WHERE id_anggota = p_id_anggota
    ORDER BY dibuat_pada DESC
    LIMIT p_limit;
END;

-- 6. SP MARK AS READ
DROP PROCEDURE IF EXISTS sp_notifikasi_mark_read;
CREATE PROCEDURE sp_notifikasi_mark_read (
    IN p_id_notifikasi INT
)
BEGIN
    UPDATE Notifikasi 
    SET is_read = 1, diupdate_pada = NOW() 
    WHERE id = p_id_notifikasi;
END;
SQL
        );
    }

    public function down(): void
    {
        // Logic rollback (jika ada)
        DB::unprepared(<<<'SQL'
DROP PROCEDURE IF EXISTS `Sp_Notifikasi`;
SQL
        );
    }
};
