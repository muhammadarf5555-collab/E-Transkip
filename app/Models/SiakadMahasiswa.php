<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiakadMahasiswa extends Model
{
    use HasFactory;

    protected $table = 'siakad_mahasiswas';

    protected $fillable = [
        'mahasiswa_id',
        'nim',
        'nama',
        'fakultas',
        'program_studi',
        'angkatan',
        'sumber_data',
        'imported_at',
    ];

    protected function casts(): array
    {
        return [
            'imported_at' => 'datetime',
        ];
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }
}
