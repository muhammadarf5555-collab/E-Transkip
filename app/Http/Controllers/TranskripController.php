<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Mahasiswa;
use App\Models\Transkrip;
use App\Models\Wisudawan;
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
    public function index(Request $request): View
    {
        $user = Auth::user();
        $query = Transkrip::with(['mahasiswa.programStudi', 'wisudawan']);

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
                    ->orWhereHas('mahasiswa', function ($m) use ($search) {
                        $m->where('nim', 'like', "%{$search}%")
                            ->orWhere('nama', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status_validasi', $request->status);
        }

        $transkrips = $query->latest()->paginate(10);

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

        return view('admin.transkrip.index', compact('transkrips', 'counts'));
    }

    public function sync(): RedirectResponse
    {
        $user = Auth::user();
        $wisudawanQuery = Wisudawan::with(['mahasiswa.siakadNilais.mataKuliah']);

        if ($user->isAdminFakultas() && $user->fakultas_id) {
            $wisudawanQuery->where(function ($q) use ($user) {
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

        $wisudawans = $wisudawanQuery->get();
        $synced = 0;

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

        AuditLog::record('Sinkronisasi Transkrip', 'Transkrip', null, "Menyinkronkan dan mengalkulasi ulang {$synced} transkrip wisudawan.");

        return redirect()->route('admin.transkrip.index')->with('success', "Sinkronisasi selesai. Berhasil memperbarui {$synced} transkrip nilai.");
    }

    public function show(Transkrip $transkrip): View
    {
        $user = Auth::user();
        if ($user->isAdminFakultas() && $transkrip->mahasiswa && ! $user->canAccessMahasiswa($transkrip->mahasiswa)) {
            abort(403, 'Akses ditolak. Transkrip ini berada di luar fakultas Anda.');
        }

        $transkrip->load([
            'mahasiswa.fakultas',
            'mahasiswa.programStudi',
            'mahasiswa.siakadNilais.mataKuliah',
            'wisudawan',
        ]);

        $qrUrl = $this->getVerificationUrl($transkrip->kode_verifikasi);
        $qrCodeSvg = base64_encode(QrCode::format('svg')->size(120)->generate($qrUrl));

        return view('admin.transkrip.show', compact('transkrip', 'qrCodeSvg', 'qrUrl'));
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

    public function downloadPdf(Request $request, Transkrip $transkrip): Response
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
