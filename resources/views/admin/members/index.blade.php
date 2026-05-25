@extends('layouts.adminlte')

@section('title', 'Manajemen Anggota')
@section('page-title', 'Manajemen Anggota')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Anggota</h3>
            <div class="card-tools">
                <a href="{{ route('members.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Tambah Anggota
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Foto</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Organisasi</th> <!-- TAMBAHKAN -->
                        <th>NIK</th>
                        <th>No HP</th>
                        <th>Tgl Bergabung</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($members as $key => $member)
                        <tr>
                            <td>{{ $members->firstItem() + $key }}</td>
                            <td>
                                @if ($member->foto)
                                    <img src="{{ asset('storage/' . $member->foto) }}" alt="Foto" width="50"
                                        height="50" class="img-circle">
                                @else
                                    <img src="{{ asset('images/default-avatar.png') }}" alt="Foto" width="50"
                                        height="50" class="img-circle">
                                @endif
                            </td>
                            <td>{{ $member->name }}</td>
                            <td>{{ $member->email }}</td>
                            <td>
                                @if ($member->organization)
                                    {{ $member->organization->name }}
                                    <span class="badge badge-info">{{ strtoupper($member->organization->type) }}</span>
                                @else
                                    <span class="badge badge-secondary">-</span>
                                @endif
                            </td>
                            <td>{{ $member->nik ?? '-' }}</td>
                            <td>{{ $member->no_hp ?? '-' }}</td>
                            <td>{{ $member->tgl_bergabung ? date('d/m/Y', strtotime($member->tgl_bergabung)) : '-' }}</td>
                            <td>
                                @if ($member->status_anggota == 'aktif')
                                    <span class="badge badge-success">Aktif</span>
                                @elseif($member->status_anggota == 'nonaktif')
                                    <span class="badge badge-warning">Nonaktif</span>
                                @elseif($member->status_anggota == 'meninggal')
                                    <span class="badge badge-danger">Meninggal</span>
                                @else
                                    <span class="badge badge-secondary">Keluar</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('members.show', $member) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('members.edit', $member) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('members.destroy', $member) }}" method="POST"
                                    style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Yakin hapus anggota ini?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $members->links() }}
        </div>
    </div>
@endsection
