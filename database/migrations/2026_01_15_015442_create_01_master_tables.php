<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // --- A. Tabel Bawaan Laravel (Cek dulu sebelum buat) ---
        
        // 1. Users
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->rememberToken();
                $table->timestamps();
            });
        }

        // 2. Password Reset Tokens
        if (!Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }

        // 3. Failed Jobs
        if (!Schema::hasTable('failed_jobs')) {
            Schema::create('failed_jobs', function (Blueprint $table) {
                $table->id();
                $table->string('uuid')->unique();
                $table->text('connection');
                $table->text('queue');
                $table->longText('payload');
                $table->longText('exception');
                $table->timestamp('failed_at')->useCurrent();
            });
        }

        // 4. Personal Access Tokens (Ini yang bikin error tadi)
        if (!Schema::hasTable('personal_access_tokens')) {
            Schema::create('personal_access_tokens', function (Blueprint $table) {
                $table->id();
                $table->morphs('tokenable');
                $table->string('name');
                $table->string('token', 64)->unique();
                $table->text('abilities')->nullable();
                $table->timestamp('last_used_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
            });
        }

        // 5. Sessions
        if (!Schema::hasTable('sessions')) {
            Schema::create('sessions', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->foreignId('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index();
            });
        }

        // --- B. Tabel Master Aplikasi (Divisi & Jabatan) ---
        // Ini jarang konflik karena nama tabel custom, tapi kita kasih cek juga biar aman
        
        if (!Schema::hasTable('Divisi')) {
            Schema::create('Divisi', function (Blueprint $table) {
                $table->id();
                $table->string('nama_divisi', 100)->unique();
                $table->string('kode_divisi', 10)->nullable()->unique();
                $table->string('deskripsi', 255)->nullable();
                $table->dateTime('dibuat_pada')->useCurrent();
            });
        }

        if (!Schema::hasTable('Jabatan')) {
            Schema::create('Jabatan', function (Blueprint $table) {
                $table->id();
                $table->string('nama_jabatan', 100);
                $table->unsignedBigInteger('id_divisi')->nullable();
                $table->enum('level_akses', ['BPH', 'KORBID', 'ANGGOTA'])->default('ANGGOTA');
                $table->dateTime('dibuat_pada')->useCurrent();

                $table->foreign('id_divisi')->references('id')->on('Divisi')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        // Urutan drop dibalik (Child dulu baru Parent)
        Schema::dropIfExists('Jabatan');
        Schema::dropIfExists('Divisi');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('personal_access_tokens');
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};