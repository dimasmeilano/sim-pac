<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>LPJ - {{ $programKerja->nama ?? 'Kegiatan' }}</title>
    <style>
        /* Pengaturan Kertas & Font Standar Makalah */
        @page {
            margin: 2.5cm 2cm 2cm 2.5cm;
        }

        /* Kiri lebih lebar untuk dijilid */
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #000;
        }

        /* Utilitas Teks */
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

        /* Kop & Judul */
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

        .sekretariat {
            font-size: 10pt;
            border-top: 2px solid #000;
            padding-top: 5px;
            margin-top: 10px;
        }

        /* Pengaturan Tabel Data */
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

        /* Tabel Tanda Tangan (TTD) */
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

        /* Galeri Foto */
        .galeri-container {
            text-align: center;
            margin-top: 20px;
        }

        .galeri-item {
            display: inline-block;
            width: 45%;
            margin: 2%;
            border: 1px solid #000;
            padding: 5px;
            background: #fff;
        }

        .galeri-img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .galeri-caption {
            font-size: 10pt;
            margin-top: 5px;
            font-style: italic;
        }

        /* Bab & Sub-bab */
        .bab-title {
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 5px;
        }

        .sub-isi {
            padding-left: 20px;
            text-align: justify;
        }
    </style>
</head>

