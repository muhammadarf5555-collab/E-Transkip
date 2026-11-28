<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Mahasiswa extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::saved(function (Mahasiswa $mhs) {
            // Sinkronkan nama ke akun user jika ada
            if ($mhs->wasChanged('nama') && $mhs->user) {
                $mhs->user->updateQuietly(['name' => $mhs->nama]);
            }

            // Sinkronkan nama & NIM ke data wisudawan jika ada
            if ($mhs->wasChanged(['nama', 'nim', 'status', 'fakultas_id', 'program_studi_id'])) {
                $wisudawan = $mhs->wisudawan ?: Wisudawan::where('nim', $mhs->nim)->orWhere('mahasiswa_id', $mhs->id)->first();
                if ($wisudawan) {
                    $wisudawan->updateQuietly(array_filter([
                        'nama' => $mhs->nama,
                        'nim' => $mhs->nim,
                        'mahasiswa_id' => $mhs->id,
                        'fakultas' => $mhs->fakultas?->nama_fakultas ?: $wisudawan->fakultas,
                        'program_studi' => $mhs->programStudi?->nama_program_studi ?: $wisudawan->program_studi,
                    ]));
                }
            }
        });
    }

    /**
     * Hitung ulang dan sinkronkan data Transkrip serta Wisudawan secara live berdasarkan nilai SIAKAD.
     */
    public function recalculateTranskrip(): Transkrip
    {
        $nilais = $this->siakadNilais()->get();
        $totalSks = 0;
        $totalMutu = 0.0;

        foreach ($nilais as $n) {
            $sks = (int) $n->sks;
            if ($sks > 0) {
                $bobot = (float) ($n->bobot > 0 ? $n->bobot : AturanBobotNilai::getBobot((string) $n->nilai_huruf));
                $totalSks += $sks;
                $totalMutu += ($sks * $bobot);
            }
        }

        $ipk = $totalSks > 0 ? round($totalMutu / $totalSks, 2) : 0.00;

        $predikat = 'Cukup';
        if ($ipk >= 3.51) {
            $predikat = 'Dengan Pujian (Cum Laude)';
        } elseif ($ipk >= 3.01) {
            $predikat = 'Sangat Memuaskan';
        } elseif ($ipk >= 2.76) {
            $predikat = 'Memuaskan';
        }

        // Cari record Wisudawan HANYA jika mahasiswa memang sudah terdaftar di data wisudawan
        $wisudawan = $this->wisudawan ?: Wisudawan::where('nim', $this->nim)->orWhere('mahasiswa_id', $this->id)->first();

        if ($wisudawan) {
            $wisudawan->updateQuietly(array_filter([
                'mahasiswa_id' => $this->id,
                'nim' => $this->nim,
                'nama' => $this->nama,
                'ipk' => $ipk,
                'total_sks' => $totalSks,
                'predikat_kelulusan' => $predikat,
                'fakultas' => $this->fakultas?->nama_fakultas ?: $wisudawan->fakultas,
                'program_studi' => $this->programStudi?->nama_program_studi ?: $wisudawan->program_studi,
            ]));
        }

        // Sinkronkan ke tabel Transkrip
        $transkrip = $this->transkrip ?: Transkrip::firstOrNew(['mahasiswa_id' => $this->id]);
        $transkrip->total_sks = $totalSks;
        $transkrip->total_mutu = $totalMutu;
        $transkrip->ipk = $ipk;
        $transkrip->predikat = $predikat;
        $transkrip->wisudawan_id = $wisudawan?->id;
        if (empty($transkrip->kode_verifikasi)) {
            $transkrip->kode_verifikasi = 'TRX-UINSSC-'.$this->nim.'-OFFICIAL';
        }
        if (empty($transkrip->status_validasi) || $transkrip->status_validasi === 'Draft') {
            $transkrip->status_validasi = 'disahkan';
        }
        $transkrip->saveQuietly();

        // Sinkronkan nama ke akun User jika ada perbedaan
        if ($this->user && $this->user->name !== $this->nama) {
            $this->user->updateQuietly(['name' => $this->nama]);
        }

        return $transkrip;
    }

    protected $table = 'mahasiswas';

    protected $fillable = [
        'user_id',
        'nim',
        'nama',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'email',
        'no_hp',
        'fakultas_id',
        'program_studi_id',
        'jenjang',
        'angkatan',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fakultas(): BelongsTo
    {
        return $this->belongsTo(Fakultas::class, 'fakultas_id');
    }

    public function programStudi(): BelongsTo
    {
        return $this->belongsTo(ProgramStudi::class, 'program_studi_id');
    }

    public function siakadMahasiswa(): HasOne
    {
        return $this->hasOne(SiakadMahasiswa::class, 'mahasiswa_id');
    }

    public function siakadNilais(): HasMany
    {
        return $this->hasMany(SiakadNilai::class, 'mahasiswa_id');
    }

    public function wisudawan(): HasOne
    {
        return $this->hasOne(Wisudawan::class, 'mahasiswa_id');
    }

    public function transkrip(): HasOne
    {
        return $this->hasOne(Transkrip::class, 'mahasiswa_id');
    }

    public function pengajuanKoreksis(): HasMany
    {
        return $this->hasMany(PengajuanKoreksi::class, 'mahasiswa_id');
    }
}
