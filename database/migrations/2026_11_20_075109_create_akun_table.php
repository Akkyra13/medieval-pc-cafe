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
        Schema::create('akun', function (Blueprint $table) {
            $table->id('id_akun');
            $table->foreignId('id_akses')->constrained('hak_akses', 'id_akses')->restrictOnDelete();
            $table->string('username', 20);
            $table->string('password', 255);
            $table->string('nama', 255);
            $table->string('notelp', 20)->nullable();
            $table->integer('saldo_deposit')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('akun');
    }
};
