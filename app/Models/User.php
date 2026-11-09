<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'fakultas_id',
        'status_aktif',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status_aktif' => 'boolean',
        ];
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdminFakultas(): bool
    {
        return $this->role === 'admin_fakultas' || $this->role === 'admin';
    }

    public function isAdmin(): bool
    {
        return $this->isSuperAdmin() || $this->isAdminFakultas();
    }

    public function isMahasiswa(): bool
    {
        return $this->role === 'mahasiswa';
    }

    /**
     * Get accessible faculty IDs.
     * Note: FITK (ID 1) also encompasses Informatika (which is administratively 'numpang' under FITK).
     */
    public function getAccessibleFakultasIds(): array
    {
        if (! $this->fakultas_id) {
            return [];
        }

        if ((int) $this->fakultas_id === 1) {
            return [1, 13];
        }

        return [(int) $this->fakultas_id];
    }

    /**
     * Check if user has permission to manage a student.
     */
    public function canAccessMahasiswa(Mahasiswa $mahasiswa): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        if (! $this->fakultas_id) {
            return false;
        }

        // FITK admin can access FITK students AND Informatika students (numpang di FITK)
        if ((int) $this->fakultas_id === 1) {
            return (int) $mahasiswa->fakultas_id === 1 || (int) $mahasiswa->program_studi_id === 1 || (int) $mahasiswa->fakultas_id === 13;
        }

        // Saintek admin (ID 13) can access Saintek students & Informatika
        if ((int) $this->fakultas_id === 13) {
            return (int) $mahasiswa->fakultas_id === 13 || (int) $mahasiswa->program_studi_id === 1;
        }

        return (int) $mahasiswa->fakultas_id === (int) $this->fakultas_id;
    }

    public function fakultas(): BelongsTo
    {
        return $this->belongsTo(Fakultas::class);
    }

    public function mahasiswa(): HasOne
    {
        return $this->hasOne(Mahasiswa::class);
    }

    public function notifikasis(): HasMany
    {
        return $this->hasMany(Notifikasi::class);
    }
}
