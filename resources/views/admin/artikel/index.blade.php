@extends('layouts.adminlte')

@section('title', 'Daftar Artikel')
@section('page-title', 'Kelola Berita & Artikel')

@section('content')
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
            {{-- Hanya Kontributor dan Editor yang bisa membuat tulisan baru --}}
            @hasanyrole('kontributor|editor|super_admin')
                <a href="{{ route('artikel.create') }}" class="btn btn-primary font-weight-bold">
                    <i class="fas fa-edit mr-1"></i> Tulis Artikel Baru
                </a>
            @endhasanyrole
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="bg-light text-center">
                        <tr>
                            <th width="5%">No</th>
                            <th>Judul Artikel</th>
                            <th width="15%">Kategori</th>
                            <th width="20%">Penulis & Asal Ranting</th>
                            <th width="15%">Status</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($artikels as $key => $item)
                            <tr>
                                <td class="text-center">{{ $key + 1 }}</td>
                                <td>
                                    <strong>{{ $item->judul }}</strong><br>
                                    <small class="text-muted"><i class="fas fa-clock"></i>
                                        {{ $item->created_at->format('d M Y H:i') }} | <i class="fas fa-eye"></i>
                                        {{ $item->dilihat ?? 0 }}x</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-info">{{ $item->kategori->nama_kategori ?? '-' }}</span>
                                </td>
                                <td class="text-center">
                                    {{ $item->user->name ?? 'Anonim' }} <br>
                                    {{-- Menampilkan nama organisasi / ranting --}}
                                    @if ($item->organization)
                                        <small class="text-primary font-weight-bold">
                                            <i class="fas fa-sitemap mr-1"></i> {{ $item->organization->name }}
                                        </small>
                                    @else
                                        <small class="text-secondary"><i class="fas fa-building mr-1"></i> Organisasi
                                            Umum</small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    {!! $item->status_badge !!}
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('artikel.edit', $item->id) }}"
                                        class="btn btn-sm btn-warning shadow-sm" title="Buka / Edit">
                                        <i class="fas fa-folder-open"></i> Buka
                                    </a>
                                    @hasrole('editor')
                                        <form action="{{ route('artikel.destroy', $item->id) }}" method="POST" class="d-inline"
                                            onsubmit="return confirm('Yakin ingin menghapus artikel ini permanen?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger shadow-sm" title="Hapus"><i
                                                    class="fas fa-trash"></i></button>
                                        </form>
                                    @endhasrole
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Belum ada data artikel.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop
