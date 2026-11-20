<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 dark:text-white leading-tight">
                    {{ __('Master Data Fakultas') }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Kelola data fakultas resmi di lingkungan UIN Siber Syekh Nurjati Cirebon.
                </p>
            </div>
            <div class="flex items-center gap-2.5">
                <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-fakultas')" style="background-color: #059669 !important; color: #ffffff !important;" class="px-4 py-2.5 text-white text-sm font-bold rounded-xl shadow-md transition gap-2 inline-flex items-center hover:opacity-90 cursor-pointer">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Fakultas
                </button>
                <form action="{{ route('admin.fakultas.sync-api') }}" method="POST" onsubmit="return confirm('Tarik dan sinkronkan data fakultas resmi dari UINSSC Bridge API?')">
                    @csrf
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
                            <span class="font-medium underline ml-1">Sistem tetap menggunakan data lokal/dummy yang sudah tersimpan.</span>
                        @endif
                    </p>
                </div>
            </div>
            <div class="flex-shrink-0">
                <form action="{{ route('admin.fakultas.sync-api') }}" method="POST" onsubmit="return confirm('Tarik dan sinkronkan data fakultas resmi dari UINSSC Bridge API?')">
                    @csrf
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

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400">
                    <tr>
                        <th class="px-4 py-3 rounded-l-lg w-12">No</th>
                        <th class="px-4 py-3 text-center w-28">Kode &amp; Romawi</th>
                        <th class="px-4 py-3">Fakultas &amp; SK BAN-PT</th>
                        <th class="px-4 py-3">Pejabat Dekan Aktif (Dinamis)</th>
                        <th class="px-4 py-3 text-center">Prodi &amp; Mhs</th>
                        <th class="px-4 py-3 rounded-r-lg text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($fakultas as $index => $item)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30">
                            <td class="px-4 py-3.5 text-xs text-gray-400 font-mono">{{ $fakultas->firstItem() + $index }}</td>
                            <td class="px-4 py-3.5 text-center space-y-1">
                                <div class="flex items-center justify-center gap-1.5">
                                    @if ($item->nama_fakultas_singkat)
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300">
                                            {{ $item->nama_fakultas_singkat }}
                                        </span>
                                    @endif
                                    @if ($item->kode_romawi)
                                        <span class="px-2 py-0.5 rounded text-xs font-mono font-bold bg-amber-50 text-amber-800 border border-amber-200 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-800" title="Kode Romawi Nomor Surat Transkrip">
                                            {{ $item->kode_romawi }}
                                        </span>
                                    @endif
                                </div>
                                @if ($item->kode_fakultas)
                                    <div class="text-[11px] font-mono text-gray-400">ID API: {{ $item->kode_fakultas }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="font-semibold text-gray-900 dark:text-white">{{ $item->nama_fakultas }}</div>
                                @if ($item->no_sk_banpt)
                                    <div class="text-xs font-mono text-gray-500 mt-0.5">SK: {{ $item->no_sk_banpt }}</div>
                                @else
                                    <div class="text-xs text-amber-600 mt-0.5">SK BAN-PT belum diatur</div>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                @if ($item->nama_dekan)
                                    <div class="flex items-center gap-1.5">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/50 dark:text-emerald-300">
                                            {{ $item->jabatan_dekan ?: 'Dekan' }}
                                        </span>
                                        <span class="font-bold text-gray-900 dark:text-white text-xs">{{ $item->nama_dekan }}</span>
                                    </div>
                                    <div class="text-xs font-mono text-gray-500 mt-0.5">NIP. {{ $item->nip_dekan ?: '-' }}</div>
                                @else
                                    <span class="text-xs text-rose-500 font-medium">Pejabat Dekan belum diisi</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-center space-y-1">
                                <div>
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300">
                                        {{ $item->program_studis_count }} Prodi
                                    </span>
                                </div>
                                <div>
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300">
                                        {{ $item->mahasiswas_count }} Mhs
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 text-right space-x-2 whitespace-nowrap">
                                <button @click="$dispatch('open-modal', 'edit-fakultas-{{ $item->id }}')" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-200 transition">
                                    Atur Pejabat &amp; Fakultas
                                </button>
                                @if (!auth()->user()->isAdminFakultas())
                                    <form action="{{ route('admin.fakultas.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus fakultas ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 dark:bg-rose-950/50 dark:hover:bg-rose-900/50 dark:text-rose-400 transition">
                                            Hapus
                                        </button>
                                    </form>
                                @endif

                                <!-- Edit Modal -->
                                <x-modal name="edit-fakultas-{{ $item->id }}" focusable>
                                    <form method="POST" action="{{ route('admin.fakultas.update', $item) }}" class="p-6 text-left">
                                        @csrf
                                        @method('PUT')
                                        <div class="border-b border-gray-100 dark:border-gray-700 pb-3 mb-4">
                                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Pengaturan Fakultas &amp; Pejabat Penandatangan</h3>
                                            <p class="text-xs text-gray-500 mt-0.5">Data Dekan, NIP, Kode Romawi, dan SK BAN-PT ini otomatis digunakan secara dinamis saat mencetak Transkrip Akademik.</p>
                                        </div>
                                        <div class="space-y-4">
                                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                                <div>
                                                    <x-input-label for="edit_kode_fakultas_{{ $item->id }}" value="Kode API" />
                                                    <x-text-input id="edit_kode_fakultas_{{ $item->id }}" name="kode_fakultas" type="number" class="mt-1 block w-full text-sm" value="{{ $item->kode_fakultas }}" placeholder="Contoh: 1" />
                                                </div>
                                                <div>
                                                    <x-input-label for="edit_singkat_{{ $item->id }}" value="Singkatan" />
                                                    <x-text-input id="edit_singkat_{{ $item->id }}" name="nama_fakultas_singkat" type="text" class="mt-1 block w-full text-sm" value="{{ $item->nama_fakultas_singkat }}" placeholder="FITK, FEBI" />
                                                </div>
                                                <div>
                                                    <x-input-label for="edit_romawi_{{ $item->id }}" value="Kode Romawi Surat" />
                                                    <x-text-input id="edit_romawi_{{ $item->id }}" name="kode_romawi" type="text" class="mt-1 block w-full text-sm font-mono" value="{{ $item->kode_romawi }}" placeholder="F.I, F.II, F.III" />
                                                </div>
                                            </div>
                                            <div>
                                                <x-input-label for="edit_nama_fakultas_{{ $item->id }}" value="Nama Lengkap Fakultas" />
                                                <x-text-input id="edit_nama_fakultas_{{ $item->id }}" name="nama_fakultas" type="text" class="mt-1 block w-full text-sm" value="{{ $item->nama_fakultas }}" required />
                                            </div>

                                            <div class="p-4 rounded-xl bg-emerald-50/60 dark:bg-gray-900/60 border border-emerald-200 dark:border-gray-700 space-y-3">
                                                <div class="text-xs font-bold uppercase tracking-wider text-emerald-800 dark:text-emerald-400">
                                                    Pejabat Penandatangan Transkrip (Dinamis)
                                                </div>
                                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                                    <div>
                                                        <x-input-label for="edit_jabatan_dekan_{{ $item->id }}" value="Jabatan Pejabat" />
                                                        <select id="edit_jabatan_dekan_{{ $item->id }}" name="jabatan_dekan" class="mt-1 block w-full text-sm rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                                                            <option value="Dekan" {{ ($item->jabatan_dekan ?: 'Dekan') === 'Dekan' ? 'selected' : '' }}>Dekan (Definitif)</option>
                                                            <option value="Plt. Dekan" {{ $item->jabatan_dekan === 'Plt. Dekan' ? 'selected' : '' }}>Plt. Dekan</option>
                                                            <option value="Pj. Dekan" {{ $item->jabatan_dekan === 'Pj. Dekan' ? 'selected' : '' }}>Pj. Dekan</option>
                                                            <option value="Direktur" {{ $item->jabatan_dekan === 'Direktur' ? 'selected' : '' }}>Direktur (Pascasarjana)</option>
                                                        </select>
                                                    </div>
                                                    <div class="sm:col-span-2">
                                                        <x-input-label for="edit_nama_dekan_{{ $item->id }}" value="Nama Lengkap &amp; Gelar Pejabat" />
                                                        <x-text-input id="edit_nama_dekan_{{ $item->id }}" name="nama_dekan" type="text" class="mt-1 block w-full text-sm font-semibold" value="{{ $item->nama_dekan }}" placeholder="Contoh: Dr. H. Saifuddin, M.Ag." />
                                                    </div>
                                                </div>
                                                <div>
                                                    <x-input-label for="edit_nip_dekan_{{ $item->id }}" value="NIP Pejabat Penandatangan" />
                                                    <x-text-input id="edit_nip_dekan_{{ $item->id }}" name="nip_dekan" type="text" class="mt-1 block w-full text-sm font-mono" value="{{ $item->nip_dekan }}" placeholder="Contoh: 197203151998031002" />
                                                </div>
                                            </div>

                                            <div>
                                                <x-input-label for="edit_sk_banpt_{{ $item->id }}" value="Nomor SK BAN-PT / Akreditasi Default Fakultas" />
                                                <x-text-input id="edit_sk_banpt_{{ $item->id }}" name="no_sk_banpt" type="text" class="mt-1 block w-full text-sm font-mono" value="{{ $item->no_sk_banpt }}" placeholder="Contoh: 3184/SK/BAN-PT/Ak.Ppj/S/VIII/2024" />
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
                            <td colspan="6" class="px-4 py-8 text-center text-gray-400 text-xs">Belum ada data fakultas. Klik "Tarik dari Bridge API" untuk mengambil data resmi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $fakultas->links() }}
        </div>
    </div>

    <!-- Modal Tambah Fakultas -->
    <x-modal name="add-fakultas" focusable>
        <form method="POST" action="{{ route('admin.fakultas.store') }}" class="p-6 text-left">
            @csrf
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Tambah Fakultas &amp; Pejabat Penandatangan</h3>
            <div class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <x-input-label for="new_kode_fakultas" value="Kode API" />
                        <x-text-input id="new_kode_fakultas" name="kode_fakultas" type="number" class="mt-1 block w-full text-sm" placeholder="Contoh: 1" />
                    </div>
                    <div>
                        <x-input-label for="new_nama_fakultas_singkat" value="Singkatan" />
                        <x-text-input id="new_nama_fakultas_singkat" name="nama_fakultas_singkat" type="text" class="mt-1 block w-full text-sm" placeholder="FITK, FST" />
                    </div>
                    <div>
                        <x-input-label for="new_kode_romawi" value="Kode Romawi Surat" />
                        <x-text-input id="new_kode_romawi" name="kode_romawi" type="text" class="mt-1 block w-full text-sm font-mono" placeholder="F.I, F.II" />
                    </div>
                </div>
                <div>
                    <x-input-label for="new_nama_fakultas" value="Nama Lengkap Fakultas" />
                    <x-text-input id="new_nama_fakultas" name="nama_fakultas" type="text" class="mt-1 block w-full text-sm" placeholder="Contoh: Fakultas Ilmu Tarbiyah dan Keguruan" required />
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <x-input-label for="new_nama_dekan" value="Nama Dekan &amp; Gelar" />
                        <x-text-input id="new_nama_dekan" name="nama_dekan" type="text" class="mt-1 block w-full text-sm" placeholder="Contoh: Dr. H. Saifuddin, M.Ag." />
                    </div>
                    <div>
                        <x-input-label for="new_nip_dekan" value="NIP Dekan" />
                        <x-text-input id="new_nip_dekan" name="nip_dekan" type="text" class="mt-1 block w-full text-sm font-mono" placeholder="Contoh: 197203151998031002" />
                    </div>
                </div>
                <div>
                    <x-input-label for="new_no_sk_banpt" value="Nomor SK BAN-PT Default" />
                    <x-text-input id="new_no_sk_banpt" name="no_sk_banpt" type="text" class="mt-1 block w-full text-sm font-mono" placeholder="Contoh: 3184/SK/BAN-PT/Ak.Ppj/S/VIII/2024" />
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                <x-primary-button style="background-color: #059669 !important;">Simpan Fakultas</x-primary-button>
            </div>
        </form>
    </x-modal>
</x-app-layout>