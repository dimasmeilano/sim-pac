@extends('layouts.public')

@section('title', 'Hasil Verifikasi Surat')

@section('content')
    <style>
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

    <div class="container mt-4 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-9">

                <div class="card shadow-lg border-0 rounded-lg overflow-hidden">
                    {{-- HEADER CARD SERAGAM --}}
                    <div class="card-header bg-success text-white text-center py-4">
                        <h3 class="font-weight-bold mb-0"><i class="fas fa-shield-alt mr-2"></i> Hasil Validasi Dokumen</h3>
                        <p class="mb-0 mt-2 text-light">Sistem Informasi Manajemen PAC IPNU IPPNU</p>
                    </div>

                    <div class="card-body p-4 p-md-5 bg-light">
                        {{-- JIKA SURAT ASLI & SAH --}}
                        @if ($status == 'asli')
                            <div class="card text-center mb-4 border-0 shadow-sm border-top border-success"
                                style="border-top-width: 5px !important; border-radius: 12px;">
                                <div class="card-body py-4">
                                    <div class="display-4 text-success mb-2"><i class="fas fa-check-circle"></i></div>
                                    <h4 class="text-success font-weight-bold">DOKUMEN ASLI & SAH</h4>
                                    <p class="text-muted mb-0">Surat ini terverifikasi secara resmi di pangkalan data kami.
                                    </p>
                                </div>
                            </div>

                            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                                <div class="card-header bg-white font-weight-bold border-bottom-0 pt-3 pb-0"><i
                                        class="fas fa-file-alt text-success mr-2"></i> Detail Informasi Surat</div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-borderless table-sm mb-0">
                                            <tr>
                                                <th width="35%" class="text-muted">Nomor Surat</th>
                                                <td class="font-weight-bold">{{ $surat->nomor_surat }}</td>
                                            </tr>
                                            <tr>
                                                <th class="text-muted">Perihal</th>
                                                <td>{{ $surat->perihal }}</td>
                                            </tr>
                                            <tr>
                                                <th class="text-muted">Tujuan</th>
                                                <td>{{ $surat->tujuan }}</td>
                                            </tr>
                                            <tr>
                                                <th class="text-muted">Organisasi</th>
                                                <td>{{ strtoupper($surat->organization->jenis_organisasi ?? 'IPNU') }} PAC
                                                </td>
                                            </tr>
                                            <tr>
                                                <th class="text-muted">Waktu Pengesahan</th>
                                                <td>{{ $surat->tanggal_ttd_ketua ? $surat->tanggal_ttd_ketua->translatedFormat('d F Y H:i') . ' WIB' : '-' }}
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            {{-- FITUR LETTER TRACKING --}}
                            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                                <div class="card-header bg-white font-weight-bold border-bottom-0 pt-3 pb-0"><i
                                        class="fas fa-history text-primary mr-2"></i> Rekam Jejak (Letter Tracking)</div>
                                <div class="card-body">
                                    <ul class="timeline mb-0">
                                        @foreach ($logs as $log)
                                            <li class="timeline-item">
                                                <span
                                                    class="font-weight-bold text-dark">{{ ucfirst($log->description) }}</span><br>
                                                <small class="text-muted">Oleh: {{ $log->causer->name ?? 'Sistem' }} |
                                                    {{ $log->created_at->translatedFormat('d M Y, H:i') }} WIB</small>
                                            </li>
                                        @endforeach
                                        <li class="timeline-item active">
                                            <span class="font-weight-bold text-primary">TTE QR Code Berhasil
                                                Dipindai</span><br>
                                            <small class="text-muted">Dokumen sedang diverifikasi oleh publik saat
                                                ini.</small>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            {{-- JIKA SURAT DRAFT / BELUM SAH --}}
                        @elseif($status == 'belum_sah')
                            <div class="card text-center border-0 shadow-sm border-top border-warning"
                                style="border-top-width: 5px !important; border-radius: 12px;">
                                <div class="card-body py-5">
                                    <div class="display-4 text-warning mb-2"><i class="fas fa-exclamation-triangle"></i>
                                    </div>
                                    <h4 class="text-warning font-weight-bold">DOKUMEN BELUM SAH</h4>
                                    <p class="text-muted">Surat dengan nomor <b>{{ $surat->nomor_surat }}</b> terdaftar di
                                        sistem, namun belum ditandatangani secara resmi (TTE) oleh Ketua.</p>
                                    <a href="/" class="btn btn-warning text-white mt-3 font-weight-bold px-4">Kembali
                                        ke Beranda</a>
                                </div>
                            </div>

                            {{-- JIKA SURAT PALSU / TIDAK ADA --}}
                        @else
                            <div class="card text-center border-0 shadow-sm border-top border-danger"
                                style="border-top-width: 5px !important; border-radius: 12px;">
                                <div class="card-body py-5">
                                    <div class="display-4 text-danger mb-2"><i class="fas fa-times-circle"></i></div>
                                    <h4 class="text-danger font-weight-bold">DOKUMEN TIDAK VALID</h4>
                                    <p class="text-muted">Nomor surat <b>{{ $nomor }}</b> tidak ditemukan dalam
                                        pangkalan data SIM PAC IPNU IPPNU. Waspadai indikasi pemalsuan dokumen!</p>
                                    <a href="/" class="btn btn-danger mt-3 font-weight-bold px-4">Laporkan Masalah</a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
