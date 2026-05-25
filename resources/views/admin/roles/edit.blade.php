@extends('layouts.adminlte')

@section('title', 'Edit Role')
@section('page-title', 'Edit Role: ' . ucfirst(str_replace('_', ' ', $role->name)))

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Form Edit Role</h3>
        </div>
        <form action="{{ route('roles.update', $role) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="form-group">
                    <label>Nama Role</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $role->name) }}" required>
                    @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Permissions</label>
                    <div class="row">
                        @foreach ($permissions as $permission)
                            <div class="col-md-3">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                            {{ in_array($permission->name, $rolePermissions) ? 'checked' : '' }}>
                                        {{ $permission->name }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('roles.index') }}" class="btn btn-default">Batal</a>
            </div>
        </form>
    </div>
@endsection
