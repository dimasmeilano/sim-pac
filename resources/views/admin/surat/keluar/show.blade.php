@extends('layouts.adminlte')

@section('title', 'Detail Surat Keluar')
@section('page-title', 'Detail Surat: ' . $suratKeluar->nomor_surat)

@push('styles')
    <style>
        /* Perbaikan CSS Timeline agar tidak hancur di layar/kolom sempit */
        .timeline {
            position: relative;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .timeline:before {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            width: 4px;
            background: #ddd;
            left: 31px;
            border-radius: 2px;
        }

        .timeline>div {
            position: relative;
            margin-bottom: 15px;
            display: block;
        }

        .timeline>div>.fa,
        .timeline>div>.fas,
        .timeline>div>.far {
            background-color: #fff;
            border-radius: 50%;
            font-size: 16px;
            height: 30px;
            left: 18px;
            line-height: 30px;
            position: absolute;
            text-align: center;
            top: 0;
            width: 30px;
            z-index: 1;
            border: 1px solid #ddd;
        }

        .timeline>div>.timeline-item {
            box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
            border-radius: 3px;
            margin-left: 60px;
            /* Memberi jarak agar ikon tidak menabrak kotak teks */
            margin-right: 0;
            background: #fff;
            color: #444;
            padding: 0;
            word-wrap: break-word;
            /* Mencegah teks terpotong huruf per huruf */
        }

        .timeline>div>.timeline-item>.time {
            color: #999;
            float: right;
            padding: 10px;
            font-size: 12px;
        }

        .timeline>div>.timeline-item>.timeline-header {
            margin: 0;
            padding: 10px;
            font-size: 14px;
            border-bottom: 1px solid #f4f4f4;
            white-space: normal;
        }

        .timeline>div>.timeline-item>.timeline-body {
            padding: 10px;
            font-size: 13px;
        }

        .time-label {
            position: relative;
            margin-bottom: 10px;
            display: block;
        }

        .time-label>span {
            font-weight: 600;
            padding: 5px 10px;
            border-radius: 4px;
            background-color: #fff;
            display: inline-block;
        }

        <style>.ttd-preview {
            margin-top: 50px;
            width: 100%;
            overflow: auto;
        }

        .ttd-preview .kiri {
            width: 45%;
            float: left;
            text-align: center;
        }

        .ttd-preview .kanan {
            width: 45%;
            float: right;
            text-align: center;
        }

        .ttd-image {
            max-height: 80px;
            margin-bottom: 10px;
        }

        .clearfix {
            clear: both;
        }
    </style>

    </style>
@endpush

@section('content')
    <div class="row">

        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Informasi Surat</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Nomor Surat</th>
                            <td><code>{{ $suratKeluar->nomor_surat }}</code></td>
                        </tr>
                        <tr>
                            <th>Perihal</th>
                            <td>{{ $suratKeluar->perihal }}</td>
                        </tr>
                        <tr>
                            <th>Tujuan</th>
                            <td>{{ $suratKeluar->tujuan }}</td>
                        </tr>
                        <tr>
                            <th>Template</th>
                            <td>
                                {{ $suratKeluar->template->nama ?? '-' }}
                                @if ($suratKeluar->template)
                                    ({{ $suratKeluar->template->kode }})
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Lampiran</th>
                            <td>
                                @if ($suratKeluar->lampiran)
                                    <a href="{{ asset('storage/' . $suratKeluar->lampiran) }}" target="_blank"
                                        class="btn btn-sm btn-info">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>{!! $suratKeluar->status_validasi_text !!}</td>
                        </tr>
                        <tr>
                            <th>Isi Surat</th>
                            <td>
                                <div class="card p-4"
                                    style="background: #fff; border: 1px solid #ddd; max-width: 800px; margin: 0 auto; box-shadow: 0 0 10px rgba(0,0,0,0.05);">
                                    <div class="surat-wrapper"
                                        style="font-family: 'Times New Roman', Times, serif; font-size: 12pt; line-height: 1.5; color: #000;">
                                        {!! $suratKeluar->isi_surat !!}
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                    <div class="ttd-preview">
                        <div class="kiri">
                            <div>Ketua</div>
                            @if ($suratKeluar->status_validasi == 'selesai' && $suratKeluar->ttd_ketua_file)
                                <div class="ttd-image">
                                    <img src="{{ asset('storage/' . $suratKeluar->ttd_ketua_file) }}"
                                        style="max-height: 60px;">
                                </div>
                            @else
                                <div style="height: 50px;"></div>
                            @endif
                            <u>{{ $suratKeluar->ditandatanganiKetuaOleh->name ?? '_____________________' }}</u>
                            <div><small>NIA: {{ $suratKeluar->ditandatanganiKetuaOleh->nik ?? '-' }}</small></div>
                        </div>
                        <div class="kanan">
                            <div>Sekretaris</div>
                            @if ($suratKeluar->ttd_sekretaris_file)
                                <div class="ttd-image">
                                    <img src="{{ asset('storage/' . $suratKeluar->ttd_sekretaris_file) }}"
                                        style="max-height: 60px;">
                                </div>
                            @else
                                <div style="height: 50px;"></div>
                            @endif
                            <u>{{ $suratKeluar->ditandatanganiSekretarisOleh->name ?? '_____________________' }}</u>
                            <div><small>NIA: {{ $suratKeluar->ditandatanganiSekretarisOleh->nik ?? '-' }}</small></div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>

        <div class="col-md-4">

            <div class="card card-primary card-outline">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title">Aksi</h3>
                </div>
                <div class="card-body">
                    @include('admin.surat.keluar.partials.tombol-aksi', ['suratKeluar' => $suratKeluar])

                    <a href="{{ route('surat.keluar.index') }}" class="btn btn-default btn-block mt-2">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>

            @if ($suratKeluar->signer)
                <div class="card card-success card-outline">
                    <div class="card-header bg-success text-white">
                        <h3 class="card-title">Informasi Tanda Tangan</h3>
                    </div>
                    <div class="card-body">
                        <p><strong>Ditandatangani Oleh:</strong> {{ $suratKeluar->signer->name }}</p>
                        <p><strong>Tanggal:</strong>
                            {{ $suratKeluar->tanggal_ttd ? date('d/m/Y H:i', strtotime($suratKeluar->tanggal_ttd)) : '-' }}
                        </p>
                        <p><strong>Tanggal Kirim:</strong>
                            {{ $suratKeluar->tanggal_kirim ? date('d/m/Y', strtotime($suratKeluar->tanggal_kirim)) : '-' }}
                        </p>
                    </div>
                </div>
            @endif

            <div class="card card-info card-outline">
                <div class="card-header bg-info text-white">
                    <h3 class="card-title">
                        <i class="fas fa-history"></i> Riwayat Surat
                    </h3>
                </div>
                <div class="card-body">
                    <div class="timeline timeline-inverse">

                        <div class="time-label">
                            <span class="bg-info">{{ $suratKeluar->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div>
                            <i class="fas fa-file-alt bg-info"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fas fa-clock"></i>
                                    {{ $suratKeluar->created_at->diffForHumans() }}</span>
                                <h3 class="timeline-header">Surat Dibuat</h3>
                                <div class="timeline-body">
                                    Dibuat oleh: <strong>{{ $suratKeluar->creator->name ?? 'Tidak diketahui' }}</strong>
                                </div>
                            </div>
                        </div>

                        @if ($suratKeluar->diajukan_oleh)
                            <div>
                                <i class="fas fa-paper-plane bg-warning"></i>
                                <div class="timeline-item">
                                    <h3 class="timeline-header">Diajukan Validasi</h3>
                                    <div class="timeline-body">
                                        Diajukan oleh: <strong>{{ $suratKeluar->diajukanOleh->name ?? '-' }}</strong><br>
                                        Validator: <strong>{{ $suratKeluar->divalidasiOleh->name ?? '-' }}</strong>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($suratKeluar->divalidasi_oleh && $suratKeluar->tanggal_validasi)
                            <div>
                                <i class="fas fa-check-circle bg-success"></i>
                                <div class="timeline-item">
                                    <span class="time"><i class="fas fa-clock"></i>
                                        {{ \Carbon\Carbon::parse($suratKeluar->tanggal_validasi)->diffForHumans() }}</span>
                                    <h3 class="timeline-header">Divalidasi Wakil</h3>
                                    <div class="timeline-body">
                                        Divalidasi oleh: <strong>{{ $suratKeluar->divalidasiOleh->name ?? '-' }}</strong>
                                        @if ($suratKeluar->catatan_validasi)
                                            <br><small class="text-muted">Catatan:
                                                {{ $suratKeluar->catatan_validasi }}</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($suratKeluar->ditandatangani_sekretaris_oleh && $suratKeluar->tanggal_ttd_sekretaris)
                            <div>
                                <i class="fas fa-signature bg-primary"></i>
                                <div class="timeline-item">
                                    <span class="time"><i class="fas fa-clock"></i>
                                        {{ \Carbon\Carbon::parse($suratKeluar->tanggal_ttd_sekretaris)->diffForHumans() }}</span>
                                    <h3 class="timeline-header">Ditandatangani Sekretaris</h3>
                                    <div class="timeline-body">
                                        Ditandatangani oleh:
                                        <strong>{{ $suratKeluar->ditandatanganiSekretarisOleh->name ?? '-' }}</strong>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($suratKeluar->ditandatangani_ketua_oleh && $suratKeluar->tanggal_ttd_ketua)
                            <div>
                                <i class="fas fa-signature bg-success"></i>
                                <div class="timeline-item">
                                    <span class="time"><i class="fas fa-clock"></i>
                                        {{ \Carbon\Carbon::parse($suratKeluar->tanggal_ttd_ketua)->diffForHumans() }}</span>
                                    <h3 class="timeline-header">Ditandatangani Ketua</h3>
                                    <div class="timeline-body">
                                        Ditandatangani oleh:
                                        <strong>{{ $suratKeluar->ditandatanganiKetuaOleh->name ?? '-' }}</strong>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($suratKeluar->status_validasi == 'selesai')
                            <div>
                                <i class="fas fa-flag-checkered bg-success"></i>
                                <div class="timeline-item">
                                    <h3 class="timeline-header">Proses Selesai</h3>
                                    <div class="timeline-body">
                                        Surat telah selesai dan siap digunakan.
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($suratKeluar->status_validasi == 'ditolak')
                            <div>
                                <i class="fas fa-times-circle bg-danger"></i>
                                <div class="timeline-item">
                                    <h3 class="timeline-header">Surat Ditolak</h3>
                                    <div class="timeline-body">
                                        Ditolak oleh: <strong>{{ $suratKeluar->divalidasiOleh->name ?? '-' }}</strong><br>
                                        Catatan: {{ $suratKeluar->catatan_validasi ?? '-' }}
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div>
                            <i class="far fa-clock bg-gray"></i>
                        </div>
                    </div>
                </div>
            </div>

        </div>
</div> @endsection
