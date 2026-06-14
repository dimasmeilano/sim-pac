<table>
    <thead>
        <tr>
            <!-- Header Baris 1 dengan Background Color -->
            <th rowspan="2"
                style="background-color: #00ff00; font-weight: bold; text-align: center; vertical-align: center;">NO</th>
            <th colspan="2"
                style="background-color: #ffff00; font-weight: bold; text-align: center; vertical-align: center;">No
                Sertifikat</th>
            <th rowspan="2"
                style="background-color: #00ff00; font-weight: bold; text-align: center; vertical-align: center;">NAMA
            </th>

            @foreach ($event->materis as $materi)
                <th colspan="2"
                    style="background-color: #ffff00; font-weight: bold; text-align: center; vertical-align: center;">
                    {{ strtoupper($materi->nama_materi) }}</th>
            @endforeach

            <th rowspan="2"
                style="background-color: #ff0000; color: #ffffff; font-weight: bold; text-align: center; vertical-align: center;">
                JUMLAH NILAI</th>
            <th rowspan="2"
                style="background-color: #ff0000; color: #ffffff; font-weight: bold; text-align: center; vertical-align: center;">
                RATA RATA</th>
            <th rowspan="2"
                style="background-color: #ff0000; color: #ffffff; font-weight: bold; text-align: center; vertical-align: center;">
                PREDIKAT</th>
        </tr>
        <tr>
            <!-- Header Baris 2 -->
            <th style="background-color: #ffff00; font-weight: bold; text-align: center;">No Urut</th>
            <th style="background-color: #ffff00; font-weight: bold; text-align: center;">Kode Sertifikat</th>

            @foreach ($event->materis as $materi)
                <th style="background-color: #ffff00; font-weight: bold; text-align: center;">ANGKA</th>
                <th style="background-color: #ffff00; font-weight: bold; text-align: center;">HURUF</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach ($event->pesertas as $index => $peserta)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td></td> <!-- Kolom Kosong untuk Diisi Manual -->
                <td></td> <!-- Kolom Kosong untuk Diisi Manual -->
                <td>{{ $peserta->nama_lengkap }}</td>

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
                    <td style="text-align: center;">{{ $angka > 0 ? $angka : '-' }}</td>
                    <td style="text-align: center;">{{ $huruf }}</td>
                @endforeach

                @php
                    $rataRata = $countMateri > 0 ? round($jumlahNilai / $countMateri) : 0;

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

                <td style="text-align: center; font-weight: bold;">{{ $jumlahNilai }}</td>
                <td style="text-align: center; font-weight: bold;">{{ $rataRata }}</td>
                <td style="text-align: center; font-weight: bold;">{{ $predikatAkhir }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
