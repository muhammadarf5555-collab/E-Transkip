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
    <body class="font-sans antialiased min-h-screen bg-slate-50 dark:bg-[#041612] text-slate-800 dark:text-slate-100 transition-colors duration-200"
          x-data="{ 
              sidebarOpen: localStorage.getItem('sidebarOpen') !== 'false',
              mobileSidebarOpen: false 
          }">

        @php
            $user = Auth::user();
            $hasSidebar = $user && ($user->isSuperAdmin() || $user->isAdminFakultas());
        @endphp

        @if ($hasSidebar)
            <!-- LAYOUT KHUSUS ADMIN FAKULTAS & SUPER ADMIN (SIDEBAR PERSISTEN KE PINGGIR DENGAN GRADIEN HIJAU) -->
            <div class="min-h-screen flex">
                
                <!-- 1. Sidebar Desktop (Hidden on mobile) -->
                <aside :class="sidebarOpen ? 'w-64' : 'w-20'" 
                       class="hidden md:flex flex-col flex-shrink-0 transition-all duration-300 z-30 min-h-screen">
                    @include('layouts.sidebar')
                </aside>

                <!-- 2. Mobile Drawer Backdrop & Sidebar (Responsif HP & Tablet) -->
                <div x-show="mobileSidebarOpen" 
                     class="fixed inset-0 z-50 flex md:hidden" 
                     style="display: none;">
                    
                    <!-- Backdrop Gelap Transparan -->
                    <div @click="mobileSidebarOpen = false" 
                         x-show="mobileSidebarOpen"
                         x-transition:enter="transition-opacity ease-linear duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition-opacity ease-linear duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>

                    <!-- Panel Drawer Mobile -->
                    <div x-show="mobileSidebarOpen"
                         x-transition:enter="transition ease-out duration-300 transform"
                         x-transition:enter-start="-translate-x-full"
                         x-transition:enter-end="translate-x-0"
                         x-transition:leave="transition ease-in duration-200 transform"
                         x-transition:leave-start="translate-x-0"
                         x-transition:leave-end="-translate-x-full"
                         class="relative flex-1 flex flex-col max-w-xs w-full shadow-2xl z-10">
                        
                        <!-- Tombol Tutup Khusus Mobile di Pojok Atas -->
                        <div class="absolute top-4 right-3 z-50">
                            <button type="button" @click="mobileSidebarOpen = false" class="p-1.5 rounded-xl bg-black/30 hover:bg-black/50 text-white transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>

                        <!-- Konten Menu Sidebar Samping -->
                        <div class="h-full overflow-hidden" x-init="sidebarOpen = true">
                            @include('layouts.sidebar')
                        </div>
                    </div>
                </div>

                <!-- 3. Main Content Area (Tampilan Responsif Mengikuti Ukuran Layar) -->
                <div class="flex-1 flex flex-col min-w-0 w-full overflow-hidden">
                    
                    <!-- Topbar Header -->
                    @include('layouts.topbar')

                    <!-- Flash Alert Messages -->
                    <div class="px-4 sm:px-6 lg:px-8 mt-4">
                        @if (session('success'))
                            <div class="flex items-center p-4 mb-4 text-emerald-900 dark:text-emerald-200 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-500/30 shadow-sm" role="alert">
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

                    <!-- Page Heading (Jika Ada) -->
                    @isset($header)
                        <div class="bg-white dark:bg-[#07241e] border-b border-gray-200 dark:border-emerald-950/80 py-4 sm:py-5 px-4 sm:px-6 lg:px-8 shadow-sm">
                            {{ $header }}
                        </div>
                    @endisset

                    <!-- Page Main Content (Scrollable & Responsif) -->
                    <main class="flex-1 py-5 sm:py-7 px-4 sm:px-6 lg:px-8 overflow-y-auto">
                        {{ $slot }}
                    </main>

                    <!-- Footer Ringkas Khusus Admin -->
                    <footer class="bg-white dark:bg-[#07241e] border-t border-gray-200 dark:border-emerald-950/80 py-3.5 px-4 sm:px-6 lg:px-8 text-xs text-gray-500 dark:text-emerald-400/80 flex flex-col sm:flex-row items-center justify-between gap-2 mt-auto">
                        <p>&copy; {{ date('Y') }} UIN Siber Syekh Nurjati Cirebon · Portal E-Transkrip Akademik</p>
                        <p class="font-semibold text-emerald-700 dark:text-emerald-300">Pusat Teknologi Informasi dan Pangkalan Data (PTIPD)</p>
                    </footer>

                </div>
            </div>

        @else
            <!-- LAYOUT MAHASISWA & UMUM -->
            <div class="flex-1 flex flex-col min-h-screen">
                @include('layouts.navigation')

                <!-- Flash Alert Messages -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
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
                <main class="py-6 sm:py-8 flex-1">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        {{ $slot }}
                    </div>
                </main>

                <!-- Footer Mahasiswa -->
                <footer class="bg-emerald-900 dark:bg-[#0b1b33] border-t border-emerald-950 dark:border-[#182f54] text-emerald-100 dark:text-slate-300 py-10 mt-auto transition-colors duration-200">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 pb-8 border-b border-emerald-800/80 dark:border-slate-700/60 text-xs">
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
                            <div class="space-y-2">
                                <h4 class="text-sm font-bold text-white uppercase tracking-wider mb-3 flex items-center gap-1.5 text-emerald-300 dark:text-emerald-400">
                                    Kontak Resmi
                                </h4>
                                <p class="text-emerald-100/90 dark:text-slate-300"><span class="font-semibold text-white">Telepon:</span> (0231) 481264</p>
                                <p class="text-emerald-100/90 dark:text-slate-300"><span class="font-semibold text-white">Email:</span> info@uinssc.ac.id</p>
                            </div>
                            <div class="space-y-2">
                                <h4 class="text-sm font-bold text-white uppercase tracking-wider mb-3 flex items-center gap-1.5 text-emerald-300 dark:text-emerald-400">
                                    Alamat Kampus
                                </h4>
                                <p class="text-emerald-100/90 dark:text-slate-300 leading-relaxed">
                                    Gedung Rektorat UIN Siber Syekh Nurjati Cirebon<br>
                                    Jl. Perjuangan ByPass Sunyaragi, Cirebon 45131
                                </p>
                            </div>
                        </div>
                        <div class="pt-6 flex flex-col sm:flex-row items-center justify-between text-xs text-emerald-200/80 dark:text-slate-400 gap-3">
                            <p>&copy; {{ date('Y') }} UIN Siber Syekh Nurjati Cirebon (UINSSC). Hak Cipta Dilindungi Undang-Undang.</p>
                            <span class="text-white font-semibold">Pusat Teknologi Informasi dan Pangkalan Data (PTIPD)</span>
                        </div>
                    </div>
                </footer>
            </div>
        @endif

    </body>
</html>
