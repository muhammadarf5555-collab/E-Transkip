<x-app-layout>
    @php
        $mhs = $transkrip->mahasiswa;
        $wis = $transkrip->wisudawan;
        $fak = $mhs->fakultas ?: $mhs->programStudi?->fakultas;
        $prodi = $mhs->programStudi;

        $kodeRomawiDefault = $fak?->kode_romawi ?: 'F.I';
        $jabatanDekanDefault = $transkrip->jabatan_dekan_override ?: ($fak?->jabatan_dekan ?: 'Dekan');
        $namaDekanDefault = $transkrip->nama_dekan_override ?: ($fak?->nama_dekan ?: 'Dr. H. Saifuddin, M.Ag.');
        $nipDekanDefault = $transkrip->nip_dekan_override ?: ($fak?->nip_dekan ?: '197203151998031002');

        if ($transkrip->no_sk_banpt_override) {
            $skBanptDefault = $transkrip->no_sk_banpt_override;
        } elseif ($prodi?->no_sk_akreditasi) {
            $skBanptDefault = $prodi->no_sk_akreditasi;
        } elseif ((int) $mhs->program_studi_id === 1 || stripos((string) $prodi?->nama_program_studi, 'Informatika') !== false) {
            $skBanptDefault = '0412/SK/LAM-INFOKOM/Ak/S/VIII/2024';
        } else {
            $skBanptDefault = $fak?->no_sk_banpt ?: '3184/SK/BAN-PT/Ak.Ppj/S/VIII/2024';
        }

        $tglLulusPreview = $wis?->tanggal_sk ?: ($wis?->tanggal_wisuda ?: now());
        $noUrutPreview = str_pad((string) ($transkrip->id + 540), 4, '0', STR_PAD_LEFT);
        $nomorTranskripOtomatis = "{$noUrutPreview}/Un.30/{$kodeRomawiDefault}/PP.00.9/" . $tglLulusPreview->format('m/Y');
    @endphp

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 dark:text-white leading-tight">
                    {{ __('Detail, Kelola Data & Validasi Transkrip Nilai') }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 flex items-center gap-2 flex-wrap">
                    <span>Transkrip Akademik Terpadu (SIAKAD + E-Wisuda): {{ $mhs->nama }} ({{ $mhs->nim }})</span>
                    @if ($transkrip->wisudawan || $mhs->status === 'Lulus')
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300">🎓 Wisudawan</span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800 dark:bg-blue-900/60 dark:text-blue-300">👤 Mahasiswa Aktif</span>
                    @endif
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.transkrip.pdf', ['transkrip' => $transkrip->id, 'mode' => 'draft']) }}" target="_blank" class="px-3.5 py-2 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 text-xs font-bold rounded-xl shadow-sm transition gap-1.5 inline-flex items-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Draft Transkrip (PDF)
                </a>
                <a href="{{ route('admin.transkrip.pdf', ['transkrip' => $transkrip->id, 'mode' => 'final']) }}" target="_blank" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-sm transition gap-1.5 inline-flex items-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Cetak PDF (Siap Cetak Pustikom)
                </a>
                <a href="{{ route('admin.transkrip.index') }}" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-xl transition">
                    &larr; Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Feedback Alerts -->
    @if (session('success'))
        <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold flex items-center justify-between shadow-sm">
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs shadow-sm">
            <span class="font-bold block mb-1">Periksa kembali isian Anda:</span>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Top Grid: Validation Form & QR Code -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Validation Action Card -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6">
            <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-gray-700">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Validasi Transkrip Jurusan / Fakultas</h3>
                    <p class="text-xs text-gray-500">Tentukan status keabsahan transkrip nilai akademik mahasiswa ini.</p>
                </div>
                <div>
                    @if ($transkrip->isDisahkan())
                        <span class="px-3 py-1 text-xs font-bold rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">STATUS: RESMI DISAHKAN</span>
                    @elseif ($transkrip->isMenungguPengesahan())
                        <span class="px-3 py-1 text-xs font-bold rounded-full bg-blue-100 text-[#2563EB] dark:bg-blue-950/60 dark:text-blue-300">STATUS: MENUNGGU PENGESAHAN AKHIR</span>
                    @elseif ($transkrip->isPerluPerbaikan())
                        <span class="px-3 py-1 text-xs font-bold rounded-full bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300">STATUS: PERLU PERBAIKAN</span>
                    @elseif ($transkrip->status_validasi === 'Ditolak' || $transkrip->status_validasi === 'ditolak')
                        <span class="px-3 py-1 text-xs font-bold rounded-full bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300">STATUS: DITOLAK</span>
                    @elseif ($transkrip->status_validasi === 'Draft' || $transkrip->status_validasi === 'draft')
                        <span class="px-3 py-1 text-xs font-bold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">STATUS: DRAFT</span>
                    @else
                        <span class="px-3 py-1 text-xs font-bold rounded-full bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300">STATUS: MENUNGGU VERIFIKASI</span>
                    @endif
                </div>
            </div>

            <!-- Workflow info note -->
            <div class="mt-4 p-3 rounded-xl bg-blue-50/70 dark:bg-blue-950/30 border border-blue-100 text-xs text-blue-900 dark:text-blue-200">
                <span class="font-bold">Alur Verifikasi Terpadu:</span>
                Admin Fakultas memverifikasi kesesuaian nilai SIAKAD, SK Yudisium &amp; PIN Ijazah (E-Wisuda), serta Pejabat Dekan. Transkrip yang disetujui diteruskan ke <span class="font-semibold text-[#2563EB]">Super Admin</span> untuk pengesahan akhir.
            </div>

            <form method="POST" action="{{ route('admin.transkrip.validate', $transkrip) }}" class="mt-5 space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="status_validasi" value="Pilih Keputusan Verifikasi" />
                        <select id="status_validasi" name="status_validasi" class="mt-1 block w-full text-sm rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-emerald-500 focus:ring-emerald-500" required>
                            <option value="menunggu_pengesahan" {{ $transkrip->status_validasi === 'menunggu_pengesahan' || $transkrip->status_validasi === 'Valid' ? 'selected' : '' }}>Verifikasi &amp; Ajukan Pengesahan Akhir</option>
                            <option value="perlu_perbaikan" {{ $transkrip->status_validasi === 'perlu_perbaikan' ? 'selected' : '' }}>Kembalikan untuk Perbaikan (Revisi Data)</option>
                            <option value="ditolak" {{ $transkrip->status_validasi === 'ditolak' || $transkrip->status_validasi === 'Ditolak' ? 'selected' : '' }}>Tolak Berkas</option>
                            <option value="Draft" {{ $transkrip->status_validasi === 'Draft' ? 'selected' : '' }}>Simpan Sebagai Draf</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label for="predikat_view" value="IPK &amp; Predikat Saat Ini" />
                        <x-text-input id="predikat_view" type="text" value="IPK {{ number_format($transkrip->ipk, 2) }} ({{ $transkrip->total_sks }} SKS) — {{ $transkrip->predikat }}" class="mt-1 block w-full bg-gray-50 text-sm font-semibold" disabled />
                    </div>
                </div>

                <div>
                    <x-input-label for="catatan" value="Catatan / Keterangan Validasi (Opsional)" />
                    <textarea id="catatan" name="catatan" rows="2" class="mt-1 block w-full text-sm rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-emerald-500 focus:ring-emerald-500" placeholder="Berikan catatan perbaikan jika ditolak atau catatan tambahan...">{{ old('catatan', $transkrip->catatan) }}</textarea>
                </div>

                <div class="pt-2 flex justify-end">
                    <x-primary-button class="py-2.5 px-6">
                        Simpan Keputusan Validasi
                    </x-primary-button>
                </div>
            </form>
        </div>

        <!-- Verification QR Code Card -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6 text-center flex flex-col items-center justify-center">
            <h3 class="text-sm font-bold uppercase tracking-wider text-gray-500 mb-4">QR Code Verifikasi Publik</h3>
            <div class="p-3 bg-white rounded-2xl border border-gray-200 shadow-sm inline-block">
                <img src="data:image/svg+xml;base64,{{ $qrCodeSvg }}" alt="QR Code Verifikasi" class="w-32 h-32" />
            </div>
            <div class="mt-3 font-mono text-[11px] font-bold text-gray-800 dark:text-gray-200 truncate max-w-full">
                {{ $transkrip->kode_verifikasi }}
            </div>
            <a href="{{ route('verifikasi.transkrip', $transkrip->kode_verifikasi) }}" target="_blank" class="mt-3 inline-flex items-center text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:underline">
                Uji Tautan Verifikasi &rarr;
            </a>
        </div>
    </div>

    <!-- ========================================================================================= -->
    <!-- FORM TERPADU: INPUT / EDIT MANUAL SK YUDISIUM, IJAZAH, SKRIPSI, BIODATA & PEJABAT DEKAN   -->
    <!-- ========================================================================================= -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border-2 border-emerald-500/30 dark:border-emerald-700/50 shadow-sm p-6 mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-4 border-b border-gray-100 dark:border-gray-700 mb-5">
            <div>
                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300 mb-1">
                    FORMULIR TERPADU (SIAKAD + E-WISUDA + PEJABAT DINAMIS)
                </div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white">
                    Kelola / Input Manual Data Kelulusan, SK Yudisium, Nomor Ijazah &amp; Pejabat Transkrip
                </h3>
                <p class="text-xs text-gray-500 mt-0.5">
                    Seluruh data di bawah ini langsung terhubung ke cetakan <strong>Draft Transkrip</strong> maupun <strong>Transkrip Siap Cetak Pustikom</strong> tanpa perlu berpindah menu.
                </p>
            </div>
            <div class="text-xs text-right">
                <span class="px-2.5 py-1 rounded-lg font-mono font-semibold bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                    Sumber Wisuda: {{ $wis?->sumber_data ?: 'SIAKAD_LOCAL' }}
                </span>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.transkrip.update-complete', $transkrip) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Kolom 1: Identitas Mahasiswa & Nomor Transkrip -->
                <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 space-y-3.5">
                    <div class="text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700 pb-2">
                        1. Biodata &amp; Penomoran Transkrip
                    </div>
                    <div>
                        <x-input-label for="nama" value="Nama Lengkap Mahasiswa (Sesuai Ijazah)" />
                        <x-text-input id="nama" name="nama" type="text" class="mt-1 block w-full text-sm font-semibold" value="{{ old('nama', $mhs->nama) }}" required />
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <x-input-label for="tempat_lahir" value="Tempat Lahir" />
                            <x-text-input id="tempat_lahir" name="tempat_lahir" type="text" class="mt-1 block w-full text-sm" value="{{ old('tempat_lahir', $mhs->tempat_lahir ?: 'Cirebon') }}" />
                        </div>
                        <div>
                            <x-input-label for="tanggal_lahir" value="Tanggal Lahir" />
                            <x-text-input id="tanggal_lahir" name="tanggal_lahir" type="date" class="mt-1 block w-full text-sm" value="{{ old('tanggal_lahir', $mhs->tanggal_lahir?->format('Y-m-d')) }}" />
                        </div>
                    </div>
                    <div>
                        <x-input-label for="nomor_transkrip" value="Nomor Surat Transkrip Resmi" />
                        <x-text-input id="nomor_transkrip" name="nomor_transkrip" type="text" class="mt-1 block w-full text-sm font-mono" value="{{ old('nomor_transkrip', $transkrip->nomor_transkrip) }}" placeholder="Otomatis: {{ $nomorTranskripOtomatis }}" />
                        <p class="text-[11px] text-gray-400 mt-1">Kosongkan bila ingin nomor otomatis: <code class="font-mono">{{ $nomorTranskripOtomatis }}</code></p>
                    </div>
                    <div>
                        <x-input-label for="no_sk_banpt_override" value="Nomor SK BAN-PT / LAM Akreditasi Prodi" />
                        <x-text-input id="no_sk_banpt_override" name="no_sk_banpt_override" type="text" class="mt-1 block w-full text-sm font-mono" value="{{ old('no_sk_banpt_override', $skBanptDefault) }}" />
                    </div>
                </div>

                <!-- Kolom 2: SK Yudisium, Nomor Ijazah Nasional (PIN), & Kelulusan -->
                <div class="p-4 rounded-xl bg-blue-50/40 dark:bg-gray-900/50 border border-blue-200 dark:border-gray-700 space-y-3.5">
                    <div class="text-xs font-bold uppercase tracking-wider text-blue-800 dark:text-blue-400 border-b border-blue-200 dark:border-gray-700 pb-2">
                        2. Data E-Wisuda (SK Yudisium &amp; PIN Ijazah)
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <x-input-label for="no_sk" value="Nomor SK Yudisium / Lulus" />
                            <x-text-input id="no_sk" name="no_sk" type="text" class="mt-1 block w-full text-sm font-mono" value="{{ old('no_sk', $wis?->no_sk) }}" placeholder="SK/YUDISIUM/..." />
                        </div>
                        <div>
                            <x-input-label for="tanggal_sk" value="Tanggal Kelulusan (SK)" />
                            <x-text-input id="tanggal_sk" name="tanggal_sk" type="date" class="mt-1 block w-full text-sm" value="{{ old('tanggal_sk', $wis?->tanggal_sk?->format('Y-m-d') ?: ($wis?->tanggal_wisuda?->format('Y-m-d') ?: date('Y-m-d'))) }}" />
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <x-input-label for="no_ijazah" value="Nomor Ijazah Nasional (PIN)" />
                            <x-text-input id="no_ijazah" name="no_ijazah" type="text" class="mt-1 block w-full text-sm font-mono" value="{{ old('no_ijazah', $wis?->no_ijazah) }}" placeholder="2010634998726..." />
                        </div>
                        <div>
                            <x-input-label for="tanggal_ijazah" value="Tanggal Ijazah" />
                            <x-text-input id="tanggal_ijazah" name="tanggal_ijazah" type="date" class="mt-1 block w-full text-sm" value="{{ old('tanggal_ijazah', $wis?->tanggal_ijazah?->format('Y-m-d')) }}" />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <x-input-label for="periode_wisuda" value="Periode Wisuda" />
                            <x-text-input id="periode_wisuda" name="periode_wisuda" type="text" class="mt-1 block w-full text-sm" value="{{ old('periode_wisuda', $wis?->periode_wisuda ?: 'Wisuda Ganjil '.date('Y')) }}" />
                        </div>
                        <div>
                            <x-input-label for="predikat" value="Predikat Kelulusan" />
                            <select id="predikat" name="predikat" class="mt-1 block w-full text-sm rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                                @php
                                    $curPredikat = old('predikat', $transkrip->predikat ?: 'Dengan Pujian (Cum Laude)');
                                @endphp
                                <option value="Dengan Pujian (Cum Laude)" {{ str_contains($curPredikat, 'Pujian') ? 'selected' : '' }}>Dengan Pujian (Cum Laude)</option>
                                <option value="Sangat Memuaskan" {{ $curPredikat === 'Sangat Memuaskan' ? 'selected' : '' }}>Sangat Memuaskan</option>
                                <option value="Memuaskan" {{ $curPredikat === 'Memuaskan' ? 'selected' : '' }}>Memuaskan</option>
                                <option value="Cukup" {{ $curPredikat === 'Cukup' ? 'selected' : '' }}>Cukup</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <x-input-label for="gelar" value="Gelar Singkat" />
                            <x-text-input id="gelar" name="gelar" type="text" class="mt-1 block w-full text-sm" value="{{ old('gelar', $wis?->gelar ?: $prodi?->gelar_akademik) }}" placeholder="S.Kom., S.Pd." />
                        </div>
                        <div>
                            <x-input-label for="gelar_full" value="Gelar Lengkap" />
                            <x-text-input id="gelar_full" name="gelar_full" type="text" class="mt-1 block w-full text-sm" value="{{ old('gelar_full', $wis?->gelar_full ?: $prodi?->gelar_akademik_full) }}" placeholder="Sarjana Komputer" />
                        </div>
                    </div>
                </div>

                <!-- Kolom 3: Pejabat Penandatangan Dinamis (Dekan & NIP) -->
                <div class="p-4 rounded-xl bg-emerald-50/50 dark:bg-gray-900/50 border border-emerald-200 dark:border-gray-700 space-y-3.5">
                    <div class="text-xs font-bold uppercase tracking-wider text-emerald-800 dark:text-emerald-400 border-b border-emerald-200 dark:border-gray-700 pb-2">
                        3. Pejabat Penandatangan (Dinamis)
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <x-input-label for="jabatan_dekan_override" value="Jabatan Pejabat" />
                            <select id="jabatan_dekan_override" name="jabatan_dekan_override" class="mt-1 block w-full text-sm rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                                <option value="Dekan" {{ $jabatanDekanDefault === 'Dekan' ? 'selected' : '' }}>Dekan (Definitif)</option>
                                <option value="Plt. Dekan" {{ $jabatanDekanDefault === 'Plt. Dekan' ? 'selected' : '' }}>Plt. Dekan</option>
                                <option value="Pj. Dekan" {{ $jabatanDekanDefault === 'Pj. Dekan' ? 'selected' : '' }}>Pj. Dekan</option>
                                <option value="Direktur" {{ $jabatanDekanDefault === 'Direktur' ? 'selected' : '' }}>Direktur</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="kode_romawi" value="Kode Romawi Surat" />
                            <x-text-input id="kode_romawi" name="kode_romawi" type="text" class="mt-1 block w-full text-sm font-mono" value="{{ old('kode_romawi', $kodeRomawiDefault) }}" placeholder="F.I, F.II" />
                        </div>
                    </div>
                    <div>
                        <x-input-label for="nama_dekan_override" value="Nama Lengkap &amp; Gelar Dekan Penandatangan" />
                        <x-text-input id="nama_dekan_override" name="nama_dekan_override" type="text" class="mt-1 block w-full text-sm font-semibold" value="{{ old('nama_dekan_override', $namaDekanDefault) }}" />
                    </div>
                    <div>
                        <x-input-label for="nip_dekan_override" value="NIP Dekan Penandatangan" />
                        <x-text-input id="nip_dekan_override" name="nip_dekan_override" type="text" class="mt-1 block w-full text-sm font-mono" value="{{ old('nip_dekan_override', $nipDekanDefault) }}" />
                    </div>
                    <div class="pt-1">
                        <label class="inline-flex items-start gap-2 cursor-pointer text-xs text-gray-700 dark:text-gray-300">
                            <input type="checkbox" name="update_master_pejabat" value="1" checked class="mt-0.5 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500" />
                            <span>
                                <strong>Simpan sebagai Pejabat Aktif Fakultas:</strong> Terapkan perubahan nama Dekan, NIP, dan SK BAN-PT ini ke seluruh mahasiswa di {{ $fak?->nama_fakultas_singkat ?: 'Fakultas ini' }}.
                            </span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Baris Penuh: Judul Skripsi / Tugas Akhir -->
            <div>
                <x-input-label for="judul_skripsi" value="Judul Skripsi / Tugas Akhir Lengkap (Tampil di Transkrip PDF)" />
                <textarea id="judul_skripsi" name="judul_skripsi" rows="2" class="mt-1 block w-full text-sm rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-emerald-500 focus:ring-emerald-500" placeholder="Tuliskan judul skripsi mahasiswa secara lengkap...">{{ old('judul_skripsi', $wis?->judul_skripsi) }}</textarea>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                <p class="text-xs text-gray-500">
                    💡 Setelah disimpan, Anda dapat langsung mengklik tombol <strong>"Cetak PDF (Siap Cetak Pustikom)"</strong> di kanan atas untuk melihat hasilnya.
                </p>
                <button type="submit" style="background-color: #059669 !important; color: #ffffff !important;" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl shadow-md transition inline-flex items-center justify-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Simpan Seluruh Data Transkrip, SK Yudisium &amp; Pejabat
                </button>
            </div>
        </form>
    </div>

    <!-- Course Grades Table + Direct Manual Grade Input -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-gray-100 dark:border-gray-700">
            <div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Daftar Mata Kuliah &amp; Nilai Akademik ({{ $mhs->siakadNilais->count() }} MK)</h3>
                <p class="text-xs text-gray-500">Mata kuliah dari SIAKAD maupun input manual yang dimasukkan ke dalam perhitungan transkrip akhir.</p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-nilai-transkrip')" style="background-color: #059669 !important; color: #ffffff !important;" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-sm transition inline-flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Nilai Mata Kuliah Manual
                </button>
            </div>
        </div>

        <div class="overflow-x-auto mt-4">
            <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400">
                    <tr>
                        <th class="px-4 py-3 rounded-l-lg w-12">No</th>
                        <th class="px-4 py-3">Kode</th>
                        <th class="px-4 py-3">Mata Kuliah</th>
                        <th class="px-4 py-3 text-center">Semester</th>
                        <th class="px-4 py-3 text-center">SKS</th>
                        <th class="px-4 py-3 text-center">Nilai</th>
                        <th class="px-4 py-3 text-center">Bobot</th>
                        <th class="px-4 py-3 text-right">AK (SKS x Bobot)</th>
                        <th class="px-4 py-3 rounded-r-lg text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($mhs->siakadNilais as $idx => $nilai)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30">
                            <td class="px-4 py-3 text-xs text-gray-400 font-mono">{{ $idx + 1 }}</td>
                            <td class="px-4 py-3 font-mono font-bold text-xs text-gray-900 dark:text-white">{{ $nilai->mataKuliah?->kode_mata_kuliah ?? '-' }}</td>
                            <td class="px-4 py-3 font-medium text-xs text-gray-800 dark:text-gray-200">
                                {{ $nilai->mataKuliah?->nama_mata_kuliah ?? '-' }}
                                @if ($nilai->sumber_data === 'Manual')
                                    <span class="ml-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">Manual</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center text-xs">Smtr {{ $nilai->semester }}</td>
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
                            <td class="px-4 py-3 text-right">
                                <button type="button" @click="$dispatch('open-modal', 'edit-nilai-trx-{{ $nilai->id }}')" class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200 transition">
                                    Ubah Nilai
                                </button>

                                <!-- Modal Edit Nilai Langsung di Halaman Transkrip -->
                                <x-modal name="edit-nilai-trx-{{ $nilai->id }}" focusable>
                                    <form method="POST" action="{{ route('admin.nilai.update', $nilai) }}" class="p-6 text-left space-y-4">
                                        @csrf
                                        @method('PUT')
                                        <h3 class="text-base font-bold text-gray-900 dark:text-white">
                                            Ubah Nilai: {{ $nilai->mataKuliah?->nama_mata_kuliah }}
                                        </h3>
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <x-input-label value="Nilai Saat Ini" />
                                                <x-text-input type="text" value="{{ $nilai->nilai_huruf }} (Bobot {{ number_format($nilai->bobot, 2) }})" class="mt-1 block w-full bg-gray-50 text-sm" disabled />
                                            </div>
                                            <div>
                                                <x-input-label for="nilai_huruf_{{ $nilai->id }}" value="Nilai Huruf Baru *" />
                                                <select id="nilai_huruf_{{ $nilai->id }}" name="nilai_huruf" class="mt-1 block w-full text-sm rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-emerald-500 focus:ring-emerald-500" required>
                                                    @foreach (['A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'D', 'E'] as $grade)
                                                        <option value="{{ $grade }}" {{ $nilai->nilai_huruf === $grade ? 'selected' : '' }}>{{ $grade }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div>
                                            <x-input-label for="alasan_{{ $nilai->id }}" value="Alasan Perubahan Nilai *" />
                                            <x-text-input id="alasan_{{ $nilai->id }}" name="alasan" type="text" class="mt-1 block w-full text-sm" value="Penyesuaian nilai yudisium / perbaikan nilai transkrip" required />
                                        </div>
                                        <div class="flex justify-end gap-2 pt-2">
                                            <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                                            <x-primary-button>Simpan &amp; Hitung Ulang IPK</x-primary-button>
                                        </div>
                                    </form>
                                </x-modal>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-8 text-center text-gray-400 text-xs">
                                Belum ada mata kuliah yang terdaftar. Klik tombol <strong>"Tambah Nilai Mata Kuliah Manual"</strong> di kanan atas tabel ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="border-t-2 border-gray-200 dark:border-gray-700 font-bold text-xs bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <td colspan="4" class="px-4 py-3 text-right">TOTAL AKUMULASI:</td>
                        <td class="px-4 py-3 text-center text-gray-900 dark:text-white">{{ $transkrip->total_sks }} SKS</td>
                        <td colspan="2" class="px-4 py-3 text-center text-emerald-700 dark:text-emerald-400">IPK: {{ number_format($transkrip->ipk, 2) }}</td>
                        <td class="px-4 py-3 text-right text-emerald-600 dark:text-emerald-400 font-mono font-black text-sm">
                            {{ number_format($transkrip->total_mutu, 2) }}
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Modal Tambah Nilai Mata Kuliah Manual Langsung dari Halaman Transkrip -->
    <x-modal name="add-nilai-transkrip" focusable>
        <form method="POST" action="{{ route('admin.nilai.store') }}" class="p-6 text-left space-y-4">
            @csrf
            <input type="hidden" name="mahasiswa_id" value="{{ $mhs->id }}" />
            <input type="hidden" name="from_transkrip" value="1" />
            <input type="hidden" name="force_overwrite" value="1" />

            <div class="border-b border-gray-100 dark:border-gray-700 pb-3">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                    Tambah Nilai Mata Kuliah Manual ({{ $mhs->nama }})
                </h3>
                <p class="text-xs text-gray-500 mt-0.5">
                    Pilih dari daftar mata kuliah yang ada atau ketik nama mata kuliah baru di bawah. IPK dan Total SKS transkrip akan langsung dihitung ulang otomatis.
                </p>
            </div>

            <div>
                <x-input-label for="trx_mata_kuliah_id" value="Pilih Mata Kuliah yang Sudah Ada" />
                <select id="trx_mata_kuliah_id" name="mata_kuliah_id" class="mt-1 block w-full text-sm rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">-- Atau Ketik Mata Kuliah Baru di Bawah --</option>
                    @foreach ($mataKuliahs ?? [] as $mk)
                        <option value="{{ $mk->id }}">{{ $mk->kode_mata_kuliah }} — {{ $mk->nama_mata_kuliah }} ({{ $mk->sks }} SKS)</option>
                    @endforeach
                </select>
            </div>

            <div class="p-3.5 rounded-xl bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 space-y-3">
                <div class="text-xs font-bold text-gray-700 dark:text-gray-300">
                    Opsional: Isi Jika Mata Kuliah Belum Ada di Daftar
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                    <div>
                        <x-input-label for="kode_mk_baru" value="Kode MK" />
                        <x-text-input id="kode_mk_baru" name="kode_mk_baru" type="text" class="mt-1 block w-full text-sm font-mono" placeholder="TI-401" />
                    </div>
                    <div class="sm:col-span-2">
                        <x-input-label for="nama_mk_baru" value="Nama Mata Kuliah Baru" />
                        <x-text-input id="nama_mk_baru" name="nama_mk_baru" type="text" class="mt-1 block w-full text-sm" placeholder="Contoh: Skripsi / Tugas Akhir" />
                    </div>
                    <div>
                        <x-input-label for="sks_baru" value="SKS" />
                        <x-text-input id="sks_baru" name="sks_baru" type="number" min="1" max="10" class="mt-1 block w-full text-sm" value="3" />
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <x-input-label for="trx_semester" value="Semester *" />
                    <select id="trx_semester" name="semester" class="mt-1 block w-full text-sm rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-emerald-500 focus:ring-emerald-500" required>
                        @for ($s = 1; $s <= 10; $s++)
                            <option value="{{ $s }}" {{ $s === 8 ? 'selected' : '' }}>Semester {{ $s }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <x-input-label for="trx_nilai_huruf" value="Nilai Huruf *" />
                    <select id="trx_nilai_huruf" name="nilai_huruf" class="mt-1 block w-full text-sm rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-emerald-500 focus:ring-emerald-500" required>
                        @foreach ($bobotList ?? [] as $b)
                            <option value="{{ $b->nilai_huruf }}">{{ $b->nilai_huruf }} (Bobot {{ number_format($b->bobot, 2) }})</option>
                        @endforeach
                        @if (empty($bobotList) || count($bobotList) === 0)
                            <option value="A">A (4.00)</option>
                            <option value="A-">A- (3.75)</option>
                            <option value="B+">B+ (3.50)</option>
                            <option value="B">B (3.00)</option>
                            <option value="C">C (2.00)</option>
                        @endif
                    </select>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                <x-primary-button style="background-color: #059669 !important;">
                    Tambah Nilai &amp; Hitung Ulang IPK
                </x-primary-button>
            </div>
        </form>
    </x-modal>

    <!-- Riwayat Perubahan Status & Log Pengesahan -->
    <div class="mt-8 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6">
        <h3 class="text-base font-bold text-gray-900 dark:text-white pb-3 border-b border-gray-100 dark:border-gray-700 mb-4 flex items-center justify-between">
            <span>Riwayat Perubahan Status &amp; Audit Transkrip</span>
            <span class="text-xs font-normal text-gray-500">{{ $transkrip->histories->count() }} Perubahan Tercatat</span>
        </h3>

        @if ($transkrip->verifiedByJurusan || $transkrip->ratifiedByAdmin)
            <div class="mb-5 grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                @if ($transkrip->verifiedByJurusan)
                    <div class="p-3.5 rounded-xl bg-blue-50/70 dark:bg-blue-950/30 border border-blue-200 dark:border-blue-900">
                        <span class="font-bold text-[#2563EB]">Diverifikasi oleh Fakultas/Jurusan:</span>
                        <div class="font-semibold text-gray-800 dark:text-gray-200 mt-1">{{ $transkrip->verifiedByJurusan->name }}</div>
                        <div class="text-gray-500 font-mono text-[11px]">{{ $transkrip->verified_by_jurusan_at?->format('d/m/Y H:i') }} WIB</div>
                    </div>
                @endif
                @if ($transkrip->ratifiedByAdmin)
                    <div class="p-3.5 rounded-xl bg-emerald-50/70 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-900">
                        <span class="font-bold text-emerald-700 dark:text-emerald-400">Disahkan Akhir oleh Admin Sistem:</span>
                        <div class="font-semibold text-gray-800 dark:text-gray-200 mt-1">{{ $transkrip->ratifiedByAdmin->name }}</div>
                        <div class="text-gray-500 font-mono text-[11px]">{{ $transkrip->ratified_by_admin_at?->format('d/m/Y H:i') }} WIB</div>
                    </div>
                @endif
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-gray-600 dark:text-gray-300">
                <thead class="text-[11px] uppercase bg-gray-50 dark:bg-gray-700/50 text-gray-500">
                    <tr>
                        <th class="px-4 py-2.5 rounded-l-lg">Waktu</th>
                        <th class="px-4 py-2.5">Oleh</th>
                        <th class="px-4 py-2.5">Status Sebelumnya</th>
                        <th class="px-4 py-2.5">Status Terbaru</th>
                        <th class="px-4 py-2.5 rounded-r-lg">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($transkrip->histories as $h)
                        <tr>
                            <td class="px-4 py-3 font-mono text-gray-500">{{ $h->created_at?->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 font-semibold text-gray-800 dark:text-gray-200">{{ $h->user?->name ?? 'Sistem' }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 font-mono text-[11px]">
                                    {{ $h->status_sebelumnya ?? '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-bold text-[11px]">
                                    {{ $h->status_terbaru }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $h->catatan ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-gray-400">Belum ada riwayat perubahan status pada berkas transkrip ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
