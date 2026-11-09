<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Services\SiakadApiService;
use Illuminate\Console\Command;

class SyncBridgeMasterCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bridge:sync-master {--fakultas= : ID/Kode fakultas spesifik (contoh: 2 untuk Syariah)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronisasi data Fakultas dan Program Studi dari UINSSC Bridge API';

    /**
     * Execute the console command.
     */
    public function handle(SiakadApiService $siakadApi): int
    {
        $this->info('Memeriksa koneksi ke UINSSC Bridge API...');

        $fakultasId = $this->option('fakultas') ? (int) $this->option('fakultas') : 2;
        $status = $siakadApi->testBridgeConnection($fakultasId);

        $this->line("Endpoint: {$status['endpoint']}");
        $this->line('Status HTTP: '.($status['http_code'] ?? 'Tidak Terhubung'));

        if ($status['status'] === 'unauthorized') {
            $this->error('Gagal: Token API tidak valid (401 Unauthorized).');
            $this->warn('Periksa kembali nilai BRIDGE_API_TOKEN pada file .env.');
            $this->comment('Sistem tetap menggunakan data lokal / dummy yang ada.');

            return Command::FAILURE;
        }

        if ($status['status'] !== 'connected') {
            $this->error("Gagal terhubung ke Bridge API: {$status['message']}");
            $this->comment('Sistem tetap menggunakan data lokal / dummy yang ada.');

            return Command::FAILURE;
        }

        $this->info('Koneksi berhasil (HTTP 200 OK)! Memulai proses sinkronisasi...');

        // 1. Sync Fakultas
        $this->line('1. Mengambil data Fakultas...');
        $fakResult = $siakadApi->syncFakultasFromBridge();
        if ($fakResult['success']) {
            $this->info("   [OK] {$fakResult['message']}");
        } else {
            $this->warn("   [GAGAL] {$fakResult['message']}");
        }

        // 2. Sync Program Studi
        $targetFakultas = $this->option('fakultas') ? (int) $this->option('fakultas') : null;
        $this->line('2. Mengambil data Program Studi'.($targetFakultas ? " (Fakultas ID: {$targetFakultas})" : ' (Semua)').'...');
        $prodiResult = $siakadApi->syncProgramStudiFromBridge($targetFakultas);
        if ($prodiResult['success']) {
            $this->info("   [OK] {$prodiResult['message']}");
        } else {
            $this->warn("   [GAGAL] {$prodiResult['message']}");
        }

        AuditLog::record(
            'Sinkronisasi CLI Bridge API',
            'MasterData',
            'CLI',
            "Sinkronisasi CLI: Fakultas ({$fakResult['created']} baru, {$fakResult['updated']} update), Prodi ({$prodiResult['created']} baru, {$prodiResult['updated']} update)."
        );

        $this->newLine();
        $this->info('Sinkronisasi Master Data selesai.');

        return Command::SUCCESS;
    }
}
