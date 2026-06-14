<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Cetak Masal ID Card - {{ $event->tema }}</title>
    <style>
        @page {
            margin: 10mm;
        }

        body {
            font-family: sans-serif;
        }

        /* Desain ID Card disesuaikan untuk DOMPDF menggunakan inline-block */
        .id-card {
            width: 85mm;
            height: 125mm;
            border: 2px solid #00723b;
            border-radius: 10px;
            display: inline-block;
            margin: 4mm;
            position: relative;
            text-align: center;
            vertical-align: top;
            background-color: #ffffff;
        }

        .header {
            background-color: #00723b;
            color: white;
            padding: 10px;
            border-bottom: 5px solid #ffc107;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }

        .header h3 {
            margin: 0;
            font-size: 20px;
            letter-spacing: 1px;
        }

        .header p {
            margin: 5px 0 0 0;
            font-size: 10px;
            text-transform: uppercase;
        }

        .photo-box {
            width: 2.5cm;
            height: 3.5cm;
            border: 2px dashed #00723b;
            margin: 15px auto;
            line-height: 3.5cm;
            color: #999;
            font-size: 10px;
            background-color: #f8f9fa;
        }

        .peserta-name {
            font-size: 16px;
            font-weight: bold;
            margin: 5px 10px;
            text-transform: uppercase;
        }

        .peserta-utusan {
            font-size: 12px;
            color: #555;
            font-weight: bold;
        }

        .status-badge {
            position: absolute;
            bottom: 15px;
            left: 10mm;
            right: 10mm;
            background-color: #ffc107;
            color: #000;
            padding: 5px;
            font-size: 16px;
            font-weight: bold;
            border: 2px solid #e0a800;
            border-radius: 5px;
            letter-spacing: 2px;
        }
    </style>
</head>

<body>

    <!-- Looping Data Peserta -->
    @foreach ($event->pesertas as $peserta)
        <div class="id-card">
            <div class="header">
                <h3>MAKESTA</h3>
                <p>{{ $event->organization->nama ?? 'IPNU-IPPNU' }}</p>
            </div>

            <div class="photo-box">Foto 3 x 4</div>

            <div class="peserta-name">{{ $peserta->nama_lengkap }}</div>
            <div class="peserta-utusan">Utusan: {{ $peserta->utusan }}</div>

            <div class="status-badge">PESERTA</div>
        </div>
    @endforeach

</body>

</html>
