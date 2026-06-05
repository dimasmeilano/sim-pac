@extends('layouts.adminlte')

@section('title', 'Manajemen Kegiatan')
@section('page-title', 'Manajemen Kegiatan')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header">
            <h3 class="card-title">Daftar Kegiatan</h3>
            <div class="card-tools">
                {{-- Tombol Tambah hanya untuk Admin & Sekretaris --}}
                @hasanyrole('super_admin|sekretaris_pac|sekretaris_ranting')
                    <a href="{{ route('kegiatan.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus-circle mr-1"></i> Tambah Kegiatan
                    </a>
                @endhasanyrole

                <a href="{{ route('absensi.scan.form') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-qrcode mr-1"></i> Scan QR Absensi
                </a>
            </div>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-striped text-nowrap align-middle">
                <thead>
                    <tr>
                        <th style="width: 5%">No</th>
                        <th>Nama Kegiatan</th>
                        <th>Program Kerja</th>
                        <th>Tempat</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Hadir</th>
                        <th style="width: 15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($kegiatan as $key => $item)
                        <tr>
                            <td>{{ $kegiatan->firstItem() + $key }}</td>
                            <td class="font-weight-bold text-primary">{{ $item->nama }}</td>
                            <td>{{ $item->programKerja->nama ?? '-' }}</td>
                            <td>{{ $item->tempat }}</td>
                            <td>
                                {{ date('d/m/Y H:i', strtotime($item->tgl_mulai)) }}<br>
                                <small class="text-muted">s/d {{ date('d/m/Y H:i', strtotime($item->tgl_selesai)) }}</small>
                            </td>
                            <td>{!! $item->status_text !!}</td>
                            <td>
                                <span
                                    class="badge badge-success">{{ $item->absensi->where('status', 'hadir')->count() }}</span>
                                <small class="text-muted">/ {{ $item->absensi->count() }}</small>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('kegiatan.show', $item) }}" class="btn btn-info btn-sm"
                                        title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('kegiatan.download-qr', $item) }}" class="btn btn-secondary btn-sm"
                                        title="Download QR">
                                        <i class="fas fa-qrcode"></i>
                                    </a>

                                    {{-- Tombol Edit & Hapus dilindungi hak akses --}}
                                    @hasanyrole('super_admin|sekretaris_pac|sekretaris_ranting')
                                        <a href="{{ route('kegiatan.edit', $item) }}" class="btn btn-warning btn-sm"
                                            title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('kegiatan.destroy', $item) }}" method="POST"
                                            style="display:inline;" onsubmit="return confirm('Yakin hapus kegiatan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endhasanyrole
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-calendar-times mb-2" style="font-size: 32px;"></i><br>
                                Belum ada kegiatan yang terdaftar untuk tingkat organisasi Anda.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($kegiatan->hasPages())
            <div class="card-footer clearfix">
                <div class="float-right">
                    {{ $kegiatan->links('pagination::bootstrap-4') }}
                </div>
            </div>
        @endif
    </div>
@endsection
