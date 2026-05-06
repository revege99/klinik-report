<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clinic_profiles', function (Blueprint $table) {
            $table->string('kode_klinik', 40)->nullable()->after('id');
            $table->boolean('is_active')->default(true)->after('deskripsi_singkat');
        });

        $profiles = DB::table('clinic_profiles')->orderBy('id')->get();

        if ($profiles->isEmpty()) {
            DB::table('clinic_profiles')->insert([
                'kode_klinik' => 'KLN001',
                'nama_klinik' => config('app.name', 'Klink Report'),
                'nama_pendek' => config('app.name', 'Klink Report'),
                'tagline' => 'Profil klinik utama',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            foreach ($profiles as $index => $profile) {
                DB::table('clinic_profiles')
                    ->where('id', $profile->id)
                    ->update([
                        'kode_klinik' => 'KLN' . str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT),
                        'is_active' => $profile->is_active ?? true,
                    ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('clinic_profiles', function (Blueprint $table) {
            $table->dropColumn(['kode_klinik', 'is_active']);
        });
    }
};
