<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Mahasiswa;
use App\Models\SiakadSyncLog;
use App\Models\Transkrip;
use App\Models\Wisudawan;
use App\Services\SiakadApiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class WisudawanController extends Controller
{
    public function index(Request $request, SiakadApiService $siakadApi): View
    {
        $siakadApi->ensureCleanOfficialDataset();

        $user = Auth::user();
        $query = Wisudawan::with(['mahasiswa.programStudi', 'mahasiswa.fakultas', 'transkrip']);

        // Scope to faculty for Admin Fakultas
        if ($user->isAdminFakultas() && $user->fakultas_id) {
            $query->where(function ($q) use ($user) {
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

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nim', 'like', "%{$search}%")
                    ->orWhere('nama', 'like', "%{$search}%")
                    ->orWhere('no_sk', 'like', "%{$search}%")
                    ->orWhere('no_ijazah', 'like', "%{$search}%")
                    ->orWhere('judul_skripsi', 'like', "%{$search}%")
                    ->orWhere('nomor_pendaftaran_wisuda', 'like', "%{$search}%")
                    ->orWhereHas('mahasiswa', function ($m) use ($search) {
                        $m->where('nama', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('periode')) {
            $query->where('periode_wisuda', $request->periode);
        }

        if ($request->filled('status')) {
            $query->where('status_validasi', $request->status);
        }

        $wisudawans = $query->latest()->paginate(10)->withQueryString();

        $periodesQuery = Wisudawan::select('periode_wisuda')->distinct();
        if ($user->isAdminFakultas() && $user->fakultas_id) {
            $periodesQuery->where(function ($q) use ($user) {
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
        $periodes = $periodesQuery->pluck('periode_wisuda');

        // Sample NIMs from faculty for quick E-Wisuda API lookup
        $mhsSampleQuery = Mahasiswa::query();
        if ($user->isAdminFakultas() && $user->fakultas_id) {
            if ((int) $user->fakultas_id === 1) {
                $mhsSampleQuery->where(function ($q) {
                    $q->where('fakultas_id', 1)->orWhere('program_studi_id', 1);
                });
            } else {
                $mhsSampleQuery->where('fakultas_id', $user->fakultas_id);
            }
        }
        $defaultSyncNims = $mhsSampleQuery->whereHas('siakadNilais')->take(5)->pluck('nim')->toArray();
        if (empty($defaultSyncNims)) {
            $defaultSyncNims = $mhsSampleQuery->take(5)->pluck('nim')->toArray();
        }
        if ($user->isSuperAdmin() || (int) $user->fakultas_id === 1) {
            array_unshift($defaultSyncNims, '2281050039');
            $defaultSyncNims = array_values(array_unique($defaultSyncNims));
        }
        $defaultSyncNimsString = implode(',', $defaultSyncNims ?: ['2281050039', '2388010002', '2388010003']);

        $ewisudaStatus = $siakadApi->testEwisudaConnection($defaultSyncNims[0] ?? '2281050039');

        return view('admin.wisudawan.index', compact('wisudawans', 'periodes', 'ewisudaStatus', 'defaultSyncNimsString'));
    }

    /**
     * Sinkronisasi Data Administrasi Lulusan dari E-Wisuda API (/api/administrative?nims=...)
     */
    public function syncEwisuda(Request $request, SiakadApiService $siakadApi): RedirectResponse
    {
        $user = Auth::user();

        $request->validate([
            'nims' => ['nullable', 'string', 'max:2000'],
            'ewisuda_api_key' => ['nullable', 'string', 'max:255'],
            'periode_wisuda' => ['nullable', 'string', 'max:100'],
        ]);

        $nimsInput = trim((string) $request->input('nims', ''));
        $customApiKey = $request->filled('ewisuda_api_key') ? trim((string) $request->input('ewisuda_api_key')) : null;
        $periodeWisuda = $request->filled('periode_wisuda') ? trim((string) $request->input('periode_wisuda')) : ('Wisuda Ganjil '.date('Y'));

        if ($nimsInput === '') {
            // Jika input NIM dikosongkan, ambil daftar NIM mahasiswa yang sudah memiliki nilai/transkrip di fakultas terkait
            $mhsQuery = Mahasiswa::query();
            if ($user->isAdminFakultas() && $user->fakultas_id) {
                if ((int) $user->fakultas_id === 1) {
                    $mhsQuery->where(function ($q) {
                        $q->where('fakultas_id', 1)->orWhere('program_studi_id', 1);
                    });
                } else {
                    $mhsQuery->where('fakultas_id', $user->fakultas_id);
                }
            }

            $autoNims = $mhsQuery->whereHas('siakadNilais')->take(10)->pluck('nim')->toArray();
            if (empty($autoNims)) {
                $autoNims = $mhsQuery->take(10)->pluck('nim')->toArray();
            }
            if ($user->isSuperAdmin() || (int) $user->fakultas_id === 1) {
                array_unshift($autoNims, '2281050039');
                $autoNims = array_values(array_unique($autoNims));
            }
            $nimsInput = implode(',', $autoNims);
        }

        // Validasi hak akses fakultas terhadap NIM yang diminta
        if ($user->isAdminFakultas() && $user->fakultas_id) {
            $requestedNims = array_values(array_filter(array_map('trim', preg_split('/[\s,;]+/', $nimsInput) ?: [])));
            $localMatches = Mahasiswa::whereIn('nim', $requestedNims)->get();
            foreach ($localMatches as $mhs) {
                if (! $user->canAccessMahasiswa($mhs)) {
                    return redirect()->route('admin.wisudawan.index')
                        ->with('error', "Akses ditolak: NIM {$mhs->nim} ({$mhs->nama}) berada di luar fakultas Anda.");
                }
            }
        }

        $result = $siakadApi->syncWisudawanFromEwisuda($nimsInput, $customApiKey, true, $periodeWisuda);

        SiakadSyncLog::create([
            'user_id' => $user->id,
            'fakultas_id' => $user->fakultas_id,
            'endpoint' => config('services.ewisuda.base_url').'/api/administrative?nims='.$nimsInput,
            'status' => $result['success'] ? 'sukses' : 'gagal',
            'total_berhasil' => $result['created'] ?? 0,
            'total_gagal' => $result['success'] ? 0 : 1,
            'total_diperbarui' => $result['updated'] ?? 0,
            'total_mahasiswa' => $result['total'] ?? 0,
            'keterangan' => $result['message'],
            'pesan_error' => $result['success'] ? null : $result['message'],
        ]);

        AuditLog::record(
            'Sinkronisasi API E-Wisuda',
            'Wisudawan',
            'EWISUDA_API',
            "{$user->name} menyinkronkan data administrasi lulusan dari E-Wisuda API ({$result['message']})."
        );

        if ($result['success']) {
            return redirect()->route('admin.wisudawan.index')->with('success', $result['message']);
        }

        return redirect()->route('admin.wisudawan.index')->with('error', $result['message']);
    }

    public function create(): View
    {
        $user = Auth::user();
        $mhsQuery = Mahasiswa::whereDoesntHave('wisudawan')->orderBy('nim');

        if ($user->isAdminFakultas() && $user->fakultas_id) {
            if ((int) $user->fakultas_id === 1) {
                $mhsQuery->where(function ($q) {
                    $q->where('fakultas_id', 1)->orWhere('program_studi_id', 1);
                });
            } else {
                $mhsQuery->where('fakultas_id', $user->fakultas_id);
            }
        }

        $mahasiswas = $mhsQuery->get();

        return view('admin.wisudawan.create', compact('mahasiswas'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'nim' => ['required', 'string', 'max:50', 'unique:wisudawans,nim'],
            'nomor_pendaftaran_wisuda' => ['nullable', 'string', 'max:100'],
            'periode_wisuda' => ['required', 'string', 'max:100'],
            'tanggal_wisuda' => ['nullable', 'date'],
            'no_sk' => ['nullable', 'string', 'max:100'],
            'tanggal_sk' => ['nullable', 'date'],
            'no_ijazah' => ['nullable', 'string', 'max:100'],
            'tanggal_ijazah' => ['nullable', 'date'],
            'ipk' => ['nullable', 'numeric', 'min:0', 'max:4'],
            'total_sks' => ['nullable', 'integer', 'min:0', 'max:300'],
            'lama_studi' => ['nullable', 'integer', 'min:1', 'max:20'],
            'semester_lulus' => ['nullable', 'string', 'max:30'],
            'gelar' => ['nullable', 'string', 'max:50'],
            'gelar_full' => ['nullable', 'string', 'max:150'],
            'judul_skripsi' => ['nullable', 'string', 'max:500'],
            'predikat_kelulusan' => ['nullable', 'string', 'max:50'],
            'is_eligible' => ['nullable', 'boolean'],
            'status_validasi' => ['required', 'in:Draft,Menunggu Validasi,Valid,Ditolak'],
        ]);

        $mhs = Mahasiswa::where('nim', $validated['nim'])->first();
        if ($user->isAdminFakultas() && $mhs && ! $user->canAccessMahasiswa($mhs)) {
            abort(403, 'Akses ditolak. Mahasiswa tidak berada di fakultas Anda.');
        }

        if ($mhs) {
            $validated['mahasiswa_id'] = $mhs->id;
            $validated['nama'] = $mhs->nama;
            $validated['fakultas'] = $mhs->fakultas?->nama_fakultas;
            $validated['program_studi'] = $mhs->programStudi?->nama_program_studi;
            $validated['gelar'] = $validated['gelar'] ?? $mhs->programStudi?->gelar_akademik;
            $validated['gelar_full'] = $validated['gelar_full'] ?? $mhs->programStudi?->gelar_akademik_full;
        }
        $validated['is_eligible'] = $request->boolean('is_eligible', true);
        $validated['sumber_data'] = 'INPUT_MANUAL';

        $wisudawan = Wisudawan::create($validated);

        if ($mhs) {
            if ($request->filled('tempat_lahir') || $request->filled('tanggal_lahir')) {
                $mhs->update(array_filter([
                    'tempat_lahir' => $request->input('tempat_lahir'),
                    'tanggal_lahir' => $request->input('tanggal_lahir'),
                    'status' => 'Lulus',
                ]));
            }
            $this->syncTranskripFromWisudawan($mhs, $wisudawan, $request);
        }

        AuditLog::record('Menambah Data Wisudawan', 'Wisudawan', (string) $wisudawan->id, "Menambahkan wisudawan NIM: {$wisudawan->nim} Periode: {$wisudawan->periode_wisuda}");

        return redirect()->route('admin.wisudawan.index')->with('success', 'Data wisudawan dan berkas transkrip otomatis berhasil disimpan.');
    }

    public function edit(Wisudawan $wisudawan): View
    {
        $user = Auth::user();
        if ($user->isAdminFakultas() && $wisudawan->mahasiswa && ! $user->canAccessMahasiswa($wisudawan->mahasiswa)) {
            abort(403, 'Akses ditolak. Wisudawan tidak berada di fakultas Anda.');
        }

        $wisudawan->load(['mahasiswa.fakultas', 'mahasiswa.programStudi', 'transkrip']);

        return view('admin.wisudawan.edit', compact('wisudawan'));
    }

    public function update(Request $request, Wisudawan $wisudawan): RedirectResponse
    {
        $user = Auth::user();
        if ($user->isAdminFakultas() && $wisudawan->mahasiswa && ! $user->canAccessMahasiswa($wisudawan->mahasiswa)) {
            abort(403, 'Akses ditolak. Wisudawan tidak berada di fakultas Anda.');
        }

        $validated = $request->validate([
            'nim' => ['required', 'string', 'max:50', 'unique:wisudawans,nim,'.$wisudawan->id],
            'nomor_pendaftaran_wisuda' => ['nullable', 'string', 'max:100'],
            'periode_wisuda' => ['required', 'string', 'max:100'],
            'tanggal_wisuda' => ['nullable', 'date'],
            'no_sk' => ['nullable', 'string', 'max:100'],
            'tanggal_sk' => ['nullable', 'date'],
            'no_ijazah' => ['nullable', 'string', 'max:100'],
            'tanggal_ijazah' => ['nullable', 'date'],
            'ipk' => ['nullable', 'numeric', 'min:0', 'max:4'],
            'total_sks' => ['nullable', 'integer', 'min:0', 'max:300'],
            'lama_studi' => ['nullable', 'integer', 'min:1', 'max:20'],
            'semester_lulus' => ['nullable', 'string', 'max:30'],
            'gelar' => ['nullable', 'string', 'max:50'],
            'gelar_full' => ['nullable', 'string', 'max:150'],
            'judul_skripsi' => ['nullable', 'string', 'max:500'],
            'predikat_kelulusan' => ['nullable', 'string', 'max:50'],
            'is_eligible' => ['nullable', 'boolean'],
            'status_validasi' => ['required', 'in:Draft,Menunggu Validasi,Valid,Ditolak'],
        ]);

        $mhs = Mahasiswa::where('nim', $validated['nim'])->first();
        if ($user->isAdminFakultas() && $mhs && ! $user->canAccessMahasiswa($mhs)) {
            abort(403, 'Akses ditolak. Mahasiswa tidak berada di fakultas Anda.');
        }

        if ($mhs) {
            $validated['mahasiswa_id'] = $mhs->id;
            $validated['nama'] = $mhs->nama;
            $validated['fakultas'] = $mhs->fakultas?->nama_fakultas;
            $validated['program_studi'] = $mhs->programStudi?->nama_program_studi;
        }
        $validated['is_eligible'] = $request->boolean('is_eligible', true);

        $wisudawan->update($validated);

        if ($mhs) {
            if ($request->filled('tempat_lahir') || $request->filled('tanggal_lahir')) {
                $mhs->update(array_filter([
                    'tempat_lahir' => $request->input('tempat_lahir'),
                    'tanggal_lahir' => $request->input('tanggal_lahir'),
                ]));
            }
            $this->syncTranskripFromWisudawan($mhs, $wisudawan, $request);
        }

        AuditLog::record('Memperbarui Data Wisudawan', 'Wisudawan', (string) $wisudawan->id, "Memperbarui data wisudawan NIM: {$wisudawan->nim}");

        return redirect()->route('admin.wisudawan.index')->with('success', 'Data wisudawan dan transkrip terkait berhasil diperbarui.');
    }

    /**
     * Sinkronkan otomatis data Wisudawan ke tabel Transkrip agar tidak terpisah-pisah.
     */
    protected function syncTranskripFromWisudawan(Mahasiswa $mhs, Wisudawan $wisudawan, Request $request): void
    {
        $nilais = $mhs->siakadNilais;
        $totalSks = 0;
        $totalMutu = 0.0;

        foreach ($nilais as $n) {
            $totalSks += $n->sks;
            $totalMutu += ($n->bobot * $n->sks);
        }

        if ($totalSks === 0 && $wisudawan->total_sks) {
            $totalSks = (int) $wisudawan->total_sks;
            $ipk = (float) ($wisudawan->ipk ?: 0);
            $totalMutu = round($totalSks * $ipk, 2);
        } else {
            $ipk = $totalSks > 0 ? round($totalMutu / $totalSks, 2) : (float) ($wisudawan->ipk ?: 0);
        }

        $predikat = $wisudawan->predikat_kelulusan;
        if (! $predikat) {
            if ($ipk >= 3.51) {
                $predikat = 'Dengan Pujian (Cum Laude)';
            } elseif ($ipk >= 3.01) {
                $predikat = 'Sangat Memuaskan';
            } elseif ($ipk >= 2.76) {
                $predikat = 'Memuaskan';
            } else {
                $predikat = 'Cukup';
            }
        }

        $transkrip = Transkrip::firstOrNew(['mahasiswa_id' => $mhs->id]);
        $transkrip->wisudawan_id = $wisudawan->id;
        $transkrip->total_sks = $totalSks;
        $transkrip->total_mutu = $totalMutu;
        $transkrip->ipk = $ipk;
        $transkrip->predikat = $predikat;

        if ($request->has('nomor_transkrip')) {
            $transkrip->nomor_transkrip = $request->input('nomor_transkrip') ?: null;
        }
        if ($request->has('no_sk_banpt_override')) {
            $transkrip->no_sk_banpt_override = $request->input('no_sk_banpt_override') ?: null;
        }

        if (empty($transkrip->kode_verifikasi)) {
            $transkrip->kode_verifikasi = 'UINSSC-TRX-'.$mhs->nim.'-'.strtoupper(Str::random(6));
        }

        if (! $transkrip->exists) {
            $transkrip->status_validasi = $wisudawan->status_validasi ?: 'Menunggu Validasi';
        }

        $transkrip->save();
    }

    public function destroy(Wisudawan $wisudawan): RedirectResponse
    {
        $user = Auth::user();
        if ($user->isAdminFakultas() && $wisudawan->mahasiswa && ! $user->canAccessMahasiswa($wisudawan->mahasiswa)) {
            abort(403, 'Akses ditolak. Wisudawan tidak berada di fakultas Anda.');
        }

        $nim = $wisudawan->nim;
        $id = $wisudawan->id;
        $wisudawan->delete();

        AuditLog::record('Menghapus Data Wisudawan', 'Wisudawan', (string) $id, "Menghapus data wisudawan NIM: {$nim}");

        return redirect()->route('admin.wisudawan.index')->with('success', 'Data wisudawan berhasil dihapus.');
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle, 1000, ',');

        $imported = 0;
        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            if (count($row) < 3) {
                continue;
            }

            $nim = trim($row[0]);
            $periode = trim($row[1]);
            $judul = trim($row[2] ?? '');
            $predikat = trim($row[3] ?? 'Memuaskan');
            $noDaftar = trim($row[4] ?? '');

            $mhs = Mahasiswa::where('nim', $nim)->first();

            Wisudawan::updateOrCreate(
                ['nim' => $nim],
                [
                    'mahasiswa_id' => $mhs?->id,
                    'periode_wisuda' => $periode,
                    'judul_skripsi' => $judul,
                    'predikat_kelulusan' => $predikat,
                    'nomor_pendaftaran_wisuda' => $noDaftar,
                    'fakultas' => $mhs?->fakultas?->nama_fakultas,
                    'program_studi' => $mhs?->programStudi?->nama_program_studi,
                    'status_validasi' => 'Menunggu Validasi',
                    'sumber_data' => 'IMPORT_WISUDAWAN',
                ]
            );
            $imported++;
        }
        fclose($handle);

        AuditLog::record('Import Data Wisudawan', 'Wisudawan', null, "Berhasil mengimpor {$imported} data wisudawan dari file CSV.");

        return redirect()->route('admin.wisudawan.index')->with('success', "Berhasil mengimpor {$imported} data wisudawan.");
    }

    public function downloadTemplate(): Response
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_wisudawan.csv"',
        ];

        $columns = ['NIM', 'Periode_Wisuda', 'Judul_Skripsi', 'Predikat_Kelulusan', 'Nomor_Pendaftaran'];
        $example = ['220001', 'Ganjil 2026', 'Analisis Penerapan Algoritma Deep Learning', 'Pujian', 'WIS-2026-001'];

        $callback = function () use ($columns, $example) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, $example);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
