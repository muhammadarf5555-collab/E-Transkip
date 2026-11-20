<?php

namespace App\Console\Commands;

use App\Models\Mahasiswa;
use App\Services\SiakadApiService;
use Illuminate\Console\Command;

class SyncAllInformatikaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bridge:sync-all-infor
                            {--force : Paksa tarik ulang seluruh nilai dari Bridge API}
                            {--all : Tarik nilai untuk seluruh mahasiswa yang masih 0 SKS di semua prodi}
                            {--fakultas= : Filter berdasarkan ID fakultas (misal 1 untuk FITK)}
                            {--limit=50 : Batas jumlah mahasiswa per eksekusi agar cepat}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronkan transkrip nilai resmi mahasiswa langsung dari Bridge API UINSSC';

    /**
     * Execute the console command.
     */
    public function handle(SiakadApiService $service): int
    {
        $force = (bool) $this->option('force');
        $all = (bool) $this->option('all');
        $fakultasId = $this->option('fakultas');
        $limit = (int) ($this->option('limit') ?: 50);

        $query = Mahasiswa::with('transkrip');

        if (! $all && ! $fakultasId) {
            $query->where('nim', 'like', '238801%');
        } elseif ($fakultasId) {
            $query->where('fakultas_id', (int) $fakultasId);
        }

        if (! $force) {
            $query->where(function ($q) {
                $q->whereDoesntHave('transkrip')
                    ->orWhereHas('transkrip', fn ($t) => $t->where('total_sks', 0));
            });
        }

        $students = $query->orderBy('nim')->take($limit)->get();

        $total = $students->count();
        $this->info("Menemukan {$total} mahasiswa yang akan ditarik nilainya dari Bridge API UINSSC...");

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $synced = 0;
        $results = [];

        foreach ($students as $mhs) {
            $hasTranscript = $mhs->transkrip && $mhs->transkrip->total_sks > 0;

            if ($force || ! $hasTranscript) {
                try {
                    $trx = $service->syncStudentTranscript($mhs);
                    if ($trx) {
                        $synced++;
                        $results[] = [
                            'nim' => $mhs->nim,
                            'nama' => $mhs->nama,
                            'sks' => $trx->total_sks,
                            'ipk' => $trx->ipk,
                            'predikat' => $trx->predikat,
                        ];
                    }
                } catch (\Throwable $e) {
                    $this->error("Gagal sync NIM {$mhs->nim}: ".$e->getMessage());
                }
            } else {
                $results[] = [
                    'nim' => $mhs->nim,
                    'nama' => $mhs->nama,
                    'sks' => $mhs->transkrip->total_sks,
                    'ipk' => $mhs->transkrip->ipk,
                    'predikat' => $mhs->transkrip->predikat,
                ];
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->table(
            ['NIM', 'Nama Mahasiswa', 'Total SKS', 'IPK', 'Predikat'],
            $results
        );

        $this->info("Selesai! Berhasil menyinkronkan {$synced} transkrip baru dari Bridge API.");

        return Command::SUCCESS;
    }
}
