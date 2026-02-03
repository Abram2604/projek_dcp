<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void {
        Schema::table('Laporan_Keuangan_Rekap', function (Blueprint $table) {
            // Tambah kolom untuk Laporan Organisasi (Summary)
            if (!Schema::hasColumn('Laporan_Keuangan_Rekap', 'pemasukan_cos')) {
                $table->decimal('pemasukan_cos', 15, 2)->default(0)->after('saldo_awal');
            }
            if (!Schema::hasColumn('Laporan_Keuangan_Rekap', 'pemasukan_non_cos')) {
                $table->decimal('pemasukan_non_cos', 15, 2)->default(0)->after('pemasukan_cos');
            }
            if (!Schema::hasColumn('Laporan_Keuangan_Rekap', 'pengeluaran_ops')) {
                $table->decimal('pengeluaran_ops', 15, 2)->default(0)->after('pemasukan_non_cos');
            }
            if (!Schema::hasColumn('Laporan_Keuangan_Rekap', 'pengeluaran_event')) {
                $table->decimal('pengeluaran_event', 15, 2)->default(0)->after('pengeluaran_ops');
            }
            if (!Schema::hasColumn('Laporan_Keuangan_Rekap', 'pengeluaran_sekretariat')) {
                $table->decimal('pengeluaran_sekretariat', 15, 2)->default(0)->after('pengeluaran_event');
            }
            if (!Schema::hasColumn('Laporan_Keuangan_Rekap', 'pengeluaran_insentif')) {
                $table->decimal('pengeluaran_insentif', 15, 2)->default(0)->after('pengeluaran_sekretariat');
            }
        });
    }

    public function down(): void {
        Schema::table('Laporan_Keuangan_Rekap', function (Blueprint $table) {
            $table->dropColumn([
                'pemasukan_cos',
                'pemasukan_non_cos',
                'pengeluaran_ops',
                'pengeluaran_event',
                'pengeluaran_sekretariat',
                'pengeluaran_insentif'
            ]);
        });
    }
};
