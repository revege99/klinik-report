<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RekapPasien extends Model
{
    use HasFactory;

    protected $table = 'rekap_pasien';

    protected $fillable = [
        'master_layanan_id',
        'tanggal',
        'bulan',
        'tahun',
        'harian',
        'layanan_medis',
        'no_rm',
        'nama_pasien',
        'jk',
        'statis_genap',
        'status_pasien',
        'jenis_bayar',
        'alamat',
        'lab',
        'icd',
        'diagnosa',
        'farmasi',
        'uang_daftar',
        'uang_periksa',
        'uang_obat',
        'uang_bersalin',
        'jasa_dokter',
        'jml_hari',
        'rawat_inap',
        'jml_visit',
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
        'petugas_admin',
        'simrs_ref',
        'synced_at',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'bulan' => 'integer',
            'tahun' => 'integer',
            'uang_daftar' => 'decimal:2',
            'uang_periksa' => 'decimal:2',
            'uang_obat' => 'decimal:2',
            'uang_bersalin' => 'decimal:2',
            'jasa_dokter' => 'decimal:2',
            'jml_hari' => 'integer',
            'rawat_inap' => 'decimal:2',
            'jml_visit' => 'integer',
            'honor_visit' => 'decimal:2',
            'oksigen' => 'decimal:2',
            'perlengkapan_bayi' => 'decimal:2',
            'jaspel_nakes' => 'decimal:2',
            'bmhp' => 'decimal:2',
            'pkl_dll' => 'decimal:2',
            'lain_lain' => 'decimal:2',
            'jumlah_rp' => 'decimal:2',
            'utang_pasien' => 'decimal:2',
            'bayar_utang_pasien' => 'decimal:2',
            'derma_solidaritas' => 'decimal:2',
            'saldo_kredit' => 'decimal:2',
            'saldo_kredit2' => 'decimal:2',
            'synced_at' => 'datetime',
        ];
    }

    public function scopeForBulan(Builder $query, int $bulan, int $tahun): Builder
    {
        return $query->where('bulan', $bulan)
            ->where('tahun', $tahun);
    }

    public function scopeForTahun(Builder $query, int $tahun): Builder
    {
        return $query->where('tahun', $tahun);
    }

    public function masterLayanan(): BelongsTo
    {
        return $this->belongsTo(MasterLayanan::class);
    }
}
