<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterKategoriPengeluaran extends Model
{
    use HasFactory;

    protected $table = 'master_kategori_pengeluaran';

    protected $fillable = [
        'kode_kategori',
        'nama_kategori',
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

    public function pengeluaran(): HasMany
    {
        return $this->hasMany(Pengeluaran::class);
    }
}
