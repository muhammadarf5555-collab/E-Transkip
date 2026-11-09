<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-white leading-tight">
                    {{ __('Pendaftaran Wisudawan Baru') }}
                </h2>
                <p class="text-sm text-emerald-100 dark:text-gray-400 mt-1">
                    Mendaftarkan mahasiswa ke dalam antrean kelulusan, SK Yudisium, dan ijazah UINSSC.
                </p>
            </div>
            <a href="{{ route('admin.wisudawan.index') }}" class="px-4 py-2 bg-white/15 hover:bg-white/25 text-white text-sm font-semibold rounded-xl transition">
                &larr; Kembali
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-8">
        <form method="POST" action="{{ route('admin.wisudawan.store') }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- NIM -->
                <div>
                    <x-input-label for="nim" value="Pilih Mahasiswa / Masukkan NIM" />
                    <input list="mahasiswa_list" id="nim" name="nim" class="mt-1 block w-full text-sm rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-emerald-500 focus:ring-emerald-500" placeholder="Ketik NIM..." value="{{ old('nim') }}" required />
                    <datalist id="mahasiswa_list">
                        @foreach ($mahasiswas as $m)
                            <option value="{{ $m->nim }}">{{ $m->nama }} ({{ $m->programStudi?->nama_program_studi }})</option>
                        @endforeach
                    </datalist>
                </div>

                <!-- Nomor Pendaftaran -->
                <div>
                    <x-input-label for="nomor_pendaftaran_wisuda" value="Nomor Pendaftaran Wisuda" />
                    <x-text-input id="nomor_pendaftaran_wisuda" name="nomor_pendaftaran_wisuda" type="text" class="mt-1 block w-full" value="{{ old('nomor_pendaftaran_wisuda', 'WIS-'.date('Y').'-'.rand(100, 999)) }}" />
                </div>

                <!-- Nomor SK Yudisium -->
                <div>
                    <x-input-label for="no_sk" value="Nomor SK Yudisium (E-Wisuda)" />
                    <x-text-input id="no_sk" name="no_sk" type="text" class="mt-1 block w-full" value="{{ old('no_sk') }}" placeholder="Contoh: SK/YUDISIUM/UINSSC/2026/0001" />
                </div>

                <!-- Tanggal SK Yudisium -->
                <div>
                    <x-input-label for="tanggal_sk" value="Tanggal SK Yudisium" />
                    <x-text-input id="tanggal_sk" name="tanggal_sk" type="date" class="mt-1 block w-full" value="{{ old('tanggal_sk') }}" />
                </div>

                <!-- Nomor Ijazah -->
                <div>
                    <x-input-label for="no_ijazah" value="Nomor Seri Ijazah (E-Wisuda)" />
                    <x-text-input id="no_ijazah" name="no_ijazah" type="text" class="mt-1 block w-full" value="{{ old('no_ijazah') }}" placeholder="Contoh: IJZ/UINSSC/2026/..." />
                </div>

                <!-- Tanggal Ijazah -->
                <div>
                    <x-input-label for="tanggal_ijazah" value="Tanggal Ijazah" />
                    <x-text-input id="tanggal_ijazah" name="tanggal_ijazah" type="date" class="mt-1 block w-full" value="{{ old('tanggal_ijazah') }}" />
                </div>

                <!-- IPK & Total SKS -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="ipk" value="IPK Lulusan" />
                        <x-text-input id="ipk" name="ipk" type="number" step="0.01" min="0" max="4" class="mt-1 block w-full" value="{{ old('ipk', '3.75') }}" />
                    </div>
                    <div>
                        <x-input-label for="total_sks" value="Total SKS" />
                        <x-text-input id="total_sks" name="total_sks" type="number" min="0" max="300" class="mt-1 block w-full" value="{{ old('total_sks', '144') }}" />
                    </div>
                </div>

                <!-- Lama Studi & Semester Lulus -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="lama_studi" value="Lama Studi (Semester)" />
                        <x-text-input id="lama_studi" name="lama_studi" type="number" min="1" max="20" class="mt-1 block w-full" value="{{ old('lama_studi', '8') }}" />
                    </div>
                    <div>
                        <x-input-label for="semester_lulus" value="Semester Lulus" />
                        <x-text-input id="semester_lulus" name="semester_lulus" type="text" class="mt-1 block w-full" value="{{ old('semester_lulus', '8 (Genap)') }}" />
                    </div>
                </div>

                <!-- Gelar Singkat & Gelar Penuh -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="gelar" value="Gelar Singkat" />
                        <x-text-input id="gelar" name="gelar" type="text" class="mt-1 block w-full" value="{{ old('gelar') }}" placeholder="Misal: S.Kom., S.Pd." />
                    </div>
                    <div>
                        <x-input-label for="gelar_full" value="Gelar Penuh" />
                        <x-text-input id="gelar_full" name="gelar_full" type="text" class="mt-1 block w-full" value="{{ old('gelar_full') }}" placeholder="Misal: Sarjana Komputer" />
                    </div>
                </div>

                <!-- Periode Wisuda -->
                <div>
                    <x-input-label for="periode_wisuda" value="Periode Wisuda" />
                    <x-text-input id="periode_wisuda" name="periode_wisuda" type="text" class="mt-1 block w-full" value="{{ old('periode_wisuda', 'Wisuda Ganjil '.date('Y')) }}" placeholder="Contoh: Wisuda Ganjil 2026" required />
                </div>

                <!-- Tanggal Wisuda -->
                <div>
                    <x-input-label for="tanggal_wisuda" value="Tanggal Pelaksanaan Wisuda" />
                    <x-text-input id="tanggal_wisuda" name="tanggal_wisuda" type="date" class="mt-1 block w-full" value="{{ old('tanggal_wisuda') }}" />
                </div>

                <!-- Predikat Kelulusan -->
                <div>
                    <x-input-label for="predikat_kelulusan" value="Predikat Kelulusan" />
                    <select id="predikat_kelulusan" name="predikat_kelulusan" class="mt-1 block w-full text-sm rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="Dengan Pujian (Cum Laude)" {{ old('predikat_kelulusan') == 'Dengan Pujian (Cum Laude)' ? 'selected' : '' }}>Dengan Pujian (Cum Laude)</option>
                        <option value="Sangat Memuaskan" {{ old('predikat_kelulusan') == 'Sangat Memuaskan' ? 'selected' : '' }}>Sangat Memuaskan</option>
                        <option value="Memuaskan" {{ old('predikat_kelulusan') == 'Memuaskan' ? 'selected' : '' }}>Memuaskan</option>
                        <option value="Cukup" {{ old('predikat_kelulusan') == 'Cukup' ? 'selected' : '' }}>Cukup</option>
                    </select>
                </div>

                <!-- Status Validasi & Eligible -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="status_validasi" value="Status Pendaftaran" />
                        <select id="status_validasi" name="status_validasi" class="mt-1 block w-full text-sm rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="Valid" {{ old('status_validasi') == 'Valid' ? 'selected' : '' }}>Valid</option>
                            <option value="Menunggu Validasi" {{ old('status_validasi') == 'Menunggu Validasi' ? 'selected' : '' }}>Menunggu Validasi</option>
                            <option value="Draft" {{ old('status_validasi') == 'Draft' ? 'selected' : '' }}>Draft</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label for="is_eligible" value="Eligible Yudisium" />
                        <select id="is_eligible" name="is_eligible" class="mt-1 block w-full text-sm rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="1" selected>Ya (Eligible)</option>
                            <option value="0">Belum Eligible</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Judul Skripsi -->
            <div>
                <x-input-label for="judul_skripsi" value="Judul Tugas Akhir / Skripsi" />
                <textarea id="judul_skripsi" name="judul_skripsi" rows="3" class="mt-1 block w-full text-sm rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-emerald-500 focus:ring-emerald-500" placeholder="Tuliskan judul skripsi mahasiswa secara lengkap...">{{ old('judul_skripsi') }}</textarea>
            </div>

            <div class="pt-6 border-t border-gray-100 dark:border-gray-700 flex justify-end gap-3">
                <a href="{{ route('admin.wisudawan.index') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition">
                    Batal
                </a>
                <x-primary-button class="py-2.5 px-6">
                    Daftarkan Wisudawan
                </x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
