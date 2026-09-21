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
        Schema::create('transkrips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswas')->onDelete('cascade');
            $table->foreignId('wisudawan_id')->nullable()->constrained('wisudawans')->onDelete('set null');
            $table->integer('total_sks')->default(0);
            $table->decimal('total_mutu', 8, 2)->default(0);
            $table->decimal('ipk', 4, 2)->default(0);
            $table->string('predikat')->nullable();
            $table->string('status_validasi')->default('Draft');
            $table->string('kode_verifikasi')->unique();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transkrips');
    }
};
