<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - SIM PAC IPNU-IPPNU</title>
    <style>
        /* Kop Surat */
        .kop-laporan-keuangan table {
            page-break-inside: avoid;
        }

        @page {
            margin: 1.5cm;
            size: A4;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.4;
            padding: 20mm;
            background: white;
            color: black;
            margin: 0;
            padding: 0;
        }

        @media print {
            body {
                padding: 0;
            }

            .no-print {
                display: none;
            }
        }

        /* Judul Laporan */
        .judul-laporan {
            text-align: center;
            margin: 20px 0;
        }

        .judul-laporan h3 {
            font-size: 14pt;
            text-decoration: underline;
        }

        /* Tabel */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        table,
        th,
        td {
            border: 1px solid #000;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        /* Tanda Tangan */
        .ttd {
            margin-top: 40px;
        }

        .ttd .row {
            display: flex;
            justify-content: space-between;
        }

        .ttd .col {
            width: 45%;
        }

        .ttd .text-right {
            text-align: right;
        }

        /* Footer */
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ccc;
            font-size: 9pt;
            text-align: center;
        }
    </style>
    @stack('styles')
</head>

<body>
    @yield('content')

    <div class="footer no-print">
        <small>© {{ date('Y') }} SIM PAC IPNU-IPPNU - Dicetak oleh {{ Auth::user()->name }} pada
            {{ date('d/m/Y H:i:s') }}</small>
    </div>
</body>

</html>
