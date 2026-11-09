<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-[#142B4A] dark:text-white leading-tight">
                    {{ __('Pengelolaan Nilai Akademik Mahasiswa') }}
                </h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Kelola nilai akademik sumber SIAKAD & Manual dengan deteksi konflik otomatis dan riwayat perubahan.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('admin.nilai.create') }}" style="background-color: #059669 !important; color: #ffffff !important;" class="inline-flex items-center px-4 py-2.5 text-xs font-bold rounded-xl shadow-md transition gap-2 hover:opacity-90">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Input Nilai Manual
                </a>
                <a href="{{ route('admin.nilai.mass') }}" style="background-color: #2563EB !important; color: #ffffff !important;" class="inline-flex items-center px-4 py-2.5 text-xs font-bold rounded-xl shadow-md transition gap-2 hover:opacity-90">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Input Nilai Massal
                </a>
                <a href="{{ route('admin.siakad.nilai') }}" class="inline-flex items-center px-4 py-2.5 bg-blue-50 hover:bg-blue-100 text-[#2563EB] text-xs font-bold rounded-xl border border-blue-200 transition gap-2">
                    <svg class="w-4 h-4 text-[#2563EB]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Data Nilai SIAKAD
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Alerts -->
    @if (session('success'))
        <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3 shadow-sm">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Informasi Integritas Nilai SIAKAD -->
    <div class="mb-6 p-4 rounded-2xl bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800 text-blue-900 dark:text-blue-200 text-xs flex items-start gap-3 shadow-sm">
        <svg class="w-5 h-5 text-[#2563EB] dark:text-blue-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <div class="leading-relaxed">
            <span class="font-bold">Ketentuan Integritas Data Nilai Akademik:</span>
            Sesuai kebijakan akademik UINSSC, nilai akademik resmi mahasiswa bersumber langsung dari <span class="font-semibold text-[#2563EB]">SIAKAD</span>. Apabila terdapat ketidaksesuaian nilai pada transkrip, perbaikan harus dilakukan melalui sistem akademik yang berwenang (SIAKAD) lalu disinkronkan kembali ke E-Transkrip guna menjaga integritas dan keabsahan dokumen transkrip.
        </div>
    </div>

    @if (session('warning'))
        <div class="mb-6 p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 text-sm flex items-center gap-3 shadow-sm">
            <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span class="font-medium">{{ session('warning') }}</span>
        </div>
    @endif

    <!-- KPI Summary Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-slate-100 dark:border-gray-700 shadow-sm">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Nilai</p>
            <h3 class="text-2xl font-black text-[#142B4A] dark:text-white mt-1">{{ number_format($stats['total_nilai']) }}</h3>
            <span class="text-xs text-slate-500">Seluruh entri akademik</span>
        </div>

        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-slate-100 dark:border-gray-700 shadow-sm">
            <p class="text-xs font-bold text-blue-500 uppercase tracking-wider">Sumber SIAKAD</p>
            <h3 class="text-2xl font-black text-blue-600 dark:text-blue-400 mt-1">{{ number_format($stats['total_siakad']) }}</h3>
            <span class="text-xs text-slate-500">Sinkronisasi resmi</span>
        </div>

        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-slate-100 dark:border-gray-700 shadow-sm">
            <p class="text-xs font-bold text-emerald-500 uppercase tracking-wider">Sumber Manual</p>
            <h3 class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ number_format($stats['total_manual']) }}</h3>
            <span class="text-xs text-slate-500">Input langsung prodi</span>
        </div>

        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-slate-100 dark:border-gray-700 shadow-sm">
            <p class="text-xs font-bold text-rose-500 uppercase tracking-wider">Konflik Nilai</p>
            <h3 class="text-2xl font-black text-rose-600 dark:text-rose-400 mt-1">{{ number_format($stats['total_konflik']) }}</h3>
            <span class="text-xs text-rose-600 font-medium">Perlu review admin</span>
        </div>
    </div>

    <!-- Filter & Search Card -->
    <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-slate-100 dark:border-gray-700 shadow-sm mb-6">
        <form method="GET" action="{{ route('admin.nilai.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
            <!-- Search -->
            <div class="lg:col-span-2">
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Cari Mahasiswa / Mata Kuliah</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik nama, NIM, atau kode mata kuliah..." class="w-full text-xs rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-[#2563EB] focus:border-transparent transition py-2 px-3" />
            </div>

            <!-- Filter Sumber Data -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Sumber Data</label>
                <select name="sumber_data" class="w-full text-xs rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-[#2563EB] transition py-2 px-3">
                    <option value="">Semua Sumber</option>
                    <option value="SIAKAD" {{ request('sumber_data') === 'SIAKAD' ? 'selected' : '' }}>SIAKAD</option>
                    <option value="Manual" {{ request('sumber_data') === 'Manual' ? 'selected' : '' }}>Manual</option>
                </select>
            </div>

            <!-- Filter Konflik -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Status Konflik</label>
                <select name="has_conflict" class="w-full text-xs rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-[#2563EB] transition py-2 px-3">
                    <option value="">Semua Status</option>
                    <option value="1" {{ request('has_conflict') == '1' ? 'selected' : '' }}>Ada Konflik Saja</option>
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-[#142B4A] hover:bg-slate-800 text-white text-xs font-bold rounded-xl transition shadow-sm">
                    Filter
                </button>
                @if (request()->hasAny(['search', 'sumber_data', 'has_conflict', 'semester']))
                    <a href="{{ route('admin.nilai.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold rounded-xl transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Grades Card -->
    <div x-data="{ editModalOpen: false, historyModalOpen: false, activeNilai: null, histories: [] }" class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-100 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-gray-700 flex items-center justify-between">
            <h3 class="font-bold text-base text-[#142B4A] dark:text-white">Daftar Nilai Akademik Mahasiswa</h3>
            <span class="text-xs text-slate-500 font-medium">{{ $nilais->total() }} entri ditemukan</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
                <thead class="text-[11px] uppercase tracking-wider bg-slate-50 dark:bg-gray-700/50 text-slate-500 font-bold border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5">Mahasiswa</th>
                        <th class="px-5 py-3.5">Mata Kuliah</th>
                        <th class="px-5 py-3.5 text-center">Semester</th>
                        <th class="px-5 py-3.5 text-center">SKS</th>
                        <th class="px-5 py-3.5 text-center">Nilai</th>
                        <th class="px-5 py-3.5 text-center">Bobot</th>
                        <th class="px-5 py-3.5 text-center">Sumber</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-gray-700 text-xs">
                    @forelse ($nilais as $n)
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-gray-700/30 transition {{ $n->has_conflict ? 'bg-rose-50/40 dark:bg-rose-950/20' : '' }}">
                            <td class="px-5 py-3.5">
                                <div class="font-bold text-slate-900 dark:text-white">{{ $n->mahasiswa?->nama ?? '-' }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">NIM: {{ $n->mahasiswa?->nim ?? '-' }}</div>
                                <div class="text-[10px] text-slate-500">{{ $n->mahasiswa?->programStudi?->nama_program_studi ?? '-' }}</div>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="font-semibold text-slate-800 dark:text-slate-200">{{ $n->mataKuliah?->nama_mata_kuliah ?? '-' }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">{{ $n->mataKuliah?->kode_mata_kuliah ?? '-' }}</div>
                            </td>
                            <td class="px-5 py-3.5 text-center font-semibold">
                                Sem. {{ $n->semester }}
                            </td>
                            <td class="px-5 py-3.5 text-center font-semibold">
                                {{ $n->sks }}
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="inline-flex items-center justify-center w-8 h-7 rounded-lg text-xs font-black shadow-sm {{ in_array($n->nilai_huruf, ['A', 'A-']) ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : (in_array($n->nilai_huruf, ['B+', 'B', 'B-']) ? 'bg-blue-100 text-blue-800 border border-blue-300' : (in_array($n->nilai_huruf, ['C+', 'C']) ? 'bg-amber-100 text-amber-800 border border-amber-300' : 'bg-rose-100 text-rose-800 border border-rose-300')) }}">
                                    {{ $n->nilai_huruf }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-center font-mono font-bold text-slate-700 dark:text-slate-300">
                                {{ number_format($n->bobot, 2) }}
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                @if ($n->sumber_data === 'SIAKAD')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                        SIAKAD
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Manual
                                    </span>
                                @endif

                                @if ($n->has_conflict)
                                    <div class="mt-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-300" title="{{ $n->conflict_details }}">
                                            ⚠️ Konflik
                                        </span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right space-x-1.5 whitespace-nowrap">
                                <button type="button" @click="activeNilai = {{ $n->toJson() }}; editModalOpen = true" class="px-2.5 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-[#2563EB] text-xs font-bold transition">
                                    Edit Nilai
                                </button>
                                @if ($n->changeHistories->count() > 0)
                                    <button type="button" @click="histories = {{ $n->changeHistories->toJson() }}; historyModalOpen = true" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition" title="Lihat {{ $n->changeHistories->count() }} riwayat perubahan">
                                        Riwayat ({{ $n->changeHistories->count() }})
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-10 text-center text-slate-400">
                                Tidak ada data nilai yang sesuai dengan kriteria pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $nilais->links() }}
        </div>

        <!-- Edit Grade Modal -->
        <div x-show="editModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div @click.outside="editModalOpen = false" class="bg-white dark:bg-gray-800 rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100 dark:border-gray-700">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="font-bold text-base text-[#142B4A] dark:text-white">Edit Nilai Akademik</h3>
                    <button type="button" @click="editModalOpen = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
                </div>

                <form :action="'{{ url('admin/nilai') }}/' + (activeNilai ? activeNilai.id : '')" method="POST" class="mt-4 space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Mata Kuliah</label>
                        <p class="text-sm font-semibold text-slate-900 dark:text-white" x-text="activeNilai?.mata_kuliah?.nama_mata_kuliah"></p>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Nilai Sekarang</label>
                            <span class="inline-flex items-center px-3 py-1 rounded-lg bg-slate-100 font-black text-sm text-slate-800" x-text="activeNilai?.nilai_huruf + ' (' + Number(activeNilai?.bobot).toFixed(2) + ')'"></span>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nilai Baru</label>
                            <select name="nilai_huruf" required class="w-full text-xs font-bold rounded-xl border-slate-200 bg-slate-50 py-2 px-3 focus:ring-2 focus:ring-[#2563EB]">
                                <option value="A">A (4.00)</option>
                                <option value="A-">A- (3.50)</option>
                                <option value="B+">B+ (3.25)</option>
                                <option value="B">B (3.00)</option>
                                <option value="B-">B- (2.75)</option>
                                <option value="C+">C+ (2.25)</option>
                                <option value="C">C (2.00)</option>
                                <option value="D">D (1.00)</option>
                                <option value="E">E (0.00)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Alasan Perubahan Nilai *</label>
                        <textarea name="alasan" required rows="3" placeholder="Contoh: Perbaikan pengisian nilai UTS/UAS berdasarkan surat keputusan prodi..." class="w-full text-xs rounded-xl border-slate-200 bg-slate-50 py-2 px-3 focus:bg-white focus:ring-2 focus:ring-[#2563EB]"></textarea>
                        <p class="text-[10px] text-slate-400 mt-1">Alasan wajib diisi dan akan dicatat secara permanen di riwayat audit perubahan nilai.</p>
                    </div>

                    <div class="pt-3 flex justify-end gap-2">
                        <button type="button" @click="editModalOpen = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-[#2563EB] hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-md shadow-blue-500/20">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- History Modal -->
        <div x-show="historyModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div @click.outside="historyModalOpen = false" class="bg-white dark:bg-gray-800 rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 dark:border-gray-700">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="font-bold text-base text-[#142B4A] dark:text-white">Riwayat Perubahan Nilai</h3>
                    <button type="button" @click="historyModalOpen = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
                </div>

                <div class="mt-4 max-h-80 overflow-y-auto space-y-3">
                    <template x-for="h in histories" :key="h.id">
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 text-xs space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-900">
                                    <span class="line-through text-rose-600" x-text="h.nilai_huruf_lama + ' (' + h.bobot_lama + ')'"></span>
                                    &rarr;
                                    <span class="text-emerald-600" x-text="h.nilai_huruf_baru + ' (' + h.bobot_baru + ')'"></span>
                                </span>
                                <span class="text-[10px] text-slate-400" x-text="new Date(h.created_at).toLocaleString('id-ID')"></span>
                            </div>
                            <p class="text-slate-600 italic" x-text="'Alasan: ' + h.alasan"></p>
                        </div>
                    </template>
                </div>

                <div class="mt-4 flex justify-end">
                    <button type="button" @click="historyModalOpen = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

