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
    <table class="table-ttd" style="margin-top: 50px;">
        <tr>
            <td>Ketua Panitia<br><br><br><br><strong><u>{{ strtoupper($lpj->nama_ketua_panitia) }}</u></strong></td>
            <td>Sekretaris Panitia<br><br><br><br><strong><u>{{ strtoupper($lpj->nama_sekretaris) }}</u></strong></td>
        </tr>
    </table>


    {{-- LAMPIRAN 1: ABSENSI --}}
    <div class="page-break"></div>
    <div class="text-center kop-teks mb-4">LAMPIRAN I<br>DAFTAR HADIR KEGIATAN</div>
    @foreach ($programKerja->kegiatans ?? [] as $keg)
        <div style="font-weight: bold; margin-bottom: 5px;">{{ $keg->nama }}
            ({{ $keg->tgl_mulai ? $keg->tgl_mulai->format('d/m/Y') : '-' }})</div>
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
    <div class="text-center">
        @if ($lpj->foto_dokumentasi_terpilih && is_array($lpj->foto_dokumentasi_terpilih))
            @foreach ($lpj->foto_dokumentasi_terpilih as $path)
                @php $imagePath = storage_path('app/public/' . $path); @endphp
                @if (file_exists($imagePath))
                    <div style="display: inline-block; width: 45%; margin: 2%; border: 1px solid #000; padding: 5px;">
                        <img src="{{ $imagePath }}" style="width: 100%; height: 200px; object-fit: cover;">
                    </div>
                @endif
            @endforeach
        @else
            <div style="margin-top: 50px; color: #666;"><em>Tidak ada foto dokumentasi terpilih.</em></div>
        @endif
    </div>

</body>

</html>
