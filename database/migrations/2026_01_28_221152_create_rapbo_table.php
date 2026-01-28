<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('RAPBO', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_divisi');
            $table->integer('tahun_anggaran'); // misal 2026
            
            // Kolom sesuai Excel
            $table->string('uraian_kegiatan', 255); // Program Kerja Bidang
            $table->integer('mp')->default(1);
            $table->integer('thn')->default(1);
            $table->integer('frek')->default(1);
            $table->decimal('nominal_satuan', 15, 2)->default(0);
            
            // Kolom Total (MP * Thn * Frek * Nominal)
            // Kita hitung di controller/DB, tapi simpan juga biar cepat load
            $table->decimal('total_budget', 15, 2)->default(0); 
            
            $table->timestamps();

            $table->foreign('id_divisi')->references('id')->on('Divisi')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('RAPBO');
    }
};