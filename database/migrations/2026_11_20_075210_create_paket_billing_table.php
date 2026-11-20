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
        Schema::create('paket_billing', function (Blueprint $table) {
            $table->id('id_paket');
            $table->foreignId('id_zona')->constrained('zona', 'id_zona')->cascadeOnDelete();
            $table->string('nama_paket', 50);
            $table->integer('durasi_jam');
            $table->integer('harga_paket');
            $table->boolean('status_aktif')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paket_billing');
    }
};
