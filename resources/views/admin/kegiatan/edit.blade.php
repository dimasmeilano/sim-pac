@extends('layouts.adminlte')

@section('title', 'Edit Kegiatan')
@section('page-title', 'Edit Kegiatan: ' . $kegiatan->nama)

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Form Edit Kegiatan</h3>
        </div>
        <form action="{{ route('kegiatan.update', $kegiatan) }}" method="POST">
            @csrf
            @method('PUT')
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
                                    <option value="{{ $org->id }}"
                                        {{ old('organization_id', $kegiatan->organization_id) == $org->id ? 'selected' : '' }}>
                                        {{ $org->name }} ({{ strtoupper($org->type) }})
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
                                <input type="text" name="nama" class="form-control"
                                    value="{{ old('nama', $kegiatan->nama) }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Program Kerja Terkait</label>
                                <select name="program_kerja_id" class="form-control">
                                    <option value="">- Tidak Terkait Progja -</option>
                                    @foreach ($programKerja as $progja)
                                        <option value="{{ $progja->id }}"
                                            {{ old('program_kerja_id', $kegiatan->program_kerja_id) == $progja->id ? 'selected' : '' }}>
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
                                <input type="text" name="tempat" class="form-control"
                                    value="{{ old('tempat', $kegiatan->tempat) }}" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tanggal Mulai</label>
                                <input type="datetime-local" name="tgl_mulai" class="form-control"
                                    value="{{ date('Y-m-d\TH:i', strtotime($kegiatan->tgl_mulai)) }}" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tanggal Selesai</label>
                                <input type="datetime-local" name="tgl_selesai" class="form-control"
                                    value="{{ date('Y-m-d\TH:i', strtotime($kegiatan->tgl_selesai)) }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="rencana" {{ $kegiatan->status == 'rencana' ? 'selected' : '' }}>Rencana
                                    </option>
                                    <option value="berlangsung" {{ $kegiatan->status == 'berlangsung' ? 'selected' : '' }}>
                                        Berlangsung</option>
                                    <option value="selesai" {{ $kegiatan->status == 'selesai' ? 'selected' : '' }}>Selesai
                                    </option>
                                    <option value="batal" {{ $kegiatan->status == 'batal' ? 'selected' : '' }}>Batal
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Deskripsi</label>
                                <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi', $kegiatan->deskripsi) }}</textarea>
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
                                                {{ old('ketua_pelaksana_id', $kegiatan->ketua_pelaksana_id) == $user->id ? 'selected' : '' }}>
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
                                        <option value="internal"
                                            {{ old('mode_absensi', $kegiatan->mode_absensi) == 'internal' ? 'selected' : '' }}>
                                            🔒 Internal (Hanya anggota yang login)
                                        </option>
                                        <option value="public"
                                            {{ old('mode_absensi', $kegiatan->mode_absensi) == 'public' ? 'selected' : '' }}>
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
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('kegiatan.index') }}" class="btn btn-default">Batal</a>
                </div>
        </form>
    </div>
@endsection
