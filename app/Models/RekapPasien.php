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
        'clinic_profile_id',
        'master_layanan_id',
        'tanggal',
        'bulan',
        'tahun',
        'no_rawat',
        'no_rm',
        'nama_pasien',
        'layanan_medis',
        'synced_at',
    ];

    protected function casts(): array
    {
        return [
            'clinic_profile_id' => 'integer',
            'master_layanan_id' => 'integer',
            'tanggal' => 'date',
            'bulan' => 'integer',
            'tahun' => 'integer',
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

    public function clinicProfile(): BelongsTo
    {
        return $this->belongsTo(ClinicProfile::class);
    }

    public function masterLayanan(): BelongsTo
    {
        return $this->belongsTo(MasterLayanan::class);
    }
}
