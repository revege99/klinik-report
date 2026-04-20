<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi_pasien', function (Blueprint $table) {
            $table->id();
            $table->string('simrs_no_rawat', 30)->unique();
            $table->string('simrs_no_reg', 20)->nullable();
            $table->date('tanggal')->index();
            $table->unsignedTinyInteger('bulan')->index();
            $table->string('harian', 30)->nullable();
            $table->string('layanan_medis')->nullable();
            $table->string('no_rm', 50)->nullable()->index();
            $table->string('nama_pasien')->nullable();
            $table->string('jk', 20)->nullable();
            $table->string('statis', 100)->nullable();
            $table->string('genap', 100)->nullable();
            $table->string('status_pasien', 100)->nullable();
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
            $table->decimal('honor_dr_visit', 15, 2)->default(0);
            $table->decimal('oksigen', 15, 2)->default(0);
            $table->decimal('perlengk_bayi', 15, 2)->default(0);
            $table->decimal('jaspel_nakes', 15, 2)->default(0);
            $table->decimal('bmhp', 15, 2)->default(0);
            $table->decimal('pkl', 15, 2)->default(0);
            $table->decimal('lain_lain', 15, 2)->default(0);
            $table->decimal('jumlah_rp', 15, 2)->default(0);
            $table->decimal('utang_pasien', 15, 2)->default(0);
            $table->decimal('utang', 15, 2)->default(0);
            $table->decimal('bayar_utang_pasien', 15, 2)->default(0);
            $table->decimal('derma_solidaritas', 15, 2)->default(0);
            $table->decimal('saldo_kredit', 15, 2)->default(0);
            $table->decimal('saldo', 15, 2)->default(0);
            $table->string('petugas_admin')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index(['tanggal', 'bulan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_pasien');
    }
};
