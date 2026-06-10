@extends('layouts.adminlte')

@section('title', 'Edit Role - ' . $user->name)
@section('page-title', 'Ubah Hak Akses: ' . $user->name)

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 border-top border-warning border-3">
                <form action="{{ route('user-role.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Centang jabatan yang ingin diberikan kepada
                            <strong>{{ $user->name }}</strong>. Satu orang bisa memiliki lebih dari satu jabatan.
                        </div>

                        <div class="form-group">
                            <label class="d-block font-weight-bold mb-3">Pilihan Hak Akses:</label>
                            <div class="row pl-3">
                                @foreach ($roles as $role)
                                    <div class="col-md-6 mb-2">
                                        <div class="custom-control custom-checkbox">
                                            <input class="custom-control-input" type="checkbox"
                                                id="role_{{ $role->id }}" name="roles[]" value="{{ $role->name }}"
                                                {{ $user->hasRole($role->name) ? 'checked' : '' }}>
                                            <label for="role_{{ $role->id }}" class="custom-control-label"
                                                style="cursor: pointer;">
                                                {{ strtoupper($role->name) }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-light text-right">
                        <a href="{{ route('user-role.index') }}" class="btn btn-secondary mr-2">Batal</a>
                        <button type="submit" class="btn btn-warning font-weight-bold"><i class="fas fa-save mr-1"></i>
                            Simpan Hak Akses</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop
