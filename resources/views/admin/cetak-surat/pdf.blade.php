<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Surat {{ $suratKeluar->nomor_surat }}</title>
    <style>
        /* 1. Press margin semua sisi menjadi 10mm (sangat hemat ruang) */
        @page {
            margin: 10mm 15mm 10mm 15mm;
            /* Atas: 10mm, Kanan: 15mm, Bawah: 10mm, Kiri: 15mm */
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10.5pt;
            /* Diturunkan sedikit dari 11pt agar lebih aman */
            line-height: 1.25;
            /* Sangat rapat namun tetap terbaca dengan baik */
            color: #000;
            margin: 0;
            padding: 0;
        }

        /* 2. Press spasi semua tabel konsideran (Menimbang, Mengingat, dll) */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2px !important;
            margin-bottom: 2px !important;
        }

        td {
            vertical-align: top;
            padding: 0px 2px !important;
            /* Nol kan padding atas-bawah antar baris */
        }

        /* 3. Press spasi paragraf teks HTML bawaan seeder */
        p,
        div {
            margin-top: 2px !important;
            margin-bottom: 2px !important;
            padding: 0 !important;
        }

        .text-center {
            text-align: center;
        }

        .text-justify {
            text-align: justify;
        }

        /* 4. Jaga bagian tanda tangan agar tetap rapat dan tidak pecah halaman */
        .ttd-container,
        table.ttd,
        .ttd {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
            margin-top: 10px !important;
            /* Jarak menuju area TTD dipersempit */
        }

        br {
            content: "";
            margin: 2px 0;
            display: block;
        }
    </style>
</head>

<body>

    <div class="konten-surat-wrapper">
        {!! $suratKeluar->isi_surat !!}
    </div>

</body>

</html>
