<?php

namespace Database\Seeders;

use App\Models\Fakultas;
use App\Models\Mahasiswa;
use App\Models\ProgramStudi;
use App\Models\Transkrip;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class KknMahasiswaSeeder extends Seeder
{
    public function run(): void
    {
        $jsonPath = 'C:/Users/H.Muklas/.gemini/antigravity/brain/969855d9-5fb9-4209-b66a-9d1e178d22d2/scratch/kkn_students.json';

        if (! file_exists($jsonPath)) {
            $this->command->error("File tidak ditemukan: {$jsonPath}");

            return;
        }

        $students = json_decode(file_get_contents($jsonPath), true);
        if (! is_array($students) || empty($students)) {
            $this->command->error('Format data kkn_students.json kosong atau tidak valid.');

            return;
        }

        $this->command->info('Memproses '.count($students).' mahasiswa resmi dari data KKN...');

        // Preload Program Studi by Kode
        $prodis = ProgramStudi::all();
        $prodiByKode = [];
        $prodiByFakultas = [];
        foreach ($prodis as $p) {
            if ($p->kode_prodi) {
                $prodiByKode[(string) $p->kode_prodi] = $p;
            }
            $prodiByFakultas[$p->fakultas_id][] = $p;
        }

        // Faculty digit mapping: 1->FITK(1), 2->FEBI(6), 3->FS(3), 4->FDKI(7), 5->FUA(4), 8->FST(13)
        $facDigitMap = [
            '1' => 1,
            '2' => 6,
            '3' => 3,
            '4' => 7,
            '5' => 4,
            '8' => 13,
        ];

        $defaultPassword = Hash::make('password');
        $createdCount = 0;
        $skippedCount = 0;

        DB::beginTransaction();

        try {
            foreach ($students as $item) {
                $nim = trim($item['nim'] ?? '');
                $nama = trim($item['nama'] ?? '');
                $jk = ($item['jenis_kelamin'] ?? 'L') === 'Perempuan' || ($item['jenis_kelamin'] ?? '') === 'P' ? 'P' : 'L';
                $noHp = trim($item['no_hp'] ?? '');

                if (! $nim || ! $nama) {
                    continue;
                }

                // If mahasiswa already exists, skip creating so we don't alter existing students
                if (Mahasiswa::where('nim', $nim)->exists()) {
                    $skippedCount++;

                    continue;
                }

                // Determine fakultas & prodi
                $fakultasId = 1; // Default to FITK
                $prodiId = null;

                if (str_starts_with($nim, '2108107')) {
                    $fakultasId = 1;
                    $prodiCode = '107';
                } else {
                    $facDigit = substr($nim, 3, 1);
                    $fakultasId = $facDigitMap[$facDigit] ?? 1;
                    $prodiCode = substr($nim, 3, 3);
                }

                if (isset($prodiByKode[$prodiCode])) {
                    $prodiId = $prodiByKode[$prodiCode]->id;
                } elseif ($prodiCode === '221' || $prodiCode === '210') {
                    $prodiId = 7; // Ekonomi Syariah
                } elseif ($prodiCode === '810' || $prodiCode === '801') {
                    $prodiId = 1; // Teknik Informatika
                } else {
                    $prodiId = $prodiByFakultas[$fakultasId][0]->id ?? 1;
                }

                $email = "{$nim}@mahasiswa.uinssc.ac.id";
                $angkatanPrefix = substr($nim, 0, 2);
                $angkatan = is_numeric($angkatanPrefix) ? '20'.$angkatanPrefix : '2023';

                // Create user
                $user = User::firstOrCreate(
                    ['email' => $email],
                    [
                        'name' => $nama,
                        'password' => $defaultPassword,
                        'role' => 'mahasiswa',
                        'fakultas_id' => $fakultasId,
                        'status_aktif' => true,
                    ]
                );

                // Create mahasiswa
                $mhs = Mahasiswa::create([
                    'user_id' => $user->id,
                    'nim' => $nim,
                    'nama' => $nama,
                    'tempat_lahir' => 'Cirebon',
                    'tanggal_lahir' => '2004-01-01',
                    'jenis_kelamin' => $jk,
                    'email' => $email,
                    'no_hp' => $noHp ?: null,
                    'fakultas_id' => $fakultasId,
                    'program_studi_id' => $prodiId,
                    'jenjang' => 'S1',
                    'angkatan' => $angkatan,
                    'status' => 'Aktif',
                ]);

                // Initial transkrip
                Transkrip::firstOrCreate(
                    ['mahasiswa_id' => $mhs->id],
                    [
                        'kode_verifikasi' => 'UINSSC-TRX-'.$nim.'-'.strtoupper(Str::random(6)),
                        'total_sks' => 0,
                        'total_mutu' => 0.00,
                        'ipk' => 0.00,
                        'predikat' => 'Dalam Proses',
                        'status_validasi' => 'Draft',
                    ]
                );

                $createdCount++;
            }

            DB::commit();
            $this->command->info("Seeding selesai! Berhasil menambahkan {$createdCount} mahasiswa baru ({$skippedCount} dilewati karena sudah ada).");
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->command->error('Terjadi error: '.$e->getMessage());
            throw $e;
        }
    }
}
