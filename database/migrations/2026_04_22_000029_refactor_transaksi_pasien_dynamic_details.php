<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksi_pasien', function (Blueprint $table) {
            if (! Schema::hasColumn('transaksi_pasien', 'jumlah_kredit')) {
                $table->decimal('jumlah_kredit', 15, 2)->default(0)->after('jumlah_rp');
            }
        });

        $this->backfillAdministrasiDetails();
        $this->syncHeaderTotals();

        Schema::table('transaksi_pasien', function (Blueprint $table) {
            $table->dropColumn([
                'uang_daftar',
                'uang_periksa',
                'uang_obat',
                'uang_bersalin',
                'jasa_dokter',
                'rawat_inap',
                'honor_dr_visit',
                'oksigen',
                'perlengk_bayi',
                'jaspel_nakes',
                'bmhp',
                'pkl',
                'lain_lain',
                'utang_pasien',
                'utang',
                'bayar_utang_pasien',
                'derma_solidaritas',
                'saldo_kredit',
                'saldo',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('transaksi_pasien', function (Blueprint $table) {
            $table->decimal('uang_daftar', 15, 2)->default(0)->after('farmasi');
            $table->decimal('uang_periksa', 15, 2)->default(0)->after('uang_daftar');
            $table->decimal('uang_obat', 15, 2)->default(0)->after('uang_periksa');
            $table->decimal('uang_bersalin', 15, 2)->default(0)->after('uang_obat');
            $table->decimal('jasa_dokter', 15, 2)->default(0)->after('uang_bersalin');
            $table->decimal('rawat_inap', 15, 2)->default(0)->after('jml_hari');
            $table->decimal('honor_dr_visit', 15, 2)->default(0)->after('jml_visit');
            $table->decimal('oksigen', 15, 2)->default(0)->after('honor_dr_visit');
            $table->decimal('perlengk_bayi', 15, 2)->default(0)->after('oksigen');
            $table->decimal('jaspel_nakes', 15, 2)->default(0)->after('perlengk_bayi');
            $table->decimal('bmhp', 15, 2)->default(0)->after('jaspel_nakes');
            $table->decimal('pkl', 15, 2)->default(0)->after('bmhp');
            $table->decimal('lain_lain', 15, 2)->default(0)->after('pkl');
            $table->decimal('utang_pasien', 15, 2)->default(0)->after('jumlah_kredit');
            $table->decimal('utang', 15, 2)->default(0)->after('utang_pasien');
            $table->decimal('bayar_utang_pasien', 15, 2)->default(0)->after('utang');
            $table->decimal('derma_solidaritas', 15, 2)->default(0)->after('bayar_utang_pasien');
            $table->decimal('saldo_kredit', 15, 2)->default(0)->after('derma_solidaritas');
            $table->decimal('saldo', 15, 2)->default(0)->after('saldo_kredit');
        });

        Schema::table('transaksi_pasien', function (Blueprint $table) {
            $table->dropColumn('jumlah_kredit');
        });
    }

    private function backfillAdministrasiDetails(): void
    {
        $masters = DB::table('master_administrasi_pasien')->get()->keyBy('field_key');
        $rows = DB::table('transaksi_pasien')
            ->select([
                'id',
                'utang_pasien',
                'utang',
                'bayar_utang_pasien',
                'derma_solidaritas',
                'saldo_kredit',
                'saldo',
            ])
            ->get();

        $payload = [];
        $now = now();

        foreach ($rows as $row) {
            foreach ([
                'utang_pasien',
                'utang',
                'bayar_utang_pasien',
                'derma_solidaritas',
                'saldo_kredit',
                'saldo',
            ] as $fieldKey) {
                $nominal = (float) ($row->{$fieldKey} ?? 0);
                $master = $masters->get($fieldKey);

                if (! $master || $nominal <= 0) {
                    continue;
                }

                $payload[] = [
                    'transaksi_pasien_id' => (int) $row->id,
                    'master_administrasi_pasien_id' => (int) $master->id,
                    'nominal' => $nominal,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        if (! empty($payload)) {
            DB::table('transaksi_pasien_administrasi')->upsert(
                $payload,
                ['transaksi_pasien_id', 'master_administrasi_pasien_id'],
                ['nominal', 'updated_at']
            );
        }
    }

    private function syncHeaderTotals(): void
    {
        $komponenDirections = DB::table('master_komponen_transaksi')
            ->pluck('arah_laporan', 'id');
        $administrasiDirections = DB::table('master_administrasi_pasien')
            ->pluck('arah_laporan', 'id');

        $transactions = DB::table('transaksi_pasien')->select('id')->get();

        foreach ($transactions as $transaction) {
            $komponenDetails = DB::table('transaksi_pasien_komponen')
                ->where('transaksi_pasien_id', $transaction->id)
                ->get();
            $administrasiDetails = DB::table('transaksi_pasien_administrasi')
                ->where('transaksi_pasien_id', $transaction->id)
                ->get();

            $debet = 0.0;
            $kredit = 0.0;

            foreach ($komponenDetails as $detail) {
                $direction = $komponenDirections[$detail->master_komponen_transaksi_id] ?? 'debet';

                if ($direction === 'kredit') {
                    $kredit += (float) $detail->nominal;
                } else {
                    $debet += (float) $detail->nominal;
                }
            }

            foreach ($administrasiDetails as $detail) {
                $direction = $administrasiDirections[$detail->master_administrasi_pasien_id] ?? 'debet';

                if ($direction === 'kredit') {
                    $kredit += (float) $detail->nominal;
                } else {
                    $debet += (float) $detail->nominal;
                }
            }

            DB::table('transaksi_pasien')
                ->where('id', $transaction->id)
                ->update([
                    'jumlah_rp' => $debet,
                    'jumlah_kredit' => $kredit,
                ]);
        }
    }
};
