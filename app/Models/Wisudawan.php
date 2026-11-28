<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Wisudawan extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::saved(function (Wisudawan $wisudawan) {
            if ($wisudawan->wasChanged(['nama', 'nim'])) {
                $mhs = $wisudawan->mahasiswa ?: Mahasiswa::where('nim', $wisudawan->nim)->first();
                if ($mhs) {
                    $mhs->updateQuietly([
                        'nama' => $wisudawan->nama,
                        'nim' => $wisudawan->nim,
                    ]);
                    if ($mhs->user) {
                        $mhs->user->updateQuietly(['name' => $wisudawan->nama]);
                    }
                }
            }

            if ($wisudawan->mahasiswa_id) {
                $transkrip = Transkrip::where('mahasiswa_id', $wisudawan->mahasiswa_id)->first();
                if ($transkrip && $transkrip->wisudawan_id !== $wisudawan->id) {
                    $transkrip->updateQuietly(['wisudawan_id' => $wisudawan->id]);
                }
            }
        });
    }

    protected $table = 'wisudawans';

    protected $fillable = [
        'mahasiswa_id',
        'nim',
        'nama',
        'nomor_pendaftaran_wisuda',
        'periode_wisuda',
        'tanggal_wisuda',
        'fakultas',
        'program_studi',
        'no_sk',
        'tanggal_sk',
        'no_ijazah',
        'tanggal_ijazah',
        'ipk',
        'total_sks',
        'lama_studi',
        'semester_lulus',
        'gelar',
        'gelar_full',
        'judul_skripsi',
        'predikat_kelulusan',
        'is_eligible',
        'status_validasi',
        'sumber_data',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_wisuda' => 'date',
            'tanggal_sk' => 'date',
            'tanggal_ijazah' => 'date',
            'ipk' => 'decimal:2',
            'total_sks' => 'integer',
            'lama_studi' => 'integer',
            'is_eligible' => 'boolean',
        ];
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }

    public function transkrip(): HasOne
    {
        return $this->hasOne(Transkrip::class, 'wisudawan_id');
    }
}
