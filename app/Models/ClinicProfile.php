<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicProfile extends Model
{
    use HasFactory;

    protected $table = 'clinic_profiles';

    protected $fillable = [
        'nama_klinik',
        'nama_pendek',
        'tagline',
        'alamat',
        'kota',
        'provinsi',
        'kode_pos',
        'telepon',
        'email',
        'website',
        'penanggung_jawab',
        'jam_pelayanan',
        'deskripsi_singkat',
    ];
}
