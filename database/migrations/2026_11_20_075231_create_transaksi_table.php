<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->string('id_transaksi')->primary();
            $table->foreignId('id_akun_pelanggan')->constrained('akun', 'id_akun')->restrictOnDelete();
            $table->foreignId('id_akun_kasir')->nullable()->constrained('akun', 'id_akun')->nullOnDelete();
            $table->dateTime('waktu_transaksi');
            $table->integer('total_bayar');
            $table->string('metode_pembayaran', 20)->nullable();
            $table->string('status_pembayaran', 15)->nullable();
            $table->string('kode_referensi', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
