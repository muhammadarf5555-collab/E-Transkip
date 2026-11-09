<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 dark:text-white leading-tight">
                    {{ __('Master Data Mata Kuliah') }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Daftar kurikulum mata kuliah akademik UINSSC.
                </p>
            </div>
            <div class="flex items-center gap-2.5">
                <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-mk')" style="background-color: #059669 !important; color: #ffffff !important;" class="px-4 py-2.5 text-white text-sm font-bold rounded-xl shadow-md transition gap-2 inline-flex items-center hover:opacity-90 cursor-pointer">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Mata Kuliah
                </button>
                <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-semibold bg-blue-50 text-[#2563EB] border border-blue-200">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Kurikulum Resmi SIAKAD
                </span>
            </div>
        </div>
    </x-slot>

    <!-- Search & Filter -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-4 mb-6">
        <form method="GET" action="{{ route('admin.mata-kuliah.index') }}" class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[240px]">
                <x-text-input name="search" type="text" placeholder="Cari kode atau nama mata kuliah..." value="{{ request('search') }}" class="w-full text-sm" />
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
                <a href="{{ route('admin.mata-kuliah.index') }}" class="text-xs text-rose-500 hover:underline">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400">
                    <tr>
                        <th class="px-4 py-3 rounded-l-lg">Kode</th>
                        <th class="px-4 py-3">Nama Mata Kuliah</th>
                        <th class="px-4 py-3 text-center">SKS</th>
                        <th class="px-4 py-3 text-center">Semester</th>
                        <th class="px-4 py-3 text-center">Total Nilai</th>
                        <th class="px-4 py-3 rounded-r-lg text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($mataKuliahs as $mk)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30">
                            <td class="px-4 py-3.5 font-mono font-bold text-gray-900 dark:text-white">{{ $mk->kode_mata_kuliah }}</td>
                            <td class="px-4 py-3.5 font-medium text-gray-800 dark:text-gray-200">{{ $mk->nama_mata_kuliah }}</td>
                            <td class="px-4 py-3.5 text-center font-bold text-emerald-600">{{ $mk->sks }}</td>
                            <td class="px-4 py-3.5 text-center text-xs">Smtr {{ $mk->semester ?? '-' }}</td>
                            <td class="px-4 py-3.5 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                    {{ $mk->nilais_count }} entri
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-right space-x-2">
                                <button @click="$dispatch('open-modal', 'edit-mk-{{ $mk->id }}')" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-200 transition">
                                    Edit
                                </button>
                                <form action="{{ route('admin.mata-kuliah.destroy', $mk) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus mata kuliah ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 dark:bg-rose-950/50 dark:hover:bg-rose-900/50 dark:text-rose-400 transition">
                                        Hapus
                                    </button>
                                </form>

                                <!-- Edit Modal -->
                                <x-modal name="edit-mk-{{ $mk->id }}" focusable>
                                    <form method="POST" action="{{ route('admin.mata-kuliah.update', $mk) }}" class="p-6 text-left">
                                        @csrf
                                        @method('PUT')
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Edit Mata Kuliah</h3>
                                        <div class="space-y-4">
                                            <div>
                                                <x-input-label for="edit_kode_{{ $mk->id }}" value="Kode Mata Kuliah" />
                                                <x-text-input id="edit_kode_{{ $mk->id }}" name="kode_mata_kuliah" type="text" class="mt-1 block w-full" value="{{ $mk->kode_mata_kuliah }}" required />
                                            </div>
                                            <div>
                                                <x-input-label for="edit_nama_{{ $mk->id }}" value="Nama Mata Kuliah" />
                                                <x-text-input id="edit_nama_{{ $mk->id }}" name="nama_mata_kuliah" type="text" class="mt-1 block w-full" value="{{ $mk->nama_mata_kuliah }}" required />
                                            </div>
                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <x-input-label for="edit_sks_{{ $mk->id }}" value="Bobot SKS" />
                                                    <x-text-input id="edit_sks_{{ $mk->id }}" name="sks" type="number" min="1" max="10" class="mt-1 block w-full" value="{{ $mk->sks }}" required />
                                                </div>
                                                <div>
                                                    <x-input-label for="edit_smtr_{{ $mk->id }}" value="Semester" />
                                                    <x-text-input id="edit_smtr_{{ $mk->id }}" name="semester" type="text" class="mt-1 block w-full" value="{{ $mk->semester }}" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-6 flex justify-end gap-3">
                                            <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                                            <x-primary-button>Simpan Perubahan</x-primary-button>
                                        </div>
                                    </form>
                                </x-modal>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-400 text-xs">Belum ada data mata kuliah.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $mataKuliahs->links() }}
        </div>
    </div>

    <!-- Modal Tambah Mata Kuliah -->
    <x-modal name="add-mk" focusable>
        <form method="POST" action="{{ route('admin.mata-kuliah.store') }}" class="p-6 text-left">
            @csrf
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Tambah Mata Kuliah Baru</h3>
            <div class="space-y-4">
                <div>
                    <x-input-label for="new_kode_mk" value="Kode Mata Kuliah" />
                    <x-text-input id="new_kode_mk" name="kode_mk" type="text" class="mt-1 block w-full" placeholder="Contoh: TIF101, PAI201" required />
                </div>
                <div>
                    <x-input-label for="new_nama_mk" value="Nama Mata Kuliah" />
                    <x-text-input id="new_nama_mk" name="nama_mk" type="text" class="mt-1 block w-full" placeholder="Contoh: Pemrograman Web Lanjut" required />
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <x-input-label for="new_sks" value="Bobot SKS" />
                        <x-text-input id="new_sks" name="sks" type="number" min="1" max="10" class="mt-1 block w-full" placeholder="Contoh: 3" required />
                    </div>
                    <div>
                        <x-input-label for="new_semester" value="Semester Penawaran" />
                        <x-text-input id="new_semester" name="semester" type="text" class="mt-1 block w-full" placeholder="Contoh: 1, 2, dst." />
                    </div>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                <x-primary-button style="background-color: #059669 !important;">Simpan Mata Kuliah</x-primary-button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
