<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'username')) {
                $table->string('username')->nullable()->unique()->after('name');
            }

            if (! Schema::hasColumn('users', 'role')) {
                $table->string('role', 50)->default('staff')->after('password');
            }

            if (! Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('role');
            }

            if (! Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('is_active');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $dropColumns = collect(['username', 'role', 'is_active', 'last_login_at'])
                ->filter(fn (string $column) => Schema::hasColumn('users', $column))
                ->all();

            if ($dropColumns !== []) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
