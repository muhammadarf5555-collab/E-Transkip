<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 dark:text-white leading-tight">
                    {{ __('Manajemen Transkrip Nilai Akademik') }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Validasi, kalkulasi IPK otomatis dari SIAKAD, dan cetak transkrip resmi UINSSC.
                </p>
            </div>
            <form action="{{ route('admin.transkrip.sync') }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-sm transition gap-2 inline-flex items-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Sinkronisasi & Kalkulasi Nilai
                </button>
            </form>
        </div>
    </x-slot>

    <!-- Status Tabs -->
    <div class="flex flex-wrap items-center gap-2 mb-6">
        <a href="{{ route('admin.transkrip.index') }}" class="px-4 py-2 rounded-xl text-xs font-bold transition {{ !request('status') ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-50' }}">
            Semua ({{ $counts['total'] }})
        </a>
        <a href="{{ route('admin.transkrip.index', ['status' => 'Menunggu Validasi']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition {{ request('status') === 'Menunggu Validasi' ? 'bg-amber-500 text-white shadow-sm' : 'bg-white dark:bg-gray-800 text-amber-600 dark:text-amber-400 hover:bg-gray-50' }}">
            Menunggu Validasi ({{ $counts['menunggu'] }})
        </a>
        <a href="{{ route('admin.transkrip.index', ['status' => 'Valid']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition {{ request('status') === 'Valid' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white dark:bg-gray-800 text-emerald-600 dark:text-emerald-400 hover:bg-gray-50' }}">
            Valid / Sah ({{ $counts['valid'] }})
        </a>
        <a href="{{ route('admin.transkrip.index', ['status' => 'Draft']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition {{ request('status') === 'Draft' ? 'bg-gray-700 text-white shadow-sm' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-50' }}">
            Draft ({{ $counts['draft'] }})
        </a>
        <a href="{{ route('admin.transkrip.index', ['status' => 'Ditolak']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition {{ request('status') === 'Ditolak' ? 'bg-rose-600 text-white shadow-sm' : 'bg-white dark:bg-gray-800 text-rose-600 dark:text-rose-400 hover:bg-gray-50' }}">
            Ditolak ({{ $counts['ditolak'] }})
        </a>
    </div>

    <!-- Search Bar -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-4 mb-6">
        <form method="GET" action="{{ route('admin.transkrip.index') }}" class="flex items-center gap-4">
            @if (request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}" />
            @endif
            <div class="flex-1">
                <x-text-input name="search" type="text" placeholder="Cari berdasarkan NIM, Nama Mahasiswa, atau Kode Verifikasi..." value="{{ request('search') }}" class="w-full text-sm" />
            </div>
            <x-primary-button>Cari</x-primary-button>
            @if (request('search'))
                <a href="{{ route('admin.transkrip.index', request('status') ? ['status' => request('status')] : []) }}" class="text-xs text-rose-500 hover:underline">Reset</a>
            @endif
        </form>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400">
                    <tr>
                        <th class="px-4 py-3 rounded-l-lg">Wisudawan</th>
                        <th class="px-4 py-3">Program Studi</th>
                        <th class="px-4 py-3 text-center">Total SKS</th>
                        <th class="px-4 py-3 text-center">IPK</th>
                        <th class="px-4 py-3">Predikat</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 rounded-r-lg text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($transkrips as $trx)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30">
                            <td class="px-4 py-3.5">
                                <a href="{{ route('admin.transkrip.show', $trx) }}" class="font-bold text-gray-900 dark:text-white hover:text-emerald-600 transition">
                                    {{ $trx->mahasiswa->nama }}
                                </a>
                                <div class="text-xs font-mono text-gray-500">{{ $trx->mahasiswa->nim }}</div>
                                <div class="text-[11px] font-mono text-gray-400 truncate max-w-xs">{{ $trx->kode_verifikasi }}</div>
                            </td>
                            <td class="px-4 py-3.5 text-xs text-gray-600 dark:text-gray-300">
                                {{ $trx->mahasiswa->programStudi?->nama_program_studi ?? '-' }}
                            </td>
                            <td class="px-4 py-3.5 text-center font-bold text-gray-900 dark:text-white text-xs">
                                {{ $trx->total_sks }}
                            </td>
                            <td class="px-4 py-3.5 text-center font-mono font-black text-sm text-emerald-600 dark:text-emerald-400">
                                {{ number_format($trx->ipk, 2) }}
                            </td>
                            <td class="px-4 py-3.5 text-xs font-medium text-gray-800 dark:text-gray-200">
                                {{ $trx->predikat ?? '-' }}
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                @if ($trx->status_validasi === 'Valid')
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">Valid</span>
                                @elseif ($trx->status_validasi === 'Menunggu Validasi')
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300">Review</span>
                                @elseif ($trx->status_validasi === 'Ditolak')
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300">Ditolak</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">Draft</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-right space-x-1.5 whitespace-nowrap">
                                <a href="{{ route('admin.transkrip.show', $trx) }}" class="px-2.5 py-1.5 text-xs font-semibold rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-200 transition">
                                    Detail
                                </a>
                                <a href="{{ route('admin.transkrip.pdf', ['transkrip' => $trx->id, 'mode' => 'draft']) }}" target="_blank" class="px-2.5 py-1.5 text-xs font-semibold rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 transition" title="Format Draft Transkrip Pustikom">
                                    Draft PDF
                                </a>
                                <a href="{{ route('admin.transkrip.pdf', ['transkrip' => $trx->id, 'mode' => 'final']) }}" target="_blank" class="px-2.5 py-1.5 text-xs font-semibold rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white transition" title="Format Siap Cetak Pustikom">
                                    Siap Cetak PDF
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-400 text-xs">
                                Belum ada transkrip nilai. Klik tombol "Sinkronisasi & Kalkulasi Nilai" di atas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $transkrips->links() }}
        </div>
    </div>
</x-app-layout>
