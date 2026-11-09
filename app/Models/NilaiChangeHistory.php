<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NilaiChangeHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'siakad_nilai_id',
        'user_id',
        'nilai_huruf_lama',
        'bobot_lama',
        'nilai_huruf_baru',
        'bobot_baru',
        'alasan',
    ];

    protected function casts(): array
    {
        return [
            'bobot_lama' => 'float',
            'bobot_baru' => 'float',
        ];
    }

    public function siakadNilai(): BelongsTo
    {
        return $this->belongsTo(SiakadNilai::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
