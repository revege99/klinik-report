<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClinicDatabaseConnection extends Model
{
    use HasFactory;

    protected $table = 'clinic_database_connections';

    protected $fillable = [
        'clinic_profile_id',
        'connection_role',
        'driver',
        'server_name',
        'host',
        'zero_tier_ip',
        'port',
        'database',
        'username',
        'password',
        'charset',
        'collation',
        'is_active',
        'notes',
        'last_verified_at',
    ];

    protected function casts(): array
    {
        return [
            'clinic_profile_id' => 'integer',
            'port' => 'integer',
            'password' => 'encrypted',
            'is_active' => 'boolean',
            'last_verified_at' => 'datetime',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function clinicProfile(): BelongsTo
    {
        return $this->belongsTo(ClinicProfile::class);
    }
}
