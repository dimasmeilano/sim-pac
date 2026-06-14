<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>LPJ - {{ $programKerja->nama }}</title>
    <style>
        @page {
            margin: 2.5cm 2cm 2cm 2.5cm;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #000;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .font-weight-bold {
            font-weight: bold;
        }

        .uppercase {
            text-transform: uppercase;
        }

        .page-break {
            page-break-before: always;
        }

        .kop-teks {
            font-size: 14pt;
            font-weight: bold;
            line-height: 1.2;
        }

        .judul-lpj {
            font-size: 16pt;
            font-weight: bold;
            margin-top: 50px;
            line-height: 1.3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .table-border th,
        .table-border td {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: top;
        }

        .table-border th {
            background-color: #e6e6e6;
            text-align: center;
            font-weight: bold;
        }

        .table-ttd {
            width: 100%;
            margin-top: 40px;
            text-align: center;
            page-break-inside: avoid;
        }

        .table-ttd td {
            width: 50%;
            padding-bottom: 80px;
            vertical-align: top;
        }

        .bab-title {
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .sub-isi {
            text-align: justify;
        }

        /* Untuk konten TinyMCE agar marginnya rapi */
        .tinymce-content p {
            margin-top: 0;
            margin-bottom: 10px;
        }

        .tinymce-content ul,
        .tinymce-content ol {
            margin-top: 0;
            margin-bottom: 10px;
            padding-left: 20px;
        }
    </style>
</head>

<body>

    {{-- COVER --}}
    <div class="text-center" style="margin-top: 100px;">
        <div class="judul-lpj">
            LAPORAN KEGIATAN<br>
            <span class="uppercase">{{ $programKerja->nama }}</span><br>
            TAHUN {{ $programKerja->tgl_mulai ? $programKerja->tgl_mulai->format('Y') : date('Y') }}
        </div>
        <div style="margin-top: 80px; margin-bottom: 80px;">
            <h1 style="font-size: 80px; margin:0; color: #1e7e34;">❁</h1>
        </div>
        <div class="kop-teks uppercase">
            PIMPINAN RANTING<br>IPNU IPPNU<br>{{ $programKerja->organization->nama ?? 'DESA BANTRUNG' }}
        </div>
    </div>

    {{-- LEMBAR PENGESAHAN --}}
    <div class="page-break"></div>
    <div class="text-center kop-teks" style="margin-bottom: 40px;">LEMBAR PENGESAHAN<br>LAPORAN KEGIATAN</div>
    <table class="table-ttd">
        <tr>
            <td>Ketua Panitia<br><br><br><br><strong><u>{{ strtoupper($lpj->nama_ketua_panitia) }}</u></strong></td>
            <td>Sekretaris Panitia<br><br><br><br><strong><u>{{ strtoupper($lpj->nama_sekretaris) }}</u></strong></td>
        </tr>
    </table>
    <div class="text-center" style="margin-top: 20px; margin-bottom: 20px;">Mengetahui,</div>
    <table class="table-ttd" style="margin-top: 0;">
        <tr>
            <td>Ketua IPNU<br><br><br><br><strong><u>(.........................................)</u></strong></td>
            <td>Ketua IPPNU<br><br><br><br><strong><u>(.........................................)</u></strong></td>
        </tr>
    </table>

    {{-- BAB PENDAHULUAN & ISI --}}
    <div class="page-break"></div>
    <div class="text-center kop-teks uppercase mb-4">LAPORAN KEGIATAN<br>{{ $programKerja->nama }}</div>

    <div class="bab-title">1. Latar Belakang</div>
    <div class="sub-isi tinymce-content">{!! $lpj->latar_belakang !!}</div>

    <div class="bab-title">2. Nama dan Tema Kegiatan</div>
    <div class="sub-isi">
        Nama Kegiatan: {{ $programKerja->nama }}<br>
        Tema Kegiatan: "{{ $lpj->tema_kegiatan }}"
    </div>

    <div class="bab-title">3. Dasar Pelaksanaan</div>
    <div class="sub-isi tinymce-content">{!! $lpj->dasar_pelaksanaan !!}</div>

    <div class="bab-title">4. Waktu dan Tempat Kegiatan</div>
    <div class="sub-isi">
        Tanggal : {{ $programKerja->tgl_mulai ? $programKerja->tgl_mulai->translatedFormat('d F Y') : '-' }} s/d
        {{ $programKerja->tgl_selesai ? $programKerja->tgl_selesai->translatedFormat('d F Y') : '-' }}<br>
        Waktu : {{ $lpj->jam_kegiatan }}<br>
        Tempat : {{ $lpj->tempat_kegiatan }}
    </div>

    <div class="bab-title">5. Daftar Peserta</div>
    <div class="sub-isi">Daftar peserta dan kehadiran selengkapnya terlampir sebagaimana <strong>Lampiran I</strong>.
    </div>

    <div class="bab-title">6. Tujuan Kegiatan</div>
    <div class="sub-isi tinymce-content">{!! $lpj->tujuan_kegiatan !!}</div>

    <div class="bab-title">7. Output Kegiatan</div>
    <div class="sub-isi tinymce-content">{!! $lpj->output_kegiatan !!}</div>

    <div class="bab-title">8. Materi Kegiatan</div>
    <div class="sub-isi tinymce-content">{!! $lpj->materi_kegiatan !!}</div>

    <div class="bab-title">9. Susunan Panitia</div>
    <div class="sub-isi">Adapun susunan panitia terlampir di dalam <strong>Lampiran II</strong>.</div>

    <div class="bab-title">10. Susunan Acara</div>
    <div class="sub-isi">Adapun susunan acara terlampir di dalam <strong>Lampiran III</strong>.</div>

    <div class="bab-title">11. Hambatan dan Harapan</div>
    <div class="sub-isi tinymce-content">{!! $lpj->hambatan_harapan !!}</div>

    <div class="bab-title">12. Realisasi Anggaran</div>
    <div class="sub-isi">Untuk realisasi penggunaan dana kegiatan ini, rinciannya terdapat di dalam <strong>Lampiran
            IV</strong>.</div>

    <div class="bab-title">13. Dokumentasi Kegiatan</div>
    <div class="sub-isi">Untuk dokumentasi kegiatan terdapat di dalam <strong>Lampiran V</strong>.</div>

    <div class="bab-title">14. Penutup</div>
    <div class="sub-isi">
        Demikian laporan kegiatan ini kami susun untuk menjelaskan tentang {{ $programKerja->nama }} tahun
        {{ $programKerja->tgl_mulai ? $programKerja->tgl_mulai->format('Y') : date('Y') }} ini. Kami sadar masih banyak
        kekurangan dalam mengemban amanat sebagai panitia kegiatan, karenanya kami mohon maaf yang sebesar-besarnya.
        Semoga laporan kegiatan ini bisa menjadi referensi atau acuan untuk kegiatan selanjutnya. Terima kasih.
    </div>

    {{-- TTD PENUTUP --}}
    @php
        \Carbon\Carbon::setLocale('id');
        // 1. Ambil nama organisasi penuh (misal: "PAC IPNU KEBOMAS" dari kolom 'nama' atau 'name')
        // Silakan sesuaikan 'nama' dengan nama kolom yang benar di tabel organizations Anda
        $nama_organisasi = strtoupper($programKerja->organization->name ?? 'PAC IPNU KEBOMAS');

        // 2. Tentukan Tingkatan berdasarkan singkatan di nama organisasi
        if (str_contains($nama_organisasi, 'PR ') || str_contains($nama_organisasi, 'RANTING')) {
            $teks_pimpinan = 'Pimpinan Ranting';
            $teks_label = 'Ranting';
        } elseif (str_contains($nama_organisasi, 'PK ') || str_contains($nama_organisasi, 'KOMISARIAT')) {
            $teks_pimpinan = 'Pimpinan Komisariat';
            $teks_label = 'Komisariat';
        } else {
            // Default jika mendeteksi PAC atau tidak terdeteksi keduanya
            $teks_pimpinan = 'Pimpinan Anak Cabang';
            $teks_label = 'Kecamatan';
        }

        // 3. Ekstrak Nama Wilayah dengan membuang atribut organisasi
        // Kata-kata di bawah ini akan dihapus dari string
        $kata_buang = [
            'PAC',
            'PR',
            'PK',
            'IPNU-IPPNU',
            'IPNU',
            'IPPNU',
            'PIMPINAN ANAK CABANG',
            'PIMPINAN RANTING',
            'PIMPINAN KOMISARIAT',
            '-',
        ];

        // Ganti kata-kata di atas dengan string kosong, lalu hilangkan spasi berlebih di awal/akhir
        $nama_wilayah = trim(str_replace($kata_buang, '', $nama_organisasi));

        // Rapikan kapitalisasi hurufnya (contoh: "KEBOMAS" menjadi "Kebomas")
        $nama_wilayah = ucwords(strtolower($nama_wilayah));
    @endphp

    <div style="page-break-inside: avoid; margin-top: 40px; font-family: 'Times New Roman', serif;">

        <table style="width: 100%; border: none; margin-bottom: 10px;">
            <tr>
                <td style="width: 60%; border: none;"></td>
                <td style="width: 40%; text-align: center; border: none;">
                    {{ $nama_wilayah }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
                </td>
            </tr>
        </table>

        <div style="text-align: center; font-weight: bold; margin-bottom: 30px; line-height: 1.3;">
            Panitia Pelaksana {{ $programKerja->nama ?? 'Kegiatan' }}<br>
            {{ $teks_pimpinan }}<br>
            Ikatan Pelajar Nahdlatul Ulama<br>
            Ikatan Pelajar Putri Nahdlatul Ulama<br>
            {{ $teks_label }} {{ $nama_wilayah }}
        </div>

        <table style="width: 100%; text-align: center; border-collapse: collapse; border: none;">
            <tr>
                <td style="width: 50%; padding: 0; border: none;">Ketua Panitia</td>
                <td style="width: 50%; padding: 0; border: none;">Sekretaris Panitia</td>
            </tr>
            <tr>
                <td style="height: 80px; border: none;"></td>
                <td style="height: 80px; border: none;"></td>
            </tr>
            <tr>
                <td style="padding: 0; border: none;">
                    <b><u>{{ $lpj->ketua_panitia ?? '( Nama Ketua Panitia )' }}</u></b>
                </td>
                <td style="padding: 0; border: none;">
                    <b><u>{{ $lpj->sekretaris_panitia ?? '( Nama Sekretaris )' }}</u></b>
                </td>
            </tr>
        </table>

    </div>


    {{-- LAMPIRAN 1: ABSENSI --}}
    <div class="page-break"></div>
    <div class="text-center kop-teks mb-4">LAMPIRAN I<br>DAFTAR HADIR KEGIATAN</div>
    @foreach ($programKerja->kegiatans ?? [] as $keg)
        <div style="font-weight: bold; margin-bottom: 5px;">{{ $keg->nama }}
            ({{ $keg->tgl_mulai ? $keg->tgl_mulai->format('d/m/Y') : '-' }})
        </div>
        <table class="table-border" style="margin-bottom: 20px;">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th>Nama Lengkap</th>
                    <th>Delegasi</th>
                    <th width="20%">Waktu Hadir</th>
                </tr>
            </thead>
            <tbody>
                @forelse($keg->absensis ?? [] as $key => $absen)
                    <tr>
                        <td class="text-center">{{ $key + 1 }}</td>
                        <td>{{ $absen->user->name ?? $absen->nama_peserta }}</td>
                        <td>{{ $absen->delegasi ?? '-' }}</td>
                        <td class="text-center">{{ $absen->waktu_absen ? $absen->waktu_absen->format('H:i') : '-' }}
                            WIB</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">Belum ada absensi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endforeach


    {{-- LAMPIRAN 2 & 3: PANITIA DAN ACARA (GAMBAR UPLOAD) --}}
    <div class="page-break"></div>
    <div class="text-center kop-teks mb-4">LAMPIRAN II<br>SUSUNAN PANITIA</div>
    @if ($lpj->file_lampiran_panitia)
        <div class="text-center"><img src="{{ storage_path('app/public/' . $lpj->file_lampiran_panitia) }}"
                style="max-width: 100%; border: 1px solid #000;"></div>
    @else
        <div class="text-center text-muted"><em>Lampiran panitia tidak diunggah.</em></div>
    @endif

    <div class="page-break"></div>
    <div class="text-center kop-teks mb-4">LAMPIRAN III<br>SUSUNAN ACARA</div>
    @if ($lpj->file_lampiran_acara)
        <div class="text-center"><img src="{{ storage_path('app/public/' . $lpj->file_lampiran_acara) }}"
                style="max-width: 100%; border: 1px solid #000;"></div>
    @else
        <div class="text-center text-muted"><em>Lampiran acara tidak diunggah.</em></div>
    @endif


    {{-- LAMPIRAN 4: KEUANGAN --}}
    <div class="page-break"></div>
    <div class="text-center kop-teks mb-4">LAMPIRAN IV<br>REALISASI ANGGARAN DANA</div>
    <table class="table-border">
        <thead>
            <tr>
                <th width="15%">Tanggal</th>
                <th width="40%">Keterangan</th>
                <th width="20%">Masuk (Rp)</th>
                <th width="20%">Keluar (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($programKerja->transaksis?->where('status_validasi', 'disetujui') ?? [] as $trx)
                <tr>
                    <td class="text-center">{{ $trx->tanggal ? $trx->tanggal->format('d/m/Y') : '-' }}</td>
                    <td>{{ $trx->keterangan }}</td>
                    <td class="text-right">
                        {{ $trx->jenis == 'masuk' ? number_format($trx->nominal, 0, ',', '.') : '-' }}</td>
                    <td class="text-right">
                        {{ $trx->jenis == 'keluar' ? number_format($trx->nominal, 0, ',', '.') : '-' }}</td>
                </tr>
            @endforeach
            <tr>
                <th colspan="2" class="text-right">TOTAL SALDO AKHIR</th>
                <th colspan="2" class="text-center">{{ number_format($saldo_akhir, 0, ',', '.') }}</th>
            </tr>
        </tbody>
    </table>


    {{-- LAMPIRAN 5: DOKUMENTASI (4 FOTO) --}}
    <div class="page-break"></div>
    <div class="text-center kop-teks mb-4">LAMPIRAN V<br>DOKUMENTASI KEGIATAN</div>
    <div class="row mt-3">
        @if ($kegiatan && $kegiatan->dokumentasi)
            @foreach ($kegiatan->dokumentasi->take(4) as $foto)
            @endforeach
        @else
            <p>Belum ada dokumentasi.</p>
        @endif
    </div>

</body>

</html>
