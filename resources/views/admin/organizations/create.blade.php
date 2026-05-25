@extends('layouts.adminlte')

@section('title', 'Tambah Organisasi')
@section('page-title', 'Tambah Organisasi Baru')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Form Tambah Organisasi</h3>
        </div>
        <form action="{{ route('organizations.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nama Organisasi <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name') }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tipe Organisasi <span class="text-danger">*</span></label>
                            <select name="type" class="form-control @error('type') is-invalid @enderror" required>
                                <option value="">Pilih Tipe</option>
                                <option value="pac" {{ old('type') == 'pac' ? 'selected' : '' }}>PAC (Pimpinan Anak
                                    Cabang)</option>
                                <option value="ranting" {{ old('type') == 'ranting' ? 'selected' : '' }}>Ranting</option>
                                <option value="departemen" {{ old('type') == 'departemen' ? 'selected' : '' }}>Departemen
                                </option>
                                <option value="lembaga" {{ old('type') == 'lembaga' ? 'selected' : '' }}>Lembaga</option>
                            </select>
                            @error('type')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Jenis Organisasi <span class="text-danger">*</span></label>
                            <select name="jenis_organisasi"
                                class="form-control @error('jenis_organisasi') is-invalid @enderror" required>
                                <option value="">Pilih Jenis</option>
                                <option value="ipnu" {{ old('jenis_organisasi') == 'ipnu' ? 'selected' : '' }}>IPNU
                                    (Laki-laki)</option>
                                <option value="ippnu" {{ old('jenis_organisasi') == 'ippnu' ? 'selected' : '' }}>IPPNU
                                    (Perempuan)</option>
                                <option value="bersama" {{ old('jenis_organisasi') == 'bersama' ? 'selected' : '' }}>
                                    Bersama IPNU & IPPNU</option>
                            </select>
                            @error('jenis_organisasi')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="text-muted">Pilih jenis organisasi ini: IPNU (khusus putra), IPPNU (khusus putri),
                                atau Bersama</small>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Periode Kepengurusan</label>
                    <input type="text" name="periode" class="form-control"
                        value="{{ old('periode', $organization->periode ?? 'XVI') }}"
                        placeholder="Contoh: XVI, XVII, XVIII">
                    <small class="text-muted">Contoh: XVI (16), XVII (17), dst. Ditulis dengan angka romawi.</small>
                </div>


                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Parent Organisasi (Induk)</label>
                            <select name="parent_id" class="form-control">
                                <option value="">- Tidak Punya Induk -</option>
                                @foreach ($parents as $parent)
                                    <option value="{{ $parent->id }}"
                                        {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                        {{ $parent->name }} ({{ strtoupper($parent->type) }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Hanya untuk Ranting (induknya PAC)</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Kontak / No Telepon</label>
                            <input type="text" name="kontak" class="form-control" value="{{ old('kontak') }}">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Alamat</label>
                    <textarea name="alamat" class="form-control" rows="3">{{ old('alamat') }}</textarea>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Kop Surat IPNU</label>
                            <input type="file" name="kop_surat_ipnu" class="form-control" accept="image/*">
                            @if (isset($organization) && $organization->kop_surat_ipnu)
                                <img src="{{ asset('storage/' . $organization->kop_surat_ipnu) }}" class="mt-2"
                                    style="width: 100%;">
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Kop Surat IPPNU</label>
                            <input type="file" name="kop_surat_ippnu" class="form-control" accept="image/*">
                            @if (isset($organization) && $organization->kop_surat_ippnu)
                                <img src="{{ asset('storage/' . $organization->kop_surat_ippnu) }}" class="mt-2"
                                    style="width: 100%;">
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Kop Surat Bersama</label>
                            <input type="file" name="kop_surat_bersama" class="form-control" accept="image/*">
                            @if (isset($organization) && $organization->kop_surat_bersama)
                                <img src="{{ asset('storage/' . $organization->kop_surat_bersama) }}" class="mt-2"
                                    style="width: 100%;">
                            @endif
                        </div>
                    </div>
                </div>

            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('organizations.index') }}" class="btn btn-default">Batal</a>
            </div>
        </form>
    </div>
@endsection
