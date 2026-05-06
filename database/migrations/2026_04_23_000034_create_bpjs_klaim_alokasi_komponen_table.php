<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bpjs_klaim_alokasi_komponen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bpjs_klaim_bulanan_id')->constrained('bpjs_klaim_bulanan')->cascadeOnDelete();
            $table->foreignId('master_komponen_transaksi_id')->nullable()->constrained('master_komponen_transaksi')->nullOnDelete();
            $table->string('kode_komponen', 20);
            $table->string('nama_komponen');
            $table->decimal('basis_nominal', 15, 2)->default(0);
            $table->decimal('persentase', 10, 4)->default(0);
            $table->decimal('nominal_alokasi', 15, 2)->default(0);
            $table->boolean('basis_pajak_obat')->default(false);
            $table->unsignedInteger('urutan_laporan')->default(0);
            $table->timestamps();

            $table->unique(
                ['bpjs_klaim_bulanan_id', 'master_komponen_transaksi_id'],
                'bpjs_klaim_alokasi_komponen_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bpjs_klaim_alokasi_komponen');
    }
};
