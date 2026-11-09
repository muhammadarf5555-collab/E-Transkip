<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('fakultas', function (Blueprint $table) {
            if (! Schema::hasColumn('fakultas', 'kode_fakultas')) {
                $table->string('kode_fakultas')->nullable()->after('id');
            }
            if (! Schema::hasColumn('fakultas', 'nama_fakultas_singkat')) {
                $table->string('nama_fakultas_singkat')->nullable()->after('nama_fakultas');
            }
        });

        Schema::table('program_studis', function (Blueprint $table) {
            if (! Schema::hasColumn('program_studis', 'id_prodi_api')) {
                $table->unsignedBigInteger('id_prodi_api')->nullable()->after('fakultas_id');
            }
            if (! Schema::hasColumn('program_studis', 'kode_prodi')) {
                $table->string('kode_prodi')->nullable()->after('id_prodi_api');
            }
            if (! Schema::hasColumn('program_studis', 'jenjang')) {
                $table->string('jenjang', 20)->nullable()->after('nama_program_studi');
            }
            if (! Schema::hasColumn('program_studis', 'sks_tempuh')) {
                $table->unsignedSmallInteger('sks_tempuh')->nullable()->after('jenjang');
            }
            if (! Schema::hasColumn('program_studis', 'gelar_akademik')) {
                $table->string('gelar_akademik', 50)->nullable()->after('sks_tempuh');
            }
            if (! Schema::hasColumn('program_studis', 'gelar_akademik_full')) {
                $table->string('gelar_akademik_full', 100)->nullable()->after('gelar_akademik');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('program_studis', function (Blueprint $table) {
            $table->dropColumn([
                'id_prodi_api',
                'kode_prodi',
                'jenjang',
                'sks_tempuh',
                'gelar_akademik',
                'gelar_akademik_full',
            ]);
        });

        Schema::table('fakultas', function (Blueprint $table) {
            $table->dropColumn([
                'kode_fakultas',
                'nama_fakultas_singkat',
            ]);
        });
    }
};
