@extends('layouts.adminlte')

@section('title', 'Setting Pengurus')
@section('page-title', 'Setting Pengurus: ' . $organization->name)

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Form Setting Pengurus Organisasi</h3>
            <div class="card-tools">
                <span class="badge badge-info">
                    Tipe: {{ $organization->type == 'pac' ? 'PAC' : 'Ranting' }} |
                    Jenis: {{ $organization->jenisOrganisasiText }}
                </span>
            </div>
        </div>
        <form action="{{ route('organizations.setting.update', $organization) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">

                <!-- ========== KETUA ========== -->
                <h5 class="mt-3 mb-3">🏛️ Ketua</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Ketua</label>
                            <select name="ketua_id" class="form-control">
                                <option value="">- Pilih Ketua -</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}"
                                        {{ $organization->ketua_id == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- ========== WAKIL KETUA (Min 3, Max 5) ========== -->
                <h5 class="mt-4 mb-3">👥 Wakil Ketua <small class="text-muted">(Minimal 3, Maksimal 5)</small></h5>
                <div class="row">
                    @for ($i = 1; $i <= 5; $i++)
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Wakil Ketua {{ $i }}</label>
                                <select name="wakil_ketua_{{ $i }}_id" class="form-control">
                                    <option value="">- Pilih Wakil Ketua {{ $i }} -</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"
                                            {{ $organization->{'wakil_ketua_' . $i . '_id'} == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endfor
                </div>

                <!-- ========== SEKRETARIS ========== -->
                <h5 class="mt-4 mb-3">📝 Sekretaris</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Sekretaris 1</label>
                            <select name="sekretaris_id" class="form-control">
                                <option value="">- Pilih Sekretaris 1 -</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}"
                                        {{ $organization->sekretaris_id == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- ========== WAKIL SEKRETARIS (Min 3, Max 5) ========== -->
                <h5 class="mt-4 mb-3">📋 Wakil Sekretaris <small class="text-muted">(Minimal 3, Maksimal 5)</small></h5>
                <div class="row">
                    @for ($i = 1; $i <= 5; $i++)
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Wakil Sekretaris {{ $i }}</label>
                                <select name="wakil_sekretaris_{{ $i }}_id" class="form-control">
                                    <option value="">- Pilih Wakil Sekretaris {{ $i }} -</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"
                                            {{ $organization->{'wakil_sekretaris_' . $i . '_id'} == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endfor
                </div>

                <!-- ========== BENDAHARA ========== -->
                <h5 class="mt-4 mb-3">💰 Bendahara</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Bendahara 1</label>
                            <select name="bendahara_id" class="form-control">
                                <option value="">- Pilih Bendahara 1 -</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}"
                                        {{ $organization->bendahara_id == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Role: bendahara_pac + permission view_keuangan + bisa validasi</small>
                        </div>
                    </div>
                </div>

                <!-- ========== WAKIL BENDAHARA (Sesuai Kebutuhan) ========== -->
                <h5 class="mt-4 mb-3">💵 Wakil Bendahara <small class="text-muted">(Sesuai kebutuhan)</small></h5>
                <div class="row">
                    @for ($i = 1; $i <= 3; $i++)
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Wakil Bendahara {{ $i }}</label>
                                <select name="wakil_bendahara_{{ $i }}_id" class="form-control">
                                    <option value="">- Pilih Wakil Bendahara {{ $i }} -</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"
                                            {{ $organization->{'wakil_bendahara_' . $i . '_id'} == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endfor
                </div>

            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('organizations.index') }}" class="btn btn-default">Batal</a>
            </div>
        </form>
    </div>
@endsection
