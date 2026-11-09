<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 dark:text-white leading-tight">
                    {{ __('Detail & Validasi Transkrip Nilai') }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Transkrip Akademik: {{ $transkrip->mahasiswa->nama }} ({{ $transkrip->mahasiswa->nim }})
                </p>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('admin.transkrip.pdf', ['transkrip' => $transkrip->id, 'mode' => 'draft']) }}" target="_blank" class="px-3.5 py-2 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 text-xs font-bold rounded-xl shadow-sm transition gap-1.5 inline-flex items-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Draft Transkrip (PDF)
                </a>
                <a href="{{ route('admin.transkrip.pdf', ['transkrip' => $transkrip->id, 'mode' => 'final']) }}" target="_blank" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-sm transition gap-1.5 inline-flex items-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Cetak PDF (Siap Cetak Pustikom)
                </a>
                <a href="{{ route('admin.transkrip.index') }}" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-xl transition">
                    &larr; Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Top Grid: Validation Form & QR Code -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Validation Action Card -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6">
            <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-gray-700">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Validasi Transkrip Jurusan / Fakultas</h3>
                    <p class="text-xs text-gray-500">Tentukan status keabsahan transkrip nilai akademik mahasiswa ini.</p>
                </div>
                <div>
                    @if ($transkrip->isDisahkan())
                        <span class="px-3 py-1 text-xs font-bold rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">STATUS: RESMI DISAHKAN</span>
                    @elseif ($transkrip->isMenungguPengesahan())
                        <span class="px-3 py-1 text-xs font-bold rounded-full bg-blue-100 text-[#2563EB] dark:bg-blue-950/60 dark:text-blue-300">STATUS: MENUNGGU PENGESAHAN AKHIR</span>
                    @elseif ($transkrip->isPerluPerbaikan())
                        <span class="px-3 py-1 text-xs font-bold rounded-full bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300">STATUS: PERLU PERBAIKAN</span>
                    @elseif ($transkrip->status_validasi === 'Ditolak' || $transkrip->status_validasi === 'ditolak')
                        <span class="px-3 py-1 text-xs font-bold rounded-full bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300">STATUS: DITOLAK</span>
                    @elseif ($transkrip->status_validasi === 'Draft' || $transkrip->status_validasi === 'draft')
                        <span class="px-3 py-1 text-xs font-bold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">STATUS: DRAFT</span>
                    @else
                        <span class="px-3 py-1 text-xs font-bold rounded-full bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300">STATUS: MENUNGGU VERIFIKASI</span>
                    @endif
                </div>
            </div>

            <!-- Workflow info note -->
            <div class="mt-4 p-3 rounded-xl bg-blue-50/70 dark:bg-blue-950/30 border border-blue-100 text-xs text-blue-900 dark:text-blue-200">
                <span class="font-bold">Alur Verifikasi Dua Tahap:</span>
                Admin Fakultas memverifikasi kesesuaian nilai dan data kelulusan wisudawan. Transkrip yang disetujui akan diteruskan ke <span class="font-semibold text-[#2563EB]">Admin Sistem (Super Admin)</span> untuk pengesahan akhir bertanda tangan Rektor UINSSC.
            </div>

            <form method="POST" action="{{ route('admin.transkrip.validate', $transkrip) }}" class="mt-5 space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="status_validasi" value="Pilih Keputusan Verifikasi" />
                        <select id="status_validasi" name="status_validasi" class="mt-1 block w-full text-sm rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-emerald-500 focus:ring-emerald-500" required>
                            <option value="menunggu_pengesahan" {{ $transkrip->status_validasi === 'menunggu_pengesahan' || $transkrip->status_validasi === 'Valid' ? 'selected' : '' }}>Verifikasi & Ajukan Pengesahan Akhir</option>
                            <option value="perlu_perbaikan" {{ $transkrip->status_validasi === 'perlu_perbaikan' ? 'selected' : '' }}>Kembalikan untuk Perbaikan (Revisi Data)</option>
                            <option value="ditolak" {{ $transkrip->status_validasi === 'ditolak' || $transkrip->status_validasi === 'Ditolak' ? 'selected' : '' }}>Tolak Berkas</option>
                            <option value="Draft" {{ $transkrip->status_validasi === 'Draft' ? 'selected' : '' }}>Simpan Sebagai Draf</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label for="predikat" value="Predikat Kelulusan" />
                        <x-text-input id="predikat" type="text" value="{{ $transkrip->predikat }}" class="mt-1 block w-full bg-gray-50 text-sm" disabled />
                    </div>
                </div>

                <div>
                    <x-input-label for="catatan" value="Catatan / Keterangan Validasi (Opsional)" />
                    <textarea id="catatan" name="catatan" rows="2" class="mt-1 block w-full text-sm rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-emerald-500 focus:ring-emerald-500" placeholder="Berikan catatan perbaikan jika ditolak atau catatan tambahan...">{{ old('catatan', $transkrip->catatan) }}</textarea>
                </div>

                <div class="pt-2 flex justify-end">
                    <x-primary-button class="py-2.5 px-6">
                        Simpan Keputusan Validasi
                    </x-primary-button>
                </div>
            </form>
        </div>

        <!-- Verification QR Code Card -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6 text-center flex flex-col items-center justify-center">
            <h3 class="text-sm font-bold uppercase tracking-wider text-gray-500 mb-4">QR Code Verifikasi Publik</h3>
            <div class="p-3 bg-white rounded-2xl border border-gray-200 shadow-sm inline-block">
                <img src="data:image/svg+xml;base64,{{ $qrCodeSvg }}" alt="QR Code Verifikasi" class="w-32 h-32" />
            </div>
            <div class="mt-3 font-mono text-[11px] font-bold text-gray-800 dark:text-gray-200 truncate max-w-full">
                {{ $transkrip->kode_verifikasi }}
            </div>
            <a href="{{ $qrUrl }}" target="_blank" class="mt-3 inline-flex items-center text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:underline">
                Uji Tautan Verifikasi &rarr;
            </a>
        </div>
    </div>

    <!-- Academic Metadata Grid -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6 mb-8">
        <h3 class="text-base font-bold text-gray-900 dark:text-white pb-3 border-b border-gray-100 dark:border-gray-700 mb-4">
            Ringkasan Berkas Transkrip
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 text-sm">
            <div>
                <span class="text-gray-400 text-xs block">Mahasiswa</span>
                <span class="font-bold text-gray-900 dark:text-white text-base block mt-0.5">{{ $transkrip->mahasiswa->nama }}</span>
                <span class="text-xs font-mono text-gray-500">{{ $transkrip->mahasiswa->nim }}</span>
            </div>
            <div>
                <span class="text-gray-400 text-xs block">Program Studi / Fakultas</span>
                <span class="font-semibold text-gray-800 dark:text-gray-200 block mt-0.5">{{ $transkrip->mahasiswa->programStudi?->nama_program_studi ?? '-' }}</span>
                <span class="text-xs text-gray-500">{{ $transkrip->mahasiswa->fakultas?->nama_fakultas ?? '-' }}</span>
            </div>
            <div>
                <span class="text-gray-400 text-xs block">Akumulasi Nilai</span>
                <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400 font-mono block mt-0.5">{{ number_format($transkrip->ipk, 2) }}</span>
                <span class="text-xs text-gray-500">{{ $transkrip->total_sks }} SKS / Total Mutu {{ number_format($transkrip->total_mutu, 2) }}</span>
            </div>
            <div>
                <span class="text-gray-400 text-xs block">Periode & Predikat</span>
                <span class="font-semibold text-indigo-600 dark:text-indigo-400 block mt-0.5">{{ $transkrip->predikat ?? '-' }}</span>
                <span class="text-xs text-gray-500">{{ $transkrip->wisudawan?->periode_wisuda ?? '-' }}</span>
            </div>
        </div>

        @if ($transkrip->wisudawan?->judul_skripsi)
            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                <span class="text-xs font-semibold text-gray-400 uppercase">Judul Skripsi / Tugas Akhir:</span>
                <p class="text-xs text-gray-700 dark:text-gray-300 italic mt-1 leading-relaxed">
                    "{{ $transkrip->wisudawan->judul_skripsi }}"
                </p>
            </div>
        @endif
    </div>

    <!-- Course Grades Table -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6">
        <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-gray-700">
            <div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Daftar Mata Kuliah & Nilai Akademik</h3>
                <p class="text-xs text-gray-500">Mata kuliah yang dimasukkan ke dalam perhitungan transkrip akhir.</p>
            </div>
            <span class="text-xs font-semibold text-gray-500 bg-gray-100 dark:bg-gray-700 px-3 py-1 rounded-full">
                {{ $transkrip->mahasiswa->siakadNilais->count() }} Mata Kuliah
            </span>
        </div>

        <div class="overflow-x-auto mt-4">
            <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400">
                    <tr>
                        <th class="px-4 py-3 rounded-l-lg w-12">No</th>
                        <th class="px-4 py-3">Kode</th>
                        <th class="px-4 py-3">Mata Kuliah</th>
                        <th class="px-4 py-3 text-center">Semester</th>
                        <th class="px-4 py-3 text-center">SKS</th>
                        <th class="px-4 py-3 text-center">Nilai</th>
                        <th class="px-4 py-3 text-center">Bobot</th>
                        <th class="px-4 py-3 rounded-r-lg text-right">Mutu (SKS x Bobot)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($transkrip->mahasiswa->siakadNilais as $idx => $nilai)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30">
                            <td class="px-4 py-3 text-xs text-gray-400 font-mono">{{ $idx + 1 }}</td>
                            <td class="px-4 py-3 font-mono font-bold text-xs text-gray-900 dark:text-white">{{ $nilai->mataKuliah?->kode_mata_kuliah ?? '-' }}</td>
                            <td class="px-4 py-3 font-medium text-xs text-gray-800 dark:text-gray-200">{{ $nilai->mataKuliah?->nama_mata_kuliah ?? '-' }}</td>
                            <td class="px-4 py-3 text-center text-xs">Smtr {{ $nilai->semester }}</td>
                            <td class="px-4 py-3 text-center text-xs font-bold text-gray-900 dark:text-white">{{ $nilai->sks }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 rounded text-xs font-bold bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white">
                                    {{ $nilai->nilai_huruf }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center text-xs font-mono">{{ number_format($nilai->bobot, 2) }}</td>
                            <td class="px-4 py-3 text-right text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                {{ number_format($nilai->sks * $nilai->bobot, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-400 text-xs">Belum ada mata kuliah yang terdaftar di SIAKAD.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="border-t-2 border-gray-200 dark:border-gray-700 font-bold text-xs bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <td colspan="4" class="px-4 py-3 text-right">TOTAL AKUMULASI:</td>
                        <td class="px-4 py-3 text-center text-gray-900 dark:text-white">{{ $transkrip->total_sks }} SKS</td>
                        <td colspan="2"></td>
                        <td class="px-4 py-3 text-right text-emerald-600 dark:text-emerald-400 font-mono font-black text-sm">
                            {{ number_format($transkrip->total_mutu, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Riwayat Perubahan Status & Log Pengesahan -->
    <div class="mt-8 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6">
        <h3 class="text-base font-bold text-gray-900 dark:text-white pb-3 border-b border-gray-100 dark:border-gray-700 mb-4 flex items-center justify-between">
            <span>Riwayat Perubahan Status & Audit Transkrip</span>
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
                        <span class="font-bold text-emerald-700 dark:text-emerald-400">Disahkan Akhir oleh Admin Sistem:</span>
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
