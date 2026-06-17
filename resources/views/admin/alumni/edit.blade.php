@extends('layouts.adminlte')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 text-gray-800">Edit Data Alumni</h1>
            <a href="{{ route('alumni.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="card shadow mb-4">
            <div class="card-body">
                <form action="{{ route('alumni.update', $alumni->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="text-primary border-bottom pb-2 mb-3">Informasi Dasar</h5>

                            @if (auth()->user()->hasRole('super_admin'))
                                <div class="form-group mb-3">
                                    <label>Pilih Organisasi / Ranting <span class="text-danger">*</span></label>
                                    <select name="organization_id"
                                        class="form-control @error('organization_id') is-invalid @enderror" required>
                                        @foreach ($organizations as $org)
                                            <option value="{{ $org->id }}"
                                                {{ old('organization_id', $alumni->organization_id) == $org->id ? 'selected' : '' }}>
                                                {{ $org->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('organization_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endif

                            <div class="form-group mb-3">
                                <label>Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="nama_lengkap"
                                    class="form-control @error('nama_lengkap') is-invalid @enderror"
                                    value="{{ old('nama_lengkap', $alumni->nama_lengkap) }}" required>
                                @error('nama_lengkap')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label>Asal Organisasi <span class="text-danger">*</span></label>
                                <select name="jenis_organisasi"
                                    class="form-control @error('jenis_organisasi') is-invalid @enderror" required>
                                    <option value="ipnu"
                                        {{ old('jenis_organisasi', $alumni->jenis_organisasi) == 'ipnu' ? 'selected' : '' }}>
                                        Majelis Alumni IPNU</option>
                                    <option value="ippnu"
                                        {{ old('jenis_organisasi', $alumni->jenis_organisasi) == 'ippnu' ? 'selected' : '' }}>
                                        Forum Alumni IPPNU</option>
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label>Tahun Angkatan / Bergabung</label>
                                <input type="number" name="tahun_angkatan" class="form-control"
                                    value="{{ old('tahun_angkatan', $alumni->tahun_angkatan) }}">
                            </div>

                            <div class="form-group mb-3">
                                <label>Jabatan Terakhir di Organisasi</label>
                                <input type="text" name="jabatan_terakhir" class="form-control"
                                    value="{{ old('jabatan_terakhir', $alumni->jabatan_terakhir) }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h5 class="text-primary border-bottom pb-2 mb-3">Networking & Kontak</h5>

                            <div class="form-group mb-3">
                                <label>Profesi Saat Ini</label>
                                <input type="text" name="profesi" class="form-control"
                                    value="{{ old('profesi', $alumni->profesi) }}">
                            </div>

                            <div class="form-group mb-3">
                                <label>Instansi / Tempat Kerja</label>
                                <input type="text" name="instansi_pekerjaan" class="form-control"
                                    value="{{ old('instansi_pekerjaan', $alumni->instansi_pekerjaan) }}">
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group mb-3">
                                    <label>No. WhatsApp</label>
                                    <input type="text" name="no_hp" class="form-control"
                                        value="{{ old('no_hp', $alumni->no_hp) }}">
                                </div>
                                <div class="col-md-6 form-group mb-3">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control"
                                        value="{{ old('email', $alumni->email) }}">
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label>Bersedia Menjadi Donatur Tetap/Insidental?</label>
                                <select name="bersedia_menjadi_donatur" class="form-control">
                                    <option value="1"
                                        {{ old('bersedia_menjadi_donatur', $alumni->bersedia_menjadi_donatur) == '1' ? 'selected' : '' }}>
                                        Ya, Bersedia</option>
                                    <option value="0"
                                        {{ old('bersedia_menjadi_donatur', $alumni->bersedia_menjadi_donatur) == '0' ? 'selected' : '' }}>
                                        Tidak / Belum Bersedia</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-4 mt-3">
                        <label>Alamat Domisili Sekarang</label>
                        <textarea name="alamat_domisili" rows="3" class="form-control">{{ old('alamat_domisili', $alumni->alamat_domisili) }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-warning btn-block font-weight-bold">
                        <i class="fas fa-save"></i> Perbarui Data Alumni
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
