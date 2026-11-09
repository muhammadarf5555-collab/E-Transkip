<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Fakultas;
use App\Models\ProgramStudi;
use App\Services\SiakadApiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProgramStudiController extends Controller
{
    public function index(Request $request, SiakadApiService $siakadApi): View
    {
        $user = Auth::user();
        $query = ProgramStudi::with('fakultas')->withCount('mahasiswas');

        // Scope to faculty for Admin Fakultas
        if ($user->isAdminFakultas() && $user->fakultas_id) {
            if ((int) $user->fakultas_id === 1) {
                $query->where(function ($q) {
                    $q->where('fakultas_id', 1)->orWhere('id', 1);
                });
                $fakultasList = Fakultas::whereIn('id', [1, 13])->get();
            } else {
                $query->where('fakultas_id', $user->fakultas_id);
                $fakultasList = Fakultas::where('id', $user->fakultas_id)->get();
            }
        } else {
            if ($request->filled('fakultas_id')) {
                $query->where('fakultas_id', $request->fakultas_id);
            }
            $fakultasList = Fakultas::orderBy('nama_fakultas')->get();
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_program_studi', 'like', "%{$search}%")
                    ->orWhere('kode_prodi', 'like', "%{$search}%")
                    ->orWhere('gelar_akademik', 'like', "%{$search}%");
            });
        }

        $programStudis = $query->orderBy('nama_program_studi')->paginate(15)->withQueryString();

        // Check live bridge connection status
        $targetFakultasId = ($user->isAdminFakultas() && $user->fakultas?->kode_fakultas) ? $user->fakultas->kode_fakultas : 2;
        if ($request->filled('fakultas_id') && ! $user->isAdminFakultas()) {
            $selectedFak = Fakultas::find($request->fakultas_id);
            if ($selectedFak && $selectedFak->kode_fakultas) {
                $targetFakultasId = $selectedFak->kode_fakultas;
            }
        }
        $bridgeStatus = $siakadApi->testBridgeConnection($targetFakultasId);

        return view('admin.master.program_studi.index', compact('programStudis', 'fakultasList', 'bridgeStatus'));
    }

    public function syncApi(Request $request, SiakadApiService $siakadApi): RedirectResponse
    {
        $user = Auth::user();
        $fakultasKode = null;

        if ($user->isAdminFakultas() && $user->fakultas?->kode_fakultas) {
            $fakultasKode = (int) $user->fakultas->kode_fakultas;
        } elseif ($request->filled('fakultas_id')) {
            $fak = Fakultas::find($request->fakultas_id);
            if ($fak && $fak->kode_fakultas) {
                $fakultasKode = (int) $fak->kode_fakultas;
            }
        } elseif ($request->filled('fakultas_kode')) {
            $fakultasKode = (int) $request->fakultas_kode;
        }

        $result = $siakadApi->syncProgramStudiFromBridge($fakultasKode);

        if ($result['success']) {
            $suffix = $fakultasKode ? " (Fakultas Kode: {$fakultasKode})" : ' (Semua Fakultas)';
            AuditLog::record(
                'Sinkronisasi API Program Studi',
                'ProgramStudi',
                'API',
                "Sinkronisasi API{$suffix}: {$result['created']} baru, {$result['updated']} diperbarui dari {$result['total']} prodi."
            );

            return redirect()->route('admin.program-studi.index', $request->only('fakultas_id'))->with('success', $result['message']);
        }

        return redirect()->route('admin.program-studi.index', $request->only('fakultas_id'))->with('error', $result['message']);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'fakultas_id' => ['required', 'exists:fakultas,id'],
            'kode_prodi' => ['nullable', 'string', 'max:50'],
            'nama_program_studi' => ['required', 'string', 'max:255'],
            'jenjang' => ['nullable', 'string', 'max:20'],
            'sks_tempuh' => ['nullable', 'integer', 'min:1'],
            'gelar_akademik' => ['nullable', 'string', 'max:50'],
            'gelar_akademik_full' => ['nullable', 'string', 'max:150'],
        ]);

        if ($user->isAdminFakultas() && $user->fakultas_id) {
            $validated['fakultas_id'] = $user->fakultas_id;
        }

        $prodi = ProgramStudi::create($validated);

        AuditLog::record('Menambah Program Studi', 'ProgramStudi', (string) $prodi->id, "Menambahkan program studi: {$prodi->nama_program_studi}");

        return redirect()->route('admin.program-studi.index')->with('success', 'Program Studi berhasil ditambahkan.');
    }

    public function update(Request $request, ProgramStudi $programStudi): RedirectResponse
    {
        $user = Auth::user();
        if ($user->isAdminFakultas() && $user->fakultas_id) {
            $isFitkInformatika = ((int) $user->fakultas_id === 1 && (int) $programStudi->id === 1);
            if ($programStudi->fakultas_id !== $user->fakultas_id && ! $isFitkInformatika) {
                abort(403, 'Akses ditolak. Program studi tidak berada di fakultas Anda.');
            }
        }

        $validated = $request->validate([
            'fakultas_id' => ['required', 'exists:fakultas,id'],
            'kode_prodi' => ['nullable', 'string', 'max:50'],
            'nama_program_studi' => ['required', 'string', 'max:255'],
            'jenjang' => ['nullable', 'string', 'max:20'],
            'sks_tempuh' => ['nullable', 'integer', 'min:1'],
            'gelar_akademik' => ['nullable', 'string', 'max:50'],
            'gelar_akademik_full' => ['nullable', 'string', 'max:150'],
        ]);

        if ($user->isAdminFakultas() && $user->fakultas_id) {
            $validated['fakultas_id'] = $programStudi->fakultas_id;
        }

        $programStudi->update($validated);

        AuditLog::record('Memperbarui Program Studi', 'ProgramStudi', (string) $programStudi->id, "Memperbarui program studi: {$programStudi->nama_program_studi}");

        return redirect()->route('admin.program-studi.index')->with('success', 'Program Studi berhasil diperbarui.');
    }

    public function destroy(ProgramStudi $programStudi): RedirectResponse
    {
        $user = Auth::user();
        if ($user->isAdminFakultas() && $user->fakultas_id) {
            $isFitkInformatika = ((int) $user->fakultas_id === 1 && (int) $programStudi->id === 1);
            if ($programStudi->fakultas_id !== $user->fakultas_id && ! $isFitkInformatika) {
                abort(403, 'Akses ditolak. Program studi tidak berada di fakultas Anda.');
            }
        }

        $nama = $programStudi->nama_program_studi;
        $id = $programStudi->id;
        $programStudi->delete();

        AuditLog::record('Menghapus Program Studi', 'ProgramStudi', (string) $id, "Menghapus program studi: {$nama}");

        return redirect()->route('admin.program-studi.index')->with('success', 'Program Studi berhasil dihapus.');
    }
}
