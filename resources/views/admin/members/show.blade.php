@extends('layouts.adminlte')

@section('title', 'Detail Anggota')
@section('page-title', 'Detail Anggota')

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                {{-- Header Hijau --}}
                <div class="card-header bg-success text-white text-center">
                    <h3 class="card-title w-100 font-weight-bold mt-1">Kartu Profil</h3>
                </div>
                <div class="card-body box-profile pt-4">
                    <div class="text-center mb-3">
                        @if ($member->foto)
                            <img class="profile-user-img img-fluid img-circle shadow-sm"
                                src="{{ asset('storage/' . $member->foto) }}" alt="Foto"
                                style="width: 120px; height: 120px; object-fit: cover; border: 3px solid #28a745;">
                        @else
                            <img class="profile-user-img img-fluid img-circle shadow-sm"
                                src="{{ asset('images/default-avatar.png') }}" alt="Foto"
                                style="width: 120px; height: 120px; border: 3px solid #dee2e6;">
                        @endif
                    </div>

                    <h3 class="profile-username text-center font-weight-bold mb-1">{{ $member->name }}</h3>

                    <p class="text-muted text-center mb-4">
                        @foreach ($member->getRoleNames() as $role)
                            @if ($role == 'cbp')
                                <span class="badge bg-olive shadow-sm px-2 py-1">CBP</span>
                            @elseif($role == 'kpp')
                                <span class="badge bg-purple shadow-sm px-2 py-1">KPP</span>
                            @else
                                <span
                                    class="badge bg-secondary shadow-sm px-2 py-1">{{ ucfirst(str_replace('_', ' ', $role)) }}</span>
                            @endif
                        @endforeach
                    </p>

                    <ul class="list-group list-group-unbordered mb-4">
                        <li class="list-group-item border-top-0">
                            <i class="far fa-envelope text-muted mr-2"></i> <b>Email</b> <a
                                class="float-right text-dark">{{ $member->email }}</a>
                        </li>
                        <li class="list-group-item">
                            <i class="far fa-id-card text-muted mr-2"></i> <b>NIK</b> <a
                                class="float-right text-dark">{{ $member->nik ?? '-' }}</a>
                        </li>
                        <li class="list-group-item border-bottom-0">
                            <i class="fab fa-whatsapp text-success mr-2"></i> <b>No HP</b> <a
                                class="float-right text-dark">{{ $member->no_hp ?? '-' }}</a>
                        </li>
                    </ul>

                    <a href="{{ route('members.edit', $member) }}"
                        class="btn btn-outline-success btn-block font-weight-bold shadow-sm">
                        <i class="fas fa-edit mr-1"></i> Edit Data Anggota
                    </a>
                    <a href="{{ route('members.index') }}" class="btn btn-light btn-block shadow-sm mt-2 text-muted">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                {{-- Header Hijau --}}
                <div class="card-header bg-success text-white">
                    <h3 class="card-title font-weight-bold mt-1">
                        <i class="fas fa-info-circle mr-1"></i> Informasi Administratif Lengkap
                    </h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <tbody>
                                <tr>
                                    <th width="35%" class="align-middle bg-light pl-4"><i
                                            class="fas fa-sitemap text-muted mr-2"></i> Asal Organisasi</th>
                                    <td class="align-middle">
                                        @if ($member->organization)
                                            <span
                                                class="font-weight-bold text-dark">{{ $member->organization->name }}</span>
                                            <span
                                                class="badge badge-info ml-2">{{ strtoupper($member->organization->type) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="align-middle bg-light pl-4"><i class="fas fa-toggle-on text-muted mr-2"></i>
                                        Status Keanggotaan</th>
                                    <td class="align-middle">
                                        @if ($member->status_anggota == 'aktif')
                                            <span class="badge badge-success px-2 py-1"><i
                                                    class="fas fa-check-circle mr-1"></i> Aktif</span>
                                        @elseif($member->status_anggota == 'nonaktif')
                                            <span class="badge badge-warning px-2 py-1"><i
                                                    class="fas fa-exclamation-circle mr-1"></i> Nonaktif</span>
                                        @elseif($member->status_anggota == 'meninggal')
                                            <span class="badge badge-danger px-2 py-1">Meninggal</span>
                                        @else
                                            <span class="badge badge-secondary px-2 py-1">Keluar</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="align-middle bg-light pl-4"><i
                                            class="fas fa-map-marker-alt text-muted mr-2"></i> Tempat, Tanggal Lahir</th>
                                    <td class="align-middle">
                                        {{ $member->tempat_lahir ? $member->tempat_lahir . ', ' : '' }}
                                        {{ $member->tanggal_lahir ? \Carbon\Carbon::parse($member->tanggal_lahir)->translatedFormat('d F Y') : '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="align-middle bg-light pl-4"><i class="fas fa-venus-mars text-muted mr-2"></i>
                                        Jenis Kelamin</th>
                                    <td class="align-middle">
                                        @if ($member->jk == 'L' || $member->jk == 'Laki-laki')
                                            Laki-laki
                                        @elseif($member->jk == 'P' || $member->jk == 'Perempuan')
                                            Perempuan
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="align-middle bg-light pl-4"><i
                                            class="fas fa-graduation-cap text-muted mr-2"></i> Pendidikan Terakhir</th>
                                    <td class="align-middle">{{ $member->pendidikan ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="align-middle bg-light pl-4"><i
                                            class="fas fa-calendar-alt text-muted mr-2"></i> Tanggal Bergabung</th>
                                    <td class="align-middle font-weight-bold text-success">
                                        {{ $member->tgl_bergabung ? \Carbon\Carbon::parse($member->tgl_bergabung)->translatedFormat('d F Y') : '-' }}
                                    </td>
                                </tr>
                                @if ($member->tgl_berhenti)
                                    <tr>
                                        <th class="align-middle bg-light pl-4 text-danger"><i
                                                class="fas fa-calendar-times mr-2"></i> Tanggal Berhenti</th>
                                        <td class="align-middle font-weight-bold text-danger">
                                            {{ \Carbon\Carbon::parse($member->tgl_berhenti)->translatedFormat('d F Y') }}
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
