<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 dark:text-white leading-tight">
                    {{ __('Direktori Data Mahasiswa') }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Kelola data mahasiswa, akun login, dan status akademik.
                </p>
            </div>
            <a href="{{ route('admin.mahasiswa.create') }}" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-sm transition gap-2 inline-flex items-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Mahasiswa
            </a>
        </div>
    </x-slot>

    <!-- Search & Filter Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-4 mb-6">
        <form method="GET" action="{{ route('admin.mahasiswa.index') }}" class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[240px]">
                <x-text-input name="search" type="text" placeholder="Cari berdasarkan NIM atau Nama Mahasiswa..." value="{{ request('search') }}" class="w-full text-sm" />
            </div>
            <div class="w-64">
                <select name="program_studi_id" onchange="this.form.submit()" class="w-full text-sm rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">Semua Program Studi</option>
                    @if (auth()->user()->isSuperAdmin())
                        @foreach ($programStudis->groupBy(fn($p) => $p->fakultas?->nama_fakultas ?? 'Lainnya') as $fakultasNama => $prodis)
                            <optgroup label="{{ $fakultasNama }}">
                                @foreach ($prodis as $prodi)
                                    <option value="{{ $prodi->id }}" {{ request('program_studi_id') == $prodi->id ? 'selected' : '' }}>
                                        {{ $prodi->nama_program_studi }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    @else
                        @foreach ($programStudis as $prodi)
                            <option value="{{ $prodi->id }}" {{ request('program_studi_id') == $prodi->id ? 'selected' : '' }}>
                                {{ $prodi->nama_program_studi }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="w-36">
                <x-text-input name="angkatan" type="text" placeholder="Angkatan (2022)" value="{{ request('angkatan') }}" class="w-full text-sm" />
            </div>
            <x-primary-button>Cari</x-primary-button>
            @if (request()->hasAny(['search', 'program_studi_id', 'angkatan']))
                <a href="{{ route('admin.mahasiswa.index') }}" class="text-xs text-rose-500 hover:underline">
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
                        <th class="px-4 py-3 rounded-l-lg">NIM & Mahasiswa</th>
                        <th class="px-4 py-3">Program Studi</th>
                        <th class="px-4 py-3">Angkatan</th>
                        <th class="px-4 py-3 text-center">Wisudawan</th>
                        <th class="px-4 py-3 text-center">Status Transkrip</th>
                        <th class="px-4 py-3 rounded-r-lg text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($mahasiswas as $mhs)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30">
                            <td class="px-4 py-3.5">
                                <a href="{{ route('admin.mahasiswa.show', $mhs) }}" class="font-bold text-gray-900 dark:text-white hover:text-emerald-600 transition">
                                    {{ $mhs->nama }}
                                </a>
                                <div class="text-xs font-mono text-gray-500">{{ $mhs->nim }}</div>
                            </td>
                            <td class="px-4 py-3.5 text-xs text-gray-600 dark:text-gray-300">
                                <div>{{ $mhs->programStudi?->nama_program_studi ?? '-' }}</div>
                                <div class="text-[11px] text-gray-400">{{ $mhs->fakultas?->nama_fakultas ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3.5 text-xs font-medium">{{ $mhs->angkatan ?? '-' }}</td>
                            <td class="px-4 py-3.5 text-center">
                                @if ($mhs->wisudawan)
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300">
                                        {{ $mhs->wisudawan->periode_wisuda }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                @if ($mhs->transkrip)
                                    @if ($mhs->transkrip->status_validasi === 'Valid')
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">Valid</span>
                                    @elseif ($mhs->transkrip->status_validasi === 'Menunggu Validasi')
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300">Review</span>
                                    @elseif ($mhs->transkrip->status_validasi === 'Ditolak')
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300">Ditolak</span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">Draft</span>
                                    @endif
                                @else
                                    <span class="text-xs text-gray-400">Belum ada</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-right space-x-2">
                                <a href="{{ route('admin.mahasiswa.show', $mhs) }}" class="px-2.5 py-1.5 text-xs font-semibold rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-200 transition">
                                    Detail
                                </a>
                                <a href="{{ route('admin.mahasiswa.edit', $mhs) }}" class="px-2.5 py-1.5 text-xs font-semibold rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-600 dark:bg-indigo-950/50 dark:hover:bg-indigo-900/50 dark:text-indigo-400 transition">
                                    Edit
                                </a>
                                <form action="{{ route('admin.mahasiswa.destroy', $mhs) }}" method="POST" class="inline" onsubmit="return confirm('Hapus mahasiswa ini beserta akun loginnnya?')">
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
                            <td colspan="6" class="px-4 py-8 text-center text-gray-400 text-xs">Belum ada data mahasiswa ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $mahasiswas->links() }}
        </div>
    </div>
</x-app-layout>
