-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: db:3306
-- Waktu pembuatan: 14 Jan 2026 pada 08.47
-- Versi server: 8.0.44
-- Versi PHP: 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Basis data: `db_sistem_dpc`
--

DELIMITER $$
--
-- Prosedur
--
CREATE DEFINER=`root`@`%` PROCEDURE `sp_dashboard_anggota` (IN `p_id_anggota` INT)   BEGIN
    -- 1. Cek Jam Masuk Hari Ini
    SELECT jam_masuk INTO @jam_masuk 
    FROM Riwayat_Absensi 
    WHERE id_anggota = p_id_anggota 
    AND tanggal = CURDATE() 
    LIMIT 1;

    -- 2. Cek Apakah Sudah Lapor Hari Ini (1 = Sudah, 0 = Belum)
    SELECT COUNT(*) INTO @sudah_lapor 
    FROM Laporan_Harian 
    WHERE id_anggota = p_id_anggota 
    AND tanggal_laporan = CURDATE();

    -- Outputkan hasilnya
    SELECT 
        COALESCE(@jam_masuk, NULL) AS jam_masuk,
        @sudah_lapor AS status_lapor;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `sp_dashboard_bph` ()   BEGIN
    -- 1. Hitung Total Anggota Aktif
    SELECT COUNT(*) INTO @total_anggota FROM Anggota WHERE status_aktif = 1;

    -- 2. Hitung Hadir Hari Ini
    SELECT COUNT(*) INTO @hadir_hari_ini FROM Riwayat_Absensi 
    WHERE tanggal = CURDATE() AND status_kehadiran = 'HADIR';

    -- 3. Hitung Dinas Hari Ini (BARU DITAMBAHKAN)
    -- Menggunakan LIKE 'DINAS%' agar menangkap 'DINAS', 'DINAS_LUAR', dll
    SELECT COUNT(*) INTO @dinas_hari_ini FROM Riwayat_Absensi 
    WHERE tanggal = CURDATE() AND status_kehadiran LIKE 'DINAS%';

    -- 4. Hitung Laporan Masuk Hari Ini
    SELECT COUNT(*) INTO @laporan_masuk FROM Laporan_Harian 
    WHERE tanggal_laporan = CURDATE();

    -- 5. Hitung Saldo Kas Bulan Ini
    SELECT COALESCE(SUM(sisa_saldo), 0) INTO @saldo_kas FROM Periode_Keuangan 
    WHERE bulan = MONTH(CURDATE()) AND tahun = YEAR(CURDATE());

    -- Outputkan hasilnya (Tambahkan dinas_hari_ini)
    SELECT 
        @total_anggota AS total_anggota,
        @hadir_hari_ini AS hadir_hari_ini,
        @dinas_hari_ini AS dinas_hari_ini,
        @laporan_masuk AS laporan_masuk,
        @saldo_kas AS saldo_kas;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `sp_login_anggota` (IN `p_username` VARCHAR(50))   BEGIN
    -- Ambil data anggota + nama jabatan + nama divisi
    SELECT 
        a.id,
        a.nama_lengkap,
        a.username,
        a.password_hash,
        a.email,
        a.status_aktif,
        j.nama_jabatan,
        j.level_akses,  -- PENTING: Untuk Sidebar (BPH/ANGGOTA)
        d.nama_divisi
    FROM Anggota a
    LEFT JOIN Jabatan j ON a.id_jabatan = j.id
    LEFT JOIN Divisi d ON a.id_divisi = d.id
    WHERE a.username = p_username 
    AND a.status_aktif = 1
    LIMIT 1;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `Anggota`
--

