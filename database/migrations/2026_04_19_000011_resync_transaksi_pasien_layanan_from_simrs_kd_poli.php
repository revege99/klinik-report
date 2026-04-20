<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $mapping = DB::table('master_layanan')
            ->where('is_active', true)
            ->whereNotNull('simrs_kd_poli')
            ->select('kode_layanan', 'simrs_kd_poli')
            ->get()
            ->mapWithKeys(function ($row) {
                $normalizedKdPoli = $this->normalizeValue($row->simrs_kd_poli);

                if ($normalizedKdPoli === '') {
                    return [];
                }

                return [$normalizedKdPoli => $row->kode_layanan];
            })
            ->all();

        DB::table('transaksi_pasien')
            ->select('id', 'simrs_no_rawat', 'layanan_medis')
            ->orderBy('id')
            ->chunkById(300, function ($rows) use ($mapping) {
                $noRawatList = collect($rows)
                    ->pluck('simrs_no_rawat')
                    ->filter()
                    ->values()
                    ->all();

                if ($noRawatList === []) {
                    return;
                }

                $simrsRows = DB::connection('simrs')
                    ->table('reg_periksa')
                    ->whereIn('no_rawat', $noRawatList)
                    ->pluck('kd_poli', 'no_rawat');

                foreach ($rows as $row) {
                    $simrsKdPoli = $simrsRows[$row->simrs_no_rawat] ?? null;

                    if (! $simrsKdPoli) {
                        continue;
                    }

                    $normalizedKdPoli = $this->normalizeValue($simrsKdPoli);
                    $normalizedCurrent = $this->normalizeValue($row->layanan_medis);
                    $targetValue = $mapping[$normalizedKdPoli] ?? strtoupper(trim((string) $simrsKdPoli));

                    if ($normalizedCurrent === $this->normalizeValue($targetValue)) {
                        continue;
                    }

                    DB::table('transaksi_pasien')
                        ->where('id', $row->id)
                        ->update([
                            'layanan_medis' => $targetValue,
                            'updated_at' => now(),
                        ]);
                }
            });
    }

    public function down(): void
    {
        // Resync is intentionally one-way because previous values may already have been incorrect.
    }

    private function normalizeValue(?string $value): string
    {
        $value = strtolower(trim((string) $value));
        $value = str_replace(['/', '-', '_'], ' ', $value);

        return preg_replace('/\s+/', ' ', $value) ?: '';
    }
};