<body>

    {{-- ==========================================
         HALAMAN 1: COVER DEPAN
         ========================================== --}}
    <div class="text-center" style="margin-top: 100px;">
        <div class="judul-lpj">
            LAPORAN KEGIATAN<br>
            <span class="uppercase">{{ $programKerja->nama ?? 'PROGRAM KERJA' }}</span><br>
            TAHUN {{ $programKerja->tgl_mulai ? $programKerja->tgl_mulai->format('Y') : date('Y') }}
        </div>

        <div style="margin-top: 80px; margin-bottom: 80px;">
            {{-- LOGO ORGANISASI (Bisa diganti dengan tag <img> jika sudah ada file logonya di server) --}}
            <h1 style="font-size: 80px; margin:0; color: #1e7e34;">❁</h1>
        </div>

        <div class="kop-teks uppercase">
            PIMPINAN RANTING<br>
            IKATAN PELAJAR NAHDLATUL ULAMA'<br>
            IKATAN PELAJAR PUTRI NAHDLATUL ULAMA'<br>
            {{ $programKerja->organization->nama ?? 'DESA BANTRUNG' }}
        </div>
    </div>

    <div class="text-center sekretariat" style="position: absolute; bottom: 0; width: 100%;">
        Sekretariat: {{ $programKerja->organization->alamat ?? 'Alamat Sekretariat Lengkap' }}<br>
        Email: {{ $programKerja->organization->email ?? 'email@organisasi.or.id' }}
    </div>


    {{-- ==========================================
         HALAMAN 2: LEMBAR PENGESAHAN
         ========================================== --}}
    <div class="page-break"></div>
    <div class="text-center kop-teks" style="margin-bottom: 40px;">
        LEMBAR PENGESAHAN<br>
        LAPORAN KEGIATAN<br>
        <span class="uppercase">{{ $programKerja->nama ?? 'PROGRAM KERJA' }}</span><br>
        TAHUN {{ $programKerja->tgl_mulai ? $programKerja->tgl_mulai->format('Y') : date('Y') }}
    </div>

    <table class="table-ttd">
        <tr>
            <td>
                Ketua Panitia<br>
                <br><br><br><br>
                <strong><u>(.........................................)</u></strong>
            </td>
            <td>
                Sekretaris Panitia<br>
                <br><br><br><br>
                <strong><u>(.........................................)</u></strong>
            </td>
        </tr>
    </table>

    <div class="text-center" style="margin-top: 20px; margin-bottom: 20px;">Mengetahui,</div>

    <table class="table-ttd" style="margin-top: 0;">
        <tr>
            <td>
                Ketua IPNU<br>
                <br><br><br><br>
                <strong><u>(.........................................)</u></strong>
            </td>
            <td>
                Ketua IPPNU<br>
                <br><br><br><br>
                <strong><u>(.........................................)</u></strong>
            </td>
        </tr>
    </table>


    {{-- ==========================================
         HALAMAN 3: BAB PENDAHULUAN (DATA OTOMATIS)
         ========================================== --}}
    <div class="page-break"></div>
    <div class="text-center kop-teks uppercase mb-4">
        LAPORAN KEGIATAN<br>
        {{ $programKerja->nama ?? 'PROGRAM KERJA' }}
    </div>

    <div class="bab-title">A. NAMA DAN TEMA KEGIATAN</div>
    <table class="sub-isi" style="width: 100%;">
        <tr>
            <td width="20%">Nama Kegiatan</td>
            <td width="2%">:</td>
            <td>{{ $programKerja->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td>Tema Kegiatan</td>
            <td>:</td>
            <td>"Meningkatkan Khidmah dan Perjuangan Melalui Inovasi Organisasi"</td>
        </tr>
    </table>

    <div class="bab-title">B. WAKTU DAN TEMPAT</div>
    <div class="sub-isi">
        Kegiatan ini telah diselenggarakan pada:<br>
        <table style="width: 100%; margin-top: 5px;">
            <tr>
                <td width="20%">Tanggal</td>
                <td width="2%">:</td>
                <td>{{ $programKerja->tgl_mulai ? $programKerja->tgl_mulai->translatedFormat('d F Y') : '-' }} s/d
                    {{ $programKerja->tgl_selesai ? $programKerja->tgl_selesai->translatedFormat('d F Y') : '-' }}</td>
            </tr>
            <tr>
                <td>Status Pelaksanaan</td>
                <td>:</td>
                <td><strong>{{ strtoupper($programKerja->status_text ?? 'Selesai') }}</strong></td>
            </tr>
        </table>
    </div>

    <div class="bab-title">C. REKAPITULASI KEUANGAN</div>
    <div class="sub-isi">
        Berikut adalah rincian global sirkulasi keuangan kegiatan (Rincian nota terlampir):<br>
        <table class="table-border" style="margin-top: 10px; width: 90%;">
            <tr>
                <th>Keterangan Uraian</th>
                <th>Total (Rp)</th>
            </tr>
            <tr>
                <td>Total Pemasukan Dana (Valid)</td>
                <td class="text-right">{{ number_format($pemasukan ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Pengeluaran Dana (Valid)</td>
                <td class="text-right">{{ number_format($pengeluaran ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th class="text-right">SALDO AKHIR</th>
                <th class="text-right">{{ number_format(($pemasukan ?? 0) - ($pengeluaran ?? 0), 0, ',', '.') }}</th>
            </tr>
        </table>
    </div>


    {{-- ==========================================
         LAMPIRAN I: DAFTAR HADIR (DIGITAL)
         ========================================== --}}
    <div class="page-break"></div>
    <div class="text-center kop-teks">LAMPIRAN I<br>DAFTAR HADIR PESERTA / PANITIA</div>

    @forelse($programKerja->kegiatans ?? [] as $keg)
        <div style="margin-top: 20px; font-weight: bold; margin-bottom: 5px;">
            Agenda: {{ $keg->nama }} ({{ $keg->tgl_mulai ? $keg->tgl_mulai->format('d/m/Y') : '-' }})
        </div>
        <table class="table-border">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="40%">Nama Lengkap</th>
                    <th width="30%">Asal / Delegasi</th>
                    <th width="25%">Kehadiran Digital</th>
                </tr>
            </thead>
            <tbody>
                @forelse($keg->absensis ?? [] as $key => $absen)
                    <tr>
                        <td class="text-center">{{ $key + 1 }}</td>
                        <td>{{ $absen->user->name ?? $absen->nama_peserta }}</td>
                        <td>{{ $absen->delegasi ?? 'Pimpinan Ranting' }}</td>
                        <td class="text-center" style="font-size: 10pt; font-style: italic;">
                            Hadir ({{ $absen->waktu_absen ? $absen->waktu_absen->format('H:i') : '-' }} WIB)
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">Belum ada data absensi masuk untuk agenda ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @empty
        <div class="text-center" style="margin-top: 30px;">Belum ada agenda kegiatan yang dicatat.</div>
    @endforelse


    {{-- ==========================================
         LAMPIRAN II: REKAP ARUS KAS (TRANSAKSI)
         ========================================== --}}
    <div class="page-break"></div>
    <div class="text-center kop-teks">LAMPIRAN II<br>REALISASI ANGGARAN BIAYA</div>

    <div class="bab-title">RINCIAN ARUS KAS</div>
    <table class="table-border">
        <thead>
            <tr>
                <th width="15%">Tanggal</th>
                <th width="40%">Keterangan / Uraian</th>
                <th width="22%">Pemasukan (Rp)</th>
                <th width="23%">Pengeluaran (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php $no_trx = 1; @endphp
            @forelse($programKerja->transaksis?->where('status_validasi', 'disetujui') ?? [] as $trx)
                <tr>
                    <td class="text-center">{{ $trx->tanggal ? $trx->tanggal->format('d/m/Y') : '-' }}</td>
                    <td>{{ $trx->keterangan }}<br><span style="font-size: 9pt; color: #555;">(Oleh:
                            {{ $trx->createdBy->name ?? 'Sistem' }})</span></td>
                    <td class="text-right">
                        {{ $trx->jenis == 'masuk' ? number_format($trx->nominal, 0, ',', '.') : '-' }}</td>
                    <td class="text-right">
                        {{ $trx->jenis == 'keluar' ? number_format($trx->nominal, 0, ',', '.') : '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">Belum ada transaksi keuangan yang disetujui.</td>
                </tr>
            @endforelse
        </tbody>
    </table>


    {{-- ==========================================
         LAMPIRAN III: DOKUMENTASI (AUTO GRID)
         ========================================== --}}
    <div class="page-break"></div>
    <div class="text-center kop-teks">LAMPIRAN III<br>DOKUMENTASI KEGIATAN</div>

    <div class="galeri-container">
        @php $foto_count = 0; @endphp
        @foreach ($programKerja->kegiatans ?? [] as $keg)
            @foreach ($keg->folders ?? [] as $folder)
                @foreach ($folder->galeris ?? [] as $foto)
                    @if (in_array(strtolower(pathinfo($foto->file_path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png']))
                        @php $foto_count++; @endphp
                        <div class="galeri-item">
                            {{-- Memanggil gambar fisik ke PDF --}}
                            @php
                                $imagePath = storage_path('app/public/' . $foto->file_path);
                                // Fallback jika file fisik tidak ketemu (mencegah error PDF)
                                $src = file_exists($imagePath) ? $imagePath : public_path('images/placeholder.jpg');
                            @endphp
                            <img src="{{ $src }}" class="galeri-img" alt="Dokumentasi">
                            <div class="galeri-caption">
                                <strong>{{ $keg->nama }}</strong><br>
                                {{ $foto->keterangan ?? 'Dokumentasi ' . $folder->nama_folder }}
                            </div>
                        </div>
                    @endif
                @endforeach
            @endforeach
        @endforeach

        @if ($foto_count == 0)
            <div style="margin-top: 50px; color: #666;">
                <em>Belum ada foto dokumentasi yang diunggah ke Galeri Workspace.</em>
            </div>
        @endif
    </div>

</body>

</html>
