@extends('layouts.public') <!-- Menginduk ke layout publik Anda -->

@section('title', $campaign->judul_campaign . ' - SIM PAC')

@section('content')

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                @if (session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">
                        <i class="fas fa-times-circle"></i> {{ session('error') }}
                    </div>
                @endif

                <!-- Detail Campaign -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        @if ($campaign->gambar_banner)
                            <img src="{{ asset('storage/' . $campaign->gambar_banner) }}" class="campaign-banner mb-3"
                                alt="Banner">
                        @else
                            <div
                                class="bg-dark campaign-banner mb-3 d-flex align-items-center justify-content-center text-white">
                                <i class="fas fa-image fa-3x"></i>
                            </div>
                        @endif

                        <h2 class="font-weight-bold">{{ $campaign->judul_campaign }}</h2>
                        <p class="text-muted">{{ $campaign->deskripsi }}</p>

                        @php
                            $persentase = 0;
                            if ($campaign->target_donasi > 0) {
                                $persentase = ($campaign->terkumpul / $campaign->target_donasi) * 100;
                                if ($persentase > 100) {
                                    $persentase = 100;
                                }
                            }
                        @endphp

                        <div class="mt-4">
                            <h3 class="text-success font-weight-bold mb-0">Rp
                                {{ number_format($campaign->terkumpul, 0, ',', '.') }}</h3>
                            <p class="small text-muted">terkumpul dari target Rp
                                {{ number_format($campaign->target_donasi, 0, ',', '.') }}</p>

                            @if ($campaign->target_donasi > 0)
                                <div class="progress mb-2" style="height: 10px;">
                                    <div class="progress-bar bg-success" role="progressbar"
                                        style="width: {{ $persentase }}%"></div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Form Donasi (Hanya Muncul Jika Status Aktif) -->
                @if ($campaign->status == 'aktif')
                    <div class="card shadow-sm border-top-success">
                        <div class="card-header bg-white">
                            <h5 class="mb-0 font-weight-bold text-success">Mulai Berdonasi</h5>
                        </div>
                        <div class="card-body">
                            <!-- INFO REKENING DINAMIS -->
                            <div class="alert alert-info small mb-4">
                                <strong>Silakan transfer donasi Anda ke rekening/E-Wallet berikut:</strong><br>
                                <div class="mt-2 text-dark font-weight-bold">
                                    {!! nl2br(e($campaign->informasi_rekening)) !!}
                                </div>
                                <hr class="border-info my-2">
                                Jika sudah transfer, mohon konfirmasi dengan mengunggah bukti/struk pada formulir di
                                bawah ini.
                            </div>

                            <form action="{{ route('donasi.public.store', $campaign->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label>Nama Lengkap (Atau Hamba Allah)</label>
                                    <input type="text" name="nama_donatur" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Nominal Transfer (Rp)</label>
                                    <input type="number" name="nominal" class="form-control" placeholder="Contoh: 50000"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label>Metode Transfer</label>
                                    <select name="metode_pembayaran" class="form-control" required>
                                        <option value="transfer_bank">Transfer Bank</option>
                                        <option value="qris">Scan QRIS</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Upload Bukti Transfer / Struk <span class="text-danger">*</span></label>
                                    <input type="file" name="bukti_transfer" class="form-control-file" accept="image/*"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label>Pesan, Doa, atau Harapan (Opsional)</label>
                                    <textarea name="pesan_harapan" class="form-control" rows="3" placeholder="Semoga donasi ini bermanfaat..."></textarea>
                                </div>

                                <button type="submit" class="btn btn-success btn-lg btn-block mt-4">
                                    <i class="fas fa-paper-plane"></i> Konfirmasi Donasi Saya
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <!-- Pesan Jika Campaign Selesai -->
                    <div class="alert alert-warning text-center shadow-sm">
                        <i class="fas fa-info-circle fa-2x mb-2"></i><br>
                        <strong>Program Donasi Ini Telah Berakhir.</strong><br>
                        Terima kasih kepada seluruh donatur yang telah berpartisipasi.
                    </div>
                @endif

            </div>
        </div>
    </div>
@endsection
