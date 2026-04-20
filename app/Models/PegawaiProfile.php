<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PegawaiProfile extends Model
{
    use HasFactory;

    protected $table = 'pegawai_profiles';

    protected $fillable = [
        'user_id',
        'nip',
        'jabatan',
        'unit_kerja',
        'phone_number',
        'bio',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
