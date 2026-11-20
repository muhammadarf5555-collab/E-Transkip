@php
    $user = Auth::user();
    $isSuperAdmin = $user && $user->isSuperAdmin();
    $isAdminFakultas = $user && $user->isAdminFakultas();
@endphp

<!-- SIDEBAR (Modern Dark Emerald Navy: Gradien Hijau Zamrud Gelap Mewah) -->
<div class="flex flex-col h-full bg-gradient-to-b from-[#022c22] via-[#064e3b] to-[#043327] text-emerald-100 border-r border-emerald-900/60 shadow-xl">
    
    <!-- Header Brand di Sidebar -->
    <div class="h-20 flex items-center px-4 border-b border-emerald-800/40 justify-between bg-black/15">
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 overflow-hidden group">
            <div class="p-2 rounded-xl bg-white/10 border border-white/15 flex-shrink-0 group-hover:bg-white/20 transition shadow-sm">
                <img src="{{ asset('images/logo-uinssc.png') }}" alt="Logo UINSSC" class="h-8 w-auto">
            </div>
            <div x-show="sidebarOpen" x-transition.opacity class="whitespace-nowrap">
                <span class="block text-sm font-black text-white tracking-wider uppercase leading-none">E-Transkrip</span>
                <span class="block text-[10px] font-bold text-amber-300 tracking-wider mt-1">UINSSC Cirebon</span>
            </div>
        </a>
    </div>

    <!-- Role Pill Badge Box -->
    <div class="p-4" x-show="sidebarOpen" x-transition.opacity>
        <div class="bg-gradient-to-r from-amber-500 via-amber-600 to-amber-700 text-white rounded-2xl p-3 shadow-md border border-amber-400/30">
            <div class="flex items-center space-x-2">
                <div class="p-1 rounded-lg bg-black/20">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <span class="text-xs font-black tracking-wider uppercase">
                    {{ $isSuperAdmin ? 'Super Admin' : 'Admin Fakultas' }}
                </span>
            </div>
            <div class="text-[11px] text-amber-100 font-medium mt-1 truncate">
                {{ $isAdminFakultas && $user->fakultas ? $user->fakultas->nama_fakultas : 'Pusat Supervisi Universitas' }}
            </div>
        </div>
    </div>

    <!-- Navigation Menu Items -->
    <div class="flex-1 overflow-y-auto py-3 px-3 space-y-5 text-xs font-medium">
        
        <!-- MENU UTAMA -->
        <div>
            <div x-show="sidebarOpen" class="px-3 mb-2 text-[10px] font-bold uppercase tracking-wider text-emerald-300/70">
                Menu Utama
            </div>
            <div class="space-y-1">
                @if ($isSuperAdmin)
                    <a href="{{ route('super-admin.dashboard') }}" 
                       class="flex items-center px-3 py-2.5 rounded-xl transition group {{ request()->routeIs('super-admin.dashboard') ? 'bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-bold shadow-lg shadow-emerald-500/30' : 'hover:bg-white/10 hover:text-white text-emerald-100/90' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('super-admin.dashboard') ? 'text-white' : 'text-emerald-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        <span x-show="sidebarOpen" class="ms-3 truncate">Dashboard Supervisi</span>
                    </a>
                @else
                    <a href="{{ route('admin.dashboard') }}" 
                       class="flex items-center px-3 py-2.5 rounded-xl transition group {{ request()->routeIs('admin.dashboard') ? 'bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-bold shadow-lg shadow-emerald-500/30' : 'hover:bg-white/10 hover:text-white text-emerald-100/90' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-emerald-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span x-show="sidebarOpen" class="ms-3 truncate">Dashboard</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- OPERASIONAL AKADEMIK -->
        <div>
            <div x-show="sidebarOpen" class="px-3 mb-2 text-[10px] font-bold uppercase tracking-wider text-emerald-300/70">
                Operasional Akademik
            </div>
            <div class="space-y-1">
                @if ($isSuperAdmin)
                    <a href="{{ route('super-admin.mahasiswa.index') }}" 
                       class="flex items-center px-3 py-2.5 rounded-xl transition group {{ request()->routeIs('super-admin.mahasiswa.*') ? 'bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-bold shadow-lg shadow-emerald-500/30' : 'hover:bg-white/10 hover:text-white text-emerald-100/90' }}">
                        <svg class="w-5 h-5 flex-shrink-0 text-emerald-300 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span x-show="sidebarOpen" class="ms-3 truncate">Data Mahasiswa</span>
                    </a>

                    <a href="{{ route('super-admin.wisudawan.index') }}" 
                       class="flex items-center px-3 py-2.5 rounded-xl transition group {{ request()->routeIs('super-admin.wisudawan.*') ? 'bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-bold shadow-lg shadow-emerald-500/30' : 'hover:bg-white/10 hover:text-white text-emerald-100/90' }}">
                        <svg class="w-5 h-5 flex-shrink-0 text-emerald-300 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                        </svg>
                        <span x-show="sidebarOpen" class="ms-3 truncate">Data Wisudawan</span>
                    </a>

                    <a href="{{ route('super-admin.nilai.index') }}" 
                       class="flex items-center px-3 py-2.5 rounded-xl transition group {{ request()->routeIs('super-admin.nilai.*') ? 'bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-bold shadow-lg shadow-emerald-500/30' : 'hover:bg-white/10 hover:text-white text-emerald-100/90' }}">
                        <svg class="w-5 h-5 flex-shrink-0 text-emerald-300 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                        <span x-show="sidebarOpen" class="ms-3 truncate">Kelola Nilai</span>
                    </a>
                @else
                    <a href="{{ route('admin.transkrip.index') }}" 
                       class="flex items-center px-3 py-2.5 rounded-xl transition group {{ request()->routeIs('admin.transkrip.*') ? 'bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-bold shadow-lg shadow-emerald-500/30' : 'hover:bg-white/10 hover:text-white text-emerald-100/90' }}">
                        <svg class="w-5 h-5 flex-shrink-0 text-emerald-300 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span x-show="sidebarOpen" class="ms-3 truncate">Transkrip Nilai</span>
                    </a>

                    <a href="{{ route('admin.nilai.index') }}" 
                       class="flex items-center px-3 py-2.5 rounded-xl transition group {{ request()->routeIs('admin.nilai.*') ? 'bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-bold shadow-lg shadow-emerald-500/30' : 'hover:bg-white/10 hover:text-white text-emerald-100/90' }}">
                        <svg class="w-5 h-5 flex-shrink-0 text-emerald-300 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <span x-show="sidebarOpen" class="ms-3 truncate">Kelola Nilai</span>
                    </a>

                    <a href="{{ route('admin.koreksi.index') }}" 
                       class="flex items-center px-3 py-2.5 rounded-xl transition group {{ request()->routeIs('admin.koreksi.*') ? 'bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-bold shadow-lg shadow-emerald-500/30' : 'hover:bg-white/10 hover:text-white text-emerald-100/90' }}">
                        <svg class="w-5 h-5 flex-shrink-0 text-emerald-300 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span x-show="sidebarOpen" class="ms-3 truncate">Pengajuan Koreksi</span>
                    </a>

                    <a href="{{ route('admin.wisudawan.index') }}" 
                       class="flex items-center px-3 py-2.5 rounded-xl transition group {{ request()->routeIs('admin.wisudawan.*') ? 'bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-bold shadow-lg shadow-emerald-500/30' : 'hover:bg-white/10 hover:text-white text-emerald-100/90' }}">
                        <svg class="w-5 h-5 flex-shrink-0 text-emerald-300 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                        </svg>
                        <span x-show="sidebarOpen" class="ms-3 truncate">Data Wisudawan</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- MASTER DATA & PANGKALAN DATA -->
        <div>
            <div x-show="sidebarOpen" class="px-3 mb-2 text-[10px] font-bold uppercase tracking-wider text-emerald-300/70">
                Master Data & Pangkalan Data
            </div>
            <div class="space-y-1">
                @if ($isAdminFakultas)
                    <a href="{{ route('admin.mahasiswa.index') }}" 
                       class="flex items-center px-3 py-2.5 rounded-xl transition group {{ request()->routeIs('admin.mahasiswa.*') ? 'bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-bold shadow-lg shadow-emerald-500/30' : 'hover:bg-white/10 hover:text-white text-emerald-100/90' }}">
                        <svg class="w-5 h-5 flex-shrink-0 text-emerald-300 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span x-show="sidebarOpen" class="ms-3 truncate">Direktori Mahasiswa</span>
                    </a>
                @endif

                <a href="{{ route('admin.fakultas.index') }}" 
                   class="flex items-center px-3 py-2.5 rounded-xl transition group {{ request()->routeIs('admin.fakultas.*') ? 'bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-bold shadow-lg shadow-emerald-500/30' : 'hover:bg-white/10 hover:text-white text-emerald-100/90' }}">
                    <svg class="w-5 h-5 flex-shrink-0 text-emerald-300 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span x-show="sidebarOpen" class="ms-3 truncate">Fakultas & Bridge</span>
                </a>

                <a href="{{ route('admin.program-studi.index') }}" 
                   class="flex items-center px-3 py-2.5 rounded-xl transition group {{ request()->routeIs('admin.program-studi.*') ? 'bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-bold shadow-lg shadow-emerald-500/30' : 'hover:bg-white/10 hover:text-white text-emerald-100/90' }}">
                    <svg class="w-5 h-5 flex-shrink-0 text-emerald-300 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                    </svg>
                    <span x-show="sidebarOpen" class="ms-3 truncate">Program Studi</span>
                </a>

                <a href="{{ route('admin.mata-kuliah.index') }}" 
                   class="flex items-center px-3 py-2.5 rounded-xl transition group {{ request()->routeIs('admin.mata-kuliah.*') ? 'bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-bold shadow-lg shadow-emerald-500/30' : 'hover:bg-white/10 hover:text-white text-emerald-100/90' }}">
                    <svg class="w-5 h-5 flex-shrink-0 text-emerald-300 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <span x-show="sidebarOpen" class="ms-3 truncate">Mata Kuliah</span>
                </a>
            </div>
        </div>

        <!-- SISTEM & AUDIT LOG -->
        <div>
            <div x-show="sidebarOpen" class="px-3 mb-2 text-[10px] font-bold uppercase tracking-wider text-emerald-300/70">
                Sistem & Keamanan
            </div>
            <div class="space-y-1">
                @if ($isSuperAdmin)
                    <a href="{{ route('super-admin.audit-log.index') }}" 
                       class="flex items-center px-3 py-2.5 rounded-xl transition group {{ request()->routeIs('super-admin.audit-log.*') ? 'bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-bold shadow-lg shadow-emerald-500/30' : 'hover:bg-white/10 hover:text-white text-emerald-100/90' }}">
                        <svg class="w-5 h-5 flex-shrink-0 text-emerald-300 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <span x-show="sidebarOpen" class="ms-3 truncate">Audit Log Aktivitas</span>
                    </a>
                @else
                    <a href="{{ route('admin.audit-log.index') }}" 
                       class="flex items-center px-3 py-2.5 rounded-xl transition group {{ request()->routeIs('admin.audit-log.*') ? 'bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-bold shadow-lg shadow-emerald-500/30' : 'hover:bg-white/10 hover:text-white text-emerald-100/90' }}">
                        <svg class="w-5 h-5 flex-shrink-0 text-emerald-300 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <span x-show="sidebarOpen" class="ms-3 truncate">Audit Log Aktivitas</span>
                    </a>
                @endif
            </div>
        </div>

    </div>

    <!-- Toggle Collapse Button di Bawah -->
    <div class="p-3 border-t border-emerald-800/40 flex justify-center bg-black/10">
        <button @click="sidebarOpen = !sidebarOpen; localStorage.setItem('sidebarOpen', sidebarOpen)" 
                class="w-full flex items-center justify-center p-2 rounded-xl bg-white/5 hover:bg-white/15 text-emerald-200 hover:text-white transition">
            <svg class="w-5 h-5 transition-transform duration-200" :class="{ 'rotate-180': !sidebarOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
            </svg>
            <span x-show="sidebarOpen" class="ms-2 text-xs font-semibold">Kecilkan Menu</span>
        </button>
    </div>
</div>
