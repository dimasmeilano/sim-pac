@extends('layouts.public') {{-- Ganti dengan layout public Anda jika namanya beda --}}

@section('title', 'Folder Publik - ' . $folder->nama_folder)

@section('content')
    <div class="container mt-4 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-10">

                {{-- HEADER FOLDER --}}
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                    <div class="card-body text-center p-5 bg-primary text-white" style="border-radius: 15px;">
                        <i class="fas fa-folder-open fa-4x mb-3 text-warning"></i>
                        <h2 class="font-weight-bold">{{ $folder->nama_folder }}</h2>
                        <p class="mb-0">📁 Bagian dari kegiatan: <strong>{{ $folder->kegiatan->nama }}</strong></p>
                    </div>
                </div>

                @if (session('success'))
                    <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
                @endif

                {{-- FORM UPLOAD PUBLIK (HANYA JIKA DIIZINKAN) --}}
                @if ($folder->tipe_akses == 'public')
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 10px;">
                        <div class="card-body bg-light">
                            <h5 class="text-primary font-weight-bold mb-3"><i class="fas fa-cloud-upload-alt"></i> Bantu
                                Dokumentasi!</h5>
                            <form action="{{ route('galeri.public_upload', $folder->share_token) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="row align-items-center">
                                    <div class="col-md-5 mb-2">
                                        <input type="file" name="file"
                                            class="form-control-file bg-white p-2 border rounded"
                                            accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" required>
                                    </div>
                                    <div class="col-md-5 mb-2">
                                        <input type="text" name="keterangan" class="form-control"
                                            placeholder="Nama Anda / Keterangan foto (Opsional)">
                                    </div>
                                    <div class="col-md-2 mb-2">
                                        <button type="submit" class="btn btn-success btn-block"><i
                                                class="fas fa-upload"></i> Unggah</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

                {{-- DAFTAR ISI FILE --}}
                <div class="row">
                    @forelse($files as $file)
                        <div class="col-md-3 col-sm-4 col-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm rounded-lg overflow-hidden">
                                <div class="bg-dark text-center position-relative" style="height: 150px;">
                                    @if (in_array(pathinfo($file->file_path, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png']))
                                        <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $file->file_path) }}" alt="Preview"
                                                style="width: 100%; height: 100%; object-fit: cover;">
                                        </a>
                                    @else
                                        <div class="d-flex align-items-center justify-content-center h-100 text-white">
                                            <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank"
                                                class="text-white text-decoration-none">
                                                <i class="fas fa-file-alt fa-3x mb-2 text-info"></i><br>Buka File
                                            </a>
                                        </div>
                                    @endif
                                </div>
                                <div class="card-body p-2 text-center bg-white">
                                    <p class="mb-0 font-weight-bold text-truncate" style="font-size: 13px;"
                                        title="{{ $file->nama_file }}">
                                        {{ $file->nama_file }}
                                    </p>
                                    <small class="text-muted" style="font-size: 11px;">
                                        {{ $file->keterangan ?? 'Tanpa Keterangan' }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5 text-muted">
                            <i class="fas fa-box-open fa-4x mb-3 text-light"></i>
                            <h5>Folder Masih Kosong</h5>
                        </div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>
@endsection
