@extends('layouts.adminlte')

@section('title', 'Tambah Inventaris')
@section('page-title', 'Tambah Inventaris Baru')

@section('content')
    <div class="card card-success shadow-sm">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-plus-square mr-1"></i> Form Tambah Barang</h3>
        </div>
        <form action="{{ route('inventaris.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="card-body">

                    {{-- INJEKSI DROPDOWN KHUSUS SUPER ADMIN --}}
                    @if (auth()->user()->hasRole('super_admin'))
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <div class="form-group border p-3 bg-light rounded">
                                    <label class="text-primary"><i class="fas fa-sitemap"></i> Pilih Pemilik Aset (Khusus
                                        Super Admin) <span class="text-danger">*</span></label>
                                    <select name="organization_id" class="form-control select2" required>
                                        <option value="">-- Pilih Organisasi PAC / Ranting --</option>
                                        @if (isset($organizations))
                                            @foreach ($organizations as $org)
                                                <option value="{{ $org->id }}"
                                                    {{ old('organization_id') == $org->id ? 'selected' : '' }}>
                                                    {{ $org->nama ?? $org->name }} ({{ strtoupper($org->type ?? 'PAC') }})
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                    @endif
                    {{-- AKHIR INJEKSI --}}
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Kode Barang <span class="text-danger">*</span></label>

                                {{-- Inject kode otomatis ke dalam value --}}
                                <input type="text" name="kode_barang" class="form-control"
                                    value="{{ $kodeOtomatis ?? '' }}" required>

                                <small class="text-info"><i class="fas fa-magic"></i> Digenerate otomatis. Bisa diubah jika
                                    ada
                                    kode fisik.</small>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Nama Barang <span class="text-danger">*</span></label>
                                <input type="text" name="nama_barang" class="form-control"
                                    placeholder="Contoh: Printer Epson L3110" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Jumlah Unit <span class="text-danger">*</span></label>
                                <input type="number" name="jumlah" class="form-control" value="1" min="1"
                                    required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Kondisi Barang <span class="text-danger">*</span></label>
                                <select name="kondisi" class="form-control" required>
                                    <option value="baik">Kondisi Baik</option>
                                    <option value="rusak_ringan">Rusak Ringan (Masih bisa dipakai)</option>
                                    <option value="rusak_berat">Rusak Berat (Tidak bisa dipakai)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Tahun Perolehan</label>
                                <input type="text" name="tahun_perolehan" class="form-control"
                                    placeholder="Contoh: 2023">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Sumber Dana / Asal Barang</label>
                                <input type="text" name="sumber_dana" class="form-control"
                                    placeholder="Contoh: Uang Kas PAC, Hibah Desa">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Foto Barang (Opsional)</label>
                                <input type="file" name="foto_barang" class="form-control-file" accept="image/*">
                                <small class="text-muted">Maksimal 2MB. Format: JPG, PNG.</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Keterangan Tambahan / Lokasi Penyimpanan</label>
                        <textarea name="keterangan" class="form-control" rows="3"
                            placeholder="Contoh: Disimpan di lemari kesekretariatan rak nomor 2"></textarea>
                    </div>
                </div>
                <div class="card-footer text-right bg-light">
                    <a href="{{ route('inventaris.index') }}" class="btn btn-default mr-2">Batal</a>
                    <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Simpan Aset</button>
                </div>
        </form>
    </div>
@endsection
