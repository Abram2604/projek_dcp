<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('Pengaturan_Tanda_Tangan', function (Blueprint $table) {
            // 1. Cek Bendahara Nama
            if (!Schema::hasColumn('Pengaturan_Tanda_Tangan', 'bendahara_nama')) {
                $table->string('bendahara_nama')->nullable()->after('sekretaris_nama');
            }

            // 2. Cek Path TTD Ketua
            if (!Schema::hasColumn('Pengaturan_Tanda_Tangan', 'path_ttd_ketua')) {
                $table->string('path_ttd_ketua')->nullable()->after('ketua_nama');
            }

            // 3. Cek Path TTD Sekretaris
            if (!Schema::hasColumn('Pengaturan_Tanda_Tangan', 'path_ttd_sekretaris')) {
                $table->string('path_ttd_sekretaris')->nullable()->after('sekretaris_nama');
            }

            // 4. Cek Path TTD Bendahara (Ini yang kemungkinan besar belum ada)
            if (!Schema::hasColumn('Pengaturan_Tanda_Tangan', 'path_ttd_bendahara')) {
                $table->string('path_ttd_bendahara')->nullable()->after('bendahara_nama');
            }
        });
    }

    public function down(): void
    {
        Schema::table('Pengaturan_Tanda_Tangan', function (Blueprint $table) {
            // Hapus hanya kolom yang kita yakini baru ditambahkan di file ini
            if (Schema::hasColumn('Pengaturan_Tanda_Tangan', 'path_ttd_bendahara')) {
                $table->dropColumn('path_ttd_bendahara');
            }
        });
    }
};