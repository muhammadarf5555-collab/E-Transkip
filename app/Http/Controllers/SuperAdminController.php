<?php

namespace App\Http\Controllers;

use App\Models\AdminFakultasRequest;
use App\Models\AturanBobotNilai;
use App\Models\AuditLog;
use App\Models\Fakultas;
use App\Models\Mahasiswa;
use App\Models\ProgramStudi;
use App\Models\SiakadSyncLog;
use App\Models\Transkrip;
use App\Models\User;
use App\Models\Wisudawan;
use App\Services\SiakadApiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class SuperAdminController extends Controller
{
    /**
     * Display Super Admin Executive & Monitoring Dashboard.
     */
    public function dashboard(Request $request): View
    {
        $stats = [
            'total_fakultas' => Fakultas::count(),
            'total_prodi' => ProgramStudi::count(),
            'total_mahasiswa' => Mahasiswa::count(),
            'total_wisudawan' => Wisudawan::count(),
            'total_transkrip' => Transkrip::count(),
            'transkrip_valid' => Transkrip::where('status_validasi', 'Valid')->count(),
            'transkrip_menunggu' => Transkrip::where('status_validasi', 'Menunggu Validasi')->count(),
            'transkrip_ditolak' => Transkrip::where('status_validasi', 'Ditolak')->count(),
            'transkrip_draft' => Transkrip::where('status_validasi', 'Draft')->count(),
            'total_users' => User::count(),
            'total_admin_fakultas' => User::whereIn('role', ['admin_fakultas', 'admin'])->count(),
            'pending_requests_count' => AdminFakultasRequest::where('status', 'pending')->count(),
        ];

        // Faculty Breakdown for University Charts
        $fakultasBreakdown = Fakultas::withCount(['mahasiswas', 'programStudis'])
            ->with(['mahasiswas.transkrip'])
            ->get()
            ->map(function ($fak) {
                $totalTrx = 0;
                $validTrx = 0;
                $totalIpk = 0.0;
                $countIpk = 0;

                foreach ($fak->mahasiswas as $mhs) {
                    if ($mhs->transkrip) {
                        $totalTrx++;
                        if ($mhs->transkrip->status_validasi === 'Valid') {
                            $validTrx++;
                        }
                        $totalIpk += $mhs->transkrip->ipk;
                        $countIpk++;
                    }
                }

                $fak->total_transkrip = $totalTrx;
                $fak->valid_transkrip = $validTrx;
                $fak->avg_ipk = $countIpk > 0 ? round($totalIpk / $countIpk, 2) : 0.00;

                return $fak;
            });

        // Transkrip Query for Super Admin monitoring
        $transkripQuery = Transkrip::with(['mahasiswa.fakultas', 'mahasiswa.programStudi', 'wisudawan']);

        if ($request->filled('fakultas_id')) {
            $transkripQuery->whereHas('mahasiswa', function ($q) use ($request) {
                $q->where('fakultas_id', $request->fakultas_id);
            });
        }

        if ($request->filled('status_validasi')) {
            $transkripQuery->where('status_validasi', $request->status_validasi);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $transkripQuery->whereHas('mahasiswa', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        $transkrips = $transkripQuery->latest()->paginate(10)->withQueryString();

        // Admin Fakultas Accounts
        $adminFakultasList = User::whereIn('role', ['admin_fakultas', 'admin'])
            ->with('fakultas')
            ->latest()
            ->get();

        // Admin Fakultas Requests
        $accountRequests = AdminFakultasRequest::with('fakultas')
            ->latest()
            ->get();

        // Faculties for filters and modals
        $fakultasList = Fakultas::orderBy('nama_fakultas')->get();

        // Grade weight rules
        $bobotRules = AturanBobotNilai::orderByDesc('bobot')->get();

        // Recent Audit Logs
        $recentLogs = AuditLog::with('user')->latest()->take(10)->get();

        // SIAKAD Sync Logs
        $siakadLogs = SiakadSyncLog::with(['user', 'fakultas'])->latest()->take(6)->get();

        return view('super_admin.dashboard', compact(
            'stats',
            'fakultasBreakdown',
            'transkrips',
            'adminFakultasList',
            'accountRequests',
            'fakultasList',
            'bobotRules',
            'recentLogs',
            'siakadLogs'
        ));
    }

    /**
     * Read-only view of a transcript for Super Admin.
     */
    public function showTranskrip(Transkrip $transkrip): View
    {
        $transkrip->load([
            'mahasiswa.fakultas',
            'mahasiswa.programStudi',
            'mahasiswa.siakadNilais.mataKuliah',
            'wisudawan',
            'verifiedByJurusan',
            'ratifiedByAdmin',
            'histories.user',
        ]);

        return view('super_admin.transkrip-show', compact('transkrip'));
    }

    /**
     * Directly create a new Admin Fakultas account.
     */
    public function storeAdminFakultas(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', Password::defaults()],
            'fakultas_id' => ['required', 'exists:fakultas,id'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin_fakultas',
            'fakultas_id' => $request->fakultas_id,
            'status_aktif' => true,
        ]);

        $fakultas = Fakultas::find($request->fakultas_id);

        AuditLog::record(
            'Pembuatan Akun Admin Fakultas',
            'User',
            (string) $user->id,
            'Super Admin '.Auth::user()->name.' membuat akun Admin Fakultas untuk '.$user->name.' ('.$user->email.') pada fakultas: '.($fakultas?->nama_fakultas ?? '-')
        );

        return back()->with('success', 'Akun Admin Fakultas untuk '.$user->name.' berhasil dibuat.');
    }

    /**
     * Approve an account request.
     */
    public function approveRequest(Request $request, AdminFakultasRequest $req): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string', Password::defaults()],
            'catatan_super_admin' => ['nullable', 'string', 'max:500'],
        ]);

        if ($req->status !== 'pending') {
            return back()->withErrors(['error' => 'Permintaan ini sudah diproses sebelumnya.']);
        }

        $existingUser = User::where('email', $req->email)->first();
        if ($existingUser) {
            $existingUser->update([
                'role' => 'admin_fakultas',
                'fakultas_id' => $req->fakultas_id,
                'status_aktif' => true,
                'password' => Hash::make($request->password),
            ]);
            $user = $existingUser;
        } else {
            $user = User::create([
                'name' => $req->nama,
                'email' => $req->email,
                'password' => Hash::make($request->password),
                'role' => 'admin_fakultas',
                'fakultas_id' => $req->fakultas_id,
                'status_aktif' => true,
            ]);
        }

        $req->update([
            'status' => 'approved',
            'created_user_id' => $user->id,
            'catatan_super_admin' => $request->catatan_super_admin,
        ]);

        AuditLog::record(
            'Persetujuan Permintaan Akun Admin Fakultas',
            'AdminFakultasRequest',
            (string) $req->id,
            'Super Admin '.Auth::user()->name.' menyetujui permintaan pembuatan akun untuk '.$req->nama.' ('.$req->email.'). Akun Admin Fakultas berhasil dibuat.'
        );

        return back()->with('success', 'Permintaan akun disetujui. Akun Admin Fakultas untuk '.$req->nama.' telah berhasil diaktifkan.');
    }

    /**
     * Reject an account request.
     */
    public function rejectRequest(Request $request, AdminFakultasRequest $req): RedirectResponse
    {
        $request->validate([
            'catatan_super_admin' => ['required', 'string', 'max:500'],
        ], [
            'catatan_super_admin.required' => 'Mohon berikan alasan penolakan permintaan.',
        ]);

        $req->update([
            'status' => 'rejected',
            'catatan_super_admin' => $request->catatan_super_admin,
        ]);

        AuditLog::record(
            'Penolakan Permintaan Akun Admin Fakultas',
            'AdminFakultasRequest',
            (string) $req->id,
            'Super Admin '.Auth::user()->name.' menolak permintaan akun dari '.$req->nama.' ('.$req->email.'). Alasan: '.$request->catatan_super_admin
        );

        return back()->with('success', 'Permintaan akun dari '.$req->nama.' telah ditolak.');
    }

    /**
     * Toggle active status of an Admin Fakultas.
     */
    public function toggleStatus(User $user): RedirectResponse
    {
        if ($user->id === Auth::id() || $user->isSuperAdmin()) {
            return back()->withErrors(['error' => 'Status Super Admin tidak dapat diubah di sini.']);
        }

        $user->status_aktif = ! $user->status_aktif;
        $user->save();

        $statusText = $user->status_aktif ? 'diaktifkan' : 'dinonaktifkan';

        AuditLog::record(
            'Ubah Status Pengguna',
            'User',
            (string) $user->id,
            'Super Admin '.Auth::user()->name.' mengubah status akun '.$user->name.' menjadi '.$statusText
        );

        return back()->with('success', "Akun {$user->name} berhasil {$statusText}.");
    }

    /**
     * Delete user.
     */
    public function destroyAdmin(User $user): RedirectResponse
    {
        if ($user->id === Auth::id() || $user->isSuperAdmin()) {
            return back()->withErrors(['error' => 'Tidak dapat menghapus akun Super Admin.']);
        }

        $nama = $user->name;
        $user->delete();

        AuditLog::record(
            'Hapus Akun Pengguna',
            'User',
            (string) $user->id,
            'Super Admin '.Auth::user()->name.' menghapus akun: '.$nama
        );

        return back()->with('success', "Akun {$nama} berhasil dihapus.");
    }

    /**
     * Update grade weight rule (Aturan Bobot Nilai).
     */
    public function updateBobotNilai(Request $request, AturanBobotNilai $bobot): RedirectResponse
    {
        $request->validate([
            'bobot' => ['required', 'numeric', 'min:0', 'max:4'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);

        $oldBobot = $bobot->bobot;
        $bobot->update([
            'bobot' => $request->bobot,
            'keterangan' => $request->keterangan,
        ]);

        AuditLog::record(
            'Pembaruan Bobot Nilai',
            'AturanBobotNilai',
            (string) $bobot->id,
            "Mengubah bobot nilai {$bobot->nilai_huruf} dari {$oldBobot} menjadi {$request->bobot}"
        );

        return back()->with('success', "Bobot nilai {$bobot->nilai_huruf} berhasil diperbarui.");
    }

    /**
     * Test & sync SIAKAD Program Studi using endpoint: GET https://bridge.uinssc.ac.id/api/program_studi?fakultas=2
     */
    public function testSiakadSync(Request $request): RedirectResponse
    {
        $service = app(SiakadApiService::class);
        $result = $service->syncProgramStudiFromBridge(2);

        $status = $result['success'] ? 'sukses' : 'gagal';
        $totalBerhasil = $result['count'] ?? 0;
        $totalGagal = $result['success'] ? 0 : 1;
        $totalDiperbarui = $result['updated_count'] ?? $totalBerhasil;
        $pesanError = $result['success'] ? null : ($result['message'] ?? 'Gagal menghubungi server Bridge API.');

        $log = SiakadSyncLog::create([
            'user_id' => Auth::id(),
            'fakultas_id' => 2,
            'endpoint' => 'https://bridge.uinssc.ac.id/api/program_studi?fakultas=2',
            'status' => $status,
            'total_berhasil' => $totalBerhasil,
            'total_gagal' => $totalGagal,
            'total_diperbarui' => $totalDiperbarui,
            'total_prodi' => $totalBerhasil,
            'total_mahasiswa' => 0,
            'total_nilai' => 0,
            'konflik_count' => 0,
            'keterangan' => $result['message'] ?? 'Sinkronisasi Bridge API Program Studi fakultas 2.',
            'pesan_error' => $pesanError,
        ]);

        AuditLog::record(
            'Sinkronisasi Manual SIAKAD',
            'SiakadSyncLog',
            (string) $log->id,
            "Super Admin memicu sinkronisasi manual: {$status}. Berhasil: {$totalBerhasil}, Gagal: {$totalGagal}, Diperbarui: {$totalDiperbarui}."
        );

        $syncSummary = [
            'waktu' => now()->translatedFormat('d F Y H:i:s'),
            'sumber' => 'API Bridge PTIPD (https://bridge.uinssc.ac.id/api/program_studi?fakultas=2)',
            'status' => $status,
            'total_berhasil' => $totalBerhasil,
            'total_gagal' => $totalGagal,
            'total_diperbarui' => $totalDiperbarui,
            'pesan' => $result['message'] ?? '',
        ];

        session()->flash('sync_summary', $syncSummary);

        if ($result['success']) {
            return back()->with('success', "Sinkronisasi selesai. Berhasil: {$totalBerhasil} prodi, Diperbarui: {$totalDiperbarui} data.");
        }

        return back()->with('warning', 'Sinkronisasi selesai dengan catatan: '.($result['message'] ?? 'Data lokal tetap aman dan tidak ditimpa.'));
    }

    /**
     * Super Admin performs final ratification of transcript.
     */
    public function ratifyTranskrip(Request $request, Transkrip $transkrip): RedirectResponse
    {
        $request->validate([
            'action' => ['required', 'in:sah,tolak'],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($request->action === 'sah') {
            $transkrip->ratified_by_admin_id = Auth::id();
            $transkrip->ratified_by_admin_at = now();
            $transkrip->recordStatusChange(Auth::id(), 'disahkan', $request->catatan ?: 'Pengesahan akhir transkrip resmi oleh Super Admin.');

            if ($transkrip->wisudawan) {
                $transkrip->wisudawan->update(['status_validasi' => 'disahkan']);
            }

            AuditLog::record(
                'Pengesahan Akhir Transkrip',
                'Transkrip',
                (string) $transkrip->id,
                "Super Admin mengesahkan transkrip resmi NIM {$transkrip->mahasiswa->nim} ({$transkrip->kode_verifikasi})."
            );

            return back()->with('success', "Transkrip NIM {$transkrip->mahasiswa->nim} berhasil disahkan dan resmi diterbitkan.");
        }

        // Action: tolak / kembalikan untuk perbaikan
        $transkrip->recordStatusChange(Auth::id(), 'perlu_perbaikan', $request->catatan ?: 'Dikembalikan oleh Super Admin untuk perbaikan data kelulusan.');

        if ($transkrip->wisudawan) {
            $transkrip->wisudawan->update(['status_validasi' => 'perlu_perbaikan']);
        }

        AuditLog::record(
            'Penolakan Pengesahan Transkrip',
            'Transkrip',
            (string) $transkrip->id,
            "Super Admin mengembalikan transkrip NIM {$transkrip->mahasiswa->nim} dengan catatan: {$request->catatan}."
        );

        return back()->with('warning', 'Transkrip dikembalikan ke Admin Jurusan dengan catatan perbaikan.');
    }

    /**
     * Read-only Mahasiswa directory for Super Admin.
     */
    public function mahasiswa(Request $request): View
    {
        $query = Mahasiswa::with(['fakultas', 'programStudi', 'transkrip']);

        if ($request->filled('fakultas_id')) {
            $query->where('fakultas_id', $request->fakultas_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        $mahasiswas = $query->latest()->paginate(15)->withQueryString();
        $fakultasList = Fakultas::orderBy('nama_fakultas')->get();

        return view('super_admin.mahasiswa', compact('mahasiswas', 'fakultasList'));
    }

    /**
     * Read-only Wisudawan directory for Super Admin.
     */
    public function wisudawan(Request $request): View
    {
        $query = Wisudawan::with(['mahasiswa.fakultas', 'mahasiswa.programStudi', 'transkrip']);

        if ($request->filled('periode_wisuda')) {
            $query->where('periode_wisuda', $request->periode_wisuda);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nim', 'like', "%{$search}%")
                    ->orWhere('nama', 'like', "%{$search}%")
                    ->orWhere('no_sk', 'like', "%{$search}%")
                    ->orWhere('no_ijazah', 'like', "%{$search}%")
                    ->orWhere('judul_skripsi', 'like', "%{$search}%")
                    ->orWhereHas('mahasiswa', function ($m) use ($search) {
                        $m->where('nama', 'like', "%{$search}%");
                    });
            });
        }

        $wisudawans = $query->latest()->paginate(15)->withQueryString();
        $periodes = Wisudawan::distinct()->pluck('periode_wisuda')->filter();

        return view('super_admin.wisudawan', compact('wisudawans', 'periodes'));
    }
}
