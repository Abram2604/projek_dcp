<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('Laporan_Keuangan_Rekap', function (Blueprint $table) {
            // Menambahkan kolom volumes (JSON) setelah kolom assets
            // Kita set nullable() jaga-jaga agar data lama tidak error
            $table->json('volumes')->nullable()->after('assets');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('Laporan_Keuangan_Rekap', function (Blueprint $table) {
            // Hapus kolom jika di-rollback
            $table->dropColumn('volumes');
        });
    }
};