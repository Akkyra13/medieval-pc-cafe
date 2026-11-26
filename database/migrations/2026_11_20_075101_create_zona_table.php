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
        Schema::create('zona', function (Blueprint $table) {
            $table->id('id_zona');
            $table->string('nama_zona', 20);
            $table->string('spek_cpu', 255)->nullable();
            $table->string('spek_gpu', 255)->nullable();
            $table->string('spek_ram', 255)->nullable();
            $table->string('spek_monitor', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zona');
    }
};
