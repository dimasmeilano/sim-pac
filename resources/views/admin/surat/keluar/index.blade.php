@extends('layouts.adminlte')

@section('title', 'Arsip Surat Keluar')
@section('page-title', 'Arsip Surat Keluar')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Surat Keluar</h3>
            <div class="card-tools">
                <!-- Tombol Tambah Data Manual (untuk surat yang sudah ada) -->
                <a href="{{ route('surat.keluar.create') }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-hand-holding"></i> Tambah Data Manual
                </a>
                <a href="{{ route('surat.template.index') }}" class="btn btn-info btn-sm">
                    <i class="fas fa-file-alt"></i> Kelola Template
                </a>
            </div>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nomor Surat</th>
                            <th>Perihal</th>
                            <th>Tujuan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($surat as $key => $item)
                            <tr>
                                <td>{{ $surat->firstItem() + $key }}</td>
                                <td><code>{{ $item->nomor_surat }}</code></td>
                                <td>{{ $item->perihal }}</td>
                                <td>{{ Str::limit($item->tujuan, 50) }}</td>
                                <td>{!! $item->status_text !!}</td>
                                <td>
                                    <a href="{{ route('surat.keluar.show', $item) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if ($item->status == 'draft')
                                        <a href="{{ route('surat.keluar.edit', $item) }}" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('surat.keluar.destroy', $item) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                    @if ($item->status == 'selesai')
                                        <a href="{{ route('surat.keluar.download', $item) }}"
                                            class="btn btn-success btn-sm">
                                            <i class="fas fa-download"></i> PDF
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Belum ada data surat keluar</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $surat->links() }}
        </div>
    </div>
@endsection
