@extends('layouts.adminlte')

@section('title', 'Detail Transaksi')
@section('page-title', 'Detail Transaksi')

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Informasi Transaksi</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Kode Transaksi</th>
                            <td><code>{{ $keuangan->kode_transaksi }}</code></td>
                        </tr>
                        <tr>
                            <th>Tanggal</th>
                            <td>{{ date('d/m/Y', strtotime($keuangan->tanggal)) }}</td>
                        </tr>
                        <tr>
                            <th>Judul</th>
                            <td>{{ $keuangan->judul }}</td>
                        </tr>
                        <tr>
                            <th>Jenis Organisasi</th>
                            <td>
                                @if ($keuangan->jenis_organisasi == 'ipnu')
                                    <span class="badge badge-primary">IPNU</span>
                                @elseif($keuangan->jenis_organisasi == 'ippnu')
                                    <span class="badge badge-danger">IPPNU</span>
                                @else
                                    <span class="badge badge-success">Bersama</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Jenis Transaksi</th>
                            <td>{!! $keuangan->jenis_text !!}</td>
                        </tr>
                        <tr>
                            <th>Nominal</th>
                            <td class="text-right">Rp {{ number_format($keuangan->nominal, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Kategori</th>
                            <td>{{ $keuangan->kategori ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Keterangan</th>
                            <td>{{ $keuangan->keterangan ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Bukti Transaksi</th>
                            <td>
                                @if ($keuangan->bukti_file)
                                    <a href="{{ asset('storage/' . $keuangan->bukti_file) }}" target="_blank"
                                        class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> Lihat Bukti
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Status Validasi</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th>Status</th>
                            <td>{!! $keuangan->status_validasi_text !!}</td>
                        </tr>
                        <tr>
                            <th>Dibuat Oleh</th>
                            <td>{{ $keuangan->createdBy->name ?? '-' }} <br>
                                <small>{{ $keuangan->created_at->format('d/m/Y H:i') }}</small>
                            </td>
                        </tr>
                        @if ($keuangan->status_validasi == 'disetujui' || $keuangan->status_validasi == 'ditolak')
                            <tr>
                                <th>Divalidasi Oleh</th>
                                <td>
                                    {{ $keuangan->validator->name ?? '-' }}
                                    <br><small>{{ $keuangan->tanggal_validasi ? date('d/m/Y H:i', strtotime($keuangan->tanggal_validasi)) : '-' }}</small>
                                </td>
                            </tr>
                        @endif
                        @if ($keuangan->catatan_validasi)
                            <tr>
                                <th>Catatan Validasi</th>
                                <td>{{ $keuangan->catatan_validasi }}</small></td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <a href="{{ route('keuangan.index') }}" class="btn btn-default">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
@endsection
