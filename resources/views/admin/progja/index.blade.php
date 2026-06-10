@extends('layouts.adminlte')

@section('title', 'Program Kerja')
@section('page-title', 'Program Kerja')

@section('content')
    <div class="card card-primary card-outline shadow-sm">
        <div class="card-header border-bottom-0 pt-3 pb-2">
            <h3 class="card-title mt-1"><i class="fas fa-project-diagram text-primary mr-2"></i> Daftar Program Kerja</h3>
            <div class="card-tools">
                {{-- Tombol tambah proker biasanya hanya untuk Sekretaris atau Admin --}}
                @hasanyrole('super_admin|sekretaris_pac|sekretaris_ranting')
                    <a href="{{ route('progja.create') }}" class="btn btn-sm btn-primary shadow-sm">
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
                        <th>Organisasi</th>
                        <th>Nama Program & Deskripsi</th>
                        <th>Anggaran</th>
                        <th>Pelaksanaan</th>
                        <th>Progress & Status</th>
                        <th style="width: 15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($programKerja as $index => $progja)
                        <tr>
                            <td>{{ $programKerja->firstItem() + $index }}</td>

                            {{-- Nama Organisasi Pemilik --}}
                            <td><span class="font-weight-bold">{{ $progja->organization->name ?? 'Semua' }}</span></td>

                            {{-- Nama Program & Deskripsi --}}
                            <td>
                                <div class="font-weight-bold text-primary">{{ $progja->nama }}</div>
                                <span class="badge badge-info mb-1">{{ strtoupper($progja->jenis) }}</span>
                                <small class="text-muted d-block text-wrap" style="max-width: 250px;">
                                    {{ Str::limit($progja->deskripsi, 60) }}
                                </small>
                            </td>

                            {{-- Anggaran --}}
                            <td>
                                <strong>Rp {{ number_format($progja->estimasi_anggaran, 0, ',', '.') }}</strong>
                            </td>

                            {{-- Pelaksanaan --}}
                            <td>
                                {{ \Carbon\Carbon::parse($progja->tgl_mulai)->format('d M Y') }} <br>
                                <small class="text-muted">s.d</small> <br>
                                {{ \Carbon\Carbon::parse($progja->tgl_selesai)->format('d M Y') }}
                            </td>

                            {{-- Progress & Status --}}
                            <td>
                                @php
                                    $totalTugas = $progja->tugas->count();
                                    $tugasSelesai = $progja->tugas->where('status', 'done')->count();
                                    $persen = $totalTugas > 0 ? round(($tugasSelesai / $totalTugas) * 100) : 0;
                                @endphp
                                <div class="progress progress-sm mb-1" style="width: 100px;">
                                    <div class="progress-bar bg-success" style="width: {{ $persen }}%"></div>
                                </div>
                                <small class="font-weight-bold">{{ $persen }}% Selesai</small>
                                <br>
                                @if ($progja->status == 'completed' || $progja->status == 'selesai')
                                    <span class="badge badge-success mt-1">Selesai</span>
                                @elseif($progja->status == 'active' || $progja->status == 'berjalan')
                                    <span class="badge badge-primary mt-1">Aktif</span>
                                @else
                                    <span class="badge badge-secondary mt-1">{{ ucfirst($progja->status) }}</span>
                                @endif
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
                                        {{-- LOGIKA TOMBOL LPJ --}}
                                        @if ($progja->status == 'completed')
                                            {{-- Sesuaikan dengan value 'Selesai' di database Anda --}}
                                            @if (!$progja->lpj)
                                                {{-- Jika Selesai tapi LPJ belum dibuat --}}
                                                <a href="{{ route('lpj.create', ['progja_id' => $progja->id]) }}"
                                                    class="btn btn-primary btn-sm shadow-sm" title="Buat Laporan">
                                                    <i class="fas fa-edit"></i> Buat LPJ
                                                </a>
                                            @else
                                                {{-- Jika LPJ sudah dibuat --}}
                                                <div class="btn-group">
                                                    <a href="{{ route('lpj.edit', $progja->lpj->id) }}"
                                                        class="btn btn-warning btn-sm" title="Edit Data LPJ">
                                                        <i class="fas fa-pencil-alt"></i>
                                                    </a>
                                                    <a href="{{ route('lpj.cetak', $progja->lpj->id) }}" target="_blank"
                                                        class="btn btn-danger btn-sm" title="Cetak Dokumen PDF">
                                                        <i class="fas fa-file-pdf"></i> Cetak LPJ
                                                    </a>
                                                </div>
                                            @endif
                                        @else
                                            {{-- Jika Belum Selesai, tampilkan badge info saja --}}
                                            <span class="badge badge-secondary" title="Selesaikan progja untuk membuat LPJ"><i
                                                    class="fas fa-lock"></i> LPJ Terkunci</span>
                                        @endif
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
