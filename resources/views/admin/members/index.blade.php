@extends('layouts.adminlte')

@section('title', 'Manajemen Anggota')
@section('page-title', 'Manajemen Anggota')

@section('content')
    <div class="card shadow-sm">
        {{-- HEADER KARTU: Warna Hijau seperti form Surat Masuk --}}
        <div class="card-header bg-success text-white d-flex align-items-center">
            <h3 class="card-title mt-1 font-weight-bold">
                <i class="fas fa-users mr-1"></i> Daftar Anggota
            </h3>
            <div class="card-tools ml-auto d-flex">
                <!-- Group Tombol Export -->
                <div class="btn-group shadow-sm mr-2">
                    <button type="button" class="btn btn-sm btn-outline-light font-weight-bold dropdown-toggle"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-download mr-1"></i> Export Data
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item text-success font-weight-bold" href="{{ route('members.export') }}">
                            <i class="fas fa-file-excel mr-2"></i> Export ke Excel
                        </a>
                        <!-- Untuk PDF, kita panggil fitur Print Kertas Surat yang pernah kita buat -->
                        <a class="dropdown-item text-danger font-weight-bold" href="{{ route('members.export.pdf') }}">
                            <i class="fas fa-file-pdf mr-2"></i> Export ke PDF Resmi
                        </a>
                    </div>
                </div>

                <button type="button" class="btn btn-sm btn-outline-light font-weight-bold shadow-sm mr-2"
                    data-toggle="modal" data-target="#modalImport">
                    <i class="fas fa-file-import mr-1"></i> Import
                </button>
                <a href="{{ route('members.create') }}" class="btn btn-sm bg-white text-success font-weight-bold shadow-sm">
                    <i class="fas fa-plus-circle mr-1"></i> Tambah Anggota
                </a>
            </div>
        </div>

        {{-- BODY KARTU: Tabel tanpa padding agar rapi --}}
        <div class="card-body p-0">
            {{-- BLOK FILTER & SEARCH --}}
            <div class="bg-light p-3 border-bottom">
                <form action="{{ route('members.index') }}" method="GET">
                    <div class="row align-items-center">

                        {{-- Filter Organisasi (Hanya tampil jika PAC/Admin) --}}
                        @hasanyrole('super_admin|sekretaris_pac')
                            <div class="col-md-4 mb-2 mb-md-0">
                                <select name="organization_id" class="form-control form-control-sm"
                                    onchange="this.form.submit()">
                                    <option value="">-- Semua Ranting / Organisasi --</option>
                                    @foreach ($organizations as $org)
                                        <option value="{{ $org->id }}"
                                            {{ request('organization_id') == $org->id ? 'selected' : '' }}>
                                            {{ $org->name }} ({{ strtoupper($org->type) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endhasanyrole

                        {{-- Kolom Search (Tampil untuk semua) --}}
                        <div class="col-md-5 mb-2 mb-md-0">
                            <div class="input-group input-group-sm">
                                <input type="text" name="search" class="form-control"
                                    placeholder="Cari nama, NIK, atau tempat lahir..." value="{{ request('search') }}">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-search"></i> Cari
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Tombol Reset --}}
                        @if (request()->has('search') || request()->has('organization_id'))
                            <div class="col-md-2">
                                <a href="{{ route('members.index') }}" class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-sync-alt"></i> Reset
                                </a>
                            </div>
                        @endif

                    </div>
                </form>
            </div>
            {{-- END BLOK FILTER & SEARCH --}}
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center" width="50">No</th>
                            <th class="text-center" width="60">Foto</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Organisasi</th>
                            <th>NIK</th>
                            <th>No HP</th>
                            <th>Tgl Bergabung</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($members as $key => $member)
                            <tr>
                                <td class="text-center align-middle">{{ $members->firstItem() + $key }}</td>
                                <td class="text-center align-middle">
                                    @if ($member->foto)
                                        <img src="{{ asset('storage/' . $member->foto) }}" alt="Foto" width="40"
                                            height="40" class="img-circle border shadow-sm" style="object-fit: cover;">
                                    @else
                                        <img src="{{ asset('images/default-avatar.png') }}" alt="Foto" width="40"
                                            height="40" class="img-circle border shadow-sm">
                                    @endif
                                </td>
                                <td class="align-middle font-weight-bold">{{ $member->name }}</td>
                                <td class="align-middle text-muted">{{ $member->email }}</td>
                                <td class="align-middle">
                                    @if ($member->organization)
                                        {{ $member->organization->name }}<br>
                                        <span
                                            class="badge badge-info text-xs">{{ strtoupper($member->organization->type) }}</span>
                                    @else
                                        <span class="badge badge-secondary">-</span>
                                    @endif
                                </td>
                                <td class="align-middle">{{ $member->nik ?? '-' }}</td>
                                <td class="align-middle">{{ $member->no_hp ?? '-' }}</td>
                                <td class="align-middle">
                                    {{ $member->tgl_bergabung ? date('d/m/Y', strtotime($member->tgl_bergabung)) : '-' }}
                                </td>
                                <td class="align-middle text-center">
                                    @if ($member->status_anggota == 'aktif')
                                        <span class="badge badge-success">Aktif</span>
                                    @elseif($member->status_anggota == 'nonaktif')
                                        <span class="badge badge-warning">Nonaktif</span>
                                    @elseif($member->status_anggota == 'meninggal')
                                        <span class="badge badge-danger">Meninggal</span>
                                    @else
                                        <span class="badge badge-secondary">Keluar</span>
                                    @endif
                                </td>
                                <td class="text-center align-middle">
                                    <div class="btn-group">
                                        <a href="{{ route('members.show', $member) }}"
                                            class="btn btn-info btn-sm shadow-sm" title="Lihat">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('members.edit', $member) }}"
                                            class="btn btn-warning btn-sm shadow-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('members.destroy', $member) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm shadow-sm" title="Hapus"
                                                onclick="return confirm('Yakin hapus anggota ini?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            {{-- TAMPILAN KETIKA DATA KOSONG (Persis seperti Surat Masuk) --}}
                            <tr>
                                <td colspan="10" class="text-center py-5">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3 d-block" style="opacity: 0.3;"></i>
                                    <h5 class="text-muted font-weight-bold">Belum ada data anggota</h5>
                                    <p class="text-muted text-sm mb-0">Anggota yang didaftarkan oleh Ranting/Komisariat
                                        akan
                                        otomatis muncul di sini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- FOOTER KARTU: Info Pagination --}}
        <div class="card-footer bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Menampilkan {{ $members->firstItem() ?? 0 }} sampai {{ $members->lastItem() ?? 0 }} dari total
                    {{ $members->total() }} anggota.
                </div>
                <div class="pagination-sm m-0">
                    {{ $members->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL IMPORT EXCEL -->
    <div class="modal fade" id="modalImport" tabindex="-1" role="dialog" aria-labelledby="modalImportLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title font-weight-bold" id="modalImportLabel"><i class="fas fa-file-excel mr-2"></i>
                        Import Data Anggota</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('members.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info text-sm shadow-sm">
                            <i class="fas fa-info-circle mr-1"></i> <b>Perhatian:</b> Format kolom file Excel harus sama
                            persis dengan template yang disediakan oleh sistem.
                        </div>

                        <div class="form-group mb-4">
                            <a href="{{ route('members.template') }}"
                                class="btn btn-outline-success btn-block font-weight-bold shadow-sm">
                                <i class="fas fa-download mr-1"></i> Download Template Excel
                            </a>
                        </div>

                        <div class="form-group">
                            <label>Pilih File Excel (.xlsx / .xls)</label>
                            <div class="custom-file">
                                <input type="file" name="file_excel" class="custom-file-input" id="customFileExcel"
                                    accept=".xlsx, .xls, .csv" required>
                                <label class="custom-file-label" for="customFileExcel">Cari file...</label>
                            </div>
                        </div>

                        <small class="text-muted d-block mt-2">
                            * Password akun hasil import akan disetel otomatis menjadi: <b>anggota123</b><br>
                            * Kolom Jenis Kelamin diisi dengan huruf <b>L</b> atau <b>P</b> saja.<br>
                            * Anggota yang di-import akan otomatis masuk ke organisasi Anda.
                        </small>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary shadow-sm" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success shadow-sm font-weight-bold"><i
                                class="fas fa-upload mr-1"></i> Proses Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            $(document).ready(function() {
                bsCustomFileInput.init(); // Untuk memunculkan nama file di custom input modal
            });
        </script>
    @endpush
@endsection
