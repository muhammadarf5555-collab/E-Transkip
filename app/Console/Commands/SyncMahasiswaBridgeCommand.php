<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\Fakultas;
use App\Models\Mahasiswa;
use App\Models\ProgramStudi;
use App\Models\SiakadMataKuliah;
use App\Models\SiakadNilai;
use App\Models\Transkrip;
use App\Models\User;
use App\Services\SiakadApiService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class SyncMahasiswaBridgeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bridge:sync-mahasiswa {nim} {--nama=} {--tempat=} {--tanggal=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronkan data dan nilai resmi mahasiswa langsung dari Bridge API UINSSC';

    /**
     * Execute the console command.
     */
    public function handle(SiakadApiService $service): int
    {
        $nim = (string) $this->argument('nim');

        // Cari data mahasiswa lama jika sudah ada di DB
        $existingMhs = Mahasiswa::where('nim', $nim)->first();

        // Tentukan nama, tempat, dan tanggal lahir secara dinamis tanpa menimpa data yang sudah ada
        $namaOption = $this->option('nama');
        $tempatOption = $this->option('tempat');
        $tanggalOption = $this->option('tanggal');

        if ($namaOption) {
            $nama = $namaOption;
        } elseif ($existingMhs && ! empty($existingMhs->nama)) {
            $nama = $existingMhs->nama;
        } elseif ($nim === '2388010017') {
            $nama = 'Muhammad Arif Rizky';
        } else {
            $nama = 'Mahasiswa '.$nim;
        }

        if ($tempatOption) {
            $tempat = $tempatOption;
        } elseif ($existingMhs && ! empty($existingMhs->tempat_lahir)) {
            $tempat = $existingMhs->tempat_lahir;
        } elseif ($nim === '2388010017') {
            $tempat = 'Subang';
        } else {
            $tempat = 'Cirebon';
        }

        if ($tanggalOption) {
            $tanggal = $tanggalOption;
        } elseif ($existingMhs && ! empty($existingMhs->tanggal_lahir)) {
            $tanggal = $existingMhs->tanggal_lahir instanceof Carbon ? $existingMhs->tanggal_lahir->format('Y-m-d') : (string) $existingMhs->tanggal_lahir;
        } elseif ($nim === '2388010017') {
            $tanggal = '2005-02-25';
        } else {
            $tanggal = '2004-01-01';
        }

        $this->info("Menghubungkan ke Bridge API UINSSC untuk NIM: {$nim} ({$nama})...");

        // 1. Pastikan Fakultas & Prodi sinkron
        $service->syncFakultasFromBridge();
        $service->syncProgramStudiFromBridge(1);

        $prodi = ProgramStudi::where('kode_prodi', '801')
            ->orWhere('nama_program_studi', 'like', '%Informatika%')
            ->first();

        // 2. Buat atau update User
        $user = User::updateOrCreate(
            ['email' => "{$nim}@mahasiswa.uinssc.ac.id"],
            [
                'name' => $nama,
                'password' => Hash::make('password'),
                'role' => 'mahasiswa',
                'status_aktif' => true,
            ]
        );

        // 3. Buat atau update Mahasiswa
        $mahasiswa = Mahasiswa::updateOrCreate(
            ['nim' => $nim],
            [
                'user_id' => $user->id,
                'nama' => $nama,
                'program_studi_id' => $prodi ? $prodi->id : 1,
                'semester' => 7,
                'tempat_lahir' => $tempat,
                'tanggal_lahir' => $tanggal,
                'status_kelulusan' => 'aktif',
            ]
        );

        $this->info("✓ Profil mahasiswa {$nama} berhasil disiapkan (Prodi: ".($prodi?->nama_program_studi ?? 'Informatika').", TTL: {$tempat}, {$tanggal}).");

        // 4. Tarik KHS langsung dari Bridge API (mencoba full transkrip dulu, lalu fallback per semester jika kosong)
        $this->line('Mengambil rekaman nilai transkrip dari Bridge API...');
        $resFull = $service->fetchFullTranskripFromBridge($nim);
        $totalMk = 0;

        if ($resFull['success'] && ! empty($resFull['data'])) {
            $countMk = count($resFull['data']);
            $this->info("✓ Berhasil memuat {$countMk} mata kuliah langsung dari Bridge API!");

            foreach ($resFull['data'] as $item) {
                $mk = SiakadMataKuliah::updateOrCreate(
                    ['kode_mata_kuliah' => $item['kode_mk']],
                    [
                        'nama_mata_kuliah' => $item['nama_mk'],
                        'sks' => (int) $item['sks'],
                        'jenis_mata_kuliah' => 'wajib',
                    ]
                );

                $bobot = (float) $item['bobot'];
                $nilaiAngka = $bobot * 25;

                SiakadNilai::updateOrCreate(
                    [
                        'mahasiswa_id' => $mahasiswa->id,
                        'mata_kuliah_id' => $mk->id,
                    ],
                    [
                        'semester' => (string) ($item['semester'] ?? '1'),
                        'nilai_huruf' => $item['nilai'],
                        'nilai_angka' => $nilaiAngka,
                        'bobot' => $bobot,
                        'sks' => (int) $item['sks'],
                        'sumber_data' => 'siakad',
                        'has_conflict' => false,
                        'imported_at' => now(),
                    ]
                );

                $totalMk++;
            }
        } else {
            // Fallback per-semester jika full transkrip tidak mengembalikan data
            $semesters = ['20221', '20222', '20231', '20232', '20241', '20242', '20251', '20252'];
            foreach ($semesters as $idx => $smtr) {
                $semesterKe = $idx + 1;
                $this->line("Mengambil KHS {$smtr}...");

                $res = $service->fetchKHSFromBridge($nim, $smtr);
                if ($res['success'] && ! empty($res['data'])) {
                    $countSem = count($res['data']);
                    $this->info("  -> Ditemukan {$countSem} mata kuliah");

                    foreach ($res['data'] as $item) {
                        $mk = SiakadMataKuliah::updateOrCreate(
                            ['kode_mata_kuliah' => $item['kode_mk']],
                            [
                                'nama_mata_kuliah' => $item['nama_mk'],
                                'sks' => (int) $item['sks'],
                                'semester' => (string) $semesterKe,
                                'jenis_mata_kuliah' => 'wajib',
                            ]
                        );

                        $bobot = (float) $item['bobot'];
                        $nilaiAngka = $bobot * 25;

                        SiakadNilai::updateOrCreate(
                            [
                                'mahasiswa_id' => $mahasiswa->id,
                                'mata_kuliah_id' => $mk->id,
                            ],
                            [
                                'semester' => (string) $semesterKe,
                                'nilai_huruf' => $item['nilai'],
                                'nilai_angka' => $nilaiAngka,
                                'bobot' => $bobot,
                                'sks' => (int) $item['sks'],
                                'sumber_data' => 'siakad',
                                'has_conflict' => false,
                                'imported_at' => now(),
                            ]
                        );

                        $totalMk++;
                    }
                }
            }
        }

        // 5. Sinkronisasi Transkrip Mahasiswa
        $nilais = SiakadNilai::where('mahasiswa_id', $mahasiswa->id)->get();
        $totalSks = 0;
        $totalMutu = 0.0;

        foreach ($nilais as $n) {
            $sks = (int) $n->sks;
            $bobot = (float) $n->bobot;
            if ($sks > 0) {
                $totalSks += $sks;
                $totalMutu += ($sks * $bobot);
            }
        }

        $ipk = $totalSks > 0 ? round($totalMutu / $totalSks, 2) : 0.00;

        $predikat = 'Memuaskan';
        if ($ipk >= 3.51) {
            $predikat = 'Dengan Pujian (Cum Laude)';
        } elseif ($ipk >= 3.00) {
            $predikat = 'Sangat Memuaskan';
        }

        $transkrip = Transkrip::updateOrCreate(
            ['mahasiswa_id' => $mahasiswa->id],
            [
                'total_sks' => $totalSks,
                'total_mutu' => $totalMutu,
                'ipk' => $ipk,
                'predikat' => $predikat,
                'status_validasi' => 'disahkan',
                'kode_verifikasi' => 'TRX-UINSSC-'.$nim.'-OFFICIAL',
            ]
        );

        AuditLog::record(
            'Sinkronisasi KHS Bridge API',
            'Transkrip',
            $nim,
            "Sinkronisasi otomatis nilai Bridge API mahasiswa {$nama} ({$nim}): {$totalMk} mata kuliah."
        );

        $this->info('=============================================');
        $this->info('SINKRONISASI SELESAI!');
        $this->info("Total Mata Kuliah Terintegrasi: {$totalMk}");
        if ($transkrip) {
            $this->info("Total SKS Ditempuh: {$transkrip->total_sks} SKS");
            $this->info("Indeks Prestasi Kumulatif (IPK): {$transkrip->ipk}");
        }
        $this->info('=============================================');

        return Command::SUCCESS;
    }
}
