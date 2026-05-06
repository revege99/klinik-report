<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterAdministrasiPasien extends Model
{
    use HasFactory;

    protected $table = 'master_administrasi_pasien';

    protected $fillable = [
        'kode_administrasi',
        'nama_administrasi',
        'field_key',
        'arah_laporan',
        'urutan_laporan',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'urutan_laporan' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function transaksiAdministrasi(): HasMany
    {
        return $this->hasMany(TransaksiPasienAdministrasi::class, 'master_administrasi_pasien_id');
    }
}
