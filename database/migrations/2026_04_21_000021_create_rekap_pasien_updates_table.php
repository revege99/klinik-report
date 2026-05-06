<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rekap_pasien_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_profile_id')
                ->unique()
                ->constrained('clinic_profiles')
                ->cascadeOnDelete();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->date('tanggal_data')->nullable();
            $table->unsignedInteger('total_data')->default(0);
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rekap_pasien_updates');
    }
};
