<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksi_pasien', function (Blueprint $table) {
            $table->string('dokter')->nullable()->after('layanan_medis');
            $table->string('penjamin')->nullable()->after('dokter')->index();
        });
    }

    public function down(): void
    {
        Schema::table('transaksi_pasien', function (Blueprint $table) {
            $table->dropIndex(['penjamin']);
            $table->dropColumn(['dokter', 'penjamin']);
        });
    }
};
