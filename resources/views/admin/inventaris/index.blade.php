@extends('layouts.adminlte')

@section('title', 'Inventaris Barang')
@section('page-title', 'Inventaris / Aset Organisasi')

@section('content')
    <div class="card card-primary card-outline shadow-sm">
        <div class="card-header border-bottom-0 pt-3 pb-2">
            <h3 class="card-title mt-1"><i class="fas fa-boxes text-primary mr-2"></i> Daftar Aset Barang</h3>
            <div class="card-tools">
                <a href="{{ route('inventaris.cetak_label') }}" class="btn btn-sm btn-info shadow-sm mr-1" target="_blank">
                    <i class="fas fa-tags mr-1"></i> Cetak Semua Label
                </a>
                <a href="{{ route('inventaris.create') }}" class="btn btn-sm btn-primary shadow-sm">
                    <i class="fas fa-plus-circle mr-1"></i> Tambah Barang
                </a>
            </div>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-striped align-middle">
                <thead>
                    <tr>
                        <th style="width: 5%">No</th>
                        <th>Foto</th>
                        <th>Kode & Nama Barang</th>
                        <th>Jumlah</th>
                        <th>Kondisi</th>
                        <th>Tahun & Sumber</th>
                        <th style="width: 15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($inventaris as $index => $item)
                        <tr>
                            <td>{{ $inventaris->firstItem() + $index }}</td>
                            <td>
                                @if ($item->foto_barang)
                                    <img src="{{ asset('storage/' . $item->foto_barang) }}" alt="Foto"
                                        class="img-thumbnail" style="height: 50px; width: 50px; object-fit: cover;">
                                @else
                                    <div class="bg-light text-secondary d-flex align-items-center justify-content-center border"
                                        style="height: 50px; width: 50px; border-radius: 4px;">
                                        <i class="fas fa-box"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="font-weight-bold text-primary">{{ $item->nama_barang }}</div>
                                <small class="text-muted"><code>{{ $item->kode_barang }}</code> | Pemilik:
                                    {{ $item->organization->name ?? 'PAC' }}</small>
                            </td>
                            <td><strong>{{ $item->jumlah }}</strong> Unit</td>
                            <td>{!! $item->kondisi_badge !!}</td>
                            <td>
                                {{ $item->tahun_perolehan ?? '-' }} <br>
                                <small class="text-muted">{{ $item->sumber_dana ?? '-' }}</small>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="{{ route('inventaris.cetak_label', $item->id) }}" class="btn btn-sm btn-info"
                                        title="Cetak Label QR" target="_blank">
                                        <i class="fas fa-qrcode"></i>
                                    </a>
                                    <a href="{{ route('inventaris.edit', $item->id) }}" class="btn btn-sm btn-warning"
                                        title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('inventaris.destroy', $item->id) }}" method="POST"
                                        class="d-inline" onsubmit="return confirm('Yakin ingin menghapus aset ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-box-open mb-2" style="font-size: 32px;"></i><br>
                                Belum ada data inventaris/aset yang didaftarkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($inventaris->hasPages())
            <div class="card-footer bg-white">
                <div class="float-right">{{ $inventaris->links() }}</div>
            </div>
        @endif
    </div>
@endsection
