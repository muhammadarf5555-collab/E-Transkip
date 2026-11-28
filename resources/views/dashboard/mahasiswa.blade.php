<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <span
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-white/20 text-white dark:bg-emerald-500/20 dark:text-emerald-300 border border-white/30 dark:border-emerald-500/30 mb-1.5 uppercase tracking-wider">
                    Portal Mahasiswa
                </span>
                <h2 class="font-extrabold text-2xl sm:text-3xl text-white tracking-tight">
                    {{ __('Transkrip Akademik') }}
                </h2>
                <p class="text-xs sm:text-sm text-emerald-100 dark:text-slate-400 mt-0.5">
                    Universitas Islam Negeri Siber Syekh Nurjati Cirebon (UINSSC)
                </p>
            </div>
            @if ($mahasiswa && $mahasiswa->transkrip)
                @php
                    $isWisudawanMhs = ($mahasiswa->status === 'Lulus' || $mahasiswa->wisudawan !== null);
                    $trxMhs = $mahasiswa->transkrip;
                @endphp
                <div class="flex items-center gap-2">
                    @if ($isWisudawanMhs)
                        @if ($trxMhs->isDisahkan())
                            <a href="{{ route('mahasiswa.transkrip.pdf', $trxMhs) }}" target="_blank"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 bg-white text-emerald-900 hover:bg-emerald-50 dark:bg-emerald-600 dark:text-white dark:hover:bg-emerald-700 text-xs font-bold rounded-xl shadow-md transition gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                Unduh Transkrip Resmi (PDF)
                            </a>
                        @else
                            <a href="{{ route('mahasiswa.transkrip.pdf', $trxMhs) }}" target="_blank"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-xl shadow-md transition gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                Unduh Draf Transkrip (PDF)
                            </a>
                        @endif
                    @else
                        <a href="{{ route('mahasiswa.transkrip.pdf', $trxMhs) }}" target="_blank"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 bg-white text-emerald-900 hover:bg-emerald-50 dark:bg-emerald-600 dark:text-white dark:hover:bg-emerald-700 text-xs font-bold rounded-xl shadow-md transition gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Unduh Transkrip Sementara (PDF)
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </x-slot>

    <!-- Session Feedback -->
    @if (session('success'))
        <div
            class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3 shadow-sm">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
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

    @if (!$mahasiswa)
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-8 text-center max-w-xl mx-auto my-8">
            <svg class="w-14 h-14 text-amber-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <h3 class="text-lg font-bold text-amber-900">Data Profil Mahasiswa Belum Terhubung</h3>
            <p class="text-xs text-amber-700 mt-2 leading-relaxed">
                Akun Anda belum ditautkan dengan data mahasiswa aktif. Silakan hubungi bagian akademik fakultas untuk
                memverifikasi akun Anda.
            </p>
        </div>
    @else
        @php
            $trx = $mahasiswa->transkrip;
            $isWisudawan = ($mahasiswa->status === 'Lulus' || $mahasiswa->wisudawan !== null);
        @endphp

        <!-- Banner Wisudawan Khusus (Hanya jika berstatus Wisudawan) -->
        @if ($isWisudawan)
            <div class="mb-6">
                @if ($trx && $trx->isDisahkan())
                    <div
                        class="app-card rounded-2xl p-4 sm:p-6 border-l-4 border-l-emerald-600 flex flex-col md:flex-row md:items-center justify-between gap-4 sm:gap-5">
                        <div class="flex items-start space-x-3 sm:space-x-4">
                            <div
                                class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-emerald-100 text-emerald-700 dark:bg-emerald-900/60 dark:text-emerald-300 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <div>
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300 mb-1">
                                    RESMI DISAHKAN
                                </span>
                                <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white">Transkrip Nilai Akademik
                                    Resmi Telah Disahkan</h3>
                                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 mt-0.5">
                                    Dokumen transkrip resmi wisuda telah disahkan oleh Admin Sistem dan bertanda tangan Rektor
                                    UINSSC dengan kode QR verifikasi digital.
                                </p>
                                <p
                                    class="text-xs text-emerald-700 dark:text-emerald-400 font-mono mt-2 font-bold inline-flex items-center gap-1.5 bg-emerald-50 dark:bg-emerald-950/60 px-2.5 py-1 rounded-lg border border-emerald-200 dark:border-emerald-800 break-all">
                                    Kode Dokumen: {{ $trx->kode_verifikasi }}
                                </p>
                            </div>
                        </div>
                        <div class="shrink-0 flex items-center gap-2 w-full md:w-auto">
                            <a href="{{ route('verifikasi.transkrip', $trx->kode_verifikasi) }}" target="_blank"
                                class="w-full md:w-auto text-center px-4 py-2 text-xs font-bold rounded-xl border border-slate-300 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 transition">
                                Cek Validasi Publik
                            </a>
                        </div>
                    </div>
                @else
                    <div class="app-card rounded-2xl p-4 sm:p-6 border-l-4 border-l-amber-500 flex items-start space-x-3 sm:space-x-4">
                        <div
                            class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-amber-100 text-amber-700 dark:bg-amber-900/60 dark:text-amber-300 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300 mb-1">
                                PROSES VERIFIKASI FAKULTAS
                            </span>
                            <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white">Transkrip Wisuda Sedang
                                Ditinjau Admin Fakultas</h3>
                            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 mt-0.5">
                                Berkas kelulusan Anda sedang diverifikasi oleh tim validator akademik fakultas sebelum diteruskan ke
                                Admin Sistem.
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        @else
            <!-- Banner Mahasiswa Aktif (Belum Wisudawan / Dalam Masa Studi) -->
            <div class="mb-6">
                <div class="app-card rounded-2xl p-4 sm:p-6 border-l-4 border-l-blue-600 flex flex-col md:flex-row md:items-center justify-between gap-4 sm:gap-5 bg-gradient-to-r from-blue-50/50 via-white to-slate-50 dark:from-blue-950/20 dark:via-gray-800 dark:to-gray-800">
                    <div class="flex items-start space-x-3 sm:space-x-4">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-blue-100 text-blue-700 dark:bg-blue-900/60 dark:text-blue-300 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300 mb-1">
                                MAHASISWA AKTIF — DALAM MASA STUDI
                            </span>
                            <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white">
                                Draf Transkrip Akademik Sementara (Semester Berjalan)
                            </h3>
                            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 mt-0.5">
                                Ini adalah rekapitulasi nilai akademik sementara Anda. Transkrip resmi kelulusan ber-QR Code resmi akan diterbitkan oleh pihak fakultas setelah Anda menyelesaikan seluruh SKS dan terdaftar di E-Wisuda.
                            </p>
                        </div>
                    </div>
                    @if ($trx)
                        <div class="shrink-0 flex items-center gap-2 w-full md:w-auto">
                            <a href="{{ route('mahasiswa.transkrip.pdf', $trx) }}" target="_blank"
                                class="w-full md:w-auto text-center px-4 py-2.5 text-xs font-bold rounded-xl bg-blue-600 hover:bg-blue-700 text-white shadow-sm transition inline-flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                Unduh Draf Transkrip (PDF)
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- KPI Cards Overview -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
            <div class="app-card p-3.5 sm:p-5 rounded-2xl">
                <div class="flex items-center justify-between">
                    <p class="text-[10px] sm:text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Indeks Prestasi Kumulatif</p>
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                </div>
                <h3 class="text-2xl sm:text-3xl font-black text-emerald-600 dark:text-emerald-400 mt-1">
                    {{ number_format($trx?->ipk ?? 0, 2) }}
                </h3>
                <span class="text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 block">Skala 4.00 resmi UIN SSC</span>
            </div>

            <div class="app-card p-3.5 sm:p-5 rounded-2xl">
                <div class="flex items-center justify-between">
                    <p class="text-[10px] sm:text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total SKS Diperoleh</p>
                    <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                </div>
                <h3 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-1">
                    {{ $trx?->total_sks ?? $mahasiswa->siakadNilais->sum('sks') }}
                </h3>
                <span class="text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 block">Kredit Semester Ditempuh</span>
            </div>

            <div class="app-card p-3.5 sm:p-5 rounded-2xl">
                <p class="text-[10px] sm:text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Predikat Kelulusan</p>
                <h3 class="text-sm sm:text-base font-bold text-emerald-700 dark:text-emerald-400 mt-1.5 sm:mt-2 truncate">
                    {{ $trx?->predikat ?? ($mahasiswa->wisudawan?->predikat_kelulusan ?? 'Dengan Pujian (Cum Laude)') }}
                </h3>
                <span class="text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 block">Akumulasi Prestasi</span>
            </div>

            <div class="app-card p-3.5 sm:p-5 rounded-2xl">
                <p class="text-[10px] sm:text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status Akademik</p>
                <h3 class="text-sm sm:text-base font-bold text-slate-800 dark:text-white mt-1.5 sm:mt-2 truncate flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    {{ ($mahasiswa->status === 'Lulus' || $mahasiswa->wisudawan !== null || $trx?->wisudawan_id !== null) ? 'Wisudawan' : 'Mahasiswa Aktif' }}
                </h3>
                <span class="text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 block">
                    {{ ($mahasiswa->status === 'Lulus' || $mahasiswa->wisudawan !== null || $trx?->wisudawan_id !== null) ? ($mahasiswa->wisudawan?->periode_wisuda ?: 'Wisudawan Resmi') : ('Semester ' . ($mahasiswa->semester ?? 7)) }}
                </span>
            </div>
        </div>

        <!-- Konten Halaman Berdasarkan Tab / Parameter View Navigasi -->
        @php
            $currentView = request('view', 'transkrip');
        @endphp

        @if ($currentView === 'koreksi')
            <!-- ================= VIEW 2: PENGAJUAN KOREKSI NILAI & RIWAYAT ================= -->
            <div class="space-y-6 mb-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Form Pengajuan Koreksi -->
                    <div class="app-card rounded-2xl p-6 h-fit" x-data="{ jenisKoreksi: 'Koreksi Nilai Mata Kuliah' }">
                        <div class="pb-3 border-b border-slate-100 dark:border-slate-800">
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Ajukan Koreksi Nilai / Data
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                Apabila terdapat ketidaksesuaian nilai akademik, data pribadi/biodata, nomor ijazah, atau tanggal lahir pada transkrip, Anda dapat
                                mengajukan perbaikan ke admin fakultas.
                            </p>
                        </div>

                        <form method="POST" action="{{ route('mahasiswa.koreksi.store') }}" enctype="multipart/form-data"
                            class="mt-4 space-y-4 text-xs">
                            @csrf

                            <div>
                                <label
                                    class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Jenis
                                    Koreksi *</label>
                                <select name="jenis_koreksi" x-model="jenisKoreksi" required
                                    class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white py-2.5 px-3 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500 font-semibold">
                                    <option value="Koreksi Nilai Mata Kuliah">Koreksi Nilai Mata Kuliah</option>
                                    <option value="Koreksi Biodata / Data Pribadi">Koreksi Biodata / Data Pribadi (Nama, Tanggal Lahir, dll)</option>
                                    <option value="Koreksi Nomor Ijazah / SK">Koreksi Nomor Ijazah / Nomor SK</option>
                                    <option value="Koreksi Judul Skripsi / Wisuda">Koreksi Judul Skripsi / Data Wisuda</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>

                            <!-- ================= 1. KOREKSI NILAI MATA KULIAH ================= -->
                            <div x-show="jenisKoreksi === 'Koreksi Nilai Mata Kuliah'" x-transition class="space-y-4">
                                <div>
                                    <label
                                        class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Mata
                                        Kuliah Terkait *</label>
                                    <select name="mata_kuliah_id" :required="jenisKoreksi === 'Koreksi Nilai Mata Kuliah'"
                                        class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white py-2.5 px-3 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500">
                                        <option value="">-- Pilih Mata Kuliah --</option>
                                        @foreach ($mahasiswa->siakadNilais as $sn)
                                            <option value="{{ $sn->mata_kuliah_id }}">
                                                {{ $sn->mataKuliah?->kode_mata_kuliah }} - {{ $sn->mataKuliah?->nama_mata_kuliah }}
                                                (Nilai Saat Ini: {{ $sn->nilai_huruf }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label
                                            class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Nilai
                                            Saat Ini</label>
                                        <input type="text" name="nilai_lama" placeholder="Contoh: B"
                                            class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white py-2.5 px-3 font-bold" />
                                    </div>
                                    <div>
                                        <label
                                            class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Nilai
                                            Seharusnya *</label>
                                        <input type="text" name="nilai_diajukan" placeholder="Contoh: A"
                                            class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-emerald-600 dark:text-emerald-400 py-2.5 px-3 font-bold" />
                                    </div>
                                </div>
                            </div>

                            <!-- ================= 2. KOREKSI BIODATA / DATA PRIBADI (KOLOM KHUSUS) ================= -->
                            <div x-show="jenisKoreksi === 'Koreksi Biodata / Data Pribadi'" x-transition class="space-y-3.5">
                                <!-- Banner Data Saat Ini -->
                                <div class="p-3 bg-emerald-50/80 dark:bg-emerald-950/40 rounded-xl border border-emerald-200 dark:border-emerald-800 text-[11px] text-emerald-900 dark:text-emerald-200">
                                    <div class="font-bold flex items-center gap-1.5 mb-1.5 text-emerald-800 dark:text-emerald-300">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        Data Biodata Tercatat Saat Ini:
                                    </div>
                                    <div class="grid grid-cols-2 gap-x-2 gap-y-1 text-[10.5px]">
                                        <div>Nama: <strong class="text-slate-900 dark:text-white">{{ $mahasiswa->nama }}</strong></div>
                                        <div>NIM: <strong class="text-slate-900 dark:text-white font-mono">{{ $mahasiswa->nim }}</strong></div>
                                        <div>Tempat Lahir: <strong class="text-slate-900 dark:text-white">{{ $mahasiswa->tempat_lahir ?? '-' }}</strong></div>
                                        <div>Tgl Lahir: <strong class="text-slate-900 dark:text-white">{{ $mahasiswa->tanggal_lahir ? \Carbon\Carbon::parse($mahasiswa->tanggal_lahir)->format('d/m/Y') : '-' }}</strong></div>
                                        <div>Gender: <strong class="text-slate-900 dark:text-white">{{ $mahasiswa->jenis_kelamin == 'L' ? 'Laki-laki' : ($mahasiswa->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}</strong></div>
                                    </div>
                                </div>

                                <div>
                                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                                        Nama Lengkap yang Benar (Sesuai KTP / Akta)
                                    </label>
                                    <input type="text" name="nama_diajukan" placeholder="Contoh: Muhammad Fulan, S.Pd" value="{{ old('nama_diajukan', $mahasiswa->nama) }}"
                                        class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white py-2 px-3 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500 font-semibold" />
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                                            Tempat Lahir yang Benar
                                        </label>
                                        <input type="text" name="tempat_lahir_diajukan" placeholder="Kota / Kabupaten Kelahiran" value="{{ old('tempat_lahir_diajukan', $mahasiswa->tempat_lahir) }}"
                                            class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white py-2 px-3 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500" />
                                    </div>
                                    <div>
                                        <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                                            Tanggal Lahir yang Benar
                                        </label>
                                        <input type="date" name="tanggal_lahir_diajukan" value="{{ old('tanggal_lahir_diajukan', $mahasiswa->tanggal_lahir ? \Carbon\Carbon::parse($mahasiswa->tanggal_lahir)->format('Y-m-d') : '') }}"
                                            class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white py-2 px-3 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500 font-medium" />
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                                            Jenis Kelamin
                                        </label>
                                        <select name="jenis_kelamin_diajukan"
                                            class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white py-2 px-3 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500 font-medium">
                                            <option value="">-- Tetap / Pilih --</option>
                                            <option value="L" {{ $mahasiswa->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki (L)</option>
                                            <option value="P" {{ $mahasiswa->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan (P)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                                            Nomor NIK KTP (16 Digit)
                                        </label>
                                        <input type="text" name="nik_diajukan" maxlength="16" placeholder="Contoh: 3209123456780001"
                                            class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white py-2 px-3 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500 font-mono" />
                                    </div>
                                </div>
                            </div>

                            <!-- ================= 3. KOREKSI NOMOR IJAZAH & NOMOR SK (KOLOM KHUSUS) ================= -->
                            <div x-show="jenisKoreksi === 'Koreksi Nomor Ijazah / SK'" x-transition class="space-y-3.5">
                                <!-- Banner Data Saat Ini -->
                                <div class="p-3 bg-amber-50/80 dark:bg-amber-950/40 rounded-xl border border-amber-200 dark:border-amber-800 text-[11px] text-amber-900 dark:text-amber-200">
                                    <div class="font-bold flex items-center gap-1.5 mb-1.5 text-amber-800 dark:text-amber-300">
                                        <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        Data Ijazah / SK Saat Ini:
                                    </div>
                                    <div class="grid grid-cols-2 gap-x-2 gap-y-1 text-[10.5px]">
                                        <div>No. Ijazah: <strong class="text-slate-900 dark:text-white font-mono">{{ $mahasiswa->wisudawan?->no_ijazah ?? 'Belum ada' }}</strong></div>
                                        <div>No. SK: <strong class="text-slate-900 dark:text-white font-mono">{{ $mahasiswa->wisudawan?->no_sk ?? 'Belum ada' }}</strong></div>
                                        <div>Tgl SK: <strong class="text-slate-900 dark:text-white">{{ $mahasiswa->wisudawan?->tanggal_sk ? \Carbon\Carbon::parse($mahasiswa->wisudawan->tanggal_sk)->format('d/m/Y') : '-' }}</strong></div>
                                        <div>Tgl Ijazah: <strong class="text-slate-900 dark:text-white">{{ $mahasiswa->wisudawan?->tanggal_ijazah ? \Carbon\Carbon::parse($mahasiswa->wisudawan->tanggal_ijazah)->format('d/m/Y') : '-' }}</strong></div>
                                    </div>
                                </div>

                                <div>
                                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                                        Nomor Ijazah Nasional (PIN / No. Seri yang Benar)
                                    </label>
                                    <input type="text" name="no_ijazah_diajukan" placeholder="Contoh: 12345/UINSSC/S1/2026 atau Nomor PIN PDDikti" value="{{ old('no_ijazah_diajukan', $mahasiswa->wisudawan?->no_ijazah) }}"
                                        class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white py-2 px-3 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500 font-mono font-bold" />
                                </div>

                                <div>
                                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                                        Nomor SK Kelulusan / SK Yudisium yang Benar
                                    </label>
                                    <input type="text" name="no_sk_diajukan" placeholder="Contoh: B-1234/Un.01/R/PP.00.9/05/2026" value="{{ old('no_sk_diajukan', $mahasiswa->wisudawan?->no_sk) }}"
                                        class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white py-2 px-3 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500 font-mono" />
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                                            Tanggal SK Kelulusan
                                        </label>
                                        <input type="date" name="tanggal_sk_diajukan" value="{{ old('tanggal_sk_diajukan', $mahasiswa->wisudawan?->tanggal_sk ? \Carbon\Carbon::parse($mahasiswa->wisudawan->tanggal_sk)->format('Y-m-d') : '') }}"
                                            class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white py-2 px-3 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500 font-medium" />
                                    </div>
                                    <div>
                                        <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                                            Tanggal Ijazah / Kelulusan
                                        </label>
                                        <input type="date" name="tanggal_ijazah_diajukan" value="{{ old('tanggal_ijazah_diajukan', $mahasiswa->wisudawan?->tanggal_ijazah ? \Carbon\Carbon::parse($mahasiswa->wisudawan->tanggal_ijazah)->format('Y-m-d') : '') }}"
                                            class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white py-2 px-3 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500 font-medium" />
                                    </div>
                                </div>
                            </div>

                            <!-- ================= 4. KOREKSI JUDUL SKRIPSI / DATA WISUDA (KOLOM KHUSUS) ================= -->
                            <div x-show="jenisKoreksi === 'Koreksi Judul Skripsi / Wisuda'" x-transition class="space-y-3.5">
                                <!-- Banner Data Saat Ini -->
                                <div class="p-3 bg-blue-50/80 dark:bg-blue-950/40 rounded-xl border border-blue-200 dark:border-blue-800 text-[11px] text-blue-900 dark:text-blue-200">
                                    <div class="font-bold flex items-center gap-1.5 mb-1 text-blue-800 dark:text-blue-300">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                        Judul Skripsi Saat Ini:
                                    </div>
                                    <p class="text-[10.5px] italic text-slate-800 dark:text-slate-200 leading-relaxed font-medium">
                                        "{{ $mahasiswa->wisudawan?->judul_skripsi ?? 'Belum ada data judul skripsi terdaftar' }}"
                                    </p>
                                </div>

                                <div>
                                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                                        Judul Skripsi yang Benar (Bahasa Indonesia)
                                    </label>
                                    <textarea name="judul_skripsi_diajukan" rows="2" placeholder="Tuliskan judul skripsi yang benar dan lengkap sesuai naskah final..."
                                        class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white py-2 px-3 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500 font-medium">{{ old('judul_skripsi_diajukan', $mahasiswa->wisudawan?->judul_skripsi) }}</textarea>
                                </div>

                                <div>
                                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                                        Judul Skripsi (Bahasa Inggris / English Title)
                                    </label>
                                    <textarea name="judul_skripsi_en_diajukan" rows="2" placeholder="Title in English for bilingual academic transcript..."
                                        class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white py-2 px-3 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500 font-medium italic"></textarea>
                                </div>

                                <div>
                                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                                        Periode / Gelombang Wisuda
                                    </label>
                                    <input type="text" name="periode_wisuda_diajukan" placeholder="Contoh: Wisuda Sarjana Semester Ganjil 2025/2026" value="{{ old('periode_wisuda_diajukan', $mahasiswa->wisudawan?->periode_wisuda) }}"
                                        class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white py-2 px-3 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500" />
                                </div>
                            </div>

                            <!-- ================= 5. KOREKSI LAINNYA ================= -->
                            <div x-show="jenisKoreksi === 'Lainnya'" x-transition class="p-3 bg-slate-50 dark:bg-slate-800/80 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs">
                                <div class="font-bold flex items-center gap-1.5 mb-1 text-slate-800 dark:text-white">
                                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    Koreksi Data Lainnya
                                </div>
                                <p class="text-[11px] leading-relaxed">
                                    Silakan sebutkan secara jelas bagian data apa yang salah dan perbaikan yang diinginkan pada kolom keterangan alasan di bawah.
                                </p>
                            </div>

                            <div>
                                <label
                                    class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Keterangan
                                    Tambahan / Alasan Koreksi *</label>
                                <textarea name="alasan" required rows="2"
                                    placeholder="Jelaskan alasan permohonan koreksi atau keterangan pendukung lainnya..."
                                    class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white py-2.5 px-3 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500"></textarea>
                            </div>

                            <div>
                                <label
                                    class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Upload
                                    Berkas Pendukung (KTP / Akta / Ijazah / KHS)</label>
                                <input type="file" name="dokumen" accept=".pdf,.jpg,.jpeg,.png"
                                    class="w-full text-xs text-slate-500 dark:text-slate-400 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 dark:file:bg-emerald-950/50 file:text-emerald-700 dark:file:text-emerald-300 hover:file:bg-emerald-100" />
                                <span class="text-[10px] text-slate-400 mt-1 block">Format: PDF, JPG, PNG (Maksimal 3MB)</span>
                            </div>

                            <div class="pt-2">
                                <button type="submit"
                                    class="w-full py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold rounded-xl shadow-md transition">
                                    Kirim Pengajuan Koreksi
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Riwayat Pengajuan Koreksi -->
                    <div class="lg:col-span-2 app-card rounded-2xl p-6">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                            <div>
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                                    Riwayat Pengajuan Koreksi Anda
                                </h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Status dan perkembangan pengajuan
                                    perubahan data</p>
                            </div>
                            <span
                                class="text-xs font-bold px-3 py-1 bg-emerald-50 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 rounded-full border border-emerald-200 dark:border-emerald-800">
                                {{ $mahasiswa->pengajuanKoreksis->count() }} Riwayat
                            </span>
                        </div>

                        <div class="overflow-x-auto mt-4 rounded-xl border border-slate-100 dark:border-slate-800">
                            <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                                <thead
                                    class="text-[10px] uppercase tracking-wider bg-slate-50 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 font-bold border-b border-slate-200 dark:border-slate-700">
                                    <tr>
                                        <th class="px-3.5 py-3">Tanggal</th>
                                        <th class="px-3.5 py-3">Jenis / Mata Kuliah</th>
                                        <th class="px-3 py-3 text-center">Koreksi Nilai</th>
                                        <th class="px-3.5 py-3">Alasan & Catatan</th>
                                        <th class="px-3 py-3 text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                    @forelse ($mahasiswa->pengajuanKoreksis as $pk)
                                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition">
                                            <td class="px-3.5 py-3 whitespace-nowrap text-slate-500 dark:text-slate-400">
                                                {{ $pk->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="px-3.5 py-3">
                                                <div class="font-bold text-slate-900 dark:text-white">{{ $pk->jenis_koreksi }}</div>
                                                @if ($pk->mataKuliah)
                                                    <div class="text-[11px] text-emerald-600 dark:text-emerald-400 font-medium">
                                                        {{ $pk->mataKuliah->nama_mata_kuliah }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-3 py-3 text-center whitespace-nowrap">
                                                @if ($pk->nilai_lama || $pk->nilai_diajukan)
                                                    <span class="font-bold">
                                                        <span class="line-through text-rose-500">{{ $pk->nilai_lama ?: '-' }}</span>
                                                        &rarr;
                                                        <span
                                                            class="text-emerald-600 dark:text-emerald-400 font-black">{{ $pk->nilai_diajukan ?: '-' }}</span>
                                                    </span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="px-3.5 py-3 max-w-xs">
                                                <p class="text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-line">{{ $pk->alasan }}</p>
                                                @if ($pk->catatan_admin)
                                                    <div
                                                        class="mt-1 text-[10px] text-emerald-900 dark:text-emerald-200 bg-emerald-50 dark:bg-emerald-950/60 p-2 rounded-lg border border-emerald-200 dark:border-emerald-800">
                                                        <strong>Respon Admin:</strong> {{ $pk->catatan_admin }}
                                                    </div>
                                                @endif
                                                @if ($pk->dokumen_pendukung)
                                                    <a href="{{ asset('storage/' . $pk->dokumen_pendukung) }}" target="_blank"
                                                        class="inline-flex items-center text-[11px] text-emerald-600 dark:text-emerald-400 hover:underline font-bold mt-1">
                                                        &darr; Berkas Lampiran Bukti
                                                    </a>
                                                @endif
                                            </td>
                                            <td class="px-3 py-3 text-center whitespace-nowrap">
                                                @if ($pk->status === 'Menunggu Review')
                                                    <span
                                                        class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300">
                                                        Menunggu Review
                                                    </span>
                                                @elseif ($pk->status === 'Disetujui')
                                                    <span
                                                        class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300">
                                                        Disetujui
                                                    </span>
                                                @else
                                                    <span
                                                        class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 dark:bg-rose-900/50 dark:text-rose-300">
                                                        Ditolak
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-3 py-8 text-center text-slate-400">
                                                Anda belum pernah mengajukan koreksi nilai atau data.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @elseif ($currentView === 'notifikasi')
            <!-- ================= VIEW 3: NOTIFIKASI ================= -->
            <div class="app-card rounded-2xl p-6 mb-8">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            Pemberitahuan & Notifikasi Sistem
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Semua info dan pembaharuan resmi seputar
                            transkrip Anda</p>
                    </div>
                    <span
                        class="text-xs font-bold px-3 py-1 bg-emerald-50 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 rounded-full border border-emerald-200 dark:border-emerald-800">
                        {{ $notifikasis->count() }} Notifikasi
                    </span>
                </div>

                <div class="divide-y divide-slate-100 dark:divide-slate-800 mt-4">
                    @forelse ($notifikasis as $notif)
                        <div
                            class="py-3.5 flex items-start space-x-3 {{ !$notif->dibaca ? 'bg-emerald-50/40 dark:bg-emerald-950/20 -mx-6 px-6 rounded-xl' : '' }}">
                            <div
                                class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 {{ $notif->tipe === 'success' ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/60 dark:text-emerald-300' : ($notif->tipe === 'danger' ? 'bg-rose-100 text-rose-600 dark:bg-rose-900/60 dark:text-rose-300' : 'bg-blue-100 text-blue-600 dark:bg-blue-900/60 dark:text-blue-300') }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-xs font-bold text-slate-900 dark:text-white">{{ $notif->judul }}</h4>
                                    <span class="text-[10px] text-slate-400">{{ $notif->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-xs text-slate-600 dark:text-slate-300 mt-1 leading-relaxed">{{ $notif->pesan }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-slate-400 text-xs">
                            Tidak ada notifikasi baru saat ini.
                        </div>
                    @endforelse
                </div>
            </div>
        @else
            <!-- ================= VIEW 1 (DEFAULT): TRANSKRIP & DAFTAR NILAI ================= -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Biodata Card -->
                <div class="app-card rounded-2xl p-4 sm:p-6 h-fit border border-slate-300 dark:border-slate-700">
                    <div class="flex items-center space-x-3 pb-4 border-b-2 border-slate-200 dark:border-slate-700">
                        <div
                            class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-200 flex items-center justify-center font-black text-sm border border-emerald-300 dark:border-emerald-700">
                            {{ substr($mahasiswa->nama, 0, 1) }}
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                                Profil & Informasi Akademik
                            </h3>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400">Pangkalan data resmi universitas</p>
                        </div>
                    </div>
                    <dl class="mt-4 space-y-3 text-xs">
                        <div class="flex justify-between items-center py-2 border-b border-slate-200 dark:border-slate-700">
                            <dt class="text-slate-600 dark:text-slate-400 font-medium">NIM</dt>
                            <dd
                                class="font-mono font-bold text-slate-900 dark:text-white bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded border border-slate-200 dark:border-slate-700">
                                {{ $mahasiswa->nim }}</dd>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-slate-200 dark:border-slate-700">
                            <dt class="text-slate-600 dark:text-slate-400 font-medium">Nama Lengkap</dt>
                            <dd class="font-bold text-slate-900 dark:text-white text-right">{{ $mahasiswa->nama }}</dd>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-slate-200 dark:border-slate-700">
                            <dt class="text-slate-600 dark:text-slate-400 font-medium">Program Studi</dt>
                            <dd class="font-semibold text-emerald-700 dark:text-emerald-400 text-right">
                                {{ $mahasiswa->programStudi?->nama_program_studi ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-slate-200 dark:border-slate-700">
                            <dt class="text-slate-600 dark:text-slate-400 font-medium">Fakultas</dt>
                            <dd class="font-medium text-slate-800 dark:text-slate-200 text-right">
                                {{ $mahasiswa->fakultas?->nama_fakultas ?? ($mahasiswa->programStudi?->fakultas?->nama_fakultas ?? 'Ilmu Tarbiyah dan Keguruan') }}
                            </dd>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-slate-200 dark:border-slate-700">
                            <dt class="text-slate-600 dark:text-slate-400 font-medium">Tempat, Tanggal Lahir</dt>
                            <dd class="font-medium text-slate-800 dark:text-slate-200 text-right">
                                {{ $mahasiswa->tempat_lahir ?? 'Subang' }},
                                {{ \Carbon\Carbon::parse($mahasiswa->tanggal_lahir ?? '2005-02-25')->translatedFormat('d F Y') }}
                            </dd>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-slate-200 dark:border-slate-700">
                            <dt class="text-slate-600 dark:text-slate-400 font-medium">Jenjang / Semester</dt>
                            <dd class="font-medium text-slate-800 dark:text-slate-200">{{ $mahasiswa->jenjang ?? 'S1' }} /
                                Semester {{ $mahasiswa->semester ?? '7' }}</dd>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <dt class="text-slate-600 dark:text-slate-400 font-medium">Status Akademik</dt>
                            <dd class="font-bold text-emerald-700 dark:text-emerald-400 flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                {{ $mahasiswa->status ?? 'Aktif' }}
                            </dd>
                        </div>
                    </dl>

                    @if ($mahasiswa->wisudawan?->judul_skripsi)
                        <div class="mt-5 pt-4 border-t-2 border-slate-200 dark:border-slate-700">
                            <dt class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Judul
                                Skripsi / Tugas Akhir</dt>
                            <dd class="text-xs text-slate-800 dark:text-slate-200 italic mt-1.5 leading-relaxed">
                                "{{ $mahasiswa->wisudawan->judul_skripsi }}"
                            </dd>
                        </div>
                    @endif
                </div>

                @php
                    $nilaiJson = $mahasiswa->siakadNilais->map(function ($n, $idx) {
                        $huruf = (string) ($n->nilai_huruf ?? '-');
                        if (in_array($huruf, ['A', 'A-'])) {
                            $badge = 'bg-emerald-100 text-emerald-800 border-emerald-300 dark:bg-emerald-900/60 dark:text-emerald-300 dark:border-emerald-700';
                        } elseif (in_array($huruf, ['B+', 'B', 'B-'])) {
                            $badge = 'bg-teal-100 text-teal-800 border-teal-300 dark:bg-teal-900/60 dark:text-teal-300 dark:border-teal-700';
                        } else {
                            $badge = 'bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-900/60 dark:text-amber-300 dark:border-amber-700';
                        }
                        $sks = (int) $n->sks;
                        $bobot = (float) $n->bobot;
                        return [
                            'id' => $n->id ?? $idx,
                            'kode' => (string) ($n->mataKuliah?->kode_mata_kuliah ?? '-'),
                            'nama' => (string) ($n->mataKuliah?->nama_mata_kuliah ?? '-'),
                            'sks' => $sks,
                            'huruf' => $huruf,
                            'bobot' => number_format($bobot, 2),
                            'mutu' => number_format($sks * $bobot, 2),
                            'badgeClass' => $badge,
                        ];
                    })->values();
                @endphp

                <!-- Grades Table -->
                <div x-data="{
                    search: '',
                    perPage: 10,
                    currentPage: 1,
                    items: {{ \Illuminate\Support\Js::from($nilaiJson) }},
                    get filteredItems() {
                        if (!this.search.trim()) return this.items;
                        const q = this.search.toLowerCase().trim();
                        return this.items.filter(item => 
                            (item.kode && item.kode.toLowerCase().includes(q)) || 
                            (item.nama && item.nama.toLowerCase().includes(q)) ||
                            (item.huruf && item.huruf.toLowerCase().includes(q))
                        );
                    },
                    get totalPages() {
                        if (this.perPage === 'all') return 1;
                        const count = this.filteredItems.length;
                        return Math.max(1, Math.ceil(count / parseInt(this.perPage)));
                    },
                    get paginatedItems() {
                        if (this.perPage === 'all') return this.filteredItems;
                        const limit = parseInt(this.perPage);
                        const start = (this.currentPage - 1) * limit;
                        return this.filteredItems.slice(start, start + limit);
                    },
                    get startItem() {
                        if (this.filteredItems.length === 0) return 0;
                        if (this.perPage === 'all') return 1;
                        return (this.currentPage - 1) * parseInt(this.perPage) + 1;
                    },
                    get endItem() {
                        if (this.perPage === 'all') return this.filteredItems.length;
                        return Math.min(this.currentPage * parseInt(this.perPage), this.filteredItems.length);
                    },
                    get pages() {
                        const total = this.totalPages;
                        if (total <= 7) {
                            return Array.from({ length: total }, (_, i) => i + 1);
                        }
                        const current = this.currentPage;
                        const result = [1];
                        if (current > 3) result.push('...');
                        const start = Math.max(2, current - 1);
                        const end = Math.min(total - 1, current + 1);
                        for (let i = start; i <= end; i++) {
                            result.push(i);
                        }
                        if (current < total - 2) result.push('...');
                        result.push(total);
                        return result;
                    },
                    goToPage(p) {
                        if (p >= 1 && p <= this.totalPages) {
                            this.currentPage = p;
                        }
                    }
                }" class="lg:col-span-2 app-card rounded-2xl p-4 sm:p-6 border border-slate-300 dark:border-slate-700">
                    <div
                        class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-4 border-b-2 border-slate-200 dark:border-slate-700">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                Daftar Nilai Akademik
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Seluruh mata kuliah terintegrasi resmi
                                dari Bridge API SIAKAD UINSSC.</p>
                        </div>
                        <span
                            class="text-xs font-bold px-3 py-1 bg-emerald-50 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 rounded-full border border-emerald-300 dark:border-emerald-700 w-fit">
                            {{ $mahasiswa->siakadNilais->count() }} Mata Kuliah
                        </span>
                    </div>

                    <!-- Search & Per-Page Controls Toolbar -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mt-4">
                        <div class="relative flex-1">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </span>
                            <input type="text" x-model="search" @input="currentPage = 1" placeholder="Cari kode atau mata kuliah..."
                                class="w-full pl-9 pr-8 py-2 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/80 text-slate-900 dark:text-white placeholder-slate-400 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
                            <button type="button" x-show="search" @click="search = ''; currentPage = 1"
                                class="absolute inset-y-0 right-0 flex items-center pr-2.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        <div class="flex items-center gap-2 self-end sm:self-auto text-xs text-slate-600 dark:text-slate-400">
                            <label for="perPageSelect" class="text-[11px] font-semibold whitespace-nowrap">Tampilkan:</label>
                            <select id="perPageSelect" x-model="perPage" @change="currentPage = 1"
                                class="text-xs font-bold rounded-xl border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white py-1.5 px-3 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                <option value="10">10 / halaman</option>
                                <option value="25">25 / halaman</option>
                                <option value="50">50 / halaman</option>
                                <option value="all">Semua Data</option>
                            </select>
                        </div>
                    </div>

                    <div class="overflow-x-auto mt-4 rounded-xl border-2 border-slate-200 dark:border-slate-700 shadow-sm">
                        <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300">
                            <thead
                                class="text-[10px] uppercase tracking-wider bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-black border-b-2 border-slate-200 dark:border-slate-700">
                                <tr>
                                    <th class="px-3.5 py-3.5 border-r border-slate-200 dark:border-slate-700">Kode</th>
                                    <th class="px-3.5 py-3.5 border-r border-slate-200 dark:border-slate-700">Mata Kuliah</th>
                                    <th class="px-3 py-3.5 text-center border-r border-slate-200 dark:border-slate-700">SKS</th>
                                    <th class="px-3 py-3.5 text-center border-r border-slate-200 dark:border-slate-700">Huruf
                                    </th>
                                    <th class="px-3 py-3.5 text-center border-r border-slate-200 dark:border-slate-700">Bobot
                                    </th>
                                    <th class="px-3.5 py-3.5 text-right font-bold">Mutu</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                                <template x-for="(n, idx) in paginatedItems" :key="n.id">
                                    <tr class="hover:bg-emerald-50/40 dark:hover:bg-slate-800/60 transition"
                                        :class="idx % 2 === 1 ? 'bg-slate-50/60 dark:bg-slate-900/30' : 'bg-white dark:bg-transparent'">
                                        <td class="px-3.5 py-3 font-mono font-bold text-emerald-800 dark:text-emerald-400 border-r border-slate-200 dark:border-slate-700"
                                            x-text="n.kode"></td>
                                        <td class="px-3.5 py-3 font-medium text-slate-900 dark:text-slate-100 border-r border-slate-200 dark:border-slate-700"
                                            x-text="n.nama"></td>
                                        <td class="px-3 py-3 text-center font-bold text-slate-900 dark:text-white border-r border-slate-200 dark:border-slate-700"
                                            x-text="n.sks"></td>
                                        <td class="px-3 py-3 text-center border-r border-slate-200 dark:border-slate-700">
                                            <span class="inline-flex items-center justify-center px-2 py-0.5 rounded font-black text-xs border"
                                                :class="n.badgeClass"
                                                x-text="n.huruf"></span>
                                        </td>
                                        <td class="px-3 py-3 text-center font-mono font-semibold text-slate-700 dark:text-slate-300 border-r border-slate-200 dark:border-slate-700"
                                            x-text="n.bobot"></td>
                                        <td class="px-3.5 py-3 text-right font-mono font-bold text-emerald-700 dark:text-emerald-400"
                                            x-text="n.mutu"></td>
                                    </tr>
                                </template>
                                <tr x-show="filteredItems.length === 0">
                                    <td colspan="6" class="px-4 py-8 text-center text-slate-400">
                                        Tidak ada mata kuliah yang cocok dengan kata kunci pencarian.
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot
                                class="border-t-2 border-slate-300 dark:border-slate-600 bg-slate-100 dark:bg-slate-800 font-bold text-slate-900 dark:text-slate-100">
                                <tr>
                                    <td colspan="2"
                                        class="px-3.5 py-3 text-right uppercase text-[10px] text-slate-600 dark:text-slate-400 border-r border-slate-200 dark:border-slate-700">
                                        Total Ditempuh:</td>
                                    <td
                                        class="px-3 py-3 text-center text-slate-900 dark:text-white font-extrabold border-r border-slate-200 dark:border-slate-700">
                                        {{ $mahasiswa->siakadNilais->sum('sks') }} SKS</td>
                                    <td colspan="2"
                                        class="px-3 py-3 text-right uppercase text-[10px] text-slate-600 dark:text-slate-400 border-r border-slate-200 dark:border-slate-700">
                                        Indeks Prestasi (IPK):</td>
                                    <td
                                        class="px-3.5 py-3 text-right text-emerald-700 dark:text-emerald-400 font-black text-sm">
                                        {{ number_format($trx?->ipk ?? 0, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Pagination Bar -->
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 mt-4 pt-3 border-t-2 border-slate-200 dark:border-slate-700 text-xs">
                        <div class="text-[11px] text-slate-500 dark:text-slate-400 order-2 sm:order-1 text-center sm:text-left">
                            Menampilkan <span class="font-bold text-slate-900 dark:text-white" x-text="startItem"></span> - 
                            <span class="font-bold text-slate-900 dark:text-white" x-text="endItem"></span> dari 
                            <span class="font-bold text-slate-900 dark:text-white" x-text="filteredItems.length"></span> mata kuliah
                            <span x-show="search" class="italic text-emerald-600 dark:text-emerald-400 font-medium">(hasil pencarian)</span>
                        </div>

                        <div class="flex items-center gap-1 order-1 sm:order-2 flex-wrap justify-center" x-show="totalPages > 1">
                            <!-- Tombol Sebelumnya -->
                            <button type="button" @click="goToPage(currentPage - 1)" :disabled="currentPage === 1"
                                class="px-2.5 py-1.5 rounded-xl border text-xs font-semibold transition flex items-center gap-1 disabled:opacity-30 disabled:cursor-not-allowed border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                                <span class="hidden sm:inline">Sebelumnya</span>
                            </button>

                            <!-- Nomor Halaman 1, 2, 3... -->
                            <template x-for="(p, i) in pages" :key="'page-' + i">
                                <div class="inline-flex">
                                    <template x-if="p === '...'">
                                        <span class="px-2 py-1 text-slate-400 font-bold select-none">...</span>
                                    </template>
                                    <template x-if="p !== '...'">
                                        <button type="button" @click="goToPage(p)"
                                            class="min-w-[32px] h-8 px-2 py-1 rounded-xl text-xs font-bold transition"
                                            :class="currentPage === p 
                                                ? 'bg-emerald-600 text-white shadow-sm ring-2 ring-emerald-600/30 font-black' 
                                                : 'border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700'"
                                            x-text="p">
                                        </button>
                                    </template>
                                </div>
                            </template>

                            <!-- Tombol Selanjutnya -->
                            <button type="button" @click="goToPage(currentPage + 1)" :disabled="currentPage === totalPages"
                                class="px-2.5 py-1.5 rounded-xl border text-xs font-semibold transition flex items-center gap-1 disabled:opacity-30 disabled:cursor-not-allowed border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700">
                                <span class="hidden sm:inline">Selanjutnya</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif
</x-app-layout>