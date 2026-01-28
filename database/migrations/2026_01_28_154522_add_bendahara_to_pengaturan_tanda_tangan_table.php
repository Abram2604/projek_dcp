<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('Pengaturan_Tanda_Tangan', function (Blueprint $table) {
        $table->string('bendahara_nama')->nullable()->after('sekretaris_nama');
    });
}

public function down()
{
    Schema::table('Pengaturan_Tanda_Tangan', function (Blueprint $table) {
        $table->dropColumn('bendahara_nama');
    });
}

};
