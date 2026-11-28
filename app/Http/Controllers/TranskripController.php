<?php

namespace App\Http\Controllers;

use App\Models\AturanBobotNilai;
use App\Models\AuditLog;
use App\Models\Fakultas;
use App\Models\Mahasiswa;
use App\Models\ProgramStudi;
use App\Models\SiakadMataKuliah;
use App\Models\Transkrip;
use App\Models\Wisudawan;
use App\Services\SiakadApiService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TranskripController extends Controller
{
    public function index(Request $request, SiakadApiService $siakadApi): View
    {
        $siakadApi->ensureCleanOfficialDataset();

        $user = Auth::user();
        $query = Transkrip::with(['mahasiswa.fakultas', 'mahasiswa.programStudi.fakultas', 'mahasiswa.siakadNilais', 'wisudawan']);

        // Scope to faculty for Admin Fakultas
        if ($user->isAdminFakultas() && $user->fakultas_id) {
            $query->whereHas('mahasiswa', function ($m) use ($user) {
                if ((int) $user->fakultas_id === 1) {
                    $m->where(function ($q) {
                        $q->where('fakultas_id', 1)->orWhere('program_studi_id', 1);
                    });
                } else {
                    $m->where('fakultas_id', $user->fakultas_id);
                }
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_verifikasi', 'like', "%{$search}%")
                    ->orWhere('nomor_transkrip', 'like', "%{$search}%")
                    ->orWhereHas('mahasiswa', function ($m) use ($search) {
                        $m->where('nim', 'like', "%{$search}%")
                            ->orWhere('nama', 'like', "%{$search}%");
                    })
                    ->orWhereHas('wisudawan', function ($w) use ($search) {
                        $w->where('no_sk', 'like', "%{$search}%")
                            ->orWhere('no_ijazah', 'like', "%{$search}%")
                            ->orWhere('judul_skripsi', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status_validasi', $request->status);
        }

        $transkrips = $query
            ->orderByRaw('CASE WHEN total_sks > 0 OR wisudawan_id IS NOT NULL THEN 0 ELSE 1 END ASC')
            ->orderByDesc('total_sks')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // Jika di halaman aktif ada mahasiswa yang nilainya masih 0 SKS, tarik otomatis secara paralel dari Bridge API UINSSC
        $emptyMahasiswas = $transkrips->getCollection()
            ->filter(fn (Transkrip $t) => (int) $t->total_sks === 0 && $t->mahasiswa !== null)
            ->map(fn (Transkrip $t) => $t->mahasiswa);

        if ($emptyMahasiswas->isNotEmpty()) {
            $syncedNow = $siakadApi->syncMultipleStudentsFromBridge($emptyMahasiswas);
            if ($syncedNow > 0) {
                $transkrips = $query
                    ->orderByRaw('CASE WHEN total_sks > 0 OR wisudawan_id IS NOT NULL THEN 0 ELSE 1 END ASC')
                    ->orderByDesc('total_sks')
                    ->latest()
                    ->paginate(10)
                    ->withQueryString();
            }
        }

        $baseCountQuery = Transkrip::query();
        if ($user->isAdminFakultas() && $user->fakultas_id) {
            $baseCountQuery->whereHas('mahasiswa', function ($m) use ($user) {
                if ((int) $user->fakultas_id === 1) {
                    $m->where(function ($q) {
                        $q->where('fakultas_id', 1)->orWhere('program_studi_id', 1);
                    });
                } else {
                    $m->where('fakultas_id', $user->fakultas_id);
                }
            });
        }

        $counts = [
            'total' => (clone $baseCountQuery)->count(),
            'draft' => (clone $baseCountQuery)->where('status_validasi', 'Draft')->count(),
            'menunggu' => (clone $baseCountQuery)->where('status_validasi', 'Menunggu Validasi')->count(),
            'valid' => (clone $baseCountQuery)->where('status_validasi', 'Valid')->count(),
            'ditolak' => (clone $baseCountQuery)->where('status_validasi', 'Ditolak')->count(),
        ];

        // Daftar Mahasiswa & Prodi untuk Modal Input Manual Terpadu
        $mhsListQuery = Mahasiswa::with(['programStudi', 'fakultas', 'wisudawan'])->orderBy('nim');
        $prodiListQuery = ProgramStudi::with('fakultas')->orderBy('nama_program_studi');
        if ($user->isAdminFakultas() && $user->fakultas_id) {
            if ((int) $user->fakultas_id === 1) {
                $mhsListQuery->where(function ($q) {
                    $q->where('fakultas_id', 1)->orWhere('program_studi_id', 1);
                });
                $prodiListQuery->where(function ($q) {
                    $q->where('fakultas_id', 1)->orWhere('id', 1);
                });
            } else {
                $mhsListQuery->where('fakultas_id', $user->fakultas_id);
                $prodiListQuery->where('fakultas_id', $user->fakultas_id);
            }
        }
        $mahasiswasList = $mhsListQuery->take(300)->get();
        $programStudisList = $prodiListQuery->get();
        $activeFakultas = ($user->isAdminFakultas() && $user->fakultas_id)
            ? Fakultas::find($user->fakultas_id)
            : Fakultas::first();

        return view('admin.transkrip.index', compact('transkrips', 'counts', 'mahasiswasList', 'programStudisList', 'activeFakultas'));
    }

    public function sync(Request $request, SiakadApiService $siakadApi): RedirectResponse
    {
        @set_time_limit(120);
        $user = Auth::user();

        // 1. Tarik otomatis data dari SIAKAD & E-Wisuda API (terutama mahasiswa utama FITK / NIM yang diminta)
        $nimsInput = trim((string) $request->input('nims', ''));
        if ($nimsInput !== '') {
            $siakadApi->syncWisudawanFromEwisuda($nimsInput, null, true);
        } elseif ($user->isSuperAdmin() || (int) $user->fakultas_id === 1) {
            $siakadApi->syncWisudawanFromEwisuda('2281050039', null, true);
        }

        $wisudawanQuery = Wisudawan::with(['mahasiswa.siakadNilais.mataKuliah']);

        if ($user->isAdminFakultas() && $user->fakultas_id) {
            $wisudawanQuery->where(function ($q) use ($user) {
                if ((int) $user->fakultas_id === 1) {
                    $q->whereHas('mahasiswa', fn ($m) => $m->where('fakultas_id', 1)->orWhere('program_studi_id', 1))
                        ->orWhere('fakultas', 'like', '%Tarbiyah%')
                        ->orWhere('fakultas', 'Fakultas Sains dan Teknologi');
                } else {
                    $q->whereHas('mahasiswa', fn ($m) => $m->where('fakultas_id', $user->fakultas_id))
                        ->orWhere('fakultas', $user->fakultas?->nama_fakultas);
                }
            });
        }

        $wisudawans = $wisudawanQuery->get();
        $synced = 0;

        // Tarik paralel terlebih dahulu jika ada wisudawan yang mahasiswanya belum punya nilai
        $emptyWisudaMhs = $wisudawans
            ->map(fn (Wisudawan $w) => $w->mahasiswa)
            ->filter(fn (?Mahasiswa $m) => $m !== null && $m->siakadNilais->isEmpty())
            ->take(8);

        if ($emptyWisudaMhs->isNotEmpty()) {
            $siakadApi->syncMultipleStudentsFromBridge($emptyWisudaMhs);
            $wisudawans->load('mahasiswa.siakadNilais');
        }

        foreach ($wisudawans as $wisudawan) {
            $mhs = $wisudawan->mahasiswa;
            if (! $mhs) {
                $mhs = Mahasiswa::where('nim', $wisudawan->nim)->first();
                if ($mhs) {
                    $wisudawan->update(['mahasiswa_id' => $mhs->id]);
                }
            }

            if (! $mhs) {
                continue;
            }

            $nilais = $mhs->siakadNilais;
            $totalSks = 0;
            $totalMutu = 0.0;

            foreach ($nilais as $nilai) {
                $sks = $nilai->sks;
                $bobot = $nilai->bobot;
                $totalSks += $sks;
                $totalMutu += ($bobot * $sks);
            }

            $ipk = $totalSks > 0 ? round($totalMutu / $totalSks, 2) : 0.0;

            $predikat = 'Cukup';
            if ($ipk >= 3.51) {
                $predikat = 'Dengan Pujian (Cum Laude)';
            } elseif ($ipk >= 3.01) {
                $predikat = 'Sangat Memuaskan';
            } elseif ($ipk >= 2.76) {
                $predikat = 'Memuaskan';
            }

            $transkrip = Transkrip::firstOrNew(['mahasiswa_id' => $mhs->id]);
            $transkrip->wisudawan_id = $wisudawan->id;
            $transkrip->total_sks = $totalSks;
            $transkrip->total_mutu = $totalMutu;
            $transkrip->ipk = $ipk;
            $transkrip->predikat = $predikat;

            if (empty($transkrip->kode_verifikasi)) {
                $transkrip->kode_verifikasi = 'UINSSC-TRX-'.$mhs->nim.'-'.strtoupper(Str::random(6));
            }

            if ($transkrip->status_validasi === 'Draft' || ! $transkrip->exists) {
                $transkrip->status_validasi = 'Menunggu Validasi';
            }

            $transkrip->save();
            $synced++;
        }

        // 2. Tarik juga batch nilai real dari Bridge API untuk transkrip yang masih 0 SKS di fakultas ini
        $emptyTrxQuery = Transkrip::with('mahasiswa')->where('total_sks', 0);
        if ($user->isAdminFakultas() && $user->fakultas_id) {
            $emptyTrxQuery->whereHas('mahasiswa', function ($m) use ($user) {
                if ((int) $user->fakultas_id === 1) {
                    $m->where(fn ($q) => $q->where('fakultas_id', 1)->orWhere('program_studi_id', 1));
                } else {
                    $m->where('fakultas_id', $user->fakultas_id);
                }
            });
        }
        $batchEmptyMhs = $emptyTrxQuery->take(6)->get()->pluck('mahasiswa')->filter();
        if ($batchEmptyMhs->isNotEmpty()) {
            $synced += $siakadApi->syncMultipleStudentsFromBridge($batchEmptyMhs);
        }

        AuditLog::record('Sinkronisasi Transkrip SIAKAD & E-Wisuda', 'Transkrip', null, "Menyinkronkan data SIAKAD + E-Wisuda dan mengalkulasi ulang {$synced} transkrip wisudawan.");

        return redirect()->route('admin.transkrip.index')->with('success', "Sinkronisasi SIAKAD & E-Wisuda selesai. Berhasil menarik dan memperbarui {$synced} berkas transkrip nilai dari API.");
    }

    public function show(Transkrip $transkrip, SiakadApiService $siakadApi): View
    {
        $user = Auth::user();
        if ($user->isAdminFakultas() && $transkrip->mahasiswa && ! $user->canAccessMahasiswa($transkrip->mahasiswa)) {
            abort(403, 'Akses ditolak. Transkrip ini berada di luar fakultas Anda.');
        }

        if ((int) $transkrip->total_sks === 0 && $transkrip->mahasiswa) {
            $siakadApi->syncStudentTranscript($transkrip->mahasiswa);
            $transkrip->refresh();
        }

        if ($transkrip->mahasiswa) {
            $transkrip = $transkrip->mahasiswa->recalculateTranskrip();
        }

        $transkrip->load([
            'mahasiswa.fakultas',
            'mahasiswa.programStudi.fakultas',
            'mahasiswa.siakadNilais.mataKuliah',
            'wisudawan',
        ]);

        $mataKuliahs = SiakadMataKuliah::orderBy('nama_mata_kuliah')->get();
        $bobotList = AturanBobotNilai::orderByDesc('bobot')->get();

        $qrUrl = $this->getVerificationUrl($transkrip->kode_verifikasi);
        $qrCodeSvg = base64_encode(QrCode::format('svg')->size(120)->generate($qrUrl));

        return view('admin.transkrip.show', compact('transkrip', 'qrCodeSvg', 'qrUrl', 'mataKuliahs', 'bobotList'));
    }

    /**
     * Input / Buat Transkrip & Data Kelulusan Manual Terpadu (SIAKAD + E-Wisuda + Pejabat dalam 1 Form).
     */
    public function storeManual(Request $request, SiakadApiService $siakadApi): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'nim' => ['required', 'string', 'max:50'],
            'nama' => ['nullable', 'string', 'max:255'],
            'program_studi_id' => ['nullable', 'exists:program_studis,id'],
            'tempat_lahir' => ['nullable', 'string', 'max:150'],
            'tanggal_lahir' => ['nullable', 'date'],
            'no_sk' => ['nullable', 'string', 'max:150'],
            'tanggal_sk' => ['nullable', 'date'],
            'no_ijazah' => ['nullable', 'string', 'max:150'],
            'tanggal_ijazah' => ['nullable', 'date'],
            'periode_wisuda' => ['nullable', 'string', 'max:100'],
            'tanggal_wisuda' => ['nullable', 'date'],
            'judul_skripsi' => ['nullable', 'string', 'max:1000'],
            'predikat_kelulusan' => ['nullable', 'string', 'max:100'],
            'nomor_transkrip' => ['nullable', 'string', 'max:150'],
            'no_sk_banpt_override' => ['nullable', 'string', 'max:150'],
            'jabatan_dekan_override' => ['nullable', 'string', 'max:100'],
            'nama_dekan_override' => ['nullable', 'string', 'max:255'],
            'nip_dekan_override' => ['nullable', 'string', 'max:50'],
        ]);

        $nim = trim($validated['nim']);
        $namaInput = $validated['nama'] ?? null;
        $prodiIdInput = $validated['program_studi_id'] ?? null;
        $tempatLahirInput = $validated['tempat_lahir'] ?? null;
        $tanggalLahirInput = $validated['tanggal_lahir'] ?? null;
        $noSkInput = $validated['no_sk'] ?? null;
        $tanggalSkInput = $validated['tanggal_sk'] ?? null;
        $noIjazahInput = $validated['no_ijazah'] ?? null;
        $tanggalIjazahInput = $validated['tanggal_ijazah'] ?? null;
        $periodeWisudaInput = $validated['periode_wisuda'];
        $tanggalWisudaInput = $validated['tanggal_wisuda'] ?? null;
        $judulSkripsiInput = $validated['judul_skripsi'] ?? null;
        $predikatInput = $validated['predikat_kelulusan'] ?? null;
        $nomorTranskripInput = $validated['nomor_transkrip'] ?? null;
        $noSkBanptInput = $validated['no_sk_banpt_override'] ?? null;
        $jabatanDekanInput = $validated['jabatan_dekan_override'] ?? null;
        $namaDekanInput = $validated['nama_dekan_override'] ?? null;
        $nipDekanInput = $validated['nip_dekan_override'] ?? null;

        $mhs = Mahasiswa::where('nim', $nim)->first();
        $dirProfile = $siakadApi->getDirectoryStudentProfile($nim);

        $asWisudawan = $request->boolean('daftarkan_wisuda', false)
            || ! empty($noSkInput)
            || ! empty($noIjazahInput)
            || ($mhs && ($mhs->wisudawan !== null || $mhs->status === 'Lulus'));

        if ($mhs) {
            if ($user->isAdminFakultas() && ! $user->canAccessMahasiswa($mhs)) {
                abort(403, 'Akses ditolak. Mahasiswa NIM '.$nim.' berada di luar fakultas Anda.');
            }
            $mhs->update(array_filter([
                'nama' => $namaInput ?: $mhs->nama,
                'program_studi_id' => $prodiIdInput ?: $mhs->program_studi_id,
                'tempat_lahir' => $tempatLahirInput ?: $mhs->tempat_lahir,
                'tanggal_lahir' => $tanggalLahirInput ?: $mhs->tanggal_lahir,
                'status' => $asWisudawan ? 'Lulus' : 'Aktif',
            ]));
        } else {
            $prodi = $prodiIdInput
                ? ProgramStudi::find($prodiIdInput)
                : ProgramStudi::where('fakultas_id', $user->fakultas_id ?: 1)->first();

            $mhs = Mahasiswa::create([
                'nim' => $nim,
                'nama' => $namaInput ?: ($dirProfile['nama'] ?? ('Mahasiswa '.$nim)),
                'tempat_lahir' => $tempatLahirInput ?: ($dirProfile['tempat_lahir'] ?? 'Cirebon'),
                'tanggal_lahir' => $tanggalLahirInput ?: ($dirProfile['tanggal_lahir'] ?? null),
                'fakultas_id' => $prodi?->fakultas_id ?: ($user->fakultas_id ?: 1),
                'program_studi_id' => $prodi?->id,
                'jenjang' => $prodi?->jenjang ?: 'S1',
                'angkatan' => substr($nim, 0, 2) ? ('20'.substr($nim, 0, 2)) : '2022',
                'status' => $asWisudawan ? 'Lulus' : 'Aktif',
            ]);
        }

        $mhs->load(['fakultas', 'programStudi', 'siakadNilais']);

        if ($mhs->siakadNilais->isEmpty()) {
            $siakadApi->syncStudentTranscript($mhs);
            $mhs->load('siakadNilais');
        }

        $nilais = $mhs->siakadNilais;
        $totalSks = 0;
        $totalMutu = 0.0;
        foreach ($nilais as $n) {
            $totalSks += $n->sks;
            $totalMutu += ($n->bobot * $n->sks);
        }
        $ipk = $totalSks > 0 ? round($totalMutu / $totalSks, 2) : 0.00;

        $predikat = $predikatInput;
        if (! $predikat) {
            if ($ipk >= 3.51) {
                $predikat = 'Dengan Pujian (Cum Laude)';
            } elseif ($ipk >= 3.01) {
                $predikat = 'Sangat Memuaskan';
            } elseif ($ipk >= 2.76) {
                $predikat = 'Memuaskan';
            } else {
                $predikat = 'Dengan Pujian (Cum Laude)';
            }
        }

        $wisudawan = null;
        if ($asWisudawan) {
            $wisudawan = Wisudawan::updateOrCreate(
                ['nim' => $mhs->nim],
                [
                    'mahasiswa_id' => $mhs->id,
                    'nama' => $mhs->nama,
                    'nomor_pendaftaran_wisuda' => 'WIS-'.date('Y').'-'.substr($mhs->nim, -4),
                    'periode_wisuda' => $periodeWisudaInput ?: ('Wisuda Ganjil '.date('Y')),
                    'tanggal_wisuda' => $tanggalWisudaInput ?: ($tanggalSkInput ?: now()->toDateString()),
                    'fakultas' => $mhs->fakultas?->nama_fakultas,
                    'program_studi' => $mhs->programStudi?->nama_program_studi,
                    'no_sk' => $noSkInput,
                    'tanggal_sk' => $tanggalSkInput,
                    'no_ijazah' => $noIjazahInput,
                    'tanggal_ijazah' => $tanggalIjazahInput ?: $tanggalSkInput,
                    'ipk' => $ipk,
                    'total_sks' => $totalSks,
                    'gelar' => $mhs->programStudi?->gelar_akademik,
                    'gelar_full' => $mhs->programStudi?->gelar_akademik_full,
                    'judul_skripsi' => $judulSkripsiInput,
                    'predikat_kelulusan' => $predikat,
                    'is_eligible' => true,
                    'status_validasi' => 'Menunggu Validasi',
                    'sumber_data' => 'INPUT_MANUAL_TERPADU',
                ]
            );
        }

        $transkrip = Transkrip::firstOrNew(['mahasiswa_id' => $mhs->id]);
        $transkrip->wisudawan_id = $wisudawan?->id;
        $transkrip->total_sks = $totalSks;
        $transkrip->total_mutu = $totalMutu;
        $transkrip->ipk = $ipk;
        $transkrip->predikat = $predikat;
        $transkrip->nomor_transkrip = $nomorTranskripInput ?: $transkrip->nomor_transkrip;
        $transkrip->no_sk_banpt_override = $noSkBanptInput ?: $transkrip->no_sk_banpt_override;
        $transkrip->jabatan_dekan_override = $jabatanDekanInput ?: $transkrip->jabatan_dekan_override;
        $transkrip->nama_dekan_override = $namaDekanInput ?: $transkrip->nama_dekan_override;
        $transkrip->nip_dekan_override = $nipDekanInput ?: $transkrip->nip_dekan_override;

        if (empty($transkrip->kode_verifikasi)) {
            $transkrip->kode_verifikasi = 'UINSSC-TRX-'.$mhs->nim.'-'.strtoupper(Str::random(6));
        }
        if (! $transkrip->exists) {
            $transkrip->status_validasi = $asWisudawan ? 'Menunggu Validasi' : 'Draft';
        }
        $transkrip->save();

        AuditLog::record(
            'Input Manual Transkrip & Kelulusan Terpadu',
            'Transkrip',
            (string) $transkrip->id,
            "{$user->name} menambahkan/melengkapi data transkrip NIM {$mhs->nim} (".($asWisudawan ? 'Wisudawan' : 'Mahasiswa Aktif').') secara terpadu.'
        );

        return redirect()->route('admin.transkrip.show', $transkrip)
            ->with('success', 'Data Transkrip '.($asWisudawan ? 'Kelulusan Wisuda' : 'Akademik Mahasiswa Aktif')." untuk {$mhs->nama} ({$mhs->nim}) berhasil disimpan secara terpadu.");
    }

    /**
     * Perbarui seluruh data Transkrip, Biodata, SK Yudisium, Ijazah, Skripsi, dan Pejabat dalam 1 Form di halaman Detail Transkrip.
     */
    public function updateComplete(Request $request, Transkrip $transkrip): RedirectResponse
    {
        $user = Auth::user();
        $mhs = $transkrip->mahasiswa;

        if ($user->isAdminFakultas() && $mhs && ! $user->canAccessMahasiswa($mhs)) {
            abort(403, 'Akses ditolak. Anda tidak berhak mengubah data transkrip di luar fakultas Anda.');
        }

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'tempat_lahir' => ['nullable', 'string', 'max:150'],
            'tanggal_lahir' => ['nullable', 'date'],
            'no_sk' => ['nullable', 'string', 'max:150'],
            'tanggal_sk' => ['nullable', 'date'],
            'no_ijazah' => ['nullable', 'string', 'max:150'],
            'tanggal_ijazah' => ['nullable', 'date'],
            'periode_wisuda' => ['nullable', 'string', 'max:100'],
            'tanggal_wisuda' => ['nullable', 'date'],
            'gelar' => ['nullable', 'string', 'max:50'],
            'gelar_full' => ['nullable', 'string', 'max:150'],
            'judul_skripsi' => ['nullable', 'string', 'max:1000'],
            'predikat' => ['nullable', 'string', 'max:100'],
            'nomor_transkrip' => ['nullable', 'string', 'max:150'],
            'no_sk_banpt_override' => ['nullable', 'string', 'max:150'],
            'jabatan_dekan_override' => ['nullable', 'string', 'max:100'],
            'nama_dekan_override' => ['nullable', 'string', 'max:255'],
            'nip_dekan_override' => ['nullable', 'string', 'max:50'],
            'kode_romawi' => ['nullable', 'string', 'max:30'],
            'update_master_pejabat' => ['nullable', 'boolean'],
        ]);

        // 1. Update Biodata Mahasiswa
        $mhs->update([
            'nama' => $validated['nama'],
            'tempat_lahir' => $validated['tempat_lahir'] ?? null,
            'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
            'status' => 'Lulus',
        ]);

        // 2. Update atau Buat Data Wisudawan (SK Yudisium, Ijazah/PIN, Skripsi, Tanggal Lulus)
        $wisudawan = $transkrip->wisudawan ?: Wisudawan::firstOrNew(['nim' => $mhs->nim]);
        $wisudawan->mahasiswa_id = $mhs->id;
        $wisudawan->nim = $mhs->nim;
        $wisudawan->nama = $mhs->nama;
        $wisudawan->fakultas = $mhs->fakultas?->nama_fakultas ?: $wisudawan->fakultas;
        $wisudawan->program_studi = $mhs->programStudi?->nama_program_studi ?: $wisudawan->program_studi;
        $wisudawan->periode_wisuda = ($validated['periode_wisuda'] ?? null) ?: ($wisudawan->periode_wisuda ?: ('Wisuda Ganjil '.date('Y')));
        $wisudawan->tanggal_wisuda = ($validated['tanggal_wisuda'] ?? null) ?: (($validated['tanggal_sk'] ?? null) ?: $wisudawan->tanggal_wisuda);
        $wisudawan->no_sk = $validated['no_sk'] ?? null;
        $wisudawan->tanggal_sk = $validated['tanggal_sk'] ?? null;
        $wisudawan->no_ijazah = $validated['no_ijazah'] ?? null;
        $wisudawan->tanggal_ijazah = ($validated['tanggal_ijazah'] ?? null) ?: ($validated['tanggal_sk'] ?? null);
        $wisudawan->gelar = ($validated['gelar'] ?? null) ?: ($wisudawan->gelar ?: $mhs->programStudi?->gelar_akademik);
        $wisudawan->gelar_full = ($validated['gelar_full'] ?? null) ?: ($wisudawan->gelar_full ?: $mhs->programStudi?->gelar_akademik_full);
        $wisudawan->judul_skripsi = $validated['judul_skripsi'] ?? null;
        $wisudawan->predikat_kelulusan = ($validated['predikat'] ?? null) ?: $transkrip->predikat;
        $wisudawan->ipk = $transkrip->ipk;
        $wisudawan->total_sks = $transkrip->total_sks;
        $wisudawan->is_eligible = true;
        if (! $wisudawan->exists) {
            $wisudawan->nomor_pendaftaran_wisuda = 'WIS-'.date('Y').'-'.substr($mhs->nim, -4);
            $wisudawan->status_validasi = $transkrip->status_validasi ?: 'Menunggu Validasi';
            $wisudawan->sumber_data = 'INPUT_MANUAL_TERPADU';
        }
        $wisudawan->save();

        // 3. Update Data Transkrip & Pejabat Penandatangan
        $transkrip->update([
            'wisudawan_id' => $wisudawan->id,
            'predikat' => ($validated['predikat'] ?? null) ?: $transkrip->predikat,
            'nomor_transkrip' => ($validated['nomor_transkrip'] ?? null) ?: null,
            'no_sk_banpt_override' => ($validated['no_sk_banpt_override'] ?? null) ?: null,
            'jabatan_dekan_override' => ($validated['jabatan_dekan_override'] ?? null) ?: null,
            'nama_dekan_override' => ($validated['nama_dekan_override'] ?? null) ?: null,
            'nip_dekan_override' => ($validated['nip_dekan_override'] ?? null) ?: null,
        ]);

        // 4. Jika dicentang: Simpan juga sebagai Pejabat Dekan & SK Akreditasi default Fakultas / Prodi
        if ($request->boolean('update_master_pejabat')) {
            $fakultasObj = $mhs->fakultas ?: $mhs->programStudi?->fakultas;
            if ($fakultasObj) {
                $fakultasObj->update(array_filter([
                    'kode_romawi' => ($validated['kode_romawi'] ?? null) ?: $fakultasObj->kode_romawi,
                    'jabatan_dekan' => ($validated['jabatan_dekan_override'] ?? null) ?: $fakultasObj->jabatan_dekan,
                    'nama_dekan' => ($validated['nama_dekan_override'] ?? null) ?: $fakultasObj->nama_dekan,
                    'nip_dekan' => ($validated['nip_dekan_override'] ?? null) ?: $fakultasObj->nip_dekan,
                    'no_sk_banpt' => ($validated['no_sk_banpt_override'] ?? null) ?: $fakultasObj->no_sk_banpt,
                ]));
            }
            if ($mhs->programStudi && ! empty($validated['no_sk_banpt_override'])) {
                $mhs->programStudi->update([
                    'no_sk_akreditasi' => $validated['no_sk_banpt_override'],
                ]);
            }
        }

        AuditLog::record(
            'Pembaruan Terpadu Data Transkrip & Kelulusan',
            'Transkrip',
            (string) $transkrip->id,
            "{$user->name} memperbarui data lengkap transkrip, SK Yudisium ({$wisudawan->no_sk}), Ijazah ({$wisudawan->no_ijazah}), dan Pejabat pada NIM {$mhs->nim}."
        );

        return redirect()->back()->with('success', 'Seluruh data Transkrip, SK Yudisium, Nomor Ijazah, Judul Skripsi, dan Pejabat Penandatangan berhasil diperbarui.');
    }

    public function validateTranskrip(Request $request, Transkrip $transkrip): RedirectResponse
    {
        $user = Auth::user();
        if ($user->isAdminFakultas() && $transkrip->mahasiswa && ! $user->canAccessMahasiswa($transkrip->mahasiswa)) {
            abort(403, 'Akses ditolak. Anda tidak berhak memvalidasi transkrip di luar fakultas Anda.');
        }

        $validated = $request->validate([
            'status_validasi' => ['required', 'string', 'in:menunggu_pengesahan,terverifikasi,perlu_perbaikan,ditolak,disahkan,diterbitkan,Valid,Menunggu Validasi,Draft'],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ]);

        $statusBaru = $validated['status_validasi'];
        $catatan = $validated['catatan'] ?? null;

        // If Admin Jurusan verifies and approves, map to 'menunggu_pengesahan'
        if ($user->isAdminFakultas() && in_array($statusBaru, ['Valid', 'terverifikasi', 'menunggu_pengesahan'])) {
            $statusBaru = 'menunggu_pengesahan';
            $transkrip->verified_by_jurusan_id = $user->id;
            $transkrip->verified_by_jurusan_at = now();
        }

        // If Super Admin approves directly
        if ($user->isSuperAdmin() && in_array($statusBaru, ['Valid', 'disahkan', 'diterbitkan'])) {
            $statusBaru = 'disahkan';
            $transkrip->ratified_by_admin_id = $user->id;
            $transkrip->ratified_by_admin_at = now();
        }

        $transkrip->recordStatusChange($user->id, $statusBaru, $catatan);

        if ($transkrip->wisudawan) {
            $transkrip->wisudawan->update(['status_validasi' => $statusBaru]);
        }

        AuditLog::record(
            'Verifikasi / Perubahan Status Transkrip',
            'Transkrip',
            (string) $transkrip->id,
            "{$user->name} mengubah status transkrip NIM {$transkrip->mahasiswa->nim} menjadi: {$statusBaru}."
        );

        $msg = $statusBaru === 'menunggu_pengesahan'
            ? 'Transkrip berhasil diverifikasi dan diteruskan ke Admin Sistem untuk pengesahan akhir.'
            : ($statusBaru === 'disahkan' ? 'Transkrip berhasil disahkan dan resmi diterbitkan.' : "Status transkrip diperbarui menjadi {$statusBaru}.");

        return redirect()->back()->with('success', $msg);
    }

    public function downloadPdf(Request $request, Transkrip $transkrip, SiakadApiService $siakadApi): Response
    {
        $user = Auth::user();

        // Backend security authorization check
        if ($user->isMahasiswa()) {
            if ($transkrip->mahasiswa->user_id !== $user->id) {
                abort(403, 'Akses tidak sah. Anda hanya dapat mengunduh transkrip akademik milik Anda sendiri.');
            }
        } elseif ($user->isAdminFakultas() && $transkrip->mahasiswa && ! $user->canAccessMahasiswa($transkrip->mahasiswa)) {
            abort(403, 'Akses ditolak. Anda hanya dapat mengunduh transkrip mahasiswa di fakultas Anda.');
        }

        if ((int) $transkrip->total_sks === 0 && $transkrip->mahasiswa) {
            $siakadApi->syncStudentTranscript($transkrip->mahasiswa);
            $transkrip->refresh();
        }

        if ($transkrip->mahasiswa) {
            $transkrip = $transkrip->mahasiswa->recalculateTranskrip();
        }

        $transkrip->load([
            'mahasiswa.fakultas',
            'mahasiswa.programStudi',
            'mahasiswa.siakadNilais.mataKuliah',
            'wisudawan',
            'verifiedByJurusan',
            'ratifiedByAdmin',
        ]);

        $mhs = $transkrip->mahasiswa;
        $isWisudawan = ($mhs->status === 'Lulus' || $transkrip->wisudawan !== null);

        // Mode cetak: 'final' (siap cetak format Pustikom 20251205141934) atau 'draft' (format draft transkrip Pustikom)
        $mode = $request->query('mode', 'final');
        $isDraftMode = ($mode === 'draft');

        $verificationUrl = $this->getVerificationUrl($transkrip->kode_verifikasi);
        $qrCodeSvg = base64_encode(QrCode::format('svg')->size(90)->margin(0)->generate($verificationUrl));

        $pdf = Pdf::loadView('pdf.transkrip', [
            'transkrip' => $transkrip,
            'qrCodeSvg' => $qrCodeSvg,
            'verificationUrl' => $verificationUrl,
            'isWisudawan' => $isWisudawan,
            'isDraftMode' => $isDraftMode,
        ])->setPaper('a4', 'portrait');

        $docType = $isDraftMode ? 'Draft_Transkrip_' : 'Transkrip_Akademik_';

        AuditLog::record(
            'Cetak / Unduh PDF Transkrip',
            'Transkrip',
            (string) $transkrip->id,
            'Mencetak dokumen '.($isDraftMode ? 'draft transkrip' : 'transkrip siap cetak').' NIM: '.$mhs->nim
        );

        return $pdf->stream($docType.$mhs->nim.'.pdf');
    }

    /**
     * Build QR code verification URL replacing localhost/127.0.0.1 with local Wi-Fi / LAN IP.
     */
    protected function getVerificationUrl(string $kodeVerifikasi): string
    {
        $url = route('verifikasi.transkrip', $kodeVerifikasi);
        $lanHost = env('QR_LAN_HOST', '192.168.1.34:8000');

        if (str_contains($url, '127.0.0.1:8000') || str_contains($url, 'localhost:8000')) {
            $url = str_replace(['127.0.0.1:8000', 'localhost:8000'], $lanHost, $url);
        }

        return $url;
    }
}
