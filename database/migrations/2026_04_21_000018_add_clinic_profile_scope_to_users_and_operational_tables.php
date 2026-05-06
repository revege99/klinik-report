<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('clinic_profile_id')->nullable()->after('role')->constrained('clinic_profiles')->nullOnDelete();
        });

        Schema::table('transaksi_pasien', function (Blueprint $table) {
            $table->foreignId('clinic_profile_id')->nullable()->after('layanan_medis')->constrained('clinic_profiles')->nullOnDelete();
        });

        Schema::table('pengeluaran', function (Blueprint $table) {
            $table->foreignId('clinic_profile_id')->nullable()->after('master_kategori_pengeluaran_id')->constrained('clinic_profiles')->nullOnDelete();
        });

        $defaultClinicId = DB::table('clinic_profiles')->orderBy('id')->value('id');

        if ($defaultClinicId) {
            DB::table('users')
                ->whereNull('clinic_profile_id')
                ->update(['clinic_profile_id' => $defaultClinicId]);

            DB::table('transaksi_pasien')
                ->whereNull('clinic_profile_id')
                ->update(['clinic_profile_id' => $defaultClinicId]);

            DB::table('pengeluaran')
                ->whereNull('clinic_profile_id')
                ->update(['clinic_profile_id' => $defaultClinicId]);
        }

        DB::table('users')
            ->where('role', 'admin')
            ->update(['role' => 'master']);
    }

    public function down(): void
    {
        Schema::table('pengeluaran', function (Blueprint $table) {
            $table->dropConstrainedForeignId('clinic_profile_id');
        });

        Schema::table('transaksi_pasien', function (Blueprint $table) {
            $table->dropConstrainedForeignId('clinic_profile_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('clinic_profile_id');
        });

        DB::table('users')
            ->where('role', 'master')
            ->update(['role' => 'admin']);
    }
};
