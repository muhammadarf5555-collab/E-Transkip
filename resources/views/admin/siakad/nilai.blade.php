<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 dark:text-white leading-tight">
                    {{ __('Data Akademik SIAKAD — Nilai Mata Kuliah') }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Nilai akademik mahasiswa hasil sinkronisasi SIAKAD untuk kalkulasi IPK transkrip.
                </p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.siakad.nilai.template') }}" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl shadow-sm transition gap-2 inline-flex items-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Unduh Template CSV
                </a>
                <button @click="$dispatch('open-modal', 'import-siakad-nilai')" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-sm transition gap-2 inline-flex items-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    Import Nilai SIAKAD
                </button>
            </div>
        </div>
    </x-slot>

    <!-- Search & Filter Bar -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-4 mb-6">
        <form method="GET" action="{{ route('admin.siakad.nilai') }}" class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[240px]">
                <x-text-input name="search" type="text" placeholder="Cari berdasarkan NIM, Nama Mahasiswa, atau Kode MK..." value="{{ request('search') }}" class="w-full text-sm" />
            </div>
            <div class="w-40">
                <select name="semester" onchange="this.form.submit()" class="w-full text-sm rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">Semua Semester</option>
                    @for ($s = 1; $s <= 8; $s++)
                        <option value="{{ $s }}" {{ request('semester') == $s ? 'selected' : '' }}>Semester {{ $s }}</option>
                    @endfor
                </select>
            </div>
            <x-primary-button>Cari</x-primary-button>
            @if (request()->hasAny(['search', 'semester']))
                <a href="{{ route('admin.siakad.nilai') }}" class="text-xs text-rose-500 hover:underline">Reset</a>
            @endif
        </form>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400">
                    <tr>
                        <th class="px-4 py-3 rounded-l-lg">Mahasiswa</th>
                        <th class="px-4 py-3">Mata Kuliah</th>
                        <th class="px-4 py-3 text-center">Semester</th>
                        <th class="px-4 py-3 text-center">SKS</th>
                        <th class="px-4 py-3 text-center">Nilai Huruf</th>
                        <th class="px-4 py-3 text-center">Bobot</th>
                        <th class="px-4 py-3 rounded-r-lg text-right">Mutu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($nilais as $n)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30">
                            <td class="px-4 py-3.5">
                                <div class="font-bold text-gray-900 dark:text-white">{{ $n->mahasiswa?->nama ?? '-' }}</div>
                                <div class="text-xs font-mono text-gray-500">{{ $n->mahasiswa?->nim ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="font-semibold text-gray-800 dark:text-gray-200 text-xs">{{ $n->mataKuliah?->nama_mata_kuliah ?? '-' }}</div>
                                <div class="text-xs font-mono text-gray-400">{{ $n->mataKuliah?->kode_mata_kuliah ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3.5 text-center text-xs">Smtr {{ $n->semester }}</td>
                            <td class="px-4 py-3.5 text-center font-bold text-gray-900 dark:text-white text-xs">{{ $n->sks }}</td>
                            <td class="px-4 py-3.5 text-center">
                                <span class="px-2 py-0.5 rounded text-xs font-bold bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white">
                                    {{ $n->nilai_huruf }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-center font-mono text-xs">{{ number_format($n->bobot, 2) }}</td>
                            <td class="px-4 py-3.5 text-right font-mono font-bold text-xs text-emerald-600 dark:text-emerald-400">
                                {{ number_format($n->sks * $n->bobot, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-400 text-xs">Belum ada data nilai akademik SIAKAD.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $nilais->links() }}
        </div>
    </div>

    <!-- Modal Import Nilai -->
    <x-modal name="import-siakad-nilai" focusable>
        <form method="POST" action="{{ route('admin.siakad.nilai.import') }}" enctype="multipart/form-data" class="p-6">
            @csrf
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Import Nilai Akademik SIAKAD</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-6">
                Unggah file CSV dengan format kolom: <span class="font-mono bg-gray-100 dark:bg-gray-700 px-1 py-0.5 rounded">NIM, Kode_Mata_Kuliah, Semester, Nilai_Huruf, Bobot, SKS</span>.
            </p>

            <div>
                <x-input-label for="file_nilai" value="Pilih File CSV (.csv)" />
                <input id="file_nilai" name="file" type="file" accept=".csv,.txt" class="mt-2 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100" required />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                <x-primary-button>Mulai Import</x-primary-button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
