@extends('layouts.adminlte')

@section('title', 'Edit Transaksi')
@section('page-title', 'Edit Transaksi: ' . $keuangan->judul)

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Form Edit Transaksi</h3>
        </div>
        <form action="{{ route('keuangan.update', $keuangan) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Judul Transaksi</label>
                            <input type="text" name="judul" class="form-control"
                                value="{{ old('judul', $keuangan->judul) }}" required>
                        </div>
                    </div>
                    @if ($keuangan->status_validasi == 'ditolak' && $keuangan->catatan_validasi)
                        <div class="alert alert-danger">
                            <h5><i class="fas fa-exclamation-triangle"></i> Transaksi Ditolak!</h5>
                            <p><strong>Catatan Bendahara:</strong></p>
                            <div class="alert alert-warning">
                                {{ $keuangan->catatan_validasi }}
                            </div>
                            <p class="mb-0">Silakan perbaiki transaksi sesuai catatan di atas, lalu simpan kembali untuk
                                diajukan validasi ulang.</p>
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Jenis Organisasi</label>
                                <input type="text" class="form-control" value="{{ $keuangan->jenis_organisasi_text }}"
                                    readonly disabled>
                                <small class="text-muted">Jenis organisasi tidak dapat diubah setelah transaksi
                                    dibuat</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Jenis Transaksi</label>
                                <select name="jenis" class="form-control" required>
                                    <option value="masuk" {{ $keuangan->jenis == 'masuk' ? 'selected' : '' }}>Pemasukan
                                    </option>
                                    <option value="keluar" {{ $keuangan->jenis == 'keluar' ? 'selected' : '' }}>Pengeluaran
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Nominal</label>
                            <input type="number" name="nominal" class="form-control"
                                value="{{ old('nominal', $keuangan->nominal) }}" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Tanggal</label>
                            <input type="date" name="tanggal" class="form-control"
                                value="{{ old('tanggal', $keuangan->tanggal->format('Y-m-d')) }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Kategori</label>
                            <select name="kategori" class="form-control">
                                <option value="">Pilih Kategori</option>
                                <option value="kegiatan" {{ $keuangan->kategori == 'kegiatan' ? 'selected' : '' }}>Kegiatan
                                </option>
                                <option value="operasional" {{ $keuangan->kategori == 'operasional' ? 'selected' : '' }}>
                                    Operasional</option>
                                <option value="donasi" {{ $keuangan->kategori == 'donasi' ? 'selected' : '' }}>Donasi
                                </option>
                                <option value="iuran" {{ $keuangan->kategori == 'iuran' ? 'selected' : '' }}>Iuran
                                </option>
                                <option value="lainnya" {{ $keuangan->kategori == 'lainnya' ? 'selected' : '' }}>Lainnya
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Program Kerja</label>
                            <select name="program_kerja_id" class="form-control">
                                <option value="">- Tidak Terkait -</option>
                                @foreach ($programKerja as $progja)
                                    <option value="{{ $progja->id }}"
                                        {{ $keuangan->program_kerja_id == $progja->id ? 'selected' : '' }}>
                                        {{ $progja->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Kegiatan Terkait</label>
                            <select name="kegiatan_id" class="form-control">
                                <option value="">- Tidak Terkait -</option>
                                @foreach ($kegiatan as $item)
                                    <option value="{{ $item->id }}"
                                        {{ $keuangan->kegiatan_id == $item->id ? 'selected' : '' }}>
                                        {{ $item->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Bukti Transaksi</label>
                            @if ($keuangan->bukti_file)
                                <div class="mb-2">
                                    <a href="{{ asset('storage/' . $keuangan->bukti_file) }}" target="_blank"
                                        class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> Lihat Bukti
                                    </a>
                                </div>
                            @endif
                            <input type="file" name="bukti" class="form-control" accept="image/*,.pdf">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="3">{{ old('keterangan', $keuangan->keterangan) }}</textarea>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('keuangan.index') }}" class="btn btn-default">Batal</a>
            </div>
        </form>
        @if ($keuangan->penolakan_history)
            <div class="alert alert-secondary">
                <h6><i class="fas fa-history"></i> Riwayat Penolakan:</h6>
                <pre style="font-size: 12px; white-space: pre-wrap;">{{ $keuangan->penolakan_history }}</pre>
            </div>
        @endif
    </div>
@endsection
