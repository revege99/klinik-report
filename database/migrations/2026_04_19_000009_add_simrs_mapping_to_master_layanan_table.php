<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_layanan', function (Blueprint $table) {
            $table->string('simrs_kd_poli', 50)->nullable()->after('nama_layanan')->index();
            $table->string('simrs_nm_poli')->nullable()->after('simrs_kd_poli');
        });

        DB::table('master_layanan')
            ->where(function ($query) {
                $query->whereIn('kode_layanan', ['K1', 'UMUM'])
                    ->orWhere('nama_layanan', 'Klinik Umum');
            })
            ->update([
                'simrs_kd_poli' => 'umum',
                'simrs_nm_poli' => 'Klinik Umum',
            ]);

        DB::table('master_layanan')
            ->where(function ($query) {
                $query->whereIn('kode_layanan', ['K5', 'PARTUS'])
                    ->orWhere('nama_layanan', 'Partus');
            })
            ->update([
                'simrs_kd_poli' => 'kia',
                'simrs_nm_poli' => 'KIA',
            ]);
    }

    public function down(): void
    {
        Schema::table('master_layanan', function (Blueprint $table) {
            $table->dropIndex(['simrs_kd_poli']);
            $table->dropColumn(['simrs_kd_poli', 'simrs_nm_poli']);
        });
    }
};
