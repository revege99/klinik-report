<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClinicProfile extends Model
{
    use HasFactory;

    protected $table = 'clinic_profiles';

    protected $fillable = [
        'kode_klinik',
        'nama_klinik',
        'nama_pendek',
        'tagline',
        'alamat',
        'kecamatan',
        'kota',
        'provinsi',
        'kode_pos',
        'logo_path',
        'telepon',
        'email',
        'website',
        'penanggung_jawab',
        'jam_pelayanan',
        'deskripsi_singkat',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function transaksiPasien(): HasMany
    {
        return $this->hasMany(TransaksiPasien::class);
    }

    public function pengeluaran(): HasMany
    {
        return $this->hasMany(Pengeluaran::class);
    }

    public function bpjsKlaimBulanan(): HasMany
    {
        return $this->hasMany(BpjsKlaimBulanan::class);
    }

    public function rekapPasien(): HasMany
    {
        return $this->hasMany(RekapPasien::class);
    }

    public function rekapPasienUpdates(): HasMany
    {
        return $this->hasMany(RekapPasienUpdate::class);
    }

    public function databaseConnections(): HasMany
    {
        return $this->hasMany(ClinicDatabaseConnection::class);
    }
}
