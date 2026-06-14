<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Ringkasan Kegiatan - {{ $programKerja->nama }}</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
        }

        .text-center {
            text-align: center;
        }

        .font-weight-bold {
            font-weight: bold;
        }

        .mt-4 {
            margin-top: 20px;
        }

        .mb-2 {
            margin-bottom: 10px;
        }

        /* Table Styles */
        .table-details {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .table-details th,
        .table-details td {
            padding: 6px 4px;
            vertical-align: top;
        }

        .table-details th {
            width: 35%;
            text-align: left;
            font-weight: normal;
        }

        /* Image Box */
        .img-box {
            text-align: center;
            margin-top: 10px;
        }

        .img-box img {
            max-width: 100%;
            max-height: 350px;
            border: 2px solid #333;
            padding: 3px;
            object-fit: cover;
        }

        .img-placeholder {
            border: 1px dashed #999;
            padding: 60px;
            color: #777;
            text-align: center;
            font-style: italic;
        }
    </style>
</head>

<body>

    @php
        // Setting Bahasa Carbon
        \Carbon\Carbon::setLocale('id');

        // Logika Kop Otomatis
        $nama_organisasi = strtoupper($programKerja->organization->nama ?? 'PAC IPNU KEBOMAS');

        if (str_contains($nama_organisasi, 'PR ') || str_contains($nama_organisasi, 'RANTING')) {
            $teks_pimpinan = 'Pimpinan Ranting';
            $teks_label = 'Ranting';
        } elseif (str_contains($nama_organisasi, 'PK ') || str_contains($nama_organisasi, 'KOMISARIAT')) {
            $teks_pimpinan = 'Pimpinan Komisariat';
            $teks_label = 'Komisariat';
        } else {
            $teks_pimpinan = 'Pimpinan Anak Cabang';
            $teks_label = 'Kecamatan';
        }

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
        $nama_wilayah = ucwords(strtolower(trim(str_replace($kata_buang, '', $nama_organisasi))));
    @endphp

    <div class="text-center font-weight-bold"
        style="font-size: 14pt; text-decoration: underline; text-transform: uppercase;">
        RINGKASAN PELAKSANAAN KEGIATAN
    </div>

    <table class="table-details">
        <tr>
            <th>Nama Kegiatan</th>
            <td>: <b>{{ $programKerja->nama }}</b></td>
        </tr>
        <tr>
            <th>Tema Kegiatan</th>
            <td>: {{ $lpj->tema_kegiatan ?? '-' }}</td>
        </tr>
        <tr>
            <th>Tanggal dan Waktu Pelaksanaan</th>
            <td>:
                {{ \Carbon\Carbon::parse($programKerja->tgl_mulai)->translatedFormat('d F Y') }}
                @if ($programKerja->tgl_selesai && $programKerja->tgl_selesai != $programKerja->tgl_mulai)
                    s/d {{ \Carbon\Carbon::parse($programKerja->tgl_selesai)->translatedFormat('d F Y') }}
                @endif
                (Pukul {{ $lpj->jam_kegiatan ?? 'Selesai' }})
            </td>
        </tr>
        <tr>
            <th>Tempat Kegiatan</th>
            <td>: {{ $lpj->tempat_kegiatan ?? '-' }}</td>
        </tr>
        <tr>
            <th>Total Estimasi Anggaran</th>
            <td>: Rp {{ number_format($programKerja->estimasi_anggaran ?? 0, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Total Realisasi Anggaran</th>
            <td>: <span style="color: red; font-weight: bold;">Rp {{ number_format($pengeluaran, 0, ',', '.') }}</span>
            </td>
        </tr>
        <tr>
            <th>Nama Penanggung Jawab</th>
            <td>: <b>{{ $lpj->nama_ketua_panitia ?? '-' }}</b></td>
        </tr>
        <tr>
            <th>Berapa orang yang hadir</th>
            <td>: {{ $totalPeserta ?? 0 }} Orang</td>
        </tr>
    </table>

    <div class="mt-4 font-weight-bold mb-2">Dokumentasi Kegiatan:</div>
    <div class="img-box">
        @php
            $foto_dokumentasi = null;
            if ($lpj->foto_dokumentasi_terpilih) {
                // Decode JSON jika disimpan dalam format JSON
                $foto_array = is_string($lpj->foto_dokumentasi_terpilih)
                    ? json_decode($lpj->foto_dokumentasi_terpilih, true)
                    : $lpj->foto_dokumentasi_terpilih;

                if (is_array($foto_array) && count($foto_array) > 0) {
                    $foto_dokumentasi = $foto_array[0];
                } elseif (is_string($lpj->foto_dokumentasi_terpilih) && !empty($lpj->foto_dokumentasi_terpilih)) {
                    $foto_dokumentasi = $lpj->foto_dokumentasi_terpilih;
                }
            }
        @endphp

        @if ($foto_dokumentasi)
            <img src="{{ public_path('storage/' . $foto_dokumentasi) }}" alt="Dokumentasi Kegiatan">
        @else
            <div class="img-placeholder">
                [ Belum ada foto dokumentasi yang diunggah untuk kegiatan ini ]
            </div>
        @endif
    </div>
</body>

</html>
