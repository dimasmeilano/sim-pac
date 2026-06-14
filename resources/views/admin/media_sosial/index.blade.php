@extends('layouts.adminlte')

@section('title', 'Media Sosial Organisasi')
@section('page-title', 'Kelola Tautan Media Sosial')

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 border-top border-info border-3">
                <div class="card-header bg-white pt-4 pb-0 border-bottom-0">
                    <button class="btn btn-info font-weight-bold" data-toggle="modal" data-target="#modalTambah">
                        <i class="fas fa-plus mr-1"></i> Tambah Akun Medsos
                    </button>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-hover">
                        <thead class="bg-light text-center">
                            <tr>
                                <th width="10%">No</th>
                                <th>Platform</th>
                                <th>Tautan (URL)</th>
                                <th width="20%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($medsos as $key => $item)
                                <tr>
                                    <td class="text-center">{{ $key + 1 }}</td>
                                    <td>
                                        <strong>
                                            @if (strtolower($item->nama_platform) == 'instagram')
                                                <i class="fab fa-instagram text-danger mr-1"></i>
                                            @elseif(strtolower($item->nama_platform) == 'facebook')
                                                <i class="fab fa-facebook text-primary mr-1"></i>
                                            @elseif(strtolower($item->nama_platform) == 'youtube')
                                                <i class="fab fa-youtube text-danger mr-1"></i>
                                            @elseif(strtolower($item->nama_platform) == 'tiktok')
                                                <i class="fab fa-tiktok text-dark mr-1"></i>
                                            @else
                                                <i class="fas fa-link text-muted mr-1"></i>
                                            @endif
                                            {{ $item->nama_platform }}
                                        </strong>
                                    </td>
                                    <td><a href="{{ $item->url_link }}" target="_blank"
                                            class="text-muted">{{ $item->url_link }}</a></td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-warning shadow-sm" data-toggle="modal"
                                            data-target="#modalEdit{{ $item->id }}" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('media-sosial.destroy', $item->id) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Yakin ingin menghapus tautan medsos ini?')">
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
                                        <form action="{{ route('media-sosial.update', $item->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-content border-0 shadow">
                                                <div class="modal-header bg-light">
                                                    <h5 class="modal-title font-weight-bold">Edit Media Sosial</h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label>Nama Platform <span class="text-danger">*</span></label>
                                                        <select name="nama_platform" class="form-control" required>
                                                            <option value="Instagram"
                                                                {{ $item->nama_platform == 'Instagram' ? 'selected' : '' }}>
                                                                Instagram</option>
                                                            <option value="Facebook"
                                                                {{ $item->nama_platform == 'Facebook' ? 'selected' : '' }}>
                                                                Facebook</option>
                                                            <option value="YouTube"
                                                                {{ $item->nama_platform == 'YouTube' ? 'selected' : '' }}>
                                                                YouTube</option>
                                                            <option value="TikTok"
                                                                {{ $item->nama_platform == 'TikTok' ? 'selected' : '' }}>
                                                                TikTok</option>
                                                            <option value="Twitter/X"
                                                                {{ $item->nama_platform == 'Twitter/X' ? 'selected' : '' }}>
                                                                Twitter / X</option>
                                                            <option value="Website"
                                                                {{ $item->nama_platform == 'Website' ? 'selected' : '' }}>
                                                                Website Lainnya</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group mt-3">
                                                        <label>URL / Tautan Lengkap <span
                                                                class="text-danger">*</span></label>
                                                        <input type="url" name="url_link" class="form-control"
                                                            value="{{ $item->url_link }}" required>
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
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Belum ada tautan media sosial.</td>
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
            <form action="{{ route('media-sosial.store') }}" method="POST">
                @csrf
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title font-weight-bold">Tambah Akun Media Sosial</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nama Platform <span class="text-danger">*</span></label>
                            <select name="nama_platform" class="form-control" required>
                                <option value="">-- Pilih Platform --</option>
                                <option value="Instagram">Instagram</option>
                                <option value="Facebook">Facebook</option>
                                <option value="YouTube">YouTube</option>
                                <option value="TikTok">TikTok</option>
                                <option value="Twitter/X">Twitter / X</option>
                                <option value="Website">Website Lainnya</option>
                            </select>
                        </div>
                        <div class="form-group mt-3">
                            <label>URL / Tautan Lengkap <span class="text-danger">*</span></label>
                            <input type="url" name="url_link" class="form-control"
                                placeholder="https://instagram.com/ipnu_ippnu..." required>
                            <small class="text-muted">Pastikan menggunakan format http:// atau https://</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-info font-weight-bold"><i class="fas fa-save mr-1"></i>
                            Simpan Tautan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop
