@extends('layouts.adminlte')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 text-gray-800">Buat Program Donasi Baru</h1>
            <a href="{{ route('donasi.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="card shadow mb-4">
            <div class="card-body">
                <form action="{{ route('donasi.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-8">
                            @if (auth()->user()->hasRole('super_admin'))
                                <div class="form-group mb-3">
                                    <label>Pilih Organisasi / Ranting <span class="text-danger">*</span></label>
                                    <select name="organization_id"
                                        class="form-control @error('organization_id') is-invalid @enderror" required>
                                        <option value="">-- Pilih Ranting / PAC --</option>
                                        @foreach ($organizations as $org)
                                            <option value="{{ $org->id }}"
                                                {{ old('organization_id') == $org->id ? 'selected' : '' }}>
                                                {{ $org->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('organization_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endif
                            <div class="form-group mb-3">
                                <label>Judul Program (Campaign) <span class="text-danger">*</span></label>
                                <input type="text" name="judul_campaign"
                                    class="form-control @error('judul_campaign') is-invalid @enderror"
                                    value="{{ old('judul_campaign') }}"
                                    placeholder="Contoh: Penggalangan Dana Makesta Raya 2026" required>
                                @error('judul_campaign')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label>Deskripsi & Tujuan Pendanaan <span class="text-danger">*</span></label>
                                <textarea name="deskripsi" rows="5" class="form-control @error('deskripsi') is-invalid @enderror"
                                    placeholder="Jelaskan secara detail untuk apa dana ini akan digunakan..." required>{{ old('deskripsi') }}</textarea>
                                @error('deskripsi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label>Informasi Rekening & E-Wallet <span class="text-danger">*</span></label>
                                <textarea name="informasi_rekening" rows="3"
                                    class="form-control @error('informasi_rekening') is-invalid @enderror"
                                    placeholder="Contoh:&#10;BSI: 7123456789 (a.n PAC IPNU)&#10;DANA: 081234567890 (a.n Bendahara)" required>{{ old('informasi_rekening', $donasi->informasi_rekening ?? '') }}</textarea>
                                <small class="text-muted">Tuliskan semua rekening/QRIS yang bisa digunakan donatur untuk
                                    transfer pada program ini.</small>
                                @error('informasi_rekening')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group mb-3">
                                    <label>Target Dana (Rp)</label>
                                    <input type="number" name="target_donasi" class="form-control"
                                        value="{{ old('target_donasi') }}" placeholder="Contoh: 5000000">
                                    <small class="text-muted">Kosongkan jika target donasi tidak terbatas.</small>
                                </div>
                                <div class="col-md-6 form-group mb-3">
                                    <label>Status Campaign <span class="text-danger">*</span></label>
                                    <select name="status" class="form-control" required>
                                        <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Aktif
                                            (Menerima Donasi)</option>
                                        <option value="selesai" {{ old('status') == 'selesai' ? 'selected' : '' }}>Selesai
                                        </option>
                                        <option value="dibatalkan" {{ old('status') == 'dibatalkan' ? 'selected' : '' }}>
                                            Dibatalkan</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card bg-light mb-3">
                                <div class="card-body">
                                    <h6 class="font-weight-bold border-bottom pb-2">Pengaturan Waktu & Gambar</h6>

                                    <div class="form-group mb-3 mt-3">
                                        <label>Tanggal Mulai</label>
                                        <input type="date" name="tgl_mulai" class="form-control"
                                            value="{{ old('tgl_mulai') }}">
                                    </div>

                                    <div class="form-group mb-3">
                                        <label>Tanggal Selesai</label>
                                        <input type="date" name="tgl_selesai" class="form-control"
                                            value="{{ old('tgl_selesai') }}">
                                    </div>

                                    <div class="form-group mb-3">
                                        <label>Gambar Banner/Poster</label>
                                        <input type="file" name="gambar_banner" class="form-control-file"
                                            accept="image/*">
                                        <small class="text-danger mt-1 d-block">*Format JPG/PNG, max 2MB.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan & Publikasikan Program
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
