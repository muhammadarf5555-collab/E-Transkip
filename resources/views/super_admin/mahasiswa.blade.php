<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('super-admin.dashboard') }}" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    </a>
                    <h2 class="font-bold text-2xl text-gray-900 dark:text-white leading-tight">
                        {{ __('Direktori Mahasiswa Universitas') }}
                    </h2>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800 dark:bg-indigo-900/60 dark:text-indigo-300">
                        Super Admin (Hanya Melihat)
                    </span>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Inspeksi terpusat seluruh data mahasiswa dari semua fakultas.
                </p>
            </div>
        </div>
    </x-slot>

    <!-- Filter & Search -->
    <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm mb-6">
        <form method="GET" action="{{ route('super-admin.mahasiswa.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
            <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Filter Fakultas</label>
                <select name="fakultas_id" class="w-full text-xs rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Semua Fakultas</option>
                    @foreach ($fakultasList as $f)
                        <option value="{{ $f->id }}" {{ request('fakultas_id') == $f->id ? 'selected' : '' }}>
                            {{ $f->nama_fakultas }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Cari Nama / NIM</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik nama atau NIM..." class="w-full text-xs rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-sm transition">
                    Cari Mahasiswa
                </button>
                @if (request()->hasAny(['fakultas_id', 'search']))
                    <a href="{{ route('super-admin.mahasiswa.index') }}" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-xl transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400">
                    <tr>
                        <th class="px-6 py-3.5">NIM & Mahasiswa</th>
                        <th class="px-6 py-3.5">Fakultas</th>
                        <th class="px-6 py-3.5">Program Studi</th>
                        <th class="px-6 py-3.5 text-center">Angkatan / Jenjang</th>
                        <th class="px-6 py-3.5 text-center">Status Transkrip</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($mahasiswas as $m)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900 dark:text-white">{{ $m->nama }}</div>
                                <div class="text-xs text-gray-500 font-mono">{{ $m->nim }}</div>
                            </td>
                            <td class="px-6 py-4 text-xs font-medium text-gray-800 dark:text-gray-200">
                                {{ $m->fakultas?->nama_fakultas ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-600 dark:text-gray-300">
                                {{ $m->programStudi?->nama_program_studi ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-center text-xs">
                                <span class="px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-700 font-medium">
                                    {{ $m->jenjang }} - {{ $m->angkatan }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if ($m->transkrip)
                                    @if ($m->transkrip->status_validasi === 'Valid')
                                        <a href="{{ route('super-admin.transkrip.show', $m->transkrip->id) }}" class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800 hover:bg-emerald-200">
                                            Valid (IPK: {{ number_format($m->transkrip->ipk, 2) }})
                                        </a>
                                    @else
                                        <a href="{{ route('super-admin.transkrip.show', $m->transkrip->id) }}" class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800 hover:bg-amber-200">
                                            {{ $m->transkrip->status_validasi }}
                                        </a>
                                    @endif
                                @else
                                    <span class="text-xs text-gray-400">Belum ada</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">
                                Data mahasiswa tidak ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($mahasiswas->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                {{ $mahasiswas->links() }}
            </div>
        @endif
    </div>
</x-app-layout>

