<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AturanBobotNilai extends Model
{
    use HasFactory;

    protected $fillable = [
        'nilai_huruf',
        'bobot',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'bobot' => 'float',
        ];
    }

    /**
     * Standard grade weights required by academic guidelines:
     * A = 4.00, A- = 3.50, B+ = 3.25, B = 3.00, B- = 2.75,
     * C+ = 2.25, C = 2.00, D = 1.00, E = 0.00
     */
    public static function getBobot(string $nilaiHuruf): float
    {
        $nilaiHuruf = strtoupper(trim($nilaiHuruf));

        $standardMap = [
            'A' => 4.00,
            'A-' => 3.50,
            'B+' => 3.25,
            'B' => 3.00,
            'B-' => 2.75,
            'C+' => 2.25,
            'C' => 2.00,
            'D' => 1.00,
            'E' => 0.00,
        ];

        // Check database first if customized
        $rule = static::where('nilai_huruf', $nilaiHuruf)->first();
        if ($rule) {
            return (float) $rule->bobot;
        }

        return $standardMap[$nilaiHuruf] ?? 0.00;
    }
}
