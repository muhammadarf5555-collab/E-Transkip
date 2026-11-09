<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\Fakultas;
use App\Models\Mahasiswa;
use App\Models\ProgramStudi;
use App\Models\SiakadMahasiswa;
use App\Models\User;
use App\Models\Wisudawan;
use Illuminate\Database\Seeder;

class MigrateInformatikaToFitkSeeder extends Seeder
{
    public function run(): void
    {
        $fitk = Fakultas::where('id', 1)->first();
        if (! $fitk) {
            $this->command->error('Fakultas FITK (ID 1) tidak ditemukan.');

            return;
        }

        // 1. Pindahkan Program Studi Teknik Informatika ke FITK
        $prodiInfor = ProgramStudi::where('id', 1)->first();
        if ($prodiInfor) {
            $prodiInfor->update(['fakultas_id' => 1]);
            $this->command->info('Program Studi Teknik Informatika berhasil dipindahkan ke FITK (ID 1).');
        }

        // 2. Pindahkan seluruh Mahasiswa Teknik Informatika dari Saintek ke FITK
        $mhsCount = Mahasiswa::where(function ($q) {
            $q->where('program_studi_id', 1)
                ->orWhere('fakultas_id', 13)
                ->orWhere('nim', 'like', '238801%');
        })->update([
            'fakultas_id' => 1,
            'program_studi_id' => 1,
        ]);
        $this->command->info("{$mhsCount} Mahasiswa Informatika berhasil dipindahkan ke FITK.");

        // 3. Perbarui user accounts mahasiswa tersebut
        $inforMhsUserIds = Mahasiswa::where('program_studi_id', 1)->whereNotNull('user_id')->pluck('user_id');
        User::whereIn('id', $inforMhsUserIds)->update(['fakultas_id' => 1]);

        // 4. Perbarui akun rizky.S.KOM agar menjadi Admin FITK
        User::where('email', 'rizky@gmail.com')->update(['fakultas_id' => 1]);
        $this->command->info('Akun rizky.S.KOM diperbarui menjadi Admin FITK.');

        // 5. Perbarui siakad_mahasiswas dan wisudawans
        SiakadMahasiswa::where('fakultas', 'Fakultas Sains dan Teknologi')->update([
            'fakultas' => 'Fakultas Ilmu Tarbiyah dan Keguruan',
        ]);
        Wisudawan::where('fakultas', 'Fakultas Sains dan Teknologi')->update([
            'fakultas' => 'Fakultas Ilmu Tarbiyah dan Keguruan',
        ]);

        AuditLog::record(
            'Migrasi Informatika ke FITK',
            'Mahasiswa',
            '1',
            "Memindahkan Prodi Teknik Informatika dan {$mhsCount} mahasiswa Informatika ke Fakultas Ilmu Tarbiyah dan Keguruan (FITK) agar data real."
        );

        $this->command->info('Migrasi selesai dengan sukses!');
    }
}
