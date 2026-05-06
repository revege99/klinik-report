<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_komponen_transaksi', function (Blueprint $table) {
            $table->string('arah_laporan', 10)
                ->default('debet')
                ->after('field_key');
        });

        DB::table('master_komponen_transaksi')
            ->whereNull('arah_laporan')
            ->orWhere('arah_laporan', '')
            ->update(['arah_laporan' => 'debet']);
    }

    public function down(): void
    {
        Schema::table('master_komponen_transaksi', function (Blueprint $table) {
            $table->dropColumn('arah_laporan');
        });
    }
};
