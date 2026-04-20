<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $lookupMap = [];

        $masterLayanan = DB::table('master_layanan')
            ->select('kode_layanan', 'nama_layanan', 'simrs_kd_poli', 'simrs_nm_poli')
            ->where('is_active', true)
            ->get();

        foreach ($masterLayanan as $layanan) {
            $keys = array_filter(array_unique(array_map(
                fn ($value) => $this->normalizeValue($value),
                array_merge(
                    [
                        $layanan->kode_layanan,
                        $layanan->nama_layanan,
                        $layanan->simrs_kd_poli,
                        $layanan->simrs_nm_poli,
                    ],
                    $this->layananAliases($layanan->nama_layanan),
                )
            )));

            foreach ($keys as $key) {
                $lookupMap[$key] = $layanan->kode_layanan;
            }
        }

        DB::table('transaksi_pasien')
            ->select('id', 'layanan_medis')
            ->whereNotNull('layanan_medis')
            ->orderBy('id')
            ->chunkById(500, function ($rows) use ($lookupMap) {
                foreach ($rows as $row) {
                    $normalized = $this->normalizeValue($row->layanan_medis);

                    if ($normalized === '' || ! isset($lookupMap[$normalized])) {
                        continue;
                    }

                    $mappedCode = $lookupMap[$normalized];

                    if ($row->layanan_medis === $mappedCode) {
                        continue;
                    }

                    DB::table('transaksi_pasien')
                        ->where('id', $row->id)
                        ->update(['layanan_medis' => $mappedCode]);
                }
            });
    }

    public function down(): void
    {
        // Data normalization is irreversible without historical source values.
    }

    private function layananAliases(?string $layananName): array
    {
        return match ($this->normalizeValue($layananName)) {
            'klinik umum' => ['klinik umum', 'poliklinik umum', 'umum'],
            'partus' => ['partus', 'kia'],
            'curetage' => ['curetage', 'curetase', 'curettage', 'curretage'],
            default => [$layananName],
        };
    }

    private function normalizeValue(?string $value): string
    {
        $value = strtolower(trim((string) $value));
        $value = str_replace(['/', '-', '_'], ' ', $value);

        return preg_replace('/\s+/', ' ', $value) ?: '';
    }
};
