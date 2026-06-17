@extends('layouts.adminlte')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 text-gray-800">Detail Program Donasi</h1>
            <a href="{{ route('donasi.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="row">
            <div class="col-xl-4 col-lg-5">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Informasi Campaign</h6>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            @if ($donasi->gambar_banner)
                                <img class="img-fluid rounded px-3 px-sm-4 mt-3 mb-4"
                                    style="width: 100%; max-height: 200px; object-fit: cover;"
                                    src="{{ asset('storage/' . $donasi->gambar_banner) }}" alt="Banner Donasi">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center rounded mt-3 mb-4"
                                    style="height: 150px;">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                </div>
                            @endif
                        </div>
                        <h5 class="font-weight-bold">{{ $donasi->judul_campaign }}</h5>
                        <p class="text-muted small">{{ $donasi->deskripsi }}</p>

                        <hr>

                        <div class="mb-3">
                            @php
                                $persentase = 0;
                                if ($donasi->target_donasi > 0) {
                                    $persentase = ($donasi->terkumpul / $donasi->target_donasi) * 100;
                                    if ($persentase > 100) {
                                        $persentase = 100;
                                    }
                                }
                            @endphp

                            <div class="d-flex justify-content-between font-weight-bold small">
                                <span>Terkumpul: Rp {{ number_format($donasi->terkumpul, 0, ',', '.') }}</span>
                                @if ($donasi->target_donasi > 0)
                                    <span>Target: Rp {{ number_format($donasi->target_donasi, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-success">Tanpa Batas Target</span>
                                @endif
                            </div>

                            @if ($donasi->target_donasi > 0)
                                <div class="progress mb-2 mt-1">
                                    <div class="progress-bar bg-success" role="progressbar"
                                        style="width: {{ $persentase }}%" aria-valuenow="{{ $persentase }}"
                                        aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <div class="text-right small text-muted">{{ number_format($persentase, 1) }}% Tercapai</div>
                            @endif
                        </div>

                        <ul class="list-group list-group-flush small">
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span><i class="fas fa-calendar-alt text-primary"></i> Periode</span>
                                <strong>
                                    {{ $donasi->tgl_mulai ? \Carbon\Carbon::parse($donasi->tgl_mulai)->format('d M Y') : '-' }}
                                    s/d
                                    {{ $donasi->tgl_selesai ? \Carbon\Carbon::parse($donasi->tgl_selesai)->format('d M Y') : '-' }}
                                </strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span><i class="fas fa-users text-primary"></i> Total Donatur Valid</span>
                                <strong>{{ $totalDonatur }} Orang</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span><i class="fas fa-info-circle text-primary"></i> Status Program</span>
                                @if ($donasi->status == 'aktif')
                                    <span class="badge badge-success">Aktif</span>
                                @elseif($donasi->status == 'selesai')
                                    <span class="badge badge-secondary">Selesai</span>
                                @else
                                    <span class="badge badge-danger">Dibatalkan</span>
                                @endif
                            </li>
                            <!-- Fitur Salin Link Publik -->
                            <div class="mt-4 p-3 bg-light rounded border">
                                <label class="small font-weight-bold text-dark mb-1"><i
                                        class="fas fa-share-alt text-success"></i> Bagikan Link Program Ini:</label>
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control text-primary bg-white" id="linkCampaign"
                                        value="{{ route('donasi.public.show', $donasi->id) }}" readonly>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-success" type="button" onclick="copyLink()"
                                            title="Salin Link">
                                            <i class="fas fa-copy"></i> Salin
                                        </button>
                                        <a href="{{ route('donasi.public.show', $donasi->id) }}" target="_blank"
                                            class="btn btn-success" title="Buka di Tab Baru">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    </div>
                                </div>
                                <small class="text-muted mt-1 d-block">Sebarkan link ini ke grup WA atau Sosial Media agar
                                    masyarakat bisa berdonasi.</small>
                            </div>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-xl-8 col-lg-7">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Riwayat Transaksi & Verifikasi</h6>
                        @can('manage_donasi')
                            <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modalTransaksiManual">
                                <i class="fas fa-plus"></i> Tambah Transaksi Manual
                            </button>
                        @endcan
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm table-hover">
                                <thead class="thead-light text-center">
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Donatur</th>
                                        <th>Nominal</th>
                                        <th>Bukti</th>
                                        <th>Status</th>
                                        @can('verify_donasi')
                                            <th>Verifikasi</th>
                                        @endcan
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($donasi->transaksis as $trx)
                                        <tr>
                                            <td class="text-center align-middle">
                                                {{ $trx->created_at->format('d/m/Y H:i') }}</td>
                                            <td class="align-middle">
                                                <strong>{{ $trx->alumni ? $trx->alumni->nama_lengkap : $trx->nama_donatur ?? 'Hamba Allah' }}</strong>
                                                @if ($trx->alumni)
                                                    <span class="badge badge-info"><i class="fas fa-user-graduate"></i>
                                                        Alumni</span>
                                                @endif
                                                <br>
                                                <small
                                                    class="text-muted">{{ str_replace('_', ' ', strtoupper($trx->metode_pembayaran)) }}</small>
                                            </td>
                                            <td class="text-right align-middle font-weight-bold text-success">
                                                Rp {{ number_format($trx->nominal, 0, ',', '.') }}
                                            </td>
                                            <td class="text-center align-middle">
                                                @if ($trx->bukti_transfer)
                                                    <a href="{{ asset('storage/' . $trx->bukti_transfer) }}"
                                                        target="_blank" class="btn btn-sm btn-outline-info">
                                                        <i class="fas fa-file-image"></i> Lihat
                                                    </a>
                                                @else
                                                    <span class="text-muted small">Tidak Ada</span>
                                                @endif
                                            </td>
                                            <td class="text-center align-middle">
                                                @if ($trx->status_pembayaran == 'verified')
                                                    <span class="badge badge-success"><i class="fas fa-check"></i>
                                                        Valid</span>
                                                @elseif($trx->status_pembayaran == 'rejected')
                                                    <span class="badge badge-danger"><i class="fas fa-times"></i>
                                                        Ditolak</span>
                                                @else
                                                    <span class="badge badge-warning"><i class="fas fa-clock"></i>
                                                        Pending</span>
                                                @endif
                                            </td>

                                            @can('verify_donasi')
                                                <td class="text-center align-middle">
                                                    @if ($trx->status_pembayaran == 'pending')
                                                        <form action="{{ route('donasi.transaksi.verify', $trx->id) }}"
                                                            method="POST" class="d-inline"
                                                            onsubmit="return confirm('Verifikasi uang ini masuk ke kas?')">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="status_pembayaran" value="verified">
                                                            <button type="submit" class="btn btn-sm btn-success mb-1"
                                                                title="Terima & Masukkan Kas">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        </form>

                                                        <form action="{{ route('donasi.transaksi.verify', $trx->id) }}"
                                                            method="POST" class="d-inline"
                                                            onsubmit="return confirm('Tolak transaksi ini?')">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="status_pembayaran" value="rejected">
                                                            <button type="submit" class="btn btn-sm btn-danger mb-1"
                                                                title="Tolak">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <small class="text-muted">Oleh:
                                                            {{ $trx->verifikator->name ?? 'Sistem' }}</small>
                                                    @endif
                                                </td>
                                            @endcan
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">Belum ada donatur yang
                                                menyumbang pada program ini.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Transaksi Manual -->
    <div class="modal fade" id="modalTransaksiManual" tabindex="-1" role="dialog"
        aria-labelledby="modalTransaksiLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalTransaksiLabel">Input Donasi Manual</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('donasi.transaksi.store', $donasi->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info small">
                            <i class="fas fa-info-circle"></i> Transaksi yang diinput melalui form ini akan otomatis
                            berstatus <strong>Valid</strong> dan masuk ke kalkulasi dana terkumpul.
                        </div>

                        <div class="form-group mb-3">
                            <label>Nama Donatur <span class="text-danger">*</span></label>
                            <input type="text" name="nama_donatur" class="form-control"
                                placeholder="Contoh: H. Abdul (Alumni 2010) / Anonim" required>
                        </div>

                        <div class="form-group mb-3">
                            <label>Nominal (Rp) <span class="text-danger">*</span></label>
                            <input type="number" name="nominal" class="form-control" placeholder="Contoh: 150000"
                                required>
                        </div>

                        <div class="form-group mb-3">
                            <label>Metode Pembayaran <span class="text-danger">*</span></label>
                            <select name="metode_pembayaran" class="form-control" required>
                                <option value="tunai">Tunai (Cash ke Pengurus)</option>
                                <option value="transfer_bank">Transfer Bank</option>
                                <option value="qris">QRIS</option>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label>Pesan / Titipan Doa (Opsional)</label>
                            <textarea name="pesan_harapan" class="form-control" rows="2" placeholder="Semoga berkah..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan
                            Transaksi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function copyLink() {
            var copyText = document.getElementById("linkCampaign");
            copyText.select();
            copyText.setSelectionRange(0, 99999); /* Untuk mobile */

            // Copy ke clipboard
            navigator.clipboard.writeText(copyText.value).then(function() {
                alert("Link berhasil disalin! Silakan paste di WhatsApp atau Instagram.");
            }).catch(function(err) {
                alert("Gagal menyalin link.");
            });
        }
    </script>
@endpush
