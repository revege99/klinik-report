<?php

namespace Tests\Feature;

use App\Models\MasterKategoriPengeluaran;
use App\Models\MasterLayanan;
use App\Models\Pengeluaran;
use App\Models\RekapPasien;
use App\Services\LaporanPendapatanService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaporanPendapatanServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_builds_monthly_report_for_single_clinic_project(): void
    {
        $umum = MasterLayanan::create([
            'kode_layanan' => 'UMUM',
            'nama_layanan' => 'Klinik Umum',
            'urutan_laporan' => 1,
            'is_active' => true,
        ]);

        MasterKategoriPengeluaran::create([
            'kode_kategori' => 'OPS',
            'nama_kategori' => 'Operasional',
            'urutan_laporan' => 1,
            'is_active' => true,
        ]);

        RekapPasien::create([
            'master_layanan_id' => $umum->id,
            'tanggal' => '2026-04-19',
            'bulan' => 4,
            'tahun' => 2026,
            'harian' => 'Sabtu',
            'layanan_medis' => 'Klinik Umum',
            'no_rm' => 'RM001',
            'nama_pasien' => 'Siti',
            'jk' => 'Pr',
            'jenis_bayar' => 'Umum',
            'uang_daftar' => 10000,
            'uang_periksa' => 25000,
            'uang_obat' => 15000,
            'jasa_dokter' => 12000,
            'jml_visit' => 1,
            'honor_visit' => 5000,
            'jaspel_nakes' => 3000,
            'bmhp' => 2000,
            'pkl_dll' => 1000,
            'lain_lain' => 500,
            'jumlah_rp' => 73500,
            'utang_pasien' => 10000,
            'bayar_utang_pasien' => 5000,
            'derma_solidaritas' => 1000,
            'petugas_admin' => 'Admin 1',
        ]);

        Pengeluaran::create([
            'kategori_pengeluaran' => 'Operasional',
            'deskripsi' => 'ATK',
            'tanggal' => '2026-04-19',
            'bulan' => 4,
            'tahun' => 2026,
            'jumlah_rp' => 20000,
            'petugas_admin' => 'Admin 1',
        ]);

        $laporan = app(LaporanPendapatanService::class)->laporanBulanan(4, 2026);

        $this->assertSame(1, $laporan['table_input']['total_pasien']);
        $this->assertSame(10000.0, $laporan['table_input']['uang_daftar']);
        $this->assertSame(73500.0, $laporan['table_input']['jumlah_rp']);
        $this->assertSame(53500.0, $laporan['rekap']['saldo_bersih']);
    }
}
