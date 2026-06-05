@extends('layouts.adminlte')

@section('title', 'Detail Surat Masuk')
@section('page-title', 'Detail & Disposisi Surat')

@section('content')
    <div class="mb-3">
        <a href="{{ route('surat.masuk.index') }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
        </a>
    </div>

    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
        </div>
    @endif

    <div class="row">
        {{-- KOLOM KIRI: INFORMASI SURAT & LAMPIRAN --}}
        <div class="col-md-7">
            <div class="card card-success shadow-sm">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-envelope-open-text mr-1"></i> Lembar Informasi Surat</h3>
                    <div class="card-tools">
                        @if ($suratMasuk->status == 'baru')
                            <span class="badge badge-danger px-2 py-1"><i class="fas fa-envelope"></i> Status: Baru</span>
                        @elseif ($suratMasuk->status == 'diproses')
                            <span class="badge badge-warning px-2 py-1 text-dark"><i class="fas fa-spinner fa-spin"></i>
                                Status: Didisposisikan</span>
                        @else
                            <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle"></i> Status:
                                Selesai</span>
                        @endif
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <tbody>
                            <tr>
                                <th width="30%" class="text-muted">Nomor Surat</th>
                                <td class="font-weight-bold text-primary h5">{{ $suratMasuk->nomor_surat }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Asal Surat / Pengirim</th>
                                <td class="font-weight-bold">{{ $suratMasuk->pengirim }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Perihal</th>
                                <td>{{ $suratMasuk->perihal }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Tanggal Surat</th>
                                <td>{{ \Carbon\Carbon::parse($suratMasuk->tanggal_surat)->format('d F Y') }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Tanggal Diterima</th>
                                <td>{{ \Carbon\Carbon::parse($suratMasuk->tanggal_diterima)->format('d F Y') }} <small
                                        class="text-muted">(Dicatat oleh:
                                        {{ $suratMasuk->penerima->name ?? 'Sistem' }})</small></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Ringkasan / Isi</th>
                                <td>{{ $suratMasuk->isi_surat ?: '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- PANEL TAMPILAN LAMPIRAN --}}
            <div class="card shadow-sm border-top-info">
                <div class="card-header bg-light">
                    <h3 class="card-title text-info font-weight-bold"><i class="fas fa-paperclip mr-1"></i> Berkas Lampiran
                    </h3>
                    <div class="card-tools">
                        @if ($suratMasuk->lampiran)
                            <a href="{{ asset('storage/' . $suratMasuk->lampiran) }}" target="_blank"
                                class="btn btn-sm btn-outline-info">
                                <i class="fas fa-external-link-alt"></i> Buka Penuh
                            </a>
                        @endif
                    </div>
                </div>
                <div class="card-body p-0 text-center bg-light">
                    @if ($suratMasuk->lampiran)
                        @php
                            $ekstensi = pathinfo($suratMasuk->lampiran, PATHINFO_EXTENSION);
                        @endphp

                        @if (in_array(strtolower($ekstensi), ['jpg', 'jpeg', 'png']))
                            <img src="{{ asset('storage/' . $suratMasuk->lampiran) }}" class="img-fluid"
                                alt="Lampiran Surat">
                        @elseif(strtolower($ekstensi) == 'pdf')
                            <iframe src="{{ asset('storage/' . $suratMasuk->lampiran) }}" width="100%" height="500px"
                                style="border: none;"></iframe>
                        @else
                            <div class="p-5">
                                <i class="fas fa-file-alt fa-4x text-muted mb-3"></i>
                                <h5>Format berkas tidak dapat dipratinjau.</h5>
                                <a href="{{ asset('storage/' . $suratMasuk->lampiran) }}" class="btn btn-primary mt-2"
                                    download>Unduh Berkas</a>
                            </div>
                        @endif
                    @else
                        <div class="p-5">
                            <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                            <h6 class="text-muted">Tidak ada berkas fisik yang dilampirkan pada surat ini.</h6>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: PANEL DISPOSISI --}}
        <div class="col-md-5">
            <div class="card card-warning shadow-sm">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-share-square mr-1"></i> Lembar Disposisi</h3>
                </div>
                <div class="card-body">

                    {{-- Menampilkan Disposisi yang sudah ada --}}
                    @if ($suratMasuk->disposisi)
                        <div class="callout callout-warning bg-light">
                            <h6 class="font-weight-bold text-warning border-bottom pb-2 mb-2">
                                <i class="fas fa-clipboard-check mr-1"></i> Instruksi Disposisi Terakhir:
                            </h6>
                            <p class="mb-0 text-justify" style="white-space: pre-wrap;">{{ $suratMasuk->disposisi }}</p>
                            <small class="text-muted d-block mt-3 text-right">
                                Terakhir diperbarui: {{ $suratMasuk->updated_at->diffForHumans() }}
                            </small>
                        </div>
                    @else
                        <div class="alert alert-light border-warning text-center">
                            <i class="fas fa-comment-dots fa-2x text-warning mb-2 d-block"></i>
                            Belum ada catatan disposisi / instruksi pada surat ini.
                        </div>
                    @endif

                    <hr>

                    {{-- Form Tambah/Update Disposisi --}}
                    <form action="{{ route('surat.masuk.disposisi', $suratMasuk->id) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>Beri Catatan / Instruksi Disposisi Baru</label>
                            <textarea name="disposisi" class="form-control @error('disposisi') is-invalid @enderror" rows="5"
                                placeholder="Contoh: Harap dihadiri oleh Waka 1, siapkan materi presentasi..." required>{{ old('disposisi', $suratMasuk->disposisi) }}</textarea>
                            @error('disposisi')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="text-muted mt-2 d-block">Menyimpan disposisi otomatis akan mengubah status surat
                                menjadi <strong>"Diproses"</strong>.</small>
                        </div>

                        <div class="text-right">
                            <button type="submit" class="btn btn-warning font-weight-bold">
                                <i class="fas fa-paper-plane mr-1"></i> Simpan Disposisi
                            </button>
                        </div>
                    </form>

                </div>
            </div>

            {{-- Panel Arsipkan (Ubah Status ke Selesai) --}}
            @if ($suratMasuk->status != 'selesai')
                <div class="card shadow-sm border-top-success mt-3">
                    <div class="card-body">
                        <h6 class="font-weight-bold text-success"><i class="fas fa-archive"></i> Selesaikan Surat</h6>
                        <p class="text-muted small">Jika surat ini sudah selesai ditindaklanjuti atau hanya sekadar
                            pemberitahuan, Anda dapat menutup/mengarsipkan surat ini.</p>

                        <form action="{{ route('surat.masuk.update', $suratMasuk->id) }}" method="POST"
                            onsubmit="return confirm('Arsipkan surat ini menjadi Selesai?');">
                            @csrf
                            @method('PUT')
                            {{-- Kirim data lama agar tidak error validasi, hanya ubah status --}}
                            <input type="hidden" name="nomor_surat" value="{{ $suratMasuk->nomor_surat }}">
                            <input type="hidden" name="pengirim" value="{{ $suratMasuk->pengirim }}">
                            <input type="hidden" name="perihal" value="{{ $suratMasuk->perihal }}">
                            <input type="hidden" name="tanggal_surat"
                                value="{{ \Carbon\Carbon::parse($suratMasuk->tanggal_surat)->format('Y-m-d') }}">
                            <input type="hidden" name="tanggal_diterima"
                                value="{{ \Carbon\Carbon::parse($suratMasuk->tanggal_diterima)->format('Y-m-d') }}">
                            <input type="hidden" name="isi_surat" value="{{ $suratMasuk->isi_surat }}">
                            <input type="hidden" name="status" value="selesai">

                            <button type="submit" class="btn btn-outline-success btn-block">
                                <i class="fas fa-check-double"></i> Tandai Selesai & Arsipkan
                            </button>
                        </form>
                    </div>
                </div>
            @endif

        </div>
    </div>
@endsection
