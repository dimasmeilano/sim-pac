<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Biodata Peserta Makesta</title>
    <style>
        @page {
            margin: 15mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        h2 {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
            text-transform: uppercase;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            vertical-align: middle;
        }

        /* Warna Header Hijau Persis Gambar */
        th {
            background-color: #00b050;
            color: #000;
            text-align: center;
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>

<body>
    <h2>BIODATA PESERTA MAKESTA<br><small style="font-size: 14px; font-weight: normal;">{{ $event->tema }} -
            {{ $event->organization->nama ?? 'PAC IPNU IPPNU' }}</small></h2>

    <table>
        <thead>
            <tr>
                <th width="5%">NO</th>
                <th width="20%">NAMA</th>
                <th width="15%">TTL</th>
                <th width="30%">ALAMAT</th>
                <th width="15%">DELEGASI</th>
                <th width="15%">NO. HP</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($event->pesertas as $index => $peserta)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $peserta->nama_lengkap }}</td>
                    <td>{{ $peserta->tempat_lahir }},
                        {{ \Carbon\Carbon::parse($peserta->tgl_lahir)->format('d F Y') }}</td>
                    <td>{{ $peserta->alamat }}</td>
                    <td class="text-center">{{ $peserta->utusan }}</td>
                    <td class="text-center">{{ $peserta->no_wa }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>
