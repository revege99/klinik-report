<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi_pasien_komponen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_pasien_id')->constrained('transaksi_pasien')->cascadeOnDelete();
            $table->foreignId('master_komponen_transaksi_id')->constrained('master_komponen_transaksi')->cascadeOnDelete();
            $table->decimal('nominal', 15, 2)->default(0);
            $table->timestamps();

            $table->unique(
                ['transaksi_pasien_id', 'master_komponen_transaksi_id'],
                'transaksi_pasien_komponen_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_pasien_komponen');
    }
};
