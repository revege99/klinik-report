<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterLayanan extends Model
{
    use HasFactory;

    protected $table = 'master_layanan';

    protected $fillable = [
        'kode_layanan',
        'nama_layanan',
        'simrs_kd_poli',
        'simrs_nm_poli',
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

    public function rekapPasien(): HasMany
    {
        return $this->hasMany(RekapPasien::class);
    }
}
