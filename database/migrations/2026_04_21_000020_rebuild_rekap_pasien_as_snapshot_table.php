<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('rekap_pasien') && ! Schema::hasTable('rekap_pasien_legacy')) {
            Schema::rename('rekap_pasien', 'rekap_pasien_legacy');
        }

        if (! Schema::hasTable('rekap_pasien')) {
            Schema::create('rekap_pasien', function (Blueprint $table) {
                $table->id();
                $table->foreignId('clinic_profile_id')
                    ->constrained('clinic_profiles')
                    ->cascadeOnDelete();
                $table->foreignId('master_layanan_id')
                    ->nullable()
                    ->constrained('master_layanan')
                    ->nullOnDelete();
                $table->date('tanggal')->index();
                $table->unsignedTinyInteger('bulan')->index();
                $table->unsignedSmallInteger('tahun')->index();
                $table->string('no_rawat', 30);
                $table->string('no_rm', 50)->nullable()->index();
                $table->string('nama_pasien')->nullable();
                $table->string('layanan_medis', 30)->nullable();
                $table->timestamp('synced_at')->nullable();
                $table->timestamps();

                $table->unique(['clinic_profile_id', 'no_rawat'], 'rekap_pasien_clinic_no_rawat_unique');
                $table->index(['clinic_profile_id', 'tahun', 'bulan'], 'rekap_pasien_clinic_period_index');
            });
        }

        if (Schema::hasTable('rekap_pasien_legacy')) {
            $defaultClinicId = DB::table('clinic_profiles')->orderBy('id')->value('id');

            if ($defaultClinicId) {
                $legacyRows = DB::table('rekap_pasien_legacy')->get();

                if ($legacyRows->isNotEmpty()) {
                    $payload = $legacyRows->map(function ($row) use ($defaultClinicId) {
                        return [
                            'clinic_profile_id' => $defaultClinicId,
                            'master_layanan_id' => $row->master_layanan_id,
                            'tanggal' => $row->tanggal,
                            'bulan' => $row->bulan,
                            'tahun' => $row->tahun,
                            'no_rawat' => $row->simrs_ref ?: ('LEGACY-' . $row->id),
                            'no_rm' => $row->no_rm,
                            'nama_pasien' => $row->nama_pasien,
                            'layanan_medis' => $row->layanan_medis,
                            'synced_at' => $row->synced_at,
                            'created_at' => $row->created_at ?? now(),
                            'updated_at' => $row->updated_at ?? now(),
                        ];
                    })->all();

                    DB::table('rekap_pasien')->upsert(
                        $payload,
                        ['clinic_profile_id', 'no_rawat'],
                        ['master_layanan_id', 'tanggal', 'bulan', 'tahun', 'no_rm', 'nama_pasien', 'layanan_medis', 'synced_at', 'updated_at']
                    );
                }
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('rekap_pasien');

        if (Schema::hasTable('rekap_pasien_legacy')) {
            Schema::rename('rekap_pasien_legacy', 'rekap_pasien');
        }
    }
};
