<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Mahasiswa;
use App\Models\SiakadMahasiswa;
use App\Models\SiakadMataKuliah;
use App\Models\SiakadNilai;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SiakadController extends Controller
{
    public function mahasiswa(Request $request): View
    {
        $user = Auth::user();
        $query = SiakadMahasiswa::with('mahasiswa');

        // Scope to faculty for Admin Fakultas
        if ($user->isAdminFakultas() && $user->fakultas_id) {
            $query->where(function ($q) use ($user) {
                if ((int) $user->fakultas_id === 1) {
                    $q->whereHas('mahasiswa', fn ($m) => $m->where('fakultas_id', 1)->orWhere('program_studi_id', 1))
                        ->orWhere('fakultas', 'Fakultas Tarbiyah dan Ilmu Keguruan')
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
                    ->orWhere('program_studi', 'like', "%{$search}%");
            });
        }

        $siakadMahasiswas = $query->orderBy('nim')->paginate(10);

        return view('admin.siakad.mahasiswa', compact('siakadMahasiswas'));
    }

    public function nilai(Request $request): View
    {
        $user = Auth::user();
        $query = SiakadNilai::with(['mahasiswa', 'mataKuliah']);

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

        $nilais = $query->latest()->paginate(15);

        return view('admin.siakad.nilai', compact('nilais'));
    }

    public function importMahasiswa(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle, 1000, ',');

        $count = 0;
        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            if (count($row) < 2) {
                continue;
            }

            $nim = trim($row[0]);
            $nama = trim($row[1]);
            $fakultas = trim($row[2] ?? '');
            $prodi = trim($row[3] ?? '');
            $angkatan = trim($row[4] ?? '');

            $mhs = Mahasiswa::where('nim', $nim)->first();

            SiakadMahasiswa::updateOrCreate(
                ['nim' => $nim],
                [
                    'mahasiswa_id' => $mhs?->id,
                    'nama' => $nama,
                    'fakultas' => $fakultas,
                    'program_studi' => $prodi,
                    'angkatan' => $angkatan,
                    'sumber_data' => 'SIAKAD_IMPORT',
                    'imported_at' => now(),
                ]
            );
            $count++;
        }
        fclose($handle);

        AuditLog::record('Import SIAKAD Mahasiswa', 'SIAKAD', null, "Berhasil mengimpor {$count} data mahasiswa dari SIAKAD.");

        return redirect()->route('admin.siakad.mahasiswa')->with('success', "Berhasil mengimpor {$count} data mahasiswa SIAKAD.");
    }

    public function importNilai(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle, 1000, ',');

        $count = 0;
        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            if (count($row) < 5) {
                continue;
            }

            $nim = trim($row[0]);
            $kodeMk = trim($row[1]);
            $semester = trim($row[2]);
            $nilaiHuruf = strtoupper(trim($row[3]));
            $bobot = (float) trim($row[4]);
            $sks = isset($row[5]) ? (int) trim($row[5]) : 3;

            $mhs = Mahasiswa::where('nim', $nim)->first();
            $mk = SiakadMataKuliah::where('kode_mata_kuliah', $kodeMk)->first();

            if (! $mhs) {
                continue;
            }

            if (! $mk) {
                $mk = SiakadMataKuliah::create([
                    'kode_mata_kuliah' => $kodeMk,
                    'nama_mata_kuliah' => 'Mata Kuliah '.$kodeMk,
                    'sks' => $sks,
                    'semester' => $semester,
                ]);
            }

            SiakadNilai::updateOrCreate(
                [
                    'mahasiswa_id' => $mhs->id,
                    'mata_kuliah_id' => $mk->id,
                    'semester' => $semester,
                ],
                [
                    'nilai_huruf' => $nilaiHuruf,
                    'nilai_angka' => $bobot,
                    'bobot' => $bobot,
                    'sks' => $sks,
                    'sumber_data' => 'SIAKAD_IMPORT',
                    'imported_at' => now(),
                ]
            );
            $count++;
        }
        fclose($handle);

        AuditLog::record('Import SIAKAD Nilai', 'SIAKAD', null, "Berhasil mengimpor {$count} rekaman nilai akademik dari SIAKAD.");

        return redirect()->route('admin.siakad.nilai')->with('success', "Berhasil mengimpor {$count} data nilai SIAKAD.");
    }

    public function downloadMahasiswaTemplate(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_siakad_mahasiswa.csv"',
        ];

        $columns = ['NIM', 'Nama', 'Fakultas', 'Program_Studi', 'Angkatan'];
        $example = ['220001', 'Ahmad Dahlan', 'Fakultas Sains dan Teknologi', 'Teknik Informatika', '2022'];

        $callback = function () use ($columns, $example) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, $example);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function downloadNilaiTemplate(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_siakad_nilai.csv"',
        ];

        $columns = ['NIM', 'Kode_Mata_Kuliah', 'Semester', 'Nilai_Huruf', 'Bobot', 'SKS'];
        $example = ['220001', 'IF101', '1', 'A', '4.00', '3'];

        $callback = function () use ($columns, $example) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, $example);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
