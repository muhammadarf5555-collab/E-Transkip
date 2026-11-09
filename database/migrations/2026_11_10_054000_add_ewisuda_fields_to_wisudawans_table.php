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
        Schema::table('wisudawans', function (Blueprint $table) {
            if (! Schema::hasColumn('wisudawans', 'nama')) {
                $table->string('nama')->nullable()->after('nim');
            }
            if (! Schema::hasColumn('wisudawans', 'no_sk')) {
                $table->string('no_sk')->nullable()->after('program_studi');
            }
            if (! Schema::hasColumn('wisudawans', 'tanggal_sk')) {
                $table->date('tanggal_sk')->nullable()->after('no_sk');
            }
            if (! Schema::hasColumn('wisudawans', 'no_ijazah')) {
                $table->string('no_ijazah')->nullable()->after('tanggal_sk');
            }
            if (! Schema::hasColumn('wisudawans', 'tanggal_ijazah')) {
                $table->date('tanggal_ijazah')->nullable()->after('no_ijazah');
            }
            if (! Schema::hasColumn('wisudawans', 'ipk')) {
                $table->decimal('ipk', 4, 2)->nullable()->after('tanggal_ijazah');
            }
            if (! Schema::hasColumn('wisudawans', 'total_sks')) {
                $table->unsignedSmallInteger('total_sks')->nullable()->after('ipk');
            }
            if (! Schema::hasColumn('wisudawans', 'lama_studi')) {
                $table->unsignedTinyInteger('lama_studi')->nullable()->after('total_sks');
            }
            if (! Schema::hasColumn('wisudawans', 'semester_lulus')) {
                $table->string('semester_lulus', 30)->nullable()->after('lama_studi');
            }
            if (! Schema::hasColumn('wisudawans', 'gelar')) {
                $table->string('gelar', 50)->nullable()->after('semester_lulus');
            }
            if (! Schema::hasColumn('wisudawans', 'gelar_full')) {
                $table->string('gelar_full', 150)->nullable()->after('gelar');
            }
            if (! Schema::hasColumn('wisudawans', 'is_eligible')) {
                $table->boolean('is_eligible')->default(true)->after('predikat_kelulusan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wisudawans', function (Blueprint $table) {
            $table->dropColumn([
                'nama',
                'no_sk',
                'tanggal_sk',
                'no_ijazah',
                'tanggal_ijazah',
                'ipk',
                'total_sks',
                'lama_studi',
                'semester_lulus',
                'gelar',
                'gelar_full',
                'is_eligible',
            ]);
        });
    }
};
