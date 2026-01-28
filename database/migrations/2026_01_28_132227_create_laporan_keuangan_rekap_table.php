<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void {
    Schema::create('Laporan_Keuangan_Rekap', function (Blueprint $table) {
        $table->id();
        $table->integer('bulan');
        $table->integer('tahun');
        // Masukkan semua field dari data React tadi dalam bentuk JSON atau Kolom
        $table->decimal('saldo_awal', 15, 2)->default(0);
        $table->json('income_cos');      // Simpan {kiic: 0, kim: 0, ...}
        $table->json('income_non_cos');
        $table->json('expenses');
        $table->json('assets');
        $table->timestamps();
        $table->unique(['bulan', 'tahun']);
    });
}
};
