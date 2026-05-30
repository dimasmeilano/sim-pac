@extends('layouts.adminlte')

@section('title', 'Tambah Template Surat')
@section('page-title', 'Tambah Template Surat')

@section('content')
    <div class="card card-primary shadow-sm">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-plus-circle"></i> Form Tambah Template Master</h3>
        </div>

        <form action="{{ route('surat.template.store') }}" method="POST">
            @csrf
            <div class="card-body">

                <h5 class="text-primary font-weight-bold mb-3 border-bottom pb-2">Informasi Dasar</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nama Template <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control"
                                placeholder="Contoh: Surat Rekomendasi Pengesahan" value="{{ old('nama') }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Kode Template <span class="text-danger">*</span></label>
                            <input type="text" name="kode" class="form-control" placeholder="Contoh: SK-001"
                                value="{{ old('kode') }}" required>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Arus Surat</label>
                            <select name="jenis" class="form-control" required>
                                <option value="keluar" {{ old('jenis') == 'keluar' ? 'selected' : '' }}>Surat Keluar
                                </option>
                                <option value="masuk" {{ old('jenis') == 'masuk' ? 'selected' : '' }}>Surat Masuk</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Kategori (Penting untuk Form Dinamis)</label>
                            <select name="jenis_surat" class="form-control" required>
                                <option value="khusus" {{ old('jenis_surat') == 'khusus' ? 'selected' : '' }}>Khusus
                                    (Template Dinamis Berbasis Variabel)</option>
                                <option value="umum" {{ old('jenis_surat') == 'umum' ? 'selected' : '' }}>Umum (Template
                                    Teks Bebas)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="nonaktif" {{ old('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <h5 class="text-success font-weight-bold mb-3 border-bottom pb-2">Konfigurasi Desain & Form</h5>

                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label>Konten Template (HTML) <span class="text-danger">*</span></label>
                            <textarea name="konten" class="form-control text-monospace" rows="15"
                                placeholder="Masukkan kode HTML template di sini..." required>{{ old('konten') }}</textarea>
                            <small class="text-muted mt-2 d-block">
                                <i class="fas fa-info-circle"></i> Gunakan format <strong>{nama_variabel}</strong> untuk
                                data yang akan diisi oleh user.
                            </small>
                        </div>
                    </div>

                    <div class="col-md-4 bg-light p-3 rounded border">
                        <div class="form-group mb-0">
                            <label class="text-success">Aturan Form Input (JSON)</label>
                            <textarea name="fields" class="form-control text-monospace" rows="12"
                                placeholder='{
    "status_desa": "select:Ranting,Komisariat",
    "nama_desa": "text",
    "masa_bhakti": "text",
    "alamat": "textarea",
    "tanggal_pelaksanaan": "date"
}'>{{ old('fields') }}</textarea>

                            <div class="mt-3 text-sm text-muted">
                                <strong>Panduan Tipe Input:</strong>
                                <ul class="pl-3 mb-0 mt-1">
                                    <li><code>text</code> : Kolom teks biasa</li>
                                    <li><code>textarea</code> : Kotak teks besar</li>
                                    <li><code>date</code> : Pemilih tanggal</li>
                                    <li><code>select:A,B,C</code> : Dropdown otomatis</li>
                                    <li><code>hidden</code> : Disembunyikan (otomatis sistem)</li>
                                </ul>
                                <p class="mt-2 text-danger mb-0"><em>* Kosongkan jika Kategori = Umum</em></p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="card-footer text-right bg-white border-top">
                <a href="{{ route('surat.template.index') }}" class="btn btn-secondary mr-2">
                    <i class="fas fa-arrow-left"></i> Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Template
                </button>
            </div>
        </form>
    </div>
@endsection
