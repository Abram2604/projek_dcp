<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Anggota', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lengkap', 150);
            $table->string('username', 50)->unique();
            $table->string('password_hash', 255);
            $table->string('email', 100)->nullable();
            $table->string('remember_token', 100)->nullable();
            $table->string('nomor_hp', 20)->nullable();
            $table->unsignedBigInteger('id_divisi')->nullable();
            $table->unsignedBigInteger('id_jabatan')->nullable();
            $table->string('string_kode_qr', 100)->nullable()->unique();
            $table->string('foto_profil', 255)->nullable();
            $table->boolean('status_aktif')->default(1);
            $table->dateTime('terakhir_login')->nullable();
            $table->dateTime('dibuat_pada')->useCurrent();
            $table->dateTime('diupdate_pada')->nullable();

            $table->foreign('id_divisi')->references('id')->on('Divisi')->onDelete('set null');
            $table->foreign('id_jabatan')->references('id')->on('Jabatan')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Anggota');
    }
};