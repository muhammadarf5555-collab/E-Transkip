<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">
            Pendaftaran Akun Mahasiswa
        </h2>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
            Buat akun mahasiswa untuk mengakses transkrip nilai akademik.
        </p>
    </div>

    <!-- Admin Fakultas Notice -->
    <div class="mb-5 p-3 rounded-xl bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800 text-xs text-indigo-900 dark:text-indigo-300">
        <span class="font-bold">Calon Admin Fakultas?</span>
        Admin Fakultas tidak mendaftar di formulir ini.
        <a href="{{ route('request-admin-fakultas.create') }}" class="font-bold text-indigo-600 dark:text-indigo-400 underline block mt-1">
            Klik di sini untuk minta pembuatan akun ke Super Admin &rarr;
        </a>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Nama Lengkap')" />
            <x-text-input id="name" class="block mt-1 w-full text-sm" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Nama mahasiswa sesuai KTM" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full text-sm" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="mhs@uinssc.ac.id" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full text-sm"
                            type="password"
                            name="password"
                            required autocomplete="new-password" placeholder="Minimal 8 karakter" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full text-sm"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi kata sandi" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between pt-2">
            <a class="underline text-xs text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100" href="{{ route('login') }}">
                {{ __('Sudah punya akun? Masuk') }}
            </a>

            <x-primary-button class="py-2 px-4 bg-emerald-600 hover:bg-emerald-700 text-xs font-semibold rounded-xl">
                {{ __('Daftar Mahasiswa') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
