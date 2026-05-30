@extends('layouts.adminlte')

@section('title', 'Detail Surat Keluar')
@section('page-title', 'Detail Surat: ' . $suratKeluar->nomor_surat)

@push('styles')
    <style>
        /* Perbaikan CSS Timeline agar tidak hancur di layar/kolom sempit */
        .timeline {
            position: relative;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .timeline:before {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            width: 4px;
            background: #ddd;
            left: 31px;
            border-radius: 2px;
        }

        .timeline>div {
            position: relative;
            margin-bottom: 15px;
            display: block;
        }

        .timeline>div>.fa,
        .timeline>div>.fas,
        .timeline>div>.far {
            background-color: #fff;
            border-radius: 50%;
            font-size: 16px;
            height: 30px;
            left: 18px;
            line-height: 30px;
            position: absolute;
            text-align: center;
            top: 0;
            width: 30px;
            z-index: 1;
            border: 1px solid #ddd;
        }

        .timeline>div>.timeline-item {
            box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
            border-radius: 3px;
            margin-left: 60px;
            margin-right: 0;
            background: #fff;
            color: #444;
            padding: 0;
            word-wrap: break-word;
        }

        .timeline>div>.timeline-item>.time {
            color: #999;
            float: right;
            padding: 10px;
            font-size: 12px;
        }

        .timeline>div>.timeline-item>.timeline-header {
            margin: 0;
            padding: 10px;
            font-size: 14px;
            border-bottom: 1px solid #f4f4f4;
            white-space: normal;
        }

        .timeline>div>.timeline-item>.timeline-body {
            padding: 10px;
            font-size: 13px;
        }

        .time-label {
            position: relative;
            margin-bottom: 10px;
            display: block;
        }

        .time-label>span {
            font-weight: 600;
            padding: 5px 10px;
            border-radius: 4px;
            background-color: #fff;
            display: inline-block;
        }

        .ttd-preview {
            margin-top: 50px;
            width: 100%;
            overflow: auto;
        }

        .ttd-preview .kiri {
            width: 45%;
            float: left;
            text-align: center;
        }

        .ttd-preview .kanan {
            width: 45%;
            float: right;
            text-align: center;
        }

        .ttd-image {
            max-height: 80px;
            margin-bottom: 10px;
        }

        .clearfix {
            clear: both;
        }
    </style>
@endpush

@section('content')
    @php
        // LOGIKA PINTAR UNTUK TOMBOL AKSI
        $user = auth()->user();
        $isCreator = $suratKeluar->created_by == $user->id;
        $jenisOrg = strtolower($user->organization->jenis_organisasi ?? 'ipnu');

        // Cek Role
        $isWasek = $user->hasRole('wakil_sekretaris') || str_contains(strtolower($user->role ?? ''), 'wasek');
        $isSekretaris = $user->hasRole('sekretaris_pac') || str_contains(strtolower($user->role ?? ''), 'sekretaris');
        $isKetua = $user->hasRole('ketua_pac') || str_contains(strtolower($user->role ?? ''), 'ketua');

        // Cek Status ACC Surat Bersama
        $sudahAccSekretarisBersama =
            ($jenisOrg == 'ipnu' && $suratKeluar->acc_sekretaris_ipnu_at) ||
            ($jenisOrg == 'ippnu' && $suratKeluar->acc_sekretaris_ippnu_at);
        $sudahAccKetuaBersama =
            ($jenisOrg == 'ipnu' && $suratKeluar->acc_ipnu_at) || ($jenisOrg == 'ippnu' && $suratKeluar->acc_ippnu_at);
    @endphp

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Informasi Surat</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Nomor Surat</th>
                            <td><code>{{ $suratKeluar->nomor_surat }}</code></td>
                        </tr>
                        <tr>
                            <th>Perihal</th>
                            <td>{{ $suratKeluar->perihal }}</td>
                        </tr>
                        <tr>
                            <th>Tujuan</th>
                            <td>{{ $suratKeluar->tujuan }}</td>
                        </tr>
                        <tr>
                            <th>Penerbit / Jenis</th>
                            <td>
                                @if ($suratKeluar->penerbit_surat == 'bersama')
                                    <span class="badge badge-primary">Surat Bersama (IPNU-IPPNU)</span>
                                @elseif($suratKeluar->penerbit_surat == 'panitia')
                                    <span class="badge badge-warning">Panitia Pelaksana</span>
                                @else
                                    <span class="badge badge-info">Pimpinan (Mandiri)</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Lampiran</th>
                            <td>
                                @if ($suratKeluar->lampiran)
                                    <a href="{{ asset('storage/' . $suratKeluar->lampiran) }}" target="_blank"
                                        class="btn btn-sm btn-info">
                                        <i class="fas fa-download"></i> Download Lampiran
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if ($suratKeluar->status_validasi == 'draft')
                                    <span class="badge badge-secondary">Draft</span>
                                @elseif($suratKeluar->status_validasi == 'menunggu_validasi_wakil')
                                    <span class="badge badge-warning">Menunggu Validasi Wasek</span>
                                @elseif($suratKeluar->status_validasi == 'menunggu_ttd_sekretaris')
                                    <span class="badge badge-info">Menunggu TTD Sekretaris</span>
                                @elseif($suratKeluar->status_validasi == 'menunggu_ttd_ketua')
                                    <span class="badge badge-primary">Menunggu TTD Ketua</span>
                                @elseif($suratKeluar->status_validasi == 'selesai')
                                    <span class="badge badge-success">Selesai & Sah</span>
                                @elseif($suratKeluar->status_validasi == 'ditolak')
                                    <span class="badge badge-danger">Ditolak</span>
                                @else
                                    <span class="badge badge-dark">{{ $suratKeluar->status_validasi }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Isi Surat</th>
                            <td>
                                <div class="card p-4"
                                    style="background: #fff; border: 1px solid #ddd; max-width: 800px; margin: 0 auto; box-shadow: 0 0 10px rgba(0,0,0,0.05);">
                                    <div class="surat-wrapper"
                                        style="font-family: 'Times New Roman', Times, serif; font-size: 12pt; line-height: 1.5; color: #000;">
                                        {!! $suratKeluar->isi_surat !!}
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-primary card-outline">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title">Aksi Validasi</h3>
                </div>
                <div class="card-body">

                    @if ($suratKeluar->status_validasi == 'draft' && $isCreator)
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalAjukan">
                            <i class="fas fa-paper-plane"></i> Ajukan Validasi
                        </button>
                    @endif

                    @if ($suratKeluar->status_validasi == 'menunggu_validasi_wakil' && $suratKeluar->divalidasi_oleh == $user->id)
                        <form action="{{ route('surat.keluar.approve', $suratKeluar->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-info btn-block mb-2"
                                onclick="return confirm('Apakah Anda yakin isi dokumen ini sudah benar?')">
                                <i class="fas fa-check-double"></i> Validasi Dokumen (Wasek)
                            </button>
                        </form>
                    @endif

                    @if ($suratKeluar->status_validasi == 'menunggu_ttd_sekretaris' && $isSekretaris)
                        @if ($suratKeluar->penerbit_surat != 'bersama' || !$sudahAccSekretarisBersama)
                            <form action="{{ route('surat.keluar.approve', $suratKeluar->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-warning btn-block mb-2"
                                    onclick="return confirm('Sematkan tanda tangan Sekretaris?')">
                                    <i class="fas fa-signature"></i> Setujui & TTD Sekretaris
                                </button>
                            </form>
                        @else
                            <button class="btn btn-outline-secondary btn-block mb-2 disabled" disabled>Menunggu Sekretaris
                                Rekan ACC</button>
                        @endif
                    @endif

                    @if ($suratKeluar->status_validasi == 'menunggu_ttd_ketua' && $isKetua)
                        @if ($suratKeluar->penerbit_surat != 'bersama' || !$sudahAccKetuaBersama)
                            <form action="{{ route('surat.keluar.approve', $suratKeluar->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success btn-block mb-2"
                                    onclick="return confirm('Sah-kan dokumen ini dan Generate QR Code TTE?')">
                                    <i class="fas fa-qrcode"></i> Sah-kan & Generate TTE
                                </button>
                            </form>
                        @else
                            <button class="btn btn-outline-secondary btn-block mb-2 disabled" disabled>Menunggu Ketua Rekan
                                ACC</button>
                        @endif
                    @endif

                    @if ($suratKeluar->status_validasi == 'selesai')
                        <a href="{{ route('surat.keluar.download', $suratKeluar->id) }}"
                            class="btn btn-danger btn-block mb-2">
                            <i class="fas fa-file-pdf"></i> Download PDF
                        </a>
                    @endif

                    <a href="{{ route('surat.keluar.index') }}" class="btn btn-default btn-block mt-3">
                        <i class="fas fa-arrow-left"></i> Kembali ke Data Surat
                    </a>
                </div>
            </div>

            <div class="card card-info card-outline">
                <div class="card-header bg-info text-white">
                    <h3 class="card-title">
                        <i class="fas fa-history"></i> Riwayat Surat
                    </h3>
                </div>
                <div class="card-body">
                    <div class="timeline timeline-inverse">
                        <div class="time-label">
                            <span class="bg-info">{{ $suratKeluar->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div>
                            <i class="fas fa-file-alt bg-info"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fas fa-clock"></i>
                                    {{ $suratKeluar->created_at->diffForHumans() }}</span>
                                <h3 class="timeline-header">Surat Dibuat</h3>
                                <div class="timeline-body">
                                    Dibuat oleh: <strong>{{ $suratKeluar->creator->name ?? 'Tidak diketahui' }}</strong>
                                </div>
                            </div>
                        </div>

                        @if ($suratKeluar->diajukan_oleh)
                            <div>
                                <i class="fas fa-paper-plane bg-warning"></i>
                                <div class="timeline-item">
                                    <h3 class="timeline-header">Diajukan Validasi</h3>
                                    <div class="timeline-body">
                                        Diajukan oleh: <strong>{{ $suratKeluar->diajukanOleh->name ?? '-' }}</strong><br>
                                        @if ($suratKeluar->divalidasiOleh)
                                            Validator (Wasek): <strong>{{ $suratKeluar->divalidasiOleh->name }}</strong>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($suratKeluar->divalidasi_oleh && $suratKeluar->tanggal_validasi)
                            <div>
                                <i class="fas fa-check-circle bg-success"></i>
                                <div class="timeline-item">
                                    <span class="time"><i class="fas fa-clock"></i>
                                        {{ \Carbon\Carbon::parse($suratKeluar->tanggal_validasi)->diffForHumans() }}</span>
                                    <h3 class="timeline-header">Divalidasi Wakil Sekretaris</h3>
                                    <div class="timeline-body">
                                        Divalidasi oleh: <strong>{{ $suratKeluar->divalidasiOleh->name ?? '-' }}</strong>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($suratKeluar->ditandatangani_sekretaris_oleh && $suratKeluar->tanggal_ttd_sekretaris)
                            <div>
                                <i class="fas fa-signature bg-primary"></i>
                                <div class="timeline-item">
                                    <span class="time"><i class="fas fa-clock"></i>
                                        {{ \Carbon\Carbon::parse($suratKeluar->tanggal_ttd_sekretaris)->diffForHumans() }}</span>
                                    <h3 class="timeline-header">Ditandatangani Sekretaris</h3>
                                    <div class="timeline-body">
                                        Ditandatangani oleh:
                                        <strong>{{ $suratKeluar->ditandatanganiSekretarisOleh->name ?? '-' }}</strong>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($suratKeluar->ditandatangani_ketua_oleh && $suratKeluar->tanggal_ttd_ketua)
                            <div>
                                <i class="fas fa-signature bg-success"></i>
                                <div class="timeline-item">
                                    <span class="time"><i class="fas fa-clock"></i>
                                        {{ \Carbon\Carbon::parse($suratKeluar->tanggal_ttd_ketua)->diffForHumans() }}</span>
                                    <h3 class="timeline-header">Ditandatangani Ketua</h3>
                                    <div class="timeline-body">
                                        Ditandatangani oleh:
                                        <strong>{{ $suratKeluar->ditandatanganiKetuaOleh->name ?? '-' }}</strong>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($suratKeluar->status_validasi == 'selesai')
                            <div>
                                <i class="fas fa-flag-checkered bg-success"></i>
                                <div class="timeline-item">
                                    <h3 class="timeline-header">Proses Selesai</h3>
                                    <div class="timeline-body">
                                        Surat telah selesai dan siap digunakan.
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($suratKeluar->status_validasi == 'ditolak')
                            <div>
                                <i class="fas fa-times-circle bg-danger"></i>
                                <div class="timeline-item">
                                    <h3 class="timeline-header">Surat Ditolak</h3>
                                    <div class="timeline-body">
                                        Catatan: {{ $suratKeluar->catatan_validasi ?? '-' }}
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div>
                            <i class="far fa-clock bg-gray"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($suratKeluar->status_validasi == 'draft' && $isCreator)
        <div class="modal fade" id="modalAjukan" tabindex="-1" role="dialog" aria-labelledby="modalAjukanLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="{{ route('surat.keluar.ajukan', $suratKeluar->id) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalAjukanLabel">Ajukan Validasi Dokumen</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">
                            <p>Anda akan mengajukan draf surat ini ke Sekretaris untuk divalidasi.</p>

                            <div class="form-group">
                                <label>Pilih Sekretaris (Pemeriksa) <span class="text-danger">*</span></label>

                                @php
                                    $tingkat = strtolower(auth()->user()->organization->type ?? 'pac');
                                    $roleSekretaris = 'sekretaris_' . $tingkat;
                                @endphp

                                <select name="pemeriksa_id" class="form-control" required>
                                    <option value="">-- Pilih Sekretaris --</option>
                                    @foreach (\App\Models\User::role($roleSekretaris)->where('organization_id', auth()->user()->organization_id)->where('id', '!=', auth()->id())->get() as $sekretaris)
                                        <option value="{{ $sekretaris->id }}">{{ $sekretaris->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Ajukan ke
                                Sekretaris</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection
