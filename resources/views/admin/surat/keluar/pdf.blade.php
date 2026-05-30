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

        /* Mencegah TTD terpotong beda halaman */
        .ttd-container,
        table.ttd,
        .ttd,
        table.tanda-tangan,
        .tanda-tangan {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }

        /* Dukungan Khusus TTD, Stempel, & QR dari SuratService */
        div[style*="page-break-inside: avoid"] {
            page-break-inside: avoid !important;
        }

        img {
            max-width: 100%;
            height: auto;
        }

        br {
            margin: 2px 0;
        }

        /* ========================================== */
        /* KONDISI 1: JIKA SURAT TUGAS (LEGA/LONG)  */
        /* ========================================== */
        @if ($surat->jenis_surat == 'tugas' || $surat->jenis_surat == 'umum')
            @page {
                /* Margin longgar standar formal. Atas 3cm (untuk kop), Bawah/Kanan/Kiri 2cm */
                margin: 3cm 2cm 2cm 2cm;
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
                /* Margin diperkecil sedikit agar ruang lebih lega. Atas 2.5cm untuk kop */
                margin: 2.5cm 1.5cm 1.5cm 1.5cm;
            }

            body {
                font-size: 10pt;
                /* Sedikit dikecilkan */
                line-height: 1.15;
                /* Lebih rapat */
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
                margin-top: 15px !important;
                /* Tarik tanda tangan lebih mendekat */
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
