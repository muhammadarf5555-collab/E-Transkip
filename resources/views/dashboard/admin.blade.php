<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="font-bold text-2xl text-gray-900 dark:text-white leading-tight">
                        {{ __('Dashboard Admin Fakultas') }}
                    </h2>
                    @if (isset($fakultas) && $fakultas)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300 border border-emerald-200">
                            {{ $fakultas->nama_fakultas }}
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800 dark:bg-indigo-900/60 dark:text-indigo-300 border border-indigo-200">
                            Operasional Akademik
                        </span>
                    @endif
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Selamat datang kembali, {{ Auth::user()->name }}. Kelola data mahasiswa, nilai SIAKAD, wisudawan, dan validasi transkrip resmi.
                </p>
            </div>
            <div class="flex items-center space-x-3">
                <form action="{{ route('admin.transkrip.sync') }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-sm transition duration-150 gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Sinkronisasi & Kalkulasi Transkrip
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <!-- Card 1 -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Mahasiswa</p>
                <h3 class="text-3xl font-extrabold text-gray-900 dark:text-white mt-1">{{ number_format($stats['total_mahasiswa']) }}</h3>
                <a href="{{ route('admin.mahasiswa.index') }}" class="inline-flex items-center text-xs font-medium text-emerald-600 dark:text-emerald-400 hover:underline mt-2">
                    Lihat Direktori &rarr;
                </a>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Data Wisudawan</p>
                <h3 class="text-3xl font-extrabold text-gray-900 dark:text-white mt-1">{{ number_format($stats['total_wisudawan']) }}</h3>
                <a href="{{ route('admin.wisudawan.index') }}" class="inline-flex items-center text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:underline mt-2">
                    Kelola Wisudawan &rarr;
                </a>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                </svg>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Menunggu Validasi</p>
                <h3 class="text-3xl font-extrabold text-amber-500 mt-1">{{ number_format($stats['transkrip_menunggu']) }}</h3>
                <a href="{{ route('admin.transkrip.index', ['status' => 'Menunggu Validasi']) }}" class="inline-flex items-center text-xs font-medium text-amber-600 dark:text-amber-400 hover:underline mt-2">
                    Validasi Sekarang &rarr;
                </a>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-500 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <!-- Card 4 -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Transkrip Valid</p>
                <h3 class="text-3xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-1">{{ number_format($stats['transkrip_valid']) }}</h3>
                <span class="inline-block text-xs text-gray-500 dark:text-gray-400 mt-2">Siap diunduh / cetak</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Recent Transcripts -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6">
            <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-gray-700">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Transkrip Terbaru</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Daftar transkrip nilai yang baru diproses / menunggu review</p>
                </div>
                <a href="{{ route('admin.transkrip.index') }}" class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:underline">
                    Semua Transkrip &rarr;
                </a>
            </div>

            <div class="overflow-x-auto mt-4">
                <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                    <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400">
                        <tr>
                            <th class="px-4 py-3 rounded-l-lg">Mahasiswa</th>
                            <th class="px-4 py-3">Prodi</th>
                            <th class="px-4 py-3">IPK / SKS</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 rounded-r-lg text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($recentTranscripts as $trx)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition">
                                <td class="px-4 py-3.5 font-medium text-gray-900 dark:text-white">
                                    <div class="font-semibold">{{ $trx->mahasiswa->nama }}</div>
                                    <div class="text-xs text-gray-500 font-mono">{{ $trx->mahasiswa->nim }}</div>
                                </td>
                                <td class="px-4 py-3.5 text-xs text-gray-600 dark:text-gray-300">
                                    {{ $trx->mahasiswa->programStudi?->nama_program_studi ?? '-' }}
                                </td>
                                <td class="px-4 py-3.5 font-medium">
                                    <span class="text-emerald-600 dark:text-emerald-400 font-bold">{{ number_format($trx->ipk, 2) }}</span>
                                    <span class="text-xs text-gray-400">({{ $trx->total_sks }} SKS)</span>
                                </td>
                                <td class="px-4 py-3.5">
                                    @if ($trx->status_validasi === 'Valid')
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">Valid</span>
                                    @elseif ($trx->status_validasi === 'Menunggu Validasi')
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300">Menunggu Review</span>
                                    @elseif ($trx->status_validasi === 'Ditolak')
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300">Ditolak</span>
                                    @else
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">Draft</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-right space-x-2">
                                    <a href="{{ route('admin.transkrip.show', $trx) }}" class="inline-flex items-center px-2.5 py-1.5 text-xs font-semibold rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 transition">
                                        Periksa
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-400">
                                    Belum ada data transkrip yang digenerate. Klik "Sinkronisasi & Kalkulasi Transkrip" untuk membuat transkrip.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Activity Audit Log -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6">
            <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-gray-700">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Aktivitas Sistem</h3>
                <a href="{{ route('admin.audit-log.index') }}" class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:underline">
                    Semua Log &rarr;
                </a>
            </div>

            <div class="mt-4 flow-root">
                <ul class="-mb-6">
                    @forelse ($recentLogs as $log)
                        <li class="relative pb-6">
                            <div class="relative flex items-start space-x-3">
                                <div>
                                    <div class="relative px-1">
                                        <div class="h-6 w-6 bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 rounded-full flex items-center justify-center ring-4 ring-white dark:ring-gray-800">
                                            <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div>
                                        <div class="text-xs font-bold text-gray-900 dark:text-white">{{ $log->aktivitas }}</div>
                                        <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400 truncate">{{ $log->description }}</p>
                                    </div>
                                    <div class="mt-1 text-[11px] text-gray-400 flex items-center space-x-2">
                                        <span>{{ $log->user?->name ?? 'Sistem' }}</span>
                                        <span>&bull;</span>
                                        <span>{{ $log->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="py-6 text-center text-xs text-gray-400">
                            Belum ada riwayat aktivitas yang tercatat.
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
