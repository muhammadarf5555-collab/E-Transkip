<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Keaslian Transkrip Akademik — UINSSC</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 min-h-screen font-sans flex flex-col justify-between">

    <!-- Top Header -->
    <header class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 py-4 shadow-sm">
        <div class="max-w-4xl mx-auto px-4 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <img src="{{ asset('images/logo-uinssc.png') }}" alt="Logo UINSSC" class="h-10 w-auto">
                <div>
                    <h1 class="text-base font-bold text-[#142B4A] dark:text-white leading-tight">E-Transkrip Akademik</h1>
                    <p class="text-xs text-[#2563EB] font-semibold">UIN Siber Syekh Nurjati Cirebon (UINSSC)</p>
                </div>
            </div>
            <a href="{{ route('login') }}" class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:underline">
                Portal Login &rarr;
            </a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-2xl mx-auto px-4 py-12 flex-1 w-full">
        @if ($transkrip && in_array(strtolower($transkrip->status_validasi), ['valid', 'disahkan', 'terverifikasi', 'diterbitkan']))
            <!-- Valid Document Card -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl border border-emerald-100 dark:border-emerald-950/50 overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-600 to-teal-600 p-8 text-center text-white">
                    <div class="w-16 h-16 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-inner">
                        <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <span class="inline-block px-3 py-1 bg-white/25 backdrop-blur-sm rounded-full text-xs font-bold tracking-wider uppercase mb-1">
                        DOKUMEN RESMI SAH
                    </span>
                    <h2 class="text-2xl font-black mt-1">Transkrip Terverifikasi Asli</h2>
                    <p class="text-emerald-100 text-xs mt-1 max-w-md mx-auto">
                        Dokumen transkrip akademik ini terdaftar resmi dalam pangkalan data UIN Siber Syekh Nurjati Cirebon.
                    </p>
                </div>

                <div class="p-6 md:p-8">
                    <div class="bg-gray-50 dark:bg-gray-700/30 p-4 rounded-2xl border border-gray-100 dark:border-gray-700/50 mb-6">
                        <span class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider block">Nomor Kode Verifikasi</span>
                        <span class="font-mono font-bold text-sm text-gray-900 dark:text-white break-all">{{ $transkrip->kode_verifikasi }}</span>
                    </div>

                    <dl class="divide-y divide-gray-100 dark:divide-gray-700 text-sm">
                        <div class="py-3 flex justify-between">
                            <dt class="text-gray-500">Nama Mahasiswa</dt>
                            <dd class="font-bold text-gray-900 dark:text-white text-right">{{ $transkrip->mahasiswa->nama }}</dd>
                        </div>
                        <div class="py-3 flex justify-between">
                            <dt class="text-gray-500">Nomor Induk Mahasiswa (NIM)</dt>
                            <dd class="font-mono font-bold text-gray-900 dark:text-white">{{ $transkrip->mahasiswa->nim }}</dd>
                        </div>
                        <div class="py-3 flex justify-between">
                            <dt class="text-gray-500">Program Studi</dt>
                            <dd class="font-medium text-gray-800 dark:text-gray-200 text-right">{{ $transkrip->mahasiswa->programStudi?->nama_program_studi ?? '-' }}</dd>
                        </div>
                        <div class="py-3 flex justify-between">
                            <dt class="text-gray-500">Fakultas</dt>
                            <dd class="font-medium text-gray-800 dark:text-gray-200 text-right">{{ $transkrip->mahasiswa->fakultas?->nama_fakultas ?? '-' }}</dd>
                        </div>
                        <div class="py-3 flex justify-between">
                            <dt class="text-gray-500">Jenjang Pendidikan</dt>
                            <dd class="font-medium text-gray-800 dark:text-gray-200">{{ $transkrip->mahasiswa->jenjang ?? 'Sarjana (S1)' }}</dd>
                        </div>
                        <div class="py-3 flex justify-between">
                            <dt class="text-gray-500">Periode Wisuda</dt>
                            <dd class="font-semibold text-indigo-600 dark:text-indigo-400">{{ $transkrip->wisudawan?->periode_wisuda ?? '-' }}</dd>
                        </div>
                        <div class="py-3 flex justify-between">
                            <dt class="text-gray-500">Total SKS Diperoleh</dt>
                            <dd class="font-bold text-gray-900 dark:text-white">{{ $transkrip->total_sks }} SKS</dd>
                        </div>
                        <div class="py-3 flex justify-between">
                            <dt class="text-gray-500">Indeks Prestasi Kumulatif (IPK)</dt>
                            <dd class="font-mono font-black text-emerald-600 dark:text-emerald-400 text-base">{{ number_format($transkrip->ipk, 2) }}</dd>
                        </div>
                        <div class="py-3 flex justify-between">
                            <dt class="text-gray-500">Predikat Kelulusan</dt>
                            <dd class="font-bold text-gray-900 dark:text-white">{{ $transkrip->predikat ?? '-' }}</dd>
                        </div>
                    </dl>

                    <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-700 text-center">
                        <span class="text-xs text-gray-400">
                            Diverifikasi secara digital pada {{ now()->translatedFormat('d F Y H:i:s') }} WIB
                        </span>
                    </div>
                </div>
            </div>
        @else
            <!-- Invalid / Not Found Card -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl border border-rose-100 dark:border-rose-950/50 overflow-hidden text-center">
                <div class="bg-gradient-to-r from-rose-600 to-red-600 p-8 text-white">
                    <div class="w-16 h-16 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-inner">
                        <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <span class="inline-block px-3 py-1 bg-white/25 backdrop-blur-sm rounded-full text-xs font-bold tracking-wider uppercase mb-1">
                        TIDAK VALID / TIDAK DITEMUKAN
                    </span>
                    <h2 class="text-2xl font-black mt-1">Dokumen Tidak Sah</h2>
                </div>

                <div class="p-8">
                    <p class="text-sm text-gray-600 dark:text-gray-300 max-w-md mx-auto leading-relaxed">
                        Kode verifikasi <span class="font-mono font-bold text-rose-600">{{ $kode }}</span> tidak terdaftar sebagai transkrip resmi yang telah divalidasi, atau transkrip tersebut masih berstatus draft/dalam proses revisi.
                    </p>

                    <div class="mt-8">
                        <a href="{{ url('/') }}" class="inline-flex items-center px-5 py-2.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-xs font-semibold rounded-xl hover:opacity-90 transition">
                            Kembali ke Halaman Utama
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </main>

    <!-- Footer -->
    <footer class="py-6 text-center text-xs text-gray-400 border-t border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-800">
        <p>&copy; {{ date('Y') }} Sistem E-Transkrip Akademik — UIN Siber Syekh Nurjati Cirebon.</p>
    </footer>

</body>
</html>
