@extends('layouts.adminlte')

@section('title', 'Review Pengajuan Ranting')
@section('page-title', 'Detail & Validasi Pengajuan')

@section('content')
    <div class="mb-3">
        <a href="{{ route('pengajuan.index') }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Antrean
        </a>
    </div>

    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-exclamation-triangle mr-1"></i> {{ session('error') }}
        </div>
    @endif

    <div class="row">
        {{-- KOLOM KIRI: DATA ORGANISASI & PENGURUS --}}
        <div class="col-md-7">

            {{-- Card Status --}}
            <div class="card shadow-sm mb-3">
                <div class="card-body d-flex justify-content-between align-items-center p-3">
                    <h6 class="mb-0 font-weight-bold text-secondary">Status Pengajuan:</h6>
                    @if ($pengajuan->status == 'menunggu_validasi')
                        <span class="badge badge-warning px-3 py-2 text-dark"><i class="fas fa-clock"></i> Menunggu
                            Validasi</span>
                    @elseif ($pengajuan->status == 'disetujui')
                        <span class="badge badge-success px-3 py-2"><i class="fas fa-check-double"></i> Disetujui &
                            Disahkan</span>
                    @elseif ($pengajuan->status == 'revisi')
                        <span class="badge badge-info px-3 py-2"><i class="fas fa-edit"></i> Menunggu Revisi</span>
                    @else
                        <span class="badge badge-danger px-3 py-2"><i class="fas fa-times"></i> Ditolak</span>
                    @endif
                </div>
            </div>

            {{-- Card Profil Organisasi --}}
            <div class="card card-primary shadow-sm">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-sitemap mr-1"></i> Profil Organisasi</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <tbody>
                            <tr>
                                <th width="35%">Nama Organisasi</th>
                                <td class="font-weight-bold text-primary">{{ $pengajuan->name }}</td>
                            </tr>
                            <tr>
                                <th>Tingkat / Jenis</th>
                                <td class="text-uppercase">{{ $pengajuan->type }} - {{ $pengajuan->jenis_organisasi }}</td>
                            </tr>
                            <tr>
                                <th>Masa Bhakti</th>
                                <td>{{ $pengajuan->periode }}</td>
                            </tr>
                            <tr>
                                <th>Email Resmi</th>
                                <td>{{ $pengajuan->email_organisasi ?: '-' }}</td>
                            </tr>
                            <tr>
                                <th>Alamat Sekretariat</th>
                                <td>{{ $pengajuan->alamat }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Card Data Pengurus --}}
            <div class="card card-info shadow-sm mt-3">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-users mr-1"></i> Data Pengurus Utama</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th width="50%" class="text-center">Kapasitas Ketua</th>
                                <th width="50%" class="text-center">Kapasitas Sekretaris</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <strong>{{ $pengajuan->ketua_name }}</strong> ({{ $pengajuan->ketua_jk }})<br>
                                    <i class="fas fa-envelope text-muted text-sm w-20px"></i>
                                    {{ $pengajuan->ketua_email }}<br>
                                    <i class="fab fa-whatsapp text-success text-sm w-20px"></i>
                                    {{ $pengajuan->ketua_no_hp }}
                                </td>
                                <td>
                                    <strong>{{ $pengajuan->sekretaris_name }}</strong>
                                    ({{ $pengajuan->sekretaris_jk }})<br>
                                    <i class="fas fa-envelope text-muted text-sm w-20px"></i>
                                    {{ $pengajuan->sekretaris_email }}<br>
                                    <i class="fab fa-whatsapp text-success text-sm w-20px"></i>
                                    {{ $pengajuan->sekretaris_no_hp }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: BERKAS & VALIDASI --}}
        <div class="col-md-5">

            {{-- PANEL AKSI VALIDASI (Hanya tampil jika belum disetujui) --}}
            @if ($pengajuan->status == 'menunggu_validasi' || $pengajuan->status == 'revisi')
                <div class="card card-warning shadow-sm border-warning">
                    <div class="card-header">
                        <h3 class="card-title text-dark font-weight-bold"><i class="fas fa-gavel mr-1"></i> Panel Eksekusi
                        </h3>
                    </div>
                    <div class="card-body bg-light">

                        {{-- Tombol Terima --}}
                        <div class="mb-4">
                            <h6 class="font-weight-bold text-success mb-2">Opsi 1: Setujui & Terbitkan Akun</h6>
                            <p class="text-muted small mb-2">Jika semua berkas sah, klik tombol ini untuk men-generate data
                                organisasi dan akun ketua/sekretaris secara otomatis.</p>
                            <form action="{{ route('pengajuan.approve', $pengajuan->id) }}" method="POST"
                                onsubmit="return confirm('Sahkan pengajuan ini? Sistem akan otomatis membuatkan akun dan merakit draf SK/Rekomendasi.');">
                                @csrf
                                <button type="submit" class="btn btn-success btn-block font-weight-bold shadow-sm">
                                    <i class="fas fa-check-circle mr-1"></i> Terima & Sahkan Pengajuan
                                </button>
                            </form>
                        </div>

                        <hr>

                        {{-- Form Tolak / Revisi --}}
                        <div>
                            <h6 class="font-weight-bold text-danger mb-2">Opsi 2: Tolak / Minta Revisi</h6>
                            <form action="{{ route('pengajuan.reject', $pengajuan->id) }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <select name="status" class="form-control form-control-sm mb-2" required>
                                        <option value="revisi">Kembalikan untuk Direvisi</option>
                                        <option value="ditolak">Tolak Sepenuhnya (Berkas Tidak Sah)</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <textarea name="catatan_admin" class="form-control form-control-sm" rows="3"
                                        placeholder="Tulis alasan mengapa ditolak/direvisi..." required></textarea>
                                </div>
                                <button type="submit" class="btn btn-outline-danger btn-sm btn-block">
                                    <i class="fas fa-paper-plane mr-1"></i> Kirim Catatan
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
            @elseif($pengajuan->status == 'disetujui' || $pengajuan->status == 'ditolak')
                {{-- Info Riwayat Validasi --}}
                <div class="alert alert-secondary shadow-sm">
                    <h6 class="font-weight-bold"><i class="fas fa-info-circle"></i> Riwayat Validasi</h6>
                    Dieksekusi oleh: <strong>{{ $pengajuan->validator->name ?? 'Admin' }}</strong><br>
                    Waktu: {{ \Carbon\Carbon::parse($pengajuan->waktu_validasi)->format('d M Y H:i') }}<br>
                    Catatan: <em>{{ $pengajuan->catatan_admin ?: 'Tidak ada catatan' }}</em>
                </div>
            @endif

            {{-- Card Berkas Syarat --}}
            <div class="card shadow-sm border-top-info">
                <div class="card-header bg-white">
                    <h3 class="card-title text-info font-weight-bold"><i class="fas fa-folder-open mr-1"></i> 10 Berkas
                        Syarat</h3>
                </div>
                <div class="card-body p-0">
                    @php
                        $berkasList = [
                            'Surat Permohonan' => $pengajuan->file_surat_permohonan,
                            'SK Konferensi' => $pengajuan->file_sk_konferensi,
                            'Berita Acara Formatur' => $pengajuan->file_ba_formatur,
                            'SK Formatur' => $pengajuan->file_sk_formatur,
                            'Susunan Pengurus' => $pengajuan->file_susunan_pengurus,
                            'Rekomendasi NU' => $pengajuan->file_rekomendasi_nu,
                            'Biodata Pengurus' => $pengajuan->file_biodata_pengurus,
                            'Hasil Konf. & LPJ' => $pengajuan->file_hasil_konferensi_lpj,
                            'Dokumentasi' => $pengajuan->file_dokumentasi,
                            'Profil Organisasi' => $pengajuan->file_profil_organisasi,
                        ];
                    @endphp
                    <ul class="list-group list-group-flush">
                        @foreach ($berkasList as $label => $file)
                            <li class="list-group-item d-flex justify-content-between align-items-center px-3 py-2">
                                <span class="text-sm">{{ $loop->iteration }}. {{ $label }}</span>
                                @if ($file)
                                    <a href="{{ asset('storage/' . $file) }}" target="_blank"
                                        class="btn btn-xs btn-outline-info rounded-pill px-3">
                                        <i class="fas fa-eye"></i> Cek
                                    </a>
                                @else
                                    <span class="badge badge-danger">Kosong</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

        </div>
    </div>
@endsection
