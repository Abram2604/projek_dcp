<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('Pengaturan_Tanda_Tangan', function (Blueprint $table) {
            $table->string('path_ttd_kadis')->nullable()->after('kadis_nip');
            $table->string('path_ttd_ketua')->nullable()->after('ketua_nama');
            $table->string('path_ttd_sekretaris')->nullable()->after('sekretaris_nama');
        });
    }

    public function down(): void
    {
        Schema::table('Pengaturan_Tanda_Tangan', function (Blueprint $table) {
            $table->dropColumn(['path_ttd_kadis', 'path_ttd_ketua', 'path_ttd_sekretaris']);
        });
    }
};