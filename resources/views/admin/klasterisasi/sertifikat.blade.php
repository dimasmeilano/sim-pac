<!DOCTYPE html>
<html>

<head>
    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }

        body {
            font-family: 'Times New Roman', serif;
            margin: 0;
            padding: 0;
            color: #333;
        }

        /* Bingkai */
        .wrapper {
            width: 100%;
            height: 100%;
            padding: 20px;
            box-sizing: border-box;
        }

        .border-outer {
            border: 5px solid #006400;
            padding: 5px;
            height: 97%;
        }

        .border-inner {
            border: 2px solid #006400;
            padding: 40px;
            height: 98%;
            position: relative;
        }

        /* Elemen Desain */
        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .logo {
            width: 100px;
            margin-bottom: 10px;
        }

        .title {
            font-size: 32px;
            font-weight: bold;
            color: #006400;
            text-transform: uppercase;
            margin: 0;
        }

        .subtitle {
            font-size: 16px;
            font-style: italic;
            margin-top: 5px;
        }

        /* Konten */
        .recipient {
            font-size: 28px;
            font-weight: bold;
            color: #000;
            margin: 30px 0;
            border-bottom: 1px solid #ccc;
            padding-bottom: 10px;
        }

        .kluster-box {
            border: 3px solid #006400;
            padding: 20px;
            width: 300px;
            margin: 40px auto;
            background: #f0f7f0;
            border-radius: 10px;
        }

        /* Footer */
        .footer-ttd {
            position: absolute;
            bottom: 80px;
            right: 60px;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #000;
            margin-top: 60px;
            width: 200px;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="border-outer">
            <div class="border-inner">
                <div class="header">
                    <!-- Tambahkan logo di sini jika sudah ada -->
                    <div style="font-size: 20px; font-weight: bold;">PIMPINAN ANAK CABANG</div>
                    <h1 class="title">IPNU IPPNU KEC. BANJAR</h1>
                    <div class="subtitle">Jl. Raya Utama No. 123, Kec. Banjar, Kab. Gresik</div>
                </div>

                <div style="text-align: center;">
                    <p style="font-size: 20px;">Menetapkan Status Klasterisasi Organisasi kepada:</p>
                    <div class="recipient">{{ strtoupper($klasterisasi->organization->nama) }}</div>
                    <p style="font-size: 18px;">Atas dedikasi dan tertib administrasi pada periode
                        <b>{{ $klasterisasi->periode_penilaian }}</b></p>
                </div>

                <div class="kluster-box">
                    <p style="margin:0; font-size: 14px; text-transform: uppercase;">Predikat</p>
                    <h1 style="margin: 5px 0; color: #006400;">KLUSTER {{ $klasterisasi->kluster }}</h1>
                </div>

                <div class="footer-ttd">
                    <p>Banjar, {{ date('d F Y') }}</p>
                    <p>Ketua PAC {{ $klasterisasi->jenis_organisasi }}</p>
                    <div class="signature-line">
                        <b>( ____________________ )</b>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
