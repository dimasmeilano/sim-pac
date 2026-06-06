<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Notulensi - {{ $notulensi->agenda }}</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            padding: 2cm;
        }

        .text-center {
            text-align: center;
        }

        .text-justify {
            text-align: justify;
        }

        .font-weight-bold {
            font-weight: bold;
        }

        .judul {
            text-decoration: underline;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .table-info td {
            vertical-align: top;
            padding: 2px 5px;
            border: none;
        }

        .table-border th,
        .table-border td {
            border: 1px solid #000;
            padding: 5px;
        }

        .ttd {
            margin-top: 40px;
            width: 100%;
            page-break-inside: avoid;
        }

        .ttd td {
            text-align: center;
            border: none;
            width: 50%;
        }

        .content-area {
            margin-top: 20px;
            text-align: justify;
        }

        p {
            margin-top: 0;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>

    <div class="text-center mb-4">
        <h3 class="judul">NOTULENSI RAPAT</h3>
        <p class="font-weight-bold">
            {{ strtoupper($notulensi->organization->nama ?? ($notulensi->organization->name ?? 'PAC IPNU IPPNU')) }}</p>
    </div>
    <hr style="border:1px solid black; margin-bottom: 20px;">

    <table class="table-info">
        <tr>
            <td width="25%">Agenda / Topik</td>
            <td width="2%">:</td>
            <td><strong>{{ $notulensi->agenda }}</strong></td>
        </tr>
        <tr>
            <td>Hari, Tanggal</td>
            <td>:</td>
            <td>{{ \Carbon\Carbon::parse($notulensi->tanggal)->translatedFormat('l, d F Y') }}</td>
        </tr>
        <tr>
            <td>Waktu</td>
            <td>:</td>
            <td>{{ $notulensi->waktu_mulai ? date('H:i', strtotime($notulensi->waktu_mulai)) : '-' }} s/d
                {{ $notulensi->waktu_selesai ? date('H:i', strtotime($notulensi->waktu_selesai)) : 'Selesai' }} WIB</td>
        </tr>
        <tr>
            <td>Tempat</td>
            <td>:</td>
            <td>{{ $notulensi->tempat }}</td>
        </tr>
        <tr>
            <td>Pemimpin Rapat</td>
            <td>:</td>
            <td>{{ $notulensi->pemimpin_rapat }}</td>
        </tr>
        <tr>
            <td>Notulis</td>
            <td>:</td>
            <td>{{ $notulensi->notulis->name ?? '-' }}</td>
        </tr>
    </table>

    <div class="content-area">
        <h4>A. ISI PEMBAHASAN</h4>
        {!! $notulensi->pembahasan !!}

        <br>
        <h4>B. KESIMPULAN / HASIL RAPAT</h4>
        {!! $notulensi->kesimpulan ?? '<em>Tidak ada kesimpulan tertulis.</em>' !!}
    </div>

    @if ($notulensi->kegiatan_id && count($absensi) > 0)
        <div style="page-break-before: always;">
            <h4 class="text-center">LAMPIRAN DAFTAR HADIR PESERTA</h4>
            <table class="table-border" style="margin-top:15px;">
                <thead>
                    <tr style="background-color: #e9ecef;">
                        <th width="8%">No</th>
                        <th width="60%">Nama Peserta</th>
                        <th width="32%">Waktu Hadir</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($absensi as $key => $absen)
                        <tr>
                            <td class="text-center">{{ $key + 1 }}</td>
                            <td>{{ $absen->user_id ? $absen->user->name : $absen->nama_peserta }}</td>
                            <td class="text-center">{{ $absen->waktu_absen->format('H:i') }} WIB</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <table class="ttd">
        <tr>
            <td>Pemimpin Rapat,</td>
            <td>Notulis,</td>
        </tr>
        <tr>
            <td colspan="2"><br><br><br><br></td>
        </tr>
        <tr>
            <td><strong><u>{{ $notulensi->pemimpin_rapat }}</u></strong></td>
            <td><strong><u>{{ $notulensi->notulis->name ?? '___________________' }}</u></strong></td>
        </tr>
    </table>

</body>

</html>
