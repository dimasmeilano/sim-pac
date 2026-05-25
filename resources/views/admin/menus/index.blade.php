@extends('layouts.adminlte')

@section('title', 'Menu Manager')
@section('page-title', 'Menu Manager')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Menu</h3>
            <div class="card-tools">
                <a href="{{ route('menus.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Tambah Menu
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Icon</th>
                        <th>Judul Menu</th>
                        <th>Route</th>
                        <th>Parent</th>
                        <th>Permission</th>
                        <th>Urutan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($menus as $menu)
                        <tr>
                            <td>{{ $menu->id }}</td>
                            <td><i class="{{ $menu->icon }}"></i></td>
                            <td>
                                {{ $menu->title }}
                                @if ($menu->children->count() > 0)
                                    <span class="badge badge-info">+{{ $menu->children->count() }}</span>
                                @endif
                            </td>
                            <td><code>{{ $menu->route }}</code></td>
                            <td>{{ $menu->parent ? $menu->parent->title : '-' }}</td>
                            <td>
                                @if ($menu->permission_required)
                                    <span class="badge badge-warning">{{ $menu->permission_required }}</span>
                                @else
                                    <span class="badge badge-secondary">-</span>
                                @endif
                            </td>
                            <td>{{ $menu->urutan }}</td>
                            <td>
                                @if ($menu->status == 'active')
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-danger">Nonaktif</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('menus.edit', $menu) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('menus.destroy', $menu) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Yakin hapus menu ini? Semua submenu juga akan terhapus!')">
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
