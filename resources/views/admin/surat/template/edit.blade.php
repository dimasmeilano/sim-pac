@extends('layouts.adminlte')

@section('title', 'Edit Template Surat')
@section('page-title', 'Edit Template: ' . $template->nama)

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Form Edit Template Surat</h3>
        </div>
        <form action="{{ route('surat.template.update', $template) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">
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
                            <label>Lampiran</label>
                            <input type="text" name="lampiran" class="form-control"
                                value="{{ old('lampiran', $template->lampiran) }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Jenis Surat</label>
                            <select name="jenis_surat" class="form-control">
                                <option value="umum"
                                    {{ old('jenis_surat', $template->jenis_surat) == 'umum' ? 'selected' : '' }}>Umum
                                </option>
                                <option value="keputusan"
                                    {{ old('jenis_surat', $template->jenis_surat) == 'keputusan' ? 'selected' : '' }}>
                                    Keputusan</option>
                                <option value="pengesahan"
                                    {{ old('jenis_surat', $template->jenis_surat) == 'pengesahan' ? 'selected' : '' }}>
                                    Pengesahan</option>
                                <option value="tugas"
                                    {{ old('jenis_surat', $template->jenis_surat) == 'tugas' ? 'selected' : '' }}>Tugas
                                </option>
                                <option value="keterangan"
                                    {{ old('jenis_surat', $template->jenis_surat) == 'keterangan' ? 'selected' : '' }}>
                                    Keterangan</option>
                                <option value="undangan"
                                    {{ old('jenis_surat', $template->jenis_surat) == 'undangan' ? 'selected' : '' }}>
                                    Undangan</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
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
                            <label>Urutan</label>
                            <input type="number" name="urutan" class="form-control"
                                value="{{ old('urutan', $template->urutan ?? 0) }}">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Fields / Placeholder <span class="text-danger">*</span></label>
                    <input type="text" name="fields" class="form-control"
                        value="{{ old('fields', is_array($template->fields) ? implode(', ', $template->fields) : $template->fields) }}"
                        placeholder="Contoh: nomor_surat, perihal, nama_ketua, nama_sekretaris">
                    <small class="text-muted">Pisahkan dengan koma (,). Gunakan {nama_field} di konten template.</small>
                </div>

                <div class="form-group">
                    <label>Konten Template <span class="text-danger">*</span></label>
                    <textarea name="konten" class="form-control" rows="15" required>{{ old('konten', $template->konten) }}</textarea>
                    <small class="text-muted">
                        Gunakan {field_name} untuk placeholder yang akan diganti saat pembuatan surat.<br>
                        Contoh: <code>Kepada Yth. {tujuan} di {tempat}</code>
                    </small>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('surat.template.index') }}" class="btn btn-default">Batal</a>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            // Preview konten (opsional)
            function updatePreview() {
                let konten = document.querySelector('[name="konten"]').value;
                let previewDiv = document.getElementById('preview-konten');
                if (previewDiv) {
                    previewDiv.innerHTML = konten.replace(/\n/g, '<br>');
                }
            }

            document.querySelector('[name="konten"]')?.addEventListener('input', updatePreview);
            updatePreview();
        </script>
    @endpush
@endsection
