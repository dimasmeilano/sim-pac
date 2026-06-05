@extends('layouts.adminlte')

@section('title', 'Perpanjangan SK Kepengurusan')
@section('page-title', 'Form Perpanjangan SK')

@section('content')
    <div class="card card-outline card-success shadow-sm">
        <div class="card-header bg-white">
            <h3 class="card-title text-success font-weight-bold">
                <i class="fas fa-sync-alt mr-1"></i> Form Pengajuan Rekomendasi Baru (Hasil Konferensi)
            </h3>
        </div>

        <div class="card-body bg-light">
            <form action="{{ route('perpanjangan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- BAGIAN 1: PROFIL ORGANISASI (READ ONLY) --}}
                <h5 class="font-weight-bold text-dark border-bottom pb-2 mb-3">Langkah 1: Konfirmasi Organisasi</h5>
                <div class="alert alert-info bg-white text-dark border-info shadow-sm">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Nama Organisasi:</strong><br>
                            {{ $organisasi->name }} <br><br>
                            <strong>Tingkat & Jenis:</strong><br>
                            {{ strtoupper($organisasi->type) }} - {{ strtoupper($organisasi->jenis_organisasi) }}
                        </div>
                        <div class="col-md-6">
                            <strong>Alamat Sekretariat:</strong><br>
                            {{ $organisasi->alamat }}<br><br>
                            <strong>Email Resmi:</strong><br>
                            {{ $organisasi->email ?: '-' }}
                        </div>
                    </div>
                </div>

                {{-- BAGIAN 2: MASA BHAKTI & PENGURUS BARU --}}
                <h5 class="font-weight-bold text-dark border-bottom pb-2 mt-4 mb-3">Langkah 2: Susunan Pengurus Terpilih
                </h5>

                <div class="form-group mb-4">
                    <label>Masa Bhakti Baru <span class="text-danger">*</span></label>
                    <input type="text" name="periode" class="form-control @error('periode') is-invalid @enderror"
                        placeholder="Contoh: 2026-2028" required>
                    <small class="text-muted">Masukkan periode masa bhakti hasil Konferensi/Rapat Anggota terakhir.</small>
                </div>

                <div class="row">
                    {{-- Data Ketua Baru --}}
                    <div class="col-md-6">
                        <div class="card shadow-none border">
                            <div class="card-body">
                                <h6 class="font-weight-bold text-primary mb-3"><i class="fas fa-user-tie"></i> Data Ketua
                                    Terpilih</h6>
                                <div class="form-group">
                                    <label>Nama Lengkap</label>
                                    <input type="text" name="ketua_name" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Email Aktif</label>
                                    <input type="email" name="ketua_email" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Jenis Kelamin</label>
                                    <select name="ketua_jk" class="form-control" required>
                                        <option value="Laki-laki">Laki-laki</option>
                                        <option value="Perempuan">Perempuan</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>No. WhatsApp</label>
                                    <input type="number" name="ketua_no_hp" class="form-control" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Data Sekretaris Baru --}}
                    <div class="col-md-6">
                        <div class="card shadow-none border">
                            <div class="card-body">
                                <h6 class="font-weight-bold text-info mb-3"><i class="fas fa-user"></i> Data Sekretaris
                                    Terpilih</h6>
                                <div class="form-group">
                                    <label>Nama Lengkap</label>
                                    <input type="text" name="sekretaris_name" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Email Aktif</label>
                                    <input type="email" name="sekretaris_email" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Jenis Kelamin</label>
                                    <select name="sekretaris_jk" class="form-control" required>
                                        <option value="Laki-laki">Laki-laki</option>
                                        <option value="Perempuan">Perempuan</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>No. WhatsApp</label>
                                    <input type="number" name="sekretaris_no_hp" class="form-control" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- BAGIAN 3: UPLOAD BERKAS PDF --}}
                <h5 class="font-weight-bold text-dark border-bottom pb-2 mt-4 mb-3">Langkah 3: Upload 10 Persyaratan Konbes
                </h5>
                <div class="alert alert-warning text-sm shadow-sm">
                    <i class="fas fa-info-circle"></i> Pastikan semua berkas dalam format <strong>.PDF</strong> hasil scan
                    asli/berwarna. Maksimal 2MB per file (Kecuali Dokumentasi 5MB).
                </div>

                <div class="row">
                    @php
                        $berkas = [
                            'file_surat_permohonan' => '1. Surat Permohonan Pengesahan',
                            'file_sk_konferensi' => '2. SK Penetapan Hasil Konferensi',
                            'file_ba_formatur' => '3. Berita Acara Rapat Formatur',
                            'file_sk_formatur' => '4. SK Susunan Pengurus (Dari Formatur)',
                            'file_susunan_pengurus' => '5. Lampiran Susunan Pengurus Lengkap',
                            'file_rekomendasi_nu' => '6. Surat Rekomendasi PRNU / MWCNU',
                            'file_biodata_pengurus' => '7. Biodata Pengurus Harian',
                            'file_hasil_konferensi_lpj' => '8. Dokumen LPJ Kepengurusan Demisioner',
                            'file_dokumentasi' => '9. Dokumentasi Kegiatan Konferensi',
                            'file_profil_organisasi' => '10. Profil Organisasi / Database Anggota',
                        ];
                    @endphp

                    @foreach ($berkas as $name => $label)
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="text-sm">{{ $label }} <span class="text-danger">*</span></label>
                                <input type="file" name="{{ $name }}"
                                    class="form-control-file @error($name) is-invalid @enderror" accept=".pdf" required>
                                @error($name)
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    @endforeach
                </div>

                <hr>

                <div class="text-right mt-4">
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary mr-2"><i class="fas fa-times mr-1"></i>
                        Batal</a>
                    <button type="submit" class="btn btn-success font-weight-bold"
                        onclick="return confirm('Pastikan data pengurus dan berkas sudah benar. Lanjutkan pengiriman?')">
                        <i class="fas fa-paper-plane mr-1"></i> Ajukan Perpanjangan SK
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
