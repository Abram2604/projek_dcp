<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Program Kerja
        Schema::create('Program_Kerja', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_divisi');
            $table->string('nama_program', 200);
            $table->text('deskripsi')->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->decimal('anggaran_rencana', 15, 2)->default(0);
            $table->enum('status_proker', ['RENCANA', 'BERJALAN', 'SELESAI', 'DITUNDA', 'DIBATALKAN'])->default('RENCANA');
            $table->tinyInteger('persen_progress')->default(0);
            $table->dateTime('dibuat_pada')->useCurrent();
            $table->unsignedBigInteger('dibuat_oleh')->nullable();

            $table->foreign('id_divisi')->references('id')->on('Divisi')->onDelete('cascade');
            $table->foreign('dibuat_oleh')->references('id')->on('Anggota');
        });

        // 2. Riwayat Absensi
        Schema::create('Riwayat_Absensi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_anggota');
            $table->date('tanggal');
            $table->time('jam_masuk')->nullable();
            $table->time('jam_pulang')->nullable();
            $table->enum('status_kehadiran', ['HADIR', 'DINAS', 'IZIN', 'SAKIT', 'ALPHA'])->default('HADIR');
            $table->enum('sumber_absensi', ['QR_DINDING', 'HP_MOBILE', 'INPUT_MANUAL', 'SYSTEM_GENERATED'])->default('QR_DINDING');
            $table->string('keterangan_tambahan', 255)->nullable();
            $table->dateTime('dibuat_pada')->useCurrent();
            $table->string('url_bukti', 255)->nullable();
            $table->foreign('id_anggota')->references('id')->on('Anggota')->onDelete('cascade');
            $table->unique(['id_anggota', 'tanggal'], 'UQ_Absensi_Harian');
        });

        // 3. Laporan Harian
        Schema::create('Laporan_Harian', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_divisi');
            $table->unsignedBigInteger('id_anggota');
            $table->unsignedBigInteger('id_program_kerja')->nullable();
            $table->date('tanggal_laporan');
            $table->string('judul_kegiatan', 200);
            $table->text('isi_laporan');
            $table->string('url_lampiran', 255)->nullable();
            $table->enum('status_laporan', ['DRAFT', 'DISUBMIT'])->default('DRAFT');
            $table->dateTime('dibuat_pada')->useCurrent();

            $table->foreign('id_divisi')->references('id')->on('Divisi');
            $table->foreign('id_anggota')->references('id')->on('Anggota')->onDelete('cascade');
            $table->foreign('id_program_kerja')->references('id')->on('Program_Kerja')->onDelete('set null');
        });

        // 4. Pengajuan Absensi
        Schema::create('Pengajuan_Absensi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_anggota');
            $table->enum('tipe_pengajuan', ['DINAS_DALAM', 'DINAS_LUAR', 'SAKIT', 'IZIN', 'CUTI']);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->string('lokasi_tujuan', 255)->nullable();
            $table->text('alasan');
            $table->string('url_lampiran', 255)->nullable();
            $table->enum('status_dokumen', ['DRAFT', 'DIAJUKAN', 'DISETUJUI', 'DITOLAK'])->default('DRAFT');
            $table->unsignedBigInteger('id_penyetuju')->nullable();
            $table->dateTime('waktu_validasi')->nullable();
            $table->string('catatan_penyetuju', 255)->nullable();
            $table->dateTime('dibuat_pada')->useCurrent();

            $table->foreign('id_anggota')->references('id')->on('Anggota')->onDelete('cascade');
            $table->foreign('id_penyetuju')->references('id')->on('Anggota');
        });

        // 5. Periode Keuangan
        Schema::create('Periode_Keuangan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_divisi');
            $table->tinyInteger('bulan');
            $table->smallInteger('tahun');
            $table->unsignedBigInteger('id_penanggung_jawab')->nullable();
            $table->decimal('saldo_awal', 15, 2)->default(0);
            $table->decimal('dana_masuk', 15, 2)->default(0);
            $table->decimal('total_dana_tersedia', 15, 2)->default(0);
            $table->decimal('total_pengeluaran', 15, 2)->default(0);
            $table->decimal('sisa_saldo', 15, 2)->default(0);
            $table->enum('status_dokumen', ['DRAFT', 'DISUBMIT', 'DIKUNCI'])->default('DRAFT');
            $table->dateTime('dibuat_pada')->useCurrent();
            $table->dateTime('diupdate_pada')->nullable();

            $table->foreign('id_divisi')->references('id')->on('Divisi')->onDelete('cascade');
            $table->foreign('id_penanggung_jawab')->references('id')->on('Anggota')->onDelete('set null');
            $table->unique(['id_divisi', 'bulan', 'tahun'], 'UQ_Laporan_Bulanan');
        });

        // 6. Transaksi Pengeluaran
        Schema::create('Transaksi_Pengeluaran', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_periode_keuangan');
            $table->unsignedBigInteger('id_program_kerja')->nullable();
            $table->date('tanggal_transaksi');
            $table->string('nama_kegiatan', 200);
            $table->text('uraian_pengeluaran');
            $table->decimal('volume', 10, 2)->default(1);
            $table->string('satuan', 50);
            $table->decimal('jumlah_rupiah', 15, 2);
            // Generated Column sesuai SQL
            $table->decimal('total_nominal', 15, 2)->virtualAs('volume * jumlah_rupiah');
            $table->string('keterangan', 255)->nullable();
            $table->string('url_bukti_struk', 255)->nullable();
            $table->dateTime('dibuat_pada')->useCurrent();

            $table->foreign('id_periode_keuangan')->references('id')->on('Periode_Keuangan')->onDelete('cascade');
            $table->foreign('id_program_kerja')->references('id')->on('Program_Kerja');
        });

        // 7. Log Audit
        Schema::create('Log_Audit', function (Blueprint $table) {
            $table->id();
            $table->integer('id_anggota')->nullable();
            $table->string('aksi', 50);
            $table->string('nama_tabel', 50)->nullable();
            $table->integer('id_record')->nullable();
            $table->text('isi_data')->nullable();
            $table->string('alamat_ip', 45)->nullable();
            $table->dateTime('waktu_kejadian')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Log_Audit');
        Schema::dropIfExists('Transaksi_Pengeluaran');
        Schema::dropIfExists('Periode_Keuangan');
        Schema::dropIfExists('Pengajuan_Absensi');
        Schema::dropIfExists('Laporan_Harian');
        Schema::dropIfExists('Riwayat_Absensi');
        Schema::dropIfExists('Program_Kerja');
    }
};