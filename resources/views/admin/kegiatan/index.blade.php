@extends('layouts.adminlte')

@section('title', 'Manajemen Kegiatan')
@section('page-title', 'Manajemen Kegiatan')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Kegiatan</h3>
            <div class="card-tools">
                <a href="{{ route('kegiatan.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Tambah Kegiatan
                </a>
                <a href="{{ route('absensi.scan.form') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-qrcode"></i> Scan QR Absensi
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Kegiatan</th>
                        <th>Program Kerja</th>
                        <th>Tempat</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Hadir</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($kegiatan as $key => $item)
                        <tr>
                            <td>{{ $kegiatan->firstItem() + $key }}</td>
                            <td>{{ $item->nama }}</td>
                            <td>{{ $item->programKerja->nama ?? '-' }}</td>
                            <td>{{ $item->tempat }}</td>
                            <td>{{ date('d/m/Y H:i', strtotime($item->tgl_mulai)) }}<br>
                                <small class="text-muted">s/d {{ date('d/m/Y H:i', strtotime($item->tgl_selesai)) }}</small>
                            </td>
                            <td>{!! $item->status_text !!}</td>
                            <td>
                                <span
                                    class="badge badge-success">{{ $item->absensi->where('status', 'hadir')->count() }}</span>
                                <small class="text-muted">/ {{ $item->absensi->count() }}</small>
                            </td>
                            <td>
                                <a href="{{ route('kegiatan.show', $item) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('kegiatan.edit', $item) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('kegiatan.download-qr', $item) }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-qrcode"></i>
                                </a>
                                <form action="{{ route('kegiatan.destroy', $item) }}" method="POST"
                                    style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Yakin hapus kegiatan ini?')">
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
            {{ $kegiatan->links() }}
        </div>
    </div>
@endsection
