@extends('layouts.adminlte')

@section('title', 'Program Kerja')
@section('page-title', 'Program Kerja')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Program Kerja</h3>
            <div class="card-tools">
                <a href="{{ route('progja.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Tambah Progja
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Program</th>
                        <th>Jenis</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Progress</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($programKerja as $key => $progja)
                        <tr>
                            <td>{{ $programKerja->firstItem() + $key }}</td>
                            <td>{!! $progja->jenis_text !!}</td>
                            <td>
                                <strong>{{ $progja->nama }}</strong>
                                <br>
                                <small class="text-muted">{{ Str::limit($progja->deskripsi, 50) }}</small>
                            </td>
                            <td>
                                {{ date('d/m/Y', strtotime($progja->tgl_mulai)) }} -
                                {{ date('d/m/Y', strtotime($progja->tgl_selesai)) }}
                            </td>
                            <td>
                                @if ($progja->status == 'planning')
                                    <span class="badge badge-secondary">Perencanaan</span>
                                @elseif($progja->status == 'active')
                                    <span class="badge badge-success">Berjalan</span>
                                @elseif($progja->status == 'completed')
                                    <span class="badge badge-primary">Selesai</span>
                                @else
                                    <span class="badge badge-danger">Dibatalkan</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $total = $progja->tugas->count();
                                    $done = $progja->tugas->where('status', 'done')->count();
                                    $persen = $total > 0 ? round(($done / $total) * 100) : 0;
                                @endphp
                                <div class="progress">
                                    <div class="progress-bar" style="width: {{ $persen }}%">
                                        {{ $persen }}%
                                    </div>
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('progja.show', $progja) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-tasks"></i> Kanban
                                </a>
                                <a href="{{ route('progja.edit', $progja) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('progja.destroy', $progja) }}" method="POST"
                                    style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Yakin hapus progja ini?')">
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
            {{ $programKerja->links() }}
        </div>
    </div>
@endsection
