<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="font-bold text-2xl text-gray-900 dark:text-white leading-tight">
                        {{ __('Portal Pengawas Super Administrator') }}
                    </h2>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800 dark:bg-indigo-900/60 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                        <svg class="w-3 h-3 me-1 text-indigo-600 dark:text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 1.944A11.954 11.954 0 012.166 5C2.056 5.649 2 6.319 2 7c0 5.225 3.34 9.67 8 11.317C14.66 16.67 18 12.225 18 7c0-.682-.057-1.35-.166-2.001A11.954 11.954 0 0110 1.944zM11 14a1 1 0 11-2 0 1 1 0 012 0zm0-7a1 1 0 10-2 0v3a1 1 0 102 0V7z" clip-rule="evenodd" />
                        </svg>
                        Super Admin: Mode Pengawas (Hanya Melihat Data)
                    </span>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Pusat supervisi transkrip nilai universitas dan manajemen terpusat akun Admin Fakultas.
                </p>
            </div>
            <div class="flex items-center space-x-3">
                <button type="button" onclick="document.getElementById('modalTambahAdmin').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm transition duration-150 gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    + Buat Akun Admin Fakultas
                </button>
            </div>
        </div>
    </x-slot>

    <!-- Success & Error Alerts -->
    @if (session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-sm flex items-center gap-3 shadow-sm">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div class="font-medium">{{ session('success') }}</div>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 text-sm shadow-sm">
            <div class="font-bold mb-1">Terjadi Kesalahan:</div>
            <ul class="list-disc list-inside text-xs space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Pending Requests Alert Banner -->
    @if ($stats['pending_requests_count'] > 0)
        <div class="mb-8 p-4 rounded-2xl bg-gradient-to-r from-amber-500/15 via-orange-500/10 to-amber-500/5 border border-amber-300 dark:border-amber-700/60 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-gray-900 dark:text-white">
                        Ada {{ $stats['pending_requests_count'] }} Permintaan Pembuatan Akun Admin Fakultas Baru
                    </h4>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">
                        Calon Admin Fakultas telah mengajukan permohonan pembuatan akun. Silakan periksa dan terbitkan akun resmi.
                    </p>
                </div>
            </div>
            <a href="#section-manajemen-admin" onclick="switchTab('tab-admin')" class="inline-flex items-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold rounded-xl shadow transition shrink-0 gap-1.5">
                Tinjau Permintaan Sekarang &darr;
            </a>
        </div>
    @endif

    <!-- Stat Cards Overview -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
        <!-- Card 1 -->
        <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
            <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Fakultas</p>
            <h3 class="text-2xl font-black text-gray-900 dark:text-white mt-1">{{ $stats['total_fakultas'] }}</h3>
            <span class="text-[11px] text-gray-500 dark:text-gray-400">{{ $stats['total_prodi'] }} Prodi</span>
        </div>

        <!-- Card 2 -->
        <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
            <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Mahasiswa</p>
            <h3 class="text-2xl font-black text-gray-900 dark:text-white mt-1">{{ number_format($stats['total_mahasiswa']) }}</h3>
            <span class="text-[11px] text-emerald-600 dark:text-emerald-400">Total terdaftar</span>
        </div>

        <!-- Card 3 -->
        <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
            <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Wisudawan</p>
            <h3 class="text-2xl font-black text-gray-900 dark:text-white mt-1">{{ number_format($stats['total_wisudawan']) }}</h3>
            <span class="text-[11px] text-indigo-600 dark:text-indigo-400">Data kelulusan</span>
        </div>

        <!-- Card 4 -->
        <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
            <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Transkrip Valid</p>
            <h3 class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ number_format($stats['transkrip_valid']) }}</h3>
            <span class="text-[11px] text-gray-500">Dari {{ $stats['total_transkrip'] }} total</span>
        </div>

        <!-- Card 5 -->
        <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
            <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Menunggu Review</p>
            <h3 class="text-2xl font-black text-amber-500 mt-1">{{ number_format($stats['transkrip_menunggu']) }}</h3>
            <span class="text-[11px] text-amber-600 dark:text-amber-400">Oleh Admin Fak.</span>
        </div>

        <!-- Card 6 -->
        <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
            <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Admin Fakultas</p>
            <h3 class="text-2xl font-black text-indigo-600 dark:text-indigo-400 mt-1">{{ $stats['total_admin_fakultas'] }}</h3>
            <span class="text-[11px] text-indigo-600 font-semibold">{{ $stats['pending_requests_count'] }} minta akun</span>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div x-data="{ activeTab: 'monitoring' }" class="space-y-6">
        <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <button @click="activeTab = 'monitoring'" :class="activeTab === 'monitoring' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium'" class="whitespace-nowrap py-4 px-1 border-b-2 text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    Monitoring Transkrip (Hanya Melihat)
                </button>

                <button @click="activeTab = 'admin-management'" :class="activeTab === 'admin-management' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium'" class="whitespace-nowrap py-4 px-1 border-b-2 text-sm flex items-center gap-2 relative">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Manajemen Akun Admin Fakultas
                    @if ($stats['pending_requests_count'] > 0)
                        <span class="inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold leading-none text-white bg-amber-500 rounded-full">
                            {{ $stats['pending_requests_count'] }}
                        </span>
                    @endif
                </button>

                <button @click="activeTab = 'audit-log'" :class="activeTab === 'audit-log' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium'" class="whitespace-nowrap py-4 px-1 border-b-2 text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    Audit Log Pengawasan
                </button>

                <button @click="activeTab = 'bobot-nilai'" :class="activeTab === 'bobot-nilai' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium'" class="whitespace-nowrap py-4 px-1 border-b-2 text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    Aturan Bobot Nilai & IPK
                </button>

                <button @click="activeTab = 'siakad-integration'" :class="activeTab === 'siakad-integration' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium'" class="whitespace-nowrap py-4 px-1 border-b-2 text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Integrasi SIAKAD
                </button>
            </nav>
        </div>

        <!-- ================= TAB 1: MONITORING TRANSKRIP (READ ONLY) ================= -->
        <div x-show="activeTab === 'monitoring'" class="space-y-6">
            <!-- Read Only Notice Box -->
            <div class="p-4 rounded-2xl bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800 text-indigo-900 dark:text-indigo-200 text-sm flex items-start gap-3">
                <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <span class="font-bold">Ketentuan Akses Super Admin:</span>
                    Halaman ini menampilkan seluruh transkrip universitas dalam <span class="font-bold underline">Mode Hanya Melihat (Read-Only)</span>. Super Admin dapat memeriksa kelengkapan data, status validasi, dan mengunduh berkas transkrip resmi untuk verifikasi, namun <span class="font-semibold text-rose-600 dark:text-rose-400">tidak memiliki hak mengubah, menghapus, atau memvalidasi nilai</span>. Operasional pengelolaan transkrip menjadi wewenang Admin Fakultas.
                </div>
            </div>

            <!-- Filter & Search Bar -->
            <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
                <form method="GET" action="{{ route('super-admin.dashboard') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                    <!-- Filter Fakultas -->
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

                    <!-- Filter Status -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Status Validasi</label>
                        <select name="status_validasi" class="w-full text-xs rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Semua Status</option>
                            <option value="Valid" {{ request('status_validasi') === 'Valid' ? 'selected' : '' }}>Valid</option>
                            <option value="Menunggu Validasi" {{ request('status_validasi') === 'Menunggu Validasi' ? 'selected' : '' }}>Menunggu Validasi</option>
                            <option value="Draft" {{ request('status_validasi') === 'Draft' ? 'selected' : '' }}>Draft</option>
                            <option value="Ditolak" {{ request('status_validasi') === 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>

                    <!-- Search -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Cari Mahasiswa / NIM</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik nama atau NIM..." class="w-full text-xs rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-sm transition">
                            Terapkan Filter
                        </button>
                        @if (request()->hasAny(['fakultas_id', 'status_validasi', 'search']))
                            <a href="{{ route('super-admin.dashboard') }}" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-xs font-semibold rounded-xl transition">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Transkrip Table (Read-Only) -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-base text-gray-900 dark:text-white">Direktori Transkrip Nilai Universitas</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Menampilkan seluruh arsip transkrip dari seluruh fakultas.</p>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                        {{ $transkrips->total() }} Data Transkrip
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                        <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-3.5">Mahasiswa</th>
                                <th class="px-6 py-3.5">Fakultas / Prodi</th>
                                <th class="px-6 py-3.5 text-center">IPK / SKS</th>
                                <th class="px-6 py-3.5 text-center">Status Validasi</th>
                                <th class="px-6 py-3.5 text-right">Aksi Supervisi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse ($transkrips as $trx)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition">
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-gray-900 dark:text-white">{{ $trx->mahasiswa?->nama ?? '-' }}</div>
                                        <div class="text-xs text-gray-500 font-mono">{{ $trx->mahasiswa?->nim ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-xs">
                                        <div class="font-medium text-gray-800 dark:text-gray-200">{{ $trx->mahasiswa?->fakultas?->nama_fakultas ?? '-' }}</div>
                                        <div class="text-gray-500">{{ $trx->mahasiswa?->programStudi?->nama_program_studi ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="text-base font-bold text-emerald-600 dark:text-emerald-400">{{ number_format($trx->ipk, 2) }}</span>
                                        <div class="text-xs text-gray-400">{{ $trx->total_sks }} SKS</div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if ($trx->isDisahkan())
                                            <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                                                <span class="w-1.5 h-1.5 me-1.5 rounded-full bg-emerald-500"></span>
                                                Resmi Disahkan
                                            </span>
                                        @elseif ($trx->isMenungguPengesahan())
                                            <span class="inline-flex items-center px-2.5 py-1 text-xs font-bold rounded-full bg-blue-100 text-[#2563EB] dark:bg-blue-950/60 dark:text-blue-300">
                                                <span class="w-1.5 h-1.5 me-1.5 rounded-full bg-blue-600 animate-pulse"></span>
                                                Menunggu Pengesahan
                                            </span>
                                        @elseif ($trx->isPerluPerbaikan())
                                            <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300">
                                                <span class="w-1.5 h-1.5 me-1.5 rounded-full bg-rose-500"></span>
                                                Perlu Perbaikan
                                            </span>
                                        @elseif ($trx->status_validasi === 'Ditolak')
                                            <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300">
                                                <span class="w-1.5 h-1.5 me-1.5 rounded-full bg-rose-500"></span>
                                                Ditolak
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300">
                                                <span class="w-1.5 h-1.5 me-1.5 rounded-full bg-amber-500"></span>
                                                Menunggu Verifikasi
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="inline-flex items-center space-x-2">
                                            @if ($trx->isMenungguPengesahan())
                                                <a href="{{ route('super-admin.transkrip.show', $trx->id) }}" class="inline-flex items-center px-3 py-1.5 bg-[#2563EB] hover:bg-blue-700 text-white text-xs font-bold rounded-lg transition gap-1 shadow-sm">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    Review & Sahkan
                                                </a>
                                            @else
                                                <!-- Detail Button (Read-Only) -->
                                                <a href="{{ route('super-admin.transkrip.show', $trx->id) }}" class="inline-flex items-center px-3 py-1.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-xs font-medium rounded-lg transition gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    Lihat Data
                                                </a>
                                            @endif

                                            <!-- PDF Download (Super Admin View) -->
                                            <a href="{{ route('super-admin.transkrip.pdf', $trx->id) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/50 dark:hover:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300 text-xs font-medium rounded-lg transition gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                PDF
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">
                                        Tidak ada data transkrip yang sesuai dengan filter pencarian.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($transkrips->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                        {{ $transkrips->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- ================= TAB 2: MANAJEMEN AKUN ADMIN FAKULTAS ================= -->
        <div x-show="activeTab === 'admin-management'" id="section-manajemen-admin" class="space-y-8">
            <!-- Rule Card Explaining the Creation Mechanism -->
            <div class="p-5 rounded-2xl bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-950/40 dark:to-purple-950/40 border border-indigo-200 dark:border-indigo-800 text-sm">
                <div class="flex items-center justify-between gap-4 flex-wrap">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center shrink-0 shadow">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 dark:text-white">Kebijakan Pembuatan Akun Admin Fakultas</h4>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">
                                Admin Fakultas tidak dapat mendaftar mandiri. Admin Fakultas wajib <span class="font-semibold text-indigo-700 dark:text-indigo-300">meminta pembuatan akun kepada Super Admin</span>, dan hanya Super Admin yang memiliki wewenang menyetujui serta menerbitkan akun.
                            </p>
                        </div>
                    </div>
                    <button type="button" onclick="document.getElementById('modalTambahAdmin').classList.remove('hidden')" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-sm transition gap-1.5 flex items-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        + Tambah Admin Fakultas Langsung
                    </button>
                </div>
            </div>

            <!-- Sub-section 1: Permintaan Akun Masuk (Pending & Past Requests) -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="font-bold text-base text-gray-900 dark:text-white">Permintaan Pembuatan Akun Dari Calon Admin Fakultas</h3>
                            @if ($stats['pending_requests_count'] > 0)
                                <span class="px-2 py-0.5 text-xs font-bold rounded-full bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300">
                                    {{ $stats['pending_requests_count'] }} Menunggu
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Pengajuan formulir resmi calon operator fakultas untuk dibuatkan akun oleh Super Admin.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                        <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-3.5">Nama & Kontak</th>
                                <th class="px-6 py-3.5">Fakultas Penugasan</th>
                                <th class="px-6 py-3.5">Keterangan / Alasan</th>
                                <th class="px-6 py-3.5 text-center">Status</th>
                                <th class="px-6 py-3.5 text-right">Tindakan Super Admin</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse ($accountRequests as $req)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition">
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-gray-900 dark:text-white">{{ $req->nama }}</div>
                                        <div class="text-xs text-gray-500 font-mono">{{ $req->email }}</div>
                                        @if ($req->nip)
                                            <div class="text-[11px] text-gray-400">NIP: {{ $req->nip }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-xs font-medium text-gray-800 dark:text-gray-200">
                                        {{ $req->fakultas?->nama_fakultas ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-xs text-gray-500 max-w-xs truncate" title="{{ $req->alasan }}">
                                        {{ $req->alasan ?: '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if ($req->status === 'pending')
                                            <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300">
                                                Menunggu Persetujuan
                                            </span>
                                        @elseif ($req->status === 'approved')
                                            <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                                                Disetujui & Dibuatkan
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300">
                                                Ditolak
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        @if ($req->status === 'pending')
                                            <div class="inline-flex items-center space-x-2">
                                                <!-- Approve Button -->
                                                <button type="button" onclick="openApproveModal({{ $req->id }}, '{{ addslashes($req->nama) }}', '{{ addslashes($req->email) }}', '{{ addslashes($req->fakultas?->nama_fakultas ?? '') }}')" class="inline-flex items-center px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm transition gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    Setujui & Buat Akun
                                                </button>

                                                <!-- Reject Button -->
                                                <button type="button" onclick="openRejectModal({{ $req->id }}, '{{ addslashes($req->nama) }}')" class="inline-flex items-center px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-semibold rounded-lg transition gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                    Tolak
                                                </button>
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-400">
                                                Selesai ({{ $req->updated_at?->diffForHumans() }})
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-6 text-center text-sm text-gray-500">
                                        Belum ada permintaan pembuatan akun dari calon Admin Fakultas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Sub-section 2: Daftar Admin Fakultas Terdaftar -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-base text-gray-900 dark:text-white">Daftar Akun Admin Fakultas Terdaftar</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Pengelola operasional transkrip di tingkat fakultas.</p>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300">
                        {{ $adminFakultasList->count() }} Pengguna
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                        <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-3.5">Nama Admin</th>
                                <th class="px-6 py-3.5">Email Akses</th>
                                <th class="px-6 py-3.5">Fakultas Dikelola</th>
                                <th class="px-6 py-3.5 text-center">Status Akun</th>
                                <th class="px-6 py-3.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse ($adminFakultasList as $adm)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition">
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-gray-900 dark:text-white">{{ $adm->name }}</div>
                                        <div class="text-[11px] text-gray-400">Role: {{ $adm->role }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-xs font-mono text-gray-600 dark:text-gray-300">
                                        {{ $adm->email }}
                                    </td>
                                    <td class="px-6 py-4 text-xs">
                                        <span class="font-medium text-gray-800 dark:text-gray-200">
                                            {{ $adm->fakultas?->nama_fakultas ?? 'Semua Fakultas / Umum' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if ($adm->status_aktif)
                                            <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                                                Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300">
                                                Nonaktif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="inline-flex items-center space-x-2">
                                            <!-- Toggle Status -->
                                            <form method="POST" action="{{ route('super-admin.admin-fakultas.toggle', $adm->id) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 {{ $adm->status_aktif ? 'bg-amber-50 hover:bg-amber-100 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300' }} text-xs font-medium rounded-lg transition">
                                                    {{ $adm->status_aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                                                </button>
                                            </form>

                                            <!-- Delete -->
                                            <form method="POST" action="{{ route('super-admin.admin-fakultas.destroy', $adm->id) }}" onsubmit="return confirm('Yakin ingin menghapus akun Admin Fakultas ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center p-1.5 text-rose-600 hover:text-rose-800 hover:bg-rose-50 dark:hover:bg-rose-950/50 rounded-lg transition" title="Hapus Akun">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-6 text-center text-sm text-gray-500">
                                        Belum ada Admin Fakultas yang terdaftar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ================= TAB 3: AUDIT LOG ================= -->
        <div x-show="activeTab === 'audit-log'" class="space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-base text-gray-900 dark:text-white">Log Aktivitas & Audit Pengawasan</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Jejak rekaman operasi yang dilakukan di seluruh sistem E-Transkrip.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                        <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-3.5">Waktu</th>
                                <th class="px-6 py-3.5">Aktor / Pengguna</th>
                                <th class="px-6 py-3.5">Aksi</th>
                                <th class="px-6 py-3.5">Modul</th>
                                <th class="px-6 py-3.5">Rincian Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700 font-mono text-xs">
                            @forelse ($recentLogs as $log)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition">
                                    <td class="px-6 py-3 text-gray-400 whitespace-nowrap">
                                        {{ $log->created_at?->format('d/m/Y H:i:s') }}
                                    </td>
                                    <td class="px-6 py-3 font-semibold text-gray-900 dark:text-white">
                                        {{ $log->user?->name ?? 'Sistem' }}
                                    </td>
                                    <td class="px-6 py-3">
                                        <span class="px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                            {{ $log->action }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 text-gray-500">
                                        {{ $log->module }}
                                    </td>
                                    <td class="px-6 py-3 text-gray-600 dark:text-gray-300 font-sans">
                                        {{ $log->details }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-6 text-center text-sm font-sans text-gray-500">
                                        Belum ada aktivitas yang tercatat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ================= TAB 4: ATURAN BOBOT NILAI & IPK ================= -->
        <div x-show="activeTab === 'bobot-nilai'" class="space-y-6">
            <!-- Information Banner -->
            <div class="p-5 rounded-2xl bg-blue-50 border border-blue-200 text-blue-900 text-xs flex items-start gap-3 shadow-sm">
                <svg class="w-6 h-6 text-[#2563EB] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="space-y-1">
                    <h4 class="font-bold text-sm">Ketentuan Perhitungan Nilai & IPK UINSSC:</h4>
                    <p class="leading-relaxed">
                        Rumus IPK: <strong>Total (SKS &times; Bobot Nilai) / Total SKS yang diperhitungkan</strong>.
                        Hasil IPK ditampilkan dengan <strong>dua angka desimal</strong>. Nilai bobot terisi secara otomatis ketika huruf mutu dipilih pada input manual maupun massal.
                    </p>
                </div>
            </div>

            <!-- Bobot Table -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-100 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-gray-700 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-base text-[#142B4A] dark:text-white">Standar Bobot Huruf Mutu Akademik</h3>
                        <p class="text-xs text-slate-500">Nilai bobot baku yang digunakan untuk kalkulasi IPK transkrip resmi.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="text-xs uppercase bg-slate-50 text-slate-500 font-bold border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-3.5">Huruf Nilai</th>
                                <th class="px-6 py-3.5 text-center">Bobot Angka Resmi</th>
                                <th class="px-6 py-3.5">Keterangan / Kategori</th>
                                <th class="px-6 py-3.5 text-right">Aksi Konfigurasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs">
                            @foreach ($bobotRules as $b)
                                <tr class="hover:bg-slate-50/50">
                                    <td class="px-6 py-3.5">
                                        <span class="inline-flex items-center justify-center w-9 h-8 rounded-xl text-xs font-black shadow-sm {{ in_array($b->nilai_huruf, ['A', 'A-']) ? 'bg-emerald-100 text-emerald-800' : (in_array($b->nilai_huruf, ['B+', 'B', 'B-']) ? 'bg-blue-100 text-blue-800' : (in_array($b->nilai_huruf, ['C+', 'C']) ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800')) }}">
                                            {{ $b->nilai_huruf }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3.5 text-center font-mono font-black text-sm text-slate-800">
                                        {{ number_format($b->bobot, 2) }}
                                    </td>
                                    <td class="px-6 py-3.5 text-slate-600">
                                        {{ $b->keterangan ?? 'Standar Akademik UINSSC' }}
                                    </td>
                                    <td class="px-6 py-3.5 text-right">
                                        <button type="button" onclick="openEditBobotModal({{ $b->id }}, '{{ $b->nilai_huruf }}', {{ $b->bobot }}, '{{ addslashes($b->keterangan ?? '') }}')" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                                            Ubah Bobot
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ================= TAB 5: INTEGRASI SIAKAD ================= -->
        <div x-show="activeTab === 'siakad-integration'" class="space-y-6">
            <!-- Endpoint Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-100 dark:border-gray-700 shadow-sm p-6">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <div class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-[#2563EB] mb-2">
                            Bridge API Endpoint Terdaftar
                        </div>
                        <h3 class="font-bold text-lg text-[#142B4A] dark:text-white">
                            Integrasi Layanan SIAKAD UINSSC
                        </h3>
                        <p class="text-xs text-slate-500 mt-1">
                            Sistem terhubung dengan endpoint resmi SIAKAD untuk penarikan data Program Studi dan verifikasi identitas NIM.
                        </p>
                        <div class="mt-3 inline-flex items-center gap-2 px-3 py-1.5 bg-slate-100 rounded-xl font-mono text-xs text-slate-800">
                            <span class="px-1.5 py-0.5 rounded bg-blue-600 text-white font-bold text-[10px]">GET</span>
                            <span>https://bridge.uinssc.ac.id/api/program_studi?fakultas=2</span>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('super-admin.siakad.sync') }}">
                        @csrf
                        <button type="submit" class="px-5 py-3 bg-[#2563EB] hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-md shadow-blue-500/20 transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Uji & Sinkronisasi Bridge SIAKAD
                        </button>
                    </form>
                </div>

                <!-- Ketentuan Integrasi -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-6 pt-6 border-t border-slate-100 text-xs">
                    <div class="p-3 rounded-xl bg-slate-50">
                        <span class="font-bold text-slate-800 block mb-1">1. Pencocokan NIM</span>
                        <p class="text-slate-500">NIM digunakan sebagai kunci identitas unik antara data SIAKAD dan data sistem transkrip.</p>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50">
                        <span class="font-bold text-slate-800 block mb-1">2. Label Sumber Data</span>
                        <p class="text-slate-500">Setiap nilai diberi label <span class="font-bold text-blue-600">SIAKAD</span> atau <span class="font-bold text-emerald-600">Manual</span>.</p>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50">
                        <span class="font-bold text-slate-800 block mb-1">3. Proteksi Penimpaan</span>
                        <p class="text-slate-500">Perbedaan data ditandai sebagai konflik dan tidak ditimpa tanpa validasi manual.</p>
                    </div>
                </div>
            </div>

            <!-- Detailed Sync Summary Alert -->
            @if (session('sync_summary'))
                <div class="bg-white dark:bg-gray-800 rounded-2xl border-2 {{ session('sync_summary')['status'] === 'Berhasil' ? 'border-emerald-300 dark:border-emerald-700' : 'border-amber-300 dark:border-amber-700' }} shadow-md p-6">
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-700">
                        <div class="flex items-center gap-3">
                            <span class="w-3 h-3 rounded-full {{ session('sync_summary')['status'] === 'Berhasil' ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                            <h4 class="font-bold text-base text-gray-900 dark:text-white">Hasil Eksekusi Sinkronisasi Manual</h4>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ session('sync_summary')['status'] === 'Berhasil' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' }}">
                            {{ session('sync_summary')['status'] }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 my-4 text-xs">
                        <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                            <span class="text-gray-400 block mb-0.5">Total Berhasil</span>
                            <span class="text-xl font-black text-emerald-600 dark:text-emerald-400">{{ session('sync_summary')['total_berhasil'] }} Data</span>
                        </div>
                        <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                            <span class="text-gray-400 block mb-0.5">Total Diperbarui</span>
                            <span class="text-xl font-black text-[#2563EB] dark:text-blue-400">{{ session('sync_summary')['total_diperbarui'] }} Data</span>
                        </div>
                        <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                            <span class="text-gray-400 block mb-0.5">Total Gagal</span>
                            <span class="text-xl font-black {{ session('sync_summary')['total_gagal'] > 0 ? 'text-rose-600' : 'text-gray-600' }}">{{ session('sync_summary')['total_gagal'] }} Data</span>
                        </div>
                        <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                            <span class="text-gray-400 block mb-0.5">Waktu Eksekusi</span>
                            <span class="font-mono text-gray-800 dark:text-gray-200 font-semibold">{{ session('sync_summary')['waktu'] }}</span>
                        </div>
                    </div>

                    <div class="text-xs space-y-1.5 pt-2 border-t border-gray-100 dark:border-gray-700">
                        <div class="flex items-center gap-2">
                            <span class="text-gray-400 w-24">Sumber:</span>
                            <span class="font-mono text-gray-800 dark:text-gray-200">{{ session('sync_summary')['sumber'] }}</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="text-gray-400 w-24 shrink-0">Keterangan:</span>
                            <span class="text-gray-700 dark:text-gray-300 font-medium">{{ session('sync_summary')['pesan'] }}</span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Sync History Logs -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-100 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-base text-[#142B4A] dark:text-white">Riwayat Sinkronisasi SIAKAD</h3>
                        <p class="text-xs text-slate-500">Catatan riwayat koneksi dan penarikan data dari server Bridge SIAKAD.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="text-xs uppercase bg-slate-50 text-slate-500 font-bold border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-3.5">Waktu</th>
                                <th class="px-6 py-3.5">Endpoint / Sumber</th>
                                <th class="px-6 py-3.5 text-center">Rincian Data</th>
                                <th class="px-6 py-3.5 text-center">Status</th>
                                <th class="px-6 py-3.5">Keterangan / Log</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs">
                            @forelse ($siakadLogs as $sLog)
                                <tr class="hover:bg-slate-50/50">
                                    <td class="px-6 py-3.5 text-slate-500 whitespace-nowrap">
                                        {{ $sLog->created_at->format('d/m/Y H:i:s') }}
                                    </td>
                                    <td class="px-6 py-3.5 font-mono text-[11px] text-slate-700">
                                        {{ $sLog->endpoint }}
                                    </td>
                                    <td class="px-6 py-3.5 text-center">
                                        <div class="font-bold text-gray-900 dark:text-white">{{ $sLog->total_berhasil ?? $sLog->total_prodi }} Berhasil</div>
                                        @if ($sLog->total_diperbarui)
                                            <div class="text-[10px] text-blue-600 font-semibold">{{ $sLog->total_diperbarui }} Diperbarui</div>
                                        @endif
                                        @if ($sLog->total_gagal)
                                            <div class="text-[10px] text-rose-600 font-semibold">{{ $sLog->total_gagal }} Gagal</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3.5 text-center">
                                        @if ($sLog->status === 'sukses' || $sLog->status === 'Berhasil')
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                                Sukses
                                            </span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                                {{ ucfirst($sLog->status) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3.5 text-slate-600">
                                        <div>{{ $sLog->keterangan }}</div>
                                        @if ($sLog->pesan_error)
                                            <div class="text-[11px] text-rose-600 font-mono mt-0.5">{{ $sLog->pesan_error }}</div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-6 text-center text-slate-400">
                                        Belum ada riwayat sinkronisasi Bridge SIAKAD.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= MODALS ================= -->

    <!-- Modal 1: Buat Admin Fakultas Langsung -->
    <div id="modalTambahAdmin" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-md w-full p-6 border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-gray-700">
                <h3 class="font-bold text-lg text-gray-900 dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    Terbitkan Akun Admin Fakultas
                </h3>
                <button type="button" onclick="document.getElementById('modalTambahAdmin').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <form method="POST" action="{{ route('super-admin.admin-fakultas.store') }}" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Nama Lengkap & Gelar</label>
                    <input type="text" name="name" required placeholder="Contoh: Ahmad Fauzi, M.Kom" class="w-full text-sm rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Email Resmi (Untuk Login)</label>
                    <input type="email" name="email" required placeholder="admin.fakultas@uinssc.ac.id" class="w-full text-sm rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Fakultas Penugasan</label>
                    <select name="fakultas_id" required class="w-full text-sm rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Pilih Fakultas --</option>
                        @foreach ($fakultasList as $f)
                            <option value="{{ $f->id }}">{{ $f->nama_fakultas }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Kata Sandi Awal</label>
                    <input type="password" name="password" required placeholder="Minimal 8 karakter" class="w-full text-sm rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="pt-3 flex items-center justify-end gap-3 border-t border-gray-100 dark:border-gray-700">
                    <button type="button" onclick="document.getElementById('modalTambahAdmin').classList.add('hidden')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-xl transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-sm transition">
                        Simpan & Buat Akun
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 2: Setujui Permintaan Akun -->
    <div id="modalApproveRequest" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-md w-full p-6 border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-gray-700">
                <h3 class="font-bold text-lg text-gray-900 dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Setujui Permintaan & Terbitkan Akun
                </h3>
                <button type="button" onclick="document.getElementById('modalApproveRequest').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <form id="formApproveRequest" method="POST" action="" class="mt-4 space-y-4">
                @csrf
                <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl space-y-1 text-xs">
                    <div><span class="text-gray-500">Nama Calon:</span> <span id="approveNama" class="font-bold text-gray-900 dark:text-white"></span></div>
                    <div><span class="text-gray-500">Email:</span> <span id="approveEmail" class="font-mono text-gray-800 dark:text-gray-200"></span></div>
                    <div><span class="text-gray-500">Fakultas:</span> <span id="approveFakultas" class="font-semibold text-indigo-600 dark:text-indigo-400"></span></div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Tetapkan Kata Sandi Akun</label>
                    <input type="password" name="password" required value="password" placeholder="Password untuk Admin Fakultas" class="w-full text-sm rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:ring-emerald-500 focus:border-emerald-500">
                    <span class="text-[11px] text-gray-400 mt-1 block">Default: password (dapat diganti oleh pengguna setelah login)</span>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Catatan Persetujuan Super Admin (Opsional)</label>
                    <textarea name="catatan_super_admin" rows="2" placeholder="Contoh: Disetujui sesuai SK Dekan..." class="w-full text-xs rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:ring-emerald-500 focus:border-emerald-500"></textarea>
                </div>

                <div class="pt-3 flex items-center justify-end gap-3 border-t border-gray-100 dark:border-gray-700">
                    <button type="button" onclick="document.getElementById('modalApproveRequest').classList.add('hidden')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-xl transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl shadow-sm transition">
                        Setujui & Terbitkan Akun
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 3: Tolak Permintaan Akun -->
    <div id="modalRejectRequest" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-md w-full p-6 border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-gray-700">
                <h3 class="font-bold text-lg text-gray-900 dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Tolak Permintaan Akun
                </h3>
                <button type="button" onclick="document.getElementById('modalRejectRequest').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <form id="formRejectRequest" method="POST" action="" class="mt-4 space-y-4">
                @csrf
                <p class="text-xs text-gray-500">
                    Anda akan menolak pengajuan akun dari <span id="rejectNama" class="font-bold text-gray-800 dark:text-gray-200"></span>.
                </p>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Alasan Penolakan</label>
                    <textarea name="catatan_super_admin" required rows="3" placeholder="Sebutkan alasan penolakan (misal: berkas penugasan belum lengkap)..." class="w-full text-xs rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:ring-rose-500 focus:border-rose-500"></textarea>
                </div>

                <div class="pt-3 flex items-center justify-end gap-3 border-t border-gray-100 dark:border-gray-700">
                    <button type="button" onclick="document.getElementById('modalRejectRequest').classList.add('hidden')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-xl transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded-xl shadow-sm transition">
                        Konfirmasi Penolakan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 3: Edit Bobot Nilai -->
    <div id="modalEditBobot" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-sm w-full p-6 border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-gray-700">
                <h3 class="font-bold text-base text-gray-900 dark:text-white flex items-center gap-2">
                    Ubah Bobot Nilai <span id="bobotHurufBadge" class="px-2 py-0.5 rounded bg-blue-100 text-blue-800 text-xs font-black"></span>
                </h3>
                <button type="button" onclick="document.getElementById('modalEditBobot').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <form id="formEditBobot" method="POST" action="" class="mt-4 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Bobot Angka (0.00 - 4.00)</label>
                    <input type="number" step="0.01" min="0" max="4" id="inputBobotAngka" name="bobot" required class="w-full text-sm font-bold rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Keterangan / Kategori</label>
                    <input type="text" id="inputBobotKet" name="keterangan" class="w-full text-xs rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="pt-3 flex items-center justify-end gap-3 border-t border-gray-100 dark:border-gray-700">
                    <button type="button" onclick="document.getElementById('modalEditBobot').classList.add('hidden')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-xl transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-[#2563EB] hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-sm transition">
                        Simpan Bobot
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditBobotModal(id, huruf, bobot, keterangan) {
            document.getElementById('formEditBobot').action = '/super-admin/bobot-nilai/' + id;
            document.getElementById('bobotHurufBadge').textContent = huruf;
            document.getElementById('inputBobotAngka').value = Number(bobot).toFixed(2);
            document.getElementById('inputBobotKet').value = keterangan;
            document.getElementById('modalEditBobot').classList.remove('hidden');
        }

        function openApproveModal(id, nama, email, fakultas) {
            document.getElementById('formApproveRequest').action = '/super-admin/requests/' + id + '/approve';
            document.getElementById('approveNama').textContent = nama;
            document.getElementById('approveEmail').textContent = email;
            document.getElementById('approveFakultas').textContent = fakultas;
            document.getElementById('modalApproveRequest').classList.remove('hidden');
        }

        function openRejectModal(id, nama) {
            document.getElementById('formRejectRequest').action = '/super-admin/requests/' + id + '/reject';
            document.getElementById('rejectNama').textContent = nama;
            document.getElementById('modalRejectRequest').classList.remove('hidden');
        }
    </script>
</x-app-layout>

