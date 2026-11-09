<?php

namespace App\Http\Controllers;

use App\Models\AturanBobotNilai;
use App\Models\AuditLog;
use App\Models\NilaiChangeHistory;
use App\Models\Notifikasi;
use App\Models\PengajuanKoreksi;
use App\Models\SiakadNilai;
use App\Models\Transkrip;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PengajuanKoreksiController extends Controller
{
    /**
     * List all correction requests (Admin Fakultas / Super Admin).
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $query = PengajuanKoreksi::with(['mahasiswa.fakultas', 'mahasiswa.programStudi', 'mataKuliah', 'siakadNilai', 'reviewer']);

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

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('mahasiswa', function ($m) use ($search) {
                $m->where('nama', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        $koreksis = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total' => (clone $query)->count(),
            'menunggu' => (clone $query)->where('status', 'Menunggu Review')->count(),
            'disetujui' => (clone $query)->where('status', 'Disetujui')->count(),
            'ditolak' => (clone $query)->where('status', 'Ditolak')->count(),
        ];

        return view('admin.koreksi.index', compact('koreksis', 'stats'));
    }

    /**
     * Mahasiswa submits a correction request.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $mahasiswa = $user->mahasiswa;

        if (! $mahasiswa) {
            abort(403, 'Hanya mahasiswa terdaftar yang dapat mengajukan koreksi.');
        }

        $request->validate([
            'jenis_koreksi' => ['required', 'string'],
            'mata_kuliah_id' => ['nullable', 'exists:siakad_mata_kuliahs,id'],
            'nilai_lama' => ['nullable', 'string', 'max:5'],
            'nilai_diajukan' => ['nullable', 'string', 'max:5'],
            'alasan' => ['required', 'string', 'max:1000'],
            'dokumen' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:3072'],
        ]);

        $dokumenPath = null;
        if ($request->hasFile('dokumen')) {
            $dokumenPath = $request->file('dokumen')->store('dokumen_koreksi', 'public');
        }

        $siakadNilaiId = null;
        if ($request->mata_kuliah_id) {
            $siakadNilai = SiakadNilai::where('mahasiswa_id', $mahasiswa->id)
                ->where('mata_kuliah_id', $request->mata_kuliah_id)
                ->first();
            $siakadNilaiId = $siakadNilai?->id;
        }

        $koreksi = PengajuanKoreksi::create([
            'mahasiswa_id' => $mahasiswa->id,
            'siakad_nilai_id' => $siakadNilaiId,
            'mata_kuliah_id' => $request->mata_kuliah_id,
            'jenis_koreksi' => $request->jenis_koreksi,
            'nilai_lama' => $request->nilai_lama,
            'nilai_diajukan' => $request->nilai_diajukan,
            'alasan' => $request->alasan,
            'dokumen_pendukung' => $dokumenPath,
            'status' => 'Menunggu Review',
        ]);

        // Send notification to Mahasiswa
        Notifikasi::kirim(
            $user->id,
            'Pengajuan Koreksi Terkirim',
            'Pengajuan koreksi Anda ('.$request->jenis_koreksi.') telah kami terima dan sedang menunggu verifikasi oleh Admin Fakultas.',
            'info',
            route('mahasiswa.dashboard')
        );

        AuditLog::record(
            'Pengajuan Koreksi Nilai/Data',
            'PengajuanKoreksi',
            (string) $koreksi->id,
            "Mahasiswa {$mahasiswa->nama} ({$mahasiswa->nim}) mengajukan koreksi {$request->jenis_koreksi}."
        );

        return back()->with('success', 'Pengajuan koreksi berhasil dikirim. Admin akademik akan segera meninjau permohonan Anda.');
    }

    /**
     * Admin approves correction request.
     */
    public function approve(Request $request, PengajuanKoreksi $koreksi): RedirectResponse
    {
        $user = Auth::user();
        if ($user->isAdminFakultas() && $koreksi->mahasiswa && ! $user->canAccessMahasiswa($koreksi->mahasiswa)) {
            abort(403, 'Akses ditolak. Pengajuan koreksi ini di luar fakultas Anda.');
        }

        $request->validate([
            'catatan_admin' => ['nullable', 'string', 'max:500'],
        ]);

        $koreksi->update([
            'status' => 'Disetujui',
            'catatan_admin' => $request->catatan_admin ?: 'Pengajuan koreksi telah disetujui.',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        // If it was a grade correction and has siakad_nilai_id & new grade
        if ($koreksi->siakad_nilai_id && $koreksi->nilai_diajukan) {
            $nilai = $koreksi->siakadNilai;
            if ($nilai) {
                $oldHuruf = $nilai->nilai_huruf;
                $oldBobot = $nilai->bobot;
                $newBobot = AturanBobotNilai::getBobot($koreksi->nilai_diajukan);

                NilaiChangeHistory::create([
                    'siakad_nilai_id' => $nilai->id,
                    'user_id' => Auth::id(),
                    'nilai_huruf_lama' => $oldHuruf,
                    'bobot_lama' => $oldBobot,
                    'nilai_huruf_baru' => $koreksi->nilai_diajukan,
                    'bobot_baru' => $newBobot,
                    'alasan' => 'Persetujuan Pengajuan Koreksi Mahasiswa #'.$koreksi->id.': '.$koreksi->alasan,
                ]);

                $nilai->update([
                    'nilai_huruf' => $koreksi->nilai_diajukan,
                    'nilai_angka' => $newBobot,
                    'bobot' => $newBobot,
                    'sumber_data' => 'Manual',
                    'has_conflict' => false,
                ]);

                // Recalculate transcript
                $this->recalculateTranscript($koreksi->mahasiswa_id);
            }
        }

        // Send notification to Mahasiswa
        if ($koreksi->mahasiswa->user_id) {
            Notifikasi::kirim(
                $koreksi->mahasiswa->user_id,
                'Pengajuan Koreksi Disetujui',
                'Pengajuan koreksi Anda ('.$koreksi->jenis_koreksi.') telah disetujui oleh admin fakultas.'.($request->catatan_admin ? ' Catatan: '.$request->catatan_admin : ''),
                'success',
                route('mahasiswa.dashboard')
            );
        }

        AuditLog::record(
            'Persetujuan Koreksi Nilai/Data',
            'PengajuanKoreksi',
            (string) $koreksi->id,
            'Admin '.Auth::user()->name." menyetujui koreksi #{$koreksi->id} untuk NIM {$koreksi->mahasiswa->nim}"
        );

        return back()->with('success', 'Pengajuan koreksi berhasil disetujui dan data akademik telah diperbarui.');
    }

    /**
     * Admin rejects correction request.
     */
    public function reject(Request $request, PengajuanKoreksi $koreksi): RedirectResponse
    {
        $user = Auth::user();
        if ($user->isAdminFakultas() && $koreksi->mahasiswa && ! $user->canAccessMahasiswa($koreksi->mahasiswa)) {
            abort(403, 'Akses ditolak. Pengajuan koreksi ini di luar fakultas Anda.');
        }

        $request->validate([
            'catatan_admin' => ['required', 'string', 'max:500'],
        ], [
            'catatan_admin.required' => 'Mohon berikan alasan penolakan koreksi.',
        ]);

        $koreksi->update([
            'status' => 'Ditolak',
            'catatan_admin' => $request->catatan_admin,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        // Send notification to Mahasiswa
        if ($koreksi->mahasiswa->user_id) {
            Notifikasi::kirim(
                $koreksi->mahasiswa->user_id,
                'Pengajuan Koreksi Ditolak',
                'Pengajuan koreksi Anda ('.$koreksi->jenis_koreksi.') ditolak oleh admin. Alasan: '.$request->catatan_admin,
                'danger',
                route('mahasiswa.dashboard')
            );
        }

        AuditLog::record(
            'Penolakan Koreksi Nilai/Data',
            'PengajuanKoreksi',
            (string) $koreksi->id,
            'Admin '.Auth::user()->name." menolak koreksi #{$koreksi->id} untuk NIM {$koreksi->mahasiswa->nim}. Alasan: {$request->catatan_admin}"
        );

        return back()->with('success', 'Pengajuan koreksi ditolak.');
    }

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
