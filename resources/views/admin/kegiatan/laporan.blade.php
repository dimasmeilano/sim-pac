@extends('layouts.adminlte')

@section('title', 'Laporan Absensi')
@section('page-title', 'Laporan Absensi: ' . $kegiatan->nama)

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Peserta</h3>
            <div class="card-tools">
                <button onclick="window.print()" class="btn btn-primary btn-sm">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="text-center mb-4" id="kop-surat">
                <h3>PIMPINAN ANAK CABANG IPNU-IPPNU</h3>
                <h4>DAFTAR HADIR PESERTA KEGIATAN</h4>
                <p>{{ $kegiatan->nama }}<br>
                    {{ $kegiatan->tempat }}, {{ date('d/m/Y', strtotime($kegiatan->tgl_mulai)) }}</p>
                <hr>
            </div>

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Status</th>
                        <th>Tanda Tangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($absensi as $key => $absen)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>
                                @if ($absen->user_id)
                                    {{ $absen->user->name }}
                                @else
                                    {{ $absen->nama_peserta }} <small>(Umum)</small>
                                @endif
                            </td>
                            <td>
                                @if ($absen->status == 'hadir')
                                    <span class="badge badge-success">Hadir</span>
                                @elseif($absen->status == 'izin')
                                    <span class="badge badge-warning">Izin</span>
                                @elseif($absen->status == 'sakit')
                                    <span class="badge badge-info">Sakit</span>
                                @else
                                    <span class="badge badge-danger">Alpha</span>
                                @endif
                            </td>
                            <td style="width: 150px;"></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="row mt-5">
                <div class="col-md-6 offset-md-6 text-center">
                    <p>Mengetahui,<br>
                        Ketua Pelaksana</p>
                    <br><br><br>
                    <p>
                        <u>
                            @if ($kegiatan->ketuaPelaksana)
                                {{ $kegiatan->ketuaPelaksana->name }}
                            @else
                                _____________________
                            @endif
                        </u>
                    </p>
                    @if ($kegiatan->ketuaPelaksana)
                        <small>Nama: {{ $kegiatan->ketuaPelaksana->name }}</small>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {

            .main-sidebar,
            .main-header,
            .card-tools,
            .btn {
                display: none !important;
            }

            .content-wrapper {
                margin-left: 0 !important;
            }

            #kop-surat {
                display: block !important;
            }
        }
    </style>
@endsection
