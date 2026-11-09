<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-white leading-tight">
                    {{ __('Edit Data Wisudawan & Administrasi E-Wisuda') }}
                </h2>
                <p class="text-sm text-emerald-100 dark:text-gray-400 mt-1">
                    Memperbarui data pendaftaran wisuda, SK Yudisium, dan Ijazah NIM {{ $wisudawan->nim }}.
                </p>
            </div>
            <a href="{{ route('admin.wisudawan.index') }}" class="px-4 py-2 bg-white/15 hover:bg-white/25 text-white text-sm font-semibold rounded-xl transition">
                &larr; Kembali
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-8">
        <form method="POST" action="{{ route('admin.wisudawan.update', $wisudawan) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- NIM -->
                <div>
                    <x-input-label for="nim" value="NIM Mahasiswa" />
                    <x-text-input id="nim" name="nim" type="text" class="mt-1 block w-full" value="{{ old('nim', $wisudawan->nim) }}" required />
                </div>

                <!-- Nomor Pendaftaran -->
                <div>
                    <x-input-label for="nomor_pendaftaran_wisuda" value="Nomor Pendaftaran Wisuda" />
                    <x-text-input id="nomor_pendaftaran_wisuda" name="nomor_pendaftaran_wisuda" type="text" class="mt-1 block w-full" value="{{ old('nomor_pendaftaran_wisuda', $wisudawan->nomor_pendaftaran_wisuda) }}" />
                </div>

                <!-- Nomor SK Yudisium -->
                <div>
                    <x-input-label for="no_sk" value="Nomor SK Yudisium (E-Wisuda)" />
                    <x-text-input id="no_sk" name="no_sk" type="text" class="mt-1 block w-full font-mono" value="{{ old('no_sk', $wisudawan->no_sk) }}" />
                </div>

                <!-- Tanggal SK Yudisium -->
                <div>
                    <x-input-label for="tanggal_sk" value="Tanggal SK Yudisium" />
                    <x-text-input id="tanggal_sk" name="tanggal_sk" type="date" class="mt-1 block w-full" value="{{ old('tanggal_sk', $wisudawan->tanggal_sk?->format('Y-m-d')) }}" />
                </div>

                <!-- Nomor Ijazah -->
                <div>
                    <x-input-label for="no_ijazah" value="Nomor Seri Ijazah (E-Wisuda)" />
                    <x-text-input id="no_ijazah" name="no_ijazah" type="text" class="mt-1 block w-full font-mono" value="{{ old('no_ijazah', $wisudawan->no_ijazah) }}" />
                </div>

                <!-- Tanggal Ijazah -->
                <div>
                    <x-input-label for="tanggal_ijazah" value="Tanggal Ijazah" />
                    <x-text-input id="tanggal_ijazah" name="tanggal_ijazah" type="date" class="mt-1 block w-full" value="{{ old('tanggal_ijazah', $wisudawan->tanggal_ijazah?->format('Y-m-d')) }}" />
                </div>

                <!-- IPK & Total SKS -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="ipk" value="IPK Lulusan" />
                        <x-text-input id="ipk" name="ipk" type="number" step="0.01" min="0" max="4" class="mt-1 block w-full" value="{{ old('ipk', $wisudawan->ipk) }}" />
                    </div>
                    <div>
                        <x-input-label for="total_sks" value="Total SKS" />
                        <x-text-input id="total_sks" name="total_sks" type="number" min="0" max="300" class="mt-1 block w-full" value="{{ old('total_sks', $wisudawan->total_sks) }}" />
                    </div>
                </div>

                <!-- Lama Studi & Semester Lulus -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="lama_studi" value="Lama Studi (Semester)" />
                        <x-text-input id="lama_studi" name="lama_studi" type="number" min="1" max="20" class="mt-1 block w-full" value="{{ old('lama_studi', $wisudawan->lama_studi) }}" />
                    </div>
                    <div>
                        <x-input-label for="semester_lulus" value="Semester Lulus" />
                        <x-text-input id="semester_lulus" name="semester_lulus" type="text" class="mt-1 block w-full" value="{{ old('semester_lulus', $wisudawan->semester_lulus) }}" />
                    </div>
                </div>

                <!-- Gelar Singkat & Gelar Penuh -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="gelar" value="Gelar Singkat" />
                        <x-text-input id="gelar" name="gelar" type="text" class="mt-1 block w-full" value="{{ old('gelar', $wisudawan->gelar) }}" />
                    </div>
                    <div>
                        <x-input-label for="gelar_full" value="Gelar Penuh" />
                        <x-text-input id="gelar_full" name="gelar_full" type="text" class="mt-1 block w-full" value="{{ old('gelar_full', $wisudawan->gelar_full) }}" />
                    </div>
                </div>

                <!-- Periode Wisuda -->
                <div>
                    <x-input-label for="periode_wisuda" value="Periode Wisuda" />
                    <x-text-input id="periode_wisuda" name="periode_wisuda" type="text" class="mt-1 block w-full" value="{{ old('periode_wisuda', $wisudawan->periode_wisuda) }}" required />
                </div>

                <!-- Tanggal Wisuda -->
                <div>
                    <x-input-label for="tanggal_wisuda" value="Tanggal Pelaksanaan Wisuda" />
                    <x-text-input id="tanggal_wisuda" name="tanggal_wisuda" type="date" class="mt-1 block w-full" value="{{ old('tanggal_wisuda', $wisudawan->tanggal_wisuda?->format('Y-m-d')) }}" />
                </div>

                <!-- Predikat Kelulusan -->
                <div>
                    <x-input-label for="predikat_kelulusan" value="Predikat Kelulusan" />
                    <select id="predikat_kelulusan" name="predikat_kelulusan" class="mt-1 block w-full text-sm rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="Dengan Pujian (Cum Laude)" {{ old('predikat_kelulusan', $wisudawan->predikat_kelulusan) == 'Dengan Pujian (Cum Laude)' ? 'selected' : '' }}>Dengan Pujian (Cum Laude)</option>
                        <option value="Sangat Memuaskan" {{ old('predikat_kelulusan', $wisudawan->predikat_kelulusan) == 'Sangat Memuaskan' ? 'selected' : '' }}>Sangat Memuaskan</option>
                        <option value="Memuaskan" {{ old('predikat_kelulusan', $wisudawan->predikat_kelulusan) == 'Memuaskan' ? 'selected' : '' }}>Memuaskan</option>
                        <option value="Cukup" {{ old('predikat_kelulusan', $wisudawan->predikat_kelulusan) == 'Cukup' ? 'selected' : '' }}>Cukup</option>
                    </select>
                </div>

                <!-- Status Validasi & Eligible -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="status_validasi" value="Status Pendaftaran" />
                        <select id="status_validasi" name="status_validasi" class="mt-1 block w-full text-sm rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="Valid" {{ old('status_validasi', $wisudawan->status_validasi) == 'Valid' ? 'selected' : '' }}>Valid</option>
                            <option value="Menunggu Validasi" {{ old('status_validasi', $wisudawan->status_validasi) == 'Menunggu Validasi' ? 'selected' : '' }}>Menunggu Validasi</option>
                            <option value="Draft" {{ old('status_validasi', $wisudawan->status_validasi) == 'Draft' ? 'selected' : '' }}>Draft</option>
                            <option value="Ditolak" {{ old('status_validasi', $wisudawan->status_validasi) == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label for="is_eligible" value="Eligible Yudisium" />
                        <select id="is_eligible" name="is_eligible" class="mt-1 block w-full text-sm rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="1" {{ old('is_eligible', $wisudawan->is_eligible ?? true) ? 'selected' : '' }}>Ya (Eligible)</option>
                            <option value="0" {{ ! old('is_eligible', $wisudawan->is_eligible ?? true) ? 'selected' : '' }}>Belum Eligible</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Judul Skripsi -->
            <div>
                <x-input-label for="judul_skripsi" value="Judul Tugas Akhir / Skripsi" />
                <textarea id="judul_skripsi" name="judul_skripsi" rows="3" class="mt-1 block w-full text-sm rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-emerald-500 focus:ring-emerald-500">{{ old('judul_skripsi', $wisudawan->judul_skripsi) }}</textarea>
            </div>

            <div class="pt-6 border-t border-gray-100 dark:border-gray-700 flex justify-end gap-3">
                <a href="{{ route('admin.wisudawan.index') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition">
                    Batal
                </a>
                <x-primary-button class="py-2.5 px-6">
                    Simpan Perubahan
                </x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
