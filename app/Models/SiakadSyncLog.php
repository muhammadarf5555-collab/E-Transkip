<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiakadSyncLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'fakultas_id',
        'endpoint',
        'status',
        'total_berhasil',
        'total_gagal',
        'total_diperbarui',
        'total_prodi',
        'total_mahasiswa',
        'total_nilai',
        'konflik_count',
        'keterangan',
        'pesan_error',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fakultas(): BelongsTo
    {
        return $this->belongsTo(Fakultas::class);
    }
}
