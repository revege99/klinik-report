<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransaksiPasienKomponen extends Model
{
    use HasFactory;

    protected $table = 'transaksi_pasien_komponen';

    protected $fillable = [
        'transaksi_pasien_id',
        'master_komponen_transaksi_id',
        'nominal',
    ];

    protected function casts(): array
    {
        return [
            'transaksi_pasien_id' => 'integer',
            'master_komponen_transaksi_id' => 'integer',
            'nominal' => 'decimal:2',
        ];
    }

    public function transaksiPasien(): BelongsTo
    {
        return $this->belongsTo(TransaksiPasien::class);
    }

    public function masterKomponenTransaksi(): BelongsTo
    {
        return $this->belongsTo(MasterKomponenTransaksi::class, 'master_komponen_transaksi_id');
    }
}
