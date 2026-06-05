@extends('layouts.adminlte')

@section('title', 'Tambah Surat Masuk')
@section('page-title', 'Tambah Surat Masuk')

@section('content')
    <div class="card card-success shadow-sm">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-inbox"></i> Formulir Surat Masuk (Manual)</h3>
        </div>

        <form action="{{ route('surat.masuk.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="row">

                    <!-- KOLOM KIRI: Konten & Lampiran -->
                    <div class="col-md-8 border-right pr-4">
                        <div class="alert alert-info bg-light text-info border-info shadow-sm mb-4">
                            <h6 class="font-weight-bold mb-1"><i class="fas fa-info-circle"></i> Informasi Penggunaan Form
                            </h6>
                            <hr class="mt-1 mb-2 border-info">
                            <small>
                                Gunakan form ini **hanya** untuk mencatat surat fisik/digital dari **instansi eksternal**
                                (contoh: PCNU, MWC NU, Polsek, Kepala Desa). Surat dari sesama Ranting/PAC di dalam
                                ekosistem aplikasi ini akan masuk secara otomatis.
                            </small>
                        </div>

                        <div class="form-group">
                            <label class="text-success font-weight-bold"><i class="fas fa-align-left"></i> Ringkasan Isi /
                                Catatan Surat <span class="text-muted font-weight-normal">(Opsional)</span></label>
                            <textarea name="isi_surat" class="form-control @error('isi_surat') is-invalid @enderror" rows="12"
                                placeholder="Tuliskan poin-poin penting, instruksi, atau ringkasan isi surat di sini untuk memudahkan pencarian nanti...">{{ old('isi_surat') }}</textarea>
                            @error('isi_surat')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mt-4 p-3 border rounded bg-light">
                            <label class="text-secondary"><i class="fas fa-file-upload"></i> File Scan / Foto Surat Asli
                                <span class="text-muted font-weight-normal">(Opsional)</span></label>
                            <input type="file" name="lampiran"
                                class="form-control-file @error('lampiran') is-invalid @enderror"
                                accept=".pdf,.jpg,.jpeg,.png">
                            <small class="text-muted mt-2 d-block"><i class="fas fa-info-circle"></i> Maksimal ukuran file
                                2MB (Format: PDF, JPG, PNG). Sangat disarankan untuk melampirkan bukti fisik surat.</small>
                            @error('lampiran')
                                <span class="text-danger d-block mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- KOLOM KANAN: Metadata / Pengaturan Surat -->
                    <div class="col-md-4 pl-4">
                        <h5 class="text-secondary font-weight-bold mb-3 border-bottom pb-2"><i class="fas fa-sliders-h"></i>
                            Detail Surat</h5>

                        <div class="form-group">
                            <label>Asal Surat / Pengirim <span class="text-danger">*</span></label>
                            <input type="text" name="pengirim"
                                class="form-control @error('pengirim') is-invalid @enderror" value="{{ old('pengirim') }}"
                                required placeholder="Contoh: PCNU Gresik">
                            @error('pengirim')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Nomor Surat <span class="text-danger">*</span></label>
                            <input type="text" name="nomor_surat"
                                class="form-control font-weight-bold @error('nomor_surat') is-invalid @enderror"
                                value="{{ old('nomor_surat') }}" required placeholder="Sesuai fisik surat">
                            @error('nomor_surat')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Perihal <span class="text-danger">*</span></label>
                            <input type="text" name="perihal" class="form-control @error('perihal') is-invalid @enderror"
                                value="{{ old('perihal') }}" required placeholder="Contoh: Undangan Turba">
                            @error('perihal')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-6 form-group">
                                <label>Tgl Surat <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_surat"
                                    class="form-control form-control-sm @error('tanggal_surat') is-invalid @enderror"
                                    value="{{ old('tanggal_surat', date('Y-m-d')) }}" required>
                                @error('tanggal_surat')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6 form-group">
                                <label>Tgl Diterima <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_diterima"
                                    class="form-control form-control-sm bg-light @error('tanggal_diterima') is-invalid @enderror"
                                    value="{{ old('tanggal_diterima', date('Y-m-d')) }}" required>
                                @error('tanggal_diterima')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group border-top pt-3 mt-1">
                            <label>Status Pemrosesan <span class="text-danger">*</span></label>
                            <select name="status" class="form-control custom-select @error('status') is-invalid @enderror"
                                required>
                                <option value="baru" {{ old('status') == 'baru' ? 'selected' : '' }}>Baru (Belum
                                    Disposisi)</option>
                                <option value="diproses" {{ old('status') == 'diproses' ? 'selected' : '' }}>Diproses
                                    (Sedang Dikerjakan)</option>
                                <option value="selesai" {{ old('status') == 'selesai' ? 'selected' : '' }}>Selesai
                                    (Diarsipkan)</option>
                            </select>
                            @error('status')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                </div>
            </div>

            <div class="card-footer bg-light text-right">
                <a href="{{ route('surat.masuk.index') }}" class="btn btn-secondary mr-2"><i class="fas fa-times"></i>
                    Batal</a>
                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Simpan Surat Masuk</button>
            </div>
        </form>
    </div>
@endsection
