@extends('layouts.adminlte')

@section('title', 'Daftar Pengajuan Ranting')
@section('page-title', 'Validasi Pengajuan Rekomendasi')

@section('content')
    <div class="card card-primary shadow-sm">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-inbox"></i> Daftar Antrean Pengajuan</h3>
        </div>
        <div class="card-body p-0">
            @if (session('success'))
                <div class="alert alert-success m-3">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger m-3">{{ session('error') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Tgl Pengajuan</th>
                            <th>Nama Organisasi</th>
                            <th>Ketua / Sekretaris</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pengajuan as $item)
                            <tr>
                                <td class="align-middle">{{ $item->created_at->format('d M Y H:i') }}</td>
                                <td class="align-middle">
                                    <strong>{{ $item->name }}</strong><br>
                                    <small class="text-muted text-uppercase">{{ $item->jenis_organisasi }} -
                                        {{ $item->type }}</small>
                                </td>
                                <td class="align-middle">
                                    <i class="fas fa-user-tie text-primary text-sm"></i> {{ $item->ketua_name }}<br>
                                    <i class="fas fa-user text-info text-sm"></i> {{ $item->sekretaris_name }}
                                </td>
                                <td class="text-center align-middle">
                                    @if ($item->status == 'menunggu_validasi')
                                        <span class="badge badge-warning"><i class="fas fa-clock"></i> Menunggu</span>
                                    @elseif ($item->status == 'disetujui')
                                        <span class="badge badge-success"><i class="fas fa-check"></i> Disetujui</span>
                                    @elseif ($item->status == 'revisi')
                                        <span class="badge badge-info"><i class="fas fa-edit"></i> Revisi</span>
                                    @else
                                        <span class="badge badge-danger"><i class="fas fa-times"></i> Ditolak</span>
                                    @endif
                                </td>
                                <td class="text-center align-middle">
                                    <a href="{{ route('pengajuan.show', $item->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-search"></i> Review Berkas
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Belum ada data pengajuan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            {{ $pengajuan->links('pagination::bootstrap-4') }}
        </div>
    </div>
@endsection
