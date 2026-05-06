<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BpjsKlaimAlokasiKomponen extends Model
{
    use HasFactory;

    protected $table = 'bpjs_klaim_alokasi_komponen';

    protected $fillable = [
        'bpjs_klaim_bulanan_id',
        'master_komponen_transaksi_id',
        'kode_komponen',
        'nama_komponen',
        'basis_nominal',
        'persentase',
        'nominal_alokasi',
        'basis_pajak_obat',
        'urutan_laporan',
    ];

    protected function casts(): array
    {
        return [
            'bpjs_klaim_bulanan_id' => 'integer',
            'master_komponen_transaksi_id' => 'integer',
            'basis_nominal' => 'float',
            'persentase' => 'float',
            'nominal_alokasi' => 'float',
            'basis_pajak_obat' => 'boolean',
            'urutan_laporan' => 'integer',
        ];
    }

    public function klaimBulanan(): BelongsTo
    {
        return $this->belongsTo(BpjsKlaimBulanan::class, 'bpjs_klaim_bulanan_id');
    }

    public function masterKomponen(): BelongsTo
    {
        return $this->belongsTo(MasterKomponenTransaksi::class, 'master_komponen_transaksi_id');
    }
}
