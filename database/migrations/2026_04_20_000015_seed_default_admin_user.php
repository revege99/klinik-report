<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        $existingAdmin = DB::table('users')
            ->where('username', 'admin')
            ->orWhere('email', 'admin@klink.local')
            ->first();

        if (! $existingAdmin) {
            DB::table('users')->insert([
                'name' => 'Administrator Klinik',
                'username' => 'admin',
                'email' => 'admin@klink.local',
                'password' => Hash::make('admin12345'),
                'role' => 'admin',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $existingAdmin = DB::table('users')
                ->where('username', 'admin')
                ->first();
        }

        if ($existingAdmin && ! DB::table('pegawai_profiles')->where('user_id', $existingAdmin->id)->exists()) {
            DB::table('pegawai_profiles')->insert([
                'user_id' => $existingAdmin->id,
                'jabatan' => 'Administrator Sistem',
                'unit_kerja' => 'Yayasan / Klinik',
                'phone_number' => null,
                'bio' => 'Akun awal lokal untuk pengelolaan dashboard dan laporan klinik.',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        $admin = DB::table('users')
            ->where('username', 'admin')
            ->orWhere('email', 'admin@klink.local')
            ->first();

        if (! $admin) {
            return;
        }

        DB::table('pegawai_profiles')->where('user_id', $admin->id)->delete();
        DB::table('users')->where('id', $admin->id)->delete();
    }
};
