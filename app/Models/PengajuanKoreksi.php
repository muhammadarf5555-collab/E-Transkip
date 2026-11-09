<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengajuanKoreksi extends Model
{
    use HasFactory;

    protected $fillable = [
        'mahasiswa_id',
        'siakad_nilai_id',
        'mata_kuliah_id',
        'jenis_koreksi',
        'nilai_lama',
        'nilai_diajukan',
        'alasan',
        'dokumen_pendukung',
        'status',
        'catatan_admin',
        'reviewed_by',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
        ];
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function siakadNilai(): BelongsTo
    {
        return $this->belongsTo(SiakadNilai::class);
    }

    public function mataKuliah(): BelongsTo
    {
        return $this->belongsTo(SiakadMataKuliah::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
