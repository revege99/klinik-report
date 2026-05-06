<?php

namespace Database\Seeders;

use App\Models\MasterLayanan;
use Illuminate\Database\Seeder;

class MasterLayananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $layanan = [
            ['kode_layanan' => 'UMUM', 'nama_layanan' => 'Klinik Umum', 'simrs_kd_poli' => 'umum', 'simrs_nm_poli' => 'Klinik Umum'],
            ['kode_layanan' => 'KB', 'nama_layanan' => 'KB', 'simrs_kd_poli' => null, 'simrs_nm_poli' => null],
            ['kode_layanan' => 'ANC', 'nama_layanan' => 'ANC', 'simrs_kd_poli' => null, 'simrs_nm_poli' => null],
            ['kode_layanan' => 'CURETAGE', 'nama_layanan' => 'Curetage', 'simrs_kd_poli' => null, 'simrs_nm_poli' => null],
            ['kode_layanan' => 'PARTUS', 'nama_layanan' => 'Partus', 'simrs_kd_poli' => 'kia', 'simrs_nm_poli' => 'KIA'],
            ['kode_layanan' => 'OBSERVASI', 'nama_layanan' => 'Observasi', 'simrs_kd_poli' => null, 'simrs_nm_poli' => null],
            ['kode_layanan' => 'IMUNISASI', 'nama_layanan' => 'Imunisasi', 'simrs_kd_poli' => null, 'simrs_nm_poli' => null],
            ['kode_layanan' => 'TERAPI', 'nama_layanan' => 'Terapi', 'simrs_kd_poli' => null, 'simrs_nm_poli' => null],
            ['kode_layanan' => 'LUKA', 'nama_layanan' => 'Perawatan Luka', 'simrs_kd_poli' => null, 'simrs_nm_poli' => null],
            ['kode_layanan' => 'KONTROL', 'nama_layanan' => 'Kontrol', 'simrs_kd_poli' => null, 'simrs_nm_poli' => null],
            ['kode_layanan' => 'KONSELING', 'nama_layanan' => 'Konseling', 'simrs_kd_poli' => null, 'simrs_nm_poli' => null],
            ['kode_layanan' => 'GIGI', 'nama_layanan' => 'Gigi', 'simrs_kd_poli' => null, 'simrs_nm_poli' => null],
        ];

        foreach ($layanan as $index => $item) {
            MasterLayanan::query()->updateOrCreate(
                ['kode_layanan' => $item['kode_layanan']],
                [
                    'nama_layanan' => $item['nama_layanan'],
                    'simrs_kd_poli' => $item['simrs_kd_poli'],
                    'simrs_nm_poli' => $item['simrs_nm_poli'],
                    'is_bpjs_claim_target' => false,
                    'urutan_laporan' => $index + 1,
                    'is_active' => true,
                ],
            );
        }
    }
}
