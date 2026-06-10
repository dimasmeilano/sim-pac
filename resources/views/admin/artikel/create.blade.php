@extends('layouts.adminlte')

@section('title', 'Tulis Artikel Baru')
@section('page-title', 'Tulis Artikel Baru')

@section('content')
    <form action="{{ route('artikel.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row">
            {{-- KOLOM KIRI: EDITOR TEKS UTAMA --}}
            <div class="col-md-8">
                <div class="card shadow-sm border-0 border-top border-primary border-3">
                    <div class="card-body">
                        <div class="form-group">
                            <label>Judul Artikel <span class="text-danger">*</span></label>
                            <input type="text" name="judul"
                                class="form-control form-control-lg @error('judul') is-invalid @enderror"
                                value="{{ old('judul') }}" placeholder="Masukkan judul berita yang menarik..." required>
                            @error('judul')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Kategori Berita <span class="text-danger">*</span></label>
                            <select name="kategori_id" class="form-control @error('kategori_id') is-invalid @enderror"
                                required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach ($kategoris as $kat)
                                    <option value="{{ $kat->id }}"
                                        {{ old('kategori_id') == $kat->id ? 'selected' : '' }}>{{ $kat->nama_kategori }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kategori_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mt-3">
                            <label>Isi Artikel <span class="text-danger">*</span></label>
                            <textarea name="isi_artikel" class="form-control tinymce-editor" rows="15" placeholder="Mulai menulis berita...">{{ old('isi_artikel') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- CREDIT JURNALISTIK --}}
                <div class="card shadow-sm mt-3">
                    <div class="card-header bg-white">
                        <h3 class="card-title font-weight-bold"><i class="fas fa-users text-info mr-2"></i> Credit Title
                        </h3>
                    </div>
                    <div class="card-body row">
                        <div class="col-md-4 form-group">
                            <label>Kontributor (Penulis)</label>
                            {{-- Terkunci otomatis atas nama yang sedang login --}}
                            <input type="text" class="form-control" value="{{ auth()->user()->name }}" readonly>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Fotografer / Sumber Foto</label>
                            {{-- Bisa dihapus/diganti jika foto dari orang lain --}}
                            <input type="text" name="fotografer" class="form-control" value="{{ auth()->user()->name }}"
                                placeholder="Ketik sumber foto...">
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Editor</label>
                            {{-- Kosong karena belum masuk meja redaksi --}}
                            <input type="text" class="form-control" value="Belum di-review" disabled>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: PANEL AKSI --}}
            <div class="col-md-4">
                {{-- PANEL FOTO --}}
                <div class="card shadow-sm border-info border-top border-3">
                    <div class="card-body">
                        <label>Gambar Cover / Thumbnail</label>
                        <div class="alert alert-light border small text-muted">
                            <i class="fas fa-info-circle"></i> Jika foto dari tim internal, Anda bisa mengosongkannya agar
                            diisi oleh <strong>Fotografer</strong> nanti.
                        </div>
                        <input type="file" name="gambar_cover" class="form-control-file border p-2 rounded"
                            accept="image/*">
                    </div>
                </div>

                {{-- PANEL SUBMIT KONTRIBUTOR --}}
                <div class="card shadow-sm mt-3 border-primary border-top border-3">
                    <div class="card-body">
                        <button type="submit" name="simpan_draft" class="btn btn-secondary btn-block mb-2"><i
                                class="fas fa-save"></i> Simpan Draft</button>
                        <button type="submit" name="kirim_review" class="btn btn-primary btn-block font-weight-bold"><i
                                class="fas fa-paper-plane"></i> Kirim ke Meja Redaksi</button>
                        <a href="{{ route('artikel.index') }}" class="btn btn-light btn-block mt-3 border">Batal</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@stop

@push('scripts')
    {{-- PANGGIL LIBRARY TINYMCE --}}
    <script src="https://cdn.tiny.cloud/1/790oy87uninpb887jjodfajw2ivcmbi6dq4vzah5gguz6igm/tinymce/6/tinymce.min.js">
    </script>
    <script>
        tinymce.init({
            selector: 'textarea.tinymce-editor',
            height: 500,
            menubar: true,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'searchreplace', 'visualblocks', 'fullscreen',
                'insertdatetime', 'media', 'table', 'wordcount'
            ],
            toolbar: 'undo redo | blocks | bold italic underline | ' +
                'alignleft aligncenter alignright alignjustify | ' +
                'bullist numlist outdent indent | link image | removeformat',
            content_style: 'body { font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; font-size: 16px; line-height: 1.6; }',
            branding: false
        });
    </script>
@endpush
