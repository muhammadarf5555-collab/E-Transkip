<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 dark:text-white leading-tight">
                    {{ __('Manajemen Transkrip Nilai Akademik (SIAKAD + E-Wisuda)') }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Penyatuan data akademik SIAKAD, data kelulusan E-Wisuda (SK Yudisium &amp; PIN Ijazah), dan pejabat penandatangan dinamis.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2.5">
                <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'input-transkrip-terpadu')" style="background-color: #059669 !important; color: #ffffff !important;" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs sm:text-sm font-bold rounded-xl shadow-md transition gap-2 inline-flex items-center cursor-pointer">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Input / Lengkapi Transkrip Manual (Terpadu)
                </button>
                <form action="{{ route('admin.transkrip.sync') }}" method="POST">
                    @csrf
                    <button type="submit" style="background-color: #1d4ed8 !important; color: #ffffff !important;" class="px-4 py-2.5 bg-blue-700 hover:bg-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-md transition gap-2 inline-flex items-center cursor-pointer">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Sinkronisasi SIAKAD &amp; E-Wisuda
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="mb-5 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold flex items-center justify-between">
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Ringkasan Alur Penyatuan Data & Pejabat Dinamis -->
    <div class="mb-6 p-4 rounded-2xl bg-emerald-50/70 dark:bg-gray-800 border border-emerald-200 dark:border-gray-700 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 text-xs">
        <div class="flex items-start gap-3">
            <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0 font-bold">
                ✓
            </div>
            <div>
                <div class="font-bold text-gray-900 dark:text-white text-sm">
                    Alur Penyatuan Data Terpadu (SIAKAD + E-Wisuda + Pejabat Fakultas Dinamis)
                </div>
                <p class="text-gray-600 dark:text-gray-300 mt-0.5 leading-relaxed">
                    Sistem menyatukan <strong>Nilai Mata Kuliah (SIAKAD)</strong> dengan <strong>SK Yudisium, Nomor Ijazah Nasional (PIN), &amp; Judul Skripsi (E-Wisuda)</strong>. Jika data API belum lengkap atau ingin ditambahkan manual, klik tombol <strong>"Input / Lengkapi Transkrip Manual"</strong> atau tombol <strong>"Kelola / Edit Lengkap"</strong> pada baris mahasiswa tanpa perlu berpindah menu.
                </p>
            </div>
        </div>
        @if (isset($activeFakultas) && $activeFakultas)
            <div class="shrink-0 bg-white dark:bg-gray-900 px-3.5 py-2.5 rounded-xl border border-emerald-200 dark:border-gray-700">
                <div class="text-[10px] font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400">
                    Pejabat Penandatangan Aktif ({{ $activeFakultas->nama_fakultas_singkat ?: 'Fakultas' }})
                </div>
                <div class="font-bold text-gray-900 dark:text-white mt-0.5">
                    {{ $activeFakultas->jabatan_dekan ?: 'Dekan' }}: {{ $activeFakultas->nama_dekan ?: 'Dr. H. Saifuddin, M.Ag.' }}
                </div>
                <div class="text-[11px] font-mono text-gray-500 flex items-center justify-between gap-3">
                    <span>NIP. {{ $activeFakultas->nip_dekan ?: '197203151998031002' }}</span>
                    <a href="{{ route('admin.fakultas.index') }}" class="text-emerald-600 hover:underline font-semibold">Ubah Pejabat &rarr;</a>
                </div>
            </div>
        @endif
    </div>

    <!-- Status Tabs -->
    <div class="flex flex-wrap items-center gap-2 mb-6">
        <a href="{{ route('admin.transkrip.index') }}" class="px-4 py-2 rounded-xl text-xs font-bold transition {{ !request('status') ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-50' }}">
            Semua ({{ $counts['total'] }})
        </a>
        <a href="{{ route('admin.transkrip.index', ['status' => 'Menunggu Validasi']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition {{ request('status') === 'Menunggu Validasi' ? 'bg-amber-500 text-white shadow-sm' : 'bg-white dark:bg-gray-800 text-amber-600 dark:text-amber-400 hover:bg-gray-50' }}">
            Menunggu Validasi ({{ $counts['menunggu'] }})
        </a>
        <a href="{{ route('admin.transkrip.index', ['status' => 'Valid']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition {{ request('status') === 'Valid' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white dark:bg-gray-800 text-emerald-600 dark:text-emerald-400 hover:bg-gray-50' }}">
            Valid / Sah ({{ $counts['valid'] }})
        </a>
        <a href="{{ route('admin.transkrip.index', ['status' => 'Draft']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition {{ request('status') === 'Draft' ? 'bg-gray-700 text-white shadow-sm' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-50' }}">
            Draft ({{ $counts['draft'] }})
        </a>
        <a href="{{ route('admin.transkrip.index', ['status' => 'Ditolak']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition {{ request('status') === 'Ditolak' ? 'bg-rose-600 text-white shadow-sm' : 'bg-white dark:bg-gray-800 text-rose-600 dark:text-rose-400 hover:bg-gray-50' }}">
            Ditolak ({{ $counts['ditolak'] }})
        </a>
    </div>

    <!-- Search Bar -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-4 mb-6">
        <form method="GET" action="{{ route('admin.transkrip.index') }}" class="flex items-center gap-4">
            @if (request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}" />
            @endif
            <div class="flex-1">
                <x-text-input name="search" type="text" placeholder="Cari berdasarkan NIM, Nama, Nomor SK Yudisium, Nomor Ijazah (PIN), atau Judul Skripsi..." value="{{ request('search') }}" class="w-full text-sm" />
            </div>
            <x-primary-button>Cari</x-primary-button>
            @if (request('search'))
                <a href="{{ route('admin.transkrip.index', request('status') ? ['status' => request('status')] : []) }}" class="text-xs text-rose-500 hover:underline">Reset</a>
            @endif
        </form>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400">
                    <tr>
                        <th class="px-4 py-3 rounded-l-lg">Mahasiswa / Wisudawan</th>
                        <th class="px-4 py-3">Prodi &amp; Pejabat Dekan</th>
                        <th class="px-4 py-3">Integrasi Sumber Data (SIAKAD + E-Wisuda)</th>
                        <th class="px-4 py-3 text-center">SKS &amp; IPK</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 rounded-r-lg text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($transkrips as $trx)
                        @php
                            $mhs = $trx->mahasiswa;
                            $wis = $trx->wisudawan;
                            $fak = $mhs->fakultas ?: $mhs->programStudi?->fakultas;
                            $dekanNama = $trx->nama_dekan_override ?: ($fak?->nama_dekan ?: 'Dr. H. Saifuddin, M.Ag.');
                            $hasEwisudaComplete = !empty($wis?->no_sk) || !empty($wis?->no_ijazah) || !empty($wis?->judul_skripsi);
                        @endphp
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30">
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-1.5 mb-1">
                                    @if ($wis !== null || $mhs->status === 'Lulus')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300">
                                            🎓 Wisudawan
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800 dark:bg-blue-900/60 dark:text-blue-300">
                                            👤 Mahasiswa Aktif
                                        </span>
                                    @endif
                                </div>
                                <a href="{{ route('admin.transkrip.show', $trx) }}" class="font-bold text-gray-900 dark:text-white hover:text-emerald-600 transition">
                                    {{ $mhs->nama }}
                                </a>
                                <div class="text-xs font-mono text-gray-500">NIM: {{ $mhs->nim }}</div>
                                @if ($trx->nomor_transkrip)
                                    <div class="text-[11px] font-mono text-emerald-700 dark:text-emerald-400">No: {{ $trx->nomor_transkrip }}</div>
                                @else
                                    <div class="text-[11px] font-mono text-gray-400 truncate max-w-xs">{{ $trx->kode_verifikasi }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-xs">
                                <div class="font-semibold text-gray-800 dark:text-gray-200">
                                    {{ $mhs->programStudi?->nama_program_studi ?? '-' }}
                                </div>
                                <div class="text-[11px] text-gray-500 mt-0.5">
                                    Dekan: <span class="font-medium text-gray-700 dark:text-gray-300">{{ $dekanNama }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 text-xs space-y-1">
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-950/50 dark:text-blue-300">
                                        ✓ SIAKAD: {{ $mhs->siakadNilais->count() }} MK ({{ $trx->total_sks }} SKS)
                                    </span>
                                    @if ($hasEwisudaComplete)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/50 dark:text-emerald-300">
                                            ✓ E-Wisuda / Yudisium Lengkap
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-950/50 dark:text-amber-300">
                                            ! SK Yudisium / Skripsi Belum Lengkap
                                        </span>
                                    @endif
                                </div>
                                @if ($wis?->no_sk || $wis?->no_ijazah)
                                    <div class="text-[11px] font-mono text-gray-500">
                                        SK: {{ $wis->no_sk ?: '-' }} | PIN: {{ $wis->no_ijazah ?: '-' }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <div class="font-mono font-black text-sm text-emerald-600 dark:text-emerald-400">
                                    IPK {{ number_format($trx->ipk, 2) }}
                                </div>
                                <div class="text-[11px] text-gray-500">{{ $trx->predikat ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                @if ($trx->isDisahkan())
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">Valid / Disahkan</span>
                                @elseif ($trx->isMenungguPengesahan())
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300">Menunggu Pengesahan</span>
                                @elseif ($trx->status_validasi === 'Menunggu Validasi')
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300">Review Fakultas</span>
                                @elseif ($trx->status_validasi === 'Ditolak' || $trx->isPerluPerbaikan())
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300">Perlu Revisi</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">Draft</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-right space-x-1.5 whitespace-nowrap">
                                <a href="{{ route('admin.transkrip.show', $trx) }}" class="px-2.5 py-1.5 text-xs font-bold rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-200 transition" title="Kelola / Edit Manual SK Yudisium, Ijazah, Skripsi, Nilai & Validasi">
                                    Kelola / Edit Lengkap
                                </a>
                                <a href="{{ route('admin.transkrip.pdf', ['transkrip' => $trx->id, 'mode' => 'draft']) }}" target="_blank" class="px-2.5 py-1.5 text-xs font-semibold rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 transition" title="Format Draft Transkrip Pustikom">
                                    Draft PDF
                                </a>
                                <a href="{{ route('admin.transkrip.pdf', ['transkrip' => $trx->id, 'mode' => 'final']) }}" target="_blank" class="px-2.5 py-1.5 text-xs font-semibold rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white transition" title="Format Siap Cetak Pustikom">
                                    Siap Cetak PDF
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-400 text-xs">
                                Belum ada transkrip nilai. Klik tombol <strong>"Input / Lengkapi Transkrip Manual"</strong> atau <strong>"Sinkronisasi SIAKAD &amp; E-Wisuda"</strong> di atas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $transkrips->links() }}
        </div>
    </div>

    <!-- MODAL INPUT / LENGKAPI TRANSKRIP MANUAL TERPADU (TIDAK TERPISAH-PISAH) -->
    @php
        $mhsLookup = ($mahasiswasList ?? collect())->mapWithKeys(function ($m) {
            $isWisuda = ($m->wisudawan !== null || $m->status === 'Lulus');
            return [
                $m->nim => [
                    'nama' => $m->nama,
                    'prodi_id' => $m->program_studi_id,
                    'tempat_lahir' => $m->tempat_lahir ?? '',
                    'tanggal_lahir' => $m->tanggal_lahir ? $m->tanggal_lahir->format('Y-m-d') : '',
                    'is_wisudawan' => $isWisuda,
                    'periode_wisuda' => $m->wisudawan?->periode_wisuda ?? ($isWisuda ? 'Wisuda Ganjil 2025' : ''),
                    'no_sk' => $m->wisudawan?->no_sk ?? '',
                    'tanggal_sk' => $m->wisudawan?->tanggal_sk ? $m->wisudawan->tanggal_sk->format('Y-m-d') : date('Y-m-d'),
                    'no_ijazah' => $m->wisudawan?->no_ijazah ?? '',
                    'tanggal_ijazah' => $m->wisudawan?->tanggal_ijazah ? $m->wisudawan->tanggal_ijazah->format('Y-m-d') : date('Y-m-d'),
                    'judul_skripsi' => $m->wisudawan?->judul_skripsi ?? '',
                ]
            ];
        });
    @endphp

    <x-modal name="input-transkrip-terpadu" maxWidth="2xl" focusable>
        <form method="POST" action="{{ route('admin.transkrip.store-manual') }}"
            x-data="{
                students: {{ Js::from($mhsLookup) }},
                nim: '',
                selectedMhs: null,
                daftarkanWisuda: false,
                onNimChange(val) {
                    this.nim = (val || '').trim();
                    this.selectedMhs = this.students[this.nim] || null;
                    if (this.selectedMhs) {
                        if (this.selectedMhs.nama) document.getElementById('manual_nama').value = this.selectedMhs.nama;
                        if (this.selectedMhs.prodi_id) document.getElementById('manual_prodi').value = this.selectedMhs.prodi_id;
                        if (this.selectedMhs.tempat_lahir) document.getElementById('manual_tempat_lahir').value = this.selectedMhs.tempat_lahir;
                        if (this.selectedMhs.tanggal_lahir) document.getElementById('manual_tanggal_lahir').value = this.selectedMhs.tanggal_lahir;
                        
                        if (this.selectedMhs.is_wisudawan) {
                            this.daftarkanWisuda = true;
                            if (this.selectedMhs.no_sk) document.getElementById('manual_no_sk').value = this.selectedMhs.no_sk;
                            if (this.selectedMhs.tanggal_sk) document.getElementById('manual_tanggal_sk').value = this.selectedMhs.tanggal_sk;
                            if (this.selectedMhs.no_ijazah) document.getElementById('manual_no_ijazah').value = this.selectedMhs.no_ijazah;
                            if (this.selectedMhs.tanggal_ijazah) document.getElementById('manual_tanggal_ijazah').value = this.selectedMhs.tanggal_ijazah;
                            if (this.selectedMhs.periode_wisuda) document.getElementById('manual_periode').value = this.selectedMhs.periode_wisuda;
                            if (this.selectedMhs.judul_skripsi) document.getElementById('manual_skripsi').value = this.selectedMhs.judul_skripsi;
                        } else {
                            this.daftarkanWisuda = false;
                        }
                    }
                }
            }"
            class="p-6 text-left space-y-5">
            @csrf
            <div class="border-b border-gray-100 dark:border-gray-700 pb-3">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                    Input / Lengkapi Data Transkrip &amp; Kelulusan Terpadu
                </h3>
                <p class="text-xs text-gray-500 mt-1">
                    Formulir terpadu untuk menambahkan atau melengkapi Biodata, SK Yudisium, Nomor Ijazah Nasional (PIN), Judul Skripsi, dan Pejabat Penandatangan dalam satu langkah.
                </p>
            </div>

            <!-- Bagian 1: Mahasiswa & Biodata -->
            <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 space-y-3">
                <div class="text-xs font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400">
                    1. Pilih Mahasiswa &amp; Biodata Kelahiran
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <x-input-label for="manual_nim" value="Cari / Pilih NIM Mahasiswa *" />
                        <input list="manual_mhs_list" id="manual_nim" name="nim" x-model="nim" @input="onNimChange($event.target.value)" @change="onNimChange($event.target.value)" class="mt-1 block w-full text-sm rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-emerald-500 focus:ring-emerald-500 font-mono" placeholder="Ketik NIM atau Nama Mahasiswa..." required />
                        <datalist id="manual_mhs_list">
                            @foreach ($mahasiswasList ?? [] as $m)
                                @php
                                    $isWisuda = ($m->wisudawan !== null || $m->status === 'Lulus');
                                    $badgeWisuda = $isWisuda ? '🎓 Wisudawan' : '👤 Mahasiswa Aktif';
                                @endphp
                                <option value="{{ $m->nim }}">{{ $m->nama }} — [{{ $badgeWisuda }}] — {{ $m->programStudi?->nama_program_studi }}</option>
                            @endforeach
                        </datalist>

                        <!-- Live Status Indicator -->
                        <div x-show="selectedMhs && selectedMhs.is_wisudawan" x-cloak class="mt-2.5 p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 flex items-start gap-2.5">
                            <span class="text-base shrink-0">🎓</span>
                            <div class="text-xs">
                                <span class="font-bold text-emerald-800 dark:text-emerald-300 uppercase tracking-wide">STATUS: SUDAH TERDAFTAR WISUDAWAN</span>
                                <p class="text-emerald-700 dark:text-emerald-400 mt-0.5">
                                    Mahasiswa ini sudah tercatat di data E-Wisuda (<span class="font-semibold" x-text="selectedMhs?.periode_wisuda || 'Wisuda'"></span>). 
                                    SK Yudisium &amp; PIN Ijazah telah dimuat otomatis.
                                </p>
                            </div>
                        </div>

                        <div x-show="selectedMhs && !selectedMhs.is_wisudawan" x-cloak class="mt-2.5 p-3 rounded-xl bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800 flex items-start gap-2.5">
                            <span class="text-base shrink-0">👤</span>
                            <div class="text-xs">
                                <span class="font-bold text-blue-800 dark:text-blue-300 uppercase tracking-wide">STATUS: MAHASISWA AKTIF (BELUM WISUDAWAN)</span>
                                <p class="text-blue-700 dark:text-blue-400 mt-0.5">
                                    Mahasiswa ini berstatus <strong>Aktif</strong> (dalam masa perkuliahan) dan belum terdaftar di E-Wisuda.
                                </p>
                                <div class="mt-2 pt-2 border-t border-blue-200/60 dark:border-blue-800/60">
                                    <label class="inline-flex items-center gap-2 cursor-pointer font-semibold text-blue-900 dark:text-blue-200">
                                        <input type="checkbox" name="daftarkan_wisuda" value="1" x-model="daftarkanWisuda" class="rounded text-emerald-600 focus:ring-emerald-500">
                                        <span>Daftarkan sebagai Wisudawan Baru sekarang (Luluskan &amp; tetapkan SK/Ijazah)</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <x-input-label for="manual_nama" value="Nama Lengkap Mahasiswa (Opsional jika sudah ada)" />
                        <x-text-input id="manual_nama" name="nama" type="text" class="mt-1 block w-full text-sm" placeholder="Otomatis dari SIAKAD / isi jika baru" />
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <x-input-label for="manual_prodi" value="Program Studi" />
                        <select id="manual_prodi" name="program_studi_id" class="mt-1 block w-full text-sm rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">-- Sesuai Data Mahasiswa --</option>
                            @foreach ($programStudisList ?? [] as $prodi)
                                <option value="{{ $prodi->id }}">{{ $prodi->nama_program_studi }} ({{ $prodi->jenjang }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="manual_tempat_lahir" value="Tempat Lahir" />
                        <x-text-input id="manual_tempat_lahir" name="tempat_lahir" type="text" class="mt-1 block w-full text-sm" placeholder="Contoh: Cirebon" />
                    </div>
                    <div>
                        <x-input-label for="manual_tanggal_lahir" value="Tanggal Lahir" />
                        <x-text-input id="manual_tanggal_lahir" name="tanggal_lahir" type="date" class="mt-1 block w-full text-sm" />
                    </div>
                </div>
            </div>

            <!-- Bagian 2: SK Yudisium, PIN Ijazah, & Judul Skripsi -->
            <div class="p-4 rounded-xl bg-blue-50/40 dark:bg-gray-900/50 border border-blue-200 dark:border-gray-700 space-y-3">
                <div class="flex items-center justify-between">
                    <div class="text-xs font-bold uppercase tracking-wider text-blue-800 dark:text-blue-400">
                        2. Data Administrasi Kelulusan (SK Yudisium, PIN Ijazah, &amp; Skripsi)
                    </div>
                    <span x-show="selectedMhs && !selectedMhs.is_wisudawan && !daftarkanWisuda" class="text-[11px] font-semibold text-slate-500 italic">
                        (Opsional — Dikosongkan jika hanya transkrip berjalan)
                    </span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <x-input-label for="manual_no_sk" value="Nomor SK Yudisium / Kelulusan" />
                        <x-text-input id="manual_no_sk" name="no_sk" type="text" class="mt-1 block w-full text-sm font-mono" placeholder="Contoh: 0842/Un.30/F.I/PP.00.9/10/2026" />
                    </div>
                    <div>
                        <x-input-label for="manual_tanggal_sk" value="Tanggal SK Yudisium / Tanggal Lulus" />
                        <x-text-input id="manual_tanggal_sk" name="tanggal_sk" type="date" class="mt-1 block w-full text-sm" value="{{ date('Y-m-d') }}" />
                    </div>
                    <div>
                        <x-input-label for="manual_no_ijazah" value="Nomor Ijazah Nasional (PIN)" />
                        <x-text-input id="manual_no_ijazah" name="no_ijazah" type="text" class="mt-1 block w-full text-sm font-mono" placeholder="Contoh: 2010634998726769213" />
                    </div>
                    <div>
                        <x-input-label for="manual_periode" value="Periode Wisuda" />
                        <x-text-input id="manual_periode" name="periode_wisuda" type="text" class="mt-1 block w-full text-sm" value="Wisuda Ganjil {{ date('Y') }}" />
                    </div>
                    <div>
                        <x-input-label for="manual_predikat" value="Predikat Kelulusan (Otomatis dari IPK jika dikosongkan)" />
                        <select id="manual_predikat" name="predikat_kelulusan" class="mt-1 block w-full text-sm rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">-- Hitung Otomatis Berdasarkan IPK --</option>
                            <option value="Dengan Pujian (Cum Laude)">Dengan Pujian (Cum Laude)</option>
                            <option value="Sangat Memuaskan">Sangat Memuaskan</option>
                            <option value="Memuaskan">Memuaskan</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label for="manual_tanggal_ijazah" value="Tanggal Ijazah / Wisuda" />
                        <x-text-input id="manual_tanggal_ijazah" name="tanggal_ijazah" type="date" class="mt-1 block w-full text-sm" value="{{ date('Y-m-d') }}" />
                    </div>
                </div>
                <div>
                    <x-input-label for="manual_skripsi" value="Judul Skripsi / Tugas Akhir Lengkap" />
                    <textarea id="manual_skripsi" name="judul_skripsi" rows="2" class="mt-1 block w-full text-sm rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-emerald-500 focus:ring-emerald-500" placeholder="Tuliskan judul skripsi mahasiswa..."></textarea>
                </div>
            </div>

            <!-- Bagian 3: Penomoran Transkrip, SK BAN-PT & Pejabat Dekan -->
            <div class="p-4 rounded-xl bg-emerald-50/50 dark:bg-gray-900/50 border border-emerald-200 dark:border-gray-700 space-y-3">
                <div class="text-xs font-bold uppercase tracking-wider text-emerald-800 dark:text-emerald-400">
                    3. Nomor Surat Transkrip, SK BAN-PT &amp; Pejabat Penandatangan (Dinamis)
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <x-input-label for="manual_nomor_transkrip" value="Nomor Surat Transkrip (Kosongkan jika otomatis)" />
                        <x-text-input id="manual_nomor_transkrip" name="nomor_transkrip" type="text" class="mt-1 block w-full text-sm font-mono" placeholder="Otomatis: 0554/Un.30/F.I/PP.00.9/..." />
                    </div>
                    <div>
                        <x-input-label for="manual_sk_banpt" value="Nomor SK BAN-PT / LAM Prodi" />
                        <x-text-input id="manual_sk_banpt" name="no_sk_banpt_override" type="text" class="mt-1 block w-full text-sm font-mono" value="{{ $activeFakultas?->no_sk_banpt }}" placeholder="Otomatis sesuai Fakultas / Prodi" />
                    </div>
                    <div>
                        <x-input-label for="manual_nama_dekan" value="Nama Dekan Penandatangan" />
                        <x-text-input id="manual_nama_dekan" name="nama_dekan_override" type="text" class="mt-1 block w-full text-sm" value="{{ $activeFakultas?->nama_dekan }}" placeholder="Otomatis mengikuti Dekan Fakultas aktif" />
                    </div>
                    <div>
                        <x-input-label for="manual_nip_dekan" value="NIP Dekan" />
                        <x-text-input id="manual_nip_dekan" name="nip_dekan_override" type="text" class="mt-1 block w-full text-sm font-mono" value="{{ $activeFakultas?->nip_dekan }}" placeholder="Otomatis mengikuti NIP Dekan aktif" />
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                <x-primary-button style="background-color: #059669 !important;">
                    Simpan &amp; Buka Detail Transkrip
                </x-primary-button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
