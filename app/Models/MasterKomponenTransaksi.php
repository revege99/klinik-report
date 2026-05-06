<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterKomponenTransaksi extends Model
{
    use HasFactory;

    protected $table = 'master_komponen_transaksi';

    protected $fillable = [
        'kode_komponen',
        'nama_komponen',
        'field_key',
        'arah_laporan',
        'ikut_alokasi_bpjs',
        'basis_pajak_obat',
        'peran_sistem',
        'urutan_laporan',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'urutan_laporan' => 'integer',
            'ikut_alokasi_bpjs' => 'boolean',
            'basis_pajak_obat' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function transaksiKomponen(): HasMany
    {
        return $this->hasMany(TransaksiPasienKomponen::class, 'master_komponen_transaksi_id');
    }

    public function bpjsKlaimAlokasiKomponen(): HasMany
    {
        return $this->hasMany(BpjsKlaimAlokasiKomponen::class, 'master_komponen_transaksi_id');
    }
}
