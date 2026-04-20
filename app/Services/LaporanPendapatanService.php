<?php

namespace App\Services;

use App\Models\MasterKategoriPengeluaran;
use App\Models\MasterLayanan;
use App\Models\Pengeluaran;
use App\Models\RekapPasien;
use Illuminate\Database\Eloquent\Builder;

class LaporanPendapatanService
{
    private const MONEY_COLUMNS = [
        'uang_daftar',
        'uang_periksa',
        'uang_obat',
        'uang_bersalin',
        'jasa_dokter',
        'rawat_inap',
        'honor_visit',
        'oksigen',
        'perlengkapan_bayi',
        'jaspel_nakes',
        'bmhp',
        'pkl_dll',
        'lain_lain',
        'jumlah_rp',
        'utang_pasien',
        'bayar_utang_pasien',
        'derma_solidaritas',
        'saldo_kredit',
        'saldo_kredit2',
    ];

    public function laporanBulanan(int $bulan, int $tahun): array
    {
        return $this->susunLaporan(
            RekapPasien::query()->forBulan($bulan, $tahun),
            Pengeluaran::query()->forBulan($bulan, $tahun),
            ['bulan' => $bulan, 'tahun' => $tahun],
        );
    }

    public function laporanTahunan(int $tahun): array
    {
        return $this->susunLaporan(
            RekapPasien::query()->forTahun($tahun),
            Pengeluaran::query()->forTahun($tahun),
            ['tahun' => $tahun],
        );
    }

    private function susunLaporan(Builder $rekapQuery, Builder $pengeluaranQuery, array $periode): array
    {
        $tablePoli = $this->buatTablePoli(clone $rekapQuery);
        $tableInput = $this->buatTableInput(clone $rekapQuery);
        $pengeluaran = $this->buatRingkasanPengeluaran(clone $pengeluaranQuery);

        $totalPerPoli = collect($tablePoli)->sum('jumlah_rp');
        $totalPendapatan = $tableInput['jumlah_rp'];
        $totalPengeluaran = $pengeluaran['total_pengeluaran'];

        return [
            'periode' => $periode,
            'table_poli' => $tablePoli,
            'table_input' => $tableInput,
            'pengeluaran' => $pengeluaran,
            'rekap' => [
                'total_pendapatan' => $this->money($totalPendapatan),
                'total_pengeluaran' => $this->money($totalPengeluaran),
                'saldo_bersih' => $this->money($totalPendapatan - $totalPengeluaran),
                'selisih_validasi' => $this->money($totalPerPoli - $totalPendapatan),
            ],
        ];
    }

    private function buatTablePoli(Builder $rekapQuery): array
    {
        $aggregates = $rekapQuery
            ->selectRaw('master_layanan_id')
            ->selectRaw("COALESCE(NULLIF(layanan_medis, ''), 'Tanpa Layanan') as nama_layanan")
            ->selectRaw('COUNT(*) as total_pasien')
            ->selectRaw("SUM(CASE WHEN lab IS NOT NULL AND lab <> '' THEN 1 ELSE 0 END) as total_lab")
            ->selectRaw('SUM(jml_hari) as jml_hari')
            ->selectRaw('SUM(jml_visit) as jml_visit')
            ->selectRaw($this->sumSelectRaw())
            ->groupBy('master_layanan_id', 'nama_layanan')
            ->get()
            ->map(fn (object $row) => $this->mapPoliRow($row));

        $aggregateById = $aggregates
            ->filter(fn (array $row) => ! is_null($row['master_layanan_id']))
            ->keyBy('master_layanan_id');

        $aggregateByName = $aggregates
            ->keyBy(fn (array $row) => $this->normalizeKey($row['nama_layanan']));

        $rows = MasterLayanan::query()
            ->active()
            ->orderBy('urutan_laporan')
            ->orderBy('nama_layanan')
            ->get()
            ->map(function (MasterLayanan $layanan) use ($aggregateById, $aggregateByName) {
                $row = $aggregateById->get($layanan->id)
                    ?? $aggregateByName->get($this->normalizeKey($layanan->nama_layanan));

                return array_merge(
                    $this->emptyPoliRow(),
                    $row ?? [],
                    [
                        'master_layanan_id' => $layanan->id,
                        'kode_layanan' => $layanan->kode_layanan,
                        'nama_layanan' => $layanan->nama_layanan,
                        'urutan_laporan' => $layanan->urutan_laporan,
                    ],
                );
            })
            ->values();

        return $rows->all();
    }

