@extends('layouts.adminlte')

@section('title', 'Detail Organisasi')
@section('page-title', 'Detail Organisasi')

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Informasi Organisasi</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Nama</th>
                            <td>{{ $organization->name }}</td>
                        </tr>
                        <tr>
                            <th>Tipe</th>
                            <td>
                                @if ($organization->type == 'pac')
                                    <span class="badge badge-primary">PAC</span>
                                @elseif($organization->type == 'ranting')
                                    <span class="badge badge-success">Ranting</span>
                                @elseif($organization->type == 'departemen')
                                    <span class="badge badge-warning">Departemen</span>
                                @else
                                    <span class="badge badge-secondary">Lembaga</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Induk Organisasi</th>
                            <td>{{ $organization->parent ? $organization->parent->name : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Kontak</th>
                            <td>{{ $organization->kontak ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Alamat</th>
                            <td>{{ $organization->alamat ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Jumlah Anggota</th>
                            <td>{{ $organization->users->count() }} orang</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Sub Organisasi</h3>
                </div>
                <div class="card-body">
                    @if ($organization->children->count() > 0)
                        <ul>
                            @foreach ($organization->children as $child)
                                <li>{{ $child->name }} ({{ strtoupper($child->type) }})</li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">Tidak ada sub organisasi</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
