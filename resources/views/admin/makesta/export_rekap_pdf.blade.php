<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Nilai Makesta</title>
    <style>
        @page {
            margin: 10mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
        }

        h2 {
            text-align: center;
            font-weight: bold;
            margin-bottom: 15px;
            text-transform: uppercase;
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 4px;
            vertical-align: middle;
            text-align: center;
        }

        /* Pewarnaan Header sesuai gambar */
        .bg-green {
            background-color: #00ff00;
        }

        .bg-yellow {
            background-color: #ffff00;
        }

        .bg-red {
            background-color: #ff0000;
            color: white;
        }

        .text-left {
            text-align: left;
            padding-left: 5px;
        }
    </style>
</head>

<body>

    <h2>REKAPITULASI NILAI MAKESTA<br>{{ $event->tema }}</h2>

    <table>
        <thead>
            <tr>
                <th rowspan="2" class="bg-green" width="3%">NO</th>
                <th colspan="2" class="bg-yellow">No Sertifikat</th>
                <th rowspan="2" class="bg-green" width="15%">NAMA</th>

                @foreach ($event->materis as $materi)
                    <th colspan="2" class="bg-yellow">{{ strtoupper($materi->nama_materi) }}</th>
                @endforeach

                <th rowspan="2" class="bg-red">JUMLAH<br>NILAI</th>
                <th rowspan="2" class="bg-red">RATA<br>RATA</th>
                <th rowspan="2" class="bg-red">PREDIKAT</th>
            </tr>
            <tr>
                <th class="bg-yellow" width="5%">No Urut</th>
                <th class="bg-yellow" width="8%">Kode Sertifikat</th>

                @foreach ($event->materis as $materi)
                    <th class="bg-yellow">ANGKA</th>
                    <th class="bg-yellow">HURUF</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($event->pesertas as $index => $peserta)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td></td>
                    <td></td>
                    <td class="text-left">{{ $peserta->nama_lengkap }}</td>

                    @php
                        $jumlahNilai = 0;
                        $countMateri = 0;
                    @endphp

                    @foreach ($event->materis as $materi)
                        @php
                            $nilaiData = $materi->nilais->where('makesta_peserta_id', $peserta->id)->first();
                            $angka = $nilaiData->nilai_akhir ?? 0;
                            $huruf = $nilaiData->abjad ?? '-';

                            if ($nilaiData) {
                                $jumlahNilai += $angka;
                                $countMateri++;
                            }
                        @endphp
                        <td>{{ $angka > 0 ? $angka : '-' }}</td>
                        <td>{{ $huruf }}</td>
                    @endforeach

                    @php
                        $rataRata = $countMateri > 0 ? round($jumlahNilai / $countMateri) : 0;

                        // Predikat Akhir Berdasarkan Rata-rata
                        $predikatAkhir = '-';
                        if ($rataRata >= 85) {
                            $predikatAkhir = 'A';
                        } elseif ($rataRata >= 75) {
                            $predikatAkhir = 'B';
                        } elseif ($rataRata >= 60) {
                            $predikatAkhir = 'C';
                        } elseif ($rataRata > 0) {
                            $predikatAkhir = 'D';
                        }
                    @endphp

                    <td><strong>{{ $jumlahNilai }}</strong></td>
                    <td><strong>{{ $rataRata }}</strong></td>
                    <td><strong>{{ $predikatAkhir }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>
