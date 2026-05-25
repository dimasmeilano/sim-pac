@extends('layouts.adminlte')

@section('title', 'Arsip Surat Masuk')
@section('page-title', 'Arsip Surat Masuk')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Surat Masuk</h3>
            <div class="card-tools">
                <a href="{{ route('surat.masuk.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Tambah Surat Masuk
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nomor Surat</th>
                            <th>Pengirim</th>
                            <th>Perihal</th>
                            <th>Tanggal Diterima</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($surat as $key => $item)
                            <tr>
                                <td>{{ $surat->firstItem() + $key }}</td>
                                <td><code>{{ $item->nomor_surat }}</code></td>
                                <td>{{ $item->pengirim }}</td>
                                <td>{{ Str::limit($item->perihal, 50) }}</td>
                                <td>{{ date('d/m/Y', strtotime($item->tanggal_diterima)) }}</td>
                                <td>{!! $item->status_text !!}</td>
                                <td>
                                    <a href="{{ route('surat.masuk.show', $item) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('surat.masuk.edit', $item) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('surat.masuk.destroy', $item) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $surat->links() }}
        </div>
    </div>
@endsection
