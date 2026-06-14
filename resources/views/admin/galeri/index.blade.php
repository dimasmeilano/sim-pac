@extends('layouts.adminlte')

@section('title', 'Workspace & Galeri')
@section('page-title', 'Galeri & Workspace Terpadu')

@section('content')
    <div class="row">
        {{-- ==============================================
             PANEL KIRI: DIREKTORI KEGIATAN
             ============================================== --}}
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white rounded-top">
                    <h3 class="card-title font-weight-bold"><i class="fas fa-sitemap mr-1"></i> Direktori Kegiatan</h3>
                </div>
                <div class="card-body p-0" style="max-height: 700px; overflow-y: auto;">
                    <div class="list-group list-group-flush">
                        @forelse($kegiatans as $keg)
                            <a href="{{ route('galeri.index', ['kegiatan_id' => $keg->id]) }}"
                                class="list-group-item list-group-item-action {{ $selectedKegiatan && $selectedKegiatan->id == $keg->id ? 'bg-primary text-white' : '' }} border-bottom">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <h6 class="mb-1 font-weight-bold text-truncate" style="max-width: 85%;">
                                        <i
                                            class="fas {{ $selectedKegiatan && $selectedKegiatan->id == $keg->id ? 'fa-folder-open' : 'fa-folder' }} mr-1"></i>
                                        {{ $keg->nama }}
                                    </h6>
                                </div>
                                <small
                                    class="{{ $selectedKegiatan && $selectedKegiatan->id == $keg->id ? 'text-light' : 'text-muted' }}">
                                    {{ $keg->tgl_mulai->format('d M Y') }}
                                </small>
                            </a>
                        @empty
                            <div class="p-4 text-center text-muted">
                                <em>Belum ada Kegiatan terdaftar.</em>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- ==============================================
             PANEL KANAN: FOLDER & FILE MANAGER
             ============================================== --}}
        <div class="col-md-9">
            @if (!$selectedKegiatan)
                <div class="card shadow-sm border-0 h-100 d-flex justify-content-center align-items-center bg-light">
                    <div class="text-center text-muted p-5">
                        <i class="fas fa-hand-point-left fa-4x mb-3 text-secondary"></i>
                        <h4>Pilih Kegiatan di sebelah kiri</h4>
                        <p>Untuk melihat ruang kerja dan galeri.</p>
                    </div>
                </div>
            @else
                {{-- BREADCRUMB NAVIGASI --}}
                <div class="bg-white p-3 rounded shadow-sm mb-3 d-flex justify-content-between align-items-center border-left"
                    style="border-left-width: 4px !important; border-color: #007bff !important;">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent p-0 m-0 font-weight-bold" style="font-size: 1.1rem;">
                            <li class="breadcrumb-item">
                                <a href="{{ route('galeri.index', ['kegiatan_id' => $selectedKegiatan->id]) }}"
                                    class="text-dark">
                                    {{ $selectedKegiatan->nama }}
                                </a>
                            </li>
                            @if ($selectedFolder)
                                <li class="breadcrumb-item active text-primary" aria-current="page">
                                    {{ $selectedFolder->nama_folder }}</li>
                            @endif
                        </ol>
                    </nav>

                    <div>
                        @if (!$selectedFolder)
                            <button class="btn btn-primary btn-sm shadow-sm" data-toggle="modal" data-target="#modalFolder">
                                <i class="fas fa-folder-plus mr-1"></i> Buat Folder
                            </button>
                        @else
                            {{-- TOMBOL EDIT PENGATURAN FOLDER --}}
                            <button class="btn btn-warning btn-sm shadow-sm mr-1" data-toggle="modal"
                                data-target="#modalEditFolder">
                                <i class="fas fa-cog"></i> Pengaturan
                            </button>

                            @if ($selectedFolder->tipe_akses == 'public')
                                <button type="button" class="btn btn-success btn-sm shadow-sm mr-1"
                                    onclick="copyToClipboard('{{ route('galeri.public_folder', $selectedFolder->share_token) }}')">
                                    <i class="fas fa-link mr-1"></i> Salin Link Folder
                                </button>
                            @endif

                            <button class="btn btn-primary btn-sm shadow-sm" data-toggle="modal" data-target="#modalUpload">
                                <i class="fas fa-cloud-upload-alt mr-1"></i> Unggah File
                            </button>
                        @endif
                    </div>
                </div>

                <div class="card shadow-sm border-0 min-vh-50">
                    <div class="card-body bg-light rounded">

                        {{-- KONDISI 1: TAMPILKAN DAFTAR FOLDER --}}
                        @if (!$selectedFolder)
                            <div class="row">
                                @forelse($folders as $folder)
                                    <div class="col-md-4 col-sm-6 mb-4">
                                        <div class="card h-100 border-0 shadow-sm folder-card transition-up"
                                            style="border-radius: 10px;">
                                            <a href="{{ route('galeri.index', ['kegiatan_id' => $selectedKegiatan->id, 'folder_id' => $folder->id]) }}"
                                                class="text-decoration-none text-dark">
                                                <div class="card-body text-center p-4">
                                                    {{-- Ikon Folder --}}
                                                    <div class="position-relative d-inline-block mb-2">
                                                        <i class="fas fa-folder text-warning" style="font-size: 4rem;"></i>
                                                        @if ($folder->tipe_akses == 'private')
                                                            <i class="fas fa-lock text-danger position-absolute"
                                                                style="bottom: 5px; right: -5px; font-size: 1.2rem; background: white; border-radius: 50%; padding: 2px;"></i>
                                                        @else
                                                            <i class="fas fa-globe text-primary position-absolute"
                                                                style="bottom: 5px; right: -5px; font-size: 1.2rem; background: white; border-radius: 50%; padding: 2px;"></i>
                                                        @endif
                                                    </div>

                                                    <h5 class="font-weight-bold mb-1 text-truncate"
                                                        title="{{ $folder->nama_folder }}">{{ $folder->nama_folder }}</h5>
                                                    <small class="text-muted d-block">{{ $folder->galeris()->count() }}
                                                        File Tersimpan</small>
                                                    <hr class="my-2">
                                                    <div class="d-flex justify-content-center align-items-center">
                                                        <small class="text-secondary"><i class="fas fa-user-edit mr-1"></i>
                                                            {{ $folder->creator->name ?? 'Admin' }}</small>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12 text-center py-5 text-muted">
                                        <i class="far fa-folder-open fa-4x mb-3 text-light"></i>
                                        <h5>Belum Ada Folder</h5>
                                        <p>Silakan buat folder baru untuk mulai menyimpan file.</p>
                                    </div>
                                @endforelse
                            </div>

                            {{-- KONDISI 2: TAMPILKAN ISI FILE DALAM FOLDER --}}
                        @else
                            <div class="row">
                                @forelse($files as $file)
                                    <div class="col-md-3 col-sm-4 col-6 mb-4">
                                        <div class="card shadow-sm h-100 border-0"
                                            style="border-radius: 10px; overflow: hidden;">

                                            <div class="position-relative"
                                                style="height: 140px; background-color: #f8f9fa;">
                                                <img src="{{ asset('storage/' . $file->file_path) }}" class="w-100 h-100"
                                                    style="object-fit: cover;" alt="Preview">
                                            </div>

                                            <div class="p-2 border-bottom">
                                                <h6 class="mb-1 text-truncate font-weight-bold text-dark"
                                                    style="font-size: 13px;" title="{{ $file->nama_file }}">
                                                    {{ $file->nama_file }}
                                                </h6>
                                                <div class="text-muted" style="font-size: 11px;">
                                                    <i class="fas fa-user-circle mr-1"></i>
                                                    {{ $file->uploader->name ?? 'Anonim' }}
                                                </div>
                                            </div>

                                            <div class="p-2 bg-light d-flex justify-content-between align-items-center">

                                                <div class="custom-control custom-switch" style="padding-left: 2.25rem;">
                                                    <input type="checkbox" class="custom-control-input toggle-publik"
                                                        id="switch_{{ $file->id }}" data-id="{{ $file->id }}"
                                                        {{ $file->tampil_di_publik ? 'checked' : '' }}>
                                                    <label class="custom-control-label font-weight-bold text-primary"
                                                        for="switch_{{ $file->id }}"
                                                        style="cursor: pointer; font-size: 12px; padding-top: 2px;">
                                                        Publik
                                                    </label>
                                                </div>

                                                <button class="btn btn-sm btn-outline-danger p-1" style="line-height: 1;"
                                                    title="Hapus File">
                                                    <i class="fas fa-trash-alt" style="font-size: 12px; width: 16px;"></i>
                                                </button>

                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12 text-center py-5 text-muted">
                                        <i class="fas fa-box-open fa-4x mb-3 text-light"></i>
                                        <h5>Folder Kosong</h5>
                                        <p>Belum ada file di dalam folder ini.</p>
                                    </div>
                                @endforelse
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- ==============================================
         MODAL 1: BUAT FOLDER BARU
         ============================================== --}}
    @if ($selectedKegiatan)
        <div class="modal fade" id="modalFolder" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><i class="fas fa-folder-plus mr-2"></i> Buat Folder Baru</h5>
                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                    </div>
                    <form action="{{ route('galeri.folder.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="kegiatan_id" value="{{ $selectedKegiatan->id }}">
                        <div class="modal-body bg-light">
                            <div class="form-group">
                                <label>Nama Folder <span class="text-danger">*</span></label>
                                <input type="text" name="nama_folder" class="form-control"
                                    placeholder="Contoh: Kuitansi Rahasia" required>
                            </div>
                            <div class="form-group">
                                <label>Hak Akses Folder <span class="text-danger">*</span></label>
                                <select name="tipe_akses" id="tipe_akses" class="form-control" required>
                                    <option value="private">🔒 Private (Hanya orang tertentu)</option>
                                    <option value="public">🌐 Public (Siapa saja punya link bisa lihat)</option>
                                </select>
                            </div>

                            {{-- Opsi Jika Public --}}
                            <div class="form-group" id="div_upload_publik" style="display: none;">
                                <div class="form-check bg-white border p-2 rounded">
                                    <input type="checkbox" class="form-check-input ml-1" id="izinkan_upload"
                                        name="izinkan_upload_publik" value="1"
                                        style="transform: scale(1.5); margin-top: 5px;">
                                    <label class="form-check-label text-success font-weight-bold ml-4"
                                        for="izinkan_upload">
                                        Izinkan publik ikut mengunggah file
                                    </label>
                                </div>
                                <small class="text-muted d-block mt-1">Gunakan ini untuk meminta peserta mengirimkan foto
                                    ke folder ini.</small>
                            </div>

                            {{-- Opsi Jika Private --}}
                            <div class="form-group" id="div_share_users">
                                <label class="text-primary"><i class="fas fa-user-plus mr-1"></i> Bagikan Akses Ke
                                    (Opsional)</label>
                                <select name="authorized_users[]" class="form-control select2" multiple="multiple"
                                    data-placeholder="Pilih pengurus...">
                                    @foreach ($users as $usr)
                                        <option value="{{ $usr->id }}">{{ $usr->name }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Biarkan kosong jika hanya Anda yang boleh melihat.</small>
                            </div>

                            {{-- Opsi Jika Public --}}
                            <div class="form-group" id="div_upload_publik" style="display: none;">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="izinkan_upload"
                                        name="izinkan_upload_publik" value="1">
                                    <label class="custom-control-label text-success" for="izinkan_upload">Izinkan publik
                                        ikut mengunggah file</label>
                                </div>
                                <small class="text-muted d-block mt-1">Gunakan ini untuk meminta peserta mengirimkan foto
                                    ke folder ini.</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Simpan
                                Folder</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- ==============================================
         MODAL 2: UPLOAD FILE
         ============================================== --}}
    @if ($selectedFolder)
        <div class="modal fade" id="modalUpload" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><i class="fas fa-cloud-upload-alt mr-2"></i> Unggah File</h5>
                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                    </div>
                    <form action="{{ route('galeri.file.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="workspace_folder_id" value="{{ $selectedFolder->id }}">
                        <div class="modal-body bg-light">
                            <div class="alert alert-info py-2">
                                Mengunggah ke folder: <strong>{{ $selectedFolder->nama_folder }}</strong>
                            </div>
                            <div class="form-group">
                                <label>Pilih File (Max 5MB) <span class="text-danger">*</span></label>
                                <input type="file" name="file"
                                    class="form-control-file border p-2 bg-white rounded" required>
                            </div>
                            <div class="form-group">
                                <label>Keterangan Tambahan (Opsional)</label>
                                <input type="text" name="keterangan" class="form-control"
                                    placeholder="Tuliskan catatan...">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-upload mr-1"></i> Mulai
                                Unggah</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
    {{-- ==============================================
         MODAL 3: EDIT PENGATURAN FOLDER
         ============================================== --}}
    @if ($selectedFolder)
        <div class="modal fade" id="modalEditFolder" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title font-weight-bold"><i class="fas fa-cog mr-2"></i> Pengaturan Folder</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <form action="{{ route('galeri.folder.update', $selectedFolder->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body bg-light">
                            <div class="form-group">
                                <label>Nama Folder <span class="text-danger">*</span></label>
                                <input type="text" name="nama_folder" class="form-control"
                                    value="{{ $selectedFolder->nama_folder }}" required>
                            </div>
                            <div class="form-group">
                                <label>Hak Akses Folder <span class="text-danger">*</span></label>
                                <select name="tipe_akses" id="edit_tipe_akses" class="form-control" required>
                                    <option value="private"
                                        {{ $selectedFolder->tipe_akses == 'private' ? 'selected' : '' }}>🔒 Private (Hanya
                                        orang tertentu)</option>
                                    <option value="public"
                                        {{ $selectedFolder->tipe_akses == 'public' ? 'selected' : '' }}>🌐 Public (Siapa
                                        saja punya link bisa lihat)</option>
                                </select>
                            </div>

                            {{-- Opsi Jika Private --}}
                            <div class="form-group" id="edit_div_share_users">
                                <label class="text-primary"><i class="fas fa-user-plus mr-1"></i> Bagikan Akses Ke</label>
                                <select name="authorized_users[]" class="form-control select2" multiple="multiple"
                                    data-placeholder="Pilih pengurus...">
                                    @php
                                        // Ambil ID user yang sudah diberi akses sebelumnya
                                        $authorizedIds = $selectedFolder->authorizedUsers->pluck('id')->toArray();
                                    @endphp
                                    @foreach ($users as $usr)
                                        <option value="{{ $usr->id }}"
                                            {{ in_array($usr->id, $authorizedIds) ? 'selected' : '' }}>{{ $usr->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Opsi Jika Public --}}
                            <div class="form-group" id="edit_div_upload_publik" style="display: none;">
                                <div class="form-check bg-white border p-2 rounded">
                                    <input type="checkbox" class="form-check-input ml-1" id="edit_izinkan_upload"
                                        name="izinkan_upload_publik" value="1"
                                        style="transform: scale(1.5); margin-top: 5px;"
                                        {{ $selectedFolder->izinkan_upload_publik ? 'checked' : '' }}>
                                    <label class="form-check-label text-success font-weight-bold ml-4"
                                        for="edit_izinkan_upload">
                                        Izinkan publik ikut mengunggah file
                                    </label>
                                </div>
                                <small class="text-muted d-block mt-1">Jika dimatikan, orang luar hanya bisa melihat
                                    (Read-Only).</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-warning font-weight-bold"><i
                                    class="fas fa-save mr-1"></i> Perbarui</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- CSS Kustom untuk Efek Hover Kartu --}}
    <style>
        .transition-up {
            transition: all 0.2s ease-in-out;
        }

        .transition-up:hover {
            transform: translateY(-5px);
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
            cursor: pointer;
        }
    </style>
@endsection

@push('scripts')
    {{-- Kita panggil library Select2 langsung dari CDN agar pasti muncul --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // 1. Sabuk Pengaman untuk Select2
            try {
                $('.select2').select2({
                    width: '100%',
                    placeholder: "Pilih pengurus..."
                });
            } catch (error) {
                console.log("Select2 gagal dimuat, tapi sistem tetap berjalan.");
            }

            // 2. Logika Pergantian Tipe Akses (Public vs Private)
            $('#tipe_akses').on('change', function() {
                if ($(this).val() === 'public') {
                    $('#div_share_users').hide(); // Sembunyikan daftar user
                    $('#div_upload_publik').fadeIn(); // Munculkan checkbox upload
                } else {
                    $('#div_share_users').fadeIn(); // Munculkan daftar user
                    $('#div_upload_publik').hide(); // Sembunyikan checkbox upload
                }
            });
            // Logika Pergantian Tipe Akses untuk Modal EDIT
            $('#edit_tipe_akses').on('change', function() {
                if ($(this).val() === 'public') {
                    $('#edit_div_share_users').hide();
                    $('#edit_div_upload_publik').fadeIn();
                } else {
                    $('#edit_div_share_users').fadeIn();
                    $('#edit_div_upload_publik').hide();
                }
            });

            // 3. Pancing agar langsung berubah saat modal baru dibuka
            // Beri jeda sedikit (100ms) agar HTML selesai dirender dulu
            setTimeout(function() {
                $('#tipe_akses').trigger('change');
            }, 100);
        });

        // Fungsi Salin Link
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                alert('Sukses! Link folder berhasil disalin. Silakan bagikan ke grup WhatsApp atau teman Anda.');
            });
        }
    </script>
    <script>
        $(document).ready(function() {
            // Mendeteksi ketika tombol toggle diklik
            $('.toggle-publik').change(function() {
                var status = $(this).prop('checked') ? 1 : 0; // Jika dicentang = 1, jika tidak = 0
                var galeri_id = $(this).data('id'); // Mengambil ID foto

                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: '{{ route('galeri.toggle') }}', // URL tujuan
                    data: {
                        '_token': '{{ csrf_token() }}', // Token keamanan wajib Laravel
                        'status': status,
                        'id': galeri_id
                    },
                    success: function(response) {
                        if (response.success) {
                            // Opsional: Jika Anda pakai SweetAlert atau Toastr, bisa ditaruh di sini
                            console.log(response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Terjadi kesalahan sistem. Silakan muat ulang halaman.');
                    }
                });
            });
        });
    </script>
@endpush
