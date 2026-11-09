<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TranskripHistory extends Model
{
    use HasFactory;

    protected $table = 'transkrip_histories';

    protected $fillable = [
        'transkrip_id',
        'user_id',
        'status_sebelumnya',
        'status_terbaru',
        'catatan',
    ];

    public function transkrip(): BelongsTo
    {
        return $this->belongsTo(Transkrip::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
