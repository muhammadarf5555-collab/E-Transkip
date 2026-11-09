<x-guest-layout>
    <div class="mb-6 text-center">
        <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 mb-3 shadow-sm">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Pengajuan Akun Admin Fakultas</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 max-w-sm mx-auto">
            Akun Admin Fakultas diterbitkan secara terpusat oleh <span class="font-semibold text-indigo-600 dark:text-indigo-400">Super Administrator</span>.
        </p>
    </div>

    <!-- Info Box -->
    <div class="mb-5 p-4 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-300 text-xs leading-relaxed flex items-start gap-3">
        <svg class="w-5 h-5 shrink-0 text-amber-600 dark:text-amber-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <div>
            <span class="font-bold">Prosedur Pembuatan Akun:</span>
            Isi formulir penugasan di bawah ini. Permintaan Anda akan masuk ke dashboard Super Admin untuk ditinjau dan dibuatkan akun resminya.
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-4 p-3 rounded-lg bg-rose-50 dark:bg-rose-950/50 border border-rose-200 text-rose-700 dark:text-rose-300 text-xs">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('request-admin-fakultas.store') }}" class="space-y-4">
        @csrf

        <!-- Nama Lengkap -->
        <div>
            <x-input-label for="nama" :value="__('Nama Lengkap & Gelar')" />
            <x-text-input id="nama" class="block mt-1 w-full" type="text" name="nama" :value="old('nama')" required autofocus placeholder="Contoh: Dr. Budi Santoso, M.T." />
            <x-input-error :messages="$errors->get('nama')" class="mt-1" />
        </div>

        <!-- Email Kedinasan -->
        <div>
            <x-input-label for="email" :value="__('Email Kedinasan / Kampus')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required placeholder="nama@uinssc.ac.id" />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- Fakultas Penugasan -->
        <div>
            <x-input-label for="fakultas_id" :value="__('Fakultas Penugasan')" />
            <select id="fakultas_id" name="fakultas_id" required class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm text-sm">
                <option value="">-- Pilih Fakultas --</option>
                @foreach ($fakultasList as $f)
                    <option value="{{ $f->id }}" {{ old('fakultas_id') == $f->id ? 'selected' : '' }}>
                        {{ $f->nama_fakultas }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('fakultas_id')" class="mt-1" />
        </div>

        <!-- NIP / NIDN -->
        <div>
            <x-input-label for="nip" :value="__('NIP / Nomor Pegawai (Opsional)')" />
            <x-text-input id="nip" class="block mt-1 w-full" type="text" name="nip" :value="old('nip')" placeholder="198501012010121001" />
            <x-input-error :messages="$errors->get('nip')" class="mt-1" />
        </div>

        <!-- Alasan / Keterangan Penugasan -->
        <div>
            <x-input-label for="alasan" :value="__('Alasan / Keterangan Penugasan Surat Tugas')" />
            <textarea id="alasan" name="alasan" rows="3" required class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm text-sm" placeholder="Contoh: Ditugaskan sebagai Operator Akademik E-Transkrip FST berdasarkan SK Dekan No. 12/2026">{{ old('alasan') }}</textarea>
            <x-input-error :messages="$errors->get('alasan')" class="mt-1" />
        </div>

        <div class="pt-2 flex flex-col sm:flex-row items-center justify-between gap-3">
            <a class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 underline" href="{{ route('login') }}">
                {{ __('Kembali ke Login') }}
            </a>

            <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-xl shadow-sm transition duration-150 text-sm gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                </svg>
                Kirim Permintaan ke Super Admin
            </button>
        </div>
    </form>
</x-guest-layout>

