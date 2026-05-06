<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('transaksi_pasien_administrasi')) {
            Schema::create('transaksi_pasien_administrasi', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('transaksi_pasien_id');
                $table->unsignedBigInteger('master_administrasi_pasien_id');
                $table->decimal('nominal', 15, 2)->default(0);
                $table->timestamps();
            });
        }

        if (! $this->hasIndex('transaksi_pasien_administrasi', 'transaksi_pasien_administrasi_unique')) {
            Schema::table('transaksi_pasien_administrasi', function (Blueprint $table) {
                $table->unique(
                    ['transaksi_pasien_id', 'master_administrasi_pasien_id'],
                    'transaksi_pasien_administrasi_unique'
                );
            });
        }

        if (! $this->hasForeignKey('transaksi_pasien_administrasi', 'tp_admin_tp_fk')) {
            Schema::table('transaksi_pasien_administrasi', function (Blueprint $table) {
                $table->foreign('transaksi_pasien_id', 'tp_admin_tp_fk')
                    ->references('id')
                    ->on('transaksi_pasien')
                    ->cascadeOnDelete();
            });
        }

        if (! $this->hasForeignKey('transaksi_pasien_administrasi', 'tp_admin_master_fk')) {
            Schema::table('transaksi_pasien_administrasi', function (Blueprint $table) {
                $table->foreign('master_administrasi_pasien_id', 'tp_admin_master_fk')
                    ->references('id')
                    ->on('master_administrasi_pasien')
                    ->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_pasien_administrasi');
    }

    private function hasIndex(string $table, string $indexName): bool
    {
        $database = DB::getDatabaseName();

        return DB::table('information_schema.statistics')
            ->where('table_schema', $database)
            ->where('table_name', $table)
            ->where('index_name', $indexName)
            ->exists();
    }

    private function hasForeignKey(string $table, string $constraintName): bool
    {
        $database = DB::getDatabaseName();

        return DB::table('information_schema.table_constraints')
            ->where('table_schema', $database)
            ->where('table_name', $table)
            ->where('constraint_name', $constraintName)
            ->where('constraint_type', 'FOREIGN KEY')
            ->exists();
    }
};
