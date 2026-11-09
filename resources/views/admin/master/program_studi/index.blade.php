<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 dark:text-white leading-tight">
                    {{ __('Master Data Program Studi') }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Daftar jurusan dan program studi resmi di lingkungan UIN Siber Syekh Nurjati Cirebon.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2.5">
                <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-prodi')" style="background-color: #059669 !important; color: #ffffff !important;" class="px-4 py-2.5 text-white text-sm font-bold rounded-xl shadow-md transition gap-2 inline-flex items-center hover:opacity-90 cursor-pointer">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Program Studi
                </button>
                <form action="{{ route('admin.program-studi.sync-api') }}" method="POST" onsubmit="return confirm('Tarik dan sinkronkan data program studi resmi dari UINSSC Bridge API?')">
                    @csrf
                    @if (request('fakultas_id'))
                        <input type="hidden" name="fakultas_id" value="{{ request('fakultas_id') }}">
                    @endif
                    <button type="submit" style="background-color: #1d4ed8 !important; color: #ffffff !important;" class="px-4 py-2.5 text-white text-sm font-bold rounded-xl shadow-md transition gap-2 inline-flex items-center hover:opacity-90 cursor-pointer">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Sinkronisasi Bridge API
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <!-- Bridge Integration Status Banner -->
    @if (isset($bridgeStatus))
        <div class="mb-6 p-4 rounded-2xl border text-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4 {{ $bridgeStatus['status'] === 'connected' ? 'bg-emerald-50 border-emerald-200 text-emerald-800 dark:bg-emerald-950/40 dark:border-emerald-800 dark:text-emerald-300' : ($bridgeStatus['status'] === 'unauthorized' ? 'bg-amber-50 border-amber-200 text-amber-800 dark:bg-amber-950/40 dark:border-amber-800 dark:text-amber-300' : 'bg-gray-50 border-gray-200 text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300') }}">
            <div class="flex items-start gap-3.5">
                <div class="mt-0.5">
                    @if ($bridgeStatus['status'] === 'connected')
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    @elseif ($bridgeStatus['status'] === 'unauthorized')
                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    @else
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    @endif
                </div>
                <div>
                    <div class="font-semibold flex flex-wrap items-center gap-2">
                        <span>Status Integrasi UINSSC Bridge API:</span>
                        @if ($bridgeStatus['status'] === 'connected')
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-200 text-emerald-900 dark:bg-emerald-800 dark:text-emerald-100">TERHUBUNG (HTTP 200)</span>
                        @elseif ($bridgeStatus['status'] === 'unauthorized')
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-200 text-amber-900 dark:bg-amber-800 dark:text-amber-100">TOKEN INVALID (HTTP 401)</span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-gray-300">DATA LOKAL / DUMMY AKTIF</span>
                        @endif
                    </div>
                    <p class="mt-0.5 text-xs opacity-90 leading-relaxed">
                        {{ $bridgeStatus['message'] }}
                        @if ($bridgeStatus['status'] !== 'connected')
                            <span class="font-medium underline ml-1">Sistem saat ini tetap berjalan normal menggunakan sumber data lokal/dummy.</span>
                        @endif
                    </p>
                </div>
            </div>
            <div class="flex-shrink-0">
                <form action="{{ route('admin.program-studi.sync-api') }}" method="POST" onsubmit="return confirm('Tarik dan sinkronkan data program studi resmi dari UINSSC Bridge API?')">
                    @csrf
                    @if (request('fakultas_id'))
                        <input type="hidden" name="fakultas_id" value="{{ request('fakultas_id') }}">
                    @endif
                    <button type="submit" style="background-color: #1d4ed8 !important; color: #ffffff !important;" class="px-5 py-2.5 text-white text-xs font-bold rounded-xl shadow-md transition gap-2 inline-flex items-center hover:opacity-90 cursor-pointer">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Tarik dari Bridge API
                    </button>
                </form>
            </div>
        </div>
    @endif

    <!-- Filter Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-4 mb-6">
        <form method="GET" action="{{ route('admin.program-studi.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
            <div class="sm:col-span-5">
                <select name="fakultas_id" onchange="this.form.submit()" class="w-full text-sm rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">-- Semua Fakultas --</option>
                    @foreach ($fakultasList as $fak)
                        <option value="{{ $fak->id }}" {{ request('fakultas_id') == $fak->id ? 'selected' : '' }}>
                            {{ $fak->nama_fakultas }} {{ $fak->nama_fakultas_singkat ? "({$fak->nama_fakultas_singkat})" : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="sm:col-span-5">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama prodi, kode, atau gelar..." class="w-full text-sm rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
            </div>
            <div class="sm:col-span-2 flex items-center gap-2">
                <button type="submit" class="w-full py-2.5 px-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-xs font-semibold rounded-xl transition text-center">
                    Cari
                </button>
                @if (request('fakultas_id') || request('search'))
                    <a href="{{ route('admin.program-studi.index') }}" class="py-2.5 px-3 text-xs text-rose-500 hover:text-rose-700 font-semibold text-center whitespace-nowrap">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400">
                    <tr>
                        <th class="px-4 py-3 rounded-l-lg w-16">No</th>
                        <th class="px-4 py-3 text-center w-24">Kode</th>
                        <th class="px-4 py-3">Nama Program Studi</th>
                        <th class="px-4 py-3 text-center w-24">Jenjang</th>
                        <th class="px-4 py-3 text-center w-20">SKS</th>
                        <th class="px-4 py-3">Gelar Akademik</th>
                        <th class="px-4 py-3">Fakultas</th>
                        <th class="px-4 py-3 text-center">Mahasiswa</th>
                        <th class="px-4 py-3 rounded-r-lg text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($programStudis as $index => $prodi)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30">
                            <td class="px-4 py-3.5 text-xs text-gray-400 font-mono">{{ $programStudis->firstItem() + $index }}</td>
                            <td class="px-4 py-3.5 text-center">
                                @if ($prodi->kode_prodi)
                                    <span class="px-2 py-0.5 rounded-md text-xs font-mono font-bold bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200">
                                        {{ $prodi->kode_prodi }}
                                    </span>
                                @else
                                    <span class="text-gray-400 text-xs">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="font-semibold text-gray-900 dark:text-white block">{{ $prodi->nama_program_studi }}</span>
                                @if ($prodi->gelar_akademik_full)
                                    <span class="text-xs text-gray-400 dark:text-gray-500">{{ $prodi->gelar_akademik_full }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                @php
                                    $jenjangColors = [
                                        'S1' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300',
                                        'S2' => 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300',
                                        'S3' => 'bg-purple-50 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300',
                                        'Profesi' => 'bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300',
                                    ];
                                    $jenjangBadge = $jenjangColors[$prodi->jenjang] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300';
                                @endphp
                                @if ($prodi->jenjang)
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $jenjangBadge }}">
                                        {{ $prodi->jenjang }}
                                    </span>
                                @else
                                    <span class="text-gray-400 text-xs">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-center text-xs font-mono">
                                {{ $prodi->sks_tempuh ?? '-' }}
                            </td>
                            <td class="px-4 py-3.5 text-xs">
                                @if ($prodi->gelar_akademik)
                                    <span class="font-semibold text-gray-900 dark:text-white">{{ $prodi->gelar_akademik }}</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-xs text-gray-700 dark:text-gray-300">
                                <span class="font-medium">{{ $prodi->fakultas?->nama_fakultas_singkat ?? $prodi->fakultas?->nama_fakultas ?? '-' }}</span>
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300">
                                    {{ $prodi->mahasiswas_count }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-right space-x-2">
                                <button @click="$dispatch('open-modal', 'edit-prodi-{{ $prodi->id }}')" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-200 transition">
                                    Edit
                                </button>
                                <form action="{{ route('admin.program-studi.destroy', $prodi) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus prodi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 dark:bg-rose-950/50 dark:hover:bg-rose-900/50 dark:text-rose-400 transition">
                                        Hapus
                                    </button>
                                </form>

                                <!-- Edit Modal -->
                                <x-modal name="edit-prodi-{{ $prodi->id }}" focusable>
                                    <form method="POST" action="{{ route('admin.program-studi.update', $prodi) }}" class="p-6 text-left">
                                        @csrf
                                        @method('PUT')
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Edit Program Studi</h3>
                                        <div class="space-y-4">
                                            <div>
                                                <x-input-label for="edit_fakultas_id_{{ $prodi->id }}" value="Fakultas" />
                                                <select id="edit_fakultas_id_{{ $prodi->id }}" name="fakultas_id" class="mt-1 block w-full text-sm rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-emerald-500 focus:ring-emerald-500" required>
                                                    @foreach ($fakultasList as $fak)
                                                        <option value="{{ $fak->id }}" {{ $prodi->fakultas_id == $fak->id ? 'selected' : '' }}>
                                                            {{ $fak->nama_fakultas }} {{ $fak->nama_fakultas_singkat ? "({$fak->nama_fakultas_singkat})" : '' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="grid grid-cols-2 gap-3">
                                                <div>
                                                    <x-input-label for="edit_kode_prodi_{{ $prodi->id }}" value="Kode Prodi" />
                                                    <x-text-input id="edit_kode_prodi_{{ $prodi->id }}" name="kode_prodi" type="text" class="mt-1 block w-full" value="{{ $prodi->kode_prodi }}" placeholder="Contoh: 312" />
                                                </div>
                                                <div>
                                                    <x-input-label for="edit_jenjang_{{ $prodi->id }}" value="Jenjang" />
                                                    <select id="edit_jenjang_{{ $prodi->id }}" name="jenjang" class="mt-1 block w-full text-sm rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                                                        <option value="">-- Pilih --</option>
                                                        <option value="S1" {{ $prodi->jenjang == 'S1' ? 'selected' : '' }}>S1 (Sarjana)</option>
                                                        <option value="S2" {{ $prodi->jenjang == 'S2' ? 'selected' : '' }}>S2 (Magister)</option>
                                                        <option value="S3" {{ $prodi->jenjang == 'S3' ? 'selected' : '' }}>S3 (Doktor)</option>
                                                        <option value="Profesi" {{ $prodi->jenjang == 'Profesi' ? 'selected' : '' }}>Profesi</option>
                                                        <option value="D3" {{ $prodi->jenjang == 'D3' ? 'selected' : '' }}>D3 (Diploma)</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div>
                                                <x-input-label for="edit_nama_prodi_{{ $prodi->id }}" value="Nama Program Studi" />
                                                <x-text-input id="edit_nama_prodi_{{ $prodi->id }}" name="nama_program_studi" type="text" class="mt-1 block w-full" value="{{ $prodi->nama_program_studi }}" required />
                                            </div>
                                            <div class="grid grid-cols-2 gap-3">
                                                <div>
                                                    <x-input-label for="edit_gelar_{{ $prodi->id }}" value="Gelar Singkat" />
                                                    <x-text-input id="edit_gelar_{{ $prodi->id }}" name="gelar_akademik" type="text" class="mt-1 block w-full" value="{{ $prodi->gelar_akademik }}" placeholder="Contoh: S.H., S.Pd." />
                                                </div>
                                                <div>
                                                    <x-input-label for="edit_sks_{{ $prodi->id }}" value="SKS Tempuh" />
                                                    <x-text-input id="edit_sks_{{ $prodi->id }}" name="sks_tempuh" type="number" class="mt-1 block w-full" value="{{ $prodi->sks_tempuh }}" placeholder="Contoh: 144" />
                                                </div>
                                            </div>
                                            <div>
                                                <x-input-label for="edit_gelar_full_{{ $prodi->id }}" value="Gelar Akademik Lengkap" />
                                                <x-text-input id="edit_gelar_full_{{ $prodi->id }}" name="gelar_akademik_full" type="text" class="mt-1 block w-full" value="{{ $prodi->gelar_akademik_full }}" placeholder="Contoh: Sarjana Hukum" />
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
                            <td colspan="9" class="px-4 py-8 text-center text-gray-400 text-xs">Belum ada data program studi. Klik "Tarik dari Bridge API" untuk sinkronisasi otomatis.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $programStudis->links() }}
        </div>
    </div>

    <!-- Modal Tambah Program Studi -->
    <x-modal name="add-prodi" focusable>
        <form method="POST" action="{{ route('admin.program-studi.store') }}" class="p-6 text-left">
            @csrf
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Tambah Program Studi Baru</h3>
            <div class="space-y-4">
                <div>
                    <x-input-label for="new_fakultas_id" value="Fakultas" />
                    <select id="new_fakultas_id" name="fakultas_id" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500" required>
                        <option value="">Pilih Fakultas</option>
                        @foreach ($fakultasList as $fak)
                            <option value="{{ $fak->id }}" {{ request('fakultas_id') == $fak->id ? 'selected' : '' }}>
                                {{ $fak->nama_fakultas }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <x-input-label for="new_kode_prodi" value="Kode Prodi" />
                        <x-text-input id="new_kode_prodi" name="kode_prodi" type="text" class="mt-1 block w-full" placeholder="Contoh: PAI, TI" />
                    </div>
                    <div>
                        <x-input-label for="new_jenjang" value="Jenjang" />
                        <select id="new_jenjang" name="jenjang" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="S1">S1 (Sarjana)</option>
                            <option value="S2">S2 (Magister)</option>
                            <option value="S3">S3 (Doktor)</option>
                            <option value="D3">D3 (Diploma)</option>
                        </select>
                    </div>
                </div>
                <div>
                    <x-input-label for="new_nama_program_studi" value="Nama Program Studi" />
                    <x-text-input id="new_nama_program_studi" name="nama_program_studi" type="text" class="mt-1 block w-full" placeholder="Contoh: Informatika" required />
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <x-input-label for="new_gelar_akademik" value="Gelar Singkat" />
                        <x-text-input id="new_gelar_akademik" name="gelar_akademik" type="text" class="mt-1 block w-full" placeholder="Contoh: S.Kom., S.Pd." />
                    </div>
                    <div>
                        <x-input-label for="new_sks_tempuh" value="SKS Tempuh" />
                        <x-text-input id="new_sks_tempuh" name="sks_tempuh" type="number" class="mt-1 block w-full" placeholder="144" value="144" />
                    </div>
                </div>
                <div>
                    <x-input-label for="new_gelar_akademik_full" value="Gelar Akademik Lengkap" />
                    <x-text-input id="new_gelar_akademik_full" name="gelar_akademik_full" type="text" class="mt-1 block w-full" placeholder="Contoh: Sarjana Komputer" />
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                <x-primary-button style="background-color: #059669 !important;">Simpan Program Studi</x-primary-button>
            </div>
        </form>
    </x-modal>
</x-app-layout>