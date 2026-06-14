@extends('layouts.adminlte')

@section('title', 'Identitas Website')
@section('page-title', 'Pengaturan Identitas Website')

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 border-top border-success border-3">
                <div class="card-header bg-white">
                    <h3 class="card-title font-weight-bold"><i class="fas fa-cogs text-success mr-2"></i> Profil Resmi PAC
                        IPNU IPPNU</h3>
                </div>
                <form action="{{ route('identitas-web.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="card-body">

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Nama Website <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" name="nama_web" class="form-control"
                                    value="{{ $identitas->nama_web }}" required>
                                <small class="text-muted">Contoh: SIM PAC IPNU IPPNU Kebomas</small>
                            </div>
                        </div>

                        <div class="form-group row mt-3">
                            <label class="col-sm-3 col-form-label">Deskripsi Singkat</label>
                            <div class="col-sm-9">
                                <textarea name="deskripsi" class="form-control" rows="3"
                                    placeholder="Situs resmi informasi dan berita kegiatan PAC...">{{ $identitas->deskripsi }}</textarea>
                            </div>
                        </div>

                        <div class="form-group row mt-3">
                            <label class="col-sm-3 col-form-label">Alamat Sekretariat</label>
                            <div class="col-sm-9">
                                <textarea name="alamat" class="form-control" rows="2" placeholder="Alamat lengkap gedung sekre...">{{ $identitas->alamat }}</textarea>
                            </div>
                        </div>

                        <div class="form-group row mt-3">
                            <label class="col-sm-3 col-form-label">Email Resmi</label>
                            <div class="col-sm-9">
                                <input type="email" name="email" class="form-control" value="{{ $identitas->email }}"
                                    placeholder="pac@ipnuippnu.or.id">
                            </div>
                        </div>

                        <div class="form-group row mt-3">
                            <label class="col-sm-3 col-form-label">No. Telepon / WA</label>
                            <div class="col-sm-9">
                                <input type="text" name="telepon" class="form-control" value="{{ $identitas->telepon }}"
                                    placeholder="081234567890">
                            </div>
                        </div>

                        <hr>

                        <div class="form-group row mt-3">
                            <label class="col-sm-3 col-form-label">Logo Organisasi</label>
                            <div class="col-sm-9">
                                @if ($identitas->logo)
                                    <div class="mb-3">
                                        <img src="{{ asset('storage/' . $identitas->logo) }}" alt="Logo"
                                            class="img-fluid rounded border p-2" style="max-height: 150px;">
                                    </div>
                                @endif
                                <input type="file" name="logo" class="form-control-file border p-2 rounded"
                                    accept="image/*">
                                <small class="text-muted">Biarkan kosong jika tidak ingin mengubah logo. Format disarankan:
                                    PNG transparan.</small>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5 class="text-success font-weight-bold border-bottom pb-2 mb-3">
                                    <i class="fas fa-id-card mr-2"></i> Konten Profil Publik (Halaman Profil)
                                </h5>
                            </div>

                            <div class="col-md-12 form-group">
                                <label class="font-weight-bold">Sejarah Singkat PAC</label>
                                <textarea name="sejarah_singkat" class="form-control tinyMceEditor" rows="6"
                                    placeholder="Tulis sejarah lengkap di sini...">{{ old('sejarah_singkat', $identitas->sejarah_singkat ?? '') }}</textarea>
                            </div>

                            <div class="col-md-12 form-group mt-2">
                                <label class="font-weight-bold">Visi & Misi Organisasi</label>
                                <textarea name="visi_misi" class="form-control tinyMceEditor" rows="6"
                                    placeholder="Tulis visi misi lengkap di sini...">{{ old('visi_misi', $identitas->visi_misi ?? '') }}</textarea>
                            </div>

                            <div class="col-md-6 form-group mt-2">
                                <label class="font-weight-bold text-success">Makna Lambang IPNU</label>
                                <textarea name="makna_lambang_ipnu" class="form-control tinyMceEditor" rows="5">{{ old('makna_lambang_ipnu', $identitas->makna_lambang_ipnu ?? '') }}</textarea>
                            </div>
                            <div class="col-md-6 form-group mt-2">
                                <label class="font-weight-bold text-success">Makna Lambang IPPNU</label>
                                <textarea name="makna_lambang_ippnu" class="form-control tinyMceEditor" rows="5">{{ old('makna_lambang_ippnu', $identitas->makna_lambang_ippnu ?? '') }}</textarea>
                            </div>
                        </div>

                    </div>
                    <div class="card-footer text-right bg-light">
                        <button type="submit" class="btn btn-success font-weight-bold">
                            <i class="fas fa-save mr-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-4">
            <div class="alert alert-info shadow-sm">
                <h5><i class="icon fas fa-info-circle"></i> Informasi</h5>
                Data identitas ini akan digunakan secara otomatis pada bagian <strong>Header</strong>,
                <strong>Footer</strong>, dan tag meta SEO di halaman depan (publik) website Anda. Pastikan data yang
                dimasukkan valid dan menggunakan logo resolusi baik.
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.tiny.cloud/1/790oy87uninpb887jjodfajw2ivcmbi6dq4vzah5gguz6igm/tinymce/6/tinymce.min.js"
        referrerpolicy="origin"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            tinymce.init({
                selector: '.tinyMceEditor',
                height: 400,
                menubar: false,
                plugins: 'lists link table code',
                toolbar: 'undo redo | bold italic underline | alignleft aligncenter alignright justify | bullist numlist | table | removeformat',
                content_style: 'body { font-family: "Times New Roman", Times, serif; font-size: 15px; }'
            });
        });
    </script>
@endpush
