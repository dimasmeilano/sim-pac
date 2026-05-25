@extends('layouts.adminlte')

@section('title', 'Edit Anggota')
@section('page-title', 'Edit Anggota: ' . $member->name)

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Form Edit Anggota</h3>
        </div>
        <form action="{{ route('members.update', $member) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $member->name) }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email', $member->email) }}" required>
                            @error('email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Password (Kosongkan jika tidak diubah)</label>
                            <input type="password" name="password"
                                class="form-control @error('password') is-invalid @enderror">
                            @error('password')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>NIK (16 digit) <span class="text-danger">*</span></label>
                            <input type="text" name="nik" class="form-control @error('nik') is-invalid @enderror"
                                value="{{ old('nik', $member->nik) }}" maxlength="16" required>
                            @error('nik')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>No HP <span class="text-danger">*</span></label>
                            <input type="text" name="no_hp" class="form-control @error('no_hp') is-invalid @enderror"
                                value="{{ old('no_hp', $member->no_hp) }}" required>
                            @error('no_hp')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Organisasi</label>
                            <select name="organization_id" class="form-control">
                                <option value="">Pilih Organisasi</option>
                                @foreach ($organizations as $org)
                                    <option value="{{ $org->id }}"
                                        {{ old('organization_id', $member->organization_id) == $org->id ? 'selected' : '' }}>
                                        {{ $org->name }}
                                        @if ($org->type == 'pac')
                                            (PAC)
                                        @elseif($org->type == 'ranting')
                                            (Ranting)
                                        @elseif($org->type == 'departemen')
                                            (Departemen)
                                        @else
                                            (Lembaga)
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" class="form-control"
                                value="{{ old('tempat_lahir', $member->tempat_lahir) }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control"
                                value="{{ old('tanggal_lahir', $member->tanggal_lahir) }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Jenis Kelamin</label>
                            <select name="jk" class="form-control">
                                <option value="">Pilih</option>
                                <option value="L" {{ old('jk', $member->jk) == 'L' ? 'selected' : '' }}>Laki-laki
                                </option>
                                <option value="P" {{ old('jk', $member->jk) == 'P' ? 'selected' : '' }}>Perempuan
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Pendidikan</label>
                            <select name="pendidikan" class="form-control">
                                <option value="">Pilih</option>
                                <option value="SD"
                                    {{ old('pendidikan', $member->pendidikan) == 'SD' ? 'selected' : '' }}>SD</option>
                                <option value="SMP"
                                    {{ old('pendidikan', $member->pendidikan) == 'SMP' ? 'selected' : '' }}>SMP</option>
                                <option value="SMA"
                                    {{ old('pendidikan', $member->pendidikan) == 'SMA' ? 'selected' : '' }}>SMA</option>
                                <option value="D3"
                                    {{ old('pendidikan', $member->pendidikan) == 'D3' ? 'selected' : '' }}>D3</option>
                                <option value="S1"
                                    {{ old('pendidikan', $member->pendidikan) == 'S1' ? 'selected' : '' }}>S1</option>
                                <option value="S2"
                                    {{ old('pendidikan', $member->pendidikan) == 'S2' ? 'selected' : '' }}>S2</option>
                                <option value="S3"
                                    {{ old('pendidikan', $member->pendidikan) == 'S3' ? 'selected' : '' }}>S3</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Status Anggota</label>
                            <select name="status_anggota" class="form-control" required>
                                <option value="aktif"
                                    {{ old('status_anggota', $member->status_anggota) == 'aktif' ? 'selected' : '' }}>Aktif
                                </option>
                                <option value="nonaktif"
                                    {{ old('status_anggota', $member->status_anggota) == 'nonaktif' ? 'selected' : '' }}>
                                    Nonaktif</option>
                                <option value="meninggal"
                                    {{ old('status_anggota', $member->status_anggota) == 'meninggal' ? 'selected' : '' }}>
                                    Meninggal</option>
                                <option value="keluar"
                                    {{ old('status_anggota', $member->status_anggota) == 'keluar' ? 'selected' : '' }}>
                                    Keluar</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tanggal Bergabung</label>
                            <input type="date" name="tgl_bergabung" class="form-control"
                                value="{{ old('tgl_bergabung', $member->tgl_bergabung) }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Foto</label>
                            @if ($member->foto)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $member->foto) }}" alt="Foto" width="100"
                                        class="img-circle">
                                </div>
                            @endif
                            <input type="file" name="foto" class="form-control" accept="image/*">
                            <small class="text-muted">Format: JPG, PNG. Max 2MB</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('members.index') }}" class="btn btn-default">Batal</a>
            </div>
        </form>
    </div>
@endsection
