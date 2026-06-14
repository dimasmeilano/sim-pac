@extends('layouts.adminlte')

@section('title', 'Kelola Slider Banner')
@section('page-title', 'Pengaturan Slider / Banner')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm border-0 border-top border-primary border-3">
                <div class="card-header bg-white pt-4 pb-0 border-bottom-0">
                    <button class="btn btn-primary font-weight-bold" data-toggle="modal" data-target="#modalTambah">
                        <i class="fas fa-image mr-1"></i> Tambah Banner Baru
                    </button>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-hover">
                        <thead class="bg-light text-center">
                            <tr>
                                <th width="5%">No</th>
                                <th width="25%">Gambar Banner</th>
                                <th>Teks (Judul & Deskripsi)</th>
                                <th width="10%">Status</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sliders as $key => $item)
                                <tr>
                                    <td class="text-center align-middle">{{ $key + 1 }}</td>
                                    <td class="text-center align-middle">
                                        <img src="{{ asset('storage/' . $item->gambar) }}"
                                            class="img-fluid rounded shadow-sm"
                                            style="max-height: 100px; object-fit: cover;">
                                    </td>
                                    <td class="align-middle">
                                        <strong>{{ $item->judul ?? '(Tanpa Judul)' }}</strong><br>
                                        <small class="text-muted">{{ $item->deskripsi_singkat ?? '-' }}</small>
                                    </td>
                                    <td class="text-center align-middle">
                                        @if ($item->status_aktif)
                                            <span class="badge badge-success"><i class="fas fa-check"></i> Aktif</span>
                                        @else
                                            <span class="badge badge-secondary"><i class="fas fa-times"></i> Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        <button class="btn btn-sm btn-warning shadow-sm" data-toggle="modal"
                                            data-target="#modalEdit{{ $item->id }}" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('slider.destroy', $item->id) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Yakin ingin menghapus banner ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger shadow-sm" title="Hapus"><i
                                                    class="fas fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>

                                {{-- MODAL EDIT --}}
                                <div class="modal fade" id="modalEdit{{ $item->id }}" tabindex="-1" role="dialog"
                                    aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <form action="{{ route('slider.update', $item->id) }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-content border-0 shadow">
                                                <div class="modal-header bg-light">
                                                    <h5 class="modal-title font-weight-bold">Edit Banner</h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label>Ganti Gambar (Opsional)</label>
                                                        <input type="file" name="gambar"
                                                            class="form-control-file border p-2 rounded" accept="image/*">
                                                        <small class="text-muted">Abaikan jika tidak ingin mengubah
                                                            gambar.</small>
                                                    </div>
                                                    <div class="form-group mt-3">
                                                        <label>Judul Teks</label>
                                                        <input type="text" name="judul" class="form-control"
                                                            value="{{ $item->judul }}">
                                                    </div>
                                                    <div class="form-group mt-3">
                                                        <label>Deskripsi Singkat</label>
                                                        <input type="text" name="deskripsi_singkat" class="form-control"
                                                            value="{{ $item->deskripsi_singkat }}">
                                                    </div>
                                                    <div class="form-group mt-3">
                                                        <label>Status Tayang</label>
                                                        <select name="status_aktif" class="form-control">
                                                            <option value="1"
                                                                {{ $item->status_aktif == 1 ? 'selected' : '' }}>Aktif
                                                                (Tampilkan)</option>
                                                            <option value="0"
                                                                {{ $item->status_aktif == 0 ? 'selected' : '' }}>Nonaktif
                                                                (Sembunyikan)</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-warning font-weight-bold"><i
                                                            class="fas fa-save mr-1"></i> Simpan</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center p-4">Belum ada data Slider Banner.</td>
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
        <div class="modal-dialog" role="document">
            <form action="{{ route('slider.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title font-weight-bold">Tambah Banner Baru</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Unggah Gambar <span class="text-danger">*</span></label>
                            <input type="file" name="gambar" class="form-control-file border p-2 rounded"
                                accept="image/*" required>
                            <small class="text-muted">Gunakan gambar landscape (lebar) resolusi tinggi agar tidak
                                pecah.</small>
                        </div>
                        <div class="form-group mt-3">
                            <label>Judul Teks (Opsional)</label>
                            <input type="text" name="judul" class="form-control"
                                placeholder="Contoh: Selamat Harlah IPNU">
                        </div>
                        <div class="form-group mt-3">
                            <label>Deskripsi Singkat (Opsional)</label>
                            <input type="text" name="deskripsi_singkat" class="form-control"
                                placeholder="Contoh: Mari rajut persatuan dan kesatuan...">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary font-weight-bold"><i
                                class="fas fa-upload mr-1"></i> Simpan Banner</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop
