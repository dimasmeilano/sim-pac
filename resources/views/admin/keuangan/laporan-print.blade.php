<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan - {{ $organization->name ?? 'PAC' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            padding: 20mm;
            background: white;
        }

        .kop-surat {
            text-align: center;
            margin-bottom: 20px;
        }

        .kop-surat img {
            width: 100%;
            max-width: 800px;
        }

        .judul {
            text-align: center;
            margin: 20px 0;
        }

        .judul h3 {
            text-decoration: underline;
            font-size: 14pt;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            vertical-align: top;
        }

        th {
            background-color: #f0f0f0;
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .ringkasan {
            margin: 20px 0;
            width: 50%;
            float: right;
        }

        .ringkasan table {
            width: 100%;
        }

        .clear {
            clear: both;
        }

        .ttd {
            margin-top: 50px;
        }

        .ttd .col {
            width: 45%;
            display: inline-block;
        }

        .ttd .right {
            text-align: right;
        }

        .footer {
            margin-top: 30px;
            font-size: 9pt;
            text-align: center;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }

        @media print {
            body {
                padding: 0;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>

    <!-- KOP SURAT -->
    <div class="kop-surat">
        @if ($organization && $organization->getKopSuratAttribute())
            <img src="{{ public_path('storage/' . $organization->getKopSuratAttribute()) }}" alt="Kop Surat">
        @else
            <div style="border-bottom: 2px solid #000; padding-bottom: 10px;">
                <h2>PIMPINAN {{ $organization->type == 'pac' ? 'ANAK CABANG' : 'RANTING' }}</h2>
                <h3>
                    @if ($organization->jenis_organisasi == 'ipnu')
                        IKATAN PELAJAR NAHDLATUL ULAMA
                    @elseif($organization->jenis_organisasi == 'ippnu')
                        IKATAN PELAJAR PUTRI NAHDLATUL ULAMA
                    @else
                        IKATAN PELAJAR NAHDLATUL ULAMA - IPPNU
                    @endif
                </h3>
                <h3>{{ strtoupper($organization->name) }}</h3>
                <p>{{ $organization->alamat ?? '' }}</p>
                <p>Email: {{ $organization->email ?? '' }}</p>
            </div>
        @endif
    </div>

    <!-- JUDUL LAPORAN -->
    <div class="judul">
        <h3>LAPORAN KEUANGAN</h3>
        <p>Periode: {{ date('d/m/Y', strtotime($startDate)) }} - {{ date('d/m/Y', strtotime($endDate)) }}</p>
        <p>
            Jenis:
            @if ($organization->jenis_organisasi == 'ipnu')
                IPNU
            @elseif($organization->jenis_organisasi == 'ippnu')
                IPPNU
            @else
                IPNU & IPPNU
            @endif
        </p>
    </div>

    <!-- RINGKASAN SALDO -->
    <div class="ringkasan">
        <table>
            <tr>
                <th style="width: 60%;">Total Pemasukan</th>
                <td class="text-right">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th>Total Pengeluaran</th>
                <td class="text-right">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</td>
            </tr>
            <tr style="background-color: #e9ecef; font-weight: bold;">
                <th>Saldo Akhir</th>
                <td class="text-right">Rp {{ number_format($saldo, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>
    <div class="clear"></div>

    <!-- TABEL TRANSAKSI -->
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 12%;">Tanggal</th>
                <th style="width: 15%;">Kode</th>
                <th style="width: 38%;">Uraian</th>
                <th style="width: 15%;">Masuk (Rp)</th>
                <th style="width: 15%;">Keluar (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse($transaksi as $item)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td class="text-center">{{ date('d/m/Y', strtotime($item->tanggal)) }}</td>
                    <td class="text-center">{{ $item->kode_transaksi }}</td>
                    <td>
                        <strong>{{ $item->judul }}</strong><br>
                        <small>{{ $item->keterangan ?: '' }}</small>
                        @if ($item->programKerja)
                            <br><small class="text-muted">Progja: {{ $item->programKerja->nama }}</small>
                        @endif
                    </td>
                    <td class="text-right">
                        {{ $item->jenis == 'masuk' ? 'Rp ' . number_format($item->nominal, 0, ',', '.') : '-' }}</td>
                    <td class="text-right">
                        {{ $item->jenis == 'keluar' ? 'Rp ' . number_format($item->nominal, 0, ',', '.') : '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data transaksi pada periode ini</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #f8f9fa;">
                <td colspan="4" class="text-right"><strong>TOTAL</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($totalMasuk, 0, ',', '.') }}</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($totalKeluar, 0, ',', '.') }}</strong></td>
            </tr>
        </tfoot>
    </table>

    <!-- TANDA TANGAN -->
    <div class="ttd">
        <div class="col">
            <p>Mengetahui,<br>Ketua</p>
            <br><br><br>
            <p><u>_____________________</u></p>
            @if ($organization && $organization->ketua)
                <small>{{ $organization->ketua->name }}</small>
            @endif
        </div>
        <div class="col right">
            <p>{{ date('d F Y', strtotime($endDate)) }}<br>Bendahara</p>
            <br><br><br>
            <p><u>_____________________</u></p>
            @if ($organization && $organization->bendahara)
                <small>{{ $organization->bendahara->name }}</small>
            @endif
        </div>
    </div>

    <div class="footer">
        <small>© {{ date('Y') }} SIM PAC IPNU-IPPNU - Dicetak oleh {{ Auth::user()->name }} pada
            {{ date('d/m/Y H:i:s') }}</small>
    </div>

</body>

</html>
