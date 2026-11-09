<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminFakultasRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'email',
        'fakultas_id',
        'nip',
        'alasan',
        'status',
        'catatan_super_admin',
        'created_user_id',
    ];

    public function fakultas(): BelongsTo
    {
        return $this->belongsTo(Fakultas::class);
    }

    public function createdUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_user_id');
    }
}
