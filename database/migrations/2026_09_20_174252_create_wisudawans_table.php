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
        Schema::create('wisudawans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->nullable()->constrained('mahasiswas')->onDelete('set null');
            $table->string('nim')->unique();
            $table->string('nomor_pendaftaran_wisuda')->nullable();
            $table->string('periode_wisuda');
            $table->date('tanggal_wisuda')->nullable();
            $table->string('fakultas')->nullable();
            $table->string('program_studi')->nullable();
            $table->string('judul_skripsi')->nullable();
            $table->string('predikat_kelulusan')->nullable();
            $table->string('status_validasi')->default('Draft');
            $table->string('sumber_data')->default('WISUDAWAN');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wisudawans');
    }
};
