@extends('layouts.adminlte')

@section('title', 'Surat Khusus (SRP/Mandat)')
@section('page-title', 'Surat Khusus / Baku')

@section('content')
    <div class="card card-primary card-outline shadow-sm">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-file-contract text-primary mr-1"></i> Pilih Template Surat Khusus</h3>
            <div class="card-tools">
                <a href="{{ route('surat.keluar.index') }}" class="btn btn-danger btn-sm">
                    <i class="fas fa-arrow-left"></i> Kembali ke Data Surat
                </a>
                @can('manage_surat')
                    <a href="{{ route('surat.template.index') }}" class="btn btn-info btn-sm ml-1">
                        <i class="fas fa-cog"></i> Kelola Template
                    </a>
                @endcan
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center" width="5%">No</th>
                            <th>Nama Surat</th>
                            <th class="text-center">Kode / Indeks</th>
                            <th class="text-center">Klasifikasi</th>
                            <th class="text-center">Lampiran</th>
                            <th class="text-center" width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($templates as $key => $item)
                            <tr>
                                <td class="text-center align-middle">{{ $key + 1 }}</td>
                                <td class="align-middle">
                                    <strong>{{ $item->nama }}</strong>
                                </td>
                                <td class="text-center align-middle">
                                    <span class="badge badge-secondary">{{ $item->kode }}</span>
                                </td>
                                <td class="text-center align-middle">
                                    @if ($item->klasifikasi)
                                        <span class="badge badge-info">{{ $item->klasifikasi }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-center align-middle">
                                    {{ $item->lampiran ?? '-' }}
                                </td>
                                <td class="text-center align-middle">
                                    <a href="{{ route('cetak-surat.create', $item->id) }}"
                                        class="btn btn-primary btn-sm btn-block">
                                        <i class="fas fa-edit"></i> Buat Surat
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="fas fa-folder-open fa-3x mb-3 text-light"></i>
                                    <h5>Belum Ada Template Surat Khusus</h5>
                                    <p>Silakan tambahkan template terlebih dahulu di menu Kelola Template.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
