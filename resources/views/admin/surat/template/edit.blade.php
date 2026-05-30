@extends('layouts.adminlte')

@section('title', 'Edit Template Surat')
@section('page-title', 'Edit Template: ' . $template->nama)

@section('content')
    <div class="card card-warning shadow-sm">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-edit"></i> Form Edit Template Master</h3>
        </div>

        <form action="{{ route('surat.template.update', $template) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card-body">

                <h5 class="text-warning font-weight-bold mb-3 border-bottom pb-2">Informasi Dasar</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nama Template <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control"
                                value="{{ old('nama', $template->nama) }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Kode Template <span class="text-danger">*</span></label>
                            <input type="text" name="kode" class="form-control"
                                value="{{ old('kode', $template->kode) }}" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Klasifikasi</label>
                            <input type="text" name="klasifikasi" class="form-control"
                                value="{{ old('klasifikasi', $template->klasifikasi) }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Lampiran Bawaan</label>
                            <input type="text" name="lampiran" class="form-control"
                                value="{{ old('lampiran', $template->lampiran) }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Kategori / Jenis Surat</label>
                            <select name="jenis_surat" class="form-control">
                                <option value="umum"
                                    {{ old('jenis_surat', $template->jenis_surat) == 'umum' ? 'selected' : '' }}>Umum (Teks
                                    Bebas)</option>
                                <option value="keputusan"
                                    {{ old('jenis_surat', $template->jenis_surat) == 'keputusan' ? 'selected' : '' }}>
                                    Keputusan (Dinamis)</option>
                                <option value="pengesahan"
                                    {{ old('jenis_surat', $template->jenis_surat) == 'pengesahan' ? 'selected' : '' }}>
                                    Pengesahan (Dinamis)</option>
                                <option value="tugas"
                                    {{ old('jenis_surat', $template->jenis_surat) == 'tugas' ? 'selected' : '' }}>Tugas
                                    (Dinamis)</option>
                                <option value="keterangan"
                                    {{ old('jenis_surat', $template->jenis_surat) == 'keterangan' ? 'selected' : '' }}>
                                    Keterangan (Dinamis)</option>
                                <option value="undangan"
                                    {{ old('jenis_surat', $template->jenis_surat) == 'undangan' ? 'selected' : '' }}>
                                    Undangan</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="aktif" {{ old('status', $template->status) == 'aktif' ? 'selected' : '' }}>
                                    Aktif</option>
                                <option value="nonaktif"
                                    {{ old('status', $template->status) == 'nonaktif' ? 'selected' : '' }}>Nonaktif
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Urutan Tampil</label>
                            <input type="number" name="urutan" class="form-control"
                                value="{{ old('urutan', $template->urutan ?? 0) }}">
                        </div>
                    </div>
                </div>

                <h5 class="text-success font-weight-bold mb-3 border-bottom pb-2">Konfigurasi Desain & Form</h5>

                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label>Konten Template (HTML) <span class="text-danger">*</span></label>
                            <textarea name="konten" class="form-control text-monospace" rows="15" required>{{ old('konten', $template->konten) }}</textarea>
                            <small class="text-muted mt-2 d-block">
                                <i class="fas fa-info-circle"></i> Gunakan <strong>{nama_variabel}</strong> untuk
                                placeholder yang akan diubah oleh sistem atau diisi oleh user.
                            </small>
                        </div>

                        <div class="mt-3 p-3 border bg-light rounded d-none" id="preview-container">
                            <label class="text-muted">Preview Konten (HTML Mentah):</label>
                            <div id="preview-konten" style="max-height: 200px; overflow-y: auto; font-size: 14px;"></div>
                        </div>
                    </div>

                    <div class="col-md-4 bg-light p-3 rounded border">
                        <div class="form-group mb-0">
                            <label class="text-success">Aturan Form Input (JSON)</label>

                            @php
                                // Menampilkan data lama. Jika data di DB masih berupa array, ubah jadi teks JSON rapi.
                                // Jika data di DB ternyata cuma teks koma biasa (sisa versi lama), kita tampilkan apa adanya (nantinya harus diedit manual jadi JSON).
                                $fieldsData = $template->fields;
                                $fieldsValue = is_array($fieldsData)
                                    ? json_encode($fieldsData, JSON_PRETTY_PRINT)
                                    : $fieldsData;
                            @endphp

                            <textarea name="fields" class="form-control text-monospace" rows="12"
                                placeholder='{
    "status_desa": "select:Ranting,Komisariat",
    "nama_desa": "text",
    "tanggal_pelaksanaan": "date"
}'>{{ old('fields', $fieldsValue) }}</textarea>

                            <div class="mt-3 text-sm text-muted">
                                <strong>Panduan Tipe Input:</strong>
                                <ul class="pl-3 mb-0 mt-1">
                                    <li><code>text</code> : Kolom teks biasa</li>
                                    <li><code>textarea</code> : Kotak teks besar</li>
                                    <li><code>date</code> : Pemilih tanggal</li>
                                    <li><code>select:A,B,C</code> : Dropdown otomatis</li>
                                    <li><code>hidden</code> : Disembunyikan (otomatis sistem)</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="card-footer bg-white border-top text-right">
                <a href="{{ route('surat.template.index') }}" class="btn btn-secondary mr-2">
                    <i class="fas fa-arrow-left"></i> Batal
                </a>
                <button type="submit" class="btn btn-warning font-weight-bold">
                    <i class="fas fa-save"></i> Perbarui Template
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            // Preview konten 
            function updatePreview() {
                let konten = document.querySelector('[name="konten"]').value;
                let previewDiv = document.getElementById('preview-konten');
                let container = document.getElementById('preview-container');

                if (previewDiv && konten.trim() !== '') {
                    container.classList.remove('d-none');
                    // Tampilkan HTML apa adanya untuk melihat strukturnya (bukan render murni)
                    previewDiv.innerText = konten;
                } else if (container) {
                    container.classList.add('d-none');
                }
            }

            document.querySelector('[name="konten"]')?.addEventListener('input', updatePreview);
            updatePreview(); // Jalankan sekali saat load
        </script>
    @endpush
@endsection
