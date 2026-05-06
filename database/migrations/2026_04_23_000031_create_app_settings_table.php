<?php

use App\Models\AppSetting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('setting_group', 50)->default('general');
            $table->string('setting_key', 120)->unique();
            $table->text('setting_value')->nullable();
            $table->string('label')->nullable();
            $table->text('description')->nullable();
            $table->string('input_type', 30)->default('text');
            $table->timestamps();
        });

        AppSetting::query()->upsert(
            AppSetting::defaults(),
            ['setting_key'],
            ['setting_group', 'setting_value', 'label', 'description', 'input_type']
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('app_settings');
    }
};
