<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SiakadMataKuliah extends Model
{
    use HasFactory;

    protected $table = 'siakad_mata_kuliahs';

    protected $fillable = [
        'kode_mata_kuliah',
        'nama_mata_kuliah',
        'sks',
        'semester',
        'jenis_mata_kuliah',
    ];

    public function nilais(): HasMany
    {
        return $this->hasMany(SiakadNilai::class, 'mata_kuliah_id');
    }
}
