@extends('layouts.adminlte')

@section('title', 'Manajemen Hak Akses')
@section('page-title', 'Kelola Role User')

@section('content')
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="bg-light text-center">
                    <tr>
                        <th width="5%">No</th>
                        <th>Nama User</th>
                        <th>Email / Kontak</th>
                        <th>Hak Akses (Role) Saat Ini</th>
                        <th width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $key => $user)
                        <tr>
                            <td class="text-center">{{ $key + 1 }}</td>
                            <td><strong>{{ $user->name }}</strong></td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @forelse($user->roles as $role)
                                    <span class="badge badge-primary">{{ strtoupper($role->name) }}</span>
                                @empty
                                    <span class="badge badge-secondary">Anggota Biasa (Tanpa Role)</span>
                                @endforelse
                            </td>
                            <td class="text-center">
                                <a href="{{ route('user-role.edit', $user->id) }}" class="btn btn-sm btn-warning"
                                    title="Ubah Akses">
                                    <i class="fas fa-key"></i> Edit Role
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop
