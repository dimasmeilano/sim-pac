@extends('layouts.adminlte')

@section('title', 'Arsip Surat Keluar')
@section('page-title', 'Arsip Surat Keluar')

@section('content')
    <div class="card card-primary card-outline shadow-sm">
        <div class="card-header border-bottom-0 pt-3 pb-2">
            <h3 class="card-title mt-1"><i class="fas fa-folder-open text-primary mr-2"></i> Daftar Surat Keluar</h3>
            <div class="card-tools">
                <div class="btn-group">
                    <a href="{{ route('cetak-surat.index') }}" class="btn btn-primary btn-sm shadow-sm"
                        title="Surat dengan template baku">
                        <i class="fas fa-file-signature"></i> Surat Khusus (Baku)
                    </a>
                    <a href="{{ route('surat.keluar.create') }}" class="btn btn-success btn-sm shadow-sm"
                        title="Surat undangan, pengantar, dll">
                        <i class="fas fa-file-alt"></i> Surat Umum (Bebas)
                    </a>
                    @can('manage_surat')
                        <a href="{{ route('surat.template.index') }}" class="btn btn-info btn-sm shadow-sm">
                            <i class="fas fa-cog"></i> Kelola Template
                        </a>
                    @endcan
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            @if (session('success'))
                <div class="alert alert-success m-3 shadow-sm">
                    <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center" width="5%">No</th>
                            <th width="20%">Nomor Surat</th>
                            <th>Perihal</th>
                            <th width="20%">Tujuan</th>
                            <th class="text-center" width="15%">Status</th>
                            <th class="text-center" width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($surat as $key => $item)
                            <tr>
                                <td class="text-center align-middle">{{ $surat->firstItem() + $key }}</td>
                                <td class="align-middle">
                                    <span class="badge badge-light border text-dark font-weight-bold"
                                        style="font-size: 13px;">
                                        {{ $item->nomor_surat }}
                                    </span>
                                    @if ($item->penerbit_surat == 'bersama')
                                        <small class="d-block text-primary mt-1"><i class="fas fa-users"></i> Surat
                                            Bersama</small>
                                    @elseif($item->penerbit_surat == 'panitia')
                                        <small class="d-block text-warning mt-1"><i class="fas fa-users-cog"></i> Panitia
                                            Pelaksana</small>
                                    @endif
                                </td>
                                <td class="align-middle text-wrap">
                                    <strong>{{ $item->perihal }}</strong>
                                    <small class="d-block text-muted">Tgl:
                                        {{ \Carbon\Carbon::parse($item->tanggal_surat)->translatedFormat('d M Y') }}</small>
                                </td>
                                <td class="align-middle">{{ Str::limit($item->tujuan, 40) }}</td>
                                <td class="text-center align-middle">
                                    {!! $item->status_text !!}
                                </td>
                                <td class="text-center align-middle">
                                    <div class="btn-group">
                                        <a href="{{ route('surat.keluar.show', $item) }}" class="btn btn-info btn-sm"
                                            title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if ($item->created_by == auth()->id())
                                            @if ($item->status == 'draft')
                                                <a href="{{ route('surat.keluar.edit', $item) }}"
                                                    class="btn btn-warning btn-sm" title="Edit Surat">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('surat.keluar.destroy', $item) }}" method="POST"
                                                    style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus draft surat ini?')"
                                                        title="Hapus Surat">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                        @if ($item->status == 'selesai')
                                            <a href="{{ route('surat.keluar.download', $item) }}"
                                                class="btn btn-success btn-sm" title="Download PDF">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3 text-light"></i>
                                    <h5>Belum Ada Arsip Surat Keluar</h5>
                                    <p>Silakan buat dokumen surat baru melalui tombol di kanan atas.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($surat->hasPages())
            <div class="card-footer bg-white border-top">
                <div class="float-right">
                    {{ $surat->links() }}
                </div>
            </div>
        @endif
    </div>
@endsection
