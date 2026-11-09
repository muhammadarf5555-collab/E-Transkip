<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-[#142B4A] dark:text-white leading-tight">
                    {{ __('Input Nilai Secara Massal') }}
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Masukkan nilai sekaligus untuk seluruh mahasiswa dalam satu Program Studi dan Mata Kuliah tertentu.
                </p>
            </div>
            <a href="{{ route('admin.nilai.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition">
                &larr; Kembali ke Daftar Nilai
            </a>
        </div>
    </x-slot>

    <!-- Selection Bar -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-100 dark:border-gray-700 shadow-sm p-6 mb-6">
        <form method="GET" action="{{ route('admin.nilai.mass') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
            <!-- Program Studi -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    1. Program Studi *
                </label>
                <select name="program_studi_id" required onchange="this.form.submit()" class="w-full text-xs rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-[#2563EB] py-2.5 px-3">
                    <option value="">-- Pilih Program Studi --</option>
                    @foreach ($programStudis as $prodi)
                        <option value="{{ $prodi->id }}" {{ $selectedProdi == $prodi->id ? 'selected' : '' }}>
                            {{ $prodi->nama_program_studi }} ({{ $prodi->fakultas?->nama_fakultas ?? '-' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Mata Kuliah -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    2. Mata Kuliah *
                </label>
                <select name="mata_kuliah_id" required class="w-full text-xs rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-[#2563EB] py-2.5 px-3">
                    <option value="">-- Pilih Mata Kuliah --</option>
                    @foreach ($mataKuliahs as $mk)
                        <option value="{{ $mk->id }}" {{ $selectedMk == $mk->id ? 'selected' : '' }}>
                            {{ $mk->kode_mata_kuliah }} - {{ $mk->nama_mata_kuliah }} ({{ $mk->sks }} SKS)
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Semester -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    3. Semester Ditempuh *
                </label>
                <select name="semester" required class="w-full text-xs rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-[#2563EB] py-2.5 px-3">
                    @for ($i = 1; $i <= 8; $i++)
                        <option value="{{ $i }}" {{ $selectedSemester == $i ? 'selected' : '' }}>Semester {{ $i }}</option>
                    @endfor
                </select>
            </div>

            <!-- Button -->
            <div>
                <button type="submit" class="w-full py-2.5 px-4 bg-[#142B4A] hover:bg-slate-800 text-white text-xs font-bold rounded-xl transition shadow-sm">
                    Tampilkan Mahasiswa
                </button>
            </div>
        </form>
    </div>

    <!-- Student Grade Table -->
    @if ($selectedProdi && $mahasiswas->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-100 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-base text-[#142B4A] dark:text-white">Formulir Pengisian Nilai Kelas</h3>
                    <p class="text-xs text-slate-500">Isi huruf nilai untuk setiap mahasiswa yang lulus atau menempuh mata kuliah ini.</p>
                </div>
                <span class="text-xs font-bold px-3 py-1 bg-blue-50 text-[#2563EB] rounded-full">
                    {{ $mahasiswas->count() }} Mahasiswa Terdaftar
                </span>
            </div>

            <form method="POST" action="{{ route('admin.nilai.mass.store') }}">
                @csrf
                <input type="hidden" name="mata_kuliah_id" value="{{ $selectedMk }}">
                <input type="hidden" name="semester" value="{{ $selectedSemester }}">

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="text-[11px] uppercase tracking-wider bg-slate-50 text-slate-500 font-bold border-b border-slate-100">
                            <tr>
                                <th class="px-5 py-3 w-12 text-center">No</th>
                                <th class="px-5 py-3">NIM</th>
                                <th class="px-5 py-3">Nama Mahasiswa</th>
                                <th class="px-5 py-3 text-center">Status</th>
                                <th class="px-5 py-3 text-center w-48">Nilai Huruf</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs">
                            @foreach ($mahasiswas as $idx => $m)
                                <tr class="hover:bg-slate-50/50">
                                    <td class="px-5 py-3 text-center font-bold text-slate-400">
                                        {{ $idx + 1 }}
                                    </td>
                                    <td class="px-5 py-3 font-mono font-bold text-slate-900">
                                        {{ $m->nim }}
                                    </td>
                                    <td class="px-5 py-3 font-medium text-slate-800">
                                        {{ $m->nama }}
                                    </td>
                                    <td class="px-5 py-3 text-center">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700">
                                            {{ $m->status ?? 'Aktif' }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-center">
                                        <select name="grades[{{ $m->id }}]" class="w-full text-xs font-bold rounded-xl border-slate-200 bg-slate-50 py-1.5 px-3 focus:bg-white focus:ring-2 focus:ring-[#2563EB]">
                                            <option value="">-- Kosong (Lewati) --</option>
                                            <option value="A">A (4.00)</option>
                                            <option value="A-">A- (3.50)</option>
                                            <option value="B+">B+ (3.25)</option>
                                            <option value="B">B (3.00)</option>
                                            <option value="B-">B- (2.75)</option>
                                            <option value="C+">C+ (2.25)</option>
                                            <option value="C">C (2.00)</option>
                                            <option value="D">D (1.00)</option>
                                            <option value="E">E (0.00)</option>
                                        </select>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-6 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                    <p class="text-xs text-slate-500">
                        * Mahasiswa dengan pilihan <em>Kosong (Lewati)</em> tidak akan dibuatkan entri nilai baru.
                    </p>
                    <button type="submit" class="px-6 py-2.5 bg-[#2563EB] hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-md shadow-blue-500/20 transition">
                        Simpan Seluruh Nilai Massal
                    </button>
                </div>
            </form>
        </div>
    @elseif ($selectedProdi)
        <div class="p-12 text-center bg-white rounded-2xl border border-slate-100">
            <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <h4 class="font-bold text-slate-700">Tidak ada mahasiswa di Program Studi ini</h4>
            <p class="text-xs text-slate-400 mt-1">Silakan pilih program studi lain atau tambahkan mahasiswa terlebih dahulu.</p>
        </div>
    @else
        <div class="p-12 text-center bg-white rounded-2xl border border-slate-100">
            <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
            </svg>
            <h4 class="font-bold text-slate-700">Pilih Program Studi dan Mata Kuliah</h4>
            <p class="text-xs text-slate-400 mt-1">Gunakan formulir pemilihan di atas untuk memuat daftar mahasiswa yang akan diinput nilainya.</p>
        </div>
    @endif
</x-app-layout>

