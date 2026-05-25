@extends('layouts.adminlte')

@section('title', 'Tambah Menu')
@section('page-title', 'Tambah Menu Baru')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Form Tambah Menu</h3>
        </div>
        <form action="{{ route('menus.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Judul Menu <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                placeholder="Contoh: Dashboard" required>
                            @error('title')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Icon</label>
                            <input type="text" name="icon" class="form-control" value="fas fa-circle"
                                placeholder="Contoh: fas fa-home, fas fa-users">
                            <small class="text-muted">Font Awesome 6: <a href="https://fontawesome.com/icons"
                                    target="_blank">Cek icon</a></small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Route / URL <span class="text-danger">*</span></label>
                            <input type="text" name="route" class="form-control @error('route') is-invalid @enderror"
                                placeholder="Contoh: /dashboard, /admin/roles" required>
                            @error('route')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Parent Menu</label>
                            <select name="parent_id" class="form-control">
                                <option value="">- Root Menu (Main Menu) -</option>
                                @foreach ($parents as $parent)
                                    <option value="{{ $parent->id }}">{{ $parent->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Permission Required</label>
                            <input type="text" name="permission_required" class="form-control"
                                placeholder="Contoh: manage_role, view_anggota">
                            <small class="text-muted">Kosongkan jika menu bisa diakses semua role</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Urutan</label>
                            <input type="number" name="urutan" class="form-control" value="0">
                            <small class="text-muted">Semakin kecil semakin atas</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="active">Aktif</option>
                                <option value="inactive">Nonaktif</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('menus.index') }}" class="btn btn-default">Batal</a>
            </div>
        </form>
    </div>
@endsection
