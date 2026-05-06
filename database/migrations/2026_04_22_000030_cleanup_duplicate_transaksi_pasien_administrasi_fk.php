<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $database = DB::getDatabaseName();
        $constraintName = 'transaksi_pasien_administrasi_transaksi_pasien_id_foreign';

        $exists = DB::table('information_schema.table_constraints')
            ->where('table_schema', $database)
            ->where('table_name', 'transaksi_pasien_administrasi')
            ->where('constraint_name', $constraintName)
            ->where('constraint_type', 'FOREIGN KEY')
            ->exists();

        if ($exists) {
            Schema::table('transaksi_pasien_administrasi', function (Blueprint $table) use ($constraintName) {
                $table->dropForeign($constraintName);
            });
        }
    }

    public function down(): void
    {
        //
    }
};
