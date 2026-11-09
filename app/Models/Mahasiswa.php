<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Mahasiswa extends Model
{
    use HasFactory;

    protected $table = 'mahasiswas';

    protected $fillable = [
        'user_id',
        'nim',
        'nama',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'email',
        'no_hp',
        'fakultas_id',
        'program_studi_id',
        'jenjang',
        'angkatan',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fakultas(): BelongsTo
    {
        return $this->belongsTo(Fakultas::class, 'fakultas_id');
    }

    public function programStudi(): BelongsTo
    {
        return $this->belongsTo(ProgramStudi::class, 'program_studi_id');
    }

    public function siakadMahasiswa(): HasOne
    {
        return $this->hasOne(SiakadMahasiswa::class, 'mahasiswa_id');
    }

    public function siakadNilais(): HasMany
    {
        return $this->hasMany(SiakadNilai::class, 'mahasiswa_id');
    }

    public function wisudawan(): HasOne
    {
        return $this->hasOne(Wisudawan::class, 'mahasiswa_id');
    }

    public function transkrip(): HasOne
    {
        return $this->hasOne(Transkrip::class, 'mahasiswa_id');
    }

    public function pengajuanKoreksis(): HasMany
    {
        return $this->hasMany(PengajuanKoreksi::class, 'mahasiswa_id');
    }
}