CREATE TABLE `Anggota` (
  `id` int NOT NULL,
  `nama_lengkap` varchar(150) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `nomor_hp` varchar(20) DEFAULT NULL,
  `id_divisi` int DEFAULT NULL,
  `id_jabatan` int DEFAULT NULL,
  `string_kode_qr` varchar(100) DEFAULT NULL,
  `foto_profil` varchar(255) DEFAULT NULL,
  `status_aktif` tinyint(1) DEFAULT '1',
  `terakhir_login` datetime DEFAULT NULL,
  `dibuat_pada` datetime DEFAULT CURRENT_TIMESTAMP,
  `diupdate_pada` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `Anggota`
--

INSERT INTO `Anggota` (`id`, `nama_lengkap`, `username`, `password_hash`, `email`, `remember_token`, `nomor_hp`, `id_divisi`, `id_jabatan`, `string_kode_qr`, `foto_profil`, `status_aktif`, `terakhir_login`, `dibuat_pada`, `diupdate_pada`) VALUES
(1, 'Bapak Ketua', 'ketua', '$2y$12$a4A1CG.OGgyD7c4NnYipnuTMj0gIjtVk.NNKsq2IhxyPPKFlS.B7C', 'ketua@spsi.com', NULL, NULL, NULL, 1, NULL, NULL, 1, NULL, '2026-01-14 01:46:47', '2026-01-14 03:42:40'),
(2, 'Ibu Sekretaris', 'sekretaris', '$2y$12$eoRr7ISrGooHvuGvs/gAFujPayZ.0XZD3HxuivzQwL5f2xJonpTtC', 'sekre@spsi.com', NULL, NULL, NULL, 2, NULL, NULL, 1, NULL, '2026-01-14 01:46:47', '2026-01-14 03:42:40'),
(3, 'Ibu Bendahara', 'bendahara', '$2y$12$Qd5c/XSWIcaS4e/hqP8Ps.XwIbwlo71ETvuyAhFegJRhEVPU0sbK2', 'bendahara@spsi.com', NULL, NULL, NULL, 3, NULL, NULL, 1, NULL, '2026-01-14 01:46:47', '2026-01-14 03:42:40'),
(4, 'Admin Organisasi', 'organisasi', '$2y$12$SICJpdLYS5MdUhj3TlcV8eqLwXJEuZHmlNI6GHCY4bAbuqpQ9QQO2', 'org@spsi.com', NULL, NULL, 1, 4, NULL, NULL, 1, NULL, '2026-01-14 01:46:47', '2026-01-14 03:42:40'),
(5, 'Admin Advokasi', 'advokasi', '$2y$12$.SaxNC1tJs8Kwj2w60UoYekBXTXKsxqrXu8OJo5np90eSVJtZ3alS', 'adv@spsi.com', NULL, NULL, 2, 5, NULL, NULL, 1, NULL, '2026-01-14 01:46:47', '2026-01-14 03:42:41'),
(6, 'Admin PSDM', 'psdm', '$2y$12$vtYFpUhHxNPZt5zINy.5WePpEWh96vzAvCV0UCmXoYrMMDXEJYUhW', 'psdm@spsi.com', NULL, NULL, 3, 6, NULL, NULL, 1, NULL, '2026-01-14 01:46:47', '2026-01-14 03:42:41'),
(7, 'Admin Ekonomi', 'ekonomi', '$2y$12$1xDGIohCPwZo0hOCwSf5MucG0jVN0VgdInZDURejThUAXEYgGpuWm', 'eko@spsi.com', NULL, NULL, 4, 7, NULL, NULL, 1, NULL, '2026-01-14 01:46:47', '2026-01-14 03:42:41'),
(8, 'Admin Publikasi', 'publikasi', '$2y$12$z4SOdbTz13dbjx6T3d3VdesXEPxOAGU9uIqi8oNcShzlq8BI6xwHW', 'pub@spsi.com', NULL, NULL, 5, 8, NULL, NULL, 1, NULL, '2026-01-14 01:46:47', '2026-01-14 03:42:41'),
(9, 'Anggota Contoh', 'anggota', '$2y$12$V.o3Mubu0ly9Bqi8igozZemmytF/9TXno4WhoSFPrFpgrJEqVofwG', 'user@spsi.com', NULL, NULL, 1, 9, NULL, NULL, 1, NULL, '2026-01-14 01:46:47', '2026-01-14 03:42:41');

-- --------------------------------------------------------

--
-- Struktur dari tabel `Divisi`
--

CREATE TABLE `Divisi` (
  `id` int NOT NULL,
  `nama_divisi` varchar(100) NOT NULL,
  `kode_divisi` varchar(10) DEFAULT NULL,
  `deskripsi` varchar(255) DEFAULT NULL,
  `dibuat_pada` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `Divisi`
--

INSERT INTO `Divisi` (`id`, `nama_divisi`, `kode_divisi`, `deskripsi`, `dibuat_pada`) VALUES
(1, 'Bidang Organisasi', 'ORG', 'Mengurus keanggotaan dan internal', '2026-01-14 01:46:47'),
(2, 'Bidang Advokasi', 'ADV', 'Hukum dan pembelaan anggota', '2026-01-14 01:46:47'),
(3, 'Bidang PSDM', 'PSDM', 'Pengembangan Sumber Daya Manusia & Diklat', '2026-01-14 01:46:47'),
(4, 'Bidang Kesejahteraan', 'KES', 'Ekonomi dan sosial', '2026-01-14 01:46:47'),
(5, 'Bidang Publikasi & Hubungan', 'PUB', 'Humas dan Media', '2026-01-14 01:46:47');

-- --------------------------------------------------------

--
-- Struktur dari tabel `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `Jabatan`
--

CREATE TABLE `Jabatan` (
  `id` int NOT NULL,
  `nama_jabatan` varchar(100) NOT NULL,
  `id_divisi` int DEFAULT NULL,
  `level_akses` enum('BPH','KORBID','ANGGOTA') NOT NULL DEFAULT 'ANGGOTA',
  `dibuat_pada` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `Jabatan`
--

INSERT INTO `Jabatan` (`id`, `nama_jabatan`, `id_divisi`, `level_akses`, `dibuat_pada`) VALUES
(1, 'Ketua DPC', NULL, 'BPH', '2026-01-14 01:46:47'),
(2, 'Sekretaris', NULL, 'BPH', '2026-01-14 01:46:47'),
(3, 'Bendahara', NULL, 'BPH', '2026-01-14 01:46:47'),
(4, 'Ketua Bidang Organisasi', 1, 'KORBID', '2026-01-14 01:46:47'),
(5, 'Ketua Bidang Advokasi', 2, 'KORBID', '2026-01-14 01:46:47'),
(6, 'Ketua Bidang PSDM', 3, 'KORBID', '2026-01-14 01:46:47'),
(7, 'Ketua Bidang Kesejahteraan', 4, 'KORBID', '2026-01-14 01:46:47'),
(8, 'Ketua Bidang Publikasi', 5, 'KORBID', '2026-01-14 01:46:47'),
(9, 'Anggota Biasa', NULL, 'ANGGOTA', '2026-01-14 01:46:47');

-- --------------------------------------------------------

--
-- Struktur dari tabel `Laporan_Harian`
--

CREATE TABLE `Laporan_Harian` (
  `id` int NOT NULL,
  `id_divisi` int NOT NULL,
  `id_anggota` int NOT NULL,
  `id_program_kerja` int DEFAULT NULL,
  `tanggal_laporan` date NOT NULL,
  `judul_kegiatan` varchar(200) NOT NULL,
  `isi_laporan` text NOT NULL,
  `url_lampiran` varchar(255) DEFAULT NULL,
  `status_laporan` enum('DRAFT','DISUBMIT') DEFAULT 'DRAFT',
  `dibuat_pada` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `Laporan_Harian`
--

INSERT INTO `Laporan_Harian` (`id`, `id_divisi`, `id_anggota`, `id_program_kerja`, `tanggal_laporan`, `judul_kegiatan`, `isi_laporan`, `url_lampiran`, `status_laporan`, `dibuat_pada`) VALUES
(1, 1, 4, NULL, '2026-01-14', 'Konsolidasi Internal Organisasi', 'Melakukan rapat rutin dengan pengurus PUK di kawasan KIIC.', NULL, 'DISUBMIT', '2026-01-14 08:17:40'),
(2, 1, 4, NULL, '2026-01-14', 'Verifikasi Anggota Baru', 'Mengecek data pendaftaran anggota baru via aplikasi.', NULL, 'DISUBMIT', '2026-01-14 08:17:40'),
(3, 2, 5, NULL, '2026-01-14', 'Pendampingan Kasus PHK', 'Melakukan mediasi tripartit di Disnaker Karawang.', NULL, 'DISUBMIT', '2026-01-14 08:17:40'),
(4, 2, 5, NULL, '2026-01-14', 'Kajian UU Cipta Kerja', 'Membedah pasal-pasal krusial untuk bahan advokasi.', NULL, 'DRAFT', '2026-01-14 08:17:40'),
(5, 3, 6, NULL, '2026-01-14', 'Persiapan Diklat Paralegal', 'Menghubungi pemateri dan booking tempat.', NULL, 'DISUBMIT', '2026-01-14 08:17:40'),
(6, 3, 6, NULL, '2026-01-14', 'Training Leadership', 'Sesi 1 materi kepemimpinan dasar.', NULL, 'DISUBMIT', '2026-01-14 08:17:40'),
(7, 3, 6, NULL, '2026-01-14', 'Evaluasi Peserta', 'Rekap nilai pre-test peserta diklat.', NULL, 'DISUBMIT', '2026-01-14 08:17:40'),
(8, 2, 5, NULL, '2026-01-13', 'Meeting Mingguan Advokasi', 'Evaluasi kasus berjalan minggu ini.', NULL, 'DISUBMIT', '2026-01-14 08:17:40'),
(9, 2, 5, NULL, '2026-01-12', 'Kunjungan ke PUK Toyota', 'Sosialisasi program bantuan hukum.', NULL, 'DISUBMIT', '2026-01-14 08:17:40');

-- --------------------------------------------------------

--
-- Struktur dari tabel `Log_Audit`
--

CREATE TABLE `Log_Audit` (
  `id` bigint NOT NULL,
  `id_anggota` int DEFAULT NULL,
  `aksi` varchar(50) NOT NULL,
  `nama_tabel` varchar(50) DEFAULT NULL,
  `id_record` int DEFAULT NULL,
  `isi_data` text,
  `alamat_ip` varchar(45) DEFAULT NULL,
  `waktu_kejadian` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2026_01_14_030712_create_sessions_table', 2);

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `Pengajuan_Absensi`
--

CREATE TABLE `Pengajuan_Absensi` (
  `id` int NOT NULL,
  `id_anggota` int NOT NULL,
  `tipe_pengajuan` enum('DINAS_DALAM','DINAS_LUAR','SAKIT','IZIN','CUTI') NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `lokasi_tujuan` varchar(255) DEFAULT NULL,
  `alasan` text NOT NULL,
  `url_lampiran` varchar(255) DEFAULT NULL,
  `status_dokumen` enum('DRAFT','DIAJUKAN','DISETUJUI','DITOLAK') DEFAULT 'DRAFT',
  `id_penyetuju` int DEFAULT NULL,
  `waktu_validasi` datetime DEFAULT NULL,
  `catatan_penyetuju` varchar(255) DEFAULT NULL,
  `dibuat_pada` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `Periode_Keuangan`
--

CREATE TABLE `Periode_Keuangan` (
  `id` int NOT NULL,
  `id_divisi` int NOT NULL,
  `bulan` tinyint NOT NULL,
  `tahun` smallint NOT NULL,
  `id_penanggung_jawab` int DEFAULT NULL,
  `saldo_awal` decimal(15,2) DEFAULT '0.00',
  `dana_masuk` decimal(15,2) DEFAULT '0.00',
  `total_dana_tersedia` decimal(15,2) DEFAULT '0.00',
  `total_pengeluaran` decimal(15,2) DEFAULT '0.00',
  `sisa_saldo` decimal(15,2) DEFAULT '0.00',
  `status_dokumen` enum('DRAFT','DISUBMIT','DIKUNCI') DEFAULT 'DRAFT',
  `dibuat_pada` datetime DEFAULT CURRENT_TIMESTAMP,
  `diupdate_pada` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `Periode_Keuangan`
--

INSERT INTO `Periode_Keuangan` (`id`, `id_divisi`, `bulan`, `tahun`, `id_penanggung_jawab`, `saldo_awal`, `dana_masuk`, `total_dana_tersedia`, `total_pengeluaran`, `sisa_saldo`, `status_dokumen`, `dibuat_pada`, `diupdate_pada`) VALUES
(1, 1, 1, 2026, NULL, 25000000.00, 0.00, 0.00, 5000000.00, 20000000.00, 'DISUBMIT', '2026-01-14 08:17:40', NULL),
(2, 2, 1, 2026, NULL, 15000000.00, 0.00, 0.00, 7500000.00, 7500000.00, 'DISUBMIT', '2026-01-14 08:17:40', NULL),
(3, 3, 1, 2026, NULL, 10000000.00, 0.00, 0.00, 2000000.00, 8000000.00, 'DISUBMIT', '2026-01-14 08:17:40', NULL),
(4, 4, 1, 2026, NULL, 50000000.00, 0.00, 0.00, 1000000.00, 49000000.00, 'DISUBMIT', '2026-01-14 08:17:40', NULL),
(5, 5, 1, 2026, NULL, 5000000.00, 0.00, 0.00, 4500000.00, 500000.00, 'DISUBMIT', '2026-01-14 08:17:40', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `Program_Kerja`
--

CREATE TABLE `Program_Kerja` (
  `id` int NOT NULL,
  `id_divisi` int NOT NULL,
  `nama_program` varchar(200) NOT NULL,
  `deskripsi` text,
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `anggaran_rencana` decimal(15,2) DEFAULT '0.00',
  `status_proker` enum('RENCANA','BERJALAN','SELESAI','DITUNDA','DIBATALKAN') DEFAULT 'RENCANA',
  `persen_progress` tinyint DEFAULT '0',
  `dibuat_pada` datetime DEFAULT CURRENT_TIMESTAMP,
  `dibuat_oleh` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `Riwayat_Absensi`
--

CREATE TABLE `Riwayat_Absensi` (
  `id` int NOT NULL,
  `id_anggota` int NOT NULL,
  `tanggal` date NOT NULL,
  `jam_masuk` time DEFAULT NULL,
  `jam_pulang` time DEFAULT NULL,
  `status_kehadiran` enum('HADIR','DINAS','IZIN','SAKIT','ALPHA') DEFAULT 'HADIR',
  `sumber_absensi` enum('QR_DINDING','HP_MOBILE','INPUT_MANUAL','SYSTEM_GENERATED') DEFAULT 'QR_DINDING',
  `keterangan_tambahan` varchar(255) DEFAULT NULL,
  `dibuat_pada` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `Riwayat_Absensi`
--

INSERT INTO `Riwayat_Absensi` (`id`, `id_anggota`, `tanggal`, `jam_masuk`, `jam_pulang`, `status_kehadiran`, `sumber_absensi`, `keterangan_tambahan`, `dibuat_pada`) VALUES
(1, 1, '2026-01-14', '07:55:00', NULL, 'HADIR', 'QR_DINDING', NULL, '2026-01-14 08:17:40'),
(2, 2, '2026-01-14', '08:05:00', NULL, 'HADIR', 'QR_DINDING', NULL, '2026-01-14 08:17:40'),
(3, 4, '2026-01-14', '07:45:00', NULL, 'HADIR', 'QR_DINDING', NULL, '2026-01-14 08:17:40'),
(4, 5, '2026-01-14', '08:10:00', NULL, 'HADIR', 'QR_DINDING', NULL, '2026-01-14 08:17:40'),
(5, 6, '2026-01-14', NULL, NULL, 'DINAS', 'QR_DINDING', NULL, '2026-01-14 08:17:40'),
(6, 7, '2026-01-14', NULL, NULL, 'SAKIT', 'QR_DINDING', NULL, '2026-01-14 08:17:40'),
(7, 8, '2026-01-14', NULL, NULL, 'IZIN', 'QR_DINDING', NULL, '2026-01-14 08:17:40'),
(8, 5, '2026-01-13', '07:50:00', '17:05:00', 'HADIR', 'QR_DINDING', NULL, '2026-01-14 08:17:40'),
(9, 5, '2026-01-12', '08:00:00', '17:10:00', 'HADIR', 'QR_DINDING', NULL, '2026-01-14 08:17:40'),
(10, 5, '2026-01-11', NULL, NULL, 'DINAS', 'QR_DINDING', NULL, '2026-01-14 08:17:40');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `payload` longtext NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('inuecKXlc398urLUWOEVvvU4baQYn3TQ37o6cXjC', 4, '172.18.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTo3OntzOjY6Il90b2tlbiI7czo0MDoiekNsa0V5WE8yUE1zRDBQSVFKS3NqVTZuOXRZMG5aRWIxNFozRUJwdSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9kYXNoYm9hcmQiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTo0O3M6MTI6InVzZXJfamFiYXRhbiI7czoyMzoiS2V0dWEgQmlkYW5nIE9yZ2FuaXNhc2kiO3M6MTA6InVzZXJfbGV2ZWwiO3M6NjoiS09SQklEIjtzOjExOiJ1c2VyX2RpdmlzaSI7czoxNzoiQmlkYW5nIE9yZ2FuaXNhc2kiO30=', 1768370567),
('u8EPdzhYUac7KGclwYTG896uCjTwSFxFcgXj8R3R', 1, '172.18.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTo4OntzOjY6Il90b2tlbiI7czo0MDoiNGZ1SzRQeEowR09BQXhjUGREOGRaeTFaeDZOenJOa21KaklJVlA3NyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6MTI6InVzZXJfamFiYXRhbiI7czo5OiJLZXR1YSBEUEMiO3M6MTA6InVzZXJfbGV2ZWwiO3M6MzoiQlBIIjtzOjExOiJ1c2VyX2RpdmlzaSI7czoxOiItIjtzOjM6InVybCI7YToxOntzOjg6ImludGVuZGVkIjtzOjMxOiJodHRwOi8vbG9jYWxob3N0OjgwMDAvZGFzaGJvYXJkIjt9fQ==', 1768363960);

-- --------------------------------------------------------

--
-- Struktur dari tabel `Transaksi_Pengeluaran`
--

CREATE TABLE `Transaksi_Pengeluaran` (
  `id` int NOT NULL,
  `id_periode_keuangan` int NOT NULL,
  `id_program_kerja` int DEFAULT NULL,
  `tanggal_transaksi` date NOT NULL,
  `nama_kegiatan` varchar(200) NOT NULL,
  `uraian_pengeluaran` text NOT NULL,
  `volume` decimal(10,2) DEFAULT '1.00',
  `satuan` varchar(50) NOT NULL,
  `jumlah_rupiah` decimal(15,2) NOT NULL,
  `total_nominal` decimal(15,2) GENERATED ALWAYS AS ((`volume` * `jumlah_rupiah`)) STORED,
  `keterangan` varchar(255) DEFAULT NULL,
  `url_bukti_struk` varchar(255) DEFAULT NULL,
  `dibuat_pada` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indeks untuk tabel yang dibuang
--

--
-- Indeks untuk tabel `Anggota`
--
ALTER TABLE `Anggota`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `string_kode_qr` (`string_kode_qr`),
  ADD KEY `id_divisi` (`id_divisi`),
  ADD KEY `id_jabatan` (`id_jabatan`);

--
-- Indeks untuk tabel `Divisi`
--
ALTER TABLE `Divisi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nama_divisi` (`nama_divisi`),
  ADD UNIQUE KEY `kode_divisi` (`kode_divisi`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indeks untuk tabel `Jabatan`
--
ALTER TABLE `Jabatan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_divisi` (`id_divisi`);

--
-- Indeks untuk tabel `Laporan_Harian`
--
ALTER TABLE `Laporan_Harian`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_divisi` (`id_divisi`),
  ADD KEY `id_anggota` (`id_anggota`),
  ADD KEY `id_program_kerja` (`id_program_kerja`);

--
-- Indeks untuk tabel `Log_Audit`
--
ALTER TABLE `Log_Audit`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indeks untuk tabel `Pengajuan_Absensi`
--
ALTER TABLE `Pengajuan_Absensi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_anggota` (`id_anggota`),
  ADD KEY `id_penyetuju` (`id_penyetuju`);

--
-- Indeks untuk tabel `Periode_Keuangan`
--
ALTER TABLE `Periode_Keuangan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UQ_Laporan_Bulanan` (`id_divisi`,`bulan`,`tahun`),
  ADD KEY `id_penanggung_jawab` (`id_penanggung_jawab`);

--
-- Indeks untuk tabel `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indeks untuk tabel `Program_Kerja`
--
ALTER TABLE `Program_Kerja`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_divisi` (`id_divisi`),
  ADD KEY `dibuat_oleh` (`dibuat_oleh`);

--
-- Indeks untuk tabel `Riwayat_Absensi`
--
ALTER TABLE `Riwayat_Absensi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UQ_Absensi_Harian` (`id_anggota`,`tanggal`);

--
-- Indeks untuk tabel `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indeks untuk tabel `Transaksi_Pengeluaran`
--
ALTER TABLE `Transaksi_Pengeluaran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_periode_keuangan` (`id_periode_keuangan`),
  ADD KEY `id_program_kerja` (`id_program_kerja`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `Anggota`
--
ALTER TABLE `Anggota`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `Divisi`
--
ALTER TABLE `Divisi`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `Jabatan`
--
ALTER TABLE `Jabatan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `Laporan_Harian`
--
ALTER TABLE `Laporan_Harian`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `Log_Audit`
--
ALTER TABLE `Log_Audit`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `Pengajuan_Absensi`
--
ALTER TABLE `Pengajuan_Absensi`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `Periode_Keuangan`
--
ALTER TABLE `Periode_Keuangan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `Program_Kerja`
--
ALTER TABLE `Program_Kerja`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `Riwayat_Absensi`
--
ALTER TABLE `Riwayat_Absensi`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `Transaksi_Pengeluaran`
--
ALTER TABLE `Transaksi_Pengeluaran`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `Anggota`
--
ALTER TABLE `Anggota`
  ADD CONSTRAINT `Anggota_ibfk_1` FOREIGN KEY (`id_divisi`) REFERENCES `Divisi` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `Anggota_ibfk_2` FOREIGN KEY (`id_jabatan`) REFERENCES `Jabatan` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `Jabatan`
--
ALTER TABLE `Jabatan`
  ADD CONSTRAINT `Jabatan_ibfk_1` FOREIGN KEY (`id_divisi`) REFERENCES `Divisi` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `Laporan_Harian`
--
ALTER TABLE `Laporan_Harian`
  ADD CONSTRAINT `Laporan_Harian_ibfk_1` FOREIGN KEY (`id_divisi`) REFERENCES `Divisi` (`id`),
  ADD CONSTRAINT `Laporan_Harian_ibfk_2` FOREIGN KEY (`id_anggota`) REFERENCES `Anggota` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `Laporan_Harian_ibfk_3` FOREIGN KEY (`id_program_kerja`) REFERENCES `Program_Kerja` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `Pengajuan_Absensi`
--
ALTER TABLE `Pengajuan_Absensi`
  ADD CONSTRAINT `Pengajuan_Absensi_ibfk_1` FOREIGN KEY (`id_anggota`) REFERENCES `Anggota` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `Pengajuan_Absensi_ibfk_2` FOREIGN KEY (`id_penyetuju`) REFERENCES `Anggota` (`id`);

--
-- Ketidakleluasaan untuk tabel `Periode_Keuangan`
--
ALTER TABLE `Periode_Keuangan`
  ADD CONSTRAINT `Periode_Keuangan_ibfk_1` FOREIGN KEY (`id_divisi`) REFERENCES `Divisi` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `Periode_Keuangan_ibfk_2` FOREIGN KEY (`id_penanggung_jawab`) REFERENCES `Anggota` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `Program_Kerja`
--
ALTER TABLE `Program_Kerja`
  ADD CONSTRAINT `Program_Kerja_ibfk_1` FOREIGN KEY (`id_divisi`) REFERENCES `Divisi` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `Program_Kerja_ibfk_2` FOREIGN KEY (`dibuat_oleh`) REFERENCES `Anggota` (`id`);

--
-- Ketidakleluasaan untuk tabel `Riwayat_Absensi`
--
ALTER TABLE `Riwayat_Absensi`
  ADD CONSTRAINT `Riwayat_Absensi_ibfk_1` FOREIGN KEY (`id_anggota`) REFERENCES `Anggota` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `Transaksi_Pengeluaran`
--
ALTER TABLE `Transaksi_Pengeluaran`
  ADD CONSTRAINT `Transaksi_Pengeluaran_ibfk_1` FOREIGN KEY (`id_periode_keuangan`) REFERENCES `Periode_Keuangan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `Transaksi_Pengeluaran_ibfk_2` FOREIGN KEY (`id_program_kerja`) REFERENCES `Program_Kerja` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
