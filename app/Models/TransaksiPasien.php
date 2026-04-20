<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransaksiPasien extends Model
{
    use HasFactory;

    protected $table = 'transaksi_pasien';

    protected $fillable = [
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
        'uang_daftar',
        'uang_periksa',
        'uang_obat',
        'uang_bersalin',
        'jasa_dokter',
        'jml_hari',
        'rawat_inap',
        'jml_visit',
        'honor_dr_visit',
        'oksigen',
        'perlengk_bayi',
        'jaspel_nakes',
        'bmhp',
        'pkl',
        'lain_lain',
        'jumlah_rp',
        'utang_pasien',
        'utang',
        'bayar_utang_pasien',
        'derma_solidaritas',
        'saldo_kredit',
        'saldo',
        'petugas_admin',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'bulan' => 'integer',
            'user_id' => 'integer',
            'uang_daftar' => 'decimal:2',
            'uang_periksa' => 'decimal:2',
            'uang_obat' => 'decimal:2',
            'uang_bersalin' => 'decimal:2',
            'jasa_dokter' => 'decimal:2',
            'jml_hari' => 'integer',
            'rawat_inap' => 'decimal:2',
            'jml_visit' => 'integer',
            'honor_dr_visit' => 'decimal:2',
            'oksigen' => 'decimal:2',
            'perlengk_bayi' => 'decimal:2',
            'jaspel_nakes' => 'decimal:2',
            'bmhp' => 'decimal:2',
            'pkl' => 'decimal:2',
            'lain_lain' => 'decimal:2',
            'jumlah_rp' => 'decimal:2',
            'utang_pasien' => 'decimal:2',
            'utang' => 'decimal:2',
            'bayar_utang_pasien' => 'decimal:2',
            'derma_solidaritas' => 'decimal:2',
            'saldo_kredit' => 'decimal:2',
            'saldo' => 'decimal:2',
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
}
