<?php

namespace Database\Seeders;

use App\Models\AturanBobotNilai;
use App\Models\Fakultas;
use App\Models\Mahasiswa;
use App\Models\Notifikasi;
use App\Models\ProgramStudi;
use App\Models\SiakadMahasiswa;
use App\Models\SiakadMataKuliah;
use App\Models\SiakadNilai;
use App\Models\SiakadSyncLog;
use App\Models\Transkrip;
use App\Models\User;
use App\Models\Wisudawan;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database with complete realistic academic data.
     */
    public function run(): void
    {
        // 1. Aturan Bobot Nilai Resmi UINSSC
        $bobotRules = [
            ['nilai_huruf' => 'A', 'bobot' => 4.00, 'keterangan' => 'Sangat Baik'],
            ['nilai_huruf' => 'A-', 'bobot' => 3.50, 'keterangan' => 'Hampir Sangat Baik'],
            ['nilai_huruf' => 'B+', 'bobot' => 3.25, 'keterangan' => 'Antara Baik & Sangat Baik'],
            ['nilai_huruf' => 'B', 'bobot' => 3.00, 'keterangan' => 'Baik'],
            ['nilai_huruf' => 'B-', 'bobot' => 2.75, 'keterangan' => 'Cukup Baik'],
            ['nilai_huruf' => 'C+', 'bobot' => 2.25, 'keterangan' => 'Antara Cukup & Baik'],
            ['nilai_huruf' => 'C', 'bobot' => 2.00, 'keterangan' => 'Cukup'],
            ['nilai_huruf' => 'D', 'bobot' => 1.00, 'keterangan' => 'Kurang'],
            ['nilai_huruf' => 'E', 'bobot' => 0.00, 'keterangan' => 'Gagal'],
        ];

        foreach ($bobotRules as $rule) {
            AturanBobotNilai::updateOrCreate(
                ['nilai_huruf' => $rule['nilai_huruf']],
                ['bobot' => $rule['bobot'], 'keterangan' => $rule['keterangan']]
            );
        }

        // 2. Fakultas Resmi UINSSC
        $f1 = Fakultas::updateOrCreate(
            ['nama_fakultas' => 'Fakultas Sains dan Teknologi'],
            ['kode_fakultas' => '1', 'nama_fakultas_singkat' => 'FST']
        );
        $f2 = Fakultas::updateOrCreate(
            ['nama_fakultas' => 'Fakultas Syariah'],
            ['kode_fakultas' => '2', 'nama_fakultas_singkat' => 'FS']
        );
        $f3 = Fakultas::updateOrCreate(
            ['nama_fakultas' => 'Fakultas Tarbiyah dan Ilmu Keguruan'],
            ['kode_fakultas' => '3', 'nama_fakultas_singkat' => 'FTIK']
        );
        $f4 = Fakultas::updateOrCreate(
            ['nama_fakultas' => 'Fakultas Dakwah dan Komunikasi Islam'],
            ['kode_fakultas' => '4', 'nama_fakultas_singkat' => 'FDKI']
        );

        // 3. User Roles: Super Admin & Admin Fakultas
        $superAdmin = User::updateOrCreate(
            ['email' => 'superadmin@uinssc.ac.id'],
            [
                'name' => 'Prof. Dr. H. Aan Jaelani, M.Ag. (Super Admin Pusat)',
                'password' => bcrypt('password'),
                'role' => 'super_admin',
                'status_aktif' => true,
            ]
        );

        $adminFakultas = User::updateOrCreate(
            ['email' => 'admin@uinssc.ac.id'],
            [
                'name' => 'Admin Fakultas Sains & Teknologi',
                'password' => bcrypt('password'),
                'role' => 'admin_fakultas',
                'fakultas_id' => $f1->id,
                'status_aktif' => true,
            ]
        );

        // 4. Program Studi Resmi
        $prodiTI = ProgramStudi::updateOrCreate(
            ['nama_program_studi' => 'Teknik Informatika'],
            [
                'fakultas_id' => $f1->id,
                'kode_prodi' => '311',
                'jenjang' => 'S1',
                'sks_tempuh' => 144,
                'gelar_akademik' => 'S.Kom.',
                'gelar_akademik_full' => 'Sarjana Komputer',
            ]
        );

        $prodiSI = ProgramStudi::updateOrCreate(
            ['nama_program_studi' => 'Sistem Informasi'],
            [
                'fakultas_id' => $f1->id,
                'kode_prodi' => '312',
                'jenjang' => 'S1',
                'sks_tempuh' => 144,
                'gelar_akademik' => 'S.Kom.',
                'gelar_akademik_full' => 'Sarjana Komputer',
            ]
        );

        $prodiHTN = ProgramStudi::updateOrCreate(
            ['nama_program_studi' => 'Hukum Tatanegara Islam'],
            [
                'fakultas_id' => $f2->id,
                'id_prodi_api' => 15,
                'kode_prodi' => '313',
                'jenjang' => 'S1',
                'sks_tempuh' => 144,
                'gelar_akademik' => 'S.H.',
                'gelar_akademik_full' => 'Sarjana Hukum',
            ]
        );

        // 5. Kurikulum Lengkap Semester 1 s/d 8 (Total Tepat 144 SKS)
        $kurikulum = [
            // Semester 1 (19 SKS)
            ['kode' => 'UIN101', 'nama' => 'Pancasila dan Kewarganegaraan', 'sks' => 2, 'semester' => '1'],
            ['kode' => 'UIN102', 'nama' => 'Bahasa Indonesia Akademik', 'sks' => 2, 'semester' => '1'],
            ['kode' => 'UIN103', 'nama' => 'Bahasa Arab Dasar', 'sks' => 2, 'semester' => '1'],
            ['kode' => 'UIN104', 'nama' => 'Studi Keislaman dan Moderasi Beragama', 'sks' => 2, 'semester' => '1'],
            ['kode' => 'TIF101', 'nama' => 'Algoritma dan Pemrograman Dasar', 'sks' => 3, 'semester' => '1'],
            ['kode' => 'TIF102', 'nama' => 'Kalkulus I', 'sks' => 3, 'semester' => '1'],
            ['kode' => 'TIF103', 'nama' => 'Pengantar Teknologi Informasi & Siber', 'sks' => 2, 'semester' => '1'],
            ['kode' => 'TIF104', 'nama' => 'Logika Matematika dan Diskrit', 'sks' => 3, 'semester' => '1'],

            // Semester 2 (19 SKS)
            ['kode' => 'UIN201', 'nama' => 'Bahasa Inggris Akademik & Siber', 'sks' => 2, 'semester' => '2'],
            ['kode' => 'UIN202', 'nama' => 'Studi Al-Qur\'an dan Hadits Tematik', 'sks' => 2, 'semester' => '2'],
            ['kode' => 'TIF201', 'nama' => 'Struktur Data dan Algoritma Lanjut', 'sks' => 3, 'semester' => '2'],
            ['kode' => 'TIF202', 'nama' => 'Sistem Basis Data Relasional', 'sks' => 3, 'semester' => '2'],
            ['kode' => 'TIF203', 'nama' => 'Arsitektur dan Organisasi Komputer', 'sks' => 3, 'semester' => '2'],
            ['kode' => 'TIF204', 'nama' => 'Kalkulus II & Aljabar Linier Terapan', 'sks' => 3, 'semester' => '2'],
            ['kode' => 'TIF205', 'nama' => 'Pemrograman Berorientasi Objek', 'sks' => 3, 'semester' => '2'],

            // Semester 3 (20 SKS)
            ['kode' => 'UIN301', 'nama' => 'Fiqih Muamalah & Etika Siber', 'sks' => 2, 'semester' => '3'],
            ['kode' => 'TIF301', 'nama' => 'Sistem Operasi Enterprise (Linux)', 'sks' => 3, 'semester' => '3'],
            ['kode' => 'TIF302', 'nama' => 'Jaringan Komputer dan Komunikasi Data', 'sks' => 3, 'semester' => '3'],
            ['kode' => 'TIF303', 'nama' => 'Desain dan Pemodelan Basis Data Lanjut', 'sks' => 3, 'semester' => '3'],
            ['kode' => 'TIF304', 'nama' => 'Rekayasa Perangkat Lunak', 'sks' => 3, 'semester' => '3'],
            ['kode' => 'TIF305', 'nama' => 'Pemrograman Web Dasar (Full Stack)', 'sks' => 3, 'semester' => '3'],
            ['kode' => 'TIF306', 'nama' => 'Statistika dan Probabilitas Komputasi', 'sks' => 3, 'semester' => '3'],

            // Semester 4 (20 SKS)
            ['kode' => 'UIN401', 'nama' => 'Metodologi Studi Islam Multidisipliner', 'sks' => 2, 'semester' => '4'],
            ['kode' => 'TIF401', 'nama' => 'Pemrograman Web Lanjut (Framework Modern)', 'sks' => 3, 'semester' => '4'],
            ['kode' => 'TIF402', 'nama' => 'Keamanan Jaringan & Sistem Komputer', 'sks' => 3, 'semester' => '4'],
            ['kode' => 'TIF403', 'nama' => 'Analisis & Perancangan Berorientasi Objek', 'sks' => 3, 'semester' => '4'],
            ['kode' => 'TIF404', 'nama' => 'Interaksi Manusia dan Komputer (UI/UX)', 'sks' => 3, 'semester' => '4'],
            ['kode' => 'TIF405', 'nama' => 'Kecerdasan Buatan (Artificial Intelligence)', 'sks' => 3, 'semester' => '4'],
            ['kode' => 'TIF406', 'nama' => 'Pengolahan Citra Digital', 'sks' => 3, 'semester' => '4'],

            // Semester 5 (18 SKS)
            ['kode' => 'TIF501', 'nama' => 'Pemrograman Aplikasi Mobile (Flutter/Kotlin)', 'sks' => 3, 'semester' => '5'],
            ['kode' => 'TIF502', 'nama' => 'Pembelajaran Mesin (Machine Learning)', 'sks' => 3, 'semester' => '5'],
            ['kode' => 'TIF503', 'nama' => 'Cloud Computing & Arsitektur Mikroservis', 'sks' => 3, 'semester' => '5'],
            ['kode' => 'TIF504', 'nama' => 'Kriptografi & Keamanan Informasi Siber', 'sks' => 3, 'semester' => '5'],
            ['kode' => 'TIF505', 'nama' => 'Penambangan Data (Data Mining)', 'sks' => 3, 'semester' => '5'],
            ['kode' => 'TIF506', 'nama' => 'Manajemen Proyek Teknologi Informasi', 'sks' => 3, 'semester' => '5'],

            // Semester 6 (18 SKS)
            ['kode' => 'UIN601', 'nama' => 'Kewirausahaan Digital & Startup Islami', 'sks' => 3, 'semester' => '6'],
            ['kode' => 'TIF601', 'nama' => 'Forensik Digital & Investigasi Siber', 'sks' => 3, 'semester' => '6'],
            ['kode' => 'TIF602', 'nama' => 'Big Data Analytics & Data Engineering', 'sks' => 3, 'semester' => '6'],
            ['kode' => 'TIF603', 'nama' => 'Internet of Things (IoT) & Smart Campus', 'sks' => 3, 'semester' => '6'],
            ['kode' => 'TIF604', 'nama' => 'Tata Kelola TI & Audit Sistem Informasi', 'sks' => 3, 'semester' => '6'],
            ['kode' => 'TIF605', 'nama' => 'Metodologi Penelitian Bidang Ilmu Komputer', 'sks' => 3, 'semester' => '6'],

            // Semester 7 (18 SKS)
            ['kode' => 'UIN701', 'nama' => 'Kuliah Kerja Mandiri (KKM) Siber Nurjati', 'sks' => 4, 'semester' => '7'],
            ['kode' => 'TIF701', 'nama' => 'Kerja Praktik / Magang Industri Siber', 'sks' => 3, 'semester' => '7'],
            ['kode' => 'TIF702', 'nama' => 'Etika Profesi dan Hukum Siber (Cyber Law)', 'sks' => 3, 'semester' => '7'],
            ['kode' => 'TIF703', 'nama' => 'Seminar Proposal Skripsi', 'sks' => 3, 'semester' => '7'],
            ['kode' => 'TIF704', 'nama' => 'Kapita Selekta Komputasi Siber', 'sks' => 3, 'semester' => '7'],
            ['kode' => 'TIF705', 'nama' => 'Pengujian dan Penjaminan Mutu Perangkat Lunak', 'sks' => 2, 'semester' => '7'],

            // Semester 8 (12 SKS -> Total SKS = 19+19+20+20+18+18+18+12 = 144 SKS)
            ['kode' => 'TIF801', 'nama' => 'Tugas Akhir / Skripsi Komputasi Siber', 'sks' => 6, 'semester' => '8'],
            ['kode' => 'TIF802', 'nama' => 'Komprehensif Keislaman dan Keilmuan', 'sks' => 3, 'semester' => '8'],
            ['kode' => 'TIF803', 'nama' => 'Publikasi Ilmiah Jurnal Terindeks', 'sks' => 3, 'semester' => '8'],
        ];

        $mataKuliahModels = [];
        foreach ($kurikulum as $item) {
            $mk = SiakadMataKuliah::updateOrCreate(
                ['kode_mata_kuliah' => $item['kode']],
                [
                    'nama_mata_kuliah' => $item['nama'],
                    'sks' => $item['sks'],
                    'semester' => $item['semester'],
                ]
            );
            $mataKuliahModels[$item['kode']] = $mk;
        }

        // 6. Data Mahasiswa dengan Variasi Nyata (Ada Banyak SKS 144 Semester 1-8 s/d Sedikit Semester 1-2)
        $studentsData = [
            [
                'nim' => '220001',
                'nama' => 'Ahmad Dahlan',
                'email' => 'mhs1@uinssc.ac.id',
                'tempat_lahir' => 'Cirebon',
                'tanggal_lahir' => '2002-05-14',
                'angkatan' => '2022',
                'status' => 'Lulus',
                'semester_max' => 8, // Mengambil SEMUA semester 1 s/d 8 (144 SKS, BANYAK)
                'is_wisudawan' => true,
                'judul_skripsi' => 'Rancang Bangun Sistem Verifikasi Transkrip Digital Menggunakan Kriptografi Kunci Publik pada UINSSC',
                'predikat' => 'Dengan Pujian (Cumlaude)',
                'status_transkrip' => 'disahkan',
                'tanggal_wisuda' => '2026-10-28',
            ],
            [
                'nim' => '220002',
                'nama' => 'Siti Nur Aisyah',
                'email' => 'mhs2@uinssc.ac.id',
                'tempat_lahir' => 'Kuningan',
                'tanggal_lahir' => '2002-09-20',
                'angkatan' => '2022',
                'status' => 'Lulus',
                'semester_max' => 8, // Mengambil SEMUA semester 1 s/d 8 (144 SKS, BANYAK)
                'is_wisudawan' => true,
                'judul_skripsi' => 'Implementasi Algoritma Machine Learning untuk Analisis Sentimen Evaluasi Pembelajaran Digital di UINSSC',
                'predikat' => 'Sangat Memuaskan',
                'status_transkrip' => 'menunggu_pengesahan',
                'tanggal_wisuda' => '2026-10-28',
            ],
            [
                'nim' => '230003',
                'nama' => 'Rizky Pratama',
                'email' => 'mhs3@uinssc.ac.id',
                'tempat_lahir' => 'Majalengka',
                'tanggal_lahir' => '2004-01-18',
                'angkatan' => '2023',
                'status' => 'Aktif',
                'semester_max' => 6, // Mengambil Semester 1 s/d 6 (~114 SKS, SEDANG TINGGI)
                'is_wisudawan' => false,
                'status_transkrip' => 'aktif',
            ],
            [
                'nim' => '240004',
                'nama' => 'Fatimah Azzahra',
                'email' => 'mhs4@uinssc.ac.id',
                'tempat_lahir' => 'Indramayu',
                'tanggal_lahir' => '2005-03-10',
                'angkatan' => '2024',
                'status' => 'Aktif',
                'semester_max' => 4, // Mengambil Semester 1 s/d 4 (78 SKS, SEDANG)
                'is_wisudawan' => false,
                'status_transkrip' => 'aktif',
            ],
            [
                'nim' => '250005',
                'nama' => 'Muhammad Al-Fatih',
                'email' => 'mhs5@uinssc.ac.id',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '2006-07-05',
                'angkatan' => '2025',
                'status' => 'Aktif',
                'semester_max' => 2, // Mengambil Semester 1 & 2 (38 SKS, SEDIKIT)
                'is_wisudawan' => false,
                'status_transkrip' => 'aktif',
            ],
            [
                'nim' => '260006',
                'nama' => 'Zaskia Amelia Putri',
                'email' => 'mhs6@uinssc.ac.id',
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '2007-11-02',
                'angkatan' => '2026',
                'status' => 'Aktif',
                'semester_max' => 1, // Mengambil Semester 1 (19 SKS, PALING SEDIKIT)
                'is_wisudawan' => false,
                'status_transkrip' => 'aktif',
            ],
        ];

        // Grade choices distribution (high grades, realistic)
        $gradesPool = [
            ['huruf' => 'A', 'bobot' => 4.00],
            ['huruf' => 'A', 'bobot' => 4.00],
            ['huruf' => 'A-', 'bobot' => 3.50],
            ['huruf' => 'B+', 'bobot' => 3.25],
            ['huruf' => 'B', 'bobot' => 3.00],
            ['huruf' => 'A', 'bobot' => 4.00],
            ['huruf' => 'A-', 'bobot' => 3.50],
        ];

        foreach ($studentsData as $idx => $sData) {
            $userMhs = User::updateOrCreate(
                ['email' => $sData['email']],
                [
                    'name' => $sData['nama'],
                    'password' => bcrypt('password'),
                    'role' => 'mahasiswa',
                    'status_aktif' => true,
                ]
            );

            $mhs = Mahasiswa::updateOrCreate(
                ['nim' => $sData['nim']],
                [
                    'user_id' => $userMhs->id,
                    'nama' => $sData['nama'],
                    'tempat_lahir' => $sData['tempat_lahir'],
                    'tanggal_lahir' => Carbon::parse($sData['tanggal_lahir']),
                    'fakultas_id' => $f1->id,
                    'program_studi_id' => $prodiTI->id,
                    'jenjang' => 'S1',
                    'angkatan' => $sData['angkatan'],
                    'status' => $sData['status'],
                ]
            );

            SiakadMahasiswa::updateOrCreate(
                ['nim' => $mhs->nim],
                [
                    'mahasiswa_id' => $mhs->id,
                    'nama' => $mhs->nama,
                    'fakultas' => $f1->nama_fakultas,
                    'program_studi' => $prodiTI->nama_program_studi,
                    'angkatan' => $mhs->angkatan,
                ]
            );

            // Populate courses based on semester_max
            $totalSks = 0;
            $totalMutu = 0.0;
            $gradeIndex = $idx;

            foreach ($kurikulum as $mkItem) {
                if ((int) $mkItem['semester'] <= $sData['semester_max']) {
                    $mkModel = $mataKuliahModels[$mkItem['kode']];
                    $gradePick = $gradesPool[($gradeIndex + (int) $mkItem['semester'] + $mkModel->id) % count($gradesPool)];

                    // Ensure high marks for final project / skripsi
                    if ($mkItem['kode'] === 'TIF801') {
                        $gradePick = ['huruf' => 'A', 'bobot' => 4.00];
                    }

                    SiakadNilai::updateOrCreate(
                        ['mahasiswa_id' => $mhs->id, 'mata_kuliah_id' => $mkModel->id],
                        [
                            'semester' => $mkItem['semester'],
                            'nilai_huruf' => $gradePick['huruf'],
                            'nilai_angka' => $gradePick['bobot'],
                            'bobot' => $gradePick['bobot'],
                            'sks' => $mkItem['sks'],
                            'sumber_data' => 'SIAKAD',
                            'has_conflict' => false,
                        ]
                    );

                    $totalSks += $mkItem['sks'];
                    $totalMutu += ($mkItem['sks'] * $gradePick['bobot']);
                    $gradeIndex++;
                }
            }

            $ipk = $totalSks > 0 ? round($totalMutu / $totalSks, 2) : 0.00;

            // Handle Wisudawan record
            $wisudawanRecord = null;
            if ($sData['is_wisudawan']) {
                $wisudawanRecord = Wisudawan::updateOrCreate(
                    ['nim' => $mhs->nim],
                    [
                        'mahasiswa_id' => $mhs->id,
                        'periode_wisuda' => 'Wisuda Ke-28 Tahun 2026',
                        'fakultas' => $f1->nama_fakultas,
                        'program_studi' => $prodiTI->nama_program_studi,
                        'judul_skripsi' => $sData['judul_skripsi'],
                        'predikat_kelulusan' => $sData['predikat'],
                        'status_validasi' => $sData['status_transkrip'],
                        'tanggal_wisuda' => Carbon::parse($sData['tanggal_wisuda']),
                    ]
                );
            }

            // Create / update Transkrip record
            $kodeVerifikasi = 'UINSSC-TRX-'.$sData['angkatan'].'-'.$sData['nim'];
            $predikat = $sData['is_wisudawan'] ? $sData['predikat'] : null;

            $transkrip = Transkrip::updateOrCreate(
                ['mahasiswa_id' => $mhs->id],
                [
                    'kode_verifikasi' => $kodeVerifikasi,
                    'total_sks' => $totalSks,
                    'total_mutu' => $totalMutu,
                    'ipk' => $ipk,
                    'predikat' => $predikat,
                    'status_validasi' => $sData['status_transkrip'] === 'disahkan' ? 'disahkan' : ($sData['status_transkrip'] === 'menunggu_pengesahan' ? 'menunggu_pengesahan' : 'Valid'),
                    'verified_by_jurusan_id' => $adminFakultas->id,
                    'verified_by_jurusan_at' => now()->subDays(2),
                    'ratified_by_admin_id' => $sData['status_transkrip'] === 'disahkan' ? $superAdmin->id : null,
                    'ratified_by_admin_at' => $sData['status_transkrip'] === 'disahkan' ? now()->subDay() : null,
                ]
            );

            // Record initial history
            $transkrip->histories()->firstOrCreate(
                [
                    'transkrip_id' => $transkrip->id,
                    'status_terbaru' => $transkrip->status_validasi,
                ],
                [
                    'user_id' => $adminFakultas->id,
                    'status_sebelumnya' => 'menunggu_verifikasi',
                    'catatan' => 'Verifikasi kelengkapan berkas akademik dari SIAKAD UINSSC.',
                ]
            );

            // Create welcome notification
            Notifikasi::firstOrCreate(
                [
                    'user_id' => $userMhs->id,
                    'judul' => 'Transkrip Akademik Tersedia',
                ],
                [
                    'pesan' => 'Transkrip nilai akademik Anda telah disinkronkan langsung dari SIAKAD resmi UINSSC.',
                    'tipe' => 'info',
                    'is_read' => false,
                ]
            );
        }

        // 7. Initial SiakadSyncLog
        SiakadSyncLog::firstOrCreate(
            ['endpoint' => 'https://bridge.uinssc.ac.id/api/program_studi?fakultas=2'],
            [
                'status' => 'sukses',
                'status_code' => 200,
                'total_berhasil' => count($kurikulum),
                'total_gagal' => 0,
                'total_diperbarui' => count($studentsData),
                'total_prodi' => 3,
                'total_mahasiswa' => count($studentsData),
                'total_nilai' => SiakadNilai::count(),
                'konflik_count' => 0,
                'keterangan' => 'Sinkronisasi awal kurikulum semester 1-8 dan data akademik mahasiswa UINSSC.',
            ]
        );

        // 8. Seeding Mahasiswa Resmi UIN SSC dari LP2M KKN & Bridge API
        $this->call(RealMahasiswaUinsscSeeder::class);
    }
}
