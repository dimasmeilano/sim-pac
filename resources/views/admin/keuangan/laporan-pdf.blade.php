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

        /* Kop Surat */
        .kop {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .kop h1 {
            font-size: 14pt;
            margin: 0;
        }

        .kop h2 {
            font-size: 12pt;
            margin: 5px 0;
        }

        .kop h3 {
            font-size: 11pt;
            margin: 0;
        }

        .kop p {
            font-size: 9pt;
            margin: 2px 0;
        }

        /* Judul Laporan */
        .judul {
            text-align: center;
            margin: 20px 0;
        }

        .judul h3 {
            text-decoration: underline;
            font-size: 13pt;
        }

        /* Tabel */
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
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        /* Ringkasan Saldo */
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

        /* Tanda Tangan */
        .ttd {
            margin-top: 50px;
        }

        .ttd .col-kiri {
            width: 45%;
            float: left;
        }

        .ttd .col-kanan {
            width: 45%;
            float: right;
            text-align: right;
        }

        .clearfix {
            clear: both;
        }

        /* Footer */
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
        }
    </style>
</head>

<body>

    <!-- ========== KOP SURAT ========== -->
    <div class="kop">
        @php
            $jenisTeks = '';
            if ($organization->jenis_organisasi == 'ipnu') {
                $jenisTeks = 'IPNU';
            } elseif ($organization->jenis_organisasi == 'ippnu') {
                $jenisTeks = 'IPPNU';
            } else {
                $jenisTeks = 'IPNU & IPPNU';
            }
        @endphp

        <h1>PIMPINAN {{ $organization->type == 'pac' ? 'ANAK CABANG' : 'RANTING' }}</h1>
        <h2>IKATAN PELAJAR NAHDLATUL ULAMA - {{ $jenisTeks }}</h2>
        <h3>{{ strtoupper($organization->name) }}</h3>
        <p>{{ $organization->alamat ?? 'Jl. Raya Kebomas No. 123, Gresik, Jawa Timur' }}</p>
        <p>Email: {{ $organization->email ?? 'pac.kebomas@ipnu-ippnu.or.id' }} | Website:
            {{ $organization->website ?? 'ipnu-kebomas.or.id' }}</p>
    </div>

    <!-- ========== JUDUL LAPORAN ========== -->
    <div class="judul">
        <h3>LAPORAN KEUANGAN</h3>
        <p>Periode: {{ date('d/m/Y', strtotime($startDate)) }} - {{ date('d/m/Y', strtotime($endDate)) }}</p>
        <p>Jenis Laporan: {{ $jenisTeks }}</p>
    </div>

    <!-- ========== RINGKASAN SALDO ========== -->
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
            <tr style="background-color: #e9ecef;">
                <th><strong>Saldo Akhir</strong></th>
                <td class="text-right"><strong>Rp {{ number_format($saldo, 0, ',', '.') }}</strong></td>
            </tr>
        </table>
    </div>
    <div class="clear"></div>

    <!-- ========== TABEL TRANSAKSI ========== -->
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
                        <strong>{{ $item->judul }}</strong>
                        @if ($item->keterangan)
                            <br><small>{{ $item->keterangan }}</small>
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

    <!-- ========== TANDA TANGAN ========== -->
    <div class="ttd">
        <div class="col-kiri">
            <p>Mengetahui,<br>
                <strong>Ketua {{ $jenisTeks }}</strong>
            </p>
            <br><br><br>
            <p><u>_____________________</u></p>
            @if ($organization && $organization->ketua)
                <small>{{ $organization->ketua->name }}</small>
            @endif
        </div>
        <div class="col-kanan">
            <p>{{ date('d F Y', strtotime($endDate)) }}<br>
                <strong>Bendahara</strong>
            </p>
            <br><br><br>
            <p><u>_____________________</u></p>
            @if ($organization && $organization->bendahara)
                <small>{{ $organization->bendahara->name }}</small>
            @endif
        </div>
    </div>
    <div class="clearfix"></div>

    <!-- ========== FOOTER ========== -->
    <div class="footer">
        <small>© {{ date('Y') }} SIM PAC IPNU-IPPNU - Dicetak pada {{ date('d/m/Y H:i:s') }}</small>
    </div>

</body>

</html>
