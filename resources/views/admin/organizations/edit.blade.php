@extends('layouts.adminlte')

@section('title', 'Edit Organisasi')
@section('page-title', 'Edit Organisasi: ' . $organization->name)

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Form Edit Organisasi</h3>
        </div>
        <form action="{{ route('organizations.update', $organization) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nama Organisasi <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $organization->name) }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tipe Organisasi <span class="text-danger">*</span></label>
                            <select name="type" class="form-control @error('type') is-invalid @enderror" required>
                                <option value="pac" {{ old('type', $organization->type) == 'pac' ? 'selected' : '' }}>PAC
                                </option>
                                <option value="ranting"
                                    {{ old('type', $organization->type) == 'ranting' ? 'selected' : '' }}>Ranting</option>
                                <option value="departemen"
                                    {{ old('type', $organization->type) == 'departemen' ? 'selected' : '' }}>Departemen
                                </option>
                                <option value="lembaga"
                                    {{ old('type', $organization->type) == 'lembaga' ? 'selected' : '' }}>Lembaga</option>
                            </select>
                            @error('type')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Jenis Organisasi <span class="text-danger">*</span></label>
                        <select name="jenis_organisasi" class="form-control" required>
                            <option value="ipnu"
                                {{ old('jenis_organisasi', $organization->jenis_organisasi) == 'ipnu' ? 'selected' : '' }}>
                                IPNU (Laki-laki)</option>
                            <option value="ippnu"
                                {{ old('jenis_organisasi', $organization->jenis_organisasi) == 'ippnu' ? 'selected' : '' }}>
                                IPPNU (Perempuan)</option>
                            <option value="bersama"
                                {{ old('jenis_organisasi', $organization->jenis_organisasi) == 'bersama' ? 'selected' : '' }}>
                                Bersama IPNU & IPPNU</option>
                        </select>
                        <small class="text-muted">Pilih jenis organisasi ini: IPNU (khusus putra), IPPNU (khusus putri),
                            atau Bersama</small>
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
                            <label>Parent Organisasi</label>
                            <select name="parent_id" class="form-control">
                                <option value="">- Tidak Punya Induk -</option>
                                @foreach ($parents as $parent)
                                    <option value="{{ $parent->id }}"
                                        {{ old('parent_id', $organization->parent_id) == $parent->id ? 'selected' : '' }}>
                                        {{ $parent->name }} ({{ strtoupper($parent->type) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Kontak / No Telepon</label>
                            <input type="text" name="kontak" class="form-control"
                                value="{{ old('kontak', $organization->kontak) }}">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Alamat</label>
                    <textarea name="alamat" class="form-control" rows="3">{{ old('alamat', $organization->alamat) }}</textarea>
                </div>
                <div class="row">

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Kop Surat IPNU</label>
                            <input type="file" name="kop_surat_ipnu" class="form-control" accept="image/*">
                            @if ($organization->kop_surat_ipnu)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $organization->kop_surat_ipnu) }}" alt="Kop IPNU"
                                        style="width: 100%; border: 1px solid #ddd;">
                                    <small class="text-muted">Kosongkan jika tidak ingin mengubah</small>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Kop Surat IPPNU</label>
                            <input type="file" name="kop_surat_ippnu" class="form-control" accept="image/*">
                            @if ($organization->kop_surat_ippnu)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $organization->kop_surat_ippnu) }}" alt="Kop IPPNU"
                                        style="width: 100%; border: 1px solid #ddd;">
                                    <small class="text-muted">Kosongkan jika tidak ingin mengubah</small>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Kop Surat Bersama</label>
                            <input type="file" name="kop_surat_bersama" class="form-control" accept="image/*">
                            @if ($organization->kop_surat_bersama)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $organization->kop_surat_bersama) }}" alt="Kop Bersama"
                                        style="width: 100%; border: 1px solid #ddd;">
                                    <small class="text-muted">Kosongkan jika tidak ingin mengubah</small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
    </div>
    <div class="card-footer">
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('organizations.index') }}" class="btn btn-default">Batal</a>
    </div>
    </form>
    </div>
@endsection
