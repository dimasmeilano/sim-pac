@extends('layouts.adminlte')

@section('title', 'Edit Menu')
@section('page-title', 'Edit Menu: ' . $menu->title)

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Form Edit Menu</h3>
        </div>
        <form action="{{ route('menus.update', $menu) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Judul Menu <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                value="{{ old('title', $menu->title) }}" required>
                            @error('title')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Icon</label>
                            <input type="text" name="icon" class="form-control"
                                value="{{ old('icon', $menu->icon) }}">
                            <small class="text-muted">Contoh: fas fa-home, fas fa-users</small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Route / URL <span class="text-danger">*</span></label>
                            <input type="text" name="route" class="form-control @error('route') is-invalid @enderror"
                                value="{{ old('route', $menu->route) }}" required>
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
                                    <option value="{{ $parent->id }}"
                                        {{ $menu->parent_id == $parent->id ? 'selected' : '' }}>
                                        {{ $parent->title }}
                                    </option>
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
                                value="{{ old('permission_required', $menu->permission_required) }}">
                            <small class="text-muted">Kosongkan jika menu bisa diakses semua role</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Urutan</label>
                            <input type="number" name="urutan" class="form-control"
                                value="{{ old('urutan', $menu->urutan) }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="active" {{ $menu->status == 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="inactive" {{ $menu->status == 'inactive' ? 'selected' : '' }}>Nonaktif
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('menus.index') }}" class="btn btn-default">Batal</a>
            </div>
        </form>
    </div>
@endsection
