@extends('layouts.adminlte')

@section('title', 'Dasbor Utama')
@section('page-title', 'Dasbor Utama')

@section('content')

    {{-- ========================================================== --}}
    {{-- 1. ZONA KHUSUS RANTING: PERINGATAN SK ORGANISASINYA SENDIRI --}}
    {{-- ========================================================== --}}
    @hasanyrole('ketua_ranting|sekretaris_ranting|bendahara_ranting')
        @php
            $organisasi = auth()->user()->organization;
            $statusSk = 'aman';
            $sisaHari = 0;

            if ($organisasi && $organisasi->tgl_berakhir_sk) {
                $tglBerakhir = \Carbon\Carbon::parse($organisasi->tgl_berakhir_sk);
                $sisaHari = (int) now()->diffInDays($tglBerakhir, false);

                if ($sisaHari < 0) {
                    $statusSk = 'demisioner';
                } elseif ($sisaHari <= 30) {
                    $statusSk = 'kritis';
                } elseif ($sisaHari <= 90) {
                    $statusSk = 'peringatan';
                }
            }
        @endphp

        @if ($statusSk == 'demisioner')
            <div class="alert alert-danger shadow-sm border-0 alert-dismissible fade show mb-4">
                <h5 class="font-weight-bold"><i class="fas fa-ban mr-2"></i> STATUS ILEGAL / DEMISIONER!</h5>
                <p class="mb-2">Masa berlaku SK Pengesahan {{ $organisasi->name }} <strong>telah habis sejak
                        {{ abs($sisaHari) }} hari yang lalu</strong> (Tanggal: {{ $tglBerakhir->format('d F Y') }}).</p>
                <hr class="border-danger" style="opacity: 0.3;">
                <p class="mb-0 small">Segera laksanakan Konferensi dan ajukan permohonan rekomendasi pengesahan baru. Fitur
                    administrasi Anda mungkin akan dibatasi.</p>
                <a href="{{ route('ranting.perpanjangan.create') }}" class="btn btn-outline-light btn-sm mt-3 font-weight-bold">
                    <i class="fas fa-sync-alt mr-1"></i> Ajukan Perpanjangan SK Sekarang
                </a>
            </div>
        @elseif($statusSk == 'kritis')
            <div class="alert alert-danger shadow-sm border-0 mb-4 bg-danger text-white">
                <h5 class="font-weight-bold"><i class="fas fa-exclamation-triangle mr-2"></i> PERINGATAN KRITIS!</h5>
                <p class="mb-0">SK Pengesahan {{ $organisasi->name }} akan kedaluwarsa dalam <strong>{{ $sisaHari }}
                        hari</strong>. Harap segera persiapkan Konferensi!</p>
            </div>
        @elseif($statusSk == 'peringatan')
            <div class="alert alert-warning shadow-sm border-0 mb-4">
                <h5 class="font-weight-bold"><i class="fas fa-bell mr-2"></i> Persiapan Konferensi</h5>
                <p class="mb-0">Masa bhakti {{ $organisasi->name }} akan berakhir dalam <strong>{{ $sisaHari }}
                        hari</strong>. Mari mulai persiapkan regenerasi kepengurusan.</p>
            </div>
        @endif
    @endhasanyrole


    {{-- ========================================================== --}}
    {{-- 2. ZONA KHUSUS PAC: WIDGET MONITORING SEMUA RANTING        --}}
    {{-- ========================================================== --}}
    @hasanyrole('super_admin|ketua_pac|sekretaris_pac')
        @php
            $semuaRanting = \App\Models\Organization::whereNotNull('tgl_berakhir_sk')
                ->whereIn('type', ['ranting', 'komisariat'])
                ->get();

            $rantingDemisioner = [];
            $rantingKritis = [];

            foreach ($semuaRanting as $org) {
                $tglBerakhir = \Carbon\Carbon::parse($org->tgl_berakhir_sk);
                $sisaHari = (int) now()->diffInDays($tglBerakhir, false);

                if ($sisaHari < 0) {
                    $org->sisa_hari = abs($sisaHari);
                    $rantingDemisioner[] = $org;
                } elseif ($sisaHari <= 30) {
                    $org->sisa_hari = $sisaHari;
                    $rantingKritis[] = $org;
                }
            }
        @endphp

        @if (count($rantingDemisioner) > 0 || count($rantingKritis) > 0)
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card card-outline card-danger shadow-sm">
                        <div class="card-header bg-danger text-white">
                            <h3 class="card-title font-weight-bold">
                                <i class="fas fa-exclamation-triangle mr-2"></i> Pantauan Masa Aktif SK Ranting / Komisariat
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                @foreach ($rantingDemisioner as $org)
                                    <li class="list-group-item d-flex justify-content-between align-items-center bg-light">
                                        <div>
                                            <span class="badge badge-danger mr-2">DEMISIONER</span>
                                            <strong class="text-dark">{{ $org->name }}</strong>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-danger small font-weight-bold">Lewat {{ $org->sisa_hari }}
                                                hari</span><br>
                                            <span class="text-muted text-xs">Berakhir:
                                                {{ \Carbon\Carbon::parse($org->tgl_berakhir_sk)->format('d M Y') }}</span>
                                        </div>
                                    </li>
                                @endforeach
                                @foreach ($rantingKritis as $org)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="badge badge-warning mr-2 text-dark">KRITIS</span>
                                            <strong class="text-dark">{{ $org->name }}</strong>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-warning small font-weight-bold text-dark">Sisa
                                                {{ $org->sisa_hari }} hari</span><br>
                                            <span class="text-muted text-xs">Berakhir:
                                                {{ \Carbon\Carbon::parse($org->tgl_berakhir_sk)->format('d M Y') }}</span>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endhasanyrole


    {{-- ========================================================== --}}
    {{-- 3. ZONA UMUM: WIDGET YANG BISA DILIHAT PAC & RANTING        --}}
    {{-- ========================================================== --}}
    @hasanyrole('super_admin|ketua_pac|sekretaris_pac|bendahara_pac|ketua_ranting|sekretaris_ranting|bendahara_ranting')

        <h5 class="mb-2 font-weight-bold text-dark">Ringkasan Kas Organisasi</h5>
        <div class="row">
            {{-- Kas IPNU --}}
            <div class="col-lg-4 col-12">
                <div class="small-box bg-success shadow-sm">
                    <div class="inner">
                        <h3>Rp {{ number_format($saldo_ipnu ?? 0, 0, ',', '.') }}</h3>
                        <p>Kas IPNU</p>
                    </div>
                    <div class="icon"><i class="fas fa-wallet"></i></div>
                </div>
            </div>
            {{-- Kas IPPNU --}}
            <div class="col-lg-4 col-12">
                <div class="small-box shadow-sm" style="background-color: #d81b60; color: white;">
                    <div class="inner">
                        <h3>Rp {{ number_format($saldo_ippnu ?? 0, 0, ',', '.') }}</h3>
                        <p>Kas IPPNU</p>
                    </div>
                    <div class="icon"><i class="fas fa-wallet"></i></div>
                </div>
            </div>
            {{-- Kas Bersama --}}
            <div class="col-lg-4 col-12">
                <div class="small-box bg-info shadow-sm">
                    <div class="inner">
                        <h3>Rp {{ number_format($saldo_bersama ?? 0, 0, ',', '.') }}</h3>
                        <p>Kas Bersama</p>
                    </div>
                    <div class="icon"><i class="fas fa-handshake"></i></div>
                </div>
            </div>
        </div>

        <h5 class="mb-2 mt-4 font-weight-bold text-dark">Ringkasan Operasional</h5>
        <div class="row">
            <div class="col-lg-4 col-12">
                <div class="small-box bg-primary shadow-sm">
                    <div class="inner">
                        <h3>{{ $total_anggota ?? 0 }}</h3>
                        <p>Total Anggota</p>
                    </div>
                    <div class="icon"><i class="fas fa-users"></i></div>
                </div>
            </div>
            <div class="col-lg-4 col-12">
                <div class="small-box bg-warning shadow-sm">
                    <div class="inner">
                        <h3>{{ $progja_aktif ?? 0 }}</h3>
                        <p>Progja Berjalan</p>
                    </div>
                    <div class="icon"><i class="fas fa-tasks"></i></div>
                </div>
            </div>
            <div class="col-lg-4 col-12">
                <div class="small-box bg-danger shadow-sm">
                    <div class="inner">
                        <h3>{{ $surat_menunggu ?? 0 }}</h3>
                        <p>Surat Menunggu Validasi</p>
                    </div>
                    <div class="icon"><i class="fas fa-envelope"></i></div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <h5 class="font-weight-bold text-secondary border-bottom pb-2">Statistik Pengunjung Publik</h5>
            </div>

            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box shadow-sm">
                    <span class="info-box-icon bg-info"><i class="fas fa-users"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Pengunjung Hari Ini</span>
                        <span class="info-box-number">{{ $statistik['unik_hari_ini'] }} <small>IP Unik</small></span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box shadow-sm">
                    <span class="info-box-icon bg-success"><i class="fas fa-chart-line"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Bulan Ini</span>
                        <span class="info-box-number">{{ $statistik['unik_bulan_ini'] }} <small>IP Unik</small></span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box shadow-sm">
                    <span class="info-box-icon bg-primary"><i class="fas fa-globe"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Keseluruhan</span>
                        <span class="info-box-number">{{ $statistik['unik_total'] }} <small>IP Unik</small></span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box shadow-sm">
                    <span class="info-box-icon bg-warning"><i class="fas fa-mouse-pointer text-white"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Klik (Hits) Hari Ini</span>
                        <span class="info-box-number">{{ $statistik['hits_hari_ini'] }} <small>Halaman</small></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ========================================================== --}}
        {{-- POSISI BARU WIDGET KLASTERISASI: DI ATAS GRAFIK & DI DALAM ROW --}}
        {{-- ========================================================== --}}
        @hasanyrole('super_admin|ketua_pac|sekretaris_pac')
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card card-success shadow-sm">
                        <div class="card-header bg-success text-white">
                            <h3 class="card-title font-weight-bold"><i class="fas fa-layer-group mr-1"></i> Sebaran Klasterisasi
                                Ranting</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="info-box bg-success">
                                        <span class="info-box-icon"><i class="fas fa-star"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Kluster 1 (Utama)</span>
                                            <span class="info-box-number">{{ $jumlah_klaster_1 ?? 0 }} Ranting</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-box bg-info">
                                        <span class="info-box-icon"><i class="fas fa-check"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Kluster 2 (Sedang)</span>
                                            <span class="info-box-number">{{ $jumlah_klaster_2 ?? 0 }} Ranting</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-box bg-warning">
                                        <span class="info-box-icon"><i class="fas fa-exclamation text-white"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Kluster 3 (Binaan)</span>
                                            <span class="info-box-number">{{ $jumlah_klaster_3 ?? 0 }} Ranting</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endhasanyrole

        {{-- AREA GRAFIK --}}
        <div class="row mt-4">
            <div class="col-lg-5 col-md-12 mb-4">
                <div class="card card-primary shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-chart-bar mr-1"></i> Grafik Arus Kas</h3>
                    </div>
                    <div class="card-body">
                        <div class="chart">
                            <canvas id="kasChart"
                                style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-info shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-chart-pie mr-1"></i> Status Surat</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="suratChart"
                            style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                    </div>
                </div>
            </div>
            {{-- WIDGET BARU: Grafik Akreditasi (Hanya untuk PAC) --}}
            @hasanyrole('super_admin|ketua_pac')
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card shadow-sm h-100" style="border-top: 3px solid #6f42c1;">
                        <div class="card-header text-white" style="background-color: #6f42c1;">
                            <h3 class="card-title"><i class="fas fa-award mr-1"></i> Hasil Akreditasi</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="akreditasiChart"
                                style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                        </div>
                    </div>
                </div>
            @endhasanyrole
        </div>



        {{-- AREA TABEL SURAT --}}
        <div class="row mt-3">
            <div class="col-12">
                <div class="card card-warning shadow-sm border-warning">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-clipboard-list mr-1"></i> Tugas Tertunda: Surat Menunggu
                            Proses</h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover table-striped text-nowrap">
                            <thead>
                                <tr>
                                    <th style="width: 5%">No</th>
                                    <th>Tanggal Surat</th>
                                    <th>Tujuan</th>
                                    <th>Status Antrean</th>
                                    <th style="width: 15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($daftar_surat_menunggu ?? [] as $index => $surat)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $surat->tanggal_surat ? \Carbon\Carbon::parse($surat->tanggal_surat)->translatedFormat('d F Y') : '-' }}
                                        </td>
                                        <td>{{ $surat->tujuan ?? 'Belum ada tujuan' }}</td>
                                        <td>
                                            <span class="badge badge-warning">
                                                {{ str_replace('_', ' ', strtoupper($surat->status_validasi)) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('surat.keluar.show', $surat->id) }}"
                                                class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i> Proses
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="fas fa-check-circle text-success mb-2" style="font-size: 24px;"></i><br>
                                            Hore! Tidak ada surat yang menunggu validasi hari ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endhasanyrole

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // GRAFIK KAS
            const ctxKas = document.getElementById('kasChart').getContext('2d');
            new Chart(ctxKas, {
                type: 'bar',
                data: {
                    labels: ['Pemasukan', 'Pengeluaran', 'Saldo Akhir'],
                    datasets: [{
                        label: 'Nominal (Rp)',
                        data: [
                            {{ $pemasukan ?? 0 }},
                            {{ $pengeluaran ?? 0 }},
                            {{ $saldo_kas ?? 0 }}
                        ],
                        backgroundColor: [
                            'rgba(40, 167, 69, 0.8)',
                            'rgba(220, 53, 69, 0.8)',
                            'rgba(23, 162, 184, 0.8)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // GRAFIK SURAT
            const ctxSurat = document.getElementById('suratChart').getContext('2d');
            new Chart(ctxSurat, {
                type: 'doughnut',
                data: {
                    labels: ['Menunggu Validasi', 'Selesai'],
                    datasets: [{
                        data: [{{ $surat_menunggu ?? 0 }}, {{ $surat_selesai ?? 0 }}],
                        backgroundColor: ['#ffc107', '#28a745'],
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

            // GRAFIK AKREDITASI (Donut Chart)
            const canvasAkreditasi = document.getElementById('akreditasiChart');
            if (canvasAkreditasi) {
                const ctxAkreditasi = canvasAkreditasi.getContext('2d');
                new Chart(ctxAkreditasi, {
                    type: 'doughnut',
                    data: {
                        labels: ['Predikat A', 'Predikat B', 'Predikat C', 'Predikat D'],
                        datasets: [{
                            data: [
                                {{ $akreditasi_A ?? 0 }},
                                {{ $akreditasi_B ?? 0 }},
                                {{ $akreditasi_C ?? 0 }},
                                {{ $akreditasi_D ?? 0 }}
                            ],
                            // Warna: Hijau (A), Biru (B), Kuning (C), Merah (D)
                            backgroundColor: ['#28a745', '#17a2b8', '#ffc107', '#dc3545'],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom', // Agar labelnya rapi di bawah chart
                                labels: {
                                    boxWidth: 12
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
@endpush
