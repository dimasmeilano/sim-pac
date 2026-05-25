@extends('layouts.adminlte')

@section('title', 'Manajemen Organisasi')
@section('page-title', 'Manajemen Organisasi')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Organisasi</h3>
            <div class="card-tools">
                <a href="{{ route('organizations.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Tambah Organisasi
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Organisasi</th>
                        <th>Tipe</th>
                        <th>Parent</th>
                        <th>Kontak</th>
                        <th>Jumlah Anggota</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($organizations as $org)
                        <tr>
                            <td>{{ $org->id }}</td>
                            <td>
                                {{ $org->name }}
                                @if ($org->children->count() > 0)
                                    <span class="badge badge-info">+{{ $org->children->count() }} sub</span>
                                @endif
                            </td>
                            <td>
                                @if ($org->type == 'pac')
                                    <span class="badge badge-primary">PAC</span>
                                @elseif($org->type == 'ranting')
                                    <span class="badge badge-success">Ranting</span>
                                @elseif($org->type == 'departemen')
                                    <span class="badge badge-warning">Departemen</span>
                                @else
                                    <span class="badge badge-secondary">Lembaga</span>
                                @endif
                            </td>
                            <td>{{ $org->parent ? $org->parent->name : '-' }}</td>
                            <td>{{ $org->kontak ?? '-' }}</td>
                            <td>{{ $org->users_count ?? $org->users->count() }}</td>
                            <td>
                                <a href="{{ route('organizations.setting', $org) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-users-cog"></i>
                                </a>
                                <a href="{{ route('organizations.show', $org) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('organizations.edit', $org) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('organizations.destroy', $org) }}" method="POST"
                                    style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Yakin hapus organisasi ini?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
