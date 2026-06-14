<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ID Card - {{ $peserta->nama_lengkap }}</title>
    <style>
        /* Pengaturan Kertas Print */
        @page {
            margin: 0;
            size: A4;
            /* Print di kertas A4 */
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #e0e0e0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Ukuran Standar ID Card Panitia/Peserta (B4: 97mm x 137mm) */
        .id-card {
            width: 97mm;
            height: 137mm;
            background-color: white;
            background-image: linear-gradient(135deg, #ffffff 0%, #f0fdf4 100%);
            border: 2px solid #00723b;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            position: relative;
            overflow: hidden;
            text-align: center;
        }

        /* Desain Header Hijau NU */
        .header {
            background-color: #00723b;
            color: white;
            padding: 15px 10px;
            border-bottom: 5px solid #ffc107;
            /* Garis kuning khas */
        }

        .header h3 {
            margin: 0;
            font-size: 24px;
            letter-spacing: 2px;
            font-weight: bold;
        }

        .header p {
            margin: 5px 0 0 0;
            font-size: 11px;
            text-transform: uppercase;
        }

        /* Area Foto & Nama */
        .photo-area {
            margin-top: 25px;
        }

        .photo-box {
            width: 3cm;
            height: 4cm;
            border: 2px dashed #00723b;
            margin: 0 auto;
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ccc;
            font-size: 10px;
        }

        .name-area {
            margin-top: 15px;
            padding: 0 10px;
        }

        .peserta-name {
            font-size: 20px;
            font-weight: bold;
            color: #000;
            margin: 0;
            text-transform: uppercase;
        }

        .peserta-utusan {
            font-size: 14px;
            color: #555;
            margin-top: 5px;
            font-weight: bold;
        }

        /* Status PESERTA (Kotak Kuning Bawah) */
        .status-badge {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #ffc107;
            color: #000;
            padding: 8px 30px;
            font-size: 22px;
            font-weight: bold;
            border-radius: 5px;
            border: 2px solid #e0a800;
            letter-spacing: 3px;
        }

        /* Tombol Print (Hanya tampil di layar) */
        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #00723b;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
        }

        @media print {
            body {
                background-color: white;
            }

            .print-btn {
                display: none;
            }

            .id-card {
                box-shadow: none;
            }
        }
    </style>
</head>

<body>

    <!-- Tombol Print -->
    <button class="print-btn" onclick="window.print()">🖨️ Cetak ID Card</button>

    <!-- Kanvas ID Card -->
    <div class="id-card">
        <!-- Header -->
        <div class="header">
            <h3>MAKESTA</h3>
            <p>{{ $peserta->event->organization->name ?? 'IPNU-IPPNU' }}</p>
        </div>

        <!-- Foto -->
        <div class="photo-area">
            <div class="photo-box">
                Tempel Foto<br>3 x 4
            </div>
        </div>

        <!-- Nama & Utusan -->
        <div class="name-area">
            <h2 class="peserta-name">{{ $peserta->nama_lengkap }}</h2>
            <div class="peserta-utusan">Utusan: {{ $peserta->utusan }}</div>
        </div>

        <!-- Badge -->
        <div class="status-badge">
            PESERTA
        </div>
    </div>

</body>

</html>
