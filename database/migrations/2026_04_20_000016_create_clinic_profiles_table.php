<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clinic_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('nama_klinik');
            $table->string('nama_pendek')->nullable();
            $table->string('tagline')->nullable();
            $table->text('alamat')->nullable();
            $table->string('kota')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('kode_pos', 20)->nullable();
            $table->string('telepon', 50)->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('penanggung_jawab')->nullable();
            $table->string('jam_pelayanan')->nullable();
            $table->text('deskripsi_singkat')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinic_profiles');
    }
};
