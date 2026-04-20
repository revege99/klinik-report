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
        Schema::create('rekap_pasien', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_layanan_id')
                ->nullable()
                ->constrained(table: 'master_layanan')
                ->nullOnDelete();
            $table->date('tanggal')->index();
            $table->unsignedTinyInteger('bulan')->index();
            $table->unsignedSmallInteger('tahun')->index();
            $table->string('harian', 50)->nullable();
            $table->string('layanan_medis')->nullable();
            $table->string('no_rm', 50)->nullable()->index();
            $table->string('nama_pasien')->nullable();
            $table->string('jk', 20)->nullable();
            $table->string('statis_genap', 100)->nullable();
            $table->string('status_pasien', 100)->nullable();
            $table->string('jenis_bayar', 100)->nullable();
            $table->text('alamat')->nullable();
            $table->text('lab')->nullable();
            $table->text('icd')->nullable();
            $table->text('diagnosa')->nullable();
            $table->text('farmasi')->nullable();
            $table->decimal('uang_daftar', 15, 2)->default(0);
            $table->decimal('uang_periksa', 15, 2)->default(0);
            $table->decimal('uang_obat', 15, 2)->default(0);
            $table->decimal('uang_bersalin', 15, 2)->default(0);
            $table->decimal('jasa_dokter', 15, 2)->default(0);
            $table->unsignedInteger('jml_hari')->default(0);
            $table->decimal('rawat_inap', 15, 2)->default(0);
            $table->unsignedInteger('jml_visit')->default(0);
            $table->decimal('honor_visit', 15, 2)->default(0);
            $table->decimal('oksigen', 15, 2)->default(0);
            $table->decimal('perlengkapan_bayi', 15, 2)->default(0);
            $table->decimal('jaspel_nakes', 15, 2)->default(0);
            $table->decimal('bmhp', 15, 2)->default(0);
            $table->decimal('pkl_dll', 15, 2)->default(0);
            $table->decimal('lain_lain', 15, 2)->default(0);
            $table->decimal('jumlah_rp', 15, 2)->default(0);
            $table->decimal('utang_pasien', 15, 2)->default(0);
            $table->decimal('bayar_utang_pasien', 15, 2)->default(0);
            $table->decimal('derma_solidaritas', 15, 2)->default(0);
            $table->decimal('saldo_kredit', 15, 2)->default(0);
            $table->decimal('saldo_kredit2', 15, 2)->default(0);
            $table->string('petugas_admin')->nullable();
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
        Schema::dropIfExists('rekap_pasien');
    }
};
