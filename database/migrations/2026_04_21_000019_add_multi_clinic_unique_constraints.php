<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clinic_profiles', function (Blueprint $table) {
            $table->unique('kode_klinik', 'clinic_profiles_kode_klinik_unique');
        });

        Schema::table('transaksi_pasien', function (Blueprint $table) {
            $table->dropUnique('transaksi_pasien_simrs_no_rawat_unique');
            $table->unique(
                ['clinic_profile_id', 'simrs_no_rawat'],
                'transaksi_pasien_clinic_profile_id_simrs_no_rawat_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('transaksi_pasien', function (Blueprint $table) {
            $table->dropUnique('transaksi_pasien_clinic_profile_id_simrs_no_rawat_unique');
            $table->unique('simrs_no_rawat', 'transaksi_pasien_simrs_no_rawat_unique');
        });

        Schema::table('clinic_profiles', function (Blueprint $table) {
            $table->dropUnique('clinic_profiles_kode_klinik_unique');
        });
    }
};
