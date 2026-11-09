<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 dark:text-white leading-tight">
                    {{ __('Audit Log & Jejak Aktivitas Sistem') }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Rekaman kronologis setiap aksi, perubahan data, validasi, dan login pada sistem E-Transkrip.
                </p>
            </div>
        </div>
    </x-slot>

    <!-- Filter & Search -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-4 mb-6">
        <form method="GET" action="{{ Auth::user()->isSuperAdmin() ? route('super-admin.audit-log.index') : route('admin.audit-log.index') }}" class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[240px]">
                <x-text-input name="search" type="text" placeholder="Cari aktivitas, deskripsi, atau IP address..." value="{{ request('search') }}" class="w-full text-sm" />
            </div>
            <div class="w-48">
                <select name="module" onchange="this.form.submit()" class="w-full text-sm rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">Semua Modul</option>
                    @foreach ($modules as $mod)
                        <option value="{{ $mod }}" {{ request('module') == $mod ? 'selected' : '' }}>{{ $mod }}</option>
                    @endforeach
                </select>
            </div>
            <x-primary-button>Cari</x-primary-button>
            @if (request()->hasAny(['search', 'module']))
                <a href="{{ Auth::user()->isSuperAdmin() ? route('super-admin.audit-log.index') : route('admin.audit-log.index') }}" class="text-xs text-rose-500 hover:underline">Reset</a>
            @endif
        </form>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400">
                    <tr>
                        <th class="px-4 py-3 rounded-l-lg w-40">Waktu</th>
                        <th class="px-4 py-3">Pengguna</th>
                        <th class="px-4 py-3 text-center">Modul</th>
                        <th class="px-4 py-3">Aktivitas</th>
                        <th class="px-4 py-3">Deskripsi</th>
                        <th class="px-4 py-3 rounded-r-lg text-right">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($logs as $log)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30">
                            <td class="px-4 py-3.5 text-xs text-gray-400 font-mono">
                                {{ $log->created_at->format('d/m/Y H:i:s') }}
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="font-bold text-xs text-gray-900 dark:text-white">{{ $log->user?->name ?? 'Sistem / Tamu' }}</div>
                                <div class="text-[11px] text-gray-400">{{ $log->user?->email ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                    {{ $log->module ?? 'General' }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 font-semibold text-xs text-emerald-700 dark:text-emerald-400">
                                {{ $log->aktivitas }}
                            </td>
                            <td class="px-4 py-3.5 text-xs text-gray-600 dark:text-gray-300 max-w-sm">
                                {{ $log->description }}
                            </td>
                            <td class="px-4 py-3.5 text-right font-mono text-xs text-gray-400">
                                {{ $log->ip_address ?? '127.0.0.1' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-400 text-xs">Belum ada catatan log aktivitas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $logs->links() }}
        </div>
    </div>
</x-app-layout>
