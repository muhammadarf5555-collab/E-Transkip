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
        // 1. Aturan Bobot Nilai
        if (! Schema::hasTable('aturan_bobot_nilais')) {
            Schema::create('aturan_bobot_nilais', function (Blueprint $table) {
                $table->id();
                $table->string('nilai_huruf', 5)->unique();
                $table->decimal('bobot', 4, 2);
                $table->string('keterangan')->nullable();
                $table->timestamps();
            });
        }

        // 2. Add conflict tracking to siakad_nilais
        if (Schema::hasTable('siakad_nilais')) {
            Schema::table('siakad_nilais', function (Blueprint $table) {
                if (! Schema::hasColumn('siakad_nilais', 'has_conflict')) {
                    $table->boolean('has_conflict')->default(false)->after('sumber_data');
                }
                if (! Schema::hasColumn('siakad_nilais', 'conflict_details')) {
                    $table->text('conflict_details')->nullable()->after('has_conflict');
                }
            });
        }

        // 3. Pengajuan Koreksi Nilai / Data
        if (! Schema::hasTable('pengajuan_koreksis')) {
            Schema::create('pengajuan_koreksis', function (Blueprint $table) {
                $table->id();
                $table->foreignId('mahasiswa_id')->constrained('mahasiswas')->cascadeOnDelete();
                $table->foreignId('siakad_nilai_id')->nullable()->constrained('siakad_nilais')->nullOnDelete();
                $table->foreignId('mata_kuliah_id')->nullable()->constrained('siakad_mata_kuliahs')->nullOnDelete();
                $table->string('jenis_koreksi')->default('Nilai Mata Kuliah'); // 'Nilai Mata Kuliah', 'Biodata Mahasiswa', 'Lainnya'
                $table->string('nilai_lama', 5)->nullable();
                $table->string('nilai_diajukan', 5)->nullable();
                $table->text('alasan');
                $table->string('dokumen_pendukung')->nullable();
                $table->string('status')->default('Menunggu Review'); // 'Menunggu Review', 'Disetujui', 'Ditolak'
                $table->text('catatan_admin')->nullable();
                $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('reviewed_at')->nullable();
                $table->timestamps();
            });
        }

        // 4. Notifikasi Pengguna
        if (! Schema::hasTable('notifikasis')) {
            Schema::create('notifikasis', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('judul');
                $table->text('pesan');
                $table->string('tipe')->default('info'); // 'info', 'success', 'warning', 'danger'
                $table->boolean('is_read')->default(false);
                $table->string('link')->nullable();
                $table->timestamps();
            });
        }

        // 5. Riwayat Perubahan Nilai
        if (! Schema::hasTable('nilai_change_histories')) {
            Schema::create('nilai_change_histories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('siakad_nilai_id')->constrained('siakad_nilais')->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('nilai_huruf_lama', 5);
                $table->decimal('bobot_lama', 4, 2);
                $table->string('nilai_huruf_baru', 5);
                $table->decimal('bobot_baru', 4, 2);
                $table->text('alasan')->nullable();
                $table->timestamps();
            });
        }

        // 6. Log Sinkronisasi SIAKAD
        if (! Schema::hasTable('siakad_sync_logs')) {
            Schema::create('siakad_sync_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('fakultas_id')->nullable()->constrained('fakultas')->nullOnDelete();
                $table->string('endpoint');
                $table->string('status')->default('sukses'); // 'sukses', 'gagal'
                $table->integer('total_prodi')->default(0);
                $table->integer('total_mahasiswa')->default(0);
                $table->integer('total_nilai')->default(0);
                $table->integer('konflik_count')->default(0);
                $table->text('keterangan')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siakad_sync_logs');
        Schema::dropIfExists('nilai_change_histories');
        Schema::dropIfExists('notifikasis');
        Schema::dropIfExists('pengajuan_koreksis');

        if (Schema::hasTable('siakad_nilais')) {
            Schema::table('siakad_nilais', function (Blueprint $table) {
                if (Schema::hasColumn('siakad_nilais', 'conflict_details')) {
                    $table->dropColumn('conflict_details');
                }
                if (Schema::hasColumn('siakad_nilais', 'has_conflict')) {
                    $table->dropColumn('has_conflict');
                }
            });
        }

        Schema::dropIfExists('aturan_bobot_nilais');
    }
};
