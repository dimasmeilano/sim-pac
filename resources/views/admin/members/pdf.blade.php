<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Data Anggota</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 11px;
        }

        .text-center {
            text-align: center;
        }

        .text-bold {
            font-weight: bold;
        }

        .text-uppercase {
            text-transform: uppercase;
        }

        /* Styling Tabel Utama */
        .table-data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .table-data th,
        .table-data td {
            border: 1px solid #000;
            padding: 5px;
        }

        .table-data th {
            background-color: #28a745;
            color: white;
        }

        /* Kop Surat */
        .kop-surat {
            border-bottom: 3px solid #000;
            margin-bottom: 15px;
            padding-bottom: 10px;
        }

        .kop-surat h2,
        .kop-surat h3,
        .kop-surat p {
            margin: 0;
        }

        .kop-image {
            width: 100%;
            max-height: 120px;
            object-fit: contain;
        }

        /* Tanda Tangan */
        .ttd-container {
            width: 100%;
            margin-top: 40px;
        }

        .ttd-box {
            width: 300px;
            float: right;
            text-align: center;
        }

        .ttd-box img {
            max-width: 120px;
            max-height: 80px;
        }
    </style>
</head>

<body>

    <div class="kop-surat text-center">
        @if ($org && $org->kop_surat_bersama)
            {{-- DOMPDF mewajibkan penggunaan public_path() untuk memanggil gambar lokal --}}
            <img src="{{ public_path('storage/' . $org->kop_surat_bersama) }}" class="kop-image">
        @else
            <h2>PIMPINAN {{ strtoupper($org->type ?? 'RANTING / ANAK CABANG') }}</h2>
            <h2 class="text-uppercase" style="color: #28a745;">IKATAN PELAJAR NAHDLATUL ULAMA</h2>
            <h2 class="text-uppercase" style="color: #28a745;">IKATAN PELAJAR PUTRI NAHDLATUL ULAMA</h2>
            <p>{{ $org->alamat ?? 'Jalan Raya Sekretariat No. 1' }} | Email: {{ $org->email ?? '-' }}</p>
        @endif
    </div>

    <div class="text-center text-bold text-uppercase">
        <h3 style="text-decoration: underline; margin-bottom: 5px;">DAFTAR SUSUNAN PENGURUS DAN ANGGOTA</h3>
        <p>PERIODE: {{ $org->periode ?? '........ / ........' }}</p>
    </div>

    <table class="table-data text-center">
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="15%">Nama Lengkap</th>
                <th width="12%">Asal Organisasi</th>
                <th width="12%">NIK</th>
                <th width="10%">No. HP</th>
                <th width="10%">Tempat Lahir</th>
                <th width="10%">Tgl Lahir</th>
                <th width="5%">L/P</th>
                <th width="8%">Pendidikan</th>
                <th width="7%">Status</th>
                <th width="8%">Bergabung</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($members as $key => $member)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td style="text-align: left;">{{ $member->name }}</td>
                    <td>{{ $member->organization->name ?? '-' }}</td>
                    <td>{{ $member->nik ?? '-' }}</td>
                    <td>{{ $member->no_hp ?? '-' }}</td>
                    <td>{{ $member->tempat_lahir ?? '-' }}</td>
                    <td>{{ $member->tanggal_lahir ? date('d/m/Y', strtotime($member->tanggal_lahir)) : '-' }}</td>
                    <td>{{ $member->jk ?? '-' }}</td>
                    <td>{{ $member->pendidikan ?? '-' }}</td>
                    <td>{{ ucfirst($member->status_anggota) }}</td>
                    <td>{{ $member->tgl_bergabung ? date('d/m/Y', strtotime($member->tgl_bergabung)) : '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="width: 100%; margin-top: 30px;">
        {{-- Keterangan Waktu & Tempat (Rata Kanan) --}}
        <div style="text-align: right; width: 100%; margin-bottom: 20px;">
            <p style="margin: 0;">Ditetapkan di : {{ $lokasi }}</p>
            <p style="margin: 0;">Pada Tanggal : {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
        </div>

        {{-- Judul Pimpinan (Rata Tengah) --}}
        <div class="text-center text-bold" style="margin-bottom: 30px;">
            PIMPINAN {{ strtoupper($org->type ?? 'RANTING') }}<br>
            IKATAN PELAJAR NAHDLATUL ULAMA <br>
        </div>

        {{-- Tabel Tanda Tangan Berdampingan (Ketua Kiri, Sekretaris Kanan) --}}
        <table style="width: 100%; text-align: center; border: none; font-size: 12px;">
            <tr>
                <td style="width: 50%; border: none; padding: 0;">Ketua,</td>
                <td style="width: 50%; border: none; padding: 0;">Sekretaris,</td>
            </tr>
            <tr>
                {{-- Area TTD Ketua + Stempel --}}
                <td style="height: 100px; border: none; vertical-align: bottom; position: relative;">
                    {{-- Stempel Organisasi (Agak digeser ke kanan agar menabrak TTD Sekretaris/Ketua) --}}
                    @if ($org && $org->stempel)
                        <img src="{{ public_path('storage/' . $org->stempel) }}"
                            style="position: absolute; right: 20px; top: -10px; width: 110px; opacity: 0.85; z-index: -1;">
                    @endif

                    {{-- Tanda Tangan Digital Ketua --}}
                    @if ($ketua && $ketua->ttd)
                        <img src="{{ public_path('storage/' . $ketua->ttd) }}" style="height: 70px;">
                    @else
                        <br><br><br>
                    @endif
                </td>

                {{-- Area TTD Sekretaris --}}
                <td style="height: 100px; border: none; vertical-align: bottom;">
                    {{-- Tanda Tangan Digital Sekretaris --}}
                    @if ($sekretaris && $sekretaris->ttd)
                        <img src="{{ public_path('storage/' . $sekretaris->ttd) }}" style="height: 70px;">
                    @else
                        <br><br><br>
                    @endif
                </td>
            </tr>
            <tr>
                {{-- Generate Nama Otomatis dari Database --}}
                <td style="border: none; padding: 0;">
                    <span class="text-bold text-uppercase" style="text-decoration: underline;">
                        {{ $ketua ? $ketua->name : '.........................................' }}
                    </span>
                </td>
                <td style="border: none; padding: 0;">
                    <span class="text-bold text-uppercase" style="text-decoration: underline;">
                        {{ $sekretaris ? $sekretaris->name : '.........................................' }}
                    </span>
                </td>
            </tr>
            <tr>
                {{-- Gunakan ternary aman agar tidak error jika Ketua/Sekretaris kosong --}}
                <td style="border: none; padding: 0;">NIA: {{ $ketua ? $ketua->nik : '-' }}</td>
                <td style="border: none; padding: 0;">NIA: {{ $sekretaris ? $sekretaris->nik : '-' }}</td>
            </tr>
        </table>
    </div>

</body>

</html>
