<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('Riwayat_Absensi', function (Blueprint $table) {
            // Tambahkan kolom diupdate_pada jika belum ada
            if (!Schema::hasColumn('Riwayat_Absensi', 'diupdate_pada')) {
                $table->dateTime('diupdate_pada')->nullable()->after('dibuat_pada');
            }
        });
    }

    public function down(): void
    {
        Schema::table('Riwayat_Absensi', function (Blueprint $table) {
            $table->dropColumn('diupdate_pada');
        });
    }
};