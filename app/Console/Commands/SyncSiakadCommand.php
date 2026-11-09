<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\SiakadSyncLog;
use App\Services\SiakadApiService;
use Exception;
use Illuminate\Console\Command;

class SyncSiakadCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'siakad:sync {--fakultas=2 : ID Fakultas target pada Bridge API} {--user= : ID User yang memicu sinkronisasi manual}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronisasi berkala data program studi dan akademik dari Bridge SIAKAD UINSSC';

    /**
     * Execute the console command.
     */
    public function handle(SiakadApiService $siakadService): int
    {
        $fakultasId = (int) $this->option('fakultas');
        $userId = $this->option('user') ? (int) $this->option('user') : null;
        $endpoint = "https://bridge.uinssc.ac.id/api/program_studi?fakultas={$fakultasId}";

        $this->info("Memulai sinkronisasi data dari endpoint: {$endpoint}...");

        try {
            $result = $siakadService->syncProgramStudiFromBridge($fakultasId);

            $status = $result['success'] ? 'sukses' : 'gagal';
            $totalBerhasil = $result['count'] ?? 0;
            $totalGagal = $result['success'] ? 0 : 1;
            $totalDiperbarui = $result['updated_count'] ?? $totalBerhasil;
            $pesanError = $result['success'] ? null : ($result['message'] ?? 'Gagal menghubungi server Bridge API.');

            // Record into SiakadSyncLog
            SiakadSyncLog::create([
                'user_id' => $userId,
                'fakultas_id' => $fakultasId,
                'endpoint' => $endpoint,
                'status' => $status,
                'total_berhasil' => $totalBerhasil,
                'total_gagal' => $totalGagal,
                'total_diperbarui' => $totalDiperbarui,
                'total_prodi' => $totalBerhasil,
                'total_mahasiswa' => 0,
                'total_nilai' => 0,
                'konflik_count' => 0,
                'keterangan' => $result['message'] ?? 'Sinkronisasi Bridge API Program Studi.',
                'pesan_error' => $pesanError,
            ]);

            AuditLog::record(
                'Sinkronisasi SIAKAD '.ucfirst($status),
                'SiakadSyncLog',
                (string) $fakultasId,
                "Sinkronisasi Bridge API fakultas {$fakultasId}: {$status}. Berhasil: {$totalBerhasil}, Diperbarui: {$totalDiperbarui}."
            );

            if ($result['success']) {
                $this->info("✓ Sinkronisasi berhasil: {$result['message']}");

                return Command::SUCCESS;
            }

            $this->warn("⚠ Sinkronisasi selesai dengan catatan: {$result['message']}");

            return Command::SUCCESS;
        } catch (Exception $e) {
            $this->error('✗ Terjadi kesalahan pada proses sinkronisasi: '.$e->getMessage());

            SiakadSyncLog::create([
                'user_id' => $userId,
                'fakultas_id' => $fakultasId,
                'endpoint' => $endpoint,
                'status' => 'gagal',
                'total_berhasil' => 0,
                'total_gagal' => 1,
                'total_diperbarui' => 0,
                'total_prodi' => 0,
                'total_mahasiswa' => 0,
                'total_nilai' => 0,
                'konflik_count' => 0,
                'keterangan' => 'Koneksi API Bridge mengalami gangguan.',
                'pesan_error' => $e->getMessage(),
            ]);

            return Command::FAILURE;
        }
    }
}
