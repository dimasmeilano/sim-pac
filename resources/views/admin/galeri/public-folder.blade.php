@extends('layouts.public')

@section('title', 'Folder Publik - ' . $folder->nama_folder)

@section('content')
    <div class="container mt-4 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-10">

                <div class="card shadow-lg border-0 rounded-lg overflow-hidden mb-4">
                    {{-- HEADER CARD SERAGAM --}}
                    <div class="card-header bg-info text-white text-center py-4 py-md-5">
                        <i class="fas fa-folder-open fa-3x mb-3 text-warning"></i>
                        <h2 class="font-weight-bold mb-2">{{ $folder->nama_folder }}</h2>
                        <p class="mb-0 text-light">Bagian dari kegiatan: <strong>{{ $folder->kegiatan->nama }}</strong></p>
                    </div>

                    <div class="card-body p-4 p-md-5 bg-light">
                        @if (session('success'))
                            <div class="alert alert-success shadow-sm rounded"><i class="fas fa-check-circle mr-1"></i>
                                {{ session('success') }}</div>
                        @endif

                        {{-- FORM UPLOAD PUBLIK (HANYA JIKA DIIZINKAN) --}}
                        @if ($folder->tipe_akses == 'public')
                            <div class="card border-0 shadow-sm mb-5 rounded-lg border-top border-info"
                                style="border-top-width: 4px !important;">
                                <div class="card-body p-4">
                                    <h5 class="text-info font-weight-bold mb-3"><i class="fas fa-cloud-upload-alt mr-1"></i>
                                        Bantu Dokumentasi Kegiatan!</h5>
                                    <p class="text-muted small mb-3">Punya foto atau dokumen menarik terkait acara ini?
                                        Unggah di sini untuk membagikannya ke seluruh anggota.</p>
                                    <form action="{{ route('galeri.public_upload', $folder->share_token) }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="row align-items-center">
                                            <div class="col-md-5 mb-2">
                                                <input type="file" name="file"
                                                    class="form-control-file bg-white p-2 border rounded shadow-sm"
                                                    accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" required>
                                            </div>
                                            <div class="col-md-5 mb-2">
                                                <input type="text" name="keterangan"
                                                    class="form-control form-control-lg shadow-sm"
                                                    placeholder="Nama Anda / Keterangan foto (Opsional)">
                                            </div>
                                            <div class="col-md-2 mb-2">
                                                <button type="submit"
                                                    class="btn btn-info btn-lg btn-block font-weight-bold shadow-sm"><i
                                                        class="fas fa-upload"></i> Kirim</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endif

                        {{-- DAFTAR ISI FILE --}}
                        <h5 class="font-weight-bold text-secondary mb-3"><i class="fas fa-images mr-1"></i> Isi Folder
                            ({{ $files->count() }} Berkas)</h5>
                        <div class="row">
                            @forelse($files as $file)
                                <div class="col-md-3 col-sm-4 col-6 mb-4">
                                    <div class="card h-100 border-0 shadow-sm rounded-lg overflow-hidden">
                                        <div class="bg-dark text-center position-relative" style="height: 160px;">
                                            @if (in_array(pathinfo($file->file_path, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png']))
                                                <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank">
                                                    <img src="{{ asset('storage/' . $file->file_path) }}" alt="Preview"
                                                        style="width: 100%; height: 100%; object-fit: cover;">
                                                </a>
                                            @else
                                                <div
                                                    class="d-flex align-items-center justify-content-center h-100 text-white bg-secondary">
                                                    <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank"
                                                        class="text-white text-decoration-none">
                                                        <i class="fas fa-file-alt fa-3x mb-2 text-light"></i><br>Buka File
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                        <div
                                            class="card-body p-3 text-center bg-white d-flex flex-column justify-content-center">
                                            <p class="mb-1 font-weight-bold text-truncate" style="font-size: 14px;"
                                                title="{{ $file->nama_file }}">
                                                {{ $file->nama_file }}
                                            </p>
                                            <small class="text-muted d-block text-truncate" style="font-size: 12px;">
                                                {{ $file->keterangan ?? 'Tanpa Keterangan' }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-box-open fa-4x mb-3 opacity-50"></i>
                                        <h5>Folder Masih Kosong</h5>
                                        <p class="small">Belum ada dokumen atau foto yang diunggah ke dalam folder ini.</p>
                                    </div>
                                </div>
                            @endforelse
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
