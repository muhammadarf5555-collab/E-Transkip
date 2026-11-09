<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('super-admin.dashboard') }}" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    </a>
                    <h2 class="font-bold text-2xl text-gray-900 dark:text-white leading-tight">
                        {{ __('Direktori Wisudawan Universitas') }}
                    </h2>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800 dark:bg-indigo-900/60 dark:text-indigo-300">
                        Super Admin
                    </span>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Inspeksi terpusat data administrasi kelulusan wisudawan universitas (terintegrasi E-Wisuda API).
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.wisudawan.index') }}" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-sm transition">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                    Tarik / Kelola Data E-Wisuda (API)
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Filter & Search -->
    <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm mb-6">
        <form method="GET" action="{{ route('super-admin.wisudawan.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
            <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Periode Wisuda</label>
                <select name="periode_wisuda" class="w-full text-xs rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Semua Periode</option>
                    @foreach ($periodes as $p)
                        <option value="{{ $p }}" {{ request('periode_wisuda') == $p ? 'selected' : '' }}>
                            {{ $p }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Cari Nama / NIM / No. SK / No. Ijazah</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik kata kunci..." class="w-full text-xs rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-sm transition">
                    Cari Wisudawan
                </button>
                @if (request()->hasAny(['periode_wisuda', 'search']))
                    <a href="{{ route('super-admin.wisudawan.index') }}" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-xl transition">
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
                        <th class="px-6 py-3.5">Wisudawan & Gelar</th>
                        <th class="px-6 py-3.5">Fakultas / Prodi</th>
                        <th class="px-6 py-3.5">SK Yudisium & Ijazah</th>
                        <th class="px-6 py-3.5">IPK / SKS / Studi</th>
                        <th class="px-6 py-3.5">Predikat & Eligible</th>
                        <th class="px-6 py-3.5 text-center">Transkrip</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($wisudawans as $w)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900 dark:text-white">
                                    {{ $w->nama ?: ($w->mahasiswa?->nama ?? '-') }}
                                    @if ($w->gelar)
                                        <span class="text-indigo-600 dark:text-indigo-400 font-semibold">, {{ $w->gelar }}</span>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-500 font-mono">{{ $w->nim }}</div>
                                @if ($w->gelar_full)
                                    <div class="text-[11px] text-gray-500">{{ $w->gelar_full }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs font-medium text-gray-800 dark:text-gray-200">
                                <div>{{ $w->fakultas }}</div>
                                <div class="text-gray-500">{{ $w->program_studi }}</div>
                                <div class="text-[11px] text-indigo-600 dark:text-indigo-400 font-semibold mt-0.5">{{ $w->periode_wisuda }}</div>
                            </td>
                            <td class="px-6 py-4 text-xs">
                                <div>
                                    <span class="text-gray-400">SK:</span>
                                    <span class="font-mono font-semibold text-gray-800 dark:text-gray-200">{{ $w->no_sk ?: '-' }}</span>
                                    @if ($w->tanggal_sk)
                                        <span class="text-[10px] text-gray-400">({{ $w->tanggal_sk->format('d/m/Y') }})</span>
                                    @endif
                                </div>
                                <div class="mt-1">
                                    <span class="text-gray-400">Ijazah:</span>
                                    <span class="font-mono font-semibold text-gray-800 dark:text-gray-200">{{ $w->no_ijazah ?: '-' }}</span>
                                    @if ($w->tanggal_ijazah)
                                        <span class="text-[10px] text-gray-400">({{ $w->tanggal_ijazah->format('d/m/Y') }})</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-xs">
                                <div class="font-bold text-emerald-700 dark:text-emerald-400">
                                    IPK: {{ number_format((float) ($w->ipk ?? $w->transkrip?->ipk ?? 0), 2) }}
                                </div>
                                <div class="text-gray-500">
                                    SKS: {{ $w->total_sks ?? $w->transkrip?->total_sks ?? '-' }} | {{ $w->lama_studi ? $w->lama_studi . ' Sem' : '-' }}
                                </div>
                                @if ($w->semester_lulus)
                                    <div class="text-[11px] text-gray-400">Lulus: {{ $w->semester_lulus }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs">
                                <div class="flex flex-col gap-1 items-start">
                                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-bold">
                                        {{ $w->predikat_kelulusan }}
                                    </span>
                                    @if ($w->is_eligible)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-50 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                                            ✓ Eligible Yudisium
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if ($w->transkrip)
                                    <a href="{{ route('super-admin.transkrip.show', $w->transkrip->id) }}" class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                        Lihat
                                    </a>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">
                                Data wisudawan tidak ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($wisudawans->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                {{ $wisudawans->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
