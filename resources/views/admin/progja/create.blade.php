@extends('layouts.adminlte')

@section('title', 'Tambah Program Kerja')
@section('page-title', 'Tambah Program Kerja Baru')

@section('content')
    <div class="card card-success shadow-sm">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-plus-square mr-1"></i> Form Tambah Program Kerja</h3>
        </div>
        <form action="{{ route('progja.store') }}" method="POST">
            @csrf
            <div class="card-body">
                {{-- KHUSUS SUPER ADMIN: Pilih Kepemilikan Progja --}}
                @if (auth()->user()->hasRole('super_admin'))
                    <div class="form-group border-bottom pb-3">
                        <label class="text-primary"><i class="fas fa-sitemap mr-1"></i> Kepemilikan Program Kerja <span
                                class="text-danger">*</span></label>
                        <select name="organization_id" class="form-control" required>
                            <option value="">-- Pilih Organisasi Pemilik --</option>
                            @foreach ($organizations as $org)
                                <option value="{{ $org->id }}">{{ $org->name }} ({{ strtoupper($org->type) }})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Sebagai Super Admin, Anda bebas membuat progja untuk PAC atau Ranting mana
                            saja.</small>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Nama Program Kerja <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Jenis Kegiatan <span class="text-danger">*</span></label>
                            <select name="jenis" class="form-control" required>
                                <option value="ipnu">IPNU (Khusus Laki-laki)</option>
                                <option value="ippnu">IPPNU (Khusus Perempuan)</option>
                                <option value="bersama">Bersama IPNU & IPPNU</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Estimasi Anggaran</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input type="number" name="estimasi_anggaran" class="form-control"
                                    placeholder="Contoh: 5000000" min="0">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="4">{{ old('deskripsi') }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="date" name="tgl_mulai"
                                class="form-control @error('tgl_mulai') is-invalid @enderror" value="{{ old('tgl_mulai') }}"
                                required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Tanggal Selesai <span class="text-danger">*</span></label>
                            <input type="date" name="tgl_selesai"
                                class="form-control @error('tgl_selesai') is-invalid @enderror"
                                value="{{ old('tgl_selesai') }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="planning" {{ old('status') == 'planning' ? 'selected' : '' }}>Perencanaan
                                </option>
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Berjalan</option>
                                <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Selesai
                                </option>
                                <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('progja.index') }}" class="btn btn-default">Batal</a>
            </div>
        </form>
    </div>
@endsection
