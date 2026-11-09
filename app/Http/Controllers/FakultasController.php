<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Fakultas;
use App\Services\SiakadApiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FakultasController extends Controller
{
    public function index(SiakadApiService $siakadApi): View
    {
        $user = Auth::user();
        $query = Fakultas::withCount(['programStudis', 'mahasiswas'])
            ->orderByRaw('CASE WHEN kode_fakultas IS NULL THEN 1 ELSE 0 END, kode_fakultas ASC, nama_fakultas ASC');

        if ($user->isAdminFakultas() && $user->fakultas_id) {
            $query->where('id', $user->fakultas_id);
        }

        $fakultas = $query->paginate(15);

        $testKode = ($user->isAdminFakultas() && $user->fakultas?->kode_fakultas) ? $user->fakultas->kode_fakultas : 2;
        $bridgeStatus = $siakadApi->testBridgeConnection($testKode);

        return view('admin.master.fakultas.index', compact('fakultas', 'bridgeStatus'));
    }

    public function syncApi(SiakadApiService $siakadApi): RedirectResponse
    {
        $user = Auth::user();
        if ($user->isAdminFakultas()) {
            abort(403, 'Hanya Super Admin yang dapat menyinkronkan seluruh fakultas.');
        }

        $result = $siakadApi->syncFakultasFromBridge();

        if ($result['success']) {
            AuditLog::record(
                'Sinkronisasi API Fakultas',
                'Fakultas',
                'API',
                "Sinkronisasi API: {$result['created']} baru, {$result['updated']} diperbarui dari {$result['total']} fakultas."
            );

            return redirect()->route('admin.fakultas.index')->with('success', $result['message']);
        }

        return redirect()->route('admin.fakultas.index')->with('error', $result['message']);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if ($user->isAdminFakultas()) {
            abort(403, 'Hanya Super Admin yang dapat menambah fakultas baru.');
        }

        $validated = $request->validate([
            'kode_fakultas' => ['nullable', 'integer', 'unique:fakultas,kode_fakultas'],
            'nama_fakultas' => ['required', 'string', 'max:255', 'unique:fakultas,nama_fakultas'],
            'nama_fakultas_singkat' => ['nullable', 'string', 'max:50'],
        ]);

        $fakultas = Fakultas::create($validated);

        AuditLog::record('Menambah Fakultas', 'Fakultas', (string) $fakultas->id, "Menambahkan fakultas: {$fakultas->nama_fakultas}");

        return redirect()->route('admin.fakultas.index')->with('success', 'Fakultas berhasil ditambahkan.');
    }

    public function update(Request $request, Fakultas $fakultas): RedirectResponse
    {
        $user = Auth::user();
        if ($user->isAdminFakultas() && $user->fakultas_id && $fakultas->id !== $user->fakultas_id) {
            abort(403, 'Akses ditolak. Anda tidak berhak mengubah fakultas lain.');
        }

        $validated = $request->validate([
            'kode_fakultas' => ['nullable', 'integer', 'unique:fakultas,kode_fakultas,'.$fakultas->id],
            'nama_fakultas' => ['required', 'string', 'max:255', 'unique:fakultas,nama_fakultas,'.$fakultas->id],
            'nama_fakultas_singkat' => ['nullable', 'string', 'max:50'],
        ]);

        $fakultas->update($validated);

        AuditLog::record('Memperbarui Fakultas', 'Fakultas', (string) $fakultas->id, "Memperbarui nama fakultas menjadi: {$fakultas->nama_fakultas}");

        return redirect()->route('admin.fakultas.index')->with('success', 'Fakultas berhasil diperbarui.');
    }

    public function destroy(Fakultas $fakultas): RedirectResponse
    {
        $user = Auth::user();
        if ($user->isAdminFakultas()) {
            abort(403, 'Hanya Super Admin yang dapat menghapus fakultas.');
        }

        $nama = $fakultas->nama_fakultas;
        $id = $fakultas->id;
        $fakultas->delete();

        AuditLog::record('Menghapus Fakultas', 'Fakultas', (string) $id, "Menghapus fakultas: {$nama}");

        return redirect()->route('admin.fakultas.index')->with('success', 'Fakultas berhasil dihapus.');
    }
}
