<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $this->normalizeFieldKeys(
            'master_komponen_transaksi',
            'nama_komponen',
            [
                'uang daftar' => 'uang_daftar',
                'biaya daftar' => 'uang_daftar',
                'biaya pendaftaran' => 'uang_daftar',
                'pendaftaran' => 'uang_daftar',
                'uang periksa' => 'uang_periksa',
                'biaya periksa' => 'uang_periksa',
                'uang pemeriksaan' => 'uang_periksa',
                'biaya pemeriksaan' => 'uang_periksa',
                'uang obat' => 'uang_obat',
                'biaya obat' => 'uang_obat',
                'obat' => 'uang_obat',
                'uang bersalin' => 'uang_bersalin',
                'biaya bersalin' => 'uang_bersalin',
                'persalinan' => 'uang_bersalin',
                'partus' => 'uang_bersalin',
                'jasa dokter' => 'jasa_dokter',
                'fee dokter' => 'jasa_dokter',
                'honor dokter' => 'jasa_dokter',
                'rawat inap' => 'rawat_inap',
                'biaya rawat inap' => 'rawat_inap',
                'honor dr visit' => 'honor_dr_visit',
                'honor dr. visit' => 'honor_dr_visit',
                'honor dokter visit' => 'honor_dr_visit',
                'oksigen' => 'oksigen',
                'biaya oksigen' => 'oksigen',
                'perlengkapan bayi' => 'perlengk_bayi',
                'perlengk bayi' => 'perlengk_bayi',
                'perlengkapan baby' => 'perlengk_bayi',
                'jaspel nakes' => 'jaspel_nakes',
                'jasa pelayanan nakes' => 'jaspel_nakes',
                'jasa nakes' => 'jaspel_nakes',
                'bmhp' => 'bmhp',
                'pkl' => 'pkl',
                'lain lain' => 'lain_lain',
                'lain-lain' => 'lain_lain',
                'lainnya' => 'lain_lain',
            ]
        );

        $this->normalizeFieldKeys(
            'master_administrasi_pasien',
            'nama_administrasi',
            [
                'utang pasien' => 'utang_pasien',
                'utang' => 'utang',
                'bayar utang pasien' => 'bayar_utang_pasien',
                'pelunasan utang pasien' => 'bayar_utang_pasien',
                'derma solidaritas' => 'derma_solidaritas',
                'derma & solidaritas' => 'derma_solidaritas',
                'saldo kredit' => 'saldo_kredit',
                'saldo' => 'saldo',
            ]
        );
    }

    public function down(): void
    {
        // Tidak di-revert agar field_key yang sudah dinormalisasi tetap stabil.
    }

    private function normalizeFieldKeys(string $table, string $labelColumn, array $map): void
    {
        $rows = DB::table($table)
            ->select('id', $labelColumn, 'field_key')
            ->orderBy('id')
            ->get();

        foreach ($rows as $row) {
            $normalizedLabel = $this->normalizeLabel($row->{$labelColumn});
            $targetFieldKey = $map[$normalizedLabel] ?? null;

            if (! $targetFieldKey || $row->field_key === $targetFieldKey) {
                continue;
            }

            $alreadyTaken = DB::table($table)
                ->where('field_key', $targetFieldKey)
                ->where('id', '!=', $row->id)
                ->exists();

            if ($alreadyTaken) {
                continue;
            }

            DB::table($table)
                ->where('id', $row->id)
                ->update([
                    'field_key' => $targetFieldKey,
                    'updated_at' => now(),
                ]);
        }
    }

    private function normalizeLabel(?string $value): string
    {
        $value = strtolower((string) $value);
        $value = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value) ?: $value;
        $value = str_replace('&', ' ', $value);
        $value = preg_replace('/[^a-z0-9]+/', ' ', $value) ?? $value;

        return trim(preg_replace('/\s+/', ' ', $value) ?? $value);
    }
};
