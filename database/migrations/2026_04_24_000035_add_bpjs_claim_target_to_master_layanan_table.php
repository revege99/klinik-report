<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_layanan', function (Blueprint $table) {
            $table->boolean('is_bpjs_claim_target')
                ->default(false)
                ->after('simrs_nm_poli');
        });
    }

    public function down(): void
    {
        Schema::table('master_layanan', function (Blueprint $table) {
            $table->dropColumn('is_bpjs_claim_target');
        });
    }
};
