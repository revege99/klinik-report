<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'username', 'email', 'password', 'role', 'is_active', 'last_login_at', 'clinic_profile_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
            'clinic_profile_id' => 'integer',
            'password' => 'hashed',
        ];
    }

    public function clinicProfile(): BelongsTo
    {
        return $this->belongsTo(ClinicProfile::class);
    }

    public function pegawaiProfile(): HasOne
    {
        return $this->hasOne(PegawaiProfile::class);
    }

    public function isStaff(): bool
    {
        return strtolower((string) $this->role) === 'staff';
    }

    public function isAdmin(): bool
    {
        return strtolower((string) $this->role) === 'admin';
    }

    public function isMaster(): bool
    {
        return strtolower((string) $this->role) === 'master';
    }

    public function canManageUsers(): bool
    {
        return $this->isMaster();
    }

    public function canManageMasters(): bool
    {
        return $this->isMaster();
    }

    public function canManageClinicProfile(): bool
    {
        return $this->isAdmin() || $this->isMaster();
    }

    public function canViewReports(): bool
    {
        return $this->isAdmin() || $this->isMaster();
    }

    public function canEditOperationalData(): bool
    {
        return $this->isAdmin() || $this->isMaster();
    }
}
