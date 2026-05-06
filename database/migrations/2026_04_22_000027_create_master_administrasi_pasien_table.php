<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_administrasi_pasien', function (Blueprint $table) {
            $table->id();
            $table->string('kode_administrasi', 20)->unique();
            $table->string('nama_administrasi');
            $table->string('field_key', 100)->unique();
            $table->string('arah_laporan', 10)->default('debet');
            $table->unsignedInteger('urutan_laporan')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $now = now();

        DB::table('master_administrasi_pasien')->insert([
            ['kode_administrasi' => 'A1', 'nama_administrasi' => 'Utang Pasien', 'field_key' => 'utang_pasien', 'arah_laporan' => 'debet', 'urutan_laporan' => 1, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['kode_administrasi' => 'A2', 'nama_administrasi' => 'Utang', 'field_key' => 'utang', 'arah_laporan' => 'kredit', 'urutan_laporan' => 2, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['kode_administrasi' => 'A3', 'nama_administrasi' => 'Bayar Utang Pasien', 'field_key' => 'bayar_utang_pasien', 'arah_laporan' => 'debet', 'urutan_laporan' => 3, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['kode_administrasi' => 'A4', 'nama_administrasi' => 'Derma & Solidaritas', 'field_key' => 'derma_solidaritas', 'arah_laporan' => 'kredit', 'urutan_laporan' => 4, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['kode_administrasi' => 'A5', 'nama_administrasi' => 'Saldo Kredit', 'field_key' => 'saldo_kredit', 'arah_laporan' => 'kredit', 'urutan_laporan' => 5, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['kode_administrasi' => 'A6', 'nama_administrasi' => 'Saldo', 'field_key' => 'saldo', 'arah_laporan' => 'debet', 'urutan_laporan' => 6, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('master_administrasi_pasien');
    }
};
