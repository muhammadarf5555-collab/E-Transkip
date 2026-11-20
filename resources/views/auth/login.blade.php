<x-guest-layout :card="false">
    <div x-data="{ showPassword: false, isLoading: false }" class="max-w-4xl w-full rounded-3xl overflow-hidden grid grid-cols-1 md:grid-cols-2 my-auto" style="box-shadow: 0 25px 60px rgba(0,0,0,0.5); border: 1px solid rgba(255,255,255,0.25);">
        
        <!-- Sisi Kiri: Foto Gedung UINSSC Sunset -->
        <div class="relative min-h-[430px] flex flex-col justify-between p-7 text-white overflow-hidden" style="min-height: 430px;">
            <img src="{{ asset('images/gedung-uinssc.webp') }}" alt="Gedung UINSSC Sunset" class="absolute inset-0 w-full h-full object-cover">
            <div class="absolute inset-0" style="background: linear-gradient(to top, rgba(2, 44, 34, 0.95) 0%, rgba(2, 44, 34, 0.40) 60%, transparent 100%);"></div>

            <!-- Satu Badge Logo Terpadu di Pojok Kiri Atas (UINSSC + AICIS 2026 Saja) -->
            <div class="relative z-10 self-start">
                <div class="inline-flex items-center gap-2.5 px-3 py-1.5 rounded-2xl shadow-lg" style="background: rgba(255,255,255,0.95); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.85);">
                    <img src="{{ asset('images/logo-uinssc.png') }}" alt="Logo UINSSC" class="h-8 w-auto object-contain" />
                    <div class="w-px h-5 bg-slate-300"></div>
                    <img src="{{ asset('images/logo-aicis.png') }}" alt="Logo AICIS 2026" class="h-6 w-auto object-contain" />
                </div>
            </div>

            <!-- Identitas Kampus Singkat -->
            <div class="relative z-10 space-y-1.5">
                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold" style="background: rgba(251, 191, 36, 0.2); border: 1px solid rgba(253, 224, 71, 0.4); color: #FCD34D;">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                    Cyber Islamic University
                </div>
                <h2 class="text-2xl font-extrabold text-white tracking-tight leading-snug">
                    E-Transkrip Akademik
                </h2>
                <p class="text-xs font-medium" style="color: rgba(209, 250, 229, 0.95);">
                    UIN Siber Syekh Nurjati Cirebon
                </p>
            </div>
        </div>

        <!-- Sisi Kanan: Form Login Nuansa Hijau Gradien (Soft Emerald & Pearl) -->
        <div class="p-7 sm:p-8 flex flex-col justify-between relative overflow-hidden" style="background: linear-gradient(135deg, #F0FDF4 0%, #F5FBF7 50%, #DCFCE7 100%);">
            <!-- Ornamen Dekorasi Halus Sudut Kanan Atas -->
            <div class="absolute -top-12 -right-12 w-36 h-36 rounded-full pointer-events-none" style="background: rgba(16, 185, 129, 0.15); filter: blur(32px);"></div>
            <div class="absolute top-0 left-0 right-0 h-1.5" style="background: linear-gradient(90deg, #022c22, #059669, #FBBF24);"></div>

            <div class="relative z-10">
                <!-- Header Form Singkat & Profesional -->
                <div class="mb-4">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-700">Portal Resmi PUSTIKOM</span>
                    <h3 class="text-xl font-extrabold text-[#064e3b] mt-0.5">Masuk ke Sistem</h3>
                    <p class="text-xs text-slate-600 mt-0.5">Gunakan Email Admin atau NIM Mahasiswa</p>
                </div>

                <!-- Session Status / Errors -->
                <x-auth-session-status class="mb-2" :status="session('status')" />

                @if ($errors->any())
                    <div class="mb-3 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-start gap-2">
                        <svg class="w-4 h-4 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div>
                            @foreach ($errors->all() as $error)
                                <p class="font-medium">{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" @submit="isLoading = true" class="space-y-3.5">
                    @csrf

                    <!-- NIM / Email Input -->
                    <div>
                        <label for="login" class="block text-xs font-bold text-[#064e3b] mb-1">
                            Email Admin / NIM
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-emerald-800/50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <input id="login" type="text" name="login" value="{{ old('login', old('email')) }}" required autofocus placeholder="NIM Mahasiswa atau Email Admin" class="w-full pl-10 pr-4 py-2.5 bg-white/95 border border-emerald-200/90 rounded-xl text-sm text-slate-900 shadow-sm placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-600 transition" />
                        </div>
                    </div>

                    <!-- Password with Show/Hide Toggle -->
                    <div>
                        <label for="password" class="block text-xs font-bold text-[#064e3b] mb-1">
                            Kata Sandi
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-emerald-800/50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input id="password" :type="showPassword ? 'text' : 'password'" name="password" required placeholder="Masukkan kata sandi" class="w-full pl-10 pr-10 py-2.5 bg-white/95 border border-emerald-200/90 rounded-xl text-sm text-slate-900 shadow-sm placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-600 transition" />
                            <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-emerald-700 cursor-pointer transition" tabindex="-1" title="Tampilkan / Sembunyikan Password">
                                <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between text-xs pt-0.5">
                        <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer text-slate-600 font-medium">
                            <input id="remember_me" type="checkbox" name="remember" class="w-3.5 h-3.5 rounded border-emerald-300 text-emerald-600 focus:ring-emerald-600">
                            <span>Ingat saya</span>
                        </label>
                    </div>

                    <!-- Submit Button with Loading State (Gradien Hijau Zamrud Elegan) -->
                    <button type="submit" :disabled="isLoading" class="w-full py-2.5 px-4 rounded-xl text-white font-bold text-sm shadow-lg transition flex items-center justify-center gap-2 cursor-pointer disabled:opacity-75 disabled:cursor-not-allowed" style="background: linear-gradient(90deg, #022c22 0%, #065f46 50%, #059669 100%);">
                        <svg x-show="isLoading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" style="display: none;">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="isLoading ? 'Memverifikasi Akun...' : 'Masuk ke Sistem'"></span>
                    </button>
                </form>
            </div>

            <!-- Keamanan & Informasi Akses Resmi -->
            <div class="relative z-10 pt-4 mt-4 border-t border-emerald-200/50 flex items-center justify-between text-[11px] text-slate-500">
                <span class="flex items-center gap-1.5 text-slate-600 font-medium">
                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Akses Resmi Terenkripsi
                </span>
                <span class="text-slate-400 font-medium">Pustikom UINSSC</span>
            </div>
        </div>

    </div>
</x-guest-layout>
