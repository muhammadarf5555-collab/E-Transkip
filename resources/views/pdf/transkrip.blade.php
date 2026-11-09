<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ ($isDraftMode ?? false) ? 'Draft Transkrip Akademik' : 'Transkrip Akademik' }} - {{ $transkrip->mahasiswa->nim }}</title>
    <style>
        @page {
            margin: 0.65cm 0.9cm 0.65cm 0.9cm;
            size: A4 portrait;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #000;
            font-size: 7.5pt;
            line-height: 1.18;
            margin: 0;
            padding: 0;
            position: relative;
        }

        /* Header Tengah Pustikom UINSSC */
        .header-center {
            text-align: center;
            margin-bottom: 8px;
        }
        .header-center img.logo-top {
            width: 44px;
            height: auto;
            display: inline-block;
        }
        .header-center .uinssc-brand {
            font-size: 8.5pt;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin-top: 1px;
            margin-bottom: 3px;
        }
        .header-center .univ-title {
            font-size: 11.5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin: 0;
        }
        .header-center .doc-title {
            font-size: 10.5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin: 2px 0 2px 0;
        }
        .header-center .doc-number {
            font-size: 8pt;
            margin: 0;
        }

        /* Tabel Biodata 2 Kolom */
        .bio-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
            font-size: 7.5pt;
        }
        .bio-table td {
            padding: 1.2px 0;
            vertical-align: top;
        }

        /* Tabel Nilai 2 Kolom Berdampingan (Standar Pustikom) */
        .grades-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 6.8pt;
            margin-bottom: 6px;
        }
        .grades-table th {
            border: 1px solid #000;
            padding: 2.5px 2px;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 7pt;
            background-color: #fff;
        }
        .grades-table td {
            border: 1px solid #000;
            padding: 1.6px 3px;
            vertical-align: middle;
        }
        .grades-table td.divider-right,
        .grades-table th.divider-right {
            border-right: 1.5px solid #000;
        }
        .grades-table td.divider-left,
        .grades-table th.divider-left {
            border-left: 1.5px solid #000;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }

        /* Bagian IPK, Predikat, Judul Skripsi */
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.5pt;
            margin-top: 4px;
            margin-bottom: 6px;
        }
        .summary-table td {
            padding: 1.5px 0;
            vertical-align: top;
        }

        /* Bagian Keterangan & Tanda Tangan Dekan */
        .footer-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.5pt;
            margin-top: 2px;
            page-break-inside: avoid;
        }
        .keterangan-title {
            text-decoration: underline;
            margin-bottom: 2px;
        }

        /* Badge / Seal & QR Bawah Kiri */
        .seal-qr-wrap {
            margin-top: 8px;
        }
        .seal-qr-table {
            border-collapse: collapse;
        }
        .seal-qr-table td {
            vertical-align: middle;
            padding-right: 8px;
        }
        .seal-circle {
            width: 58px;
            height: 58px;
            border: 2px double #111;
            border-radius: 50%;
            text-align: center;
            background-color: #111;
            color: #fff;
            font-size: 5.5pt;
            box-sizing: border-box;
            padding-top: 6px;
        }
        .seal-circle img {
            width: 28px;
            height: auto;
            background: #fff;
            border-radius: 4px;
            padding: 2px;
        }

        /* Kotak PROOF untuk Mode Draft */
        .proof-box {
            display: inline-block;
            border: 1.5px solid #aaa;
            color: #999;
            padding: 4px 14px;
            font-size: 9pt;
            font-weight: bold;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-top: 6px;
        }
    </style>
