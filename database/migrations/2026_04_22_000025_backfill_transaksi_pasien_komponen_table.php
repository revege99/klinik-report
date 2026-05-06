<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $components = DB::table('master_komponen_transaksi')
            ->select('id', 'field_key')
            ->orderBy('urutan_laporan')
            ->get();

        if ($components->isEmpty()) {
            return;
        }

        $transactions = DB::table('transaksi_pasien')->select(['id', ...$components->pluck('field_key')->all()])->get();
        $now = now();
        $payload = [];

        foreach ($transactions as $transaction) {
            foreach ($components as $component) {
                $nominal = (float) ($transaction->{$component->field_key} ?? 0);

                if ($nominal <= 0) {
                    continue;
                }

                $payload[] = [
                    'transaksi_pasien_id' => $transaction->id,
                    'master_komponen_transaksi_id' => $component->id,
                    'nominal' => $nominal,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        foreach (array_chunk($payload, 500) as $chunk) {
            DB::table('transaksi_pasien_komponen')->upsert(
                $chunk,
                ['transaksi_pasien_id', 'master_komponen_transaksi_id'],
                ['nominal', 'updated_at']
            );
        }
    }

    public function down(): void
    {
        DB::table('transaksi_pasien_komponen')->truncate();
    }
};
