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
        Schema::create('siakad_nilais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswas')->onDelete('cascade');
            $table->foreignId('mata_kuliah_id')->constrained('siakad_mata_kuliahs')->onDelete('cascade');
            $table->string('semester');
            $table->string('nilai_huruf');
            $table->decimal('nilai_angka', 5, 2);
            $table->decimal('bobot', 5, 2);
            $table->integer('sks');
            $table->string('sumber_data')->default('SIAKAD');
            $table->timestamp('imported_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siakad_nilais');
    }
};
