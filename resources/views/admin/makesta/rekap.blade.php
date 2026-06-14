@extends('layouts.adminlte') {{-- Sesuaikan dengan nama layout admin Anda --}}

@section('content')
    <div class="container-fluid">

        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h3 class="font-weight-bold text-primary mb-0">Rekapitulasi Nilai - {{ $event->tema }}</h3>
            <div>
                <a href="{{ route('makesta-event.export-rekap-pdf', $event->id) }}" target="_blank"
                    class="btn btn-sm btn-danger shadow-sm mr-2">
                    <i class="fas fa-file-pdf mr-1"></i> Export PDF
                </a>
                <a href="{{ route('makesta-event.export-rekap-excel', $event->id) }}"
                    class="btn btn-sm btn-success shadow-sm mr-2">
                    <i class="fas fa-file-excel mr-1"></i> Export Excel
                </a>
                <a href="{{ route('makesta-event.index', $event->id) }}" class="btn btn-sm btn-secondary shadow-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
        </div>
        <div class="card shadow mb-4 border-left-primary">
            <div class="card-header py-3 bg-light">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-poll mr-2"></i>Matriks Nilai Akhir Peserta
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                        <thead class="bg-primary text-white text-center">
                            <tr>
                                <th width="5%">No</th>
                                <th>Nama Peserta</th>
                                @foreach ($event->materis as $materi)
                                    <th>{{ $materi->nama_materi }}</th>
                                @endforeach
                                <th class="bg-success text-white" width="12%">Rata-Rata Akhir</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($event->pesertas as $index => $peserta)
                                <tr>
                                    <td class="text-center align-middle">{{ $index + 1 }}</td>
                                    <td class="font-weight-bold align-middle">{{ $peserta->nama_lengkap }}</td>

                                    @php
                                        $total = 0;
                                        $count = 0;
                                    @endphp
                                    @foreach ($event->materis as $materi)
                                        @php
                                            // KUNCI: Mengambil data dari model nilai baru (nilai_akhir dan abjad)
                                            $nilaiData = $materi->nilais
                                                ->where('makesta_peserta_id', $peserta->id)
                                                ->first();
                                            $nilaiAkhir = $nilaiData->nilai_akhir ?? 0;
                                            $abjad = $nilaiData->abjad ?? '-';

                                            if ($nilaiData) {
                                                $total += $nilaiAkhir;
                                                $count++;
                                            }
                                        @endphp
                                        <td class="text-center align-middle">
                                            @if ($nilaiData)
                                                <span class="font-weight-bold"
                                                    style="font-size: 1.1rem;">{{ $nilaiAkhir }}</span>
                                                <span class="badge badge-warning text-dark d-block mx-auto mt-1"
                                                    style="max-width: 40px; font-size: 11px;">
                                                    {{ $abjad }}
                                                </span>
                                            @else
                                                <span class="text-muted small">Belum Dinilai</span>
                                            @endif
                                        </td>
                                    @endforeach

                                    <td class="text-center align-middle font-weight-bold text-success bg-light"
                                        style="font-size: 1.2rem;">
                                        {{ $count > 0 ? number_format($total / $count, 1) : '0.0' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
