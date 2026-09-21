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
        Schema::create('siakad_mata_kuliahs', function (Blueprint $table) {
            $table->id();
            $table->string('kode_mata_kuliah')->unique();
            $table->string('nama_mata_kuliah');
            $table->integer('sks');
            $table->string('semester')->nullable();
            $table->string('jenis_mata_kuliah')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siakad_mata_kuliahs');
    }
};
