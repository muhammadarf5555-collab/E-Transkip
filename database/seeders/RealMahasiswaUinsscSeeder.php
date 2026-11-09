<?php

namespace Database\Seeders;

use App\Models\Fakultas;
use App\Models\Mahasiswa;
use App\Models\ProgramStudi;
use App\Models\User;
use App\Services\SiakadApiService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class RealMahasiswaUinsscSeeder extends Seeder
{
    public function run(): void
    {
        $service = app(SiakadApiService::class);

        $fakultasSaintek = Fakultas::find(13)
            ?? Fakultas::where('nama_fakultas', 'like', '%Sains dan Teknologi%')->first();
        $prodiInfor = ProgramStudi::where('kode_prodi', '801')
            ->orWhere('nama_program_studi', 'like', '%Informatika%')
            ->first();

        $students = [
            ['nim' => '2388010001', 'nama' => 'SITI MALIKHA', 'jk' => 'P', 'no_hp' => '085795040913', 'is_infor' => true],
            ['nim' => '2388010002', 'nama' => 'ANANDA SATHRIA MAULANA AMRI', 'jk' => 'L', 'no_hp' => '085157588037', 'is_infor' => true],
            ['nim' => '2388010003', 'nama' => 'MUHAMAD ARYA PANGESTU', 'jk' => 'L', 'no_hp' => '085157223590', 'is_infor' => true],
            ['nim' => '2388010004', 'nama' => 'ABDUL KHARIS', 'jk' => 'L', 'no_hp' => '+628562721843', 'is_infor' => true],
            ['nim' => '2388010005', 'nama' => 'NAJWA RAFA FAUZIAH', 'jk' => 'P', 'no_hp' => '08131662447', 'is_infor' => true],
            ['nim' => '2388010006', 'nama' => 'CITA AMELIA', 'jk' => 'P', 'no_hp' => '083142885979', 'is_infor' => true],
            ['nim' => '2388010007', 'nama' => 'FARIZ NOOR DJATI', 'jk' => 'L', 'no_hp' => '08889903069', 'is_infor' => true],
            ['nim' => '2388010008', 'nama' => 'SYAHRIL NADZIF RAMADHAN', 'jk' => 'L', 'no_hp' => '085871265772', 'is_infor' => true],
            ['nim' => '2388010009', 'nama' => 'REVI INJANI', 'jk' => 'P', 'no_hp' => '082295350505', 'is_infor' => true],
            ['nim' => '2388010010', 'nama' => 'YUSUF ALIM ROMADON', 'jk' => 'L', 'no_hp' => '081224159432', 'is_infor' => true],
            ['nim' => '2388010011', 'nama' => 'NATASYA MORA ENDRAYANI', 'jk' => 'P', 'no_hp' => '085721516738', 'is_infor' => true],
            ['nim' => '2388010012', 'nama' => 'RATU BILLAH LINGGA SUCI', 'jk' => 'P', 'no_hp' => '0882000260420', 'is_infor' => true],
            ['nim' => '2388010014', 'nama' => 'MUHAMAD MAHBUB', 'jk' => 'L', 'no_hp' => '0895336368237', 'is_infor' => true],
            ['nim' => '2388010015', 'nama' => 'MOHAMAD KADIK', 'jk' => 'L', 'no_hp' => '085797878190', 'is_infor' => true],
            ['nim' => '2388010016', 'nama' => 'FAQIH FIRANSYAH', 'jk' => 'L', 'no_hp' => '087819439031', 'is_infor' => true],
            ['nim' => '2388010017', 'nama' => 'MUHAMMAD ARIF RIZKY', 'jk' => 'L', 'no_hp' => '085172033209', 'is_infor' => true],
            ['nim' => '2388010018', 'nama' => 'IRWAN MUTHOLIB', 'jk' => 'L', 'no_hp' => '08978472393', 'is_infor' => true],
            ['nim' => '2388010019', 'nama' => 'ANDIKA MUHAMMAD SURYA', 'jk' => 'L', 'no_hp' => '082115913604', 'is_infor' => true],
            ['nim' => '2388010020', 'nama' => 'HARITS FAQIHUDDIN', 'jk' => 'L', 'no_hp' => '083899298411', 'is_infor' => true],
            ['nim' => '2388010021', 'nama' => 'KHAERUL ANAM', 'jk' => 'L', 'no_hp' => '083140443663', 'is_infor' => true],
            ['nim' => '2388010022', 'nama' => 'CATUR PRASETIYO GAMA', 'jk' => 'L', 'no_hp' => '082130483729', 'is_infor' => true],
            ['nim' => '2388010023', 'nama' => 'DICKY DERMAWAN', 'jk' => 'L', 'no_hp' => '082119306131', 'is_infor' => true],
            ['nim' => '2388010024', 'nama' => 'MUHAMMAD FATIH KHOIRI ASIM', 'jk' => 'L', 'no_hp' => '085161500214', 'is_infor' => true],
            ['nim' => '2388010025', 'nama' => 'KHOLIS IRAWATI ISKANDAR', 'jk' => 'P', 'no_hp' => '083159101708', 'is_infor' => true],
            ['nim' => '2388010026', 'nama' => 'MAEMUNAH HALIMATUS SADIYAH', 'jk' => 'P', 'no_hp' => '081314879590', 'is_infor' => true],
            ['nim' => '2388010027', 'nama' => 'DIMAS ADRIANSAH', 'jk' => 'L', 'no_hp' => '088972042818', 'is_infor' => true],
            ['nim' => '2388010028', 'nama' => 'MUHAMMAD RAFLY SAPUTRA', 'jk' => 'L', 'no_hp' => '082211141520', 'is_infor' => true],
            ['nim' => '2388010029', 'nama' => 'ANS SHIFATAMA', 'jk' => 'P', 'no_hp' => '083195832326', 'is_infor' => true],
            ['nim' => '2388010030', 'nama' => 'WILDAN MUHAMMAD FAHMI', 'jk' => 'L', 'no_hp' => '082119484482', 'is_infor' => true],
            ['nim' => '2388010031', 'nama' => 'EKA TIARA', 'jk' => 'P', 'no_hp' => '085878192892', 'is_infor' => true],
            ['nim' => '2388010032', 'nama' => 'AFIFAH ZAYYIN AMATILLAH', 'jk' => 'P', 'no_hp' => '083823124086', 'is_infor' => true],
            ['nim' => '2388010033', 'nama' => 'DIKA HASAN NUGROHO', 'jk' => 'L', 'no_hp' => '082316849866', 'is_infor' => true],
            ['nim' => '2388010036', 'nama' => 'KHOSYATILLAH', 'jk' => 'P', 'no_hp' => '088802079899', 'is_infor' => true],
            ['nim' => '2388010037', 'nama' => 'ABD KHOLIK', 'jk' => 'L', 'no_hp' => '085933675790', 'is_infor' => true],
            ['nim' => '2388010038', 'nama' => 'ZAIDAN ALIF FIRDAUS', 'jk' => 'L', 'no_hp' => '081222943722', 'is_infor' => true],
            ['nim' => '2388010039', 'nama' => 'AHMAD REFIANSYAH NAZMI MUBAROK', 'jk' => 'L', 'no_hp' => '085524971660', 'is_infor' => true],
            ['nim' => '2388010040', 'nama' => 'MORIN PITA LAURA', 'jk' => 'P', 'no_hp' => '081313545340', 'is_infor' => true],
            ['nim' => '2388010042', 'nama' => 'MUHAMMAD CEVI MAULANA RAMADHAN', 'jk' => 'L', 'no_hp' => '085794280723', 'is_infor' => true],
            ['nim' => '2388010043', 'nama' => 'MOCH SUBCHI RAMADHANI', 'jk' => 'L', 'no_hp' => '081572073171', 'is_infor' => true],
            ['nim' => '2388010044', 'nama' => 'KRISNA ADILLA FERNANDA', 'jk' => 'L', 'no_hp' => '083116854467', 'is_infor' => true],
            ['nim' => '2388010045', 'nama' => 'DONI SETIAWAN', 'jk' => 'L', 'no_hp' => '089621178011', 'is_infor' => true],
            ['nim' => '2388010046', 'nama' => 'ALIYYAH AZZAHRA TAQI KUSUMA', 'jk' => 'P', 'no_hp' => '089531546071', 'is_infor' => true],
            ['nim' => '2388010047', 'nama' => 'MOHAMMAD RAIHAN AKBAR', 'jk' => 'L', 'no_hp' => '089505404960', 'is_infor' => true],
            ['nim' => '2388010048', 'nama' => 'MUHAMAD DEVANI ALGHIFARI', 'jk' => 'L', 'no_hp' => '085155336242', 'is_infor' => true],
            ['nim' => '2388010049', 'nama' => 'MUHAMMAD SYIFAULQULUB HAKIM', 'jk' => 'L', 'no_hp' => '081284117673', 'is_infor' => true],
            ['nim' => '2388010050', 'nama' => 'MERIN KHARISTA PUTRI', 'jk' => 'P', 'no_hp' => '085703116149', 'is_infor' => true],
            ['nim' => '2388010051', 'nama' => 'MUHAMMAD JIBRIL JUDEX FACTI ALIYUDIN', 'jk' => 'L', 'no_hp' => '081218109882', 'is_infor' => true],
            ['nim' => '2388010052', 'nama' => 'MUHAMMAD INGGAR AGUS SHOLIHIN', 'jk' => 'L', 'no_hp' => '083821463041', 'is_infor' => true],
            ['nim' => '2383140005', 'nama' => 'SYAFIRA SALMA SUBEKTI', 'jk' => 'P', 'no_hp' => '081234567891', 'is_infor' => false, 'fakultas_kode' => 2, 'prodi_kode' => '314'],
            ['nim' => '2382120044', 'nama' => 'MOHAMMAD ILHAM ABDILLAH', 'jk' => 'L', 'no_hp' => '081234567892', 'is_infor' => false, 'fakultas_kode' => 7, 'prodi_kode' => '212'],
            ['nim' => '2384110096', 'nama' => 'NADIA SALMA', 'jk' => 'P', 'no_hp' => '081234567893', 'is_infor' => false, 'fakultas_kode' => 8, 'prodi_kode' => '411'],
            ['nim' => '2385110014', 'nama' => 'SYELLY QOTHROTUNNADA', 'jk' => 'P', 'no_hp' => '081234567894', 'is_infor' => false, 'fakultas_kode' => 3, 'prodi_kode' => '511'],
            ['nim' => '2381100096', 'nama' => 'AINUL HIYAH', 'jk' => 'P', 'no_hp' => '081234567895', 'is_infor' => false, 'fakultas_kode' => 1, 'prodi_kode' => '110'],
            ['nim' => '2281050039', 'nama' => 'MAHASISWA SENIOR FITK', 'jk' => 'L', 'no_hp' => '081234567896', 'is_infor' => false, 'fakultas_kode' => 1, 'prodi_kode' => '105'],
        ];

        $this->command->info('Memulai seeding '.count($students).' mahasiswa resmi UIN SSC...');

        foreach ($students as $data) {
            $nim = $data['nim'];
            $nama = $data['nama'];
            $jk = $data['jk'];
            $noHp = $data['no_hp'];
            $email = "{$nim}@mahasiswa.uinssc.ac.id";

            if (! empty($data['is_infor'])) {
                $fakultasId = $fakultasSaintek ? $fakultasSaintek->id : 13;
                $prodiId = $prodiInfor ? $prodiInfor->id : 1;
            } else {
                $fak = Fakultas::where('kode_fakultas', $data['fakultas_kode'])->first();
                $prodi = ProgramStudi::where('kode_prodi', $data['prodi_kode'])->first();
                $fakultasId = $fak ? $fak->id : 1;
                $prodiId = $prodi ? $prodi->id : 1;
            }

            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $nama,
                    'password' => Hash::make('password'),
                    'role' => 'mahasiswa',
                    'fakultas_id' => $fakultasId,
                    'status_aktif' => true,
                ]
            );

            $tempatLahir = $nim === '2388010017' ? 'Subang' : ($nim === '2388010002' ? 'Cirebon' : 'Cirebon');
            $tanggalLahir = $nim === '2388010017' ? '2005-02-25' : ($nim === '2388010002' ? '2004-06-15' : '2004-01-01');

            $mahasiswa = Mahasiswa::updateOrCreate(
                ['nim' => $nim],
                [
                    'user_id' => $user->id,
                    'nama' => $nama,
                    'tempat_lahir' => $tempatLahir,
                    'tanggal_lahir' => $tanggalLahir,
                    'jenis_kelamin' => $jk,
                    'email' => $email,
                    'no_hp' => $noHp,
                    'fakultas_id' => $fakultasId,
                    'program_studi_id' => $prodiId,
                    'jenjang' => 'S1',
                    'angkatan' => str_starts_with($nim, '22') ? '2022' : '2023',
                    'status' => 'Aktif',
                ]
            );

            // Sync full transcript for the primary students directly
            if (in_array($nim, ['2388010017', '2388010002', '2388010003', '2388010004', '2388010025', '2388010049'], true)) {
                try {
                    $service->syncStudentTranscript($mahasiswa);
                    $this->command->info("  [✓] {$nim} - {$nama} terintegrasi nilai Bridge API.");
                } catch (\Throwable $e) {
                    Log::warning("Gagal auto-sync nilai {$nim}: ".$e->getMessage());
                }
            } else {
                $this->command->line("  [•] {$nim} - {$nama} tersimpan.");
            }
        }

        $this->command->info('Seeding mahasiswa resmi UIN SSC selesai!');
    }
}
