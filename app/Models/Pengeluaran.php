<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengeluaran extends Model
{
    use HasFactory;

    protected $table = 'pengeluaran';

    protected $fillable = [
        'master_kategori_pengeluaran_id',
        'user_id',
        'tanggal',
        'bulan',
        'tahun',
        'kategori_pengeluaran',
        'deskripsi',
        'jumlah_rp',
        'petugas_admin',
        'keterangan',
        'simrs_ref',
        'synced_at',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'bulan' => 'integer',
            'tahun' => 'integer',
            'user_id' => 'integer',
            'jumlah_rp' => 'decimal:2',
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

    public function masterKategoriPengeluaran(): BelongsTo
    {
        return $this->belongsTo(MasterKategoriPengeluaran::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
