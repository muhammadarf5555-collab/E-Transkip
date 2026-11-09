<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 dark:text-white leading-tight">
                    {{ __('Detail Profil & Akademik Mahasiswa') }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    {{ $mahasiswa->nama }} &bull; <span class="font-mono">{{ $mahasiswa->nim }}</span>
                </p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.mahasiswa.edit', $mahasiswa) }}" class="px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-sm font-semibold rounded-xl transition">
                    Edit Profil
                </a>
                <a href="{{ route('admin.mahasiswa.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition">
                    &larr; Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Top Grid: Bio, SIAKAD Match, Wisudawan, & Transkrip Status -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Bio Card -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6">
            <h3 class="text-sm font-bold uppercase tracking-wider text-gray-500 pb-3 border-b border-gray-100 dark:border-gray-700">
                Informasi Pribadi
            </h3>
            <dl class="mt-4 space-y-2.5 text-xs">
                <div class="flex justify-between">
                    <dt class="text-gray-500">NIM</dt>
                    <dd class="font-mono font-bold text-gray-900 dark:text-white">{{ $mahasiswa->nim }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Nama</dt>
                    <dd class="font-semibold text-gray-900 dark:text-white text-right">{{ $mahasiswa->nama }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Email Akun</dt>
                    <dd class="text-gray-700 dark:text-gray-300">{{ $mahasiswa->user?->email ?? '-' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Program Studi</dt>
                    <dd class="font-medium text-gray-900 dark:text-white text-right">{{ $mahasiswa->programStudi?->nama_program_studi ?? '-' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Fakultas</dt>
                    <dd class="font-medium text-gray-900 dark:text-white text-right">{{ $mahasiswa->fakultas?->nama_fakultas ?? '-' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Tempat, Tgl Lahir</dt>
                    <dd class="text-gray-700 dark:text-gray-300">{{ $mahasiswa->tempat_lahir ?? '-' }}, {{ $mahasiswa->tanggal_lahir?->format('d/m/Y') ?? '-' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Jenis Kelamin</dt>
                    <dd class="text-gray-700 dark:text-gray-300">{{ $mahasiswa->jenis_kelamin === 'L' ? 'Laki-laki' : ($mahasiswa->jenis_kelamin === 'P' ? 'Perempuan' : '-') }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Status</dt>
                    <dd class="font-bold text-emerald-600">{{ $mahasiswa->status ?? 'Aktif' }}</dd>
                </div>
            </dl>
        </div>

        <!-- Wisudawan Status Card -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6">
            <h3 class="text-sm font-bold uppercase tracking-wider text-gray-500 pb-3 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <span>Data Wisudawan</span>
                @if ($mahasiswa->wisudawan)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-800 dark:bg-indigo-900/60 dark:text-indigo-300">Terdaftar</span>
                @else
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-600">Belum Ada</span>
                @endif
            </h3>
            @if ($mahasiswa->wisudawan)
                <dl class="mt-4 space-y-2.5 text-xs">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Periode Wisuda</dt>
                        <dd class="font-bold text-gray-900 dark:text-white">{{ $mahasiswa->wisudawan->periode_wisuda }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">No. Pendaftaran</dt>
                        <dd class="font-mono text-gray-700 dark:text-gray-300">{{ $mahasiswa->wisudawan->nomor_pendaftaran_wisuda ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Tanggal Wisuda</dt>
                        <dd class="text-gray-700 dark:text-gray-300">{{ $mahasiswa->wisudawan->tanggal_wisuda?->format('d F Y') ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Predikat</dt>
                        <dd class="font-semibold text-indigo-600 dark:text-indigo-400">{{ $mahasiswa->wisudawan->predikat_kelulusan ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 mb-1">Judul Skripsi:</dt>
                        <dd class="text-gray-800 dark:text-gray-200 italic bg-gray-50 dark:bg-gray-700/50 p-2.5 rounded-xl border border-gray-100 dark:border-gray-700 leading-relaxed">
                            "{{ $mahasiswa->wisudawan->judul_skripsi ?? '-' }}"
                        </dd>
                    </div>
                </dl>
            @else
                <div class="py-12 text-center text-xs text-gray-400">
                    Mahasiswa ini belum masuk dalam antrean pendaftaran wisuda.
                </div>
            @endif
        </div>

        <!-- Transkrip Status Card -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6">
            <h3 class="text-sm font-bold uppercase tracking-wider text-gray-500 pb-3 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <span>Status Transkrip</span>
                @if ($mahasiswa->transkrip)
                    @if ($mahasiswa->transkrip->status_validasi === 'Valid')
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Valid</span>
                    @elseif ($mahasiswa->transkrip->status_validasi === 'Menunggu Validasi')
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">Review</span>
                    @elseif ($mahasiswa->transkrip->status_validasi === 'Ditolak')
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">Ditolak</span>
                    @else
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-800">Draft</span>
                    @endif
                @else
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-600">Belum Ada</span>
                @endif
            </h3>

            @if ($mahasiswa->transkrip)
                <div class="mt-4 space-y-3">
                    <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50 dark:bg-gray-700/30">
                        <span class="text-xs text-gray-500">IPK Transkrip</span>
                        <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400 font-mono">{{ number_format($mahasiswa->transkrip->ipk, 2) }}</span>
                    </div>
                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <div class="p-2.5 rounded-xl bg-gray-50 dark:bg-gray-700/30">
                            <span class="text-gray-400 block text-[11px]">Total SKS</span>
                            <span class="font-bold text-gray-900 dark:text-white text-base">{{ $mahasiswa->transkrip->total_sks }}</span>
                        </div>
                        <div class="p-2.5 rounded-xl bg-gray-50 dark:bg-gray-700/30">
                            <span class="text-gray-400 block text-[11px]">Total Mutu</span>
                            <span class="font-bold text-gray-900 dark:text-white text-base">{{ number_format($mahasiswa->transkrip->total_mutu, 2) }}</span>
                        </div>
                    </div>
                    <div class="text-xs font-mono text-gray-400 truncate">
                        Kode: {{ $mahasiswa->transkrip->kode_verifikasi }}
                    </div>
                    <div class="pt-2 flex items-center gap-2">
                        <a href="{{ route('admin.transkrip.show', $mahasiswa->transkrip) }}" class="flex-1 py-2 text-center text-xs font-semibold rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-800 transition">
                            Lihat Draft Transkrip
                        </a>
                        <a href="{{ route('admin.transkrip.pdf', $mahasiswa->transkrip) }}" target="_blank" class="py-2 px-3 text-xs font-semibold rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white transition">
                            PDF
                        </a>
                    </div>
                </div>
            @else
                <div class="py-12 text-center text-xs text-gray-400">
                    Transkrip belum digenerate. Silakan lakukan sinkronisasi data wisudawan.
                </div>
            @endif
        </div>
    </div>

    <!-- Grades Table from SIAKAD -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6">
        <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-gray-700">
            <div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Daftar Nilai Akademik Mahasiswa (SIAKAD)</h3>
                <p class="text-xs text-gray-500 mt-0.5">Daftar mata kuliah dan nilai yang bersumber dari sistem SIAKAD</p>
            </div>
            <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 dark:bg-emerald-950/60 dark:text-emerald-400 px-3 py-1 rounded-full">
                {{ $mahasiswa->siakadNilais->count() }} Mata Kuliah
            </span>
        </div>

        <div class="overflow-x-auto mt-4">
            <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400">
                    <tr>
                        <th class="px-4 py-3 rounded-l-lg">Kode</th>
                        <th class="px-4 py-3">Mata Kuliah</th>
                        <th class="px-4 py-3 text-center">Semester</th>
                        <th class="px-4 py-3 text-center">SKS</th>
                        <th class="px-4 py-3 text-center">Nilai</th>
                        <th class="px-4 py-3 text-center">Bobot</th>
                        <th class="px-4 py-3 rounded-r-lg text-right">Mutu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($mahasiswa->siakadNilais as $nilai)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30">
                            <td class="px-4 py-3 font-mono font-bold text-xs text-gray-900 dark:text-white">
                                {{ $nilai->mataKuliah?->kode_mata_kuliah ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-xs font-medium text-gray-800 dark:text-gray-200">
                                {{ $nilai->mataKuliah?->nama_mata_kuliah ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-center text-xs font-medium">{{ $nilai->semester }}</td>
                            <td class="px-4 py-3 text-center text-xs font-bold text-gray-900 dark:text-white">{{ $nilai->sks }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 rounded text-xs font-bold bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white">
                                    {{ $nilai->nilai_huruf }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center text-xs font-mono">{{ number_format($nilai->bobot, 2) }}</td>
                            <td class="px-4 py-3 text-right text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                {{ number_format($nilai->sks * $nilai->bobot, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-400 text-xs">Belum ada data nilai SIAKAD untuk mahasiswa ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
