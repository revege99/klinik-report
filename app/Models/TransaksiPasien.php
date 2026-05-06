<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransaksiPasien extends Model
{
    use HasFactory;

    protected $table = 'transaksi_pasien';

    protected $fillable = [
        'clinic_profile_id',
        'simrs_no_rawat',
        'simrs_no_reg',
        'tanggal',
        'bulan',
        'harian',
        'layanan_medis',
        'user_id',
        'dokter',
        'penjamin',
        'no_rm',
        'nama_pasien',
        'jk',
        'statis',
        'genap',
        'status_pasien',
        'alamat',
        'lab',
        'icd',
        'diagnosa',
        'farmasi',
        'jml_hari',
        'jml_visit',
        'jumlah_rp',
        'jumlah_kredit',
        'petugas_admin',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'clinic_profile_id' => 'integer',
            'tanggal' => 'date',
            'bulan' => 'integer',
            'user_id' => 'integer',
            'jml_hari' => 'integer',
            'jml_visit' => 'integer',
            'jumlah_rp' => 'decimal:2',
            'jumlah_kredit' => 'decimal:2',
        ];
    }

    public function masterLayanan(): BelongsTo
    {
        return $this->belongsTo(MasterLayanan::class, 'layanan_medis', 'kode_layanan');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function clinicProfile(): BelongsTo
    {
        return $this->belongsTo(ClinicProfile::class);
    }

    public function komponenTransaksi(): HasMany
    {
        return $this->hasMany(TransaksiPasienKomponen::class);
    }

    public function administrasiTransaksi(): HasMany
    {
        return $this->hasMany(TransaksiPasienAdministrasi::class);
    }
}
