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
        Schema::create('master_layanan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_layanan', 50)->unique();
            $table->string('nama_layanan');
            $table->unsignedInteger('urutan_laporan')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_layanan');
    }
};
