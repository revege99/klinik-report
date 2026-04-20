<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pengeluaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_kategori_pengeluaran_id')
                ->nullable()
                ->constrained(table: 'master_kategori_pengeluaran')
                ->nullOnDelete();
            $table->date('tanggal')->index();
            $table->unsignedTinyInteger('bulan')->index();
            $table->unsignedSmallInteger('tahun')->index();
            $table->string('kategori_pengeluaran')->nullable();
            $table->string('deskripsi');
            $table->decimal('jumlah_rp', 15, 2)->default(0);
            $table->string('petugas_admin')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('simrs_ref', 191)->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();

            $table->index(['tahun', 'bulan']);
            $table->index(['simrs_ref']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengeluaran');
    }
};
