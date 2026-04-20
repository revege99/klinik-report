<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksi_pasien', function (Blueprint $table) {
            if (! Schema::hasColumn('transaksi_pasien', 'user_id')) {
                $table->foreignId('user_id')
                    ->nullable()
                    ->after('layanan_medis')
                    ->constrained()
                    ->nullOnDelete();
            }
        });

        Schema::table('pengeluaran', function (Blueprint $table) {
            if (! Schema::hasColumn('pengeluaran', 'user_id')) {
                $table->foreignId('user_id')
                    ->nullable()
                    ->after('master_kategori_pengeluaran_id')
                    ->constrained()
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('transaksi_pasien', function (Blueprint $table) {
            if (Schema::hasColumn('transaksi_pasien', 'user_id')) {
                $table->dropConstrainedForeignId('user_id');
            }
        });

        Schema::table('pengeluaran', function (Blueprint $table) {
            if (Schema::hasColumn('pengeluaran', 'user_id')) {
                $table->dropConstrainedForeignId('user_id');
            }
        });
    }
};
