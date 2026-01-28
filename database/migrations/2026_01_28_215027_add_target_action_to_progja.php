<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('Program_Kerja', function (Blueprint $table) {
            $table->text('target')->nullable()->after('nama_program');
            $table->text('action')->nullable()->after('target');
        });
    }

    public function down(): void
    {
        Schema::table('Program_Kerja', function (Blueprint $table) {
            $table->dropColumn(['target', 'action']);
        });
    }
};