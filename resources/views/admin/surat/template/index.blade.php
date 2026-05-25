@extends('layouts.adminlte')

@section('title', 'Template Surat')
@section('page-title', 'Template Surat')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Template Surat</h3>
            <div class="card-tools">
                <a href="{{ route('surat.template.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Tambah Template
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Template</th>
                            <th>Kode</th>
                            <th>Jenis</th>
                            <th>Placeholder</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($templates as $key => $item)
                            <tr>
                                <td>{{ $templates->firstItem() + $key }}</td>
                                <td>{{ $item->nama }}</td>
                                <td><code>{{ $item->kode }}</code></td>
                                <td>
                                    @if ($item->jenis == 'keluar')
                                        <span class="badge badge-primary">Keluar</span>
                                    @else
                                        <span class="badge badge-info">Masuk</span>
                                    @endif
                                </td>
                                <td>
                                    @foreach ($item->placeholder ?? [] as $placeholder)
                                        <code>{{ $placeholder }}</code>
                                    @endforeach
                                </td>
                                <td>
                                    @if ($item->status == 'aktif')
                                        <span class="badge badge-success">Aktif</span>
                                    @else
                                        <span class="badge badge-danger">Nonaktif</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('surat.template.edit', $item) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('surat.template.destroy', $item) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                                </td>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $templates->links() }}
        </div>
    </div>
@endsection
