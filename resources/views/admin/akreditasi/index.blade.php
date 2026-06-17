@extends('layouts.adminlte')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 text-gray-800">Manajemen Akreditasi Organisasi</h1>

            @if (
                !auth()->user()->hasRole('super_admin') &&
                    !auth()->user()->hasAnyRole(['ketua_pac', 'sekretaris_pac']))
                <a href="{{ route('akreditasi.create') }}" class="btn btn-primary font-weight-bold">
                    <i class="fas fa-paper-plane"></i> Ajukan Borang Baru
                </a>
            @endif
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-award"></i>
                    @if (auth()->user()->hasRole('super_admin') ||
                            auth()->user()->hasAnyRole(['ketua_pac', 'sekretaris_pac']))
                        Daftar Antrean Akreditasi Ranting
                    @else
                        Riwayat Akreditasi Ranting Anda
                    @endif
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                        <thead class="bg-light text-center">
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">Tanggal Ajuan</th>
                                @if (auth()->user()->hasRole('super_admin') ||
                                        auth()->user()->hasAnyRole(['ketua_pac', 'sekretaris_pac']))
                                    <th>Nama Ranting</th>
                                @endif
                                <th>Nama Kegiatan / Keterangan</th>
                                <th width="20%">Status</th>
                                <th width="12%">Grade Akhir</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pengajuans as $index => $p)
                                <tr>
                                    <td class="text-center align-middle">{{ $index + 1 }}</td>
                                    <td class="text-center align-middle">
                                        {{ \Carbon\Carbon::parse($p->created_at)->format('d/m/Y H:i') }}
                                    </td>

                                    @if (auth()->user()->hasRole('super_admin') ||
                                            auth()->user()->hasAnyRole(['ketua_pac', 'sekretaris_pac']))
                                        <td class="align-middle font-weight-bold text-primary">
                                            {{ $p->organization->nama ?? $p->organization->name }}
                                        </td>
                                    @endif
                                    <td class="align-middle">
                                        <strong>{{ $p->kegiatan ?? 'Nama Kegiatan Tidak Tersedia' }}</strong>
                                        @if ($p->catatan_pac)
                                            <br><small class="text-danger"><strong>Catatan PAC:</strong>
                                                {{ $p->catatan_pac }}</small>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        @if ($p->status == 'Selesai Dinilai')
                                            <span class="badge badge-success px-3 py-2">
                                                <i class="fas fa-check-double"></i> Selesai Dinilai
                                            </span>
                                        @elseif ($p->status == 'Menunggu Finalisasi Ketua')
                                            <span class="badge badge-primary px-3 py-2">
                                                <i class="fas fa-user-tie"></i> Menunggu TTD Ketua
                                            </span>
                                        @else
                                            <span class="badge badge-warning px-3 py-2 text-dark">
                                                <i class="fas fa-clock"></i> Menunggu Review
                                            </span>
                                        @endif
                                    </td>

                                    <td class="text-center align-middle font-weight-bold h5">
                                        @if ($p->grade_akhir)
                                            <span class="badge badge-dark text-white px-3 py-1">GRADE
                                                {{ $p->grade_akhir }}</span>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>

                                    <td class="text-center align-middle">
                                        <a href="{{ route('akreditasi.show', $p->id) }}"
                                            class="btn btn-sm btn-info btn-block">
                                            @if (auth()->user()->hasRole('super_admin') ||
                                                    auth()->user()->hasRole('ketua_pac') ||
                                                    auth()->user()->hasRole('sekretaris_pac'))
                                                <i class="fas fa-search"></i> Periksa / Review
                                            @else
                                                <i class="fas fa-eye"></i> Lihat Detail
                                            @endif
                                        </a>

                                        @if (
                                            !auth()->user()->hasRole('super_admin') &&
                                                !auth()->user()->hasRole('ketua_pac') &&
                                                !auth()->user()->hasRole('sekretaris_pac'))
                                            @if ($p->status == 'Menunggu Penilaian PAC')
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="fas fa-folder-open fa-2x mb-2 d-block"></i>
                                        Belum ada riwayat pengajuan borang akreditasi.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
