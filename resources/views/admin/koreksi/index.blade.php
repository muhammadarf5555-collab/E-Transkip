<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-[#142B4A] dark:text-white leading-tight">
                    {{ __('Pengelolaan Pengajuan Koreksi Nilai & Data') }}
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Verifikasi, persetujuan, atau penolakan permohonan perbaikan nilai dan biodata yang diajukan mahasiswa.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                    <span class="w-2 h-2 rounded-full bg-amber-500 me-2 animate-pulse"></span>
                    {{ $stats['menunggu'] }} Menunggu Verifikasi
                </span>
            </div>
        </div>
    </x-slot>

    <!-- Alerts -->
    @if (session('success'))
        <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3 shadow-sm">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

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

    <!-- KPI Summary Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-slate-100 dark:border-gray-700 shadow-sm">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Pengajuan</p>
            <h3 class="text-2xl font-black text-[#142B4A] dark:text-white mt-1">{{ number_format($stats['total']) }}</h3>
            <span class="text-xs text-slate-500">Semua riwayat pengajuan</span>
        </div>

        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-slate-100 dark:border-gray-700 shadow-sm">
            <p class="text-xs font-bold text-amber-500 uppercase tracking-wider">Menunggu Review</p>
            <h3 class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-1">{{ number_format($stats['menunggu']) }}</h3>
            <span class="text-xs text-amber-600 font-medium">Perlu tindakan</span>
        </div>

        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-slate-100 dark:border-gray-700 shadow-sm">
            <p class="text-xs font-bold text-emerald-500 uppercase tracking-wider">Disetujui</p>
            <h3 class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ number_format($stats['disetujui']) }}</h3>
            <span class="text-xs text-slate-500">Nilai telah diperbarui</span>
        </div>

        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-slate-100 dark:border-gray-700 shadow-sm">
            <p class="text-xs font-bold text-rose-500 uppercase tracking-wider">Ditolak</p>
            <h3 class="text-2xl font-black text-rose-600 dark:text-rose-400 mt-1">{{ number_format($stats['ditolak']) }}</h3>
            <span class="text-xs text-slate-500">Dengan catatan revisi</span>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-slate-100 dark:border-gray-700 shadow-sm mb-6">
        <form method="GET" action="{{ route('admin.koreksi.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
            <!-- Search -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Cari Mahasiswa</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik nama atau NIM..." class="w-full text-xs rounded-xl border-slate-200 bg-slate-50 py-2 px-3 focus:bg-white focus:ring-2 focus:ring-[#2563EB]" />
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Status Pengajuan</label>
                <select name="status" class="w-full text-xs rounded-xl border-slate-200 bg-slate-50 py-2 px-3 focus:bg-white focus:ring-2 focus:ring-[#2563EB]">
                    <option value="">Semua Status</option>
                    <option value="Menunggu Review" {{ request('status') === 'Menunggu Review' ? 'selected' : '' }}>Menunggu Review</option>
                    <option value="Disetujui" {{ request('status') === 'Disetujui' ? 'selected' : '' }}>Disetujui</option>
                    <option value="Ditolak" {{ request('status') === 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>

            <!-- Action buttons -->
            <div class="flex gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-[#142B4A] hover:bg-slate-800 text-white text-xs font-bold rounded-xl shadow-sm transition">
                    Filter Pengajuan
                </button>
                @if (request()->hasAny(['search', 'status']))
                    <a href="{{ route('admin.koreksi.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold rounded-xl transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Koreksi -->
    <div x-data="{ approveModal: false, rejectModal: false, currentKoreksi: null }" class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-100 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-base text-[#142B4A] dark:text-white">Daftar Permohonan Koreksi Mahasiswa</h3>
            <span class="text-xs text-slate-500 font-medium">{{ $koreksis->total() }} total pengajuan</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="text-[11px] uppercase tracking-wider bg-slate-50 text-slate-500 font-bold border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5">Tanggal</th>
                        <th class="px-5 py-3.5">Mahasiswa</th>
                        <th class="px-5 py-3.5">Jenis / Mata Kuliah</th>
                        <th class="px-5 py-3.5 text-center">Perubahan Nilai</th>
                        <th class="px-5 py-3.5">Alasan & Bukti</th>
                        <th class="px-5 py-3.5 text-center">Status</th>
                        <th class="px-5 py-3.5 text-right">Aksi Verifikasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse ($koreksis as $k)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-5 py-3.5 whitespace-nowrap text-slate-500">
                                <div>{{ $k->created_at->format('d M Y') }}</div>
                                <div class="text-[10px] text-slate-400">{{ $k->created_at->format('H:i') }} WIB</div>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="font-bold text-slate-900">{{ $k->mahasiswa?->nama ?? '-' }}</div>
                                <div class="font-mono text-slate-500 text-[11px]">NIM: {{ $k->mahasiswa?->nim ?? '-' }}</div>
                                <div class="text-[10px] text-slate-400">{{ $k->mahasiswa?->programStudi?->nama_program_studi ?? '-' }}</div>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="font-semibold text-slate-800 block">{{ $k->jenis_koreksi }}</span>
                                @if ($k->mataKuliah)
                                    <div class="text-[11px] text-blue-600 font-medium mt-0.5">
                                        {{ $k->mataKuliah->kode_mata_kuliah }} - {{ $k->mataKuliah->nama_mata_kuliah }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-center whitespace-nowrap">
                                @if ($k->nilai_lama || $k->nilai_diajukan)
                                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-50 border border-slate-200 font-bold">
                                        <span class="line-through text-rose-600">{{ $k->nilai_lama ?: '-' }}</span>
                                        <span>&rarr;</span>
                                        <span class="text-emerald-600 text-sm font-black">{{ $k->nilai_diajukan ?: '-' }}</span>
                                    </div>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 max-w-xs">
                                <p class="line-clamp-2 text-slate-700">{{ $k->alasan }}</p>
                                @if ($k->dokumen_pendukung)
                                    <a href="{{ asset('storage/' . $k->dokumen_pendukung) }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-bold text-[#2563EB] hover:underline mt-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                        </svg>
                                        Lihat Berkas Lampiran
                                    </a>
                                @endif
                                @if ($k->catatan_admin)
                                    <div class="mt-1 text-[10px] text-slate-500 italic bg-slate-50 p-1.5 rounded-lg border border-slate-200">
                                        Catatan: {{ $k->catatan_admin }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-center whitespace-nowrap">
                                @if ($k->status === 'Menunggu Review')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                        Menunggu Review
                                    </span>
                                @elseif ($k->status === 'Disetujui')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        Disetujui
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                        Ditolak
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right whitespace-nowrap space-x-1.5">
                                @if ($k->status === 'Menunggu Review')
                                    <button type="button" @click="currentKoreksi = {{ $k->toJson() }}; approveModal = true" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition">
                                        Setujui
                                    </button>
                                    <button type="button" @click="currentKoreksi = {{ $k->toJson() }}; rejectModal = true" class="px-3 py-1.5 rounded-xl bg-rose-100 hover:bg-rose-200 text-rose-700 font-bold text-xs transition">
                                        Tolak
                                    </button>
                                @else
                                    <span class="text-xs text-slate-400 font-medium">Selesai diverifikasi</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-10 text-center text-slate-400">
                                Belum ada permohonan koreksi yang diajukan mahasiswa.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $koreksis->links() }}
        </div>

        <!-- Approve Modal -->
        <div x-show="approveModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div @click.outside="approveModal = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="font-bold text-base text-emerald-700 flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Setujui Koreksi Nilai / Data
                    </h3>
                    <button type="button" @click="approveModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
                </div>

                <form :action="'{{ url('admin/koreksi') }}/' + (currentKoreksi ? currentKoreksi.id : '') + '/approve'" method="POST" class="mt-4 space-y-4">
                    @csrf
                    <p class="text-xs text-slate-600">
                        Menyetujui pengajuan ini akan <strong>secara otomatis memperbarui nilai akademik</strong> mahasiswa menjadi nilai yang diajukan dan menghitung ulang IPK transkrip.
                    </p>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Catatan Verifikator (Opsional)</label>
                        <textarea name="catatan_admin" rows="3" placeholder="Contoh: Bukti KHS valid, nilai resmi telah disesuaikan..." class="w-full text-xs rounded-xl border-slate-200 bg-slate-50 py-2 px-3 focus:bg-white focus:ring-2 focus:ring-emerald-500"></textarea>
                    </div>

                    <div class="pt-2 flex justify-end gap-2">
                        <button type="button" @click="approveModal = false" class="px-4 py-2 bg-slate-100 text-slate-700 text-xs font-semibold rounded-xl">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-md shadow-emerald-600/20">Konfirmasi Setuju</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Reject Modal -->
        <div x-show="rejectModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div @click.outside="rejectModal = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="font-bold text-base text-rose-700 flex items-center gap-2">
                        <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Tolak Pengajuan Koreksi
                    </h3>
                    <button type="button" @click="rejectModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
                </div>

                <form :action="'{{ url('admin/koreksi') }}/' + (currentKoreksi ? currentKoreksi.id : '') + '/reject'" method="POST" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Alasan Penolakan *</label>
                        <textarea name="catatan_admin" required rows="3" placeholder="Contoh: Bukti dokumen lampiran tidak terbaca / tidak sesuai dengan arsip dosen..." class="w-full text-xs rounded-xl border-slate-200 bg-slate-50 py-2 px-3 focus:bg-white focus:ring-2 focus:ring-rose-500"></textarea>
                        <p class="text-[10px] text-slate-400 mt-1">Alasan penolakan akan dikirimkan sebagai notifikasi kepada mahasiswa.</p>
                    </div>

                    <div class="pt-2 flex justify-end gap-2">
                        <button type="button" @click="rejectModal = false" class="px-4 py-2 bg-slate-100 text-slate-700 text-xs font-semibold rounded-xl">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow-md shadow-rose-600/20">Tolak Permohonan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

