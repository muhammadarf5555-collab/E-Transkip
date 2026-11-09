<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Fakultas;
use App\Models\Mahasiswa;
use App\Models\ProgramStudi;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class MahasiswaController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $query = Mahasiswa::with(['fakultas', 'programStudi', 'transkrip', 'wisudawan']);

        // Scope to faculty for Admin Fakultas
        // Note: FITK (ID 1) also encompasses Informatika which is 'numpang' in FITK
        if ($user->isAdminFakultas() && $user->fakultas_id) {
            if ((int) $user->fakultas_id === 1) {
                $query->where(function ($q) {
                    $q->where('fakultas_id', 1)->orWhere('program_studi_id', 1);
                });
            } else {
                $query->where('fakultas_id', $user->fakultas_id);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nim', 'like', "%{$search}%")
                    ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        if ($request->filled('program_studi_id')) {
            $query->where('program_studi_id', $request->program_studi_id);
        }

        if ($request->filled('angkatan')) {
            $query->where('angkatan', $request->angkatan);
        }

        $mahasiswas = $query->orderBy('nim')->paginate(10);

        // Filter Program Studi dropdown
        $prodiQuery = ProgramStudi::with('fakultas')->orderBy('nama_program_studi');
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

        return view('admin.mahasiswa.index', compact('mahasiswas', 'programStudis'));
    }

    public function create(): View
    {
        $user = Auth::user();
        if ($user->isAdminFakultas() && $user->fakultas_id) {
            if ((int) $user->fakultas_id === 1) {
                $fakultasList = Fakultas::whereIn('id', [1, 13])->get();
                $prodiList = ProgramStudi::where(function ($q) {
                    $q->where('fakultas_id', 1)->orWhere('id', 1);
                })->orderBy('nama_program_studi')->get();
            } else {
                $fakultasList = Fakultas::where('id', $user->fakultas_id)->get();
                $prodiList = ProgramStudi::where('fakultas_id', $user->fakultas_id)->orderBy('nama_program_studi')->get();
            }
        } else {
            $fakultasList = Fakultas::orderBy('nama_fakultas')->get();
            $prodiList = ProgramStudi::with('fakultas')->orderBy('nama_program_studi')->get();
        }

        return view('admin.mahasiswa.create', compact('fakultasList', 'prodiList'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'nim' => ['required', 'string', 'max:50', 'unique:mahasiswas,nim'],
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'fakultas_id' => ['nullable', 'exists:fakultas,id'],
            'program_studi_id' => ['nullable', 'exists:program_studis,id'],
            'tempat_lahir' => ['nullable', 'string', 'max:100'],
            'tanggal_lahir' => ['nullable', 'date'],
            'jenis_kelamin' => ['nullable', 'in:L,P'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'jenjang' => ['nullable', 'string', 'max:10'],
            'angkatan' => ['nullable', 'string', 'max:10'],
            'status' => ['nullable', 'string', 'max:50'],
        ]);

        if ($user->isAdminFakultas() && $user->fakultas_id) {
            $allowedFakultas = $user->getAccessibleFakultasIds();
            if (! empty($validated['fakultas_id'])) {
                if (! in_array((int) $validated['fakultas_id'], $allowedFakultas, true)) {
                    abort(403, 'Fakultas tidak sesuai dengan hak akses Anda.');
                }
            } else {
                $validated['fakultas_id'] = $user->fakultas_id;
            }

            if (! empty($validated['program_studi_id'])) {
                $prodi = ProgramStudi::find($validated['program_studi_id']);
                if ($prodi && ! in_array((int) $prodi->fakultas_id, $allowedFakultas, true) && ! ((int) $user->fakultas_id === 1 && (int) $prodi->id === 1)) {
                    abort(403, 'Program studi tidak sesuai dengan fakultas Anda.');
                }
            }
        }

        // Auto create user account for mahasiswa
        $mhsUser = User::create([
            'name' => $validated['nama'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['nim']), // default password is NIM
            'role' => 'mahasiswa',
            'fakultas_id' => $validated['fakultas_id'] ?? null,
        ]);

        $validated['user_id'] = $mhsUser->id;
        $mahasiswa = Mahasiswa::create($validated);

        AuditLog::record('Menambah Mahasiswa', 'Mahasiswa', (string) $mahasiswa->id, "Menambahkan mahasiswa baru: {$mahasiswa->nim} - {$mahasiswa->nama}");

        return redirect()->route('admin.mahasiswa.index')->with('success', 'Mahasiswa berhasil ditambahkan dengan akun login (Password default adalah NIM).');
    }

    public function show(Mahasiswa $mahasiswa): View
    {
        $user = Auth::user();
        if ($user->isAdminFakultas() && ! $user->canAccessMahasiswa($mahasiswa)) {
            abort(403, 'Akses ditolak. Mahasiswa tidak berada di fakultas Anda.');
        }

        $mahasiswa->load([
            'user',
            'fakultas',
            'programStudi',
            'siakadMahasiswa',
            'siakadNilais.mataKuliah',
            'wisudawan',
            'transkrip',
        ]);

        return view('admin.mahasiswa.show', compact('mahasiswa'));
    }

    public function edit(Mahasiswa $mahasiswa): View
    {
        $user = Auth::user();
        if ($user->isAdminFakultas() && ! $user->canAccessMahasiswa($mahasiswa)) {
            abort(403, 'Akses ditolak. Mahasiswa tidak berada di fakultas Anda.');
        }

        if ($user->isAdminFakultas() && $user->fakultas_id) {
            if ((int) $user->fakultas_id === 1) {
                $fakultasList = Fakultas::whereIn('id', [1, 13])->get();
                $prodiList = ProgramStudi::where(function ($q) {
                    $q->where('fakultas_id', 1)->orWhere('id', 1);
                })->orderBy('nama_program_studi')->get();
            } else {
                $fakultasList = Fakultas::where('id', $user->fakultas_id)->get();
                $prodiList = ProgramStudi::where('fakultas_id', $user->fakultas_id)->orderBy('nama_program_studi')->get();
            }
        } else {
            $fakultasList = Fakultas::orderBy('nama_fakultas')->get();
            $prodiList = ProgramStudi::with('fakultas')->orderBy('nama_program_studi')->get();
        }

        return view('admin.mahasiswa.edit', compact('mahasiswa', 'fakultasList', 'prodiList'));
    }

    public function update(Request $request, Mahasiswa $mahasiswa): RedirectResponse
    {
        $user = Auth::user();
        if ($user->isAdminFakultas() && ! $user->canAccessMahasiswa($mahasiswa)) {
            abort(403, 'Akses ditolak. Mahasiswa tidak berada di fakultas Anda.');
        }

        $validated = $request->validate([
            'nim' => ['required', 'string', 'max:50', 'unique:mahasiswas,nim,'.$mahasiswa->id],
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'fakultas_id' => ['nullable', 'exists:fakultas,id'],
            'program_studi_id' => ['nullable', 'exists:program_studis,id'],
            'tempat_lahir' => ['nullable', 'string', 'max:100'],
            'tanggal_lahir' => ['nullable', 'date'],
            'jenis_kelamin' => ['nullable', 'in:L,P'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'jenjang' => ['nullable', 'string', 'max:10'],
            'angkatan' => ['nullable', 'string', 'max:10'],
            'status' => ['nullable', 'string', 'max:50'],
        ]);

        if ($user->isAdminFakultas() && $user->fakultas_id) {
            $allowedFakultas = $user->getAccessibleFakultasIds();
            if (! empty($validated['fakultas_id'])) {
                if (! in_array((int) $validated['fakultas_id'], $allowedFakultas, true)) {
                    abort(403, 'Fakultas tidak sesuai dengan hak akses Anda.');
                }
            } else {
                $validated['fakultas_id'] = $user->fakultas_id;
            }

            if (! empty($validated['program_studi_id'])) {
                $prodi = ProgramStudi::find($validated['program_studi_id']);
                if ($prodi && ! in_array((int) $prodi->fakultas_id, $allowedFakultas, true) && ! ((int) $user->fakultas_id === 1 && (int) $prodi->id === 1)) {
                    abort(403, 'Program studi tidak sesuai dengan fakultas Anda.');
                }
            }
        }

        $mahasiswa->update($validated);

        if ($mahasiswa->user && $request->filled('nama')) {
            $mahasiswa->user->update([
                'name' => $validated['nama'],
                'email' => $validated['email'] ?? $mahasiswa->user->email,
                'fakultas_id' => $validated['fakultas_id'] ?? $mahasiswa->user->fakultas_id,
            ]);
        }

        AuditLog::record('Memperbarui Mahasiswa', 'Mahasiswa', (string) $mahasiswa->id, "Memperbarui data mahasiswa: {$mahasiswa->nim}");

        return redirect()->route('admin.mahasiswa.index')->with('success', 'Data Mahasiswa berhasil diperbarui.');
    }

    public function destroy(Mahasiswa $mahasiswa): RedirectResponse
    {
        $user = Auth::user();
        if ($user->isAdminFakultas() && ! $user->canAccessMahasiswa($mahasiswa)) {
            abort(403, 'Akses ditolak. Mahasiswa tidak berada di fakultas Anda.');
        }

        $nim = $mahasiswa->nim;
        $id = $mahasiswa->id;
        $mhsUser = $mahasiswa->user;

        $mahasiswa->delete();
        if ($mhsUser) {
            $mhsUser->delete();
        }

        AuditLog::record('Menghapus Mahasiswa', 'Mahasiswa', (string) $id, "Menghapus mahasiswa dan user akun: {$nim}");

        return redirect()->route('admin.mahasiswa.index')->with('success', 'Mahasiswa berhasil dihapus.');
    }
}
