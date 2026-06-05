@extends('layouts.adminlte')

@section('title', 'Daftar Surat Masuk')
@section('page-title', 'Surat Masuk')

@section('content')
    <div class="card card-success shadow-sm border-top-success">
        <div class="card-header">
            <h3 class="card-title mt-1"><i class="fas fa-inbox mr-1"></i> Arsip Surat Masuk</h3>
            <div class="card-tools">
                <a href="{{ route('surat.masuk.create') }}"
                    class="btn btn-sm btn-light text-success font-weight-bold shadow-sm">
                    <i class="fas fa-plus-circle"></i> Catat Surat Eksternal
                </a>
            </div>
        </div>

        <div class="card-body p-0">
            {{-- Notifikasi --}}
            @if (session('success'))
                <div class="alert alert-success m-3 alert-dismissible fade show">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger m-3 alert-dismissible fade show">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th width="25%">Asal Surat / Pengirim</th>
                            <th width="30%">Nomor & Perihal</th>
                            <th width="15%">Penerimaan</th>
                            <th width="12%" class="text-center">Status</th>
                            <th width="13%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($surat as $item)
                            <tr>
                                <td class="text-center align-middle">{{ $loop->iteration + $surat->firstItem() - 1 }}</td>

                                <td class="align-middle">
                                    <span class="font-weight-bold text-dark">{{ $item->pengirim }}</span>
                                    <br>
                                    <small class="text-muted"><i class="far fa-calendar-alt"></i> Tgl Surat:
                                        {{ \Carbon\Carbon::parse($item->tanggal_surat)->format('d/m/Y') }}</small>
                                </td>

                                <td class="align-middle">
                                    <span class="text-primary font-weight-bold">{{ $item->nomor_surat }}</span>
                                    <br>
                                    <span class="text-muted">{{ \Illuminate\Support\Str::limit($item->perihal, 50) }}</span>
                                </td>

                                <td class="align-middle">
                                    <span
                                        class="font-weight-bold">{{ \Carbon\Carbon::parse($item->tanggal_diterima)->format('d M Y') }}</span>
                                    <br>
                                    <small class="text-muted">Oleh: {{ $item->penerima->name ?? 'Sistem Otomatis' }}</small>
                                </td>

                                <td class="text-center align-middle">
                                    @if ($item->status == 'baru')
                                        <span class="badge badge-danger px-2 py-1"><i class="fas fa-envelope"></i>
                                            Baru</span>
                                    @elseif ($item->status == 'diproses')
                                        <span class="badge badge-warning px-2 py-1 text-dark"><i
                                                class="fas fa-spinner fa-spin"></i> Disposisi</span>
                                    @else
                                        <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle"></i>
                                            Selesai</span>
                                    @endif
                                </td>

                                <td class="text-center align-middle">
                                    <div class="btn-group">
                                        <a href="{{ route('surat.masuk.show', $item->id) }}" class="btn btn-sm btn-info"
                                            title="Lihat Detail & Disposisi">
                                            <i class="fas fa-search"></i>
                                        </a>
                                        <a href="{{ route('surat.masuk.edit', $item->id) }}" class="btn btn-sm btn-warning"
                                            title="Edit Data">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('surat.masuk.destroy', $item->id) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus arsip surat masuk ini secara permanen?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus Surat">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <div class="mb-3">
                                        <i class="fas fa-inbox fa-4x text-light"></i>
                                    </div>
                                    <h5 class="font-weight-bold text-secondary">Belum ada surat masuk</h5>
                                    <p>Surat yang dikirim oleh instansi luar atau ranting akan otomatis muncul di sini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Menampilkan {{ $surat->firstItem() ?? 0 }} sampai {{ $surat->lastItem() ?? 0 }} dari total
                    {{ $surat->total() }} arsip.
                </div>
                <div class="pagination-sm m-0">
                    {{ $surat->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
@endsection
