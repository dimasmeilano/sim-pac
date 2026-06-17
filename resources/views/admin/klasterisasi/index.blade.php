@extends('layouts.adminlte')

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 text-gray-800"><i class="fas fa-layer-group text-primary"></i> Manajemen Klasterisasi Organisasi
            </h1>

            @if (!auth()->user()->hasAnyRole(['ketua_pac', 'sekretaris_pac', 'Ketua PAC', 'Sekretaris PAC']))
                <a href="{{ route('klasterisasi.create') }}" class="btn btn-primary shadow-sm">
                    <i class="fas fa-paper-plane"></i> Ajukan Klasterisasi Baru
                </a>
            @endif
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if (session('warning'))
            <div class="alert alert-warning alert-dismissible fade show shadow-sm" role="alert">
                <i class="fas fa-exclamation-triangle"></i> {{ session('warning') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary">
                <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-list"></i> Daftar Riwayat Klasterisasi</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover text-center" id="dataTable" width="100%"
                        cellspacing="0">
                        <thead class="bg-light">
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">Tanggal Ajuan</th>

                                @if (auth()->user()->hasAnyRole(['ketua_pac', 'sekretaris_pac', 'Ketua PAC', 'Sekretaris PAC']))
                                    <th>Nama Ranting</th>
                                @endif

                                <th>Periode</th>
                                <th>Total Skor</th>
                                <th>Hasil Kluster</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $index => $row)
                                <tr>
                                    <td class="align-middle">{{ $index + 1 }}</td>
                                    <td class="align-middle">{{ $row->created_at->format('d/m/Y H:i') }}</td>

                                    @if (auth()->user()->hasAnyRole(['ketua_pac', 'sekretaris_pac', 'Ketua PAC', 'Sekretaris PAC']))
                                        <td class="align-middle font-weight-bold text-left">
                                            {{ $row->organization->nama ?? ($row->organization->name ?? 'Organisasi Tidak Diketahui') }}
                                        </td>
                                    @endif

                                    <td class="align-middle font-weight-bold">{{ $row->periode_penilaian }}</td>

                                    <td class="align-middle">
                                        <span class="h5 font-weight-bold text-dark">{{ $row->total_skor }}</span>
                                    </td>

                                    <td class="align-middle">
                                        @if ($row->kluster == 1)
                                            <span class="badge badge-success px-3 py-2" style="font-size: 14px;">Kluster
                                                1</span>
                                        @elseif($row->kluster == 2)
                                            <span class="badge badge-info px-3 py-2" style="font-size: 14px;">Kluster
                                                2</span>
                                        @elseif($row->kluster == 3)
                                            <span class="badge badge-warning px-3 py-2" style="font-size: 14px;">Kluster
                                                3</span>
                                        @else
                                            <span class="badge badge-secondary px-3 py-2">Belum Dikalkulasi</span>
                                        @endif
                                    </td>

                                    <td class="align-middle">
                                        <a href="{{ route('klasterisasi.show', $row->id) }}"
                                            class="btn btn-info btn-sm shadow-sm">
                                            <i class="fas fa-eye"></i> Lihat Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    @php
                                        $colspan = auth()
                                            ->user()
                                            ->hasAnyRole(['ketua_pac', 'sekretaris_pac', 'Ketua PAC', 'Sekretaris PAC'])
                                            ? 7
                                            : 6;
                                    @endphp
                                    <td colspan="{{ $colspan }}" class="text-center py-4 text-muted">
                                        <i class="fas fa-folder-open fa-3x mb-3 opacity-50"></i><br>
                                        Belum ada data pengajuan klasterisasi.
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
