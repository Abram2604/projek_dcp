-- =============================================================
-- 1. BUAT DATABASE & GUNAKAN
-- =============================================================
CREATE DATABASE IF NOT EXISTS db_sistem_dpc;
USE db_sistem_dpc;

-- =============================================================
-- A. MODUL MASTER DATA 
-- =============================================================

-- 2. Tabel Divisi
CREATE TABLE IF NOT EXISTS Divisi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_divisi VARCHAR(100) NOT NULL UNIQUE, 
    kode_divisi VARCHAR(10) UNIQUE,           
    deskripsi VARCHAR(255) NULL,
    dibuat_pada DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 3. Tabel Jabatan
CREATE TABLE IF NOT EXISTS Jabatan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_jabatan VARCHAR(100) NOT NULL,       
    id_divisi INT NULL,                       
    level_akses ENUM('BPH', 'KORBID', 'ANGGOTA') NOT NULL DEFAULT 'ANGGOTA', 
    dibuat_pada DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (id_divisi) REFERENCES Divisi(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 4. Tabel Anggota
CREATE TABLE IF NOT EXISTS Anggota (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_lengkap VARCHAR(150) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    nomor_hp VARCHAR(20),
    
    id_divisi INT NULL,
    id_jabatan INT NULL,
    
    string_kode_qr VARCHAR(100) UNIQUE, 
    foto_profil VARCHAR(255) NULL,
    status_aktif TINYINT(1) DEFAULT 1,         
    terakhir_login DATETIME NULL,
    
    dibuat_pada DATETIME DEFAULT CURRENT_TIMESTAMP,
    diupdate_pada DATETIME NULL,
    
    FOREIGN KEY (id_divisi) REFERENCES Divisi(id) ON DELETE SET NULL,
    FOREIGN KEY (id_jabatan) REFERENCES Jabatan(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- =============================================================
-- B. MODUL PROGRAM KERJA 
-- =============================================================

-- 5. Tabel Program_Kerja
CREATE TABLE IF NOT EXISTS Program_Kerja (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_divisi INT NOT NULL,
    nama_program VARCHAR(200) NOT NULL,
    deskripsi TEXT,
    
    tanggal_mulai DATE,
    tanggal_selesai DATE,
    
    anggaran_rencana DECIMAL(15, 2) DEFAULT 0,
    
    status_proker ENUM('RENCANA', 'BERJALAN', 'SELESAI', 'DITUNDA', 'DIBATALKAN') DEFAULT 'RENCANA',
    persen_progress TINYINT DEFAULT 0 CHECK (persen_progress <= 100),
    
    dibuat_pada DATETIME DEFAULT CURRENT_TIMESTAMP,
    dibuat_oleh INT NULL, 
    
    FOREIGN KEY (id_divisi) REFERENCES Divisi(id) ON DELETE CASCADE,
    FOREIGN KEY (dibuat_oleh) REFERENCES Anggota(id) ON DELETE NO ACTION
) ENGINE=InnoDB;

-- =============================================================
-- C. MODUL ABSENSI & PERIZINAN
-- =============================================================

-- 6. Tabel Riwayat_Absensi 
CREATE TABLE IF NOT EXISTS Riwayat_Absensi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_anggota INT NOT NULL,
    tanggal DATE NOT NULL,
    
    jam_masuk TIME NULL,
    jam_pulang TIME NULL,
    
    status_kehadiran ENUM('HADIR', 'DINAS', 'IZIN', 'SAKIT', 'ALPHA') DEFAULT 'HADIR', 
    sumber_absensi ENUM('QR_DINDING', 'HP_MOBILE', 'INPUT_MANUAL', 'SYSTEM_GENERATED') DEFAULT 'QR_DINDING',
    
    keterangan_tambahan VARCHAR(255) NULL,
    
    dibuat_pada DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (id_anggota) REFERENCES Anggota(id) ON DELETE CASCADE,
    CONSTRAINT UQ_Absensi_Harian UNIQUE (id_anggota, tanggal)
) ENGINE=InnoDB;

-- 7. Tabel Pengajuan_Absensi 
CREATE TABLE IF NOT EXISTS Pengajuan_Absensi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_anggota INT NOT NULL,
    
    tipe_pengajuan ENUM('DINAS_DALAM', 'DINAS_LUAR', 'SAKIT', 'IZIN', 'CUTI') NOT NULL,
    
    tanggal_mulai DATE NOT NULL,
    tanggal_selesai DATE NOT NULL,
    
    lokasi_tujuan VARCHAR(255) NULL, 
    alasan TEXT NOT NULL,
    url_lampiran VARCHAR(255),       
    
    status_dokumen ENUM('DRAFT', 'DIAJUKAN', 'DISETUJUI', 'DITOLAK') DEFAULT 'DRAFT',
    id_penyetuju INT NULL,           
    waktu_validasi DATETIME NULL,
    catatan_penyetuju VARCHAR(255) NULL,
    
    dibuat_pada DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (id_anggota) REFERENCES Anggota(id) ON DELETE CASCADE,
    FOREIGN KEY (id_penyetuju) REFERENCES Anggota(id) ON DELETE NO ACTION
) ENGINE=InnoDB;

-- =============================================================
-- D. MODUL LAPORAN HARIAN 
-- =============================================================

-- 8. Tabel Laporan_Harian
CREATE TABLE IF NOT EXISTS Laporan_Harian (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_divisi INT NOT NULL,
    id_anggota INT NOT NULL,
    id_program_kerja INT NULL, 
    
    tanggal_laporan DATE NOT NULL,
    judul_kegiatan VARCHAR(200) NOT NULL,
    isi_laporan TEXT NOT NULL,
    url_lampiran VARCHAR(255) NULL,
    
    status_laporan ENUM('DRAFT', 'DISUBMIT') DEFAULT 'DRAFT',
    
    dibuat_pada DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (id_divisi) REFERENCES Divisi(id) ON DELETE NO ACTION, 
    FOREIGN KEY (id_anggota) REFERENCES Anggota(id) ON DELETE CASCADE,
    FOREIGN KEY (id_program_kerja) REFERENCES Program_Kerja(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- =============================================================
-- E. MODUL KEUANGAN
-- =============================================================

-- 9. Tabel Periode_Keuangan 
CREATE TABLE IF NOT EXISTS Periode_Keuangan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_divisi INT NOT NULL,
    
    bulan TINYINT NOT NULL CHECK (bulan BETWEEN 1 AND 12),
    tahun SMALLINT NOT NULL,
    
    id_penanggung_jawab INT NULL, 
    
    saldo_awal DECIMAL(15, 2) DEFAULT 0,
    dana_masuk DECIMAL(15, 2) DEFAULT 0,
    
    total_dana_tersedia DECIMAL(15, 2) DEFAULT 0, 
    total_pengeluaran DECIMAL(15, 2) DEFAULT 0,
    sisa_saldo DECIMAL(15, 2) DEFAULT 0,
    
    status_dokumen ENUM('DRAFT', 'DISUBMIT', 'DIKUNCI') DEFAULT 'DRAFT',
    
    dibuat_pada DATETIME DEFAULT CURRENT_TIMESTAMP,
    diupdate_pada DATETIME NULL,
    
    FOREIGN KEY (id_divisi) REFERENCES Divisi(id) ON DELETE CASCADE,
    FOREIGN KEY (id_penanggung_jawab) REFERENCES Anggota(id) ON DELETE SET NULL,
    
    CONSTRAINT UQ_Laporan_Bulanan UNIQUE (id_divisi, bulan, tahun)
) ENGINE=InnoDB;

-- 10. Tabel Transaksi_Pengeluaran
CREATE TABLE IF NOT EXISTS Transaksi_Pengeluaran (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_periode_keuangan INT NOT NULL,
    id_program_kerja INT NULL,
    
    tanggal_transaksi DATE NOT NULL,
    nama_kegiatan VARCHAR(200) NOT NULL,
    uraian_pengeluaran TEXT NOT NULL,
    
    volume DECIMAL(10, 2) DEFAULT 1,
    satuan VARCHAR(50) NOT NULL, 
    jumlah_rupiah DECIMAL(15, 2) NOT NULL, 
    
    -- Calculated Column for MySQL 5.7+
    total_nominal DECIMAL(15, 2) GENERATED ALWAYS AS (volume * jumlah_rupiah) STORED,
    
    keterangan VARCHAR(255) NULL,
    url_bukti_struk VARCHAR(255) NULL,
    
    dibuat_pada DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (id_periode_keuangan) REFERENCES Periode_Keuangan(id) ON DELETE CASCADE,
    FOREIGN KEY (id_program_kerja) REFERENCES Program_Kerja(id) ON DELETE NO ACTION
) ENGINE=InnoDB;

-- =============================================================
-- F. LOG AUDIT 
-- =============================================================

-- 11. Tabel Log_Audit
CREATE TABLE IF NOT EXISTS Log_Audit (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    id_anggota INT NULL,
    aksi VARCHAR(50) NOT NULL,    
    nama_tabel VARCHAR(50) NULL,
    id_record INT NULL,           
    isi_data TEXT NULL, 
    alamat_ip VARCHAR(45) NULL,
    waktu_kejadian DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =============================================================
-- G. SEEDING DATA
-- =============================================================

-- A. Insert Divisi 
INSERT INTO Divisi (nama_divisi, kode_divisi, deskripsi) VALUES 
('Organisasi', 'ORG', 'Bidang Organisasi'),
('Advokasi', 'ADV', 'Bidang Advokasi'),
('Pengembangan Sumber Daya Manusia', 'PSDM', 'Bidang Pengembangan SDM'),
('Kesejahteraan', 'KES', 'Bidang Kesejahteraan'),
('Publikasi dan Hubungan Antar Lembaga', 'PUB', 'Bidang Publikasi & Hubungan Antar Lembaga');

-- B. Insert Jabatan
-- 01. KETUA (BPH)
INSERT INTO Jabatan (nama_jabatan, id_divisi, level_akses) VALUES ('Ketua', NULL, 'BPH');

-- 02 - 06. WAKIL KETUA (KORBID)
INSERT INTO Jabatan (nama_jabatan, id_divisi, level_akses) VALUES 
('Wakil Ketua Bidang Organisasi', (SELECT id FROM Divisi WHERE kode_divisi='ORG'), 'KORBID'),
('Wakil Ketua Bidang Advokasi', (SELECT id FROM Divisi WHERE kode_divisi='ADV'), 'KORBID'),
('Wakil Ketua Bidang Pengembangan SDM', (SELECT id FROM Divisi WHERE kode_divisi='PSDM'), 'KORBID'),
('Wakil Ketua Bidang Kesejahteraan', (SELECT id FROM Divisi WHERE kode_divisi='KES'), 'KORBID'),
('Wakil Ketua Bidang Publikasi, Hubungan Antar Lembaga', (SELECT id FROM Divisi WHERE kode_divisi='PUB'), 'KORBID');

-- 07. SEKRETARIS (BPH)
INSERT INTO Jabatan (nama_jabatan, id_divisi, level_akses) VALUES ('Sekretaris', NULL, 'BPH');

-- 08 - 12. WAKIL SEKRETARIS (ANGGOTA)
INSERT INTO Jabatan (nama_jabatan, id_divisi, level_akses) VALUES 
('Wakil Sekretaris Bidang Organisasi', (SELECT id FROM Divisi WHERE kode_divisi='ORG'), 'ANGGOTA'),
('Wakil Sekretaris Bidang Advokasi', (SELECT id FROM Divisi WHERE kode_divisi='ADV'), 'ANGGOTA'),
('Wakil Sekretaris Bidang Pengembangan SDM', (SELECT id FROM Divisi WHERE kode_divisi='PSDM'), 'ANGGOTA'),
('Wakil Sekretaris Bidang Kesejahteraan', (SELECT id FROM Divisi WHERE kode_divisi='KES'), 'ANGGOTA'),
('Wakil Sekretaris Bidang Publikasi, Hubungan Antar Lembaga', (SELECT id FROM Divisi WHERE kode_divisi='PUB'), 'ANGGOTA');

-- 13 - 15. BENDAHARA (BPH)
INSERT INTO Jabatan (nama_jabatan, id_divisi, level_akses) VALUES 
('Bendahara', NULL, 'BPH'),
('Wakil Bendahara I', NULL, 'BPH'),
('Wakil Bendahara II', NULL, 'BPH');

-- C. Insert User Super Admin
INSERT INTO Anggota (nama_lengkap, username, password_hash, id_divisi, id_jabatan, string_kode_qr, status_aktif) 
SELECT 
    'Admin Sistem', 
    'admin', 
    '$2y$10$DUMMYHASHFORADMIN123', 
    NULL, 
    id, 
    'QR-ADMIN-SYS', 
    1
FROM Jabatan WHERE nama_jabatan = 'Sekretaris'
LIMIT 1;