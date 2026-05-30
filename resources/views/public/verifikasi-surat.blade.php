<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Surat Digital PAC IPNU IPPNU</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .timeline {
            position: relative;
            padding-left: 30px;
            list-style: none;
        }

        .timeline:before {
            content: " ";
            background: #dee2e6;
            display: inline-block;
            position: absolute;
            left: 16px;
            width: 2px;
            height: 100%;
            z-index: 400;
        }

        .timeline-item {
            margin: 20px 0;
        }

        .timeline-item:before {
            content: " ";
            background: #28a745;
            display: inline-block;
            position: absolute;
            border-radius: 50%;
            left: 12px;
            width: 10px;
            height: 10px;
            z-index: 400;
            border: 2px solid #fff;
        }

        .timeline-item.active:before {
            background: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.3);
        }
    </style>
</head>

<body>

    <div class="container my-5">
        <div class="text-center mb-4">
            <h3 class="font-weight-bold text-success">E-OFFICE PAC IPNU IPPNU</h3>
            <p class="text-muted">Sistem Informasi Manajemen & Validasi Dokumen Digital</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-8">

                {{-- JIKA SURAT ASLI & SAH --}}
                @if ($status == 'asli')
                    <div class="card text-center mb-4 bg-white border-top border-success"
                        style="border-top-width: 5px !important;">
                        <div class="card-body py-4">
                            <div class="display-4 text-success mb-2">✓</div>
                            <h4 class="text-success font-weight-bold">DOKUMEN ASLI & SAH</h4>
                            <p class="text-muted mb-0">Surat ini terverifikasi secara resmi di dalam sistem SIM PAC IPNU
                                IPPNU</p>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header bg-white font-weight-bold">Detail Informasi Surat</div>
                        <div class="card-body p-0">
                            <table class="table table-striped mb-0">
                                <tr>
                                    <th width="30%">Nomor Surat</th>
                                    <td>{{ $surat->nomor_surat }}</td>
                                </tr>
                                <tr>
                                    <th>Perihal</th>
                                    <td>{{ $surat->perihal }}</td>
                                </tr>
                                <tr>
                                    <th>Tujuan</th>
                                    <td>{{ $surat->tujuan }}</td>
                                </tr>
                                <tr>
                                    <th>Organisasi</th>
                                    <td>{{ strtoupper($surat->organization->jenis_organisasi ?? 'IPNU') }} PAC</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Sah (Ketua)</th>
                                    <td>{{ $surat->tanggal_ttd_ketua ? $surat->tanggal_ttd_ketua->translatedFormat('d F Y H:i') . ' WIB' : '-' }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    {{-- FITUR LETTER TRACKING --}}
                    <div class="card">
                        <div class="card-header bg-white font-weight-bold">Sistem Pelacakan Surat (Letter Tracking)
                        </div>
                        <div class="card-body">
                            <ul class="timeline mb-0">
                                @foreach ($logs as $log)
                                    <li class="timeline-item">
                                        <span class="font-weight-bold text-dark">{{ ucfirst($log->description) }}</span>
                                        <br>
                                        <small class="text-muted">
                                            Oleh: {{ $log->causer->name ?? 'Sistem' }} |
                                            {{ $log->created_at->translatedFormat('d M Y, H:i') }} WIB
                                        </small>
                                    </li>
                                @endforeach
                                <li class="timeline-item active">
                                    <span class="font-weight-bold text-primary">TTE QR Code Berhasil Dipindai</span>
                                    <br>
                                    <small class="text-muted">Dokumen sedang diverifikasi oleh publik saat ini.</small>
                                </li>
                            </ul>
                        </div>
                    </div>

                    {{-- JIKA SURAT DRAFT / BELUM SAH --}}
                @elseif($status == 'belum_sah')
                    <div class="card text-center bg-white border-top border-warning"
                        style="border-top-width: 5px !important;">
                        <div class="card-body py-5">
                            <div class="display-4 text-warning mb-2">⚠</div>
                            <h4 class="text-warning font-weight-bold">DOKUMEN BELUM SAH</h4>
                            <p class="text-muted">Surat dengan nomor <b>{{ $surat->nomor_surat }}</b> terdaftar di
                                sistem, namun belum ditandatangani secara resmi oleh Ketua PAC.</p>
                            <a href="/" class="btn btn-warning text-white mt-3">Kembali ke Beranda</a>
                        </div>
                    </div>

                    {{-- JIKA SURAT PALSU / TIDAK ADA --}}
                @else
                    <div class="card text-center bg-white border-top border-danger"
                        style="border-top-width: 5px !important;">
                        <div class="card-body py-5">
                            <div class="display-4 text-danger mb-2">❌</div>
                            <h4 class="text-danger font-weight-bold">DOKUMEN TIDAK VALID / PALSU</h4>
                            <p class="text-muted">Nomor surat <b>{{ $nomor }}</b> tidak ditemukan dalam pangkalan
                                data SIM PAC IPNU IPPNU. Waspadai pemalsuan dokumen!</p>
                            <a href="/" class="btn btn-danger mt-3">Laporkan Masalah</a>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>

</body>

</html>
