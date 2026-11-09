<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Mahasiswa;
use App\Models\PengajuanKoreksi;
use App\Models\SiakadMataKuliah;
use App\Models\SiakadNilai;
use App\Models\Transkrip;
use App\Models\Wisudawan;
use App\Services\SiakadApiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Redirect dashboard based on role.
     */
    public function index(): RedirectResponse
    {
        $user = Auth::user();

        if ($user->isSuperAdmin()) {
            return redirect()->route('super-admin.dashboard');
        }

        if ($user->isAdminFakultas()) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('mahasiswa.dashboard');
    }

    /**
     * Admin Fakultas Dashboard view (Scoped strictly to assigned faculty).
     */
    public function admin(): View
    {
        $user = Auth::user();
        $fakultasId = $user->fakultas_id;

        $mahasiswaQuery = Mahasiswa::query();
        $transkripQuery = Transkrip::query();
        $wisudawanQuery = Wisudawan::query();
        $nilaiQuery = SiakadNilai::query();
        $koreksiQuery = PengajuanKoreksi::query();

        $scopeMahasiswa = null;
        if ($fakultasId) {
            if ((int) $fakultasId === 1) {
                $scopeMahasiswa = fn ($q) => $q->where(function ($sub) {
                    $sub->where('fakultas_id', 1)->orWhere('program_studi_id', 1);
                });
                $mahasiswaQuery->where(function ($q) {
                    $q->where('fakultas_id', 1)->orWhere('program_studi_id', 1);
                });
            } else {
                $scopeMahasiswa = fn ($q) => $q->where('fakultas_id', $fakultasId);
                $mahasiswaQuery->where('fakultas_id', $fakultasId);
            }

            $transkripQuery->whereHas('mahasiswa', $scopeMahasiswa);
            $wisudawanQuery->whereHas('mahasiswa', $scopeMahasiswa);
            $nilaiQuery->whereHas('mahasiswa', $scopeMahasiswa);
            $koreksiQuery->whereHas('mahasiswa', $scopeMahasiswa);
        }

        $stats = [
            'total_mahasiswa' => $mahasiswaQuery->count(),
            'total_wisudawan' => $wisudawanQuery->count(),
            'total_mata_kuliah' => SiakadMataKuliah::count(),
            'total_transkrip' => $transkripQuery->count(),
            'transkrip_menunggu' => (clone $transkripQuery)->where('status_validasi', 'Menunggu Validasi')->count(),
            'transkrip_valid' => (clone $transkripQuery)->where('status_validasi', 'Valid')->count(),
            'transkrip_ditolak' => (clone $transkripQuery)->where('status_validasi', 'Ditolak')->count(),
            'transkrip_draft' => (clone $transkripQuery)->where('status_validasi', 'Draft')->count(),
            'koreksi_menunggu' => (clone $koreksiQuery)->where('status', 'Menunggu Review')->count(),
            'konflik_count' => (clone $nilaiQuery)->where('has_conflict', true)->count(),
        ];

        $recentTranscriptsQuery = Transkrip::with(['mahasiswa.programStudi', 'wisudawan']);
        if ($fakultasId && $scopeMahasiswa) {
            $recentTranscriptsQuery->whereHas('mahasiswa', $scopeMahasiswa);
        }
        $recentTranscripts = $recentTranscriptsQuery->latest()->take(6)->get();

        $recentKoreksisQuery = PengajuanKoreksi::with(['mahasiswa', 'mataKuliah']);
        if ($fakultasId && $scopeMahasiswa) {
            $recentKoreksisQuery->whereHas('mahasiswa', $scopeMahasiswa);
        }
        $recentKoreksis = $recentKoreksisQuery->latest()->take(5)->get();

        $recentLogs = AuditLog::with('user')
            ->latest()
            ->take(8)
            ->get();

        $fakultas = $user->fakultas;

        return view('dashboard.admin', compact('stats', 'recentTranscripts', 'recentKoreksis', 'recentLogs', 'fakultas'));
    }

    /**
     * Mahasiswa Dashboard view.
     */
    public function mahasiswa(): View
    {
        $user = Auth::user();

        $mahasiswa = Mahasiswa::with([
            'fakultas',
            'programStudi',
            'siakadNilais.mataKuliah',
            'wisudawan',
            'transkrip',
            'pengajuanKoreksis.mataKuliah',
        ])->where('user_id', $user->id)->first();

        // If not linked yet, try finding by email or name
        if (! $mahasiswa) {
            $mahasiswa = Mahasiswa::with([
                'fakultas',
                'programStudi',
                'siakadNilais.mataKuliah',
                'wisudawan',
                'transkrip',
                'pengajuanKoreksis.mataKuliah',
            ])->where('email', $user->email)->first();

            if ($mahasiswa && ! $mahasiswa->user_id) {
                $mahasiswa->update(['user_id' => $user->id]);
            }
        }

        // Auto-sync real transcript from Bridge API if grades/transcript are empty
        if ($mahasiswa && ($mahasiswa->siakadNilais->isEmpty() || ! $mahasiswa->transkrip || $mahasiswa->transkrip->total_sks == 0)) {
            app(SiakadApiService::class)->syncStudentTranscript($mahasiswa);
            $mahasiswa->load(['siakadNilais.mataKuliah', 'transkrip']);
        }

        $notifikasis = $user->notifikasis()->latest()->take(10)->get();

        $mataKuliahList = SiakadMataKuliah::orderBy('nama_mata_kuliah')->get();

        return view('dashboard.mahasiswa', compact('mahasiswa', 'notifikasis', 'mataKuliahList'));
    }
}
