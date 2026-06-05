@extends('layouts.adminlte')

@section('title', 'Tambah Anggota')
@section('page-title', 'Tambah Anggota Baru')

@section('content')
    <div class="card shadow-sm border-0">
        <div class="card-header bg-success text-white">
            <h3 class="card-title font-weight-bold mt-1">
                <i class="fas fa-user-plus mr-1"></i> Form Pendaftaran Anggota
            </h3>
        </div>

        <form action="{{ route('members.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body px-4 py-4">

                {{-- BAGIAN 1: INFORMASI PRIBADI --}}
                <h5 class="text-success font-weight-bold border-bottom pb-2 mb-3">
                    <i class="fas fa-id-card mr-1"></i> Informasi Pribadi
                </h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name') }}" placeholder="Masukkan nama lengkap sesuai KTP" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>NIK (16 digit) <span class="text-danger">*</span></label>
                            <input type="text" name="nik" class="form-control @error('nik') is-invalid @enderror"
                                value="{{ old('nik') }}" maxlength="16" placeholder="Masukkan 16 digit NIK" required>
                            @error('nik')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir') }}"
                                placeholder="Contoh: Gresik">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control"
                                value="{{ old('tanggal_lahir') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Jenis Kelamin</label>
                            <select name="jk" class="form-control">
                                <option value="">-- Pilih --</option>
                                <option value="L" {{ old('jk') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('jk') == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Pendidikan Terakhir</label>
                            <select name="pendidikan" class="form-control">
                                <option value="">-- Pilih Pendidikan --</option>
                                <option value="SD" {{ old('pendidikan') == 'SD' ? 'selected' : '' }}>SD Sederajat
                                </option>
                                <option value="SMP" {{ old('pendidikan') == 'SMP' ? 'selected' : '' }}>SMP Sederajat
                                </option>
                                <option value="SMA" {{ old('pendidikan') == 'SMA' ? 'selected' : '' }}>SMA Sederajat
                                </option>
                                <option value="D3" {{ old('pendidikan') == 'D3' ? 'selected' : '' }}>D3</option>
                                <option value="S1" {{ old('pendidikan') == 'S1' ? 'selected' : '' }}>S1</option>
                                <option value="S2" {{ old('pendidikan') == 'S2' ? 'selected' : '' }}>S2</option>
                                <option value="S3" {{ old('pendidikan') == 'S3' ? 'selected' : '' }}>S3</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>No HP / WhatsApp <span class="text-danger">*</span></label>
                            <input type="text" name="no_hp" class="form-control @error('no_hp') is-invalid @enderror"
                                value="{{ old('no_hp') }}" placeholder="Contoh: 081234567890" required>
                            @error('no_hp')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- BAGIAN 2: AKUN & AUTENTIKASI --}}
                <h5 class="text-success font-weight-bold border-bottom pb-2 mb-3 mt-4">
                    <i class="fas fa-lock mr-1"></i> Data Akun & Login
                </h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email') }}" placeholder="Email aktif" required>
                            @error('email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Password <span class="text-danger">*</span></label>
                            <input type="password" name="password"
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="Minimal 8 karakter" required>
                            @error('password')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Konfirmasi Password <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control"
                                placeholder="Ulangi password" required>
                        </div>
                    </div>
                </div>

                {{-- BAGIAN 3: DATA KEANGGOTAAN --}}
                <h5 class="text-success font-weight-bold border-bottom pb-2 mb-3 mt-4">
                    <i class="fas fa-users-cog mr-1"></i> Data Keanggotaan
                </h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Asal Organisasi <span class="text-danger">*</span></label>

                            {{-- Jika yang login PAC / Admin, tampilkan Dropdown --}}
                            @hasanyrole('super_admin|sekretaris_pac')
                                <select name="organization_id" class="form-control" required>
                                    <option value="">-- Pilih Organisasi / Ranting --</option>
                                    @foreach ($organizations as $org)
                                        <option value="{{ $org->id }}"
                                            {{ old('organization_id') == $org->id ? 'selected' : '' }}>
                                            {{ $org->name }} ({{ strtoupper($org->type) }})
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                {{-- Jika yang login Ranting, tampilkan teks mati (Read-Only) --}}
                                <input type="text" class="form-control bg-light text-muted font-weight-bold"
                                    value="{{ auth()->user()->organization->name ?? 'Belum ada organisasi' }}" readonly>
                                {{-- Kirim ID secara diam-diam (hidden) ke Controller --}}
                                <input type="hidden" name="organization_id" value="{{ auth()->user()->organization_id }}">
                            @endhasanyrole

                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Status Anggota</label>
                            <select name="status_anggota" class="form-control" required>
                                <option value="aktif" {{ old('status_anggota') == 'aktif' ? 'selected' : '' }}>Aktif
                                </option>
                                <option value="nonaktif" {{ old('status_anggota') == 'nonaktif' ? 'selected' : '' }}>
                                    Nonaktif</option>
                                <option value="meninggal" {{ old('status_anggota') == 'meninggal' ? 'selected' : '' }}>
                                    Meninggal</option>
                                <option value="keluar" {{ old('status_anggota') == 'keluar' ? 'selected' : '' }}>Keluar
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Tanggal Bergabung</label>
                            <input type="date" name="tgl_bergabung" class="form-control"
                                value="{{ old('tgl_bergabung', date('Y-m-d')) }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Foto Profil Kader</label>
                            <div class="custom-file">
                                <input type="file" name="foto" class="custom-file-input" id="customFile"
                                    accept="image/jpeg,image/png,image/jpg">
                                <label class="custom-file-label" for="customFile">Pilih file foto...</label>
                            </div>
                            <small class="text-muted"><i class="fas fa-info-circle"></i> Format yang diizinkan: JPG, JPEG,
                                PNG. Maksimal ukuran 2MB.</small>
                        </div>
                    </div>
                </div>

            </div>
            <div class="card-footer bg-white text-right">
                <a href="{{ route('members.index') }}" class="btn btn-secondary shadow-sm mr-2">
                    <i class="fas fa-times mr-1"></i> Batal
                </a>
                <button type="submit" class="btn btn-success font-weight-bold shadow-sm">
                    <i class="fas fa-save mr-1"></i> Simpan Data Anggota
                </button>
            </div>
        </form>
    </div>

    {{-- Script untuk membuat nama file foto muncul saat dipilih (AdminLTE custom file input) --}}
@section('js')
    <script>
        $(document).ready(function() {
            bsCustomFileInput.init();
        });
    </script>
@endsection

@endsection