    private function buatTableInput(Builder $rekapQuery): array
    {
        $row = $rekapQuery
            ->selectRaw('COUNT(*) as total_pasien')
            ->selectRaw("SUM(CASE WHEN lab IS NOT NULL AND lab <> '' THEN 1 ELSE 0 END) as total_lab")
            ->selectRaw('SUM(jml_hari) as jml_hari')
            ->selectRaw('SUM(jml_visit) as jml_visit')
            ->selectRaw($this->sumSelectRaw())
            ->first();

        if (! $row) {
            return $this->emptyInputSummary();
        }

        return array_merge(
            [
                'total_pasien' => (int) ($row->total_pasien ?? 0),
                'total_lab' => (int) ($row->total_lab ?? 0),
                'jml_hari' => (int) ($row->jml_hari ?? 0),
                'jml_visit' => (int) ($row->jml_visit ?? 0),
            ],
            $this->mapMoneyFields($row),
        );
    }

    private function buatRingkasanPengeluaran(Builder $pengeluaranQuery): array
    {
        $aggregates = $pengeluaranQuery
            ->selectRaw('master_kategori_pengeluaran_id')
            ->selectRaw("COALESCE(NULLIF(kategori_pengeluaran, ''), 'Tanpa Kategori') as nama_kategori")
            ->selectRaw('COUNT(*) as total_item')
            ->selectRaw('SUM(jumlah_rp) as jumlah_rp')
            ->groupBy('master_kategori_pengeluaran_id', 'nama_kategori')
            ->get()
            ->map(fn (object $row) => [
                'master_kategori_pengeluaran_id' => $row->master_kategori_pengeluaran_id ? (int) $row->master_kategori_pengeluaran_id : null,
                'nama_kategori' => $row->nama_kategori,
                'total_item' => (int) ($row->total_item ?? 0),
                'jumlah_rp' => $this->money($row->jumlah_rp),
            ]);

        $aggregateById = $aggregates
            ->filter(fn (array $row) => ! is_null($row['master_kategori_pengeluaran_id']))
            ->keyBy('master_kategori_pengeluaran_id');

        $aggregateByName = $aggregates
            ->keyBy(fn (array $row) => $this->normalizeKey($row['nama_kategori']));

        $rows = MasterKategoriPengeluaran::query()
            ->active()
            ->orderBy('urutan_laporan')
            ->orderBy('nama_kategori')
            ->get()
            ->map(function (MasterKategoriPengeluaran $kategori) use ($aggregateById, $aggregateByName) {
                $row = $aggregateById->get($kategori->id)
                    ?? $aggregateByName->get($this->normalizeKey($kategori->nama_kategori));

                return array_merge(
                    [
                        'master_kategori_pengeluaran_id' => $kategori->id,
                        'nama_kategori' => $kategori->nama_kategori,
                        'total_item' => 0,
                        'jumlah_rp' => 0.0,
                        'urutan_laporan' => $kategori->urutan_laporan,
                    ],
                    $row ?? [],
                );
            })
            ->values();

        return [
            'per_kategori' => $rows->all(),
            'total_pengeluaran' => $this->money($rows->sum('jumlah_rp')),
        ];
    }

    private function mapPoliRow(object $row): array
    {
        return array_merge(
            [
                'master_layanan_id' => $row->master_layanan_id ? (int) $row->master_layanan_id : null,
                'nama_layanan' => $row->nama_layanan,
                'total_pasien' => (int) ($row->total_pasien ?? 0),
                'total_lab' => (int) ($row->total_lab ?? 0),
                'jml_hari' => (int) ($row->jml_hari ?? 0),
                'jml_visit' => (int) ($row->jml_visit ?? 0),
            ],
            $this->mapMoneyFields($row),
        );
    }

    private function mapMoneyFields(object $row): array
    {
        $mapped = [];

        foreach (self::MONEY_COLUMNS as $column) {
            $mapped[$column] = $this->money($row->{$column} ?? 0);
        }

        return $mapped;
    }

    private function emptyPoliRow(): array
    {
        return array_merge(
            [
                'master_layanan_id' => null,
                'kode_layanan' => null,
                'nama_layanan' => 'Tanpa Layanan',
                'urutan_laporan' => 9999,
                'total_pasien' => 0,
                'total_lab' => 0,
                'jml_hari' => 0,
                'jml_visit' => 0,
            ],
            collect(self::MONEY_COLUMNS)
                ->mapWithKeys(fn (string $column) => [$column => 0.0])
                ->all(),
        );
    }

    private function emptyInputSummary(): array
    {
        return array_merge(
            [
                'total_pasien' => 0,
                'total_lab' => 0,
                'jml_hari' => 0,
                'jml_visit' => 0,
            ],
            collect(self::MONEY_COLUMNS)
                ->mapWithKeys(fn (string $column) => [$column => 0.0])
                ->all(),
        );
    }

    private function sumSelectRaw(): string
    {
        return collect(self::MONEY_COLUMNS)
            ->map(fn (string $column) => "SUM({$column}) as {$column}")
            ->implode(', ');
    }

    private function money(mixed $value): float
    {
        return round((float) $value, 2);
    }

    private function normalizeKey(string $value): string
    {
        return str($value)
            ->lower()
            ->trim()
            ->replaceMatches('/\s+/', ' ')
            ->value();
    }
}
