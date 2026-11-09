<?php

namespace Database\Seeders;

use App\Models\Mahasiswa;
use App\Models\Wisudawan;
use App\Services\SiakadApiService;
use Illuminate\Database\Seeder;

class WisudawanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $service = app(SiakadApiService::class);

        $existingNims = Wisudawan::pluck('nim')->filter()->values()->all();

        // Ambil perwakilan mahasiswa dari berbagai fakultas yang memiliki SKS tinggi / siap yudisium
        $sampleRealNims = Mahasiswa::whereIn('nim', [
            '2281050039', // FITK - Tadris Matematika
            '2381010152', // FITK - PAI
            '2381090107', // FITK - MPI
            '2388010001', // FITK - Teknik Informatika (Siti Malikha)
            '2388010002', // FITK - Teknik Informatika (Ananda Sathria Maulana Amri)
            '2388010003', // FITK - Teknik Informatika (Muhamad Arya Pangestu)
        ])->pluck('nim')->all();

        // Tambah perwakilan dari fakultas lain jika ada yang total_sks >= 120
        $otherFacultyGraduates = Mahasiswa::whereNotIn('fakultas_id', [1])
            ->whereHas('transkrip', function ($q) {
                $q->where('total_sks', '>=', 110);
            })
            ->orderByDesc('id')
            ->limit(6)
            ->pluck('nim')
            ->all();

        $allNims = array_values(array_unique(array_merge($existingNims, $sampleRealNims, $otherFacultyGraduates)));

        if (! empty($allNims)) {
            $service->syncWisudawanFromEwisuda($allNims, null, true, 'Periode II Tahun 2026');
        }
    }
}
