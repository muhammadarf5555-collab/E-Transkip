<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 dark:text-white leading-tight">
                    {{ __('Edit Data Mahasiswa') }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Memperbarui informasi mahasiswa {{ $mahasiswa->nama }} ({{ $mahasiswa->nim }}).
                </p>
            </div>
            <a href="{{ route('admin.mahasiswa.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition">
                &larr; Kembali
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-8">
        <form method="POST" action="{{ route('admin.mahasiswa.update', $mahasiswa) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- NIM -->
                <div>
                    <x-input-label for="nim" value="Nomor Induk Mahasiswa (NIM)" />
                    <x-text-input id="nim" name="nim" type="text" class="mt-1 block w-full" value="{{ old('nim', $mahasiswa->nim) }}" required />
                </div>

                <!-- Nama -->
                <div>
                    <x-input-label for="nama" value="Nama Lengkap" />
                    <x-text-input id="nama" name="nama" type="text" class="mt-1 block w-full" value="{{ old('nama', $mahasiswa->nama) }}" required />
                </div>

                <!-- Email -->
                <div>
                    <x-input-label for="email" value="Alamat Email (Akun Login)" />
                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" value="{{ old('email', $mahasiswa->user?->email ?? $mahasiswa->email) }}" />
                </div>

                <!-- No HP -->
                <div>
                    <x-input-label for="no_hp" value="Nomor WhatsApp / HP" />
                    <x-text-input id="no_hp" name="no_hp" type="text" class="mt-1 block w-full" value="{{ old('no_hp', $mahasiswa->no_hp) }}" />
                </div>

                <!-- Fakultas -->
                <div>
                    <x-input-label for="fakultas_id" value="Fakultas" />
                    @if (auth()->user()->isAdminFakultas() && auth()->user()->fakultas)
                        <input type="hidden" name="fakultas_id" value="{{ auth()->user()->fakultas_id }}" />
                        <div class="mt-1 px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-sm font-semibold flex items-center justify-between">
                            <span>{{ auth()->user()->fakultas->nama_fakultas }}</span>
                            <span class="text-xs bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300 px-2 py-0.5 rounded-full font-medium">Terkunci ke Fakultas Anda</span>
                        </div>
                    @else
                        <select id="fakultas_id" name="fakultas_id" class="mt-1 block w-full text-sm rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">-- Pilih Fakultas --</option>
                            @foreach ($fakultasList as $fak)
                                <option value="{{ $fak->id }}" {{ old('fakultas_id', $mahasiswa->fakultas_id) == $fak->id ? 'selected' : '' }}>
                                    {{ $fak->nama_fakultas }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                </div>

                <!-- Program Studi -->
                <div>
                    <x-input-label for="program_studi_id" value="Program Studi" />
                    <select id="program_studi_id" name="program_studi_id" class="mt-1 block w-full text-sm rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">-- Pilih Program Studi --</option>
                        @foreach ($prodiList as $prodi)
                            <option value="{{ $prodi->id }}" {{ old('program_studi_id', $mahasiswa->program_studi_id) == $prodi->id ? 'selected' : '' }}>
                                {{ $prodi->nama_program_studi }}@if(auth()->user()->isSuperAdmin() && $prodi->fakultas) ({{ $prodi->fakultas->nama_fakultas }})@endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Tempat Lahir -->
                <div>
                    <x-input-label for="tempat_lahir" value="Tempat Lahir" />
                    <x-text-input id="tempat_lahir" name="tempat_lahir" type="text" class="mt-1 block w-full" value="{{ old('tempat_lahir', $mahasiswa->tempat_lahir) }}" />
                </div>

                <!-- Tanggal Lahir -->
                <div>
                    <x-input-label for="tanggal_lahir" value="Tanggal Lahir" />
                    <x-text-input id="tanggal_lahir" name="tanggal_lahir" type="date" class="mt-1 block w-full" value="{{ old('tanggal_lahir', $mahasiswa->tanggal_lahir?->format('Y-m-d')) }}" />
                </div>

                <!-- Jenis Kelamin -->
                <div>
                    <x-input-label for="jenis_kelamin" value="Jenis Kelamin" />
                    <select id="jenis_kelamin" name="jenis_kelamin" class="mt-1 block w-full text-sm rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="L" {{ old('jenis_kelamin', $mahasiswa->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('jenis_kelamin', $mahasiswa->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <!-- Status -->
                <div>
                    <x-input-label for="status" value="Status Akademik" />
                    <select id="status" name="status" class="mt-1 block w-full text-sm rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="Aktif" {{ old('status', $mahasiswa->status) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="Lulus" {{ old('status', $mahasiswa->status) == 'Lulus' ? 'selected' : '' }}>Lulus</option>
                        <option value="Cuti" {{ old('status', $mahasiswa->status) == 'Cuti' ? 'selected' : '' }}>Cuti</option>
                        <option value="Non-Aktif" {{ old('status', $mahasiswa->status) == 'Non-Aktif' ? 'selected' : '' }}>Non-Aktif</option>
                    </select>
                </div>
            </div>

            <div class="pt-6 border-t border-gray-100 dark:border-gray-700 flex justify-end gap-3">
                <a href="{{ route('admin.mahasiswa.index') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition">
                    Batal
                </a>
                <x-primary-button class="py-2.5 px-6">
                    Simpan Perubahan
                </x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
