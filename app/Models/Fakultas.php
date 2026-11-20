<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fakultas extends Model
{
    use HasFactory;

    protected $table = 'fakultas';

    protected $fillable = [
        'kode_fakultas',
        'nama_fakultas',
        'nama_fakultas_singkat',
        'kode_romawi',
        'nama_dekan',
        'nip_dekan',
        'jabatan_dekan',
        'no_sk_banpt',
    ];

    public function programStudis(): HasMany
    {
        return $this->hasMany(ProgramStudi::class, 'fakultas_id');
    }

    public function mahasiswas(): HasMany
    {
        return $this->hasMany(Mahasiswa::class, 'fakultas_id');
    }
}
