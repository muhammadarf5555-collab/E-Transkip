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
        // 1. Create transkrip_histories table
        if (! Schema::hasTable('transkrip_histories')) {
            Schema::create('transkrip_histories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('transkrip_id')->constrained('transkrips')->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('status_sebelumnya');
                $table->string('status_terbaru');
                $table->text('catatan')->nullable();
                $table->timestamps();
            });
        }

        // 2. Add verification & ratification columns to transkrips
        Schema::table('transkrips', function (Blueprint $table) {
            if (! Schema::hasColumn('transkrips', 'verified_by_jurusan_id')) {
                $table->foreignId('verified_by_jurusan_id')->nullable()->after('status_validasi')->constrained('users')->nullOnDelete();
                $table->timestamp('verified_by_jurusan_at')->nullable()->after('verified_by_jurusan_id');
                $table->foreignId('ratified_by_admin_id')->nullable()->after('verified_by_jurusan_at')->constrained('users')->nullOnDelete();
                $table->timestamp('ratified_by_admin_at')->nullable()->after('ratified_by_admin_id');
            }
        });

        // 3. Add success/failure/updated counts and error message to siakad_sync_logs
        Schema::table('siakad_sync_logs', function (Blueprint $table) {
            if (! Schema::hasColumn('siakad_sync_logs', 'total_berhasil')) {
                $table->integer('total_berhasil')->default(0)->after('status');
                $table->integer('total_gagal')->default(0)->after('total_berhasil');
                $table->integer('total_diperbarui')->default(0)->after('total_gagal');
                $table->text('pesan_error')->nullable()->after('keterangan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siakad_sync_logs', function (Blueprint $table) {
            $table->dropColumn(['total_berhasil', 'total_gagal', 'total_diperbarui', 'pesan_error']);
        });

        Schema::table('transkrips', function (Blueprint $table) {
            $table->dropConstrainedForeignId('verified_by_jurusan_id');
            $table->dropColumn('verified_by_jurusan_at');
            $table->dropConstrainedForeignId('ratified_by_admin_id');
            $table->dropColumn('ratified_by_admin_at');
        });

        Schema::dropIfExists('transkrip_histories');
    }
};
