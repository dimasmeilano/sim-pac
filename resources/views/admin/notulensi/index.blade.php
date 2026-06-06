@extends('layouts.adminlte')

@section('title', 'Notulensi Rapat')
@section('page-title', 'Notulensi Rapat')

@section('content')
    <div class="card card-primary card-outline shadow-sm">
        <div class="card-header border-bottom-0 pt-3 pb-2">
            <h3 class="card-title mt-1"><i class="fas fa-book-open text-primary mr-2"></i> Daftar Notulensi</h3>
            <div class="card-tools">
                <a href="{{ route('notulensi.create') }}" class="btn btn-sm btn-primary shadow-sm">
                    <i class="fas fa-plus-circle mr-1"></i> Tambah Notulensi
                </a>
            </div>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-striped align-middle">
                <thead>
                    <tr>
                        <th style="width: 5%">No</th>
                        <th>Agenda Rapat</th>
                        <th>Tanggal & Waktu</th>
                        <th>Tempat</th>
                        <th>Notulis</th>
                        <th>Status</th>
                        <th style="width: 15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($notulensi as $index => $item)
                        <tr>
                            <td>{{ $notulensi->firstItem() + $index }}</td>
                            <td>
                                <strong class="text-primary">{{ $item->agenda }}</strong>
                                @if ($item->kegiatan)
                                    <br><small class="text-muted"><i class="fas fa-link"></i> Terkait:
                                        {{ Str::limit($item->kegiatan->nama, 30) }}</small>
                                @endif
                            </td>
                            <td>
                                {{ $item->tanggal->format('d/m/Y') }}<br>
                                <small class="text-muted">
                                    <i class="far fa-clock"></i>
                                    {{ $item->waktu_mulai ? date('H:i', strtotime($item->waktu_mulai)) : '-' }} s/d
                                    {{ $item->waktu_selesai ? date('H:i', strtotime($item->waktu_selesai)) : 'Selesai' }}
                                </small>
                            </td>
                            <td>{{ $item->tempat }}</td>
                            <td>{{ $item->notulis->name ?? '-' }}</td>
                            <td>{!! $item->status_badge !!}</td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center align-items-center">
                                    <a href="{{ route('notulensi.show', $item->id) }}" class="btn btn-sm btn-info mr-1"
                                        title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    @if ($item->status == 'draft')
                                        <a href="{{ route('notulensi.edit', $item->id) }}"
                                            class="btn btn-sm btn-warning mr-1" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif

                                    <form action="{{ route('notulensi.destroy', $item->id) }}" method="POST"
                                        class="m-0 p-0 mr-1"
                                        onsubmit="return confirm('Yakin ingin menghapus notulensi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>

                                    @if ($item->status == 'draft')
                                        <form action="{{ route('notulensi.finalize', $item->id) }}" method="POST"
                                            class="m-0 p-0"
                                            onsubmit="return confirm('Kunci notulensi menjadi FINAL? Setelah ini dokumen siap dicetak.')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-success" title="Finalkan!">
                                                <i class="fas fa-check-circle"></i>
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('notulensi.pdf', $item->id) }}" class="btn btn-sm btn-success"
                                            title="Cetak PDF">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-book-dead mb-2" style="font-size: 32px;"></i><br>
                                Belum ada data notulensi rapat yang dicatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($notulensi->hasPages())
            <div class="card-footer bg-white">
                <div class="float-right">{{ $notulensi->links() }}</div>
            </div>
        @endif
    </div>
@endsection
