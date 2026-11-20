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
        Schema::create('saldo_waktu', function (Blueprint $table) {
            $table->foreignId('id_akun')->constrained('akun', 'id_akun')->cascadeOnDelete();
            $table->foreignId('id_zona')->constrained('zona', 'id_zona')->cascadeOnDelete();
            $table->integer('sisa_waktu_menit')->default(0);

            $table->primary(['id_akun', 'id_zona']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saldo_waktu');
    }
};
