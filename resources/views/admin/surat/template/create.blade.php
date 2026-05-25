@extends('layouts.adminlte')

@section('title', 'Tambah Template Surat')
@section('page-title', 'Tambah Template Surat')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Form Tambah Template</h3>
        </div>
        <form action="{{ route('surat.template.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nama Template <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Kode Template <span class="text-danger">*</span></label>
                            <input type="text" name="kode" class="form-control" placeholder="Contoh: SK-001" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Jenis Surat</label>
                            <select name="jenis" class="form-control" required>
                                <option value="keluar">Surat Keluar</option>
                                <option value="masuk">Surat Masuk</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Konten Template <span class="text-danger">*</span></label>
                    <textarea name="konten" class="form-control" rows="10"
                        placeholder="Gunakan {placeholder} untuk variabel dinamis. Contoh: {nama}, {tanggal}, {tujuan}" required></textarea>
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i>
                        Gunakan kurung kurawal <strong>{ }</strong> untuk placeholder.
                        Contoh: <strong>Kepada {tujuan}, di {tempat}</strong>
                    </small>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('surat.template.index') }}" class="btn btn-default">Batal</a>
            </div>
        </form>
    </div>
@endsection
