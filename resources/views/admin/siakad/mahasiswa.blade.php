<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 dark:text-white leading-tight">
                    {{ __('Data Akademik SIAKAD — Mahasiswa') }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Sumber data mahasiswa yang diimpor dari sistem SIAKAD universitas.
                </p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.siakad.mahasiswa.template') }}" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl shadow-sm transition gap-2 inline-flex items-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Unduh Template CSV
                </a>
                <button @click="$dispatch('open-modal', 'import-siakad-mhs')" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-sm transition gap-2 inline-flex items-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    Import Data SIAKAD
                </button>
            </div>
        </div>
    </x-slot>

    <!-- Search Bar -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-4 mb-6">
        <form method="GET" action="{{ route('admin.siakad.mahasiswa') }}" class="flex items-center gap-4">
            <div class="flex-1">
                <x-text-input name="search" type="text" placeholder="Cari berdasarkan NIM, Nama, atau Program Studi SIAKAD..." value="{{ request('search') }}" class="w-full text-sm" />
            </div>
            <x-primary-button>Cari</x-primary-button>
            @if (request('search'))
                <a href="{{ route('admin.siakad.mahasiswa') }}" class="text-xs text-rose-500 hover:underline">Reset</a>
            @endif
        </form>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400">
                    <tr>
                        <th class="px-4 py-3 rounded-l-lg">NIM</th>
                        <th class="px-4 py-3">Nama Mahasiswa</th>
                        <th class="px-4 py-3">Program Studi</th>
                        <th class="px-4 py-3">Fakultas</th>
                        <th class="px-4 py-3 text-center">Angkatan</th>
                        <th class="px-4 py-3 text-center">Status Link</th>
                        <th class="px-4 py-3 rounded-r-lg text-right">Sumber Data</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($siakadMahasiswas as $sm)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30">
                            <td class="px-4 py-3.5 font-mono font-bold text-gray-900 dark:text-white">{{ $sm->nim }}</td>
                            <td class="px-4 py-3.5 font-semibold text-gray-900 dark:text-white">{{ $sm->nama }}</td>
                            <td class="px-4 py-3.5 text-xs text-gray-600 dark:text-gray-300">{{ $sm->program_studi ?? '-' }}</td>
                            <td class="px-4 py-3.5 text-xs text-gray-500 dark:text-gray-400">{{ $sm->fakultas ?? '-' }}</td>
                            <td class="px-4 py-3.5 text-center text-xs">{{ $sm->angkatan ?? '-' }}</td>
                            <td class="px-4 py-3.5 text-center">
                                @if ($sm->mahasiswa_id)
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                                        Terhubung
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300">
                                        NIM Baru
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-right font-mono text-xs text-gray-400">
                                {{ $sm->sumber_data }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-400 text-xs">Belum ada data mahasiswa SIAKAD. Silakan gunakan tombol "Import Data SIAKAD".</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $siakadMahasiswas->links() }}
        </div>
    </div>

    <!-- Modal Import -->
    <x-modal name="import-siakad-mhs" focusable>
        <form method="POST" action="{{ route('admin.siakad.mahasiswa.import') }}" enctype="multipart/form-data" class="p-6">
            @csrf
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Import Data Mahasiswa SIAKAD</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-6">
                Unggah file CSV dengan kolom: <span class="font-mono bg-gray-100 dark:bg-gray-700 px-1 py-0.5 rounded">NIM, Nama, Fakultas, Program_Studi, Angkatan</span>.
            </p>

            <div>
                <x-input-label for="file_mhs" value="Pilih File CSV (.csv)" />
                <input id="file_mhs" name="file" type="file" accept=".csv,.txt" class="mt-2 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100" required />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                <x-primary-button>Mulai Import</x-primary-button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
