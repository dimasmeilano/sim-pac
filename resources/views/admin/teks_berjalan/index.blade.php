@extends('layouts.adminlte')

@section('title', 'Teks Berjalan')
@section('page-title', 'Kelola Teks Berjalan (Marquee)')

@section('content')
    <div class="row">
        <div class="col-md-10">
            <div class="card shadow-sm border-0 border-top border-warning border-3">
                <div class="card-header bg-white pt-4 pb-0 border-bottom-0">
                    <button class="btn btn-warning font-weight-bold" data-toggle="modal" data-target="#modalTambah">
                        <i class="fas fa-bullhorn mr-1"></i> Tambah Pengumuman
                    </button>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-hover">
                        <thead class="bg-light text-center">
                            <tr>
                                <th width="5%">No</th>
                                <th>Isi Teks Pengumuman</th>
                                <th width="15%">Status</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($teks_berjalans as $key => $item)
                                <tr>
                                    <td class="text-center align-middle">{{ $key + 1 }}</td>
                                    <td class="align-middle">
                                        <em>"{{ $item->isi_teks }}"</em>
                                    </td>
                                    <td class="text-center align-middle">
                                        @if ($item->status_aktif)
                                            <span class="badge badge-success">Tayang</span>
                                        @else
                                            <span class="badge badge-secondary">Disembunyikan</span>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        <button class="btn btn-sm btn-info shadow-sm" data-toggle="modal"
                                            data-target="#modalEdit{{ $item->id }}" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('teks-berjalan.destroy', $item->id) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Yakin ingin menghapus teks ini?')">
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
                                        <form action="{{ route('teks-berjalan.update', $item->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-content border-0 shadow">
                                                <div class="modal-header bg-light">
                                                    <h5 class="modal-title font-weight-bold">Edit Pengumuman</h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label>Isi Teks / Pengumuman <span
                                                                class="text-danger">*</span></label>
                                                        <textarea name="isi_teks" class="form-control" rows="3" required>{{ $item->isi_teks }}</textarea>
                                                    </div>
                                                    <div class="form-group mt-3">
                                                        <label>Status Tayang di Web</label>
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
                                                            class="fas fa-save mr-1"></i> Simpan Perubahan</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center p-4">Belum ada pengumuman teks berjalan.</td>
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
            <form action="{{ route('teks-berjalan.store') }}" method="POST">
                @csrf
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title font-weight-bold">Tambah Pengumuman Baru</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Isi Teks / Pengumuman <span class="text-danger">*</span></label>
                            <textarea name="isi_teks" class="form-control" rows="3"
                                placeholder="Contoh: Selamat dan sukses atas diselenggarakannya Konferensi Anak Cabang ke-XI..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning font-weight-bold"><i
                                class="fas fa-paper-plane mr-1"></i> Simpan Pengumuman</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop
