<?php

namespace App\Http\Controllers;

use App\Models\AturanBobotNilai;
use App\Models\AuditLog;
use App\Models\Mahasiswa;
use App\Models\NilaiChangeHistory;
use App\Models\ProgramStudi;
use App\Models\SiakadMataKuliah;
use App\Models\SiakadNilai;
use App\Models\Transkrip;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NilaiController extends Controller
{
    /**
     * Display list of grades with filter, source label (SIAKAD vs Manual), and conflict indicator.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $query = SiakadNilai::with(['mahasiswa.programStudi', 'mahasiswa.fakultas', 'mataKuliah', 'changeHistories.user']);

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
                $q->whereHas('mahasiswa', function ($m) use ($search) {
                    $m->where('nim', 'like', "%{$search}%")
                        ->orWhere('nama', 'like', "%{$search}%");
                })->orWhereHas('mataKuliah', function ($mk) use ($search) {
                    $mk->where('kode_mata_kuliah', 'like', "%{$search}%")
                        ->orWhere('nama_mata_kuliah', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }

        if ($request->filled('sumber_data')) {
            $query->where('sumber_data', $request->sumber_data);
        }

        if ($request->filled('has_conflict')) {
            $query->where('has_conflict', true);
        }

        $nilais = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total_nilai' => (clone $query)->count(),
            'total_siakad' => (clone $query)->where('sumber_data', 'SIAKAD')->count(),
            'total_manual' => (clone $query)->where('sumber_data', 'Manual')->count(),
            'total_konflik' => (clone $query)->where('has_conflict', true)->count(),
        ];

        return view('admin.nilai.index', compact('nilais', 'stats'));
    }

    /**
     * Show form for manual grade entry.
     */
    public function create(): View
    {
        $user = Auth::user();
        $mahasiswaQuery = Mahasiswa::orderBy('nama');

        if ($user->isAdminFakultas() && $user->fakultas_id) {
            if ((int) $user->fakultas_id === 1) {
                $mahasiswaQuery->where(function ($q) {
                    $q->where('fakultas_id', 1)->orWhere('program_studi_id', 1);
                });
            } else {
                $mahasiswaQuery->where('fakultas_id', $user->fakultas_id);
            }
        }

        $mahasiswas = $mahasiswaQuery->get();
        $mataKuliahs = SiakadMataKuliah::orderBy('nama_mata_kuliah')->get();
        $bobotList = AturanBobotNilai::orderByDesc('bobot')->get();

        return view('admin.nilai.create', compact('mahasiswas', 'mataKuliahs', 'bobotList'));
    }

    /**
     * Store single manual grade entry with conflict detection.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $mhs = Mahasiswa::findOrFail($request->mahasiswa_id);
        if ($user->isAdminFakultas() && ! $user->canAccessMahasiswa($mhs)) {
            abort(403, 'Akses ditolak. Mahasiswa tidak berada di fakultas Anda.');
        }

        $request->validate([
            'mahasiswa_id' => ['required', 'exists:mahasiswas,id'],
            'mata_kuliah_id' => ['required', 'exists:siakad_mata_kuliahs,id'],
            'semester' => ['required', 'string', 'max:10'],
            'nilai_huruf' => ['required', 'string', 'in:A,A-,B+,B,B-,C+,C,D,E'],
        ]);

        $mk = SiakadMataKuliah::findOrFail($request->mata_kuliah_id);
        $bobot = AturanBobotNilai::getBobot($request->nilai_huruf);

        // Check if grade already exists for this student and course
        $existing = SiakadNilai::where('mahasiswa_id', $request->mahasiswa_id)
            ->where('mata_kuliah_id', $request->mata_kuliah_id)
            ->first();

        if ($existing) {
            // If existing is from SIAKAD and differs from manual entry, flag conflict
            if ($existing->sumber_data === 'SIAKAD' && $existing->nilai_huruf !== $request->nilai_huruf) {
                $existing->update([
                    'has_conflict' => true,
                    'conflict_details' => "Konflik Data: Nilai SIAKAD adalah {$existing->nilai_huruf} ({$existing->bobot}), namun dicoba diinput manual sebagai {$request->nilai_huruf} ({$bobot}).",
                ]);

                AuditLog::record(
                    'Konflik Nilai Terdeteksi',
                    'SiakadNilai',
                    (string) $existing->id,
                    "Konflik nilai pada NIM {$existing->mahasiswa->nim} MK {$mk->nama_mata_kuliah}: SIAKAD ({$existing->nilai_huruf}) vs Manual ({$request->nilai_huruf})"
                );

                return redirect()->route('admin.nilai.index')->with('warning', 'Terdeteksi konflik data antara SIAKAD dan Manual. Nilai lama tidak ditimpa secara otomatis.');
            }
        }

        $nilai = SiakadNilai::updateOrCreate(
            [
                'mahasiswa_id' => $request->mahasiswa_id,
                'mata_kuliah_id' => $request->mata_kuliah_id,
            ],
            [
                'semester' => $request->semester,
                'nilai_huruf' => $request->nilai_huruf,
                'nilai_angka' => $bobot,
                'bobot' => $bobot,
                'sks' => $mk->sks,
                'sumber_data' => 'Manual',
                'has_conflict' => false,
                'conflict_details' => null,
                'imported_at' => now(),
            ]
        );

        $this->recalculateTranscript($nilai->mahasiswa_id);

        AuditLog::record(
            'Input Nilai Manual',
            'SiakadNilai',
            (string) $nilai->id,
            "Input nilai manual untuk NIM {$nilai->mahasiswa->nim}: MK {$mk->nama_mata_kuliah} = {$request->nilai_huruf}"
        );

        return redirect()->route('admin.nilai.index')->with('success', 'Nilai manual berhasil disimpan.');
    }

    /**
     * Show form for mass/bulk grade entry.
     */
    public function massCreate(Request $request): View
    {
        $user = Auth::user();
        $prodiQuery = ProgramStudi::orderBy('nama_program_studi');
        if ($user->isAdminFakultas() && $user->fakultas_id) {
            if ((int) $user->fakultas_id === 1) {
                $prodiQuery->where(function ($q) {
                    $q->where('fakultas_id', 1)->orWhere('id', 1);
                });
            } else {
                $prodiQuery->where('fakultas_id', $user->fakultas_id);
            }
        }

        $programStudis = $prodiQuery->get();
        $mataKuliahs = SiakadMataKuliah::orderBy('nama_mata_kuliah')->get();

        $selectedProdi = $request->program_studi_id;
        $selectedMk = $request->mata_kuliah_id;
        $selectedSemester = $request->semester ?? '1';

        $mahasiswas = collect();
        if ($selectedProdi) {
            $mahasiswas = Mahasiswa::where('programStudi_id', $selectedProdi)
                ->orderBy('nim')
                ->get();
        }

        return view('admin.nilai.mass', compact('programStudis', 'mataKuliahs', 'mahasiswas', 'selectedProdi', 'selectedMk', 'selectedSemester'));
    }

    /**
     * Store mass/bulk grade entry.
     */
    public function massStore(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if ($user->isAdminFakultas() && is_array($request->grades)) {
            $mhsIds = array_keys($request->grades);
            $invalidStudents = Mahasiswa::whereIn('id', $mhsIds)->get()
                ->reject(fn ($m) => $user->canAccessMahasiswa($m));
            if ($invalidStudents->isNotEmpty()) {
                abort(403, 'Akses ditolak. Beberapa mahasiswa berada di luar fakultas Anda.');
            }
        }

        $request->validate([
            'mata_kuliah_id' => ['required', 'exists:siakad_mata_kuliahs,id'],
            'semester' => ['required', 'string'],
            'grades' => ['required', 'array'],
        ]);

        $mk = SiakadMataKuliah::findOrFail($request->mata_kuliah_id);
        $savedCount = 0;

        foreach ($request->grades as $mhsId => $grade) {
            if (empty($grade) || ! in_array($grade, ['A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'D', 'E'], true)) {
                continue;
            }

            $bobot = AturanBobotNilai::getBobot($grade);

            SiakadNilai::updateOrCreate(
                [
                    'mahasiswa_id' => $mhsId,
                    'mata_kuliah_id' => $mk->id,
                ],
                [
                    'semester' => $request->semester,
                    'nilai_huruf' => $grade,
                    'nilai_angka' => $bobot,
                    'bobot' => $bobot,
                    'sks' => $mk->sks,
                    'sumber_data' => 'Manual',
                    'imported_at' => now(),
                ]
            );

            $this->recalculateTranscript($mhsId);
            $savedCount++;
        }

        AuditLog::record(
            'Input Nilai Massal',
            'SiakadNilai',
            (string) $mk->id,
            "Input nilai massal berhasil untuk {$savedCount} mahasiswa pada mata kuliah {$mk->nama_mata_kuliah}"
        );

        return redirect()->route('admin.nilai.index')->with('success', "Berhasil menyimpan {$savedCount} nilai secara massal.");
    }

    /**
     * Edit a grade and record change history.
     */
    public function update(Request $request, SiakadNilai $nilai): RedirectResponse
    {
        $user = Auth::user();
        if ($user->isAdminFakultas() && $nilai->mahasiswa && ! $user->canAccessMahasiswa($nilai->mahasiswa)) {
            abort(403, 'Akses ditolak. Mahasiswa tidak berada di fakultas Anda.');
        }

        $request->validate([
            'nilai_huruf' => ['required', 'string', 'in:A,A-,B+,B,B-,C+,C,D,E'],
            'alasan' => ['required', 'string', 'max:500'],
        ]);

        $oldHuruf = $nilai->nilai_huruf;
        $oldBobot = $nilai->bobot;
        $newBobot = AturanBobotNilai::getBobot($request->nilai_huruf);

        // Record history
        NilaiChangeHistory::create([
            'siakad_nilai_id' => $nilai->id,
            'user_id' => Auth::id(),
            'nilai_huruf_lama' => $oldHuruf,
            'bobot_lama' => $oldBobot,
            'nilai_huruf_baru' => $request->nilai_huruf,
            'bobot_baru' => $newBobot,
            'alasan' => $request->alasan,
        ]);

        $nilai->update([
            'nilai_huruf' => $request->nilai_huruf,
            'nilai_angka' => $newBobot,
            'bobot' => $newBobot,
            'has_conflict' => false,
            'conflict_details' => null,
        ]);

        $this->recalculateTranscript($nilai->mahasiswa_id);

        AuditLog::record(
            'Perubahan Nilai Mahasiswa',
            'SiakadNilai',
            (string) $nilai->id,
            "Mengubah nilai NIM {$nilai->mahasiswa->nim} MK {$nilai->mataKuliah->nama_mata_kuliah} dari {$oldHuruf} ({$oldBobot}) menjadi {$request->nilai_huruf} ({$newBobot}). Alasan: {$request->alasan}"
        );

        return back()->with('success', 'Nilai berhasil diperbarui dan dicatat dalam riwayat perubahan.');
    }

    /**
     * Helper to recalculate transcript totals & IPK for a student.
     */
    protected function recalculateTranscript(int $mahasiswaId): void
    {
        $nilais = SiakadNilai::where('mahasiswa_id', $mahasiswaId)->get();
        $totalSks = 0;
        $totalMutu = 0.0;

        foreach ($nilais as $n) {
            $bobot = AturanBobotNilai::getBobot($n->nilai_huruf);
            $totalSks += $n->sks;
            $totalMutu += ($bobot * $n->sks);
        }

        $ipk = $totalSks > 0 ? round($totalMutu / $totalSks, 2) : 0.00;

        $trx = Transkrip::where('mahasiswa_id', $mahasiswaId)->first();
        if ($trx) {
            $trx->update([
                'total_sks' => $totalSks,
                'total_mutu' => $totalMutu,
                'ipk' => $ipk,
            ]);
        }
    }
}
