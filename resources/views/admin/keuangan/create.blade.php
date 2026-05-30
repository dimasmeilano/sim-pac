@extends('layouts.adminlte')

@section('title', 'Tambah Transaksi')
@section('page-title', 'Tambah Transaksi Baru')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Form Tambah Transaksi</h3>
        </div>
        <form action="{{ route('keuangan.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Judul Transaksi <span class="text-danger">*</span></label>
                            <input type="text" name="judul" class="form-control @error('judul') is-invalid @enderror"
                                value="{{ old('judul') }}" placeholder="Contoh: Pembelian ATK Kesekretariatan" required>
                            @error('judul')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Jenis Organisasi <span class="text-danger">*</span></label>
                            <select name="jenis_organisasi" class="form-control" required>
                                @php
                                    $user = auth()->user();
                                    $isSuperAdmin = $user->hasRole('super_admin');
                                    $userJenis = $user->organization?->jenis_organisasi ?? 'bersama';
                                @endphp

                                @if ($isSuperAdmin || $userJenis == 'ipnu')
                                    <option value="ipnu" {{ old('jenis_organisasi') == 'ipnu' ? 'selected' : '' }}>IPNU
                                    </option>
                                @endif

                                @if ($isSuperAdmin || $userJenis == 'ippnu')
                                    <option value="ippnu" {{ old('jenis_organisasi') == 'ippnu' ? 'selected' : '' }}>IPPNU
                                    </option>
                                @endif

                                <option value="bersama" {{ old('jenis_organisasi') == 'bersama' ? 'selected' : '' }}>Bersama
                                    IPNU & IPPNU</option>
                            </select>
                            <small class="text-muted">
                                @if (!$isSuperAdmin && $userJenis == 'ipnu')
                                    Hanya IPNU & Bersama
                                @elseif(!$isSuperAdmin && $userJenis == 'ippnu')
                                    Hanya IPPNU & Bersama
                                @else
                                    Akses semua jenis
                                @endif
                            </small>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Jenis Transaksi <span class="text-danger">*</span></label>
                            <select name="jenis" class="form-control" required>
                                <option value="">- Pilih Jenis -</option>
                                <option value="masuk" {{ old('jenis') == 'masuk' ? 'selected' : '' }}>Pemasukan</option>
                                <option value="keluar" {{ old('jenis') == 'keluar' ? 'selected' : '' }}>Pengeluaran</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Nominal (Rp) <span class="text-danger">*</span></label>
                            <input type="number" name="nominal" class="form-control @error('nominal') is-invalid @enderror"
                                value="{{ old('nominal') }}" placeholder="Contoh: 150000" min="0" required>
                            @error('nominal')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror"
                                value="{{ old('tanggal', date('Y-m-d')) }}" required>
                            @error('tanggal')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Kategori</label>
                            <select name="kategori" class="form-control">
                                <option value="">- Pilih Kategori -</option>
                                <option value="kegiatan" {{ old('kategori') == 'kegiatan' ? 'selected' : '' }}>Kegiatan
                                </option>
                                <option value="operasional" {{ old('kategori') == 'operasional' ? 'selected' : '' }}>
                                    Operasional</option>
                                <option value="donasi" {{ old('kategori') == 'donasi' ? 'selected' : '' }}>Donasi</option>
                                <option value="iuran" {{ old('kategori') == 'iuran' ? 'selected' : '' }}>Iuran Anggota
                                </option>
                                <option value="lainnya" {{ old('kategori') == 'lainnya' ? 'selected' : '' }}>Lainnya
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Program Kerja Terkait</label>
                            <select name="program_kerja_id" class="form-control">
                                <option value="">- Tidak Terkait -</option>
                                @foreach ($programKerja as $progja)
                                    <option value="{{ $progja->id }}"
                                        {{ old('program_kerja_id') == $progja->id ? 'selected' : '' }}>
                                        {{ $progja->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Kegiatan Terkait</label>
                            <select name="kegiatan_id" class="form-control">
                                <option value="">- Tidak Terkait -</option>
                                @foreach ($kegiatan as $item)
                                    <option value="{{ $item->id }}"
                                        {{ old('kegiatan_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->nama }} ({{ date('d/m/Y', strtotime($item->tgl_mulai)) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Bukti Transaksi (Struk/Nota)</label>
                            <input type="file" name="bukti" class="form-control" accept="image/*,.pdf"
                                style="padding: 3px;">
                            <small class="text-muted">Format: JPG, PNG, PDF. Max 2MB.</small>
                        </div>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Keterangan Tambahan</label>
                            <textarea name="keterangan" class="form-control" rows="3"
                                placeholder="Tuliskan detail transaksi jika diperlukan...">{{ old('keterangan') }}</textarea>
                        </div>
                    </div>
                </div>

            </div>
            <div class="card-footer text-right">
                <a href="{{ route('keuangan.index') }}" class="btn btn-default mr-2"><i class="fas fa-times"></i> Batal</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Transaksi</button>
            </div>
        </form>
    </div>
@endsection
