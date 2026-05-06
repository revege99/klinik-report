<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RekapPasienUpdate extends Model
{
    use HasFactory;

    protected $table = 'rekap_pasien_updates';

    protected $fillable = [
        'clinic_profile_id',
        'user_id',
        'tanggal_data',
        'total_data',
        'synced_at',
    ];

    protected function casts(): array
    {
        return [
            'clinic_profile_id' => 'integer',
            'user_id' => 'integer',
            'tanggal_data' => 'date',
            'total_data' => 'integer',
            'synced_at' => 'datetime',
        ];
    }

    public function clinicProfile(): BelongsTo
    {
        return $this->belongsTo(ClinicProfile::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
