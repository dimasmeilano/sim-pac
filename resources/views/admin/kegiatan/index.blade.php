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
                        <th>Organisasi</th>
                        <th>Nama Kegiatan & Progja</th>
                        <th>Tempat & Tanggal</th>
                        <th>Status</th>
                        <th>Kehadiran</th>
                        <th style="width: 15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($kegiatan as $key => $item)
                        <tr>
                            <td>{{ $kegiatan->firstItem() + $key }}</td>

                            {{-- Nama Organisasi --}}
                            <td><span class="font-weight-bold">{{ $item->organization->name ?? 'Semua' }}</span></td>

                            {{-- Nama & Progja --}}
                            <td>
                                <strong class="text-primary">{{ $item->nama }}</strong><br>
                                <small class="text-muted"><i class="fas fa-umbrella"></i>
                                    {{ $item->programKerja->nama ?? 'Tanpa Payung Progja' }}</small>
                            </td>

                            {{-- Tempat & Waktu --}}
                            <td>
                                <i class="fas fa-map-marker-alt text-danger"></i> {{ $item->tempat }}<br>
                                <small class="text-muted"><i class="far fa-clock"></i>
                                    {{ date('d/m/Y', strtotime($item->tgl_mulai)) }}</small>
                            </td>

                            {{-- Status --}}
                            <td>{!! $item->status_text ?? ucfirst($item->status) !!}</td>

                            {{-- Kehadiran --}}
                            <td>
                                <span class="badge badge-success" title="Hadir"><i class="fas fa-user-check"></i>
                                    {{ $item->absensi->where('status', 'hadir')->count() }}</span>
                                <small class="text-muted">/ {{ $item->absensi->count() }} Total</small>
                            </td>

                            {{-- Tombol Aksi (Tetap sama seperti asli Anda) --}}
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="{{ route('kegiatan.show', $item) }}" class="btn btn-info btn-sm"
                                        title="Detail"><i class="fas fa-eye"></i></a>
                                    <a href="{{ route('kegiatan.download-qr', $item) }}" class="btn btn-secondary btn-sm"
                                        title="Download QR"><i class="fas fa-qrcode"></i></a>
                                    @hasanyrole('super_admin|sekretaris_pac|sekretaris_ranting')
                                        <a href="{{ route('kegiatan.edit', $item) }}" class="btn btn-warning btn-sm"><i
                                                class="fas fa-edit"></i></a>
                                        <form action="{{ route('kegiatan.destroy', $item) }}" method="POST"
                                            style="display:inline;" onsubmit="return confirm('Yakin hapus kegiatan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"><i
                                                    class="fas fa-trash"></i></button>
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
