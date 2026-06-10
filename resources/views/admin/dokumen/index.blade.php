@extends('layouts.adminlte')

@section('title', 'Manajemen Dokumen')
@section('page-title')
    {{ $kategori == 'e-library' ? 'E-Library (Perpustakaan Digital)' : 'Repository (Arsip Organisasi)' }}
@endsection

@section('content')
    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible shadow-sm">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h5><i class="icon fas fa-check"></i> Sukses!</h5>
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger shadow-sm">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Navigasi Switcher & Tombol Upload --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="btn-group shadow-sm">
            <a href="{{ route('dokumen.index', ['kategori' => 'repository']) }}"
                class="btn {{ $kategori == 'repository' ? 'btn-primary' : 'btn-default' }}">
                <i class="fas fa-archive mr-1"></i> Repository
            </a>
            <a href="{{ route('dokumen.index', ['kategori' => 'e-library']) }}"
                class="btn {{ $kategori == 'e-library' ? 'btn-primary' : 'btn-default' }}">
                <i class="fas fa-book mr-1"></i> E-Library
            </a>
        </div>

        <button class="btn btn-success shadow-sm" data-toggle="modal" data-target="#modalUpload">
            <i class="fas fa-cloud-upload-alt mr-1"></i> Upload File Baru
        </button>
    </div>

    {{-- Tabel Tampilan File --}}
    <div class="card card-outline {{ $kategori == 'e-library' ? 'card-success' : 'card-primary' }} shadow-sm">
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover table-striped align-middle">
                <thead class="bg-light">
                    <tr>
                        <th style="width: 5%" class="text-center">No</th>
                        <th style="width: 35%">Nama Dokumen</th>
                        <th style="width: 15%">Ukuran</th>
                        <th style="width: 15%">Hak Akses</th>
                        <th style="width: 15%">Pengunggah</th>
                        <th style="width: 15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($dokumen as $index => $doc)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>
                                {{-- Ikon Pintar Berdasarkan Tipe File --}}
                                @if (in_array($doc->tipe_file, ['pdf']))
                                    <i class="fas fa-file-pdf text-danger fa-lg mr-2"></i>
                                @elseif(in_array($doc->tipe_file, ['doc', 'docx']))
                                    <i class="fas fa-file-word text-primary fa-lg mr-2"></i>
                                @elseif(in_array($doc->tipe_file, ['xls', 'xlsx']))
                                    <i class="fas fa-file-excel text-success fa-lg mr-2"></i>
                                @elseif(in_array($doc->tipe_file, ['zip', 'rar']))
                                    <i class="fas fa-file-archive text-warning fa-lg mr-2"></i>
                                @else
                                    <i class="fas fa-file-alt text-secondary fa-lg mr-2"></i>
                                @endif

                                <strong>{{ $doc->nama_dokumen }}</strong><br>
                                <span class="text-muted text-xs">{{ $doc->deskripsi ?? 'Tidak ada deskripsi' }}</span>
                            </td>
                            <td>
                                <span class="badge badge-light border">
                                    {{ $doc->ukuran_file > 1024 ? number_format($doc->ukuran_file / 1024, 2) . ' MB' : number_format($doc->ukuran_file, 0) . ' KB' }}
                                </span><br>
                                <span class="text-xs text-muted">{{ $doc->created_at->format('d M Y') }}</span>
                            </td>
                            <td>
                                @if ($doc->hak_akses == 'publik')
                                    <span class="badge badge-success"><i class="fas fa-globe"></i> Publik</span>
                                @elseif($doc->hak_akses == 'internal')
                                    <span class="badge badge-warning"><i class="fas fa-building"></i> Internal</span>
                                @else
                                    <span class="badge badge-danger"><i class="fas fa-user-secret"></i> Rahasia</span>
                                @endif
                            </td>
                            <td>
                                <span class="text-sm">{{ $doc->user->name }}</span><br>
                                <span class="text-xs text-muted">{{ $doc->organization->name ?? 'Sistem' }}</span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="{{ route('dokumen.download', $doc->id) }}" class="btn btn-sm btn-info"
                                        title="Download">
                                        <i class="fas fa-download"></i>
                                    </a>

                                    {{-- Tombol hapus hanya muncul jika dia Super Admin atau pemilik dokumen --}}
                                    @if (auth()->user()->hasRole('super_admin') || $doc->organization_id == auth()->user()->organization_id)
                                        <form action="{{ route('dokumen.destroy', $doc->id) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Yakin ingin menghapus dokumen ini secara permanen?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <i class="fas fa-folder-open fa-3x mb-3 text-light"></i><br>
                                Belum ada dokumen di
                                <strong>{{ $kategori == 'e-library' ? 'E-Library' : 'Repository' }}</strong> ini.
                            </td>
                        </tr>
                    @endempty
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Upload Dokumen --}}
<div class="modal fade" id="modalUpload" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-cloud-upload-alt mr-2"></i> Upload Dokumen Baru</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {{-- PENTING: enctype="multipart/form-data" wajib ada untuk upload file --}}
            <form action="{{ route('dokumen.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    {{-- KHUSUS SUPER ADMIN: Pilih Organisasi Pemilik Dokumen --}}
                    @if (auth()->user()->hasRole('super_admin'))
                        <div class="form-group border-bottom pb-3">
                            <label class="text-primary"><i class="fas fa-sitemap mr-1"></i> Pemilik Dokumen (Organisasi)
                                <span class="text-danger">*</span></label>
                            <select name="organization_id" class="form-control" required>
                                <option value="">-- Pilih Organisasi Pemilik --</option>
                                @foreach ($organizations as $org)
                                    <option value="{{ $org->id }}">{{ $org->name }}
                                        ({{ strtoupper($org->type) }})</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Sebagai Super Admin, tentukan dokumen ini milik siapa.</small>
                        </div>
                    @endif
                    <div class="form-group">
                        <label>Simpan Ke</label>
                        <select name="kategori" class="form-control" required>
                            <option value="repository" {{ $kategori == 'repository' ? 'selected' : '' }}>Repository
                                (Arsip Organisasi)</option>
                            <option value="e-library" {{ $kategori == 'e-library' ? 'selected' : '' }}>E-Library
                                (Buku/Modul)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Nama Dokumen <span class="text-danger">*</span></label>
                        <input type="text" name="nama_dokumen" class="form-control"
                            placeholder="Contoh: SK Pengesahan PAC 2024-2026" required>
                    </div>

                    <div class="form-group">
                        <label>File Dokumen <span class="text-danger">*</span></label>
                        <input type="file" name="file_dokumen" class="form-control-file border p-1 rounded"
                            required>
                        <small class="text-muted">Maksimal 10MB. Format: PDF, DOCX, XLSX, PPTX, ZIP.</small>
                    </div>

                    <div class="form-group">
                        <label>Hak Akses (Gembok Keamanan) <span class="text-danger">*</span></label>
                        <select name="hak_akses" class="form-control" required>
                            <option value="internal">Internal (Hanya bisa diunduh oleh anggota Ranting/PAC Anda)
                            </option>
                            <option value="publik">Publik (Bisa diunduh oleh siapa saja termasuk Anggota via HP)
                            </option>
                            <option value="rahasia">Rahasia (Hanya Ketua & Sekretaris yang bisa mengunduh)</option>
                        </select>
                    </div>

                    <div class="form-group mb-0">
                        <label>Deskripsi Singkat (Opsional)</label>
                        <textarea name="deskripsi" class="form-control" rows="2" placeholder="Keterangan tambahan file ini..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-upload"></i> Mulai
                        Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
