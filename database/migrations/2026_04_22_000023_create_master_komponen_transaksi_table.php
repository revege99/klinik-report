<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_komponen_transaksi', function (Blueprint $table) {
            $table->id();
            $table->string('kode_komponen', 20)->unique();
            $table->string('nama_komponen');
            $table->string('field_key', 100)->unique();
            $table->unsignedInteger('urutan_laporan')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $now = now();

        DB::table('master_komponen_transaksi')->insert([
            ['kode_komponen' => 'D1', 'nama_komponen' => 'Uang Daftar', 'field_key' => 'uang_daftar', 'urutan_laporan' => 1, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['kode_komponen' => 'D2', 'nama_komponen' => 'Uang Periksa', 'field_key' => 'uang_periksa', 'urutan_laporan' => 2, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['kode_komponen' => 'D3', 'nama_komponen' => 'Uang Obat', 'field_key' => 'uang_obat', 'urutan_laporan' => 3, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['kode_komponen' => 'D4', 'nama_komponen' => 'Uang Bersalin', 'field_key' => 'uang_bersalin', 'urutan_laporan' => 4, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['kode_komponen' => 'D5', 'nama_komponen' => 'Jasa Dokter', 'field_key' => 'jasa_dokter', 'urutan_laporan' => 5, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['kode_komponen' => 'D6', 'nama_komponen' => 'Rawat Inap', 'field_key' => 'rawat_inap', 'urutan_laporan' => 6, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['kode_komponen' => 'D7', 'nama_komponen' => 'Honor dr Visit', 'field_key' => 'honor_dr_visit', 'urutan_laporan' => 7, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['kode_komponen' => 'D8', 'nama_komponen' => 'Oksigen', 'field_key' => 'oksigen', 'urutan_laporan' => 8, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['kode_komponen' => 'D9', 'nama_komponen' => 'Perlengkapan Bayi', 'field_key' => 'perlengk_bayi', 'urutan_laporan' => 9, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['kode_komponen' => 'D10', 'nama_komponen' => 'Jaspel Nakes', 'field_key' => 'jaspel_nakes', 'urutan_laporan' => 10, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['kode_komponen' => 'D11', 'nama_komponen' => 'BMHP', 'field_key' => 'bmhp', 'urutan_laporan' => 11, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['kode_komponen' => 'D12', 'nama_komponen' => 'PKL', 'field_key' => 'pkl', 'urutan_laporan' => 12, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['kode_komponen' => 'D13', 'nama_komponen' => 'Lain-lain', 'field_key' => 'lain_lain', 'urutan_laporan' => 13, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('master_komponen_transaksi');
    }
};
