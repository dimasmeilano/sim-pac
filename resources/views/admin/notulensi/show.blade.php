@extends('layouts.adminlte')

@section('title', 'Detail Notulensi')
@section('page-title', 'Detail Notulensi Rapat')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h3 class="card-title font-weight-bold">{{ $notulensi->agenda }}</h3>
            <div class="card-tools">
                @if ($notulensi->status == 'final')
                    <a href="{{ route('notulensi.pdf', $notulensi->id) }}" class="btn btn-success btn-sm">
                        <i class="fas fa-file-pdf mr-1"></i> Download PDF
                    </a>
                @endif
                <a href="{{ route('notulensi.index') }}" class="btn btn-default btn-sm">Kembali</a>
            </div>
        </div>
        <div class="card-body">
            <div class="row border-bottom pb-3 mb-4">
                <div class="col-md-6">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th width="150">Hari, Tanggal</th>
                            <td>: {{ \Carbon\Carbon::parse($notulensi->tanggal)->translatedFormat('l, d F Y') }}</td>
                        </tr>
                        <tr>
                            <th>Waktu</th>
                            <td>: {{ $notulensi->waktu_mulai ? date('H:i', strtotime($notulensi->waktu_mulai)) : '-' }} s/d
                                {{ $notulensi->waktu_selesai ? date('H:i', strtotime($notulensi->waktu_selesai)) : 'Selesai' }}
                            </td>
                        </tr>
                        <tr>
                            <th>Tempat</th>
                            <td>: {{ $notulensi->tempat }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th width="150">Pimpinan Rapat</th>
                            <td>: {{ $notulensi->pemimpin_rapat }}</td>
                        </tr>
                        <tr>
                            <th>Notulis</th>
                            <td>: {{ $notulensi->notulis->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>: {!! $notulensi->status_badge !!}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <h5 class="text-primary font-weight-bold">A. Pembahasan</h5>
            <div class="p-3 bg-light rounded border mb-4">
                {!! $notulensi->pembahasan !!}
            </div>

            <h5 class="text-success font-weight-bold">B. Kesimpulan</h5>
            <div class="p-3 bg-light rounded border mb-4">
                {!! $notulensi->kesimpulan ?? '<em>Tidak ada kesimpulan khusus.</em>' !!}
            </div>

            @if ($notulensi->kegiatan_id)
                <h5 class="text-info font-weight-bold">C. Daftar Hadir (Terkait Kegiatan)</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="bg-light">
                            <tr>
                                <th width="5%">No</th>
                                <th>Nama Peserta</th>
                                <th>Waktu Hadir</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($absensi as $key => $absen)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $absen->user_id ? $absen->user->name : $absen->nama_peserta }}</td>
                                    <td>{{ $absen->waktu_absen->format('H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">Belum ada data hadir.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif

        </div>
    </div>
@endsection
