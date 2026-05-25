@extends('layouts.adminlte')

@section('title', 'Form Absensi')
@section('page-title', 'Absensi: ' . $kegiatan->nama)

@section('content')
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title">
                        <i class="fas fa-fingerprint"></i> Form Absensi
                    </h3>
                </div>
                <div class="card-body">
                    @if ($sudahAbsen)
                        <div class="alert alert-warning text-center">
                            <i class="fas fa-check-circle fa-3x mb-3"></i>
                            <h5>Anda sudah melakukan absensi untuk kegiatan ini!</h5>
                            <a href="{{ route('kegiatan.show', $kegiatan) }}" class="btn btn-primary mt-3">
                                Lihat Detail Kegiatan
                            </a>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <strong>📋 Informasi Kegiatan:</strong><br>
                            {{ $kegiatan->nama }}<br>
                            📍 {{ $kegiatan->tempat }}<br>
                            📅 {{ date('d/m/Y H:i', strtotime($kegiatan->tgl_mulai)) }}
                        </div>

                        <form action="{{ route('absensi.store', $kegiatan) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label>Status Kehadiran</label>
                                <select name="status" class="form-control" required>
                                    <option value="">Pilih Status</option>
                                    <option value="hadir">Hadir</option>
                                    <option value="izin">Izin</option>
                                    <option value="sakit">Sakit</option>
                                    <option value="alpha">Alpha (Tanpa Keterangan)</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Keterangan (jika izin/sakit)</label>
                                <textarea name="keterangan" class="form-control" rows="3" placeholder="Isi keterangan jika berhalangan hadir..."></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-save"></i> Simpan Absensi
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
