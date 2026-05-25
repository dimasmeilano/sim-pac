@extends('layouts.public')

@section('title', 'Absensi - ' . $kegiatan->nama)

@section('content')
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card">
                <div class="card-header bg-primary text-white text-center">
                    <h3 class="card-title mb-0">Absensi Kegiatan</h3>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <h4>{{ $kegiatan->nama }}</h4>
                        <p><i class="fas fa-map-marker-alt"></i> {{ $kegiatan->tempat }}</p>
                        <p><i class="fas fa-calendar-alt"></i> {{ date('d/m/Y H:i', strtotime($kegiatan->tgl_mulai)) }}</p>
                    </div>

                    @if ($sudahAbsen)
                        <div class="alert alert-success text-center">
                            <i class="fas fa-check-circle fa-2x mb-2"></i>
                            <h5>Terima kasih!</h5>
                            <p>Anda sudah melakukan absensi untuk kegiatan ini.</p>
                        </div>
                    @else
                        <form action="{{ route('absensi.public.store', $kegiatan) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label>Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="nama" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Asal PAC / Ranting / Instansi</label>
                                <input type="text" name="asal" class="form-control"
                                    placeholder="Contoh: PAC Kebomas, IPNU Gresik">
                            </div>
                            <div class="form-group">
                                <label>Nomor WhatsApp / Telepon</label>
                                <input type="text" name="no_hp" class="form-control" placeholder="Contoh: 08123456789">
                            </div>
                            <div class="form-group">
                                <label>Status Kehadiran</label>
                                <select name="status" class="form-control" required>
                                    <option value="hadir">✅ Hadir</option>
                                    <option value="izin">📝 Izin</option>
                                    <option value="sakit">🤒 Sakit</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-fingerprint"></i> Absen Sekarang
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
