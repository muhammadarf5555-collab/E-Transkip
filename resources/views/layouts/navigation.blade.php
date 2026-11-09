<nav x-data="{ open: false, masterOpen: false, siakadOpen: false }" class="bg-emerald-700 dark:bg-[#0b1b33] border-b border-emerald-800 dark:border-[#162a4d] sticky top-0 z-40 shadow-md transition-colors duration-200">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 sm:h-20">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 group">
                        <div class="p-2 rounded-xl bg-white/15 dark:bg-white/10 border border-white/20 dark:border-white/10 group-hover:bg-white/25 dark:group-hover:bg-white/20 transition shadow-sm">
                            <img src="{{ asset('images/logo-uinssc.png') }}" alt="Logo UINSSC" class="h-9 sm:h-10 w-auto">
                        </div>
                        <div>
                            <span class="block text-base sm:text-lg font-black tracking-tight text-white group-hover:text-emerald-100 transition leading-tight">E-Transkrip</span>
                            <span class="block text-[10px] sm:text-[11px] font-bold text-emerald-200 dark:text-emerald-400 tracking-wider leading-tight">UINSSC Cirebon</span>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-6 sm:-my-px sm:ms-8 md:flex items-center">
                    @if (Auth::user()->isSuperAdmin())
                        <!-- Super Admin Links -->
                        <a href="{{ route('super-admin.dashboard') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-semibold leading-5 transition duration-150 {{ request()->routeIs('super-admin.dashboard') ? 'border-white text-white font-bold' : 'border-transparent text-emerald-100 dark:text-slate-300 hover:text-white hover:border-emerald-300' }}">
                            {{ __('Dashboard Supervisi') }}
                        </a>

                        <!-- Master Data Dropdown (Super Admin) -->
                        <div class="relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
                            <button @click="open = ! open" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-semibold leading-5 transition duration-150 {{ request()->routeIs('admin.fakultas.*', 'admin.program-studi.*', 'admin.mata-kuliah.*') ? 'border-white text-white font-bold' : 'border-transparent text-emerald-100 dark:text-slate-300 hover:text-white hover:border-emerald-300' }}">
                                <span>Master Data</span>
                                <svg class="ms-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="open" x-transition class="absolute left-0 mt-2 w-52 rounded-xl shadow-xl py-1.5 bg-emerald-800 dark:bg-[#0f2342] border border-emerald-600 dark:border-[#1e3a66] z-50">
                                <a href="{{ route('admin.fakultas.index') }}" class="block px-4 py-2.5 text-xs text-white hover:bg-emerald-600 dark:hover:bg-emerald-600/30">Fakultas & Bridge API</a>
                                <a href="{{ route('admin.program-studi.index') }}" class="block px-4 py-2.5 text-xs text-white hover:bg-emerald-600 dark:hover:bg-emerald-600/30">Program Studi</a>
                                <a href="{{ route('admin.mata-kuliah.index') }}" class="block px-4 py-2.5 text-xs text-white hover:bg-emerald-600 dark:hover:bg-emerald-600/30">Mata Kuliah</a>
                            </div>
                        </div>

                        <a href="{{ route('super-admin.mahasiswa.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-semibold leading-5 transition duration-150 {{ request()->routeIs('super-admin.mahasiswa.*') ? 'border-white text-white font-bold' : 'border-transparent text-emerald-100 dark:text-slate-300 hover:text-white hover:border-emerald-300' }}">
                            {{ __('Data Mahasiswa') }}
                        </a>

                        <a href="{{ route('super-admin.wisudawan.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-semibold leading-5 transition duration-150 {{ request()->routeIs('super-admin.wisudawan.*') ? 'border-white text-white font-bold' : 'border-transparent text-emerald-100 dark:text-slate-300 hover:text-white hover:border-emerald-300' }}">
                            {{ __('Data Wisudawan') }}
                        </a>

                        <a href="{{ route('super-admin.nilai.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-semibold leading-5 transition duration-150 {{ request()->routeIs('super-admin.nilai.*') ? 'border-white text-white font-bold' : 'border-transparent text-emerald-100 dark:text-slate-300 hover:text-white hover:border-emerald-300' }}">
                            {{ __('Kelola Nilai') }}
                        </a>

                        <a href="{{ route('super-admin.audit-log.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-semibold leading-5 transition duration-150 {{ request()->routeIs('super-admin.audit-log.*') ? 'border-white text-white font-bold' : 'border-transparent text-emerald-100 dark:text-slate-300 hover:text-white hover:border-emerald-300' }}">
                            {{ __('Audit Log') }}
                        </a>

                    @elseif (Auth::user()->isAdminFakultas())
                        <!-- Admin Fakultas Links -->
                        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-semibold leading-5 transition duration-150 {{ request()->routeIs('admin.dashboard') ? 'border-white text-white font-bold' : 'border-transparent text-emerald-100 dark:text-slate-300 hover:text-white hover:border-emerald-300' }}">
                            {{ __('Dashboard') }}
                        </a>

                        <!-- Master Data Dropdown -->
                        <div class="relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
                            <button @click="open = ! open" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-semibold leading-5 transition duration-150 {{ request()->routeIs('admin.fakultas.*', 'admin.program-studi.*', 'admin.mata-kuliah.*', 'admin.mahasiswa.*') ? 'border-white text-white font-bold' : 'border-transparent text-emerald-100 dark:text-slate-300 hover:text-white hover:border-emerald-300' }}">
                                <span>Master Data</span>
                                <svg class="ms-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="open" x-transition class="absolute left-0 mt-2 w-52 rounded-xl shadow-xl py-1.5 bg-emerald-800 dark:bg-[#0f2342] border border-emerald-600 dark:border-[#1e3a66] z-50">
                                <a href="{{ route('admin.fakultas.index') }}" class="block px-4 py-2.5 text-xs text-white hover:bg-emerald-600 dark:hover:bg-emerald-600/30">Fakultas</a>
                                <a href="{{ route('admin.program-studi.index') }}" class="block px-4 py-2.5 text-xs text-white hover:bg-emerald-600 dark:hover:bg-emerald-600/30">Program Studi</a>
                                <a href="{{ route('admin.mata-kuliah.index') }}" class="block px-4 py-2.5 text-xs text-white hover:bg-emerald-600 dark:hover:bg-emerald-600/30">Mata Kuliah</a>
                                <a href="{{ route('admin.mahasiswa.index') }}" class="block px-4 py-2.5 text-xs text-white hover:bg-emerald-600 dark:hover:bg-emerald-600/30">Mahasiswa</a>
                            </div>
                        </div>

                        <a href="{{ route('admin.nilai.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-semibold leading-5 transition duration-150 {{ request()->routeIs('admin.nilai.*') ? 'border-white text-white font-bold' : 'border-transparent text-emerald-100 dark:text-slate-300 hover:text-white hover:border-emerald-300' }}">
                            {{ __('Kelola Nilai') }}
                        </a>

                        <a href="{{ route('admin.koreksi.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-semibold leading-5 transition duration-150 {{ request()->routeIs('admin.koreksi.*') ? 'border-white text-white font-bold' : 'border-transparent text-emerald-100 dark:text-slate-300 hover:text-white hover:border-emerald-300' }}">
                            {{ __('Koreksi Nilai') }}
                        </a>

                        <a href="{{ route('admin.wisudawan.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-semibold leading-5 transition duration-150 {{ request()->routeIs('admin.wisudawan.*') ? 'border-white text-white font-bold' : 'border-transparent text-emerald-100 dark:text-slate-300 hover:text-white hover:border-emerald-300' }}">
                            {{ __('Wisudawan') }}
                        </a>

                        <a href="{{ route('admin.transkrip.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-semibold leading-5 transition duration-150 {{ request()->routeIs('admin.transkrip.*') ? 'border-white text-white font-bold' : 'border-transparent text-emerald-100 dark:text-slate-300 hover:text-white hover:border-emerald-300' }}">
                            {{ __('Transkrip') }}
                        </a>

                        <a href="{{ route('admin.audit-log.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-semibold leading-5 transition duration-150 {{ request()->routeIs('admin.audit-log.*') ? 'border-white text-white font-bold' : 'border-transparent text-emerald-100 dark:text-slate-300 hover:text-white hover:border-emerald-300' }}">
                            {{ __('Audit Log') }}
                        </a>
                    @else
                        <!-- Mahasiswa Links (Sejajar di Atas & Rapi) -->
                        <a href="{{ route('mahasiswa.dashboard') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-bold leading-5 transition duration-150 {{ request()->routeIs('mahasiswa.dashboard') && !request()->has('view') ? 'border-white text-white' : 'border-transparent text-emerald-100 dark:text-slate-300 hover:text-white hover:border-emerald-300' }}">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            {{ __('Transkrip Saya') }}
                        </a>

                        <a href="{{ route('mahasiswa.dashboard', ['view' => 'koreksi']) }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 {{ request('view') === 'koreksi' ? 'border-white text-white font-bold' : 'border-transparent text-emerald-100 dark:text-slate-300 hover:text-white hover:border-emerald-300' }}">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                            {{ __('Pengajuan Koreksi') }}
                        </a>

                        <a href="{{ route('mahasiswa.dashboard', ['view' => 'notifikasi']) }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 relative {{ request('view') === 'notifikasi' ? 'border-white text-white font-bold' : 'border-transparent text-emerald-100 dark:text-slate-300 hover:text-white hover:border-emerald-300' }}">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                            {{ __('Notifikasi') }}
                        </a>
                    @endif
                </div>
            </div>

            <!-- Settings & Dark Mode Toggle -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-3">
                <!-- Theme Toggle Button (Siang/Malam) -->
                <button type="button" @click="darkMode = !darkMode; localStorage.setItem('theme', darkMode ? 'dark' : 'light')" class="p-2.5 rounded-xl border border-white/20 dark:border-white/10 bg-white/10 dark:bg-white/5 text-amber-300 hover:bg-white/20 dark:hover:bg-white/10 transition cursor-pointer" title="Ganti Mode Siang/Malam">
                    <!-- Icon Sun (Siang) -->
                    <svg x-show="darkMode" class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <!-- Icon Moon (Malam) -->
                    <svg x-show="!darkMode" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>

                <!-- Role Badge -->
                @if (Auth::user()->isSuperAdmin())
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-white/20 text-white border border-white/30">
                        Super Admin
                    </span>
                @elseif (Auth::user()->isAdminFakultas())
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-white/20 text-white border border-white/30" title="{{ Auth::user()->fakultas?->nama_fakultas }}">
                        Admin {{ Auth::user()->fakultas?->nama_fakultas_singkat ?? 'Fakultas' }}
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-white/20 text-white border border-white/30">
                        Mahasiswa
                    </span>
                @endif

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3.5 py-2 border border-white/20 dark:border-white/10 text-sm leading-4 font-semibold rounded-xl text-white bg-white/10 dark:bg-white/10 hover:bg-white/20 dark:hover:bg-white/20 shadow-sm transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-2">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profil Saya') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();" class="text-rose-600 dark:text-rose-400">
                                {{ __('Keluar (Log Out)') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <!-- Theme Toggle Button Mobile -->
                <button type="button" @click="darkMode = !darkMode; localStorage.setItem('theme', darkMode ? 'dark' : 'light')" class="p-2 mr-2 rounded-xl border border-white/20 dark:border-white/10 bg-white/10 dark:bg-white/5 text-amber-300 hover:bg-white/20 transition" title="Ganti Mode">
                    <svg x-show="darkMode" class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    <svg x-show="!darkMode" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                </button>

                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-xl text-white hover:bg-emerald-800 dark:hover:bg-slate-800 focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu (Mobile) -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-emerald-800 dark:bg-[#0b1b33] border-b border-emerald-900 dark:border-[#162a4d] transition-colors">
        <div class="pt-2 pb-3 space-y-1 px-3">
            @if (Auth::user()->isSuperAdmin())
                <x-responsive-nav-link :href="route('super-admin.dashboard')" :active="request()->routeIs('super-admin.dashboard')">
                    {{ __('Dashboard Supervisi') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.fakultas.index')" :active="request()->routeIs('admin.fakultas.*')">
                    {{ __('Fakultas & Bridge') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.program-studi.index')" :active="request()->routeIs('admin.program-studi.*')">
                    {{ __('Program Studi') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.mata-kuliah.index')" :active="request()->routeIs('admin.mata-kuliah.*')">
                    {{ __('Mata Kuliah') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('super-admin.mahasiswa.index')" :active="request()->routeIs('super-admin.mahasiswa.*')">
                    {{ __('Data Mahasiswa') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('super-admin.wisudawan.index')" :active="request()->routeIs('super-admin.wisudawan.*')">
                    {{ __('Data Wisudawan') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('super-admin.nilai.index')" :active="request()->routeIs('super-admin.nilai.*')">
                    {{ __('Kelola Nilai') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('super-admin.audit-log.index')" :active="request()->routeIs('super-admin.audit-log.*')">
                    {{ __('Audit Log') }}
                </x-responsive-nav-link>
            @elseif (Auth::user()->isAdminFakultas())
                <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.fakultas.index')" :active="request()->routeIs('admin.fakultas.*')">
                    {{ __('Fakultas') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.program-studi.index')" :active="request()->routeIs('admin.program-studi.*')">
                    {{ __('Program Studi') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.mata-kuliah.index')" :active="request()->routeIs('admin.mata-kuliah.*')">
                    {{ __('Mata Kuliah') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.nilai.index')" :active="request()->routeIs('admin.nilai.*')">
                    {{ __('Kelola Nilai') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.koreksi.index')" :active="request()->routeIs('admin.koreksi.*')">
                    {{ __('Koreksi Nilai') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.mahasiswa.index')" :active="request()->routeIs('admin.mahasiswa.*')">
                    {{ __('Data Mahasiswa') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.wisudawan.index')" :active="request()->routeIs('admin.wisudawan.*')">
                    {{ __('Data Wisudawan') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.transkrip.index')" :active="request()->routeIs('admin.transkrip.*')">
                    {{ __('Transkrip Nilai') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.audit-log.index')" :active="request()->routeIs('admin.audit-log.*')">
                    {{ __('Audit Log') }}
                </x-responsive-nav-link>
            @else
                <x-responsive-nav-link :href="route('mahasiswa.dashboard')" :active="request()->routeIs('mahasiswa.dashboard') && !request()->has('view')">
                    {{ __('Transkrip Saya') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('mahasiswa.dashboard', ['view' => 'koreksi'])" :active="request('view') === 'koreksi'">
                    {{ __('Pengajuan Koreksi') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('mahasiswa.dashboard', ['view' => 'notifikasi'])" :active="request('view') === 'notifikasi'">
                    {{ __('Notifikasi') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-3 pb-3 border-t border-emerald-900 dark:border-slate-800 px-3">
            <div class="px-3 py-2 bg-emerald-900/60 dark:bg-slate-800/60 rounded-xl mb-2">
                <div class="font-bold text-sm text-white">{{ Auth::user()->name }}</div>
                <div class="font-medium text-xs text-emerald-200 dark:text-slate-400">{{ Auth::user()->email }} ({{ ucfirst(Auth::user()->role) }})</div>
            </div>

            <div class="space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profil') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
