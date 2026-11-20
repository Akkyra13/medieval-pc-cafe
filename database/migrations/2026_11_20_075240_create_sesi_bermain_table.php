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
        Schema::create('sesi_bermain', function (Blueprint $table) {
            $table->id('id_sesi');
            $table->char('id_pc', 5);
            $table->foreignId('id_akun')->constrained('akun', 'id_akun')->cascadeOnDelete();
            $table->dateTime('waktu_login');
            $table->dateTime('waktu_logout')->nullable();
            $table->integer('durasi_terpakai_menit')->nullable();

            $table->foreign('id_pc')->references('id_pc')->on('pc')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sesi_bermain');
    }
};