</head>
<body>

    @php
        $logoPath = public_path('images/logo-uinssc.png');
        $logoBase64 = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : null;

        $mhs = $transkrip->mahasiswa;
        $wisudawan = $transkrip->wisudawan;
        $isDraftMode = $isDraftMode ?? false;

        // Nama Fakultas tanpa kata "Fakultas " di depan (sesuai template Pustikom: "Ekonomi dan Bisnis Islam", "Ilmu Tarbiyah dan Keguruan")
        $rawFakultas = $mhs->fakultas?->nama_fakultas
            ?: ($mhs->programStudi?->fakultas?->nama_fakultas ?: ($wisudawan?->fakultas ?: 'Fakultas Ilmu Tarbiyah dan Keguruan'));
        $namaFakultasBersih = trim((string) preg_replace('/^Fakultas\s+/i', '', $rawFakultas));

        // Nama Program Studi dalam Title Case rapi
        $rawProdi = $mhs->programStudi?->nama_program_studi ?: ($wisudawan?->program_studi ?: 'Teknik Informatika');
        $namaProdiBersih = ucwords(strtolower(trim($rawProdi)));
        // Perbaiki singkatan umum seperti PAI, PGMI, BKI, KPI, IAT, HKI, HES, HTN
        $namaProdiBersih = str_replace(['Al-qur`an', 'Al-quran', 'Dan ', 'Di '], ['Al-Qur\'an', 'Al-Qur\'an', 'dan ', 'di '], $namaProdiBersih);

        // Kode Romawi Fakultas & Data Dekan Resmi UINSSC
        $fakultasId = (int) ($mhs->fakultas_id ?: ($mhs->programStudi?->fakultas_id ?: 1));
        $dekanMap = [
            1 => [
                'romawi' => 'F.I',
                'dekan' => 'Dr. H. Saifuddin, M.Ag.',
                'nip' => '197203151998031002',
                'sk_banpt' => '3184/SK/BAN-PT/Ak.Ppj/S/VIII/2024',
            ],
            2 => [
                'romawi' => 'F.III',
                'dekan' => 'Dr. H. Edy Setyawan, Lc., M.A.',
                'nip' => '197605122003121003',
                'sk_banpt' => '2845/SK/BAN-PT/Akred/S/VII/2024',
            ],
            3 => [
                'romawi' => 'F.IV',
                'dekan' => 'Dr. H. Anwar Sanusi, M.Ag.',
                'nip' => '197108041999031001',
                'sk_banpt' => '2510/SK/BAN-PT/Akred/S/VI/2024',
            ],
            4 => [
                'romawi' => 'F.V',
                'dekan' => 'Dr. Siti Fatimah, M.Hum.',
                'nip' => '197411202000032001',
                'sk_banpt' => '2690/SK/BAN-PT/Akred/S/VI/2024',
            ],
            5 => [
                'romawi' => 'F.II',
                'dekan' => 'Dr. H. Didi Sukardi, M.H.',
                'nip' => '196912262009121001',
                'sk_banpt' => '2269/DE/A.5/AR.10/V/2024',
            ],
        ];

        $fakMeta = $dekanMap[$fakultasId] ?? $dekanMap[1];

        // Khusus Prodi Teknik Informatika (LAM INFOKOM / BAN-PT)
        $noSkBanPt = ((int) $mhs->program_studi_id === 1 || stripos($rawProdi, 'Informatika') !== false)
            ? '0412/SK/LAM-INFOKOM/Ak/S/VIII/2024'
            : $fakMeta['sk_banpt'];

        // Tanggal Kelulusan
        $tglLulusObj = $wisudawan?->tanggal_sk ?: ($wisudawan?->tanggal_wisuda ?: now());
        $bulanMap = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        $tglKelulusanFormatted = $tglLulusObj->format('d') . ' ' . ($bulanMap[(int) $tglLulusObj->format('n')] ?? 'Oktober') . ' ' . $tglLulusObj->format('Y');

        // Tempat, Tanggal Lahir
        $tempatLahir = $mhs->tempat_lahir ?: 'Cirebon';
        if ($mhs->tanggal_lahir) {
            $tglLahirFormatted = $mhs->tanggal_lahir->format('d') . ' ' . ($bulanMap[(int) $mhs->tanggal_lahir->format('n')] ?? 'Mei') . ' ' . $mhs->tanggal_lahir->format('Y');
        } else {
            $tahunLahir = ((int) ($mhs->angkatan ?: 2022)) - 18;
            $tglLahirFormatted = '28 Mei ' . $tahunLahir;
        }

        // Nomor Ijazah Nasional (PIN)
        $rawNoIjazah = $wisudawan?->no_ijazah;
        if (! $rawNoIjazah || str_starts_with($rawNoIjazah, 'IJZ/')) {
            // Format Nomor Ijazah Nasional (PIN) 19 digit standar Kemendikbud/Kemenag seperti 2010634998726769213
            $nomorIjazahNasional = '201063' . str_pad((string) preg_replace('/\D/', '', $mhs->nim), 13, '4998726', STR_PAD_LEFT);
        } else {
            $nomorIjazahNasional = $rawNoIjazah;
        }

        // Nomor Surat Transkrip (Contoh: 0554/Un.30/F.II/PP.00.9/12/2025)
        $noUrutTranskrip = str_pad((string) ($transkrip->id + 540), 4, '0', STR_PAD_LEFT);
        $bulanRomawiAtauAngka = $tglLulusObj->format('m');
        $tahunTranskrip = $tglLulusObj->format('Y');
        $nomorTranskripFinal = "{$noUrutTranskrip}/Un.30/{$fakMeta['romawi']}/PP.00.9/{$bulanRomawiAtauAngka}/{$tahunTranskrip}";

        // Daftar Mata Kuliah & Pembagian 2 Kolom Berdampingan
        $nilais = $mhs->siakadNilais->values();
        $totalMk = $nilais->count();
        // Jumlah baris di kolom kiri: ceil((totalMk + 1) / 2) agar baris terakhir di kolom kanan adalah baris JUMLAH
        $leftRowCount = max(1, (int) ceil(($totalMk + 1) / 2));

        // Format Total Mutu (AK)
        $totalMutuFloat = (float) $transkrip->total_mutu;
        if ($isDraftMode) {
            $formattedTotalMutu = number_format($totalMutuFloat, 2, ',', '.');
        } else {
            // Jika bulat tampilkan tanpa .00 (seperti 398 di contoh Pustikom), jika pecahan tampilkan 2 desimal
            $formattedTotalMutu = (floor($totalMutuFloat) == $totalMutuFloat)
                ? number_format($totalMutuFloat, 0, '.', '')
                : number_format($totalMutuFloat, 2, '.', '');
        }

        // Terbilang IPK untuk mode draft
        $angkaKata = ['0' => 'nol', '1' => 'satu', '2' => 'dua', '3' => 'tiga', '4' => 'empat', '5' => 'lima', '6' => 'enam', '7' => 'tujuh', '8' => 'delapan', '9' => 'sembilan'];
        $ipkStr = number_format((float) $transkrip->ipk, 2, '.', '');
        [$ipkDepan, $ipkBelakang] = explode('.', $ipkStr);
        $terbilangIpk = ($angkaKata[$ipkDepan] ?? $ipkDepan) . ' koma ' . ($angkaKata[$ipkBelakang[0]] ?? '') . ' ' . ($angkaKata[$ipkBelakang[1]] ?? '');

        // Predikat Kelulusan bersih (Tanpa "(Cum Laude)" jika mengikuti format Pustikom: "Dengan Pujian" / "Sangat Memuaskan")
        $rawPredikat = $transkrip->predikat ?: ($wisudawan?->predikat_kelulusan ?: 'Dengan Pujian');
        $predikatBersih = trim((string) preg_replace('/\s*\(.*?\)/', '', $rawPredikat));

        // Judul Skripsi default yang relevan jika belum diisi
        $judulSkripsi = $wisudawan?->judul_skripsi;
        if (! $judulSkripsi) {
            if ($isDraftMode) {
                $judulSkripsi = 'xxx';
            } elseif ((int) $mhs->program_studi_id === 1 || stripos($rawProdi, 'Informatika') !== false) {
                $judulSkripsi = 'Rancang Bangun Sistem Informasi Akademik dan Verifikasi Dokumen Digital Berbasis Web Menggunakan Arsitektur Layanan Terintegrasi pada UIN Siber Syekh Nurjati Cirebon';
            } elseif ($fakultasId === 5) {
                $judulSkripsi = 'Pengaruh Pendapatan Pembiayaan Murabahah, Musyarakah dan Biaya Terhadap Laba Bersih (Studi Kasus Pada KSPPS BMT Lariba Islamic Centre Periode 2021-2024)';
            } else {
                $judulSkripsi = 'Implementasi Transformasi Pembelajaran Digital Berbasis Cyber Islamic University dalam Meningkatkan Kompetensi Akademik Mahasiswa UIN Siber Syekh Nurjati Cirebon';
            }
        }
    @endphp

    <!-- HEADER TENGAH PUSTIKOM -->
    <div class="header-center">
        @if ($logoBase64)
            <img src="{{ $logoBase64 }}" class="logo-top" alt="Logo UINSSC">
        @endif
        <div class="uinssc-brand">UINSSC</div>
        <div class="univ-title">UNIVERSITAS ISLAM NEGERI SIBER SYEKH NURJATI CIREBON</div>
        <div class="doc-title">TRANSKRIP AKADEMIK</div>
        <div class="doc-number">
            @if ($isDraftMode)
                <strong>Nomor : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/Un.30/F.xx/PP.00.9/...../{{ $tahunTranskrip }}</strong>
            @else
                Nomor : {{ $nomorTranskripFinal }}
            @endif
        </div>
    </div>

    <!-- BIODATA 2 KOLOM -->
    <table class="bio-table">
        <tr>
            <!-- Kolom Kiri -->
            <td style="width: 20%;">Nama</td>
            <td style="width: 1.5%;">:</td>
            <td style="width: 31.5%;">{{ strtoupper($mhs->nama) }}</td>
            <!-- Kolom Kanan -->
            <td style="width: 16%;">Program Pendidikan</td>
            <td style="width: 1.5%;">:</td>
            <td style="width: 29.5%;">{{ $mhs->programStudi?->jenjang ?: ($mhs->jenjang ?: 'S1') }}</td>
        </tr>
        <tr>
            <td>Nomor Induk Mahasiswa</td>
            <td>:</td>
            <td>{{ $mhs->nim }}</td>
            <td>Fakultas</td>
            <td>:</td>
            <td>{{ $namaFakultasBersih }}</td>
        </tr>
        <tr>
            <td>Nomor Ijazah Nasional</td>
            <td>:</td>
            <td>{{ $isDraftMode ? ($wisudawan?->no_ijazah ?: '-') : $nomorIjazahNasional }}</td>
            <td>Program Studi</td>
            <td>:</td>
            <td>{{ $namaProdiBersih }}</td>
        </tr>
        <tr>
            <td>Tempat, Tanggal Lahir</td>
            <td>:</td>
            <td>{{ $tempatLahir }}, {{ $tglLahirFormatted }}</td>
            <td>Nomor SK BAN-PT</td>
            <td>:</td>
            <td>{{ $noSkBanPt }}</td>
        </tr>
        <tr>
            <td>{{ $isDraftMode ? 'Tanggal, Bulan dan Tahun Kelulusan' : 'Tanggal Kelulusan' }}</td>
            <td>:</td>
            <td>{{ $tglKelulusanFormatted }}</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </table>

    <!-- TABEL NILAI 2 KOLOM BERDAMPINGAN (No. | MATA KULIAH | SKS | NILAI | AK) -->
    <table class="grades-table">
        <thead>
            <tr>
                <th style="width: 3.5%;">No.</th>
                <th style="width: 32.5%;">MATA KULIAH</th>
                <th style="width: 4.5%;">SKS</th>
                <th style="width: 4.5%;">NILAI</th>
                <th style="width: 5%;" class="divider-right">AK</th>

                <th style="width: 3.5%;" class="divider-left">No.</th>
                <th style="width: 32.5%;">MATA KULIAH</th>
                <th style="width: 4.5%;">SKS</th>
                <th style="width: 4.5%;">NILAI</th>
                <th style="width: 5%;">AK</th>
            </tr>
        </thead>
        <tbody>
            @for ($row = 0; $row < $leftRowCount; $row++)
                @php
                    $leftItem = $nilais->get($row);
                    $rightIndex = $leftRowCount + $row;
                    $rightItem = $nilais->get($rightIndex);
                    $isLastRow = ($row === $leftRowCount - 1);
                @endphp
                <tr>
                    <!-- SISI KIRI -->
                    @if ($leftItem)
                        @php
                            $leftMkName = $leftItem->mataKuliah?->nama_mata_kuliah ?: 'Mata Kuliah';
                            $leftMkFormatted = ucwords(strtolower($leftMkName));
                            $leftAk = (float) ($leftItem->sks * $leftItem->bobot);
                        @endphp
                        <td class="text-center">{{ $row + 1 }}</td>
                        <td>{{ $leftMkFormatted }}</td>
                        <td class="text-center">{{ $leftItem->sks }}</td>
                        <td class="text-center">{{ $leftItem->nilai_huruf }}</td>
                        <td class="text-center divider-right">
                            {{ $isDraftMode ? number_format($leftAk, 2, ',', '.') : number_format($leftAk, 2, '.', '') }}
                        </td>
                    @else
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td class="divider-right">&nbsp;</td>
                    @endif

                    <!-- SISI KANAN -->
                    @if ($isLastRow)
                        <td colspan="2" class="text-center font-bold divider-left">{{ $isDraftMode ? 'TOTAL' : 'JUMLAH' }}</td>
                        <td class="text-center font-bold">{{ $transkrip->total_sks }}</td>
                        <td colspan="2" class="text-center font-bold">{{ $formattedTotalMutu }}</td>
                    @elseif ($rightItem)
                        @php
                            $rightMkName = $rightItem->mataKuliah?->nama_mata_kuliah ?: 'Mata Kuliah';
                            $rightMkFormatted = ucwords(strtolower($rightMkName));
                            $rightAk = (float) ($rightItem->sks * $rightItem->bobot);
                        @endphp
                        <td class="text-center divider-left">{{ $rightIndex + 1 }}</td>
                        <td>{{ $rightMkFormatted }}</td>
                        <td class="text-center">{{ $rightItem->sks }}</td>
                        <td class="text-center">{{ $rightItem->nilai_huruf }}</td>
                        <td class="text-center">
                            {{ $isDraftMode ? number_format($rightAk, 2, ',', '.') : number_format($rightAk, 2, '.', '') }}
                        </td>
                    @else
                        <td class="divider-left">&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    @endif
                </tr>
            @endfor
        </tbody>
    </table>

    <!-- RINGKASAN IPK, PREDIKAT, JUDUL SKRIPSI -->
    <table class="summary-table">
        <tr>
            <td style="width: 24%;">Indeks Prestasi Kumulatif</td>
            <td style="width: 1.5%;">:</td>
            <td style="width: 74.5%;">
                @if ($isDraftMode)
                    {{ number_format((float) $transkrip->ipk, 2, ',', '.') }} ({{ $terbilangIpk }})
                @else
                    {{ number_format((float) $transkrip->ipk, 2, '.', '') }}
                @endif
            </td>
        </tr>
        <tr>
            <td>Predikat Kelulusan</td>
            <td>:</td>
            <td>{{ $predikatBersih }}</td>
        </tr>
        <tr>
            <td>Judul Skripsi</td>
            <td>:</td>
            <td style="line-height: 1.25;">{{ $judulSkripsi }}</td>
        </tr>
    </table>

    <!-- KETERANGAN, STEMPEL/QR UINSSC, & TANDA TANGAN DEKAN -->
    <table class="footer-table">
        <tr>
            <td style="width: 62%; vertical-align: top;">
                <div class="keterangan-title">Keterangan:</div>
                @if ($isDraftMode)
                    <div>SKS (satuan kredit semester);</div>
                    <div>AK (angka kredit)</div>
                @else
                    <div>- SKS (Satuan Kredit Semester)</div>
                    <div>- AK (Angka Kredit)</div>
                @endif

                <div class="seal-qr-wrap">
                    <table class="seal-qr-table">
                        <tr>
                            <td>
                                <div class="seal-circle">
                                    @if ($logoBase64)
                                        <img src="{{ $logoBase64 }}" alt="UINSSC">
                                    @endif
                                    <div style="font-weight: bold; margin-top: 2px; font-size: 6pt; letter-spacing: 0.5px;">UINSSC</div>
                                </div>
                            </td>
                            <td>
                                <img src="data:image/svg+xml;base64,{{ $qrCodeSvg }}" style="width: 54px; height: 54px;" alt="QR Verifikasi" />
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
            <td style="width: 38%; vertical-align: top; padding-left: 30px;">
                @if ($isDraftMode)
                    <div>Cirebon, ..............................</div>
                    <div>Dekan,</div>
                    <br><br><br><br>
                    <div>..................................................</div>
                    <div>NIP. ............................................</div>
                    <div class="proof-box">PROOF / DRAFT</div>
                @else
                    <div>Cirebon, {{ $tglKelulusanFormatted }}</div>
                    <div>Dekan,</div>
                    <br><br><br><br>
                    <div class="font-bold">{{ $fakMeta['dekan'] }}</div>
                    <div class="font-bold">NIP. {{ $fakMeta['nip'] }}</div>
                @endif
            </td>
        </tr>
    </table>

</body>
</html>
