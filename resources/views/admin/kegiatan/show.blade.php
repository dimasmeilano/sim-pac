@extends('layouts.adminlte')

@section('title', 'Detail Kegiatan')
@section('page-title', $kegiatan->nama)

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Informasi Kegiatan</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Nama Kegiatan</th>
                            <td>{{ $kegiatan->nama }}</td>
                        </tr>
                        <tr>
                            <th>Program Kerja</th>
                            <td>{{ $kegiatan->programKerja->nama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Tempat</th>
                            <td>{{ $kegiatan->tempat }}</td>
                        </tr>
                        <tr>
                            <th>Waktu Pelaksanaan</th>
                            <td>{{ date('d/m/Y H:i', strtotime($kegiatan->tgl_mulai)) }} -
                                {{ date('d/m/Y H:i', strtotime($kegiatan->tgl_selesai)) }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>{!! $kegiatan->status_text !!}</td>
                        </tr>
                        <tr>
                            <th>Deskripsi</th>
                            <td>{{ $kegiatan->deskripsi ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th width="30%">Ketua Pelaksana</th>
                            <td>
                                @if ($kegiatan->ketuaPelaksana)
                                    <strong>{{ $kegiatan->ketuaPelaksana->name }}</strong><br>
                                    <small>{{ $kegiatan->ketuaPelaksana->email }}</small>
                                @else
                                    <span class="badge badge-warning">Belum ditentukan</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title">QR Code Absensi</h3>
                </div>
                <div class="card-body text-center">
                    @if ($kegiatan->qr_code && Storage::disk('public')->exists($kegiatan->qr_code))
                        <img src="{{ asset('storage/' . $kegiatan->qr_code) }}" alt="QR Code"
                            style="width: 200px; height: 200px;">
                        <br><br>
                        <a href="{{ route('kegiatan.download-qr', $kegiatan) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-download"></i> Download QR
                        </a>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> QR Code belum tersedia
                        </div>
                        <form action="{{ route('kegiatan.regenerate-qr', $kegiatan) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-sync"></i> Generate QR Code
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-users"></i> Daftar Absensi
                        <span class="badge badge-primary ml-2">{{ $total }} Peserta</span>
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('kegiatan.laporan', $kegiatan) }}" class="btn btn-success btn-sm">
                            <i class="fas fa-print"></i> Cetak Laporan
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>{{ $hadir }}</h3>
                                    <p>Hadir</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>{{ $izin }}</h3>
                                    <p>Izin</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ $sakit }}</h3>
                                    <p>Sakit</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3>{{ $alpha }}</h3>
                                    <p>Alpha</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Anggota</th>
                                <th>Waktu Absen</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($kegiatan->absensi as $key => $absen)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>
                                        @if ($absen->user_id)
                                            {{ $absen->user->name }}
                                        @else
                                            {{ $absen->nama_peserta }}
                                            <small class="text-muted d-block">(Peserta Umum:
                                                {{ $absen->asal_peserta ?? '-' }})</small>
                                        @endif
                                    </td>
                                    <td>{{ date('d/m/Y H:i', strtotime($absen->waktu_absen)) }}</td>
                                    <td>{!! $absen->status_text !!}</td>
                                    <td>{{ $absen->keterangan ?? '-' }}</td>
                                </tr>
                            @endforeach

                            @if ($kegiatan->absensi->count() == 0)
                                <tr>
                                    <td colspan="5" class="text-center">Belum ada data absensi</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
