@extends('layouts.adminlte')

@section('title', 'Program Kerja')
@section('page-title', 'Program Kerja')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header">
            <h3 class="card-title">Daftar Program Kerja</h3>
            <div class="card-tools">
                {{-- Tombol tambah proker biasanya hanya untuk Sekretaris atau Admin --}}
                @hasanyrole('super_admin|sekretaris_pac|sekretaris_ranting')
                    <a href="{{ route('progja.create') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus-circle mr-1"></i> Tambah Progja
                    </a>
                @endhasanyrole
            </div>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-striped text-nowrap align-middle">
                <thead>
                    <tr>
                        <th style="width: 5%">No</th>
                        <th>Nama Program</th>
                        <th>Jenis</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Progress</th>
                        <th style="width: 15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($programKerja as $index => $progja)
                        <tr>
                            {{-- Nomor Otomatis sesuai Paginasi --}}
                            <td>{{ $programKerja->firstItem() + $index }}</td>

                            {{-- Nama Program / Organisasi --}}
                            <td>
                                <span class="font-weight-bold">{{ $progja->organization->nama ?? 'Bersama' }}</span>
                            </td>

                            {{-- Jenis & Deskripsi Program --}}
                            <td>
                                <div class="font-weight-bold text-primary">{{ $progja->nama_program }}</div>
                                <small class="text-muted d-block text-wrap" style="max-width: 300px;">
                                    {{ Str::limit($progja->deskripsi, 60) }}
                                </small>
                            </td>

                            {{-- Tanggal Pelaksanaan --}}
                            <td>
                                {{ \Carbon\Carbon::parse($progja->tgl_mulai)->format('d/m/Y') }} -
                                {{ \Carbon\Carbon::parse($progja->tgl_selesai)->format('d/m/Y') }}
                            </td>

                            {{-- Status Badge --}}
                            <td>
                                @if ($progja->status == 'completed' || $progja->status == 'selesai')
                                    <span class="badge badge-success px-2 py-1">Selesai</span>
                                @elseif($progja->status == 'active' || $progja->status == 'berjalan')
                                    <span class="badge badge-primary px-2 py-1">Aktif</span>
                                @else
                                    <span class="badge badge-secondary px-2 py-1">{{ ucfirst($progja->status) }}</span>
                                @endif
                            </td>

                            {{-- Progress Bar --}}
                            <td>
                                <div class="progress progress-sm" style="width: 100px;">
                                    {{-- Sesuaikan rumus persentase tugas jika ada --}}
                                    @php
                                        $totalTugas = $progja->tugas->count();
                                        $tugasSelesai = $progja->tugas->where('status', 'selesai')->count();
                                        $persen = $totalTugas > 0 ? round(($tugasSelesai / $totalTugas) * 100) : 0;
                                    @endphp
                                    <div class="progress-bar bg-success" role="progressbar"
                                        style="width: {{ $persen }}%" aria-valuenow="{{ $persen }}"
                                        aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <small class="text-muted">{{ $persen }}% Selesai</small>
                            </td>

                            {{-- Tombol Aksi --}}
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('progja.show', $progja->id) }}" class="btn btn-sm btn-info"
                                        title="Papan Kanban">
                                        <i class="fas fa-tasks"></i> Kanban
                                    </a>

                                    {{-- Tombol Edit & Hapus hanya muncul untuk yang memiliki hak akses --}}
                                    @hasanyrole('super_admin|sekretaris_pac|sekretaris_ranting')
                                        <a href="{{ route('progja.edit', $progja->id) }}" class="btn btn-sm btn-warning"
                                            title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('progja.destroy', $progja->id) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus program kerja ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endhasanyrole
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-folder-open mb-2" style="font-size: 32px;"></i><br>
                                Belum ada program kerja yang terdaftar untuk tingkat organisasi Anda.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer untuk Navigasi Halaman (Pagination) --}}
        @if ($programKerja->hasPages())
            <div class="card-footer clearfix">
                <div class="float-right">
                    {{ $programKerja->links('pagination::bootstrap-4') }}
                </div>
            </div>
        @endif
    </div>
@endsection
