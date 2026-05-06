<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clinic_database_connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_profile_id')
                ->constrained('clinic_profiles')
                ->cascadeOnDelete();
            $table->string('connection_role', 50);
            $table->string('driver', 30)->default('mariadb');
            $table->string('server_name')->nullable();
            $table->string('host');
            $table->string('zero_tier_ip')->nullable();
            $table->unsignedSmallInteger('port')->default(3306);
            $table->string('database');
            $table->string('username');
            $table->text('password')->nullable();
            $table->string('charset', 30)->default('utf8mb4');
            $table->string('collation', 50)->default('utf8mb4_unicode_ci');
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamp('last_verified_at')->nullable();
            $table->timestamps();

            $table->unique(
                ['clinic_profile_id', 'connection_role'],
                'clinic_database_connections_role_unique'
            );
            $table->index(['connection_role', 'is_active'], 'clinic_database_connections_role_active_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinic_database_connections');
    }
};
