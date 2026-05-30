@extends('layouts.adminlte')

@section('title', 'Template Surat')
@section('page-title', 'Template Surat')

@section('content')
    <div class="card card-primary shadow-sm">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-file-alt"></i> Daftar Template Surat</h3>
            <div class="card-tools">
                @if (auth()->user()->hasRole('sekretaris_pac') ||
                        auth()->user()->hasRole('sekretaris_ranting') ||
                        auth()->user()->hasRole('sekretaris_komisariat') ||
                        auth()->user()->hasRole('super_admin'))
                    <a href="{{ route('surat.template.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Tambah Template
                    </a>
                @endif
                <a href="{{ route('surat.keluar.index') }}" class="btn btn-danger btn-sm">
                    <i class="fas fa-arrow-left"></i> Kembali ke Data Surat
                </a>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center" width="5%">No</th>
                            <th>Nama Template</th>
                            <th>Kode</th>
                            <th class="text-center">Jenis</th>
                            <th>Placeholder</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($templates as $key => $item)
                            <tr>
                                <td class="text-center">{{ $templates->firstItem() + $key }}</td>
                                <td>{{ $item->nama }}</td>
                                <td><code>{{ $item->kode }}</code></td>
                                <td class="text-center">
                                    @if ($item->jenis == 'keluar')
                                        <span class="badge badge-primary">Keluar</span>
                                    @else
                                        <span class="badge badge-info">Masuk</span>
                                    @endif
                                </td>
                                <td>
                                    @foreach ($item->placeholder ?? [] as $placeholder)
                                        <span class="badge badge-secondary mb-1">{{ $placeholder }}</span>
                                    @endforeach
                                </td>
                                <td class="text-center">
                                    @if ($item->status == 'aktif')
                                        <span class="badge badge-success"><i class="fas fa-check-circle"></i> Aktif</span>
                                    @else
                                        <span class="badge badge-danger"><i class="fas fa-times-circle"></i> Nonaktif</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if (auth()->user()->hasRole('sekretaris_pac') ||
                                            auth()->user()->hasRole('sekretaris_ranting') ||
                                            auth()->user()->hasRole('sekretaris_komisariat') ||
                                            auth()->user()->hasRole('super_admin'))
                                        <a href="{{ route('surat.template.edit', $item) }}" class="btn btn-warning btn-sm"
                                            title="Edit Template">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <form action="{{ route('surat.template.destroy', $item) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus Template"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus template ini secara permanen?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted text-sm"><i class="fas fa-lock"></i> Terkunci</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-folder-open fa-3x mb-3 d-block text-black-50"></i>
                                    Belum ada data template surat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer bg-white border-top">
            {{ $templates->links() }}
        </div>
    </div>
@endsection
