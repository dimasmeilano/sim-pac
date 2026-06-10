@extends('layouts.adminlte')

@section('title', 'Tambah Kegiatan')
@section('page-title', 'Tambah Kegiatan Baru')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Form Tambah Kegiatan</h3>
        </div>
        <form action="{{ route('kegiatan.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="card-body">
                    {{-- KHUSUS SUPER ADMIN --}}
                    @if (auth()->user()->hasRole('super_admin'))
                        <div class="form-group border-bottom pb-3">
                            <label class="text-primary"><i class="fas fa-sitemap mr-1"></i> Organisasi Penyelenggara <span
                                    class="text-danger">*</span></label>
                            <select name="organization_id" class="form-control" required>
                                <option value="">-- Pilih Organisasi --</option>
                                @foreach ($organizations as $org)
                                    <option value="{{ $org->id }}">{{ $org->name }} ({{ strtoupper($org->type) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    {{-- Lanjut ke form Nama Kegiatan Anda yang asli... --}}
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Nama Kegiatan <span class="text-danger">*</span></label>
                                <input type="text" name="nama"
                                    class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}"
                                    required>
                                @error('nama')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Program Kerja Terkait</label>
                                <select name="program_kerja_id" class="form-control">
                                    <option value="">- Tidak Terkait Progja -</option>
                                    @foreach ($programKerja as $progja)
                                        <option value="{{ $progja->id }}"
                                            {{ old('program_kerja_id') == $progja->id ? 'selected' : '' }}>
                                            {{ $progja->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tempat <span class="text-danger">*</span></label>
                                <input type="text" name="tempat"
                                    class="form-control @error('tempat') is-invalid @enderror" value="{{ old('tempat') }}"
                                    required>
                                @error('tempat')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tanggal Mulai <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="tgl_mulai"
                                    class="form-control @error('tgl_mulai') is-invalid @enderror"
                                    value="{{ old('tgl_mulai') }}" required>
                                @error('tgl_mulai')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tanggal Selesai <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="tgl_selesai"
                                    class="form-control @error('tgl_selesai') is-invalid @enderror"
                                    value="{{ old('tgl_selesai') }}" required>
                                @error('tgl_selesai')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="rencana" {{ old('status') == 'rencana' ? 'selected' : '' }}>Rencana
                                    </option>
                                    <option value="berlangsung" {{ old('status') == 'berlangsung' ? 'selected' : '' }}>
                                        Berlangsung</option>
                                    <option value="selesai" {{ old('status') == 'selesai' ? 'selected' : '' }}>Selesai
                                    </option>
                                    <option value="batal" {{ old('status') == 'batal' ? 'selected' : '' }}>Batal</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Deskripsi</label>
                                <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi') }}</textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Ketua Pelaksana</label>
                                    <select name="ketua_pelaksana_id" class="form-control">
                                        <option value="">- Pilih Ketua Pelaksana -</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}"
                                                {{ old('ketua_pelaksana_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Mode Absensi</label>
                                    <select name="mode_absensi" class="form-control">
                                        <option value="internal" {{ old('mode_absensi') == 'internal' ? 'selected' : '' }}>
                                            🔒 Internal (Hanya anggota yang login)
                                        </option>
                                        <option value="public" {{ old('mode_absensi') == 'public' ? 'selected' : '' }}>
                                            🌍 Publik (Siapa saja bisa absen tanpa login)
                                        </option>
                                    </select>
                                    <small class="text-muted">
                                        <strong>Internal:</strong> QR hanya bisa diakses anggota yang sudah login (untuk
                                        kegiatan internal)<br>
                                        <strong>Publik:</strong> QR bisa diakses siapa saja, cocok untuk undangan lintas PAC
                                        atau open event
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="{{ route('kegiatan.index') }}" class="btn btn-default">Batal</a>
                </div>
        </form>
    </div>
@endsection
