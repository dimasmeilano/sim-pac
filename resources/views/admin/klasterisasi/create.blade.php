@extends('layouts.adminlte')

@section('content')
    @php
        // Ambil jenis organisasi dari user yang login (default IPNU jika kosong)
        $jenis_organisasi = auth()->user()->organization->jenis ?? 'IPNU';
    @endphp

    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 text-gray-800"><i class="fas fa-edit text-success"></i> Form Pengajuan Klasterisasi</h1>
            <a href="{{ route('klasterisasi.index') }}" class="btn btn-secondary shadow-sm"><i class="fas fa-arrow-left"></i>
                Kembali</a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow" role="alert">
                <h5><i class="icon fas fa-ban"></i> Terjadi Kesalahan!</h5>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <form action="{{ route('klasterisasi.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- INFORMASI PERIODE & JENIS ORGANISASI -->
            <div class="card shadow mb-4 border-bottom-{{ $jenis_organisasi == 'IPNU' ? 'success' : 'warning' }}">
                <div class="card-body">
                    <div class="form-group row mb-0 align-items-center">
                        <label class="col-sm-3 col-form-label font-weight-bold text-dark h5 mb-0">Periode Penilaian
                            Aktif:</label>
                        <div class="col-sm-3">
                            <input type="text" name="periode_penilaian"
                                class="form-control font-weight-bold bg-light text-center"
                                value="{{ date('Y') }}-{{ date('Y') + 1 }}" readonly style="font-size: 16px;">
                        </div>
                        <div class="col-sm-6 text-right">
                            <span
                                class="badge badge-{{ $jenis_organisasi == 'IPNU' ? 'success' : 'warning' }} px-4 py-2 shadow-sm"
                                style="font-size: 16px;">
                                <i class="fas {{ $jenis_organisasi == 'IPNU' ? 'fa-male' : 'fa-female' }}"></i> Borang
                                Khusus {{ $jenis_organisasi }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            @if ($jenis_organisasi == 'IPNU')
                <!-- ==========================================
                     PARAMETER 1 & 2 KHUSUS IPNU
                     ========================================== -->
                <div class="card mb-4 shadow border-left-success">
                    <div class="card-header bg-success text-white font-weight-bold"><i class="fas fa-users"></i> PARAMETER
                        I: Jumlah Penduduk Muslim</div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Persentase Penduduk Muslim:</label>
                            <select name="penduduk_muslim" class="form-control" required>
                                <option value="">-- Pilih Persentase --</option>
                                <option value="60-100">60% - 100% (Bobot 25)</option>
                                <option value="20-59">20% - 59% (Bobot 10)</option>
                                <option value="0-19">0% - 19% (Bobot 5)</option>
                            </select>
                        </div>
                        <div class="row bg-light p-3 rounded">
                            <div class="col-md-6 form-group">
                                <label>Upload Bukti BPS (Gambar/PDF):</label>
                                <input type="file" name="p1_file_bukti" class="form-control-file">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Link Data BPS (Opsional):</label>
                                <input type="url" name="p1_link_bps" class="form-control"
                                    placeholder="https://gresikkab.bps.go.id/...">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4 shadow border-left-success">
                    <div class="card-header bg-success text-white font-weight-bold"><i class="fas fa-school"></i> PARAMETER
                        II: Jumlah Pesantren & Lembaga NU</div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Jumlah Lembaga Secara Keseluruhan:</label>
                            <select name="jumlah_pesantren" class="form-control" required>
                                <option value="">-- Pilih Jumlah --</option>
                                <option value="lebih_3">> 3 Lembaga (Bobot 25)</option>
                                <option value="2_sampai_3">2 - 3 Lembaga (Bobot 10)</option>
                                <option value="kurang_2">
                                    < 2 Lembaga (Bobot 5)</option>
                            </select>
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <label class="font-weight-bold text-success"><i class="fas fa-graduation-cap"></i> Tabel
                                    Lembaga Ma'arif NU</label>
                                <table class="table table-bordered table-hover table-sm">
                                    <thead class="bg-light text-center">
                                        <tr>
                                            <th>Nama Lembaga</th>
                                            <th>Alamat</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @for ($i = 0; $i < 3; $i++)
                                            <tr>
                                                <td><input type="text" name="p2_tabel_lembaga[{{ $i }}][nama]"
                                                        class="form-control form-control-sm"></td>
                                                <td><input type="text"
                                                        name="p2_tabel_lembaga[{{ $i }}][alamat]"
                                                        class="form-control form-control-sm"></td>
                                            </tr>
                                        @endfor
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <label class="font-weight-bold text-success"><i class="fas fa-mosque"></i> Tabel Pondok
                                    Pesantren</label>
                                <table class="table table-bordered table-hover table-sm">
                                    <thead class="bg-light text-center">
                                        <tr>
                                            <th>Nama Pesantren</th>
                                            <th>Alamat</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @for ($i = 0; $i < 3; $i++)
                                            <tr>
                                                <td><input type="text"
                                                        name="p2_tabel_pesantren[{{ $i }}][nama]"
                                                        class="form-control form-control-sm"></td>
                                                <td><input type="text"
                                                        name="p2_tabel_pesantren[{{ $i }}][alamat]"
                                                        class="form-control form-control-sm"></td>
                                            </tr>
                                        @endfor
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- ==========================================
                     PARAMETER 1 & 2 KHUSUS IPPNU
                     ========================================== -->
                <div class="card mb-4 shadow border-left-warning">
                    <div class="card-header bg-warning text-dark font-weight-bold"><i class="fas fa-sitemap"></i> PARAMETER
                        I: Jumlah Pimpinan Aktif (IPPNU)</div>
                    <div class="card-body">
                        <label class="font-weight-bold text-warning">Daftar Pimpinan Ranting / Komisariat di Bawah
                            Anda:</label>
                        <table class="table table-bordered table-sm text-center">
                            <thead class="bg-light">
                                <tr>
                                    <th>Nama Desa / Sekolah</th>
                                    <th>Nama Pimpinan</th>
                                    <th>Status SP</th>
                                </tr>
                            </thead>
                            <tbody>
                                @for ($i = 0; $i < 3; $i++)
                                    <tr>
                                        <td><input type="text" name="p1_tabel_pimpinan[{{ $i }}][desa]"
                                                class="form-control form-control-sm" placeholder="Contoh: Desa Sembayat">
                                        </td>
                                        <td><input type="text" name="p1_tabel_pimpinan[{{ $i }}][nama]"
                                                class="form-control form-control-sm"
                                                placeholder="Contoh: PR IPPNU Sembayat"></td>
                                        <td>
                                            <select name="p1_tabel_pimpinan[{{ $i }}][sp]"
                                                class="form-control form-control-sm">
                                                <option value="Sudah">Sudah Memiliki SP</option>
                                                <option value="Belum">Belum Memiliki SP</option>
                                            </select>
                                        </td>
                                    </tr>
                                @endfor
                            </tbody>
                        </table>
                        <div class="form-group mt-3 bg-light p-3 rounded">
                            <label class="font-weight-bold">Hasil Persentase Keaktifan Pimpinan (%):</label>
                            <input type="number" step="0.01" name="p1_persentase_aktif" class="form-control"
                                placeholder="Contoh: 85.5" required>
                            <small class="text-muted">*Hitung persentase dari total PR/PK yang aktif dibagi total PR/PK
                                keseluruhan.</small>
                        </div>
                    </div>
                </div>

                <div class="card mb-4 shadow border-left-warning">
                    <div class="card-header bg-warning text-dark font-weight-bold"><i class="fas fa-tasks"></i> PARAMETER
                        II: Program Kerja Terlaksana (IPPNU)</div>
                    <div class="card-body">
                        <label class="font-weight-bold text-warning">Daftar Program Kerja:</label>
                        <table class="table table-bordered table-sm text-center">
                            <thead class="bg-light">
                                <tr>
                                    <th>Nama Program Kerja</th>
                                    <th>Status Terlaksana</th>
                                </tr>
                            </thead>
                            <tbody>
                                @for ($i = 0; $i < 4; $i++)
                                    <tr>
                                        <td><input type="text"
                                                name="p2_tabel_proker[{{ $i }}][nama_proker]"
                                                class="form-control form-control-sm" placeholder="Nama Proker..."></td>
                                        <td>
                                            <select name="p2_tabel_proker[{{ $i }}][status]"
                                                class="form-control form-control-sm">
                                                <option value="Terlaksana">Terlaksana</option>
                                                <option value="Belum">Belum Terlaksana</option>
                                            </select>
                                        </td>
                                    </tr>
                                @endfor
                            </tbody>
                        </table>
                        <div class="form-group mt-3 bg-light p-3 rounded">
                            <label class="font-weight-bold">Hasil Persentase Proker Terlaksana (%):</label>
                            <input type="number" step="0.01" name="p2_persentase_proker" class="form-control"
                                placeholder="Contoh: 75" required>
                        </div>
                    </div>
                </div>
            @endif

            <!-- ==========================================
                 PARAMETER 3 & 4 (BERLAKU UNTUK KEDUANYA)
                 ========================================== -->
            <div class="card mb-4 shadow border-left-info">
                <div class="card-header bg-info text-white font-weight-bold"><i class="fas fa-handshake"></i> PARAMETER
                    III: Dukungan Stakeholder & Alumni</div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Tingkat Dukungan Stakeholder:</label>
                        <select name="dukungan_stakeholder" class="form-control" required>
                            <option value="">-- Pilih --</option>
                            <option value="kuat">Kuat (Bobot 25)</option>
                            <option value="sedang">Sedang (Bobot 10)</option>
                            <option value="lemah">Lemah (Bobot 5)</option>
                        </select>
                    </div>

                    <label class="font-weight-bold text-info mt-3"><i class="fas fa-file-contract"></i> A. Tabel Program
                        Kerja Sama (MOU)</label>
                    <table class="table table-bordered table-sm text-center">
                        <thead class="bg-light">
                            <tr>
                                <th>Kegiatan</th>
                                <th>Stakeholder</th>
                                <th>No Surat</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for ($i = 0; $i < 2; $i++)
                                <tr>
                                    <td><input type="text" name="p3_tabel_mou[{{ $i }}][kegiatan]"
                                            class="form-control form-control-sm"></td>
                                    <td><input type="text" name="p3_tabel_mou[{{ $i }}][stakeholder]"
                                            class="form-control form-control-sm"></td>
                                    <td><input type="text" name="p3_tabel_mou[{{ $i }}][no_surat]"
                                            class="form-control form-control-sm"></td>
                                    <td><input type="date" name="p3_tabel_mou[{{ $i }}][tanggal]"
                                            class="form-control form-control-sm"></td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>

                    <div class="row mt-4">
                        <div class="col-md-5">
                            <label class="font-weight-bold text-info">B. Struktur Majelis Alumni</label>
                            <table class="table table-bordered table-sm text-center">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Jabatan</th>
                                        <th>Nama Lengkap</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Ketua <input type="hidden" name="p3_struktur_alumni[0][jabatan]"
                                                value="Ketua"></td>
                                        <td><input type="text" name="p3_struktur_alumni[0][nama]"
                                                class="form-control form-control-sm"></td>
                                    </tr>
                                    <tr>
                                        <td>Sekretaris <input type="hidden" name="p3_struktur_alumni[1][jabatan]"
                                                value="Sekretaris"></td>
                                        <td><input type="text" name="p3_struktur_alumni[1][nama]"
                                                class="form-control form-control-sm"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-7">
                            <label class="font-weight-bold text-info">C. Pembinaan Alumni</label>
                            <table class="table table-bordered table-sm text-center">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Materi Pembinaan</th>
                                        <th>Nama Pengisi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @for ($i = 0; $i < 2; $i++)
                                        <tr>
                                            <td><input type="text"
                                                    name="p3_kegiatan_alumni[{{ $i }}][kegiatan]"
                                                    class="form-control form-control-sm"></td>
                                            <td><input type="text"
                                                    name="p3_kegiatan_alumni[{{ $i }}][nama_alumni]"
                                                    class="form-control form-control-sm"></td>
                                        </tr>
                                    @endfor
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4 shadow border-left-info">
                <div class="card-header bg-info text-white font-weight-bold"><i class="fas fa-map-marked-alt"></i>
                    PARAMETER IV: Kondisi Geografis</div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Kondisi Akses Geografis Menuju PCNU:</label>
                        <select name="kondisi_geografis" class="form-control" required>
                            <option value="">-- Pilih Akses --</option>
                            <option value="mudah">Mudah (Bobot 20)</option>
                            <option value="sedang">Sedang (Bobot 10)</option>
                            <option value="sulit">Sulit (Bobot 5)</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label>Upload Screenshot Maps:</label>
                            <input type="file" name="p4_file_peta" class="form-control-file border p-2 rounded">
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Deskripsi Infrastruktur:</label>
                            <textarea name="p4_infrastruktur" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Deskripsi Transportasi Umum:</label>
                            <textarea name="p4_transportasi" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg btn-block shadow-sm mb-5">
                <i class="fas fa-paper-plane"></i> Ajukan Portofolio Klasterisasi {{ $jenis_organisasi }}
            </button>
        </form>
    </div>
@endsection
