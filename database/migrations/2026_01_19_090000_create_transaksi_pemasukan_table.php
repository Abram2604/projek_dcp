<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Transaksi_Pemasukan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_periode_keuangan');
            $table->date('tanggal_transaksi');
            $table->string('sumber_dana', 200);
            $table->string('kategori_pemasukan', 100)->nullable();
            $table->decimal('jumlah_rupiah', 15, 2);
            $table->text('keterangan')->nullable();
            $table->string('url_bukti', 255)->nullable();
            $table->dateTime('dibuat_pada')->useCurrent();

            $table->foreign('id_periode_keuangan')
                ->references('id')
                ->on('Periode_Keuangan')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Transaksi_Pemasukan');
    }
};
