<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BpjsKlaimBulanan extends Model
{
    use HasFactory;

    protected $table = 'bpjs_klaim_bulanan';

    protected $fillable = [
        'clinic_profile_id',
        'user_id',
        'master_komponen_selisih_id',
        'bulan',
        'tahun',
        'tanggal_terima',
        'total_klaim',
        'total_versi_klinik',
        'total_komponen_acuan',
        'jumlah_komponen_acuan',
        'selisih_nominal',
        'selisih_persen',
        'selisih_arah',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'clinic_profile_id' => 'integer',
            'user_id' => 'integer',
            'master_komponen_selisih_id' => 'integer',
            'bulan' => 'integer',
            'tahun' => 'integer',
            'tanggal_terima' => 'date',
            'total_klaim' => 'float',
            'total_versi_klinik' => 'float',
            'total_komponen_acuan' => 'float',
            'jumlah_komponen_acuan' => 'integer',
            'selisih_nominal' => 'float',
            'selisih_persen' => 'float',
        ];
    }

    public function scopeForBulan(Builder $query, int $bulan, int $tahun): Builder
    {
        return $query->where('bulan', $bulan)
            ->where('tahun', $tahun);
    }

    public function clinicProfile(): BelongsTo
    {
        return $this->belongsTo(ClinicProfile::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function masterKomponenSelisih(): BelongsTo
    {
        return $this->belongsTo(MasterKomponenTransaksi::class, 'master_komponen_selisih_id');
    }

    public function alokasiKomponen(): HasMany
    {
        return $this->hasMany(BpjsKlaimAlokasiKomponen::class, 'bpjs_klaim_bulanan_id');
    }
}
