<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $procedures = [
            "DROP PROCEDURE IF EXISTS `sp_dashboard_anggota`",
            "CREATE PROCEDURE `sp_dashboard_anggota` (IN `p_id_anggota` INT) BEGIN SELECT jam_masuk INTO @jam_masuk FROM Riwayat_Absensi WHERE id_anggota = p_id_anggota AND tanggal = CURDATE() LIMIT 1; SELECT COUNT(*) INTO @sudah_lapor FROM Laporan_Harian WHERE id_anggota = p_id_anggota AND tanggal_laporan = CURDATE(); SELECT COALESCE(@jam_masuk, NULL) AS jam_masuk, @sudah_lapor AS status_lapor; END",

            "DROP PROCEDURE IF EXISTS `sp_dashboard_bph`",
            "CREATE PROCEDURE `sp_dashboard_bph` () BEGIN SELECT COUNT(*) INTO @total_anggota FROM Anggota WHERE status_aktif = 1; SELECT COUNT(*) INTO @hadir_hari_ini FROM Riwayat_Absensi WHERE tanggal = CURDATE() AND status_kehadiran = 'HADIR'; SELECT COUNT(*) INTO @dinas_hari_ini FROM Riwayat_Absensi WHERE tanggal = CURDATE() AND status_kehadiran LIKE 'DINAS%'; SELECT COUNT(*) INTO @laporan_masuk FROM Laporan_Harian WHERE tanggal_laporan = CURDATE(); SELECT COALESCE(SUM(sisa_saldo), 0) INTO @saldo_kas FROM Periode_Keuangan WHERE bulan = MONTH(CURDATE()) AND tahun = YEAR(CURDATE()); SELECT @total_anggota AS total_anggota, @hadir_hari_ini AS hadir_hari_ini, @dinas_hari_ini AS dinas_hari_ini, @laporan_masuk AS laporan_masuk, @saldo_kas AS saldo_kas; END",

            "DROP PROCEDURE IF EXISTS `sp_divisi_list`",
            "CREATE PROCEDURE `sp_divisi_list` () BEGIN SELECT id, nama_divisi, kode_divisi FROM Divisi ORDER BY nama_divisi; END",

            "DROP PROCEDURE IF EXISTS `sp_laporan_create`",
            "CREATE PROCEDURE `sp_laporan_create` (IN `p_id_divisi` INT, IN `p_id_anggota` INT, IN `p_id_program_kerja` INT, IN `p_tanggal` DATE, IN `p_judul` VARCHAR(200), IN `p_isi` TEXT, IN `p_url_lampiran` VARCHAR(255), IN `p_status` VARCHAR(10)) BEGIN INSERT INTO Laporan_Harian (id_divisi, id_anggota, id_program_kerja, tanggal_laporan, judul_kegiatan, isi_laporan, url_lampiran, status_laporan) VALUES (p_id_divisi, p_id_anggota, p_id_program_kerja, p_tanggal, p_judul, p_isi, p_url_lampiran, p_status); SELECT LAST_INSERT_ID() AS id_laporan; END",

            "DROP PROCEDURE IF EXISTS `sp_laporan_detail`",
            "CREATE PROCEDURE `sp_laporan_detail` (IN `p_id_laporan` INT) BEGIN SELECT l.id, l.tanggal_laporan, l.judul_kegiatan, l.isi_laporan, l.status_laporan, l.url_lampiran, l.id_divisi, d.nama_divisi, d.kode_divisi, l.id_anggota, a.nama_lengkap, l.id_program_kerja, pk.nama_program, l.dibuat_pada FROM Laporan_Harian l JOIN Divisi d ON l.id_divisi = d.id JOIN Anggota a ON l.id_anggota = a.id LEFT JOIN Program_Kerja pk ON l.id_program_kerja = pk.id WHERE l.id = p_id_laporan LIMIT 1; END",

            "DROP PROCEDURE IF EXISTS `sp_laporan_list`",
            "CREATE PROCEDURE `sp_laporan_list` (IN `p_level_akses` VARCHAR(10), IN `p_id_divisi` INT, IN `p_search` VARCHAR(200), IN `p_start_date` DATE, IN `p_end_date` DATE, IN `p_filter_divisi` INT) BEGIN SELECT l.id, l.tanggal_laporan, l.judul_kegiatan, l.isi_laporan, l.status_laporan, l.url_lampiran, l.id_divisi, d.nama_divisi, d.kode_divisi, l.id_anggota, a.nama_lengkap, l.id_program_kerja, pk.nama_program, COALESCE(SUM(tp.total_nominal), 0) AS total_pengeluaran FROM Laporan_Harian l JOIN Divisi d ON l.id_divisi = d.id JOIN Anggota a ON l.id_anggota = a.id LEFT JOIN Program_Kerja pk ON l.id_program_kerja = pk.id LEFT JOIN Transaksi_Pengeluaran tp ON tp.tanggal_transaksi = l.tanggal_laporan AND tp.nama_kegiatan = l.judul_kegiatan AND (tp.id_program_kerja <=> l.id_program_kerja) WHERE (p_level_akses = 'BPH' OR l.id_divisi = p_id_divisi) AND (p_filter_divisi IS NULL OR p_filter_divisi = 0 OR l.id_divisi = p_filter_divisi) AND (p_start_date IS NULL OR l.tanggal_laporan >= p_start_date) AND (p_end_date IS NULL OR l.tanggal_laporan <= p_end_date) AND ( p_search IS NULL OR p_search = '' OR l.judul_kegiatan LIKE CONCAT('%', p_search, '%') OR l.isi_laporan LIKE CONCAT('%', p_search, '%') OR a.nama_lengkap LIKE CONCAT('%', p_search, '%') ) GROUP BY l.id, l.tanggal_laporan, l.judul_kegiatan, l.isi_laporan, l.status_laporan, l.url_lampiran, l.id_divisi, d.nama_divisi, d.kode_divisi, l.id_anggota, a.nama_lengkap, l.id_program_kerja, pk.nama_program ORDER BY l.tanggal_laporan DESC, l.id DESC; END",

            "DROP PROCEDURE IF EXISTS `sp_laporan_pengeluaran_detail`",
            "CREATE PROCEDURE `sp_laporan_pengeluaran_detail` (IN `p_id_laporan` INT) BEGIN SELECT tp.id, tp.tanggal_transaksi, tp.nama_kegiatan, tp.uraian_pengeluaran, tp.volume, tp.satuan, tp.jumlah_rupiah, tp.total_nominal, tp.keterangan, tp.url_bukti_struk FROM Laporan_Harian l LEFT JOIN Transaksi_Pengeluaran tp ON tp.tanggal_transaksi = l.tanggal_laporan AND tp.nama_kegiatan = l.judul_kegiatan AND (tp.id_program_kerja <=> l.id_program_kerja) WHERE l.id = p_id_laporan ORDER BY tp.id ASC; END",

            "DROP PROCEDURE IF EXISTS `sp_laporan_saldo_bph`",
            "CREATE PROCEDURE `sp_laporan_saldo_bph` (IN `p_bulan` TINYINT, IN `p_tahun` SMALLINT) BEGIN SELECT d.id, d.nama_divisi, d.kode_divisi, COALESCE(pk.saldo_awal, 0) AS saldo_awal, COALESCE(pk.total_pengeluaran, 0) AS total_pengeluaran, COALESCE(pk.sisa_saldo, 0) AS sisa_saldo FROM Divisi d LEFT JOIN Periode_Keuangan pk ON pk.id_divisi = d.id AND pk.bulan = p_bulan AND pk.tahun = p_tahun ORDER BY d.nama_divisi; END",

            "DROP PROCEDURE IF EXISTS `sp_laporan_saldo_divisi`",
            "CREATE PROCEDURE `sp_laporan_saldo_divisi` (IN `p_id_divisi` INT, IN `p_bulan` TINYINT, IN `p_tahun` SMALLINT) BEGIN SELECT d.id, d.nama_divisi, d.kode_divisi, COALESCE(pk.saldo_awal, 0) AS saldo_awal, COALESCE(pk.total_pengeluaran, 0) AS total_pengeluaran, COALESCE(pk.sisa_saldo, 0) AS sisa_saldo FROM Divisi d LEFT JOIN Periode_Keuangan pk ON pk.id_divisi = d.id AND pk.bulan = p_bulan AND pk.tahun = p_tahun WHERE d.id = p_id_divisi LIMIT 1; END",

            "DROP PROCEDURE IF EXISTS `sp_login_anggota`",
            "CREATE PROCEDURE `sp_login_anggota` (IN `p_username` VARCHAR(50)) BEGIN SELECT a.id, a.nama_lengkap, a.username, a.password_hash, a.email, a.status_aktif, j.nama_jabatan, j.level_akses, d.nama_divisi FROM Anggota a LEFT JOIN Jabatan j ON a.id_jabatan = j.id LEFT JOIN Divisi d ON a.id_divisi = d.id WHERE a.username = p_username AND a.status_aktif = 1 LIMIT 1; END",

            "DROP PROCEDURE IF EXISTS `sp_pengeluaran_create`",
            "CREATE PROCEDURE `sp_pengeluaran_create` (IN `p_id_periode_keuangan` INT, IN `p_id_program_kerja` INT, IN `p_tanggal` DATE, IN `p_nama_kegiatan` VARCHAR(200), IN `p_uraian` TEXT, IN `p_volume` DECIMAL(10,2), IN `p_satuan` VARCHAR(50), IN `p_jumlah_rupiah` DECIMAL(15,2), IN `p_keterangan` VARCHAR(255), IN `p_url_bukti` VARCHAR(255)) BEGIN DECLARE v_total DECIMAL(15,2); SET v_total = COALESCE(p_volume, 1) * COALESCE(p_jumlah_rupiah, 0); INSERT INTO Transaksi_Pengeluaran (id_periode_keuangan, id_program_kerja, tanggal_transaksi, nama_kegiatan, uraian_pengeluaran, volume, satuan, jumlah_rupiah, keterangan, url_bukti_struk) VALUES (p_id_periode_keuangan, p_id_program_kerja, p_tanggal, p_nama_kegiatan, p_uraian, p_volume, p_satuan, p_jumlah_rupiah, p_keterangan, p_url_bukti); UPDATE Periode_Keuangan SET total_pengeluaran = total_pengeluaran + v_total, sisa_saldo = sisa_saldo - v_total WHERE id = p_id_periode_keuangan; END",

            "DROP PROCEDURE IF EXISTS `sp_periode_keuangan_get`",
            "CREATE PROCEDURE `sp_periode_keuangan_get` (IN `p_id_divisi` INT, IN `p_tanggal` DATE) BEGIN SELECT id, saldo_awal, total_pengeluaran, sisa_saldo FROM Periode_Keuangan WHERE id_divisi = p_id_divisi AND bulan = MONTH(p_tanggal) AND tahun = YEAR(p_tanggal) LIMIT 1; END",

            "DROP PROCEDURE IF EXISTS `sp_program_kerja_by_divisi`",
            "CREATE PROCEDURE `sp_program_kerja_by_divisi` (IN `p_id_divisi` INT) BEGIN SELECT id, nama_program FROM Program_Kerja WHERE p_id_divisi IS NULL OR id_divisi = p_id_divisi ORDER BY nama_program; END",
        ];

        foreach ($procedures as $sql) {
            DB::unprepared($sql);
        }
    }

    public function down(): void
    {
        $drops = [
            "sp_dashboard_anggota", "sp_dashboard_bph", "sp_divisi_list", "sp_laporan_create",
            "sp_laporan_detail", "sp_laporan_list", "sp_laporan_pengeluaran_detail",
            "sp_laporan_saldo_bph", "sp_laporan_saldo_divisi", "sp_login_anggota",
            "sp_pengeluaran_create", "sp_periode_keuangan_get", "sp_program_kerja_by_divisi"
        ];

        foreach ($drops as $proc) {
            DB::unprepared("DROP PROCEDURE IF EXISTS `$proc`");
        }
    }
};