@extends('layouts.public')

@section('title', $artikel->judul)

@section('content')
    <div class="container mt-4 mb-5">
        <div class="row">

            {{-- BAGIAN KIRI: KONTEN UTAMA ARTIKEL --}}
            <div class="col-lg-8">
                <div class="card shadow-lg border-0 rounded-lg overflow-hidden mb-4">
                    {{-- Gambar Cover --}}
                    @if ($artikel->gambar_cover)
                        <img src="{{ asset('storage/' . $artikel->gambar_cover) }}" class="card-img-top w-100"
                            style="max-height: 400px; object-fit: cover;" alt="{{ $artikel->judul }}">
                    @endif

                    <div class="card-body p-4 p-md-5">
                        {{-- Kategori & Judul --}}
                        <span class="badge badge-success mb-3 px-3 py-2 shadow-sm"><i class="fas fa-tag mr-1"></i>
                            {{ $artikel->kategori->nama_kategori ?? 'Umum' }}</span>
                        <h1 class="font-weight-bold text-dark mb-3" style="line-height: 1.4;">{{ $artikel->judul }}</h1>

                        {{-- Meta Info (Penulis, Waktu, Tayangan) --}}
                        <div class="d-flex flex-wrap align-items-center text-muted small mb-4 pb-3 border-bottom">
                            <span class="mr-3 mb-2"><i class="fas fa-user-edit text-primary mr-1"></i>
                                {{ $artikel->user->name ?? 'Redaksi' }}
                                @if ($artikel->organization)
                                    ({{ $artikel->organization->name }})
                                @endif
                            </span>
                            <span class="mr-3 mb-2"><i class="fas fa-calendar-alt text-danger mr-1"></i>
                                {{ $artikel->published_at ? \Carbon\Carbon::parse($artikel->published_at)->translatedFormat('l, d F Y - H:i') : $artikel->created_at->translatedFormat('l, d F Y - H:i') }}
                                WIB</span>
                            <span class="mb-2"><i class="fas fa-eye text-success mr-1"></i> {{ $artikel->dilihat }} kali
                                dibaca</span>
                        </div>

                        {{-- Isi Artikel --}}
                        <div class="isi-artikel text-justify text-dark" style="font-size: 16px; line-height: 1.8;">
                            {!! $artikel->isi_artikel !!}
                        </div>
                    </div>
                </div>

                {{-- KOLOM KOMENTAR --}}
                <div class="card shadow-lg border-0 rounded-lg mt-5">
                    <div class="card-header bg-white font-weight-bold pt-4 pb-0 border-bottom-0 h4">
                        <i class="fas fa-comments text-success mr-2"></i> Komentar ({{ $komentars->count() }})
                    </div>
                    <div class="card-body p-4 p-md-5">

                        {{-- Form Tambah Komentar --}}
                        <div class="bg-light p-4 rounded-lg mb-5 border-left border-success"
                            style="border-left-width: 5px !important;">
                            <h5 class="font-weight-bold mb-3">Tinggalkan Balasan</h5>
                            @if (session('success_komentar'))
                                <div class="alert alert-success"><i class="fas fa-check-circle mr-1"></i>
                                    {{ session('success_komentar') }}</div>
                            @endif
                            <form action="{{ route('artikel.komentar', $artikel->slug) }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label class="text-muted font-weight-bold">Nama Lengkap <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="nama_pengunjung" class="form-control" required
                                            placeholder="Nama Anda...">
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label class="text-muted font-weight-bold">Email <span
                                                class="text-secondary">(Opsional)</span></label>
                                        <input type="email" name="email" class="form-control"
                                            placeholder="Email tidak akan dipublikasikan">
                                    </div>
                                </div>
                                <div class="form-group mt-2">
                                    <label class="text-muted font-weight-bold">Tulis Komentar <span
                                            class="text-danger">*</span></label>
                                    <textarea name="isi_komentar" class="form-control" rows="4" required
                                        placeholder="Tulis tanggapan Anda di sini..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-success font-weight-bold shadow-sm px-4"><i
                                        class="fas fa-paper-plane mr-1"></i> Kirim Komentar</button>
                            </form>
                        </div>

                        {{-- Daftar Komentar yang sudah masuk --}}
                        <div class="komentar-list">
                            @forelse($komentars as $komentar)
                                <div class="media mb-4 pb-4 border-bottom">
                                    <div class="mr-3 bg-success rounded-circle d-flex align-items-center justify-content-center text-white shadow-sm"
                                        style="width: 50px; height: 50px; font-size: 20px;">
                                        {{ strtoupper(substr($komentar->nama_pengunjung, 0, 1)) }}
                                    </div>
                                    <div class="media-body">
                                        <h6 class="mt-0 font-weight-bold mb-1">{{ $komentar->nama_pengunjung }}</h6>
                                        <small class="text-muted d-block mb-2"><i class="far fa-clock mr-1"></i>
                                            {{ $komentar->created_at->diffForHumans() }}</small>
                                        <p class="mb-0 text-dark">{{ $komentar->isi_komentar }}</p>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-muted py-4">
                                    <i class="far fa-comment-dots fa-3x mb-3 opacity-50"></i>
                                    <p>Belum ada komentar. Jadilah yang pertama memberikan tanggapan!</p>
                                </div>
                            @endforelse
                        </div>

                    </div>
                </div>
            </div>

            {{-- BAGIAN KANAN: SIDEBAR (BERITA LAINNYA) --}}
            <div class="col-lg-4 mt-4 mt-lg-0">
                <div class="card shadow-sm border-0 rounded-lg">
                    <div class="card-header bg-success text-white font-weight-bold">
                        <i class="fas fa-fire mr-1"></i> Berita Terkini
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            @forelse($berita_lain as $berita)
                                <li class="list-group-item p-3">
                                    <div class="row align-items-center">
                                        @if ($berita->gambar_cover)
                                            <div class="col-4 pr-0">
                                                <img src="{{ asset('storage/' . $berita->gambar_cover) }}"
                                                    class="w-100 rounded shadow-sm"
                                                    style="height: 60px; object-fit: cover;">
                                            </div>
                                        @endif
                                        <div class="{{ $berita->gambar_cover ? 'col-8' : 'col-12' }}">
                                            <h6 class="mb-1" style="font-size: 14px; line-height: 1.4;">
                                                <a href="{{ route('artikel.baca', $berita->slug) }}"
                                                    class="text-dark text-decoration-none font-weight-bold">{{ $berita->judul }}</a>
                                            </h6>
                                            <small class="text-muted"><i class="far fa-clock"></i>
                                                {{ $berita->created_at->translatedFormat('d M Y') }}</small>
                                        </div>
                                    </div>
                                </li>
                            @empty
                                <li class="list-group-item text-muted text-center py-4">Belum ada berita lain.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <style>
        /* Styling agar gambar di dalam isi artikel tidak melebar keluar batas (responsive) */
        .isi-artikel img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .isi-artikel iframe {
            max-width: 100%;
        }
    </style>
@endsection
