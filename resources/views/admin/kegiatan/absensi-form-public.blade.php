@extends('layouts.public')

@section('title', 'Absensi - ' . $kegiatan->nama)

@section('content')
    <div class="container mt-4 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-lg border-0 rounded-lg overflow-hidden">
                    {{-- HEADER CARD SERAGAM --}}
                    <div class="card-header bg-primary text-white text-center py-4">
                        <h3 class="card-title font-weight-bold mb-0"><i class="fas fa-clipboard-list mr-2"></i> Daftar Hadir
                            Kegiatan</h3>
                        <p class="mb-0 mt-2 text-light">Sistem Informasi Manajemen PAC IPNU IPPNU</p>
                    </div>

                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-4 border-bottom pb-3">
                            <h4 class="font-weight-bold text-dark">{{ $kegiatan->nama }}</h4>
                            <p class="text-muted mb-1"><i class="fas fa-map-marker-alt text-danger mr-1"></i>
                                {{ $kegiatan->tempat }}</p>
                            <p class="text-muted mb-0"><i class="fas fa-calendar-alt text-primary mr-1"></i>
                                {{ date('d/m/Y H:i', strtotime($kegiatan->tgl_mulai)) }} WIB</p>
                        </div>

                        @if ($sudahAbsen)
                            <div class="alert alert-success text-center py-4 rounded-lg shadow-sm">
                                <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
                                <h5 class="font-weight-bold">Terima kasih!</h5>
                                <p class="mb-0">Anda sudah melakukan absensi untuk kegiatan ini.</p>
                            </div>
                        @else
                            <form action="{{ route('absensi.public.store', $kegiatan) }}" method="POST">
                                @csrf
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold text-secondary">Nama Lengkap <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="nama" class="form-control form-control-lg bg-light"
                                        required>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold text-secondary">Asal PAC / Ranting / Instansi</label>
                                    <input type="text" name="asal" class="form-control form-control-lg bg-light"
                                        placeholder="Contoh: PR Sukorejo">
                                </div>
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold text-secondary">Nomor WhatsApp / Telepon</label>
                                    <input type="text" name="no_hp" class="form-control form-control-lg bg-light"
                                        placeholder="Contoh: 08123456789">
                                </div>
                                <div class="form-group mb-4">
                                    <label class="font-weight-bold text-secondary">Status Kehadiran <span
                                            class="text-danger">*</span></label>
                                    <select name="status" class="form-control form-control-lg bg-light" required>
                                        <option value="hadir">✅ Hadir di Lokasi</option>
                                        <option value="izin">📝 Izin Berhalangan</option>
                                        <option value="sakit">🤒 Sakit</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary btn-lg btn-block font-weight-bold shadow-sm">
                                    <i class="fas fa-fingerprint mr-1"></i> Rekam Absensi Saya
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
