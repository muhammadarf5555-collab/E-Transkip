<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transkrip extends Model
{
    use HasFactory;

    protected $table = 'transkrips';

    protected $fillable = [
        'mahasiswa_id',
        'wisudawan_id',
        'total_sks',
        'total_mutu',
        'ipk',
        'predikat',
        'status_validasi',
        'verified_by_jurusan_id',
        'verified_by_jurusan_at',
        'ratified_by_admin_id',
        'ratified_by_admin_at',
        'kode_verifikasi',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'total_sks' => 'integer',
            'total_mutu' => 'float',
            'ipk' => 'float',
            'verified_by_jurusan_at' => 'datetime',
            'ratified_by_admin_at' => 'datetime',
        ];
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }

    public function wisudawan(): BelongsTo
    {
        return $this->belongsTo(Wisudawan::class, 'wisudawan_id');
    }

    public function verifiedByJurusan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by_jurusan_id');
    }

    public function ratifiedByAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ratified_by_admin_id');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(TranskripHistory::class, 'transkrip_id')->latest();
    }

    /**
     * Check if transcript has been finalized & ratified by Super Admin.
     */
    public function isDisahkan(): bool
    {
        return in_array($this->status_validasi, ['disahkan', 'diterbitkan', 'Valid'], true);
    }

    /**
     * Check if transcript is verified by department and waiting for Super Admin ratification.
     */
    public function isMenungguPengesahan(): bool
    {
        return in_array($this->status_validasi, ['menunggu_pengesahan', 'terverifikasi'], true);
    }

    /**
     * Check if transcript needs review/verification by department admin.
     */
    public function isMenungguVerifikasi(): bool
    {
        return in_array($this->status_validasi, ['menunggu_verifikasi', 'Menunggu Validasi', 'Draft', 'draft'], true);
    }

    /**
     * Check if transcript needs revision.
     */
    public function isPerluPerbaikan(): bool
    {
        return in_array($this->status_validasi, ['perlu_perbaikan', 'ditolak', 'Ditolak'], true);
    }

    /**
     * Helper to record status change history.
     */
    public function recordStatusChange(?int $userId, string $statusBaru, ?string $catatan = null): TranskripHistory
    {
        $statusSebelumnya = (string) ($this->status_validasi ?? 'draft');

        $history = TranskripHistory::create([
            'transkrip_id' => $this->id,
            'user_id' => $userId,
            'status_sebelumnya' => $statusSebelumnya,
            'status_terbaru' => $statusBaru,
            'catatan' => $catatan,
        ]);

        $this->update([
            'status_validasi' => $statusBaru,
            'catatan' => $catatan ?? $this->catatan,
        ]);

        return $history;
    }
}
