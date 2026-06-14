@extends('layouts.adminlte')

@section('title', 'Kelola Widget Sidebar')
@section('page-title', 'Pengaturan Widget HTML / Embed')

@section('content')
    <div class="row">
        <div class="col-md-10">
            <div class="card shadow-sm border-0 border-top border-primary border-3">
                <div class="card-header bg-white pt-4 pb-0 border-bottom-0">
                    <button class="btn btn-primary font-weight-bold" data-toggle="modal" data-target="#modalTambah">
                        <i class="fas fa-plus-square mr-1"></i> Tambah Widget Baru
                    </button>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-1"></i> Gunakan widget ini untuk menempelkan (embed) kode dari
                        YouTube, Google Maps, Facebook Fanspage, atau *script* eksternal lainnya yang akan tayang di sisi
                        kanan Beranda.
                    </div>
                    <table class="table table-bordered table-hover">
                        <thead class="bg-light text-center">
                            <tr>
                                <th width="5%">No</th>
                                <th width="10%">Urutan</th>
                                <th>Nama Widget</th>
                                <th width="15%">Status</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($widgets as $key => $item)
                                <tr>
                                    <td class="text-center align-middle">{{ $key + 1 }}</td>
                                    <td class="text-center align-middle"><span class="badge badge-dark">Posisi
                                            {{ $item->urutan }}</span></td>
                                    <td class="align-middle"><strong>{{ $item->nama_widget }}</strong></td>
                                    <td class="text-center align-middle">
                                        @if ($item->status_aktif)
                                            <span class="badge badge-success">Tayang</span>
                                        @else
                                            <span class="badge badge-secondary">Disembunyikan</span>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        <button class="btn btn-sm btn-warning shadow-sm" data-toggle="modal"
                                            data-target="#modalEdit{{ $item->id }}" title="Edit"><i
                                                class="fas fa-edit"></i></button>
                                        <form action="{{ route('widget.destroy', $item->id) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Yakin menghapus widget ini?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-danger shadow-sm"><i
                                                    class="fas fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>

                                {{-- MODAL EDIT --}}
                                <div class="modal fade" id="modalEdit{{ $item->id }}" tabindex="-1" role="dialog"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <form action="{{ route('widget.update', $item->id) }}" method="POST">
                                            @csrf @method('PUT')
                                            <div class="modal-content border-0 shadow">
                                                <div class="modal-header bg-light">
                                                    <h5 class="modal-title font-weight-bold">Edit Widget</h5>
                                                    <button type="button" class="close"
                                                        data-dismiss="modal"><span>&times;</span></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-8 form-group">
                                                            <label>Nama / Judul Widget</label>
                                                            <input type="text" name="nama_widget" class="form-control"
                                                                value="{{ $item->nama_widget }}" required>
                                                        </div>
                                                        <div class="col-md-4 form-group">
                                                            <label>Nomor Urut</label>
                                                            <input type="number" name="urutan" class="form-control"
                                                                value="{{ $item->urutan }}" required>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Kode HTML / Iframe Embed</label>
                                                        <textarea name="isi_html" class="form-control text-monospace bg-dark text-success" rows="6" required>{{ $item->isi_html }}</textarea>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Status</label>
                                                        <select name="status_aktif" class="form-control">
                                                            <option value="1"
                                                                {{ $item->status_aktif == 1 ? 'selected' : '' }}>Aktif
                                                                (Tayangkan)</option>
                                                            <option value="0"
                                                                {{ $item->status_aktif == 0 ? 'selected' : '' }}>Nonaktif
                                                                (Sembunyikan)</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer text-right">
                                                    <button type="submit" class="btn btn-warning font-weight-bold"><i
                                                            class="fas fa-save mr-1"></i> Simpan Perubahan</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center p-4">Belum ada Widget.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL TAMBAH --}}
    <div class="modal fade" id="modalTambah" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form action="{{ route('widget.store') }}" method="POST">
                @csrf
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title font-weight-bold">Tambah Widget Baru</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-8 form-group">
                                <label>Nama / Judul Widget</label>
                                <input type="text" name="nama_widget" class="form-control"
                                    placeholder="Contoh: Video Profil IPNU" required>
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Nomor Urutan Tampil</label>
                                <input type="number" name="urutan" class="form-control" value="1" required>
                            </div>
                        </div>
                        <div class="form-group mt-2">
                            <label>Kode HTML / Iframe Embed</label>
                            <textarea name="isi_html" class="form-control text-monospace bg-dark text-success" rows="6"
                                placeholder="<iframe src='...' width='100%'></iframe>" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer text-right">
                        <button type="submit" class="btn btn-primary font-weight-bold"><i class="fas fa-save mr-1"></i>
                            Simpan Widget</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop
