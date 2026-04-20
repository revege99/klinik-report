<?php

namespace Database\Seeders;

use App\Models\MasterKategoriPengeluaran;
use Illuminate\Database\Seeder;

class MasterKategoriPengeluaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['kode_kategori' => 'OPERASIONAL', 'nama_kategori' => 'Operasional Klinik'],
            ['kode_kategori' => 'HONOR', 'nama_kategori' => 'Honor dan Jasa'],
            ['kode_kategori' => 'ATK', 'nama_kategori' => 'ATK dan Administrasi'],
            ['kode_kategori' => 'OBAT', 'nama_kategori' => 'Obat dan Farmasi'],
            ['kode_kategori' => 'LAB', 'nama_kategori' => 'Laboratorium'],
            ['kode_kategori' => 'TRANSPORT', 'nama_kategori' => 'Transportasi'],
            ['kode_kategori' => 'LAIN', 'nama_kategori' => 'Lain-lain'],
        ];

        foreach ($categories as $index => $category) {
            MasterKategoriPengeluaran::query()->updateOrCreate(
                ['kode_kategori' => $category['kode_kategori']],
                [
                    'nama_kategori' => $category['nama_kategori'],
                    'urutan_laporan' => $index + 1,
                    'is_active' => true,
                ],
            );
        }
    }
}
