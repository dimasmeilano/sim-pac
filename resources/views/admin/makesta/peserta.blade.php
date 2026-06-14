@extends('layouts.adminlte')

@section('content')
    <div class="container-fluid">

        <!-- Header & Tombol Kembali -->

        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Data Peserta Makesta</h1>
            <a href="{{ route('makesta-event.index') }}" class="btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50 mr-1"></i> Kembali
            </a>
        </div>


        <!-- Informasi Singkat Event -->
        <div class="alert alert-info border-left-info shadow-sm mb-4">
            <h6 class="font-weight-bold mb-1">{{ $event->tema }}</h6>
            <small><i class="fas fa-map-marker-alt text-danger mr-1"></i> {{ $event->lokasi }} | <i
                    class="fas fa-users text-primary mr-1"></i> Total Pendaftar: <strong>{{ $event->pesertas->count() }}
                    Orang</strong></small>
        </div>

        <!-- Tabel Data Peserta -->
        <div class="card shadow mb-4 border-bottom-primary">
            <div class="card-header py-3 bg-light d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-list mr-2"></i>Daftar Calon Peserta</h6>

                <!-- Tombol Cetak Masal -->
                @if ($event->pesertas->count() > 0)
                    <div>
                        <a href="{{ route('makesta-event.idcard-masal', $event->id) }}" target="_blank"
                            class="btn btn-sm btn-warning font-weight-bold shadow-sm mr-2">
                            <i class="fas fa-print mr-1"></i> ID Card
                        </a>
                        <a href="{{ route('makesta-event.export-peserta', $event->id) }}" target="_blank"
                            class="btn btn-sm btn-success font-weight-bold shadow-sm">
                            <i class="fas fa-file-pdf mr-1"></i> Export Biodata
                        </a>
                    </div>
                @endif
            </div>

            <!-- KOTAK MAGIC LINK EVALUASI PESERTA -->
            <div class="card shadow-sm border-left-info mb-4">
                <div class="card-body">
                    <h6 class="font-weight-bold text-info mb-2">
                        <i class="fas fa-mobile-alt mr-1"></i> Link Portal Evaluasi Digital (Bagikan ke Peserta)
                    </h6>
                    <div class="input-group">
                        <!-- Input berisi URL yang di-generate otomatis oleh Laravel -->
                        <input type="text" id="linkEvaluasi" class="form-control bg-light font-weight-bold text-primary"
                            value="{{ route('peserta.evaluasi.login', $event->id) }}" readonly>
                        <div class="input-group-append">
                            <!-- Tombol Copy -->
                            <button class="btn btn-info font-weight-bold" type="button" onclick="copyLinkEvaluasi()">
                                <i class="fas fa-copy mr-1"></i> Copy Link
                            </button>
                            <!-- Tombol Tes Buka Link -->
                            <a href="{{ route('peserta.evaluasi.login', $event->id) }}" target="_blank"
                                class="btn btn-secondary" title="Tes buka halaman">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        </div>
                    </div>
                    <small class="mt-2 d-block text-muted">
                        <i class="fas fa-info-circle mr-1"></i> Peserta dapat mengisi evaluasi pemateri, panitia,
                        instruktur, dan refleksi harian melalui link di atas.
                    </small>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                        <thead class="bg-light text-center">
                            <tr>
                                <th width="5%">No</th>
                                <th width="25%">Nama & Info Kontak</th>
                                <th width="20%">Utusan / Pangkalan</th>
                                <th width="20%">Berkas Persyaratan</th>
                                <th width="15%">Status</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($event->pesertas as $peserta)
                                <tr>
                                    <td class="text-center align-middle">{{ $loop->iteration }}</td>
                                    <td class="align-middle">
                                        <strong>{{ $peserta->nama_lengkap }}</strong><br>
                                        <small class="text-muted"><i class="fas fa-venus-mars"></i>
                                            {{ $peserta->jenis_kelamin }}</small><br>
                                        <small class="text-success font-weight-bold"><i class="fab fa-whatsapp"></i>
                                            {{ $peserta->no_wa }}</small>
                                    </td>
                                    <td class="align-middle text-center">{{ $peserta->utusan }}</td>
                                    <td class="align-middle text-center">
                                        @if ($peserta->berkas_syarat)
                                            <a href="{{ asset('storage/' . $peserta->berkas_syarat) }}" target="_blank"
                                                class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-file-download mr-1"></i> Cek Berkas
                                            </a>
                                        @else
                                            <span class="text-muted small">Tidak ada berkas</span>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        @if ($peserta->status_kelulusan == 'Menunggu')
                                            <span class="badge badge-warning px-2 py-1">Menunggu</span>
                                        @elseif($peserta->status_kelulusan == 'Mengikuti')
                                            <span class="badge badge-primary px-2 py-1">Mengikuti</span>
                                        @elseif($peserta->status_kelulusan == 'Lulus')
                                            <span class="badge badge-success px-2 py-1">Lulus</span>
                                        @else
                                            <span class="badge badge-danger px-2 py-1">Tidak Lulus</span>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        <!-- Tombol Cetak ID Card -->
                                        <a href="{{ route('makesta-peserta.idcard', $peserta->id) }}" target="_blank"
                                            class="btn btn-sm btn-warning btn-circle" title="Cetak ID Card">
                                            <i class="fas fa-id-badge"></i>
                                        </a>
                                        <!-- Tombol Modal Edit Status -->
                                        <button class="btn btn-sm btn-info btn-circle" data-toggle="modal"
                                            data-target="#modalStatus{{ $peserta->id }}" title="Ubah Status Peserta">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <!-- Form Hapus Peserta -->
                                        <form action="{{ route('makesta-peserta.destroy', $peserta->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger btn-circle"
                                                onclick="return confirm('Yakin ingin menghapus data peserta ini secara permanen?')"
                                                title="Hapus Peserta">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>

                                        <!-- Modal Edit Status (Tampil saat tombol biru diklik) -->
                                        <div class="modal fade text-left" id="modalStatus{{ $peserta->id }}"
                                            tabindex="-1" role="dialog">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <form
                                                        action="{{ route('makesta-peserta.update-status', $peserta->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-header bg-info text-white">
                                                            <h5 class="modal-title font-weight-bold">Ubah Status Peserta
                                                            </h5>
                                                            <button type="button" class="close text-white"
                                                                data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Nama: <strong>{{ $peserta->nama_lengkap }}</strong></p>
                                                            <div class="form-group">
                                                                <label class="font-weight-bold">Status Kelulusan</label>
                                                                <select name="status_kelulusan" class="form-control"
                                                                    required>
                                                                    <option value="Menunggu"
                                                                        {{ $peserta->status_kelulusan == 'Menunggu' ? 'selected' : '' }}>
                                                                        Menunggu (Baru Daftar)</option>
                                                                    <option value="Mengikuti"
                                                                        {{ $peserta->status_kelulusan == 'Mengikuti' ? 'selected' : '' }}>
                                                                        Mengikuti (Sedang Makesta)</option>
                                                                    <option value="Lulus"
                                                                        {{ $peserta->status_kelulusan == 'Lulus' ? 'selected' : '' }}>
                                                                        Lulus (Sah Anggota)</option>
                                                                    <option value="Tidak Lulus"
                                                                        {{ $peserta->status_kelulusan == 'Tidak Lulus' ? 'selected' : '' }}>
                                                                        Tidak Lulus</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-dismiss="modal">Batal</button>
                                                            <button type="submit"
                                                                class="btn btn-info font-weight-bold">Simpan
                                                                Perubahan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="fas fa-users-slash fa-3x mb-3 opacity-50 d-block"></i>
                                        Belum ada peserta yang mendaftar di event ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- SCRIPT UNTUK COPY LINK -->
    <script>
        function copyLinkEvaluasi() {
            // Ambil elemen input teks
            var copyText = document.getElementById("linkEvaluasi");

            // Pilih isi teksnya
            copyText.select();
            copyText.setSelectionRange(0, 99999); /* Untuk perangkat mobile */

            // Eksekusi perintah copy
            document.execCommand("copy");

            // Beri notifikasi ke panitia
            alert("✅ Link Evaluasi berhasil di-copy!\n\nSilakan paste (tempel) di Grup WA Peserta.");
        }
    </script>
@endsection
