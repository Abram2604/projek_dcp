<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Notifikasi', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('id_anggota'); 
            $table->string('judul', 100); // Contoh: "Laporan Masuk"
            $table->text('pesan');        // Isi detail notifikasi
            $table->enum('tipe', ['info', 'success', 'warning', 'alert'])->default('info');
            $table->boolean('is_read')->default(0); 
            $table->string('link_url', 255)->nullable(); 
            $table->dateTime('dibuat_pada')->useCurrent();
            $table->dateTime('diupdate_pada')->nullable();
            $table->foreign('id_anggota')->references('id')->on('Anggota')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Notifikasi');
    }
};