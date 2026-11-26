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
        Schema::create('detail_transaksi', function (Blueprint $table) {
            $table->id('id_detail');
            $table->string('id_transaksi', 10);
            $table->foreign('id_transaksi')->references('id_transaksi')->on('transaksi')->cascadeOnDelete();
            $table->foreignId('id_paket')->constrained('paket','id_paket')->restrictOnDelete();
            $table->integer('qty')->default(1);
            $table->integer('durasi_beli_menit');
            $table->integer('subtotal_harga');


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_transaksi');
    }
};
