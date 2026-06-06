@extends('layouts.adminlte')

@section('title', 'Edit Inventaris')
@section('page-title', 'Edit Data: ' . $inventaris->nama_barang)

@section('content')
    <div class="card card-warning shadow-sm">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-edit mr-1"></i> Form Edit Barang</h3>
        </div>
        <form action="{{ route('inventaris.update', $inventaris) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Kode Barang <span class="text-danger">*</span></label>
                            <input type="text" name="kode_barang" class="form-control"
                                value="{{ old('kode_barang', $inventaris->kode_barang) }}" required>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group">
                            <label>Nama Barang <span class="text-danger">*</span></label>
                            <input type="text" name="nama_barang" class="form-control"
                                value="{{ old('nama_barang', $inventaris->nama_barang) }}" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Jumlah Unit <span class="text-danger">*</span></label>
                            <input type="number" name="jumlah" class="form-control"
                                value="{{ old('jumlah', $inventaris->jumlah) }}" min="1" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Kondisi Barang <span class="text-danger">*</span></label>
                            <select name="kondisi" class="form-control" required>
                                <option value="baik" {{ $inventaris->kondisi == 'baik' ? 'selected' : '' }}>Kondisi Baik
                                </option>
                                <option value="rusak_ringan" {{ $inventaris->kondisi == 'rusak_ringan' ? 'selected' : '' }}>
                                    Rusak Ringan</option>
                                <option value="rusak_berat" {{ $inventaris->kondisi == 'rusak_berat' ? 'selected' : '' }}>
                                    Rusak Berat</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Tahun Perolehan</label>
                            <input type="text" name="tahun_perolehan" class="form-control"
                                value="{{ old('tahun_perolehan', $inventaris->tahun_perolehan) }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Sumber Dana / Asal Barang</label>
                            <input type="text" name="sumber_dana" class="form-control"
                                value="{{ old('sumber_dana', $inventaris->sumber_dana) }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Foto Barang Baru (Opsional)</label>
                            <input type="file" name="foto_barang" class="form-control-file" accept="image/*">
                            @if ($inventaris->foto_barang)
                                <small class="text-info mt-2 d-block"><i class="fas fa-check"></i> Foto lama sudah ada.
                                    Biarkan kosong jika tidak ingin mengubah.</small>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Keterangan Tambahan / Lokasi Penyimpanan</label>
                    <textarea name="keterangan" class="form-control" rows="3">{{ old('keterangan', $inventaris->keterangan) }}</textarea>
                </div>
            </div>
            <div class="card-footer text-right bg-light">
                <a href="{{ route('inventaris.index') }}" class="btn btn-default mr-2">Batal</a>
                <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Update Aset</button>
            </div>
        </form>
    </div>
@endsection
