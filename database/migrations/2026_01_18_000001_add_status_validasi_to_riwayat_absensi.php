<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('Riwayat_Absensi', function (Blueprint $table) {
            // Kolom untuk status persetujuan
            if (!Schema::hasColumn('Riwayat_Absensi', 'status_validasi')) {
                // Default APPROVED agar data lama dianggap sah
                $table->enum('status_validasi', ['PENDING', 'APPROVED', 'REJECTED'])
                      ->default('APPROVED') 
                      ->after('status_kehadiran');
            }
            
            // Kolom siapa yang memvalidasi (Opsional, untuk audit trail)
            if (!Schema::hasColumn('Riwayat_Absensi', 'divalidasi_oleh')) {
                $table->unsignedBigInteger('divalidasi_oleh')->nullable()->after('status_validasi');
            }
        });
    }

    public function down(): void
    {
        Schema::table('Riwayat_Absensi', function (Blueprint $table) {
            $table->dropColumn(['status_validasi', 'divalidasi_oleh']);
        });
    }
};