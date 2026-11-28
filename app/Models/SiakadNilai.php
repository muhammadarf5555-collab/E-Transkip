<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SiakadNilai extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::saving(function (SiakadNilai $nilai) {
            if ($nilai->nilai_angka === null) {
                $nilai->nilai_angka = (float) ($nilai->bobot * 25);
            }
        });

        static::saved(function (SiakadNilai $nilai) {
            $nilai->mahasiswa?->recalculateTranskrip();
        });

        static::deleted(function (SiakadNilai $nilai) {
            $nilai->mahasiswa?->recalculateTranskrip();
        });
    }

    protected $table = 'siakad_nilais';

    protected $fillable = [
        'mahasiswa_id',
        'mata_kuliah_id',
        'semester',
        'nilai_huruf',
        'nilai_angka',
        'bobot',
        'sks',
        'sumber_data',
        'has_conflict',
        'conflict_details',
        'imported_at',
    ];

    protected function casts(): array
    {
        return [
            'nilai_angka' => 'float',
            'bobot' => 'float',
            'sks' => 'integer',
            'has_conflict' => 'boolean',
            'imported_at' => 'datetime',
        ];
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }

    public function mataKuliah(): BelongsTo
    {
        return $this->belongsTo(SiakadMataKuliah::class, 'mata_kuliah_id');
    }

    public function changeHistories(): HasMany
    {
        return $this->hasMany(NilaiChangeHistory::class, 'siakad_nilai_id');
    }

    public function koreksiRequests(): HasMany
    {
        return $this->hasMany(PengajuanKoreksi::class, 'siakad_nilai_id');
    }
}
