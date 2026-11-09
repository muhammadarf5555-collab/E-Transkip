<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('super-admin.dashboard') }}" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    </a>
                    <h2 class="font-bold text-2xl text-gray-900 dark:text-white leading-tight">
                        {{ __('Supervisi Transkrip Nilai') }}
                    </h2>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800 dark:bg-indigo-900/60 dark:text-indigo-300">
                        Mode Pengawas (Read-Only)
                    </span>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Inspeksi arsip transkrip nilai mahasiswa secara komprehensif.
                </p>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('super-admin.transkrip.pdf', ['transkrip' => $transkrip->id, 'mode' => 'draft']) }}" target="_blank" class="inline-flex items-center px-3.5 py-2 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 text-xs font-bold rounded-xl shadow-sm transition duration-150 gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Draft Transkrip (PDF)
                </a>
                <a href="{{ route('super-admin.transkrip.pdf', ['transkrip' => $transkrip->id, 'mode' => 'final']) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-sm transition duration-150 gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Cetak PDF Resmi (Siap Cetak)
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Super Admin Action / Notice -->
    @if ($transkrip->isDisahkan())
        <div class="mb-6 p-5 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-900 dark:text-emerald-200 text-sm flex items-start justify-between gap-4 shadow-sm">
            <div class="flex items-start gap-3">
                <svg class="w-6 h-6 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                <div>
                    <h4 class="font-bold text-base text-emerald-950 dark:text-emerald-100">Transkrip Resmi Telah Disahkan</h4>
                    <p class="text-xs text-emerald-800 dark:text-emerald-300 mt-0.5">
                        Dokumen telah disahkan oleh <span class="font-bold">{{ $transkrip->ratifiedByAdmin?->name ?? 'Admin Sistem' }}</span> pada <span class="font-mono">{{ $transkrip->ratified_by_admin_at?->format('d/m/Y H:i') }} WIB</span>. Tanda tangan Rektor Prof. Dr. H. Aan Jaelani, M.Ag. (NIP. 197506012005011008) aktif dan sah.
                    </p>
                </div>
            </div>
            <a href="{{ route('super-admin.transkrip.pdf', $transkrip->id) }}" target="_blank" class="shrink-0 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition shadow-sm">
                Lihat PDF Resmi
            </a>
        </div>
    @elseif ($transkrip->isMenungguPengesahan())
        <div class="mb-6 p-6 rounded-2xl bg-white dark:bg-gray-800 border-2 border-indigo-200 dark:border-indigo-800 shadow-md">
            <div class="flex items-start justify-between pb-4 border-b border-gray-100 dark:border-gray-700">
                <div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-[#2563EB] mb-2">
                        MENUNGGU PENGESAHAN AKHIR
                    </span>
                    <h3 class="text-lg font-black text-gray-900 dark:text-white">Pengesahan Akhir Transkrip Akademik Resmi</h3>
                    <p class="text-xs text-gray-500 mt-1">
                        Transkrip telah diverifikasi oleh <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $transkrip->verifiedByJurusan?->name ?? 'Admin Fakultas' }}</span> pada <span class="font-mono">{{ $transkrip->verified_by_jurusan_at?->format('d/m/Y H:i') }} WIB</span>. Sebagai Super Admin, Anda memiliki hak prerogatif melakukan pengesahan akhir dokumen transkrip bertanda tangan Rektor.
                    </p>
                </div>
            </div>

            <form method="POST" action="{{ route('super-admin.transkrip.ratify', $transkrip->id) }}" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Catatan Pengesahan / Catatan Revisi (Opsional)</label>
                    <textarea name="catatan" rows="2" class="w-full text-sm rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Tambahkan catatan resmi jika diperlukan..."></textarea>
                </div>
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="submit" name="action" value="tolak" class="px-4 py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold rounded-xl border border-rose-200 transition">
                        Kembalikan untuk Perbaikan
                    </button>
                    <button type="submit" name="action" value="sah" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-md shadow-emerald-600/20 transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Sahkan Transkrip Final & Terbitkan
                    </button>
                </div>
            </form>
        </div>
    @else
        <div class="mb-6 p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-amber-900 dark:text-amber-200 text-xs flex items-center gap-3">
            <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <div>
                <span class="font-bold">Mode Pengawasan:</span>
                Transkrip ini berstatus <span class="font-semibold">{{ $transkrip->status_validasi }}</span>. Pengesahan akhir oleh Super Admin dapat dilakukan setelah Admin Fakultas memverifikasi berkas menjadi status <em>Menunggu Pengesahan</em>.
            </div>
        </div>
    @endif

    <!-- Student Biodata Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6 mb-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between pb-6 border-b border-gray-100 dark:border-gray-700 gap-4">
            <div>
                <span class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider">Identitas Mahasiswa</span>
                <h3 class="text-2xl font-black text-gray-900 dark:text-white mt-1">{{ $transkrip->mahasiswa->nama }}</h3>
                <p class="text-sm font-mono text-gray-500">NIM: {{ $transkrip->mahasiswa->nim }}</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="text-right">
                    <div class="text-xs text-gray-500">Status Validasi</div>
                    <div class="mt-1">
                        @if ($transkrip->status_validasi === 'Valid')
                            <span class="px-3 py-1 text-xs font-bold rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                                Valid (Disahkan)
                            </span>
                        @elseif ($transkrip->status_validasi === 'Menunggu Validasi')
                            <span class="px-3 py-1 text-xs font-bold rounded-full bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300">
                                Menunggu Validasi
                            </span>
                        @elseif ($transkrip->status_validasi === 'Ditolak')
                            <span class="px-3 py-1 text-xs font-bold rounded-full bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300">
                                Ditolak
                            </span>
                        @else
                            <span class="px-3 py-1 text-xs font-bold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                Draft
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 pt-6 text-sm">
            <div>
                <span class="text-xs text-gray-400">Fakultas</span>
                <p class="font-semibold text-gray-900 dark:text-white mt-0.5">{{ $transkrip->mahasiswa->fakultas?->nama_fakultas ?? '-' }}</p>
            </div>
            <div>
                <span class="text-xs text-gray-400">Program Studi / Jenjang</span>
                <p class="font-semibold text-gray-900 dark:text-white mt-0.5">{{ $transkrip->mahasiswa->programStudi?->nama_program_studi ?? '-' }} ({{ $transkrip->mahasiswa->jenjang }})</p>
            </div>
            <div>
                <span class="text-xs text-gray-400">Periode Wisuda</span>
                <p class="font-semibold text-gray-900 dark:text-white mt-0.5">{{ $transkrip->wisudawan?->periode_wisuda ?? '-' }}</p>
            </div>
            <div>
                <span class="text-xs text-gray-400">Predikat Kelulusan</span>
                <p class="font-semibold text-emerald-600 dark:text-emerald-400 mt-0.5">{{ $transkrip->wisudawan?->predikat_kelulusan ?? '-' }}</p>
            </div>
        </div>

        @if ($transkrip->wisudawan?->judul_skripsi)
            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 text-xs">
                <span class="text-gray-400">Judul Skripsi / Tugas Akhir:</span>
                <p class="font-medium text-gray-800 dark:text-gray-200 italic mt-0.5">"{{ $transkrip->wisudawan->judul_skripsi }}"</p>
            </div>
        @endif
    </div>

    <!-- Academic Records Table -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <h3 class="font-bold text-base text-gray-900 dark:text-white">Rincian Mata Kuliah & Prestasi Akademik</h3>
            <span class="text-xs text-gray-500 font-mono">Kode Verifikasi: {{ $transkrip->kode_verifikasi }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400">
                    <tr>
                        <th class="px-6 py-3.5">No</th>
                        <th class="px-6 py-3.5">Kode MK</th>
                        <th class="px-6 py-3.5">Nama Mata Kuliah</th>
                        <th class="px-6 py-3.5 text-center">Semester</th>
                        <th class="px-6 py-3.5 text-center">SKS</th>
                        <th class="px-6 py-3.5 text-center">Nilai Huruf</th>
                        <th class="px-6 py-3.5 text-center">Bobot</th>
                        <th class="px-6 py-3.5 text-right">Mutu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($transkrip->mahasiswa->siakadNilais as $index => $nilai)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition">
                            <td class="px-6 py-3.5 text-xs text-gray-400">{{ $index + 1 }}</td>
                            <td class="px-6 py-3.5 text-xs font-mono font-medium text-gray-900 dark:text-white">{{ $nilai->mataKuliah?->kode_mata_kuliah ?? '-' }}</td>
                            <td class="px-6 py-3.5 text-sm font-medium text-gray-800 dark:text-gray-200">{{ $nilai->mataKuliah?->nama_mata_kuliah ?? '-' }}</td>
                            <td class="px-6 py-3.5 text-center text-xs">{{ $nilai->semester }}</td>
                            <td class="px-6 py-3.5 text-center text-xs font-bold">{{ $nilai->sks }}</td>
                            <td class="px-6 py-3.5 text-center">
                                <span class="px-2 py-0.5 text-xs font-bold rounded bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300">
                                    {{ $nilai->nilai_huruf }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5 text-center text-xs">{{ number_format($nilai->bobot, 2) }}</td>
                            <td class="px-6 py-3.5 text-right text-xs font-bold text-gray-900 dark:text-white">
                                {{ number_format($nilai->sks * $nilai->bobot, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-sm text-gray-500">
                                Belum ada data nilai yang tercatat untuk mahasiswa ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-50 dark:bg-gray-700/50 text-xs font-bold text-gray-900 dark:text-white">
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-right uppercase tracking-wider">Total Kumulatif:</td>
                        <td class="px-6 py-4 text-center text-base">{{ $transkrip->total_sks }} SKS</td>
                        <td colspan="2" class="px-6 py-4 text-right uppercase tracking-wider">Total Mutu:</td>
                        <td class="px-6 py-4 text-right text-base">{{ number_format($transkrip->total_mutu, 2) }}</td>
                    </tr>
                    <tr class="bg-indigo-50/50 dark:bg-indigo-950/30">
                        <td colspan="4" class="px-6 py-4 text-right uppercase tracking-wider text-indigo-700 dark:text-indigo-300">Indeks Prestasi Kumulatif (IPK):</td>
                        <td colspan="4" class="px-6 py-4 text-right text-2xl font-black text-indigo-700 dark:text-indigo-300">
                            {{ number_format($transkrip->ipk, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Riwayat Perubahan Status & Audit Pengesahan -->
    <div class="mt-8 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6">
        <h3 class="text-base font-bold text-gray-900 dark:text-white pb-3 border-b border-gray-100 dark:border-gray-700 mb-4 flex items-center justify-between">
            <span>Riwayat Perubahan Status & Pengesahan Dokumen</span>
            <span class="text-xs font-normal text-gray-500">{{ $transkrip->histories->count() }} Perubahan Tercatat</span>
        </h3>

        @if ($transkrip->verifiedByJurusan || $transkrip->ratifiedByAdmin)
            <div class="mb-5 grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                @if ($transkrip->verifiedByJurusan)
                    <div class="p-3.5 rounded-xl bg-blue-50/70 dark:bg-blue-950/30 border border-blue-200 dark:border-blue-900">
                        <span class="font-bold text-[#2563EB]">Diverifikasi oleh Fakultas/Jurusan:</span>
                        <div class="font-semibold text-gray-800 dark:text-gray-200 mt-1">{{ $transkrip->verifiedByJurusan->name }}</div>
                        <div class="text-gray-500 font-mono text-[11px]">{{ $transkrip->verified_by_jurusan_at?->format('d/m/Y H:i') }} WIB</div>
                    </div>
                @endif
                @if ($transkrip->ratifiedByAdmin)
                    <div class="p-3.5 rounded-xl bg-emerald-50/70 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-900">
                        <span class="font-bold text-emerald-700 dark:text-emerald-400">Disahkan Akhir oleh Super Admin:</span>
                        <div class="font-semibold text-gray-800 dark:text-gray-200 mt-1">{{ $transkrip->ratifiedByAdmin->name }}</div>
                        <div class="text-gray-500 font-mono text-[11px]">{{ $transkrip->ratified_by_admin_at?->format('d/m/Y H:i') }} WIB</div>
                    </div>
                @endif
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-gray-600 dark:text-gray-300">
                <thead class="text-[11px] uppercase bg-gray-50 dark:bg-gray-700/50 text-gray-500">
                    <tr>
                        <th class="px-4 py-2.5 rounded-l-lg">Waktu</th>
                        <th class="px-4 py-2.5">Oleh</th>
                        <th class="px-4 py-2.5">Status Sebelumnya</th>
                        <th class="px-4 py-2.5">Status Terbaru</th>
                        <th class="px-4 py-2.5 rounded-r-lg">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($transkrip->histories as $h)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-4 py-2.5 font-mono text-gray-500 whitespace-nowrap">{{ $h->created_at->format('d/m/Y H:i:s') }}</td>
                            <td class="px-4 py-2.5 font-semibold text-gray-800 dark:text-gray-200">{{ $h->user?->name ?? 'Sistem' }}</td>
                            <td class="px-4 py-2.5">
                                <span class="px-2 py-0.5 rounded text-[11px] bg-gray-100 text-gray-600">{{ $h->status_sebelumnya ?? '-' }}</span>
                            </td>
                            <td class="px-4 py-2.5">
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ in_array($h->status_terbaru, ['disahkan', 'diterbitkan', 'Valid']) ? 'bg-emerald-100 text-emerald-800' : (in_array($h->status_terbaru, ['perlu_perbaikan', 'ditolak']) ? 'bg-rose-100 text-rose-800' : 'bg-blue-100 text-blue-800') }}">
                                    {{ $h->status_terbaru }}
                                </span>
                            </td>
                            <td class="px-4 py-2.5 text-gray-600 dark:text-gray-300">{{ $h->catatan ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-4 text-center text-gray-400">Belum ada riwayat perubahan status transkrip.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>

