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
            $table->boolean('ikut_alokasi_bpjs')
                ->default(true)
                ->after('arah_laporan');
            $table->boolean('basis_pajak_obat')
                ->default(false)
                ->after('ikut_alokasi_bpjs');
            $table->string('peran_sistem', 40)
                ->nullable()
                ->after('basis_pajak_obat');
        });

        DB::table('master_komponen_transaksi')
            ->where('field_key', 'uang_obat')
            ->update(['basis_pajak_obat' => true]);
    }

    public function down(): void
    {
        Schema::table('master_komponen_transaksi', function (Blueprint $table) {
            $table->dropColumn([
                'ikut_alokasi_bpjs',
                'basis_pajak_obat',
                'peran_sistem',
            ]);
        });
    }
};
