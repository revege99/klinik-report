<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransaksiPasienAdministrasi extends Model
{
    use HasFactory;

    protected $table = 'transaksi_pasien_administrasi';

    protected $fillable = [
        'transaksi_pasien_id',
        'master_administrasi_pasien_id',
        'nominal',
    ];

    protected function casts(): array
    {
        return [
            'transaksi_pasien_id' => 'integer',
            'master_administrasi_pasien_id' => 'integer',
            'nominal' => 'decimal:2',
        ];
    }

    public function transaksiPasien(): BelongsTo
    {
        return $this->belongsTo(TransaksiPasien::class);
    }

    public function masterAdministrasiPasien(): BelongsTo
    {
        return $this->belongsTo(MasterAdministrasiPasien::class, 'master_administrasi_pasien_id');
    }
}
