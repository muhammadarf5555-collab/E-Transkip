@php
    $user = Auth::user();
    $isSuperAdmin = $user && $user->isSuperAdmin();
    $isAdminFakultas = $user && $user->isAdminFakultas();
@endphp

<header class="bg-white dark:bg-[#071d18] border-b border-gray-200 dark:border-emerald-950/80 sticky top-0 z-20 shadow-sm transition-colors duration-200">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 sm:h-20 gap-4">
            
            <!-- Left: Toggle Sidebar Mobile & Desktop + Search Bar -->
            <div class="flex items-center space-x-3 flex-1 max-w-xl">
                <!-- Mobile Hamburger Toggle -->
                <button type="button" 
                        @click="mobileSidebarOpen = true" 
                        class="md:hidden p-2 rounded-xl text-gray-600 hover:text-emerald-700 hover:bg-emerald-50 dark:text-emerald-300 dark:hover:text-white dark:hover:bg-white/10 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <!-- Desktop Sidebar Toggle Button -->
                <button type="button" 
                        @click="sidebarOpen = !sidebarOpen; localStorage.setItem('sidebarOpen', sidebarOpen)" 
                        class="hidden md:flex p-2 rounded-xl text-gray-500 hover:text-emerald-700 hover:bg-emerald-50 dark:text-emerald-300 dark:hover:text-white dark:hover:bg-white/10 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                    </svg>
                </button>

                <!-- Global Search Input (Responsif HP & Desktop) -->
                <div class="relative w-full max-w-md hidden sm:block">
                    <form action="{{ $isSuperAdmin ? route('super-admin.mahasiswa.index') : route('admin.mahasiswa.index') }}" method="GET">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" 
                                   name="search" 
                                   placeholder="Cari mahasiswa, NIM, atau transkrip..." 
                                   class="w-full pl-10 pr-12 py-2 text-xs rounded-xl bg-gray-50 dark:bg-[#04241d] border border-gray-200 dark:border-emerald-900/60 text-gray-800 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
                            <button type="submit" class="absolute inset-y-1 right-1 px-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white rounded-lg flex items-center justify-center transition shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right: Dark Mode Toggle & User Profile Dropdown (QR Publik Dihapus Sesuai Permintaan) -->
            <div class="flex items-center space-x-2 sm:space-x-3">

                <!-- Dark Mode Toggle Button -->
                <button type="button"
                        @click="darkMode = !darkMode; localStorage.setItem('theme', darkMode ? 'dark' : 'light'); if (darkMode) { document.documentElement.classList.add('dark'); } else { document.documentElement.classList.remove('dark'); }"
                        class="p-2 rounded-xl text-gray-500 hover:text-gray-800 hover:bg-gray-100 dark:text-emerald-300 dark:hover:text-amber-400 dark:hover:bg-white/10 transition">
                    <svg x-show="!darkMode" class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                    <svg x-show="darkMode" class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </button>

                <!-- Profile Dropdown -->
                <div class="relative" x-data="{ userMenuOpen: false }" @click.outside="userMenuOpen = false">
                    <button @click="userMenuOpen = !userMenuOpen" 
                            class="flex items-center space-x-2.5 p-1 sm:px-3 sm:py-2 rounded-xl hover:bg-gray-100 dark:hover:bg-white/10 transition border border-transparent hover:border-gray-200 dark:hover:border-white/10">
                        <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-emerald-700 via-emerald-600 to-teal-500 text-white font-black text-sm flex items-center justify-center shadow-sm uppercase flex-shrink-0">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <div class="hidden sm:block text-left">
                            <span class="block text-xs font-bold text-gray-800 dark:text-white leading-tight truncate max-w-[130px]">
                                {{ Auth::user()->name }}
                            </span>
                            <span class="block text-[10px] font-semibold text-emerald-600 dark:text-emerald-400 leading-tight">
                                {{ $isSuperAdmin ? 'Super Administrator' : 'Admin Fakultas' }}
                            </span>
                        </div>
                        <svg class="w-4 h-4 text-gray-400 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Dropdown Panel -->
                    <div x-show="userMenuOpen" 
                         x-transition
                         class="absolute right-0 mt-2 w-56 rounded-2xl shadow-xl py-2 bg-white dark:bg-[#07241e] border border-gray-200 dark:border-emerald-900/60 z-50 text-xs">
                        <div class="px-4 py-2 border-b border-gray-100 dark:border-emerald-900/40">
                            <p class="font-bold text-gray-900 dark:text-white truncate">{{ Auth::user()->name }}</p>
                            <p class="text-[11px] text-gray-500 dark:text-gray-400 truncate">{{ Auth::user()->email }}</p>
                        </div>

                        <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2.5 text-gray-700 dark:text-gray-200 hover:bg-emerald-50 dark:hover:bg-white/10 gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span>Edit Profil Saya</span>
                        </a>

                        <div class="border-t border-gray-100 dark:border-emerald-900/40 my-1"></div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center px-4 py-2.5 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 gap-2 font-semibold">
                                <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                <span>Keluar Sistem</span>
                            </button>
                        </form>
                    </div>
                </div>

            </div>

        </div>
    </div>
</header>
