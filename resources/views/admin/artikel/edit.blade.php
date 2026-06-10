@extends('layouts.adminlte')

@section('title', 'Ruang Redaksi - ' . Str::limit($artikel->judul, 20))
@section('page-title', 'Ruang Redaksi')

@section('content')
    <form action="{{ route('artikel.update', $artikel->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- ALERT JIKA ADA REVISI DARI EDITOR --}}
        @if ($artikel->status == 'revisi' && $artikel->catatan_editor)
            <div class="alert alert-danger shadow-sm">
                <h5><i class="icon fas fa-ban"></i> Artikel Dikembalikan oleh Editor!</h5>
                <strong>Catatan Revisi:</strong> {{ $artikel->catatan_editor }}
            </div>
        @endif

        <div class="row">
            {{-- KOLOM KIRI: EDITOR TEKS UTAMA --}}
            <div class="col-md-8">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="form-group">
                            <label>Judul Artikel</label>
                            {{-- Kunci form jika Fotografer, ATAU jika artikel sedang direview/publish (kecuali Editor yg buka) --}}
                            @php
                                $isReadonly =
                                    auth()->user()->hasRole('fotografer') ||
                                    (!auth()->user()->hasRole('editor') &&
                                        in_array($artikel->status, ['menunggu_review', 'publish']));
                            @endphp
                            <input type="text" name="judul" class="form-control form-control-lg"
                                value="{{ $artikel->judul }}" {{ $isReadonly ? 'readonly' : 'required' }}>
                        </div>

                        <div class="form-group">
                            <label>Kategori Berita</label>
                            <select name="kategori_id" class="form-control" {{ $isReadonly ? 'disabled' : 'required' }}>
                                @foreach ($kategoris as $kat)
                                    <option value="{{ $kat->id }}"
                                        {{ $artikel->kategori_id == $kat->id ? 'selected' : '' }}>{{ $kat->nama_kategori }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($isReadonly)
                                <input type="hidden" name="kategori_id" value="{{ $artikel->kategori_id }}">
                            @endif
                        </div>

                        <div class="form-group mt-3">
                            <label>Isi Artikel</label>
                            <textarea name="isi_artikel" class="form-control {{ $isReadonly ? '' : 'tinymce-editor' }}" rows="15"
                                {{ $isReadonly ? 'readonly' : '' }}>{!! $artikel->isi_artikel !!}</textarea>
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
                            <input type="text" name="kontributor" class="form-control"
                                value="{{ $artikel->kontributor }}" readonly>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Fotografer / Sumber</label>
                            <input type="text" name="fotografer" class="form-control"
                                value="{{ $artikel->fotografer ?? (auth()->user()->hasRole('fotografer') ? auth()->user()->name : '') }}"
                                {{ !auth()->user()->hasRole('fotografer') && !auth()->user()->hasRole('editor') ? 'readonly' : '' }}>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Editor</label>
                            <input type="text" name="editor" class="form-control" value="{{ $artikel->editor }}"
                                readonly>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: PANEL AKSI (BERUBAH SESUAI ROLE) --}}
            <div class="col-md-4">

                {{-- PANEL FOTO (Tampil untuk Fotografer, Kontributor saat awal, dan Editor) --}}
                @if (!in_array($artikel->status, ['publish']) || auth()->user()->hasRole('editor'))
                    <div class="card shadow-sm border-info border-top border-3">
                        <div class="card-body">
                            <label>Gambar Cover / Thumbnail</label>
                            @if ($artikel->gambar_cover)
                                <div class="mb-3">
                                    <img src="{{ asset('storage/' . $artikel->gambar_cover) }}"
                                        class="img-fluid rounded border">
                                </div>
                            @endif
                            <input type="file" name="gambar_cover" class="form-control-file border p-2 rounded"
                                accept="image/*">
                            @hasrole('fotografer')
                                <button type="submit" class="btn btn-info btn-block font-weight-bold mt-3"><i
                                        class="fas fa-upload"></i> Simpan Foto</button>
                            @endhasrole
                        </div>
                    </div>
                @endif

                {{-- PANEL KONTRIBUTOR --}}
                @hasrole('kontributor')
                    @if (in_array($artikel->status, ['draft', 'revisi']))
                        <div class="card shadow-sm mt-3 border-primary border-top border-3">
                            <div class="card-body">
                                <button type="submit" name="simpan_draft" class="btn btn-secondary btn-block mb-2"><i
                                        class="fas fa-save"></i> Simpan Draft</button>
                                <button type="submit" name="kirim_review" class="btn btn-primary btn-block font-weight-bold"><i
                                        class="fas fa-paper-plane"></i> Kirim ke Redaksi</button>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning text-center mt-3 shadow-sm">
                            <i class="fas fa-lock fa-2x mb-2"></i><br>
                            Artikel sedang di meja redaksi atau sudah terbit.
                        </div>
                    @endif
                @endhasrole

                {{-- PANEL EDITOR --}}
                @hasrole('editor')
                    <div class="card shadow-sm mt-3 border-danger border-top border-3">
                        <div class="card-body bg-light">
                            <h5 class="text-danger font-weight-bold border-bottom pb-2 mb-3">Tindakan Editor</h5>

                            @if ($artikel->status == 'publish')
                                {{-- JIKA SUDAH TERBIT --}}
                                <div class="alert alert-success text-center small mb-3">
                                    <i class="fas fa-check-circle"></i> Artikel ini sudah tayang di publik.
                                </div>
                                <button type="submit" name="update_biasa"
                                    class="btn btn-primary btn-block font-weight-bold mb-2"><i class="fas fa-save"></i> Simpan
                                    Perubahan</button>
                                <button type="submit" name="turunkan_tayangan"
                                    class="btn btn-outline-danger btn-block font-weight-bold"
                                    onclick="return confirm('Yakin ingin menarik berita ini dari publik dan mengembalikannya ke Draft?')"><i
                                        class="fas fa-arrow-down"></i> Kembalikan ke Draft</button>
                            @else
                                {{-- JIKA BELUM TERBIT (Masih Review/Draft/Revisi) --}}
                                <div class="form-group">
                                    <label>Catatan Revisi</label>
                                    <textarea name="catatan_editor" class="form-control" rows="3"
                                        placeholder="Isi jika tulisan ditolak/dikembalikan...">{{ $artikel->catatan_editor }}</textarea>
                                </div>

                                <button type="submit" name="setujui_publish"
                                    class="btn btn-success btn-block font-weight-bold mb-2"><i class="fas fa-check-circle"></i>
                                    Setujui & Publish</button>
                                <button type="submit" name="tolak_revisi" class="btn btn-danger btn-block font-weight-bold"><i
                                        class="fas fa-times-circle"></i> Tolak & Kembalikan</button>
                            @endif
                        </div>
                    </div>
                @endhasrole

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
