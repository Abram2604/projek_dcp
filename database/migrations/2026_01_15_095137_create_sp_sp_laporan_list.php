<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Hapus SP lama jika ada (untuk menghindari error 'already exists')
        DB::unprepared("DROP PROCEDURE IF EXISTS `sp_laporan_list`");

        // 2. Buat SP Baru
        DB::unprepared("
CREATE PROCEDURE `sp_laporan_list` (
    IN `p_level_akses` VARCHAR(10), 
    IN `p_id_divisi` INT, 
    IN `p_search` VARCHAR(200), 
    IN `p_start_date` DATE, 
    IN `p_end_date` DATE, 
    IN `p_filter_divisi` INT
) 
BEGIN 
    SELECT
        l.id,
        l.tanggal_laporan,
        l.judul_kegiatan,
        l.isi_laporan,
        l.status_laporan,
        l.url_lampiran,
        l.id_divisi,
        d.nama_divisi,
        d.kode_divisi,
        l.id_anggota,
        a.nama_lengkap,
        l.id_program_kerja,
        pk.nama_program,
        COALESCE(SUM(tp.total_nominal), 0) AS total_pengeluaran
    FROM Laporan_Harian l
    JOIN Divisi d ON l.id_divisi = d.id
    JOIN Anggota a ON l.id_anggota = a.id
    LEFT JOIN Program_Kerja pk ON l.id_program_kerja = pk.id
    LEFT JOIN Transaksi_Pengeluaran tp
        ON tp.tanggal_transaksi = l.tanggal_laporan
        AND tp.nama_kegiatan = l.judul_kegiatan
        AND (tp.id_program_kerja <=> l.id_program_kerja)
    WHERE (p_level_akses = 'BPH' OR l.id_divisi = p_id_divisi)
        AND (p_filter_divisi IS NULL OR p_filter_divisi = 0 OR l.id_divisi = p_filter_divisi)
        AND (p_start_date IS NULL OR l.tanggal_laporan >= p_start_date)
        AND (p_end_date IS NULL OR l.tanggal_laporan <= p_end_date)
        AND (
            p_search IS NULL OR p_search = '' OR
            -- PERBAIKAN DISINI: Paksa Collation agar cocok
            l.judul_kegiatan LIKE CONCAT('%', p_search, '%') COLLATE utf8mb4_unicode_ci OR
            l.isi_laporan LIKE CONCAT('%', p_search, '%') COLLATE utf8mb4_unicode_ci OR
            a.nama_lengkap LIKE CONCAT('%', p_search, '%') COLLATE utf8mb4_unicode_ci
        )
    GROUP BY
        l.id, l.tanggal_laporan, l.judul_kegiatan, l.isi_laporan, l.status_laporan,
        l.url_lampiran, l.id_divisi, d.nama_divisi, d.kode_divisi, l.id_anggota,
        a.nama_lengkap, l.id_program_kerja, pk.nama_program
    ORDER BY l.tanggal_laporan DESC, l.id DESC;
END
        ");
    }

    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS `sp_laporan_list`");
    }
};
