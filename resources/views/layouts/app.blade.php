<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full" x-data="{ darkMode: localStorage.getItem('theme') === 'dark' }" :class="{ 'dark': darkMode }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'E-Transkrip UINSSC') }}</title>

        <link rel="icon" type="image/png" href="{{ asset('images/logo-uinssc.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

        <script>
            if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased min-h-screen flex flex-col transition-colors duration-200">
        <div class="flex-1">
            @include('layouts.navigation')

            <!-- Flash Alert Messages -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                @if (session('success'))
                    <div class="flex items-center p-4 mb-4 text-emerald-800 dark:text-emerald-200 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-500/30 shadow-sm" role="alert">
                        <svg class="flex-shrink-0 inline w-5 h-5 me-3 text-emerald-600 dark:text-emerald-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
                        </svg>
                        <div class="text-sm font-medium">
                            {{ session('success') }}
                        </div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="flex items-center p-4 mb-4 text-rose-800 dark:text-rose-200 rounded-2xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-500/30 shadow-sm" role="alert">
                        <svg class="flex-shrink-0 inline w-5 h-5 me-3 text-rose-600 dark:text-rose-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 11.793a1 1 0 1 1-1.414 1.414L10 11.414l-2.293 2.293a1 1 0 0 1-1.414-1.414L8.586 10 6.293 7.707a1 1 0 0 1 1.414-1.414L10 8.586l2.293-2.293a1 1 0 0 1 1.414 1.414L11.414 10l2.293 2.293Z"/>
                        </svg>
                        <div class="text-sm font-medium">
                            {{ session('error') }}
                        </div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="p-4 mb-4 text-rose-800 dark:text-rose-200 rounded-2xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-500/30 shadow-sm" role="alert">
                        <div class="font-bold text-sm mb-1 text-rose-700 dark:text-rose-300">Terdapat beberapa kesalahan input:</div>
                        <ul class="list-disc list-inside text-xs sm:text-sm space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-emerald-800 dark:bg-[#0e213d] border-b border-emerald-900 dark:border-[#1b345b] shadow-sm transition-colors duration-200">
                    <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="py-6 sm:py-8">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    {{ $slot }}
                </div>
            </main>
        </div>

        <!-- Footer: Mode Siang Hijau Kampus (Emerald 800), Mode Malam Deep Midnight Navy -->
        <footer class="bg-emerald-900 dark:bg-[#0b1b33] border-t border-emerald-950 dark:border-[#182f54] text-emerald-100 dark:text-slate-300 py-10 mt-auto transition-colors duration-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8 pb-8 border-b border-emerald-800/80 dark:border-slate-700/60 text-xs">
                    <!-- Kolom 1: Profil Institusi -->
                    <div class="md:col-span-2 space-y-3">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 rounded-xl bg-white/15 dark:bg-white/10 border border-white/20 dark:border-white/10 shadow-sm">
                                <img src="{{ asset('images/logo-uinssc.png') }}" class="h-10 w-auto" alt="Logo UINSSC">
                            </div>
                            <div>
                                <span class="block text-base font-black text-white tracking-wide">E-Transkrip Akademik</span>
                                <span class="block text-[11px] font-bold text-emerald-300 dark:text-emerald-400">UIN Siber Syekh Nurjati Cirebon</span>
                            </div>
                        </div>
                        <p class="text-emerald-100/90 dark:text-slate-300 leading-relaxed max-w-lg">
                            Sistem Informasi Layanan Transkrip Akademik Resmi berbasis tanda tangan elektronik dan verifikasi digital terintegrasi pangkalan data SIAKAD & Bridge API Pustikom.
                        </p>
                    </div>

                    <!-- Kolom 2: Kontak Resmi -->
                    <div class="space-y-2">
                        <h4 class="text-sm font-bold text-white uppercase tracking-wider mb-3 flex items-center gap-1.5 text-emerald-300 dark:text-emerald-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            Kontak Resmi
                        </h4>
                        <p class="flex items-center gap-2 text-emerald-100/90 dark:text-slate-300">
                            <span class="font-semibold text-white">Telepon:</span> (0231) 481264
                        </p>
                        <p class="flex items-center gap-2 text-emerald-100/90 dark:text-slate-300">
                            <span class="font-semibold text-white">Fax:</span> (0231) 489926
                        </p>
                        <p class="flex items-center gap-2 text-emerald-100/90 dark:text-slate-300">
                            <span class="font-semibold text-white">Email:</span> info@uinssc.ac.id
                        </p>
                        <p class="flex items-center gap-2 text-emerald-100/90 dark:text-slate-300">
                            <span class="font-semibold text-white">Portal:</span> <a href="https://uinssc.ac.id" target="_blank" class="text-emerald-300 dark:text-emerald-400 hover:underline">www.uinssc.ac.id</a>
                        </p>
                    </div>

                    <!-- Kolom 3: Alamat Kampus -->
                    <div class="space-y-2">
                        <h4 class="text-sm font-bold text-white uppercase tracking-wider mb-3 flex items-center gap-1.5 text-emerald-300 dark:text-emerald-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Alamat Kampus
                        </h4>
                        <p class="text-emerald-100/90 dark:text-slate-300 leading-relaxed">
                            Gedung Rektorat UIN Siber Syekh Nurjati Cirebon<br>
                            Jl. Perjuangan ByPass Sunyaragi, Kec. Kesambi, Kota Cirebon, Jawa Barat 45131
                        </p>
                    </div>
                </div>

                <div class="pt-6 flex flex-col sm:flex-row items-center justify-between text-xs text-emerald-200/80 dark:text-slate-400 gap-3">
                    <p>&copy; {{ date('Y') }} UIN Siber Syekh Nurjati Cirebon (UINSSC). Hak Cipta Dilindungi Undang-Undang.</p>
                    <div class="flex items-center space-x-4">
                        <span class="text-white font-semibold">Pusat Teknologi Informasi dan Pangkalan Data (PTIPD)</span>
                    </div>
                </div>
            </div>
        </footer>
    </body>
</html>
