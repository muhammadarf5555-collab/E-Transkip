<x-guest-layout>
    <div x-data="{ showPassword: false, isLoading: false }" class="space-y-6">
        <!-- Brand Header with Official UINSSC Logo -->
        <div class="text-center">
            <img src="{{ asset('images/logo-uinssc.png') }}" alt="Logo UINSSC" class="w-20 h-auto mx-auto mb-3 drop-shadow-md">
            <h1 class="text-2xl font-bold text-[#142B4A] tracking-tight">
                E-Transkrip UINSSC
            </h1>
            <p class="text-xs text-slate-500 font-medium mt-1">
                UIN Siber Syekh Nurjati Cirebon
            </p>
            <div class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold bg-blue-50 text-[#2563EB] mt-3">
                Portal Tunggal Akademik & Kelulusan
            </div>
        </div>

        <!-- Session Status / Errors -->
        <x-auth-session-status class="mb-2" :status="session('status')" />

        @if ($errors->any())
            <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-start gap-2.5">
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

        <form method="POST" action="{{ route('login') }}" @submit="isLoading = true" class="space-y-4">
            @csrf

            <!-- NIM / Email Input -->
            <div>
                <label for="login" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    NIM Mahasiswa atau Email Admin
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <input id="login" type="text" name="login" value="{{ old('login', old('email')) }}" required autofocus placeholder="Contoh: 220001 atau admin@uinssc.ac.id" class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:border-transparent transition" />
                </div>
            </div>

            <!-- Password with Show/Hide Toggle -->
            <div>
                <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Kata Sandi (Password)
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <input id="password" :type="showPassword ? 'text' : 'password'" name="password" required placeholder="Masukkan kata sandi akun Anda" class="w-full pl-10 pr-11 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:border-transparent transition" />
                    <!-- Show/Hide Toggle Button -->
                    <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition" tabindex="-1" title="Tampilkan / Sembunyikan Password">
                        <!-- Eye open icon -->
                        <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <!-- Eye slash icon -->
                        <svg x-show="showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between text-xs pt-1">
                <label for="remember_me" class="inline-flex items-center cursor-pointer">
                    <input id="remember_me" type="checkbox" name="remember" class="w-4 h-4 rounded text-[#2563EB] border-slate-300 focus:ring-[#2563EB]">
                    <span class="ms-2 text-slate-600 font-medium">Ingat saya</span>
                </label>
            </div>

            <!-- Submit Button with Loading State -->
            <div class="pt-2">
                <button type="submit" :disabled="isLoading" class="w-full py-3 px-4 rounded-xl bg-[#2563EB] hover:bg-blue-700 text-white font-bold text-sm shadow-md shadow-blue-500/20 transition flex items-center justify-center gap-2 cursor-pointer disabled:opacity-75 disabled:cursor-not-allowed">
                    <!-- Spinner when loading -->
                    <svg x-show="isLoading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" style="display: none;">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-text="isLoading ? 'Memverifikasi Akun...' : 'Masuk ke Sistem'"></span>
                </button>
            </div>
        </form>

        <!-- Quick Demo Switcher -->
        <div class="pt-4 border-t border-slate-100 space-y-2">
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider text-center">
                Pilih Akses Cepat Demo (3 Role):
            </p>

            <div class="grid grid-cols-3 gap-2">
                <!-- Mahasiswa -->
                <button type="button" @click="document.getElementById('login').value = '220001'; document.getElementById('password').value = 'password'" class="p-2.5 rounded-xl bg-slate-50 hover:bg-blue-50 border border-slate-200 text-left transition group">
                    <span class="block text-[11px] font-bold text-slate-800 group-hover:text-[#2563EB]">Mahasiswa</span>
                    <span class="block text-[10px] text-slate-500 font-mono">220001</span>
                </button>

                <!-- Admin Fakultas -->
                <button type="button" @click="document.getElementById('login').value = 'admin@uinssc.ac.id'; document.getElementById('password').value = 'password'" class="p-2.5 rounded-xl bg-slate-50 hover:bg-blue-50 border border-slate-200 text-left transition group">
                    <span class="block text-[11px] font-bold text-slate-800 group-hover:text-[#2563EB]">Admin Fak.</span>
                    <span class="block text-[10px] text-slate-500 font-mono">admin@...</span>
                </button>

                <!-- Super Admin -->
                <button type="button" @click="document.getElementById('login').value = 'superadmin@uinssc.ac.id'; document.getElementById('password').value = 'password'" class="p-2.5 rounded-xl bg-slate-50 hover:bg-blue-50 border border-slate-200 text-left transition group">
                    <span class="block text-[11px] font-bold text-slate-800 group-hover:text-[#2563EB]">Super Admin</span>
                    <span class="block text-[10px] text-slate-500 font-mono">super@...</span>
                </button>
            </div>
            <p class="text-[10px] text-center text-slate-400 italic">
                *Password demo untuk semua akun: <code class="font-bold text-slate-600">password</code>
            </p>
        </div>
    </div>
</x-guest-layout>
