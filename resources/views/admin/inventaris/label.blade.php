<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Label Inventaris Aset</title>
    <!-- FontAwesome untuk ikon Print -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 20px;
        }

        .no-print {
            text-align: center;
            margin-bottom: 20px;
            background: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .btn-print {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }

        /* Layout Kertas A4 */
        .page {
            width: 210mm;
            min-height: 297mm;
            padding: 10mm;
            margin: 0 auto;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            /* 3 Kolom ke samping */
            grid-auto-rows: min-content;
            gap: 15px;
            box-sizing: border-box;
        }

        /* Desain Kotak Stiker Label */
        .label-box {
            border: 2px solid #343a40;
            border-radius: 8px;
            padding: 10px;
            background: #fff;
            display: flex;
            align-items: center;
            page-break-inside: avoid;
            /* Mencegah label terpotong di beda halaman */
        }

        .qr-area {
            flex-shrink: 0;
            margin-right: 12px;
            border: 1px solid #ddd;
            padding: 3px;
            border-radius: 4px;
        }

        .info-area {
            flex: 1;
            font-size: 11px;
            line-height: 1.3;
            color: #000;
        }

        .info-area h4 {
            margin: 0 0 4px 0;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            border-bottom: 1px solid #343a40;
            padding-bottom: 3px;
        }

        .info-area p {
            margin: 2px 0;
        }

        .kode-teks {
            font-family: 'Courier New', Courier, monospace;
            font-weight: bold;
            font-size: 12px;
        }

        /* Konfigurasi saat diprint */
        @media print {
            body {
                background: none;
                padding: 0;
                margin: 0;
            }

            .no-print {
                display: none !important;
            }

            .page {
                margin: 0;
                box-shadow: none;
                border: none;
                width: 100%;
                padding: 5mm;
            }
        }
    </style>
</head>

<body>

    <div class="no-print">
        <button onclick="window.print()" class="btn-print"><i class="fas fa-print"></i> Print Label Stiker</button>
        <br><br>
        <small class="text-muted">Gunakan kertas HVS biasa lalu pasang dengan lakban bening, atau gunakan Kertas Stiker
            Label utuh.</small>
    </div>

    <div class="page">
        @foreach ($inventaris as $item)
            <div class="label-box">
                <div class="qr-area">
                    {{-- Generate QR Code otomatis dari Kode Barang --}}
                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(65)->margin(0)->generate($item->kode_barang) !!}
                </div>
                <div class="info-area">
                    <h4>{{ $item->organization->nama ?? ($item->organization->name ?? 'PAC IPNU IPPNU') }}</h4>
                    <p class="kode-teks">{{ $item->kode_barang }}</p>
                    <p><strong>{{ Str::limit($item->nama_barang, 25) }}</strong></p>
                    <p>Thn: {{ $item->tahun_perolehan ?? '-' }} | Kds:
                        {{ ucfirst(str_replace('_', ' ', $item->kondisi)) }}</p>
                </div>
            </div>
        @endforeach
    </div>

</body>

</html>
