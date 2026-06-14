@extends('layouts.adminlte') {{-- Sesuaikan dengan layout admin Anda --}}

@section('content')
    <div class="container-fluid">

        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h3 class="font-weight-bold text-primary mb-0">Hasil Evaluasi Digital Peserta</h3>
            <a href="{{ route('makesta-event.index', $event->id) }}" class="btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>

        <ul class="nav nav-pills mb-4 shadow-sm p-2 bg-white rounded" id="evaluasiTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active font-weight-bold" id="pemateri-tab" data-toggle="tab" data-target="#pemateri"
                    type="button" role="tab"><i class="fas fa-chalkboard-teacher mr-1"></i> Evaluasi Pemateri</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link font-weight-bold" id="panitia-tab" data-toggle="tab" data-target="#panitia"
                    type="button" role="tab"><i class="fas fa-users-cog mr-1"></i> Evaluasi Panitia</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link font-weight-bold" id="instruktur-tab" data-toggle="tab" data-target="#instruktur"
                    type="button" role="tab"><i class="fas fa-user-shield mr-1"></i> Evaluasi Instruktur</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link font-weight-bold" id="refleksi-tab" data-toggle="tab" data-target="#refleksi"
                    type="button" role="tab"><i class="fas fa-brain mr-1"></i> Jurnal Refleksi Harian</button>
            </li>
        </ul>

        <div class="tab-content" id="evaluasiTabContent">

            <div class="tab-pane fade show active" id="pemateri" role="tabpanel">
                <div class="card shadow border-left-primary">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Nilai & Catatan Kepuasan Pemateri</h6>
                    </div>
                    <div class="card-body">
                        @foreach ($event->materis as $materi)
                            @php
                                // Hitung rata-rata nilai untuk materi ini dari seluruh JSON evaluasi yang masuk
                                $totalNilai = 0;
                                $count = 0;
                                $catatans = [];
                                foreach ($evaluasiPemateri as $eval) {
                                    if (
                                        isset($eval->data_evaluasi[$materi->id]['nilai']) &&
                                        $eval->data_evaluasi[$materi->id]['nilai'] != ''
                                    ) {
                                        $totalNilai += $eval->data_evaluasi[$materi->id]['nilai'];
                                        $count++;
                                        if (!empty($eval->data_evaluasi[$materi->id]['catatan'])) {
                                            $catatans[] = $eval->data_evaluasi[$materi->id]['catatan'];
                                        }
                                    }
                                }
                                $rataRata = $count > 0 ? round($totalNilai / $count, 1) : null;
                            @endphp
                            <div class="bg-white p-3 rounded mb-3 border shadow-sm">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <h6 class="font-weight-bold text-dark mb-0">{{ $materi->nama_materi }}</h6>
                                        <small class="text-muted">Narasumber:
                                            <strong>{{ $materi->nama_pemateri }}</strong></small>
                                    </div>
                                    <div class="text-right">
                                        <span
                                            class="badge {{ $rataRata >= 80 ? 'badge-success' : 'badge-warning' }} p-2 style='font-size: 14px;'">
                                            Rata-Rata Skor: {{ $rataRata ?? 'Belum ada data' }}
                                        </span>
                                    </div>
                                </div>
                                <label class="small font-weight-bold text-muted mb-1">Komentar / Catatan dari
                                    Peserta:</label>
                                <ul class="pl-3 mb-0 text-dark small">
                                    @forelse($catatans as $catatan)
                                        <li>"{{ $catatan }}"</li>
                                    @empty
                                        <li class="text-muted italic">Belum ada catatan khusus untuk materi ini.</li>
                                    @endforelse
                                </ul>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="panitia" role="tabpanel">
                <div class="card shadow border-left-success">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-success">Kritik & Saran untuk Kinerja Panitia</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped small">
                                <thead class="bg-success text-white text-center">
                                    <tr>
                                        <th width="10%">Hari Ke</th>
                                        <th width="15%">Rating Angka</th>
                                        <th>Catatan / Masukan dari Peserta</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($evaluasiPanitia as $eval)
                                        <tr>
                                            <td class="text-center font-weight-bold">Hari ke-{{ $eval->hari_ke }}</td>
                                            <td class="text-center font-weight-bold text-success" style="font-size: 13px;">
                                                {{ $eval->data_evaluasi['nilai'] ?? '-' }}</td>
                                            <td>"{{ $eval->data_evaluasi['catatan'] ?? 'Tidak menulis catatan' }}"</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">Belum ada evaluasi panitia
                                                yang masuk.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="instruktur" role="tabpanel">
                <div class="card shadow border-left-info">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-info">Penilaian Pendampingan Instruktur PC</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped small">
                                <thead class="bg-info text-white text-center">
                                    <tr>
                                        <th width="10%">Hari Ke</th>
                                        <th width="15%">Rating Angka</th>
                                        <th>Apresiasi / Evaluasi Forum</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($evaluasiInstruktur as $eval)
                                        <tr>
                                            <td class="text-center font-weight-bold">Hari ke-{{ $eval->hari_ke }}</td>
                                            <td class="text-center font-weight-bold text-info" style="font-size: 13px;">
                                                {{ $eval->data_evaluasi['nilai'] ?? '-' }}</td>
                                            <td>"{{ $eval->data_evaluasi['catatan'] ?? 'Tidak menulis catatan' }}"</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">Belum ada evaluasi instruktur
                                                yang masuk.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="refleksi" role="tabpanel">
                <div class="card shadow border-left-danger">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-danger">Isi Pemikiran & Refleksi Diri Peserta</h6>
                    </div>
                    <div class="card-body">
                        @forelse($evaluasiRefleksi as $eval)
                            <div class="bg-white p-3 rounded mb-3 border shadow-sm border-left-danger">
                                <div class="d-flex justify-content-between border-bottom pb-1 mb-2">
                                    <span class="font-weight-bold text-primary"><i
                                            class="fas fa-user-circle mr-1"></i>{{ $eval->peserta->nama_lengkap }}</span>
                                    <span class="badge badge-danger font-weight-bold">Hari Ke-{{ $eval->hari_ke }}</span>
                                </div>
                                <p class="mb-1"><strong>1. Pengalaman paling bermanfaat:</strong><br><span
                                        class="text-dark small">"{{ $eval->data_evaluasi['pengalaman'] ?? '-' }}"</span>
                                </p>
                                <p class="mb-1"><strong>2. Tingkat partisipasi diri:</strong><br><span
                                        class="text-dark small">"{{ $eval->data_evaluasi['partisipasi'] ?? '-' }}"</span>
                                </p>
                                <p class="mb-1"><strong>3. Faktor pendorong / penghambat:</strong><br><span
                                        class="text-dark small">"{{ $eval->data_evaluasi['hambatan'] ?? '-' }}"</span></p>
                                <p class="mb-2"><strong>4. Pengetahuan yang diperoleh:</strong><br><span
                                        class="text-dark small">"{{ $eval->data_evaluasi['pengetahuan'] ?? '-' }}"</span>
                                </p>
                            </div>
                        @empty
                            <p class="text-center text-muted my-3">Belum ada jurnal refleksi harian yang dikirim oleh
                                peserta.</p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
