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
        Schema::create('pc', function (Blueprint $table) {
            $table->char('id_pc', 5)->primary();
            $table->foreignId('id_zona')->constrained('zona', 'id_zona')->restrictOnDelete();
            $table->string('ip_address', 15)->nullable();
            $table->string('status_pc', 20)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pc');
    }
};
