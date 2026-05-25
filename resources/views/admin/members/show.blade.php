@extends('layouts.adminlte')

@section('title', 'Detail Anggota')
@section('page-title', 'Detail Anggota')

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        @if ($member->foto)
                            <img class="profile-user-img img-fluid img-circle" src="{{ asset('storage/' . $member->foto) }}"
                                alt="Foto">
                        @else
                            <img class="profile-user-img img-fluid img-circle" src="{{ asset('images/default-avatar.png') }}"
                                alt="Foto">
                        @endif
                    </div>
                    <h3 class="profile-username text-center">{{ $member->name }}</h3>
                    <p class="text-muted text-center">
                        @foreach ($member->getRoleNames() as $role)
                            {{ ucfirst(str_replace('_', ' ', $role)) }}
                        @endforeach
                    </p>
                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>Email</b> <a class="float-right">{{ $member->email }}</a>
                        </li>
                        <li class="list-group-item">
                            <b>NIK</b> <a class="float-right">{{ $member->nik ?? '-' }}</a>
                        </li>
                        <li class="list-group-item">
                            <b>No HP</b> <a class="float-right">{{ $member->no_hp ?? '-' }}</a>
                        </li>
                        <li class="list-group-item">
                            <b>Status</b>
                            <a class="float-right">
                                @if ($member->status_anggota == 'aktif')
                                    <span class="badge badge-success">Aktif</span>
                                @elseif($member->status_anggota == 'nonaktif')
                                    <span class="badge badge-warning">Nonaktif</span>
                                @elseif($member->status_anggota == 'meninggal')
                                    <span class="badge badge-danger">Meninggal</span>
                                @else
                                    <span class="badge badge-secondary">Keluar</span>
                                @endif
                            </a>
                        </li>
                    </ul>
                    <a href="{{ route('members.edit', $member) }}" class="btn btn-primary btn-block">
                        <i class="fas fa-edit"></i> Edit Profil
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Informasi Lengkap</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Tempat, Tanggal Lahir</th>
                            <td>{{ $member->tempat_lahir ? $member->tempat_lahir . ', ' : '' }}
                                {{ $member->tanggal_lahir ? date('d/m/Y', strtotime($member->tanggal_lahir)) : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Jenis Kelamin</th>
                            <td>{{ $member->jk == 'L' ? 'Laki-laki' : ($member->jk == 'P' ? 'Perempuan' : '-') }}</td>
                        </tr>
                        <tr>
                            <th>Pendidikan</th>
                            <td>{{ $member->pendidikan ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Bergabung</th>
                            <td>{{ $member->tgl_bergabung ? date('d/m/Y', strtotime($member->tgl_bergabung)) : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Berhenti</th>
                            <td>{{ $member->tgl_berhenti ? date('d/m/Y', strtotime($member->tgl_berhenti)) : '-' }}</td>
                        </tr>

                        <tr>
                            <th width="30%">Organisasi</th>
                            <td>
                                @if ($member->organization)
                                    {{ $member->organization->name }}
                                    <span class="badge badge-info">{{ strtoupper($member->organization->type) }}</span>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
