<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>E-Transkrip Akademik — UIN Siber Syekh Nurjati Cirebon</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen font-sans flex flex-col justify-between selection:bg-emerald-500 selection:text-white">

    <!-- Top Navigation -->
    <header class="border-b border-slate-800 bg-slate-900/80 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-emerald-500 to-teal-400 flex items-center justify-center text-slate-950 font-black text-xl shadow-lg shadow-emerald-500/25">
                    ET
                </div>
                <div>
                    <h1 class="text-base font-extrabold text-white leading-tight tracking-tight">E-Transkrip UINSSC</h1>
                    <p class="text-xs text-emerald-400 font-medium">UIN Siber Syekh Nurjati Cirebon</p>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <a href="{{ route('request-admin-fakultas.create') }}" class="hidden sm:inline-flex px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-indigo-300 text-xs font-semibold border border-indigo-500/30 transition">
                    Minta Akun Admin Fakultas &rarr;
                </a>
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-5 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 text-sm font-bold shadow-lg shadow-emerald-500/20 transition">
                            Ke Dashboard &rarr;
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 text-sm font-bold shadow-lg shadow-emerald-500/20 transition">
                            Masuk ke Sistem
                        </a>
                    @endauth
                @endif
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <main class="flex-1 max-w-7xl mx-auto px-6 py-16 flex flex-col items-center justify-center text-center">
        <!-- Badge -->
        <div class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 mb-8 backdrop-blur-sm">
            <span class="w-2 h-2 rounded-full bg-emerald-400 me-2 animate-pulse"></span>
            Sistem Informasi Transkrip Akademik Resmi & Terverifikasi
        </div>

        <h1 class="text-4xl sm:text-6xl font-black text-white max-w-4xl tracking-tight leading-tight">
            Transkrip Akademik Digital <br class="hidden sm:block">
            <span class="bg-gradient-to-r from-emerald-400 via-teal-300 to-cyan-400 bg-clip-text text-transparent">
                Cepat, Tervalidasi & Terpercaya
            </span>
        </h1>

        <p class="mt-6 text-base sm:text-lg text-slate-400 max-w-2xl leading-relaxed">
            Integrasi otomatis data nilai akademik dari <strong>SIAKAD</strong> dan data kelulusan <strong>Wisudawan</strong> dengan 3 tingkatan akses pengguna: Mahasiswa, Admin Fakultas, dan Super Admin.
        </p>

        <!-- Quick Verification Box -->
        <div class="mt-10 w-full max-w-lg bg-slate-800/80 border border-slate-700/80 p-3 rounded-2xl shadow-2xl backdrop-blur-lg">
            <form action="{{ url('/verifikasi') }}" method="GET" onsubmit="event.preventDefault(); window.location.href='/verifikasi/' + document.getElementById('search_code').value.trim();" class="flex gap-2">
                <input id="search_code" type="text" placeholder="Masukkan Kode Verifikasi Transkrip..." class="flex-1 bg-slate-900/90 text-white placeholder-slate-500 text-sm rounded-xl px-4 py-3 border border-slate-700 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" required />
                <button type="submit" class="px-6 py-3 bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-sm rounded-xl transition shadow-md shadow-emerald-500/20">
                    Cek Validitas
                </button>
            </form>
        </div>

        <!-- 3 User Roles Cards -->
        <div class="w-full mt-16 text-left">
            <div class="text-center mb-8">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-400">Arsitektur Pengguna Sistem</span>
                <h2 class="text-2xl font-extrabold text-white mt-1">3 Halaman Web & Akses Peran</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Role 1: Mahasiswa -->
                <div class="bg-slate-800/60 border border-slate-700/70 p-6 rounded-2xl hover:border-emerald-500/50 transition">
                    <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center font-bold mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" />
                        </svg>
                    </div>
                    <span class="text-xs font-bold text-emerald-400 uppercase tracking-wider">Halaman Pengguna 1</span>
                    <h3 class="text-lg font-bold text-white mt-1">Portal Mahasiswa</h3>
                    <p class="text-xs text-slate-400 mt-2 leading-relaxed">
                        Mahasiswa dapat melihat biodata, seluruh daftar mata kuliah yang telah ditempuh, bobot SKS, IPK, serta mengunduh berkas transkrip resmi berformat PDF yang telah disahkan.
                    </p>
                </div>

                <!-- Role 2: Admin Fakultas -->
                <div class="bg-slate-800/60 border border-slate-700/70 p-6 rounded-2xl hover:border-indigo-500/50 transition">
                    <div class="w-12 h-12 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center font-bold mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <span class="text-xs font-bold text-indigo-400 uppercase tracking-wider">Halaman Pengguna 2</span>
                    <h3 class="text-lg font-bold text-white mt-1">Portal Admin Fakultas</h3>
                    <p class="text-xs text-slate-400 mt-2 leading-relaxed">
                        Mengelola operasional tingkat fakultas: mengimpor data mahasiswa & nilai SIAKAD, mengelola wisudawan, mengkalkulasi, dan memvalidasi transkrip kelulusan.
                    </p>
                    <div class="mt-4 pt-3 border-t border-slate-700/60 text-xs text-amber-300 flex items-center gap-1.5">
                        <svg class="w-4 h-4 shrink-0 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                        <span>Akun wajib dibuatkan oleh Super Admin</span>
                    </div>
                </div>

                <!-- Role 3: Super Admin -->
                <div class="bg-slate-800/60 border border-slate-700/70 p-6 rounded-2xl hover:border-purple-500/50 transition">
                    <div class="w-12 h-12 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center font-bold mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <span class="text-xs font-bold text-purple-400 uppercase tracking-wider">Halaman Pengguna 3</span>
                    <h3 class="text-lg font-bold text-white mt-1">Portal Super Administrator</h3>
                    <p class="text-xs text-slate-400 mt-2 leading-relaxed">
                        Pusat pengawasan universitas secara menyeluruh: <strong>hanya bisa melihat data transkrip</strong> (read-only, tidak dapat mengubah nilai) dan berwenang menyetujui serta menerbitkan akun Admin Fakultas.
                    </p>
                    <div class="mt-4 pt-3 border-t border-slate-700/60 text-xs text-purple-300 flex items-center gap-1.5">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        <span>Mode Supervisi: Read-Only Data</span>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-800 py-8 text-center text-xs text-slate-500 bg-slate-950">
        <p>&copy; {{ date('Y') }} Sistem E-Transkrip Akademik — Universitas Islam Negeri Siber Syekh Nurjati Cirebon.</p>
    </footer>

</body>
</html>
