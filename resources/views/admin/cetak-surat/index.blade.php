@extends('layouts.adminlte')

@section('title', 'Cetak Surat')
@section('page-title', 'Cetak Surat')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Pilih Jenis Surat</h3>
            <div class="card-tools">
                @can('manage_surat')
                    <a href="{{ route('surat.template.index') }}" class="btn btn-info btn-sm">
                        <i class="fas fa-cog"></i> Kelola Template
                    </a>
                @endcan
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Surat</th>
                            <th>Kode</th>
                            <th>Klasifikasi</th>
                            <th>Lampiran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($templates as $key => $item)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $item->nama }}</td>
                                <td><code>{{ $item->kode }}</code></td>
                                <td>{{ $item->klasifikasi ?? '-' }}</td>
                                <td>{{ $item->lampiran ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('cetak-surat.create', $item->id) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-file-alt"></i> Buat Surat
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
