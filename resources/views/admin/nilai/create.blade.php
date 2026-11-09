<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-[#142B4A] dark:text-white leading-tight">
                    {{ __('Input Nilai Manual') }}
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Tambahkan atau sesuaikan nilai mata kuliah mahasiswa secara manual dengan perhitungan bobot otomatis.
                </p>
            </div>
            <a href="{{ route('admin.nilai.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition">
                &larr; Kembali ke Daftar Nilai
            </a>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto py-4">
        @if ($errors->any())
            <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs shadow-sm">
                <span class="font-bold block mb-1">Terjadi Kesalahan:</span>
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div x-data="{
            selectedGrade: 'A',
            gradeWeights: {
                'A': 4.00,
                'A-': 3.50,
                'B+': 3.25,
                'B': 3.00,
                'B-': 2.75,
                'C+': 2.25,
                'C': 2.00,
                'D': 1.00,
                'E': 0.00
            },
            get currentWeight() {
                return (this.gradeWeights[this.selectedGrade] !== undefined) ? this.gradeWeights[this.selectedGrade].toFixed(2) : '0.00';
            }
        }" class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-100 dark:border-gray-700 shadow-sm p-6 sm:p-8">

            <form method="POST" action="{{ route('admin.nilai.store') }}" class="space-y-5">
                @csrf

                <!-- Pilih Mahasiswa -->
                <div>
                    <label for="mahasiswa_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Mahasiswa *
                    </label>
                    <select id="mahasiswa_id" name="mahasiswa_id" required class="w-full text-sm rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-[#2563EB] py-2.5 px-3">
                        <option value="">-- Pilih Mahasiswa --</option>
                        @foreach ($mahasiswas as $m)
                            <option value="{{ $m->id }}" {{ old('mahasiswa_id') == $m->id ? 'selected' : '' }}>
                                {{ $m->nim }} - {{ $m->nama }} ({{ $m->programStudi?->nama_program_studi ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Pilih Mata Kuliah -->
                <div>
                    <label for="mata_kuliah_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Mata Kuliah *
                    </label>
                    <select id="mata_kuliah_id" name="mata_kuliah_id" required class="w-full text-sm rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-[#2563EB] py-2.5 px-3">
                        <option value="">-- Pilih Mata Kuliah --</option>
                        @foreach ($mataKuliahs as $mk)
                            <option value="{{ $mk->id }}" {{ old('mata_kuliah_id') == $mk->id ? 'selected' : '' }}>
                                {{ $mk->kode_mata_kuliah }} - {{ $mk->nama_mata_kuliah }} ({{ $mk->sks }} SKS)
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Semester -->
                <div>
                    <label for="semester" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Semester Ditempuh *
                    </label>
                    <select id="semester" name="semester" required class="w-full text-sm rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-[#2563EB] py-2.5 px-3">
                        @for ($i = 1; $i <= 8; $i++)
                            <option value="{{ $i }}" {{ old('semester', '1') == $i ? 'selected' : '' }}>Semester {{ $i }}</option>
                        @endfor
                    </select>
                </div>

                <!-- Nilai Huruf & Bobot Otomatis -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <div>
                        <label for="nilai_huruf" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Nilai Huruf *
                        </label>
                        <select id="nilai_huruf" name="nilai_huruf" x-model="selectedGrade" required class="w-full text-sm font-bold rounded-xl border-slate-200 bg-white focus:ring-2 focus:ring-[#2563EB] py-2.5 px-3">
                            <option value="A">A</option>
                            <option value="A-">A-</option>
                            <option value="B+">B+</option>
                            <option value="B">B</option>
                            <option value="B-">B-</option>
                            <option value="C+">C+</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                            <option value="E">E</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">
                            Bobot Mutu (Otomatis)
                        </label>
                        <div class="h-10 flex items-center px-4 rounded-xl bg-white border border-slate-200 font-mono font-black text-lg text-emerald-600">
                            <span x-text="currentWeight"></span>
                            <span class="text-xs text-slate-400 font-normal ms-2">/ 4.00</span>
                        </div>
                    </div>
                </div>

                <!-- Ketentuan Informasi -->
                <div class="p-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-900 text-xs flex items-start gap-3">
                    <svg class="w-5 h-5 text-[#2563EB] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <p class="font-bold">Ketentuan Validasi & Konflik SIAKAD:</p>
                        <p class="mt-0.5 text-blue-800">
                            Jika nilai untuk mata kuliah ini sudah ada dari sinkronisasi SIAKAD dengan nilai yang berbeda, sistem akan menandainya sebagai <strong>Konflik</strong> dan tidak akan menimpa data secara otomatis sampai diverifikasi oleh validator.
                        </p>
                    </div>
                </div>

                <div class="pt-2 flex justify-end gap-3">
                    <a href="{{ route('admin.nilai.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl">
                        Batal
                    </a>
                    <button type="submit" class="px-5 py-2.5 bg-[#2563EB] hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-md shadow-blue-500/20">
                        Simpan Nilai Manual
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

