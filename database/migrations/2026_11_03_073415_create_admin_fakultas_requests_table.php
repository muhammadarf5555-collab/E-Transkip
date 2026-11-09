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
        Schema::create('admin_fakultas_requests', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('email');
            $table->foreignId('fakultas_id')->constrained('fakultas')->cascadeOnDelete();
            $table->string('nip')->nullable();
            $table->text('alasan')->nullable();
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->text('catatan_super_admin')->nullable();
            $table->foreignId('created_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_fakultas_requests');
    }
};
