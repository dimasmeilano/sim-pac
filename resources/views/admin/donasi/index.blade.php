@extends('layouts.adminlte')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 text-gray-800">Manajemen Fundraising & Donasi</h1>
            <a href="{{ route('donasi.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Buat Campaign Baru
            </a>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Daftar Program Penggalangan Dana</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>Banner</th>
                                <th>Judul Program (Campaign)</th>
                                <th>Target Dana</th>
                                <th>Terkumpul</th>
                                <th>Periode</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($campaigns as $donasi)
                                <tr>
                                    <td class="text-center">
                                        @if ($donasi->gambar_banner)
                                            <img src="{{ asset('storage/' . $donasi->gambar_banner) }}" alt="Banner"
                                                width="80" class="rounded">
                                        @else
                                            <div class="bg-light text-muted d-flex align-items-center justify-content-center rounded"
                                                style="width: 80px; height: 50px;">
                                                <i class="fas fa-image"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $donasi->judul_campaign }}</strong><br>
                                        <small class="text-muted">{{ Str::limit($donasi->deskripsi, 50) }}</small>
                                    </td>
                                    <td>Rp {{ number_format($donasi->target_donasi, 0, ',', '.') }}</td>
                                    <td class="text-success font-weight-bold">Rp
                                        {{ number_format($donasi->terkumpul, 0, ',', '.') }}</td>
                                    <td>
                                        <small>
                                            {{ $donasi->tgl_mulai ? \Carbon\Carbon::parse($donasi->tgl_mulai)->format('d M Y') : '-' }}
                                            s/d <br>
                                            {{ $donasi->tgl_selesai ? \Carbon\Carbon::parse($donasi->tgl_selesai)->format('d M Y') : '-' }}
                                        </small>
                                    </td>
                                    <td>
                                        @if ($donasi->status == 'aktif')
                                            <span class="badge badge-success">Aktif</span>
                                        @elseif($donasi->status == 'selesai')
                                            <span class="badge badge-secondary">Selesai</span>
                                        @else
                                            <span class="badge badge-danger">Dibatalkan</span>
                                        @endif
                                    </td>
                                    <td>
                                        <!-- Tombol Show ini nanti penting untuk Bendahara memverifikasi uang masuk -->
                                        <a href="{{ route('donasi.show', $donasi->id) }}" class="btn btn-sm btn-info mb-1"
                                            title="Lihat Detail & Verifikasi">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('donasi.edit', $donasi->id) }}"
                                            class="btn btn-sm btn-warning mb-1" title="Edit Campaign">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Belum ada program penggalangan dana yang dibuat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $campaigns->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
