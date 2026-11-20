<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('fakultas', function (Blueprint $table) {
            if (! Schema::hasColumn('fakultas', 'kode_romawi')) {
                $table->string('kode_romawi', 30)->nullable()->after('nama_fakultas_singkat');
            }
            if (! Schema::hasColumn('fakultas', 'nama_dekan')) {
                $table->string('nama_dekan')->nullable()->after('kode_romawi');
            }
            if (! Schema::hasColumn('fakultas', 'nip_dekan')) {
                $table->string('nip_dekan', 50)->nullable()->after('nama_dekan');
            }
            if (! Schema::hasColumn('fakultas', 'jabatan_dekan')) {
                $table->string('jabatan_dekan', 100)->default('Dekan')->nullable()->after('nip_dekan');
            }
            if (! Schema::hasColumn('fakultas', 'no_sk_banpt')) {
                $table->string('no_sk_banpt')->nullable()->after('jabatan_dekan');
            }
        });

        Schema::table('program_studis', function (Blueprint $table) {
            if (! Schema::hasColumn('program_studis', 'no_sk_akreditasi')) {
                $table->string('no_sk_akreditasi')->nullable()->after('gelar_akademik_full');
            }
            if (! Schema::hasColumn('program_studis', 'peringkat_akreditasi')) {
                $table->string('peringkat_akreditasi', 50)->nullable()->after('no_sk_akreditasi');
            }
        });

        Schema::table('transkrips', function (Blueprint $table) {
            if (! Schema::hasColumn('transkrips', 'nomor_transkrip')) {
                $table->string('nomor_transkrip')->nullable()->after('kode_verifikasi');
            }
            if (! Schema::hasColumn('transkrips', 'no_sk_banpt_override')) {
                $table->string('no_sk_banpt_override')->nullable()->after('nomor_transkrip');
            }
            if (! Schema::hasColumn('transkrips', 'nama_dekan_override')) {
                $table->string('nama_dekan_override')->nullable()->after('no_sk_banpt_override');
            }
            if (! Schema::hasColumn('transkrips', 'nip_dekan_override')) {
                $table->string('nip_dekan_override', 50)->nullable()->after('nama_dekan_override');
            }
            if (! Schema::hasColumn('transkrips', 'jabatan_dekan_override')) {
                $table->string('jabatan_dekan_override', 100)->nullable()->after('nip_dekan_override');
            }
        });

        // Seed initial dynamic officials for existing UINSSC faculties
        $defaults = [
            'FITK' => [
                'kode_romawi' => 'F.I',
                'nama_dekan' => 'Dr. H. Saifuddin, M.Ag.',
                'nip_dekan' => '197203151998031002',
                'jabatan_dekan' => 'Dekan',
                'no_sk_banpt' => '3184/SK/BAN-PT/Ak.Ppj/S/VIII/2024',
            ],
            'FS' => [
                'kode_romawi' => 'F.III',
                'nama_dekan' => 'Dr. H. Edy Setyawan, Lc., M.A.',
                'nip_dekan' => '197605122003121003',
                'jabatan_dekan' => 'Dekan',
                'no_sk_banpt' => '2845/SK/BAN-PT/Akred/S/VII/2024',
            ],
            'FUA' => [
                'kode_romawi' => 'F.IV',
                'nama_dekan' => 'Dr. H. Anwar Sanusi, M.Ag.',
                'nip_dekan' => '197108041999031001',
                'jabatan_dekan' => 'Dekan',
                'no_sk_banpt' => '2510/SK/BAN-PT/Akred/S/VI/2024',
            ],
            'PASCA' => [
                'kode_romawi' => 'PPs',
                'nama_dekan' => 'Prof. Dr. H. Sugianto, S.H., M.H.',
                'nip_dekan' => '196804101995031004',
                'jabatan_dekan' => 'Direktur',
                'no_sk_banpt' => '2690/SK/BAN-PT/Akred/M/VI/2024',
            ],
            'FEBI' => [
                'kode_romawi' => 'F.II',
                'nama_dekan' => 'Dr. H. Didi Sukardi, M.H.',
                'nip_dekan' => '196912262009121001',
                'jabatan_dekan' => 'Dekan',
                'no_sk_banpt' => '2269/DE/A.5/AR.10/V/2024',
            ],
            'FDKI' => [
                'kode_romawi' => 'F.V',
                'nama_dekan' => 'Dr. Siti Fatimah, M.Hum.',
                'nip_dekan' => '197411202000032001',
                'jabatan_dekan' => 'Dekan',
                'no_sk_banpt' => '2690/SK/BAN-PT/Akred/S/VI/2024',
            ],
            'FST' => [
                'kode_romawi' => 'F.VI',
                'nama_dekan' => 'Dr. H. Saifuddin, M.Ag.',
                'nip_dekan' => '197203151998031002',
                'jabatan_dekan' => 'Dekan',
                'no_sk_banpt' => '0412/SK/LAM-INFOKOM/Ak/S/VIII/2024',
            ],
        ];

        foreach ($defaults as $singkat => $meta) {
            DB::table('fakultas')
                ->where('nama_fakultas_singkat', $singkat)
                ->whereNull('nama_dekan')
                ->update($meta);
        }

        // Seed Informatika accreditation
        DB::table('program_studis')
            ->where(function ($q) {
                $q->where('id', 1)->orWhere('nama_program_studi', 'like', '%Informatika%');
            })
            ->whereNull('no_sk_akreditasi')
            ->update([
                'no_sk_akreditasi' => '0412/SK/LAM-INFOKOM/Ak/S/VIII/2024',
                'peringkat_akreditasi' => 'Baik Sekali',
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transkrips', function (Blueprint $table) {
            $table->dropColumn([
                'nomor_transkrip',
                'no_sk_banpt_override',
                'nama_dekan_override',
                'nip_dekan_override',
                'jabatan_dekan_override',
            ]);
        });

        Schema::table('program_studis', function (Blueprint $table) {
            $table->dropColumn([
                'no_sk_akreditasi',
                'peringkat_akreditasi',
            ]);
        });

        Schema::table('fakultas', function (Blueprint $table) {
            $table->dropColumn([
                'kode_romawi',
                'nama_dekan',
                'nip_dekan',
                'jabatan_dekan',
                'no_sk_banpt',
            ]);
        });
    }
};
