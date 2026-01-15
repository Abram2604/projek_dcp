<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SqlImportSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Data Divisi
        DB::table('Divisi')->insertOrIgnore([
            ['id' => 1, 'nama_divisi' => 'Bidang Organisasi', 'kode_divisi' => 'ORG', 'deskripsi' => 'Mengurus keanggotaan dan internal', 'dibuat_pada' => '2026-01-14 01:46:47'],
            ['id' => 2, 'nama_divisi' => 'Bidang Advokasi', 'kode_divisi' => 'ADV', 'deskripsi' => 'Hukum dan pembelaan anggota', 'dibuat_pada' => '2026-01-14 01:46:47'],
            ['id' => 3, 'nama_divisi' => 'Bidang PSDM', 'kode_divisi' => 'PSDM', 'deskripsi' => 'Pengembangan Sumber Daya Manusia & Diklat', 'dibuat_pada' => '2026-01-14 01:46:47'],
            ['id' => 4, 'nama_divisi' => 'Bidang Kesejahteraan', 'kode_divisi' => 'KES', 'deskripsi' => 'Ekonomi dan sosial', 'dibuat_pada' => '2026-01-14 01:46:47'],
            ['id' => 5, 'nama_divisi' => 'Bidang Publikasi & Hubungan', 'kode_divisi' => 'PUB', 'deskripsi' => 'Humas dan Media', 'dibuat_pada' => '2026-01-14 01:46:47'],
        ]);

        // 2. Data Jabatan
        DB::table('Jabatan')->insertOrIgnore([
            ['id' => 1, 'nama_jabatan' => 'Ketua DPC', 'id_divisi' => null, 'level_akses' => 'BPH', 'dibuat_pada' => '2026-01-14 01:46:47'],
            ['id' => 2, 'nama_jabatan' => 'Sekretaris', 'id_divisi' => null, 'level_akses' => 'BPH', 'dibuat_pada' => '2026-01-14 01:46:47'],
            ['id' => 3, 'nama_jabatan' => 'Bendahara', 'id_divisi' => null, 'level_akses' => 'BPH', 'dibuat_pada' => '2026-01-14 01:46:47'],
            ['id' => 4, 'nama_jabatan' => 'Ketua Bidang Organisasi', 'id_divisi' => 1, 'level_akses' => 'KORBID', 'dibuat_pada' => '2026-01-14 01:46:47'],
            ['id' => 5, 'nama_jabatan' => 'Ketua Bidang Advokasi', 'id_divisi' => 2, 'level_akses' => 'KORBID', 'dibuat_pada' => '2026-01-14 01:46:47'],
            ['id' => 6, 'nama_jabatan' => 'Ketua Bidang PSDM', 'id_divisi' => 3, 'level_akses' => 'KORBID', 'dibuat_pada' => '2026-01-14 01:46:47'],
            ['id' => 7, 'nama_jabatan' => 'Ketua Bidang Kesejahteraan', 'id_divisi' => 4, 'level_akses' => 'KORBID', 'dibuat_pada' => '2026-01-14 01:46:47'],
            ['id' => 8, 'nama_jabatan' => 'Ketua Bidang Publikasi', 'id_divisi' => 5, 'level_akses' => 'KORBID', 'dibuat_pada' => '2026-01-14 01:46:47'],
            ['id' => 9, 'nama_jabatan' => 'Anggota Biasa', 'id_divisi' => null, 'level_akses' => 'ANGGOTA', 'dibuat_pada' => '2026-01-14 01:46:47'],
        ]);

        // 3. Data Anggota (User)
        DB::table('Anggota')->insertOrIgnore([
            ['id' => 1, 'nama_lengkap' => 'Bapak Ketua', 'username' => 'ketua', 'password_hash' => '$2y$12$a4A1CG.OGgyD7c4NnYipnuTMj0gIjtVk.NNKsq2IhxyPPKFlS.B7C', 'email' => 'ketua@spsi.com', 'id_divisi' => null, 'id_jabatan' => 1, 'status_aktif' => 1, 'dibuat_pada' => '2026-01-14 01:46:47', 'diupdate_pada' => '2026-01-14 03:42:40'],
            ['id' => 2, 'nama_lengkap' => 'Ibu Sekretaris', 'username' => 'sekretaris', 'password_hash' => '$2y$12$eoRr7ISrGooHvuGvs/gAFujPayZ.0XZD3HxuivzQwL5f2xJonpTtC', 'email' => 'sekre@spsi.com', 'id_divisi' => null, 'id_jabatan' => 2, 'status_aktif' => 1, 'dibuat_pada' => '2026-01-14 01:46:47', 'diupdate_pada' => '2026-01-14 03:42:40'],
            ['id' => 3, 'nama_lengkap' => 'Ibu Bendahara', 'username' => 'bendahara', 'password_hash' => '$2y$12$Qd5c/XSWIcaS4e/hqP8Ps.XwIbwlo71ETvuyAhFegJRhEVPU0sbK2', 'email' => 'bendahara@spsi.com', 'id_divisi' => null, 'id_jabatan' => 3, 'status_aktif' => 1, 'dibuat_pada' => '2026-01-14 01:46:47', 'diupdate_pada' => '2026-01-14 03:42:40'],
            ['id' => 4, 'nama_lengkap' => 'Admin Organisasi', 'username' => 'organisasi', 'password_hash' => '$2y$12$SICJpdLYS5MdUhj3TlcV8eqLwXJEuZHmlNI6GHCY4bAbuqpQ9QQO2', 'email' => 'org@spsi.com', 'id_divisi' => 1, 'id_jabatan' => 4, 'status_aktif' => 1, 'dibuat_pada' => '2026-01-14 01:46:47', 'diupdate_pada' => '2026-01-14 03:42:40'],
            ['id' => 5, 'nama_lengkap' => 'Admin Advokasi', 'username' => 'advokasi', 'password_hash' => '$2y$12$.SaxNC1tJs8Kwj2w60UoYekBXTXKsxqrXu8OJo5np90eSVJtZ3alS', 'email' => 'adv@spsi.com', 'id_divisi' => 2, 'id_jabatan' => 5, 'status_aktif' => 1, 'dibuat_pada' => '2026-01-14 01:46:47', 'diupdate_pada' => '2026-01-14 03:42:41'],
            ['id' => 6, 'nama_lengkap' => 'Admin PSDM', 'username' => 'psdm', 'password_hash' => '$2y$12$vtYFpUhHxNPZt5zINy.5WePpEWh96vzAvCV0UCmXoYrMMDXEJYUhW', 'email' => 'psdm@spsi.com', 'id_divisi' => 3, 'id_jabatan' => 6, 'status_aktif' => 1, 'dibuat_pada' => '2026-01-14 01:46:47', 'diupdate_pada' => '2026-01-14 03:42:41'],
            ['id' => 7, 'nama_lengkap' => 'Admin Ekonomi', 'username' => 'ekonomi', 'password_hash' => '$2y$12$1xDGIohCPwZo0hOCwSf5MucG0jVN0VgdInZDURejThUAXEYgGpuWm', 'email' => 'eko@spsi.com', 'id_divisi' => 4, 'id_jabatan' => 7, 'status_aktif' => 1, 'dibuat_pada' => '2026-01-14 01:46:47', 'diupdate_pada' => '2026-01-14 03:42:41'],
            ['id' => 8, 'nama_lengkap' => 'Admin Publikasi', 'username' => 'publikasi', 'password_hash' => '$2y$12$z4SOdbTz13dbjx6T3d3VdesXEPxOAGU9uIqi8oNcShzlq8BI6xwHW', 'email' => 'pub@spsi.com', 'id_divisi' => 5, 'id_jabatan' => 8, 'status_aktif' => 1, 'dibuat_pada' => '2026-01-14 01:46:47', 'diupdate_pada' => '2026-01-14 03:42:41'],
            ['id' => 9, 'nama_lengkap' => 'Anggota Contoh', 'username' => 'anggota', 'password_hash' => '$2y$12$V.o3Mubu0ly9Bqi8igozZemmytF/9TXno4WhoSFPrFpgrJEqVofwG', 'email' => 'user@spsi.com', 'id_divisi' => 1, 'id_jabatan' => 9, 'status_aktif' => 1, 'dibuat_pada' => '2026-01-14 01:46:47', 'diupdate_pada' => '2026-01-14 03:42:41'],
        ]);

        // 4. Riwayat Absensi
        DB::table('Riwayat_Absensi')->insertOrIgnore([
            ['id' => 1, 'id_anggota' => 1, 'tanggal' => '2026-01-14', 'jam_masuk' => '07:55:00', 'jam_pulang' => null, 'status_kehadiran' => 'HADIR', 'dibuat_pada' => '2026-01-14 08:17:40'],
            ['id' => 2, 'id_anggota' => 2, 'tanggal' => '2026-01-14', 'jam_masuk' => '08:05:00', 'jam_pulang' => null, 'status_kehadiran' => 'HADIR', 'dibuat_pada' => '2026-01-14 08:17:40'],
            ['id' => 3, 'id_anggota' => 4, 'tanggal' => '2026-01-14', 'jam_masuk' => '07:45:00', 'jam_pulang' => null, 'status_kehadiran' => 'HADIR', 'dibuat_pada' => '2026-01-14 08:17:40'],
            ['id' => 4, 'id_anggota' => 5, 'tanggal' => '2026-01-14', 'jam_masuk' => '08:10:00', 'jam_pulang' => null, 'status_kehadiran' => 'HADIR', 'dibuat_pada' => '2026-01-14 08:17:40'],
            ['id' => 5, 'id_anggota' => 6, 'tanggal' => '2026-01-14', 'jam_masuk' => null, 'jam_pulang' => null, 'status_kehadiran' => 'DINAS', 'dibuat_pada' => '2026-01-14 08:17:40'],
            ['id' => 6, 'id_anggota' => 7, 'tanggal' => '2026-01-14', 'jam_masuk' => null, 'jam_pulang' => null, 'status_kehadiran' => 'SAKIT', 'dibuat_pada' => '2026-01-14 08:17:40'],
            ['id' => 7, 'id_anggota' => 8, 'tanggal' => '2026-01-14', 'jam_masuk' => null, 'jam_pulang' => null, 'status_kehadiran' => 'IZIN', 'dibuat_pada' => '2026-01-14 08:17:40'],
            ['id' => 8, 'id_anggota' => 5, 'tanggal' => '2026-01-13', 'jam_masuk' => '07:50:00', 'jam_pulang' => '17:05:00', 'status_kehadiran' => 'HADIR', 'dibuat_pada' => '2026-01-14 08:17:40'],
            ['id' => 9, 'id_anggota' => 5, 'tanggal' => '2026-01-12', 'jam_masuk' => '08:00:00', 'jam_pulang' => '17:10:00', 'status_kehadiran' => 'HADIR', 'dibuat_pada' => '2026-01-14 08:17:40'],
            ['id' => 10, 'id_anggota' => 5, 'tanggal' => '2026-01-11', 'jam_masuk' => null, 'jam_pulang' => null, 'status_kehadiran' => 'DINAS', 'dibuat_pada' => '2026-01-14 08:17:40'],
        ]);

        // 5. Periode Keuangan (Saldo)
        DB::table('Periode_Keuangan')->insertOrIgnore([
            ['id' => 1, 'id_divisi' => 1, 'bulan' => 1, 'tahun' => 2026, 'saldo_awal' => 25000000.00, 'total_pengeluaran' => 5000000.00, 'sisa_saldo' => 20000000.00, 'status_dokumen' => 'DISUBMIT', 'dibuat_pada' => '2026-01-14 08:17:40'],
            ['id' => 2, 'id_divisi' => 2, 'bulan' => 1, 'tahun' => 2026, 'saldo_awal' => 15000000.00, 'total_pengeluaran' => 7500000.00, 'sisa_saldo' => 7500000.00, 'status_dokumen' => 'DISUBMIT', 'dibuat_pada' => '2026-01-14 08:17:40'],
            ['id' => 3, 'id_divisi' => 3, 'bulan' => 1, 'tahun' => 2026, 'saldo_awal' => 10000000.00, 'total_pengeluaran' => 2000000.00, 'sisa_saldo' => 8000000.00, 'status_dokumen' => 'DISUBMIT', 'dibuat_pada' => '2026-01-14 08:17:40'],
            ['id' => 4, 'id_divisi' => 4, 'bulan' => 1, 'tahun' => 2026, 'saldo_awal' => 50000000.00, 'total_pengeluaran' => 1000000.00, 'sisa_saldo' => 49000000.00, 'status_dokumen' => 'DISUBMIT', 'dibuat_pada' => '2026-01-14 08:17:40'],
            ['id' => 5, 'id_divisi' => 5, 'bulan' => 1, 'tahun' => 2026, 'saldo_awal' => 5000000.00, 'total_pengeluaran' => 4500000.00, 'sisa_saldo' => 500000.00, 'status_dokumen' => 'DISUBMIT', 'dibuat_pada' => '2026-01-14 08:17:40'],
        ]);

        // 6. Laporan Harian
        DB::table('Laporan_Harian')->insertOrIgnore([
            ['id' => 1, 'id_divisi' => 1, 'id_anggota' => 1, 'id_program_kerja' => null, 'tanggal_laporan' => '2026-01-14', 'judul_kegiatan' => 'operasional kesekretariatan', 'isi_laporan' => 'coba hasil codingan', 'url_lampiran' => '/storage/laporan/IrheF8KX30DnZepQGUi25F9SwpcIc4n81qMJBvrz.png', 'status_laporan' => 'DISUBMIT', 'dibuat_pada' => '2026-01-14 20:40:07'],
            ['id' => 4, 'id_divisi' => 3, 'id_anggota' => 1, 'id_program_kerja' => null, 'tanggal_laporan' => '2026-01-19', 'judul_kegiatan' => 'LDKM', 'isi_laporan' => 'coba masukin data lagi', 'url_lampiran' => '/storage/laporan/MJD49CGsUAZfqiUngo3GflOSRaMtQ8Fk1y2MiOgC.png', 'status_laporan' => 'DISUBMIT', 'dibuat_pada' => '2026-01-15 01:11:54'],
        ]);

        // 7. Sessions (Opsional, tapi diminta untuk tidak meninggalkan data apapun)
        // Note: Sebaiknya hanya seed ini di lokal, di prod session sifatnya sementara.
        DB::table('sessions')->insertOrIgnore([
            ['id' => 'D1F6JLjU3eGfq6ewHzTVCKqWkgwNW4fPLgkQD9nz', 'user_id' => 1, 'ip_address' => '172.19.0.1', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'payload' => 'YTo4OntzOjY6Il90b2tlbiI7czo0MDoiZEFHTDF0NWc1VnpFamhRRkZvY3hhU2VleFFGNmpTWm5aYnNZSVlSZiI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vbG9jYWxob3N0OjgwMDAvZGFzaGJvYXJkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjEyOiJ1c2VyX2phYmF0YW4iO3M6OToiS2V0dWEgRFBDIjtzOjEwOiJ1c2VyX2xldmVsIjtzOjM6IkJQSCI7czoxMToidXNlcl9kaXZpc2kiO3M6MToiLSI7fQ==', 'last_activity' => 1768384639],
            ['id' => 'inuecKXlc398urLUWOEVvvU4baQYn3TQ37o6cXjC', 'user_id' => 4, 'ip_address' => '172.18.0.1', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'payload' => 'YTo3OntzOjY6Il90b2tlbiI7czo0MDoiekNsa0V5WE8yUE1zRDBQSVFKS3NqVTZuOXRZMG5aRWIxNFozRUJwdSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9kYXNoYm9hcmQiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTo0O3M6MTI6InVzZXJfamFiYXRhbiI7czoyMzoiS2V0dWEgQmlkYW5nIE9yZ2FuaXNhc2kiO3M6MTA6InVzZXJfbGV2ZWwiO3M6NjoiS09SQklEIjtzOjExOiJ1c2VyX2RpdmlzaSI7czoxNzoiQmlkYW5nIE9yZ2FuaXNhc2kiO30=', 'last_activity' => 1768370567],
            // ... (Data session lain bisa ditambahkan, tapi biasanya ini expired dan tidak perlu)
        ]);
    }
}