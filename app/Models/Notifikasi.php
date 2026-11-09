<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notifikasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'judul',
        'pesan',
        'tipe',
        'is_read',
        'link',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
        ];
    }

    public function getDibacaAttribute(): bool
    {
        return (bool) $this->is_read;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function kirim(int $userId, string $judul, string $pesan, string $tipe = 'info', ?string $link = null): self
    {
        return self::create([
            'user_id' => $userId,
            'judul' => $judul,
            'pesan' => $pesan,
            'tipe' => $tipe,
            'is_read' => false,
            'link' => $link,
        ]);
    }
}
