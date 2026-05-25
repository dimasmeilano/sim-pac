@extends('layouts.adminlte')

@section('title', 'Edit Program Kerja')
@section('page-title', 'Edit Program Kerja: ' . $progja->nama)

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Form Edit Program Kerja</h3>
        </div>
        <form action="{{ route('progja.update', $progja) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="form-group">
                    <label>Nama Program Kerja <span class="text-danger">*</span></label>
                    <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                        value="{{ old('nama', $progja->nama) }}" required>
                    @error('nama')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="4">{{ old('deskripsi', $progja->deskripsi) }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tanggal Mulai</label>
                            <input type="date" name="tgl_mulai" class="form-control"
                                value="{{ old('tgl_mulai', $progja->tgl_mulai->format('Y-m-d')) }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tanggal Selesai</label>
                            <input type="date" name="tgl_selesai" class="form-control"
                                value="{{ old('tgl_selesai', $progja->tgl_selesai->format('Y-m-d')) }}">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="planning" {{ old('status', $progja->status) == 'planning' ? 'selected' : '' }}>
                            Perencanaan</option>
                        <option value="active" {{ old('status', $progja->status) == 'active' ? 'selected' : '' }}>Berjalan
                        </option>
                        <option value="completed" {{ old('status', $progja->status) == 'completed' ? 'selected' : '' }}>
                            Selesai</option>
                        <option value="cancelled" {{ old('status', $progja->status) == 'cancelled' ? 'selected' : '' }}>
                            Dibatalkan</option>
                    </select>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('progja.show', $progja) }}" class="btn btn-default">Batal</a>
            </div>
        </form>
    </div>
@endsection
