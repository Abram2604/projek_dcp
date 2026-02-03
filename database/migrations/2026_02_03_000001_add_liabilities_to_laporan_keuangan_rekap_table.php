<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void {
        Schema::table('Laporan_Keuangan_Rekap', function (Blueprint $table) {
            // Tambah kolom untuk Liabilities (Pengeluaran & Modal dari Laporan Posisi Keuangan)
            if (!Schema::hasColumn('Laporan_Keuangan_Rekap', 'liabilities')) {
                $table->json('liabilities')->nullable()->after('assets');
            }
        });
    }

    public function down(): void {
        Schema::table('Laporan_Keuangan_Rekap', function (Blueprint $table) {
            if (Schema::hasColumn('Laporan_Keuangan_Rekap', 'liabilities')) {
                $table->dropColumn('liabilities');
            }
        });
    }
};
