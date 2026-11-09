<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-white leading-tight">
                    {{ __('Kelola Data Wisudawan & E-Wisuda API') }}
                </h2>
                <p class="text-sm text-emerald-100 dark:text-gray-400 mt-1">
                    Integrasi data administrasi lulusan (SK Yudisium, Ijazah, IPK, Lama Studi &amp; Gelar) dari E-Wisuda UINSSC.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2.5">
                <button @click="$dispatch('open-modal', 'sync-ewisuda')" class="px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-xs sm:text-sm font-bold rounded-xl shadow-md transition gap-2 inline-flex items-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Tarik Data E-Wisuda (API)
                </button>
                <a href="{{ route('admin.wisudawan.template') }}" class="px-3.5 py-2.5 bg-white/15 hover:bg-white/25 text-white text-xs sm:text-sm font-semibold rounded-xl border border-white/25 transition gap-1.5 inline-flex items-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Template CSV
                </a>
                <button @click="$dispatch('open-modal', 'import-wisudawan')" class="px-3.5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs sm:text-sm font-semibold rounded-xl shadow-sm transition gap-1.5 inline-flex items-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    Import CSV
                </button>
                <a href="{{ route('admin.wisudawan.create') }}" class="px-4 py-2.5 bg-white text-emerald-900 hover:bg-emerald-50 text-xs sm:text-sm font-bold rounded-xl shadow-sm transition gap-1.5 inline-flex items-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Manual
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Flash Alerts -->
    @if (session('success'))
        <div class="mb-5 p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-sm font-semibold flex items-center justify-between">
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if (session('error'))
        <div class="mb-5 p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-sm font-semibold flex items-center justify-between">
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- E-Wisuda API Integration Status Card -->
    @if (isset($ewisudaStatus))
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-4 mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-start gap-3.5">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 {{ $ewisudaStatus['connected'] ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-300' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">E-Wisuda - Data Administrasi API</h3>
                        @if ($ewisudaStatus['connected'])
                            <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300">
                                Terhubung (200 OK)
                            </span>
                        @else
                            <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded-full bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-300">
                                Siap Sinkronisasi (Header X-Ewisuda-Key)
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        Endpoint: <code class="font-mono text-[11px] bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded text-emerald-700 dark:text-emerald-300">GET {{ config('services.ewisuda.base_url') }}/api/administrative?nims=...</code>
                    </p>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">
                        {{ $ewisudaStatus['message'] }}
                    </p>
                </div>
            </div>
            <div class="shrink-0">
                <button @click="$dispatch('open-modal', 'sync-ewisuda')" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-sm transition inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Lookup &amp; Sinkronkan NIM
                </button>
            </div>
        </div>
    @endif

    <!-- Search & Filter Bar -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-4 mb-6">
        <form method="GET" action="{{ route('admin.wisudawan.index') }}" class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[240px]">
                <x-text-input name="search" type="text" placeholder="Cari NIM, Nama, No SK Yudisium, No Ijazah, atau Skripsi..." value="{{ request('search') }}" class="w-full text-sm" />
            </div>
            <div class="w-48">
                <select name="periode" onchange="this.form.submit()" class="w-full text-sm rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">Semua Periode</option>
                    @foreach ($periodes as $p)
                        <option value="{{ $p }}" {{ request('periode') == $p ? 'selected' : '' }}>{{ $p }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-44">
                <select name="status" onchange="this.form.submit()" class="w-full text-sm rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">Semua Status</option>
                    <option value="Draft" {{ request('status') == 'Draft' ? 'selected' : '' }}>Draft</option>
                    <option value="Menunggu Validasi" {{ request('status') == 'Menunggu Validasi' ? 'selected' : '' }}>Menunggu Validasi</option>
                    <option value="Valid" {{ request('status') == 'Valid' ? 'selected' : '' }}>Valid</option>
                    <option value="Ditolak" {{ request('status') == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>
            <x-primary-button>Cari</x-primary-button>
            @if (request()->hasAny(['search', 'periode', 'status']))
                <a href="{{ route('admin.wisudawan.index') }}" class="text-xs text-rose-500 hover:underline">Reset</a>
            @endif
        </form>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400">
                    <tr>
                        <th class="px-4 py-3 rounded-l-lg">Mahasiswa &amp; Gelar</th>
                        <th class="px-4 py-3">SK Yudisium &amp; Ijazah</th>
                        <th class="px-4 py-3">IPK / SKS / Studi</th>
                        <th class="px-4 py-3">Periode &amp; Predikat</th>
                        <th class="px-4 py-3 text-center">Eligible &amp; Status</th>
                        <th class="px-4 py-3 rounded-r-lg text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($wisudawans as $w)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30">
                            <td class="px-4 py-3.5">
                                <div class="font-bold text-gray-900 dark:text-white">
                                    {{ $w->mahasiswa?->nama ?? $w->nama ?? 'Nama Belum Ditautkan' }}
                                    @if ($w->gelar)
                                        <span class="text-emerald-600 dark:text-emerald-400 font-extrabold">, {{ $w->gelar }}</span>
                                    @endif
                                </div>
                                <div class="text-xs font-mono text-gray-500">NIM: {{ $w->nim }}</div>
                                <div class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">
                                    {{ $w->program_studi ?? $w->mahasiswa?->programStudi?->nama_program_studi ?? '-' }}
                                    @if ($w->gelar_full)
                                        <span class="text-gray-400">({{ $w->gelar_full }})</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3.5 text-xs">
                                @if ($w->no_sk)
                                    <div class="font-mono font-semibold text-gray-800 dark:text-gray-200">SK: {{ $w->no_sk }}</div>
                                    <div class="text-[11px] text-gray-400">Tgl SK: {{ $w->tanggal_sk?->format('d/m/Y') ?? '-' }}</div>
                                @else
                                    <div class="text-gray-400">SK: -</div>
                                @endif
                                @if ($w->no_ijazah)
                                    <div class="font-mono font-semibold text-indigo-600 dark:text-indigo-400 mt-1">IJZ: {{ $w->no_ijazah }}</div>
                                    <div class="text-[11px] text-gray-400">Tgl Ijazah: {{ $w->tanggal_ijazah?->format('d/m/Y') ?? '-' }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-xs">
                                <div class="font-bold text-emerald-600 dark:text-emerald-400 text-sm">
                                    IPK {{ number_format((float) ($w->ipk ?? $w->transkrip?->ipk ?? 0), 2) }}
                                </div>
                                <div class="text-gray-600 dark:text-gray-300 font-medium">
                                    {{ $w->total_sks ?? $w->transkrip?->total_sks ?? '-' }} SKS
                                </div>
                                @if ($w->lama_studi || $w->semester_lulus)
                                    <div class="text-[11px] text-gray-400 mt-0.5">
                                        Studi: {{ $w->lama_studi ?? '-' }} Smt (Lulus Smt {{ $w->semester_lulus ?? '-' }})
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-xs">
                                <div class="font-semibold text-gray-800 dark:text-gray-200">{{ $w->periode_wisuda }}</div>
                                <div class="text-indigo-600 dark:text-indigo-400 font-bold mt-0.5">{{ $w->predikat_kelulusan ?? '-' }}</div>
                                @if ($w->judul_skripsi)
                                    <div class="text-[11px] text-gray-400 max-w-[200px] truncate mt-0.5" title="{{ $w->judul_skripsi }}">
                                        "{{ $w->judul_skripsi }}"
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-center space-y-1">
                                <div>
                                    @if ($w->is_eligible ?? true)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/50 dark:text-emerald-300 dark:border-emerald-800">
                                            Eligible Yudisium
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            Belum Eligible
                                        </span>
                                    @endif
                                </div>
                                <div>
                                    @if ($w->status_validasi === 'Valid' || $w->status_validasi === 'disahkan')
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">Valid</span>
                                    @elseif ($w->status_validasi === 'Menunggu Validasi' || $w->status_validasi === 'menunggu_pengesahan')
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300">Menunggu</span>
                                    @elseif ($w->status_validasi === 'Ditolak')
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300">Ditolak</span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">{{ $w->status_validasi }}</span>
                                    @endif
                                </div>
                                <div class="text-[10px] font-mono text-gray-400">{{ $w->sumber_data }}</div>
                            </td>
                            <td class="px-4 py-3.5 text-right space-x-1.5 whitespace-nowrap">
                                <a href="{{ route('admin.wisudawan.edit', $w) }}" class="px-2.5 py-1.5 text-xs font-semibold rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-200 transition">
                                    Edit
                                </a>
                                <form action="{{ route('admin.wisudawan.destroy', $w) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data wisudawan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1.5 text-xs font-semibold rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 dark:bg-rose-950/50 dark:hover:bg-rose-900/50 dark:text-rose-400 transition">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-400 text-xs">
                                Belum ada data wisudawan terdaftar. Klik tombol <strong>"Tarik Data E-Wisuda (API)"</strong> di atas untuk mengambil data lulusan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $wisudawans->links() }}
        </div>
    </div>

    <!-- Modal Sinkronisasi E-Wisuda API -->
    <x-modal name="sync-ewisuda" focusable>
        <form method="POST" action="{{ route('admin.wisudawan.sync-ewisuda') }}" class="p-6 space-y-5">
            @csrf
            <div class="border-b border-gray-100 dark:border-gray-700 pb-3">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                    Tarik Data Administrasi Lulusan (E-Wisuda API)
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Mengambil data SK Yudisium, Nomor Ijazah, IPK, Total SKS, Lama Studi, Gelar, dan Predikat Kelulusan per NIM melalui <code class="font-mono text-emerald-600">GET /api/administrative?nims=...</code>
                </p>
            </div>

            <div>
                <x-input-label for="nims" value="Daftar NIM Mahasiswa (Pisahkan dengan koma)" />
                <textarea id="nims" name="nims" rows="3" class="mt-1 block w-full text-sm font-mono rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-emerald-500 focus:ring-emerald-500" placeholder="Contoh: 2388010001,2388010002,201410001">{{ old('nims', $defaultSyncNimsString ?? '2388010001,2388010002,2388010017') }}</textarea>
                <p class="text-[11px] text-gray-500 mt-1">
                    Kosongkan kolom ini jika ingin menarik otomatis daftar NIM mahasiswa tingkat akhir di fakultas Anda.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="periode_wisuda_sync" value="Periode Wisuda" />
                    <x-text-input id="periode_wisuda_sync" name="periode_wisuda" type="text" class="mt-1 block w-full text-sm" value="Wisuda Ganjil {{ date('Y') }}" required />
                </div>
                <div>
                    <x-input-label for="ewisuda_api_key" value="Header X-Ewisuda-Key (Opsional)" />
                    <x-text-input id="ewisuda_api_key" name="ewisuda_api_key" type="text" class="mt-1 block w-full text-sm font-mono" value="{{ config('services.ewisuda.api_key') }}" placeholder="Isi jika menggunakan API Key baru..." />
                </div>
            </div>

            <div class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-xs text-emerald-800 dark:text-emerald-200">
                <strong>Field E-Wisuda yang Disinkronkan:</strong>
                <span class="font-mono text-[11px] block mt-0.5">
                    nim, nama, fakultas, no_sk, tanggal_sk, no_ijazah, tanggal_ijazah, ipk, lama_studi, semester_lulus, gelar, gelar_full, predikat_lulus, total_sks, is_eligible
                </span>
            </div>

            <div class="mt-6 flex justify-end gap-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                <x-primary-button class="bg-emerald-600 hover:bg-emerald-700">
                    Tarik &amp; Sinkronkan Data Wisudawan
                </x-primary-button>
            </div>
        </form>
    </x-modal>

    <!-- Modal Import Wisudawan CSV -->
    <x-modal name="import-wisudawan" focusable>
        <form method="POST" action="{{ route('admin.wisudawan.import') }}" enctype="multipart/form-data" class="p-6">
            @csrf
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Import Data Wisudawan (CSV)</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-6">
                Format kolom CSV: <span class="font-mono bg-gray-100 dark:bg-gray-700 px-1 py-0.5 rounded">NIM, Periode_Wisuda, Judul_Skripsi, Predikat_Kelulusan, Nomor_Pendaftaran</span>.
            </p>

            <div>
                <x-input-label for="file_wisuda" value="Pilih File CSV (.csv)" />
                <input id="file_wisuda" name="file" type="file" accept=".csv,.txt" class="mt-2 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" required />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                <x-primary-button class="bg-indigo-600 hover:bg-indigo-700">Mulai Import</x-primary-button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
