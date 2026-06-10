@extends('layouts.adminlte')

@section('title', 'Kategori Artikel')
@section('page-title', 'Kelola Kategori Berita')

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white pt-4 pb-0 border-bottom-0">
                    <button class="btn btn-primary font-weight-bold" data-toggle="modal" data-target="#modalTambah">
                        <i class="fas fa-plus mr-1"></i> Tambah Kategori
                    </button>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-hover">
                        <thead class="bg-light text-center">
                            <tr>
                                <th width="10%">No</th>
                                <th>Nama Kategori</th>
                                <th>Slug (URL)</th>
                                <th width="20%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kategoris as $key => $item)
                                <tr>
                                    <td class="text-center">{{ $key + 1 }}</td>
                                    <td><strong>{{ $item->nama_kategori }}</strong></td>
                                    <td class="text-muted">{{ $item->slug }}</td>
                                    <td class="text-center">
                                        {{-- Tombol Edit memanggil Modal Edit --}}
                                        <button class="btn btn-sm btn-warning shadow-sm" data-toggle="modal"
                                            data-target="#modalEdit{{ $item->id }}" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <form action="{{ route('kategori.destroy', $item->id) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger shadow-sm" title="Hapus"><i
                                                    class="fas fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>

                                {{-- MODAL EDIT UNTUK SETIAP BARIS --}}
                                <div class="modal fade" id="modalEdit{{ $item->id }}" tabindex="-1" role="dialog"
                                    aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <form action="{{ route('kategori.update', $item->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-content border-0 shadow">
                                                <div class="modal-header bg-light">
                                                    <h5 class="modal-title font-weight-bold">Edit Kategori</h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label>Nama Kategori <span class="text-danger">*</span></label>
                                                        <input type="text" name="nama_kategori" class="form-control"
                                                            value="{{ $item->nama_kategori }}" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-warning font-weight-bold"><i
                                                            class="fas fa-save mr-1"></i> Simpan Perubahan</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                {{-- AKHIR MODAL EDIT --}}

                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Belum ada kategori. Silakan tambahkan!</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL TAMBAH KATEGORI --}}
    <div class="modal fade" id="modalTambah" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="{{ route('kategori.store') }}" method="POST">
                @csrf
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title font-weight-bold">Tambah Kategori Baru</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nama Kategori <span class="text-danger">*</span></label>
                            <input type="text" name="nama_kategori" class="form-control"
                                placeholder="Contoh: Berita Utama, Opini, Kajian..." required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary font-weight-bold"><i class="fas fa-save mr-1"></i>
                            Simpan Kategori</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop
