<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\SiakadMataKuliah;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SiakadMataKuliahController extends Controller
{
    public function index(Request $request): View
    {
        $query = SiakadMataKuliah::withCount('nilais');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_mata_kuliah', 'like', "%{$search}%")
                    ->orWhere('nama_mata_kuliah', 'like', "%{$search}%");
            });
        }

        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }

        $mataKuliahs = $query->orderBy('kode_mata_kuliah')->paginate(10);

        return view('admin.master.mata_kuliah.index', compact('mataKuliahs'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode_mata_kuliah' => ['required', 'string', 'max:50', 'unique:siakad_mata_kuliahs,kode_mata_kuliah'],
            'nama_mata_kuliah' => ['required', 'string', 'max:255'],
            'sks' => ['required', 'integer', 'min:1', 'max:10'],
            'semester' => ['nullable', 'string', 'max:10'],
            'jenis_mata_kuliah' => ['nullable', 'string', 'max:50'],
        ]);

        $mk = SiakadMataKuliah::create($validated);

        AuditLog::record('Menambah Mata Kuliah', 'MataKuliah', (string) $mk->id, "Menambahkan mata kuliah: {$mk->kode_mata_kuliah} - {$mk->nama_mata_kuliah}");

        return redirect()->route('admin.mata-kuliah.index')->with('success', 'Mata Kuliah berhasil ditambahkan.');
    }

    public function update(Request $request, SiakadMataKuliah $mataKuliah): RedirectResponse
    {
        $validated = $request->validate([
            'kode_mata_kuliah' => ['required', 'string', 'max:50', 'unique:siakad_mata_kuliahs,kode_mata_kuliah,'.$mataKuliah->id],
            'nama_mata_kuliah' => ['required', 'string', 'max:255'],
            'sks' => ['required', 'integer', 'min:1', 'max:10'],
            'semester' => ['nullable', 'string', 'max:10'],
            'jenis_mata_kuliah' => ['nullable', 'string', 'max:50'],
        ]);

        $mataKuliah->update($validated);

        AuditLog::record('Memperbarui Mata Kuliah', 'MataKuliah', (string) $mataKuliah->id, "Memperbarui mata kuliah: {$mataKuliah->kode_mata_kuliah}");

        return redirect()->route('admin.mata-kuliah.index')->with('success', 'Mata Kuliah berhasil diperbarui.');
    }

    public function destroy(SiakadMataKuliah $mataKuliah): RedirectResponse
    {
        $kode = $mataKuliah->kode_mata_kuliah;
        $id = $mataKuliah->id;
        $mataKuliah->delete();

        AuditLog::record('Menghapus Mata Kuliah', 'MataKuliah', (string) $id, "Menghapus mata kuliah: {$kode}");

        return redirect()->route('admin.mata-kuliah.index')->with('success', 'Mata Kuliah berhasil dihapus.');
    }
}
