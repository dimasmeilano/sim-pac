@extends('layouts.adminlte') {{-- Sesuaikan dengan layout admin Anda --}}

@section('content')
    <div class="container-fluid">

        <!-- Header Halaman -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Daftar Event Makesta</h1>
            <a href="{{ route('makesta-event.create') }}" class="btn btn-sm btn-success shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Buat Event Baru
            </a>
        </div>
        <!-- Tabel Data -->
        <div class="card shadow mb-4 border-bottom-success">
            <div class="card-header py-3 bg-light d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-success">Data Penyelenggaraan Makesta</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                        <thead class="bg-light">
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th width="20%">Penyelenggara</th>
                                <th>Tema Kegiatan</th>
                                <th width="20%">Waktu & Tempat</th>
                                <th width="15%" class="text-center">Status</th>
                                <th width="15%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($events as $event)
                                <tr>
                                    <td class="text-center align-middle">{{ $loop->iteration }}</td>
                                    <td class="align-middle font-weight-bold">
                                        {{ $event->organization->name ?? 'Tidak Diketahui' }}
                                    </td>
                                    <td class="align-middle">
                                        <strong>{{ $event->tema }}</strong><br>
                                        <small class="text-muted"><i class="fas fa-users"></i> Kuota:
                                            {{ $event->kuota_peserta ? $event->kuota_peserta . ' Orang' : 'Tidak Dibatasi' }}</small>
                                    </td>
                                    <td class="align-middle">
                                        <small class="d-block mb-1">
                                            <i class="far fa-calendar-alt text-success mr-1"></i>
                                            {{ \Carbon\Carbon::parse($event->tgl_mulai)->format('d M Y') }} -
                                            {{ \Carbon\Carbon::parse($event->tgl_selesai)->format('d M Y') }}
                                        </small>
                                        <small class="d-block">
                                            <i class="fas fa-map-marker-alt text-danger mr-1"></i> {{ $event->lokasi }}
                                        </small>
                                    </td>
                                    <td class="text-center align-middle">
                                        @if ($event->status == 'Menunggu Verifikasi')
                                            <span class="badge badge-warning px-2 py-1">Menunggu Verifikasi</span>
                                        @elseif($event->status == 'Disetujui')
                                            <span class="badge badge-primary px-2 py-1">Disetujui</span>
                                        @elseif($event->status == 'Berjalan')
                                            <span class="badge badge-success px-2 py-1">Berjalan</span>
                                        @else
                                            <span class="badge badge-secondary px-2 py-1">{{ $event->status }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle" width="20%">

                                        <!-- GRUP 1: Tombol Teks Utama (Lebar Seragam) -->
                                        <a href="{{ route('makesta-event.peserta', $event->id) }}"
                                            class="btn btn-sm btn-primary btn-block mb-1 font-weight-bold shadow-sm">
                                            <i class="fas fa-users mr-1"></i> Kelola Peserta
                                            <span class="badge badge-light ml-1">{{ $event->pesertas_count ?? 1 }}</span>
                                        </a>

                                        <a href="{{ route('makesta-event.rekap', $event->id) }}"
                                            class="btn btn-sm btn-success btn-block mb-1 font-weight-bold shadow-sm">
                                            <i class="fas fa-chart-bar mr-1"></i> Rekap Nilai
                                        </a>

                                        <a href="{{ route('makesta-event.rekap-evaluasi', $event->id) }}"
                                            class="btn btn-sm btn-info btn-block mb-2 font-weight-bold shadow-sm text-white">
                                            <i class="fas fa-poll mr-1"></i> Rekap Evaluasi
                                        </a>

                                        <!-- GRUP 2: Tombol Icon Aksi (Sejajar di Tengah) -->
                                        <div class="d-flex justify-content-center">
                                            <a href="{{ route('makesta-event.show', $event->id) }}"
                                                class="btn btn-sm btn-info mr-1 shadow-sm" title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('makesta-event.edit', $event->id) }}"
                                                class="btn btn-sm btn-warning mr-1 shadow-sm" title="Edit Data">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <!-- Tombol Hapus (biarkan jika Anda punya tombol ini sebelumnya) -->
                                            <form action="{{ route('makesta-event.destroy', $event->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger shadow-sm"
                                                    onclick="return confirm('Yakin ingin menghapus event ini?')"
                                                    title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>

                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="fas fa-folder-open fa-3x mb-3 opacity-50 d-block"></i>
                                        Belum ada event Makesta yang didaftarkan.
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
