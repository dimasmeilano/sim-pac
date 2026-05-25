<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Surat {{ $surat->nomor_surat }}</title>
    <style>
        /* ========================================== */
        /* ATURAN GLOBAL BASE                */
        /* ========================================== */
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            margin: 0;
            padding: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .text-center {
            text-align: center;
        }

        .text-justify {
            text-align: justify;
        }

        .ttd-container,
        table.ttd,
        .ttd,
        table.tanda-tangan,
        .tanda-tangan {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }

        br {
            margin: 2px 0;
        }

        /* ========================================== */
        /* KONDISI 1: JIKA SURAT TUGAS (LEGA/LONG)  */
        /* ========================================== */
        @if ($surat->jenis_surat == 'tugas')
            @page {
                margin: 15mm 20mm 15mm 20mm;
                /* Margin longgar standar formal */
            }

            body {
                font-size: 11pt;
                line-height: 1.45;
                /* Jarak baris lega */
            }

            table {
                margin-top: 8px;
                margin-bottom: 8px;
            }

            td {
                vertical-align: top;
                padding: 5px 4px;
                /* Kolom tabel memiliki jarak napas */
            }

            p,
            div {
                margin-top: 0;
                margin-bottom: 12px;
                /* Jarak antar paragraf renggang */
                padding: 0;
            }

            .tanda-tangan {
                margin-top: 40px !important;
            }

            .tanda-tangan td {
                padding: 6px !important;
            }

            /* ========================================== */
            /* KONDISI 2: JIKA SRP / LAINNYA (RAPAT)    */
            /* ========================================== */
        @else
            @page {
                margin: 8mm 12mm 8mm 12mm;
                /* Margin diperkecil sedikit agar ruang lebih lega */
            }

            body {
                font-size: 10pt;
                /* Sedikit dikecilkan dari 10.5pt */
                line-height: 1.15;
                /* Lebih rapat dari 1.21 */
            }

            /* Memastikan semua margin dan padding ditekan seminimal mungkin */
            table,
            p,
            div {
                margin-top: 0 !important;
                margin-bottom: 0 !important;
                padding: 0 !important;
            }

            .tanda-tangan {
                margin-top: 5px !important;
                /* Tarik tanda tangan lebih mendekat ke isi surat */
            }

            .tanda-tangan td {
                padding: 0px !important;
            }
        @endif
    </style>
</head>

<body>

    <div class="konten-surat-wrapper">
        {!! $surat->isi_surat !!}
    </div>

</body>

</html>
