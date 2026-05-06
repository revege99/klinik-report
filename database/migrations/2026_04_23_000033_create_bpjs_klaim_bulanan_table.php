<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bpjs_klaim_bulanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_profile_id')->constrained('clinic_profiles')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('master_komponen_selisih_id')->nullable()->constrained('master_komponen_transaksi')->nullOnDelete();
            $table->unsignedTinyInteger('bulan');
            $table->unsignedSmallInteger('tahun');
            $table->date('tanggal_terima')->nullable();
            $table->decimal('total_klaim', 15, 2)->default(0);
            $table->decimal('total_versi_klinik', 15, 2)->default(0);
            $table->decimal('total_komponen_acuan', 15, 2)->default(0);
            $table->unsignedInteger('jumlah_komponen_acuan')->default(0);
            $table->decimal('selisih_nominal', 15, 2)->default(0);
            $table->decimal('selisih_persen', 10, 4)->default(0);
            $table->string('selisih_arah', 10)->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['clinic_profile_id', 'bulan', 'tahun'], 'bpjs_klaim_bulanan_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bpjs_klaim_bulanan');
    }
};
