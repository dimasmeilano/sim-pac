@extends('layouts.adminlte')

@section('title', 'Pengaturan Organisasi')
@section('page-title', 'Pengaturan Profil Organisasi')

@section('content')
    <form action="{{ route('organisasi.saya.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-12">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show shadow-sm">
                        <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
            </div>

            {{-- KOLOM KIRI: INFORMASI DASAR --}}
            <div class="col-md-6">
                <div class="card card-outline card-success shadow-sm">
                    <div class="card-header bg-white">
                        <h3 class="card-title font-weight-bold text-success"><i class="fas fa-info-circle mr-1"></i>
                            Informasi Dasar Organisasi</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Nama Organisasi</label>
                            <input type="text" class="form-control bg-light" value="{{ $organisasi->name }}" disabled>
                            <small class="text-muted">Nama organisasi bersifat statis sesuai yang disahkan oleh PAC.</small>
                        </div>

                        <div class="form-group">
                            <label>Alamat Sekretariat Resmi</label>
                            <textarea name="alamat" class="form-control" rows="3" placeholder="Masukkan alamat lengkap sekretariat..."
                                required>{{ old('alamat', $organisasi->alamat) }}</textarea>
                        </div>

                        <div class="form-group">
                            <label>Email Resmi Organisasi</label>
                            <input type="email" name="email" class="form-control"
                                value="{{ old('email', $organisasi->email) }}"
                                placeholder="contoh: ranting.sukorejo@gmail.com">
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nomor Kontak / WA Resmi</label>
                                    <input type="text" name="kontak" class="form-control"
                                        value="{{ old('kontak', $organisasi->kontak) }}" placeholder="082xxxxxxxxx">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Website / Sosial Media</label>
                                    <input type="text" name="website" class="form-control"
                                        value="{{ old('website', $organisasi->website) }}" placeholder="instagram.com/xxx">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: ATRIBUT & KELENGKAPAN SURAT --}}
            <div class="col-md-6">
                <div class="card card-outline card-warning shadow-sm">
                    <div class="card-header bg-white">
                        <h3 class="card-title font-weight-bold text-warning"><i class="fas fa-image mr-1"></i> Atribut &
                            Gambar Surat</h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info text-sm shadow-sm">
                            <i class="fas fa-info-circle"></i> File yang diupload maksimal berukuran <strong>2MB</strong>.
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label>Logo Organisasi</label>
                                    <input type="file" name="logo" class="form-control-file" accept="image/*">
                                    @if ($organisasi->logo)
                                        <div class="mt-2 text-center bg-light p-2 rounded border">
                                            <img src="{{ asset('storage/' . $organisasi->logo) }}" alt="Logo"
                                                class="img-fluid" style="max-height: 80px;">
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label>Stempel Resmi (.png)</label>
                                    <input type="file" name="stempel" class="form-control-file" accept="image/png">
                                    @if ($organisasi->stempel)
                                        <div class="mt-2 text-center bg-light p-2 rounded border">
                                            <img src="{{ asset('storage/' . $organisasi->stempel) }}" alt="Stempel"
                                                class="img-fluid" style="max-height: 80px;">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="form-group">
                            <label>Kop Surat IPNU (Gambar Header)</label>
                            <input type="file" name="kop_surat_ipnu" class="form-control-file" accept="image/*">
                            @if ($organisasi->kop_surat_ipnu)
                                <img src="{{ asset('storage/' . $organisasi->kop_surat_ipnu) }}" class="img-thumbnail mt-2"
                                    style="max-height: 50px;">
                            @endif
                        </div>

                        <div class="form-group">
                            <label>Kop Surat IPPNU (Gambar Header)</label>
                            <input type="file" name="kop_surat_ippnu" class="form-control-file" accept="image/*">
                            @if ($organisasi->kop_surat_ippnu)
                                <img src="{{ asset('storage/' . $organisasi->kop_surat_ippnu) }}" class="img-thumbnail mt-2"
                                    style="max-height: 50px;">
                            @endif
                        </div>

                        <div class="form-group">
                            <label>Kop Surat Bersama (Gambar Header)</label>
                            <input type="file" name="kop_surat_bersama" class="form-control-file" accept="image/*">
                            @if ($organisasi->kop_surat_bersama)
                                <img src="{{ asset('storage/' . $organisasi->kop_surat_bersama) }}"
                                    class="img-thumbnail mt-2" style="max-height: 50px;">
                            @endif
                        </div>

                    </div>
                    <div class="card-footer text-right bg-white">
                        <button type="submit" class="btn btn-success font-weight-bold shadow-sm">
                            <i class="fas fa-save mr-1"></i> Simpan Perubahan Atribut
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
