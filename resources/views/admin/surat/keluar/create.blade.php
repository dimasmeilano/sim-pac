@extends('layouts.adminlte')

@section('title', 'Tambah Data Surat Manual')
@section('page-title', 'Tambah Data Surat Manual (Surat yang Sudah Ada)')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Form Input Surat Manual</h3>
            <div class="card-tools">
                <span class="badge badge-warning">
                    <i class="fas fa-info-circle"></i> Untuk surat yang sudah dibuat di luar sistem
                </span>
            </div>
        </div>
        <form action="{{ route('surat.keluar.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nomor Surat <span class="text-danger">*</span></label>
                            <input type="text" name="nomor_surat" class="form-control" value="{{ old('nomor_surat') }}"
                                required placeholder="Contoh: 001/PAC/SK/73/XVI/III/26">
                            <small class="text-muted">Masukkan nomor surat sesuai yang sudah ada</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Template Surat</label>
                            <select name="template_id" class="form-control">
                                <option value="">- Pilih Template (Opsional) -</option>
                                @foreach ($templates as $template)
                                    <option value="{{ $template->id }}">{{ $template->nama }} ({{ $template->kode }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Perihal <span class="text-danger">*</span></label>
                            <input type="text" name="perihal" class="form-control" value="{{ old('perihal') }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tujuan <span class="text-danger">*</span></label>
                            <input type="text" name="tujuan" class="form-control" value="{{ old('tujuan') }}" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tanggal Surat <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_surat" class="form-control"
                                value="{{ old('tanggal_surat', date('Y-m-d')) }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Lampiran</label>
                            <input type="file" name="lampiran" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                            <small class="text-muted">Upload file surat (PDF/JPG/PNG) jika ada</small>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Isi Surat <span class="text-danger">*</span></label>
                    <textarea name="isi_surat" class="form-control" rows="10" placeholder="Copy-paste isi surat di sini...">{{ old('isi_surat') }}</textarea>
                    <small class="text-muted">Copy-paste isi surat yang sudah ada (teks biasa)</small>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan ke Arsip
                </button>
                <a href="{{ route('surat.keluar.index') }}" class="btn btn-default">Batal</a>
            </div>
        </form>
    </div>
@endsection
