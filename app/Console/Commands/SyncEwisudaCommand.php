<?php

namespace App\Console\Commands;

use App\Models\Wisudawan;
use App\Services\SiakadApiService;
use Illuminate\Console\Command;

class SyncEwisudaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ewisuda:sync
                            {--nims= : Daftar NIM wisudawan dipisahkan koma (misal: 201410001,201410002)}
                            {--api-key= : Custom X-Ewisuda-Key untuk autentikasi ke wisuda.uinssc.ac.id}
                            {--periode= : Periode Wisuda (misal: Periode II Tahun 2026)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronisasi data administrasi wisudawan dari API E-Wisuda (GET /api/administrative?nims=...)';

    /**
     * Execute the console command.
     */
    public function handle(SiakadApiService $service): int
    {
        $nimsOption = $this->option('nims');
        $apiKey = $this->option('api-key') ?: null;
        $periode = $this->option('periode') ?: null;

        if ($nimsOption) {
            $nims = $nimsOption;
        } else {
            $existingNims = Wisudawan::pluck('nim')->filter()->values()->all();
            $defaultRealNims = [
                '2388010001',
                '2388010002',
                '2388010003',
                '2281050039',
                '2381010152',
                '2381090107',
            ];
            $nims = array_values(array_unique(array_merge($existingNims, $defaultRealNims)));
        }

        $this->info('Menghubungi API E-Wisuda (/api/administrative)...');

        $result = $service->syncWisudawanFromEwisuda($nims, $apiKey, true, $periode);

        if (! $result['success']) {
            $this->error($result['message'] ?? 'Gagal menyinkronkan data E-Wisuda.');

            return self::FAILURE;
        }

        $this->info($result['message']);
        $this->line('Sumber Data : '.($result['source'] ?? '-'));
        $this->line('Berhasil    : '.($result['synced_count'] ?? 0).' wisudawan');
        $this->line('Diperbarui  : '.($result['updated_count'] ?? 0).' wisudawan');

        return self::SUCCESS;
    }
}
