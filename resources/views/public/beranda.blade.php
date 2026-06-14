@extends('layouts.public')

@section('title', 'Beranda Utama')

@section('content')

    {{-- PECAH DATA BERITA DI BLADE --}}
    @php
        $artikelUtama = $artikels->first();
        $beritaSamping = $artikels->skip(1)->take(3);
        $beritaGrid = $artikels->skip(4);
    @endphp

    <!-- ==========================================
                                     1. SLIDER / CAROUSEL BANNER UTAMA
                                     ========================================== -->
    @if (isset($sliders) && $sliders->count() > 0)
        <div class="container mt-4">
            <div id="heroSlider" class="carousel slide shadow-sm" data-ride="carousel"
                style="border-radius: 12px; overflow: hidden;">
                <ol class="carousel-indicators">
                    @foreach ($sliders as $key => $slider)
                        <li data-target="#heroSlider" data-slide-to="{{ $key }}"
                            class="{{ $key == 0 ? 'active' : '' }}"></li>
                    @endforeach
                </ol>
                <div class="carousel-inner">
                    @foreach ($sliders as $key => $slider)
                        <div class="carousel-item {{ $key == 0 ? 'active' : '' }}" style="height: 50vh; min-height: 350px;">
                            {{-- Gambar Banner --}}
                            <img src="{{ asset('storage/' . $slider->gambar) }}" class="d-block w-100"
                                style="object-fit: cover; height: 100%; filter: brightness(60%);" alt="Banner">

                            {{-- Teks di atas Banner --}}
                            @if ($slider->judul || $slider->deskripsi_singkat)
                                <div class="carousel-caption d-none d-md-block" style="bottom: 20%;">
                                    @if ($slider->judul)
                                        {{-- Menggunakan font-serif (Merriweather) agar senada dengan tema --}}
                                        <h2 class="font-serif text-white text-uppercase"
                                            style="text-shadow: 2px 2px 5px rgba(0,0,0,0.8);">{{ $slider->judul }}</h2>
                                    @endif
                                    @if ($slider->deskripsi_singkat)
                                        <p class="lead text-light" style="text-shadow: 1px 1px 4px rgba(0,0,0,0.8);">
                                            {{ $slider->deskripsi_singkat }}</p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
                <a class="carousel-control-prev" href="#heroSlider" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Sebelumnya</span>
                </a>
                <a class="carousel-control-next" href="#heroSlider" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Selanjutnya</span>
                </a>
            </div>
        </div>
    @endif

    <!-- ==========================================
                                     2. HERO SECTION: BERITA UTAMA & SOROTAN
                                     ========================================== -->
    <div class="container mt-4 mt-md-5 mb-5">
        @if ($artikelUtama)
            <div class="row">
                <!-- Berita Utama (Kiri) -->
                <div class="col-lg-8 mb-4 mb-lg-0">
                    <div class="card-news position-relative">
                        <a href="{{ route('artikel.baca', $artikelUtama->slug) }}">
                            @if ($artikelUtama->gambar_cover)
                                <img src="{{ asset('storage/' . $artikelUtama->gambar_cover) }}" class="hero-img"
                                    alt="{{ $artikelUtama->judul }}">
                            @else
                                <div
                                    class="hero-img bg-secondary d-flex align-items-center justify-content-center text-white">
                                    <i class="fas fa-image fa-4x opacity-50"></i>
                                </div>
                            @endif
                        </a>
                        <div class="p-4 bg-white">
                            <span
                                class="badge bg-nu px-3 py-2 mb-2">{{ $artikelUtama->kategori->nama_kategori ?? 'Umum' }}</span>
                            <h2 class="font-serif mb-2" style="line-height: 1.4;">
                                <a href="{{ route('artikel.baca', $artikelUtama->slug) }}"
                                    class="news-title">{{ $artikelUtama->judul }}</a>
                            </h2>
                            <p class="text-muted mb-3">{{ Str::limit(strip_tags($artikelUtama->isi_artikel), 120, '...') }}
                            </p>
                            <small class="text-muted font-weight-bold">
                                <i class="fas fa-user-edit text-nu mr-1"></i> {{ $artikelUtama->user->name ?? 'Redaksi' }}
                                <span class="mx-2">•</span>
                                <i class="far fa-clock mr-1"></i>
                                {{ $artikelUtama->created_at->translatedFormat('d M Y') }}
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Berita Sorotan (Kanan) -->
                <div class="col-lg-4">
                    <h4 class="font-serif border-bottom pb-2 mb-4 border-success"><span
                            class="border-bottom border-success border-3 pb-2">Kabar Terkini</span></h4>

                    @forelse($beritaSamping as $berita)
                        <div class="media mb-4 align-items-center">
                            @if ($berita->gambar_cover)
                                <a href="{{ route('artikel.baca', $berita->slug) }}">
                                    <img src="{{ asset('storage/' . $berita->gambar_cover) }}"
                                        class="side-news-img shadow-sm mr-3" alt="{{ $berita->judul }}">
                                </a>
                            @endif
                            <div class="media-body">
                                <span class="text-nu font-weight-bold text-uppercase d-block mb-1"
                                    style="font-size: 11px;">{{ $berita->kategori->nama_kategori ?? 'Umum' }}</span>
                                <h6 class="font-serif mb-1" style="line-height: 1.4;">
                                    <a href="{{ route('artikel.baca', $berita->slug) }}"
                                        class="news-title">{{ $berita->judul }}</a>
                                </h6>
                                <small class="text-muted"><i class="far fa-clock mr-1"></i>
                                    {{ $berita->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted">Belum ada berita tambahan.</p>
                    @endforelse
                </div>
            </div>
        @endif
    </div>

    <!-- KONTEN BAWAH (GRID BERITA LAMA & WIDGET) -->
    <div class="container my-5">
        <div class="row">

            <!-- Grid Berita Selanjutnya -->
            <div class="col-lg-8">
                <h4 class="font-serif border-bottom pb-2 mb-4"><span class="border-bottom border-dark border-3 pb-2">Lebih
                        Banyak Berita</span></h4>
                <div class="row">
                    @forelse($beritaGrid as $artikel)
                        <div class="col-md-6 mb-4">
                            <div class="card-news h-100 d-flex flex-column">
                                @if ($artikel->gambar_cover)
                                    <a href="{{ route('artikel.baca', $artikel->slug) }}">
                                        <img src="{{ asset('storage/' . $artikel->gambar_cover) }}" class="grid-news-img"
                                            alt="{{ $artikel->judul }}">
                                    </a>
                                @endif
                                <div class="p-3 d-flex flex-column flex-grow-1">
                                    <span class="text-nu font-weight-bold text-uppercase mb-2"
                                        style="font-size: 11px;">{{ $artikel->kategori->nama_kategori ?? 'Umum' }}</span>
                                    <h5 class="font-serif mb-2">
                                        <a href="{{ route('artikel.baca', $artikel->slug) }}"
                                            class="news-title">{{ $artikel->judul }}</a>
                                    </h5>
                                    <p class="text-muted small flex-grow-1">
                                        {{ Str::limit(strip_tags($artikel->isi_artikel), 80, '...') }}</p>
                                    <small class="text-muted border-top pt-2 mt-2">
                                        <i class="far fa-clock mr-1"></i>
                                        {{ $artikel->created_at->translatedFormat('d F Y') }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    @empty
                        @if (!$artikelUtama)
                            <div class="col-12 text-center py-5 text-muted">
                                <i class="fas fa-newspaper fa-4x mb-3 opacity-50"></i>
                                <p>Belum ada berita yang diterbitkan.</p>
                            </div>
                        @endif
                    @endforelse
                </div>
            </div>

            <!-- Sidebar Widget Dinamis -->
            <div class="col-lg-4 mt-5 mt-lg-0">
                <div class="card-news mb-4 overflow-hidden border-0 shadow-sm" style="border-radius: 10px;">
                    <div
                        class="bg-success text-white font-weight-bold p-3 font-serif d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-calendar-alt mr-2 text-warning"></i> Agenda Kegiatan</span>
                    </div>
                    <div class="list-group list-group-flush">
                        @forelse($agendas as $agenda)
                            <div class="list-group-item list-group-item-action border-bottom-0 border-top">
                                <h6 class="font-weight-bold text-dark mb-1" style="line-height: 1.4;">{{ $agenda->nama }}
                                </h6>

                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <small class="text-muted font-weight-bold">
                                        <i class="far fa-clock text-success mr-1"></i>
                                        {{ \Carbon\Carbon::parse($agenda->tgl_mulai)->translatedFormat('d M Y') }}
                                        @if ($agenda->tgl_mulai != $agenda->tgl_selesai && $agenda->tgl_selesai != null)
                                            - {{ \Carbon\Carbon::parse($agenda->tgl_selesai)->translatedFormat('d M') }}
                                        @endif
                                    </small>

                                    {{-- Kondisi warna badge berdasarkan status (Bisa disesuaikan dengan isi string status di database Anda) --}}
                                    @php
                                        $badgeColor = 'secondary';
                                        if (strtolower($agenda->status) == 'terlaksana') {
                                            $badgeColor = 'success';
                                        } elseif (strtolower($agenda->status) == 'berjalan') {
                                            $badgeColor = 'primary';
                                        } elseif (strtolower($agenda->status) == 'terencana') {
                                            $badgeColor = 'warning text-dark';
                                        }
                                    @endphp
                                    <span
                                        class="badge badge-{{ $badgeColor }} px-2 py-1">{{ $agenda->status ?? 'Terencana' }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="list-group-item text-center text-muted py-4">
                                <i class="far fa-calendar-times fa-2x mb-2 opacity-50"></i>
                                <p class="mb-0 small">Belum ada agenda terdekat.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                @foreach ($widgets as $widget)
                    <div class="card-news mb-4">
                        <div class="bg-dark text-white font-weight-bold p-3 font-serif">
                            <i class="fas fa-th-large mr-2 text-warning"></i> {{ $widget->nama_widget }}
                        </div>
                        <div class="p-3 bg-white">
                            <div class="widget-content w-100" style="overflow: hidden;">
                                {!! $widget->isi_html !!}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>

    <div class="container my-5">
        <h4 class="font-serif border-bottom pb-2 mb-4 border-success">
            <span class="border-bottom border-success border-3 pb-2">Dokumentasi Kegiatan</span>
        </h4>

        <div class="row">
            @forelse($kegiatan_galeri as $kegiatan)
                <div class="col-lg-3 col-md-4 col-6 mb-4">
                    <div class="card shadow-sm border-0 h-100"
                        style="border-radius: 12px; overflow: hidden; cursor: pointer; transition: transform 0.3s ease;"
                        onmouseover="this.style.transform='translateY(-5px)'"
                        onmouseout="this.style.transform='translateY(0)'" data-toggle="modal"
                        data-target="#galeriModal{{ $kegiatan->id }}">

                        <div style="height: 160px; overflow: hidden; background-color: #eaeded;">
                            <img src="{{ asset('storage/' . $kegiatan->fotoPublik->first()->file_path) }}"
                                class="w-100 h-100" style="object-fit: cover;" alt="Cover {{ $kegiatan->nama }}">
                        </div>

                        <div class="p-3 bg-white text-center">
                            <h6 class="font-weight-bold text-dark mb-1 text-truncate" title="{{ $kegiatan->nama }}">
                                {{ $kegiatan->nama }}
                            </h6>
                            <span class="badge badge-success px-2 py-1" style="font-size: 11px; border-radius: 20px;">
                                <i class="fas fa-images mr-1"></i> {{ $kegiatan->fotoPublik->count() }} Foto
                            </span>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="galeriModal{{ $kegiatan->id }}" tabindex="-1" role="dialog"
                    aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                        <div class="modal-content" style="border-radius: 16px; border: none; overflow: hidden;">

                            <div class="modal-header bg-light border-0 py-3">
                                <h5 class="modal-title font-weight-bold text-dark">
                                    <i class="fas fa-folder-open text-warning mr-2"></i>{{ $kegiatan->nama }}
                                </h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <div class="modal-body p-4" style="background-color: #f8f9fa;">
                                <div class="row">
                                    @foreach ($kegiatan->fotoPublik->take(4) as $foto)
                                        <div class="col-md-6 col-12 mb-4">
                                            <div class="card border-0 shadow-sm rounded-lg overflow-hidden h-100 bg-white">
                                                <div style="height: 200px; background-color: #f1f2f6;">
                                                    <img src="{{ asset('storage/' . $foto->file_path) }}"
                                                        class="w-100 h-100" style="object-fit: cover;" alt="Dokumentasi">
                                                </div>
                                                @if ($foto->keterangan)
                                                    <div class="p-2 bg-white text-center border-top">
                                                        <small
                                                            class="text-muted d-block text-truncate">{{ $foto->keterangan }}</small>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            @empty
                <div class="col-12 text-center text-muted py-5">
                    <i class="far fa-folder-open fa-4x mb-3 opacity-30 text-success"></i>
                    <p class="mb-0">Belum ada album dokumentasi kegiatan yang dipublikasikan.</p>
                </div>
            @endforelse
        </div>
    </div>
    <!-- ==========================================
                         3. STATISTIK ORGANISASI (COMPANY PROFILE)
                         ========================================== -->
    <div class="py-5 mt-5 shadow-sm position-relative bg-white border-top border-success"
        style="border-top-width: 4px !important; background-image: url('https://www.transparenttextures.com/patterns/cubes.png');">
        <div class="container py-4">

            <div class="text-center mb-5">
                <span class="badge badge-success px-3 py-2 font-weight-bold mb-2">PROFIL KEKUATAN</span>
                <h2 class="font-serif font-weight-bold text-dark">Jaringan & Keanggotaan</h2>
                <p class="text-muted">Tumbuh dan bergerak bersama mengabdi untuk agama, bangsa, dan negara.</p>
            </div>

            <!-- Baris Grid: Dibagi menjadi 3 kolom per baris (col-md-4) -->
            <div class="row text-center">

                <!-- Item 1: Ranting IPNU -->
                <div class="col-6 col-md-4 mb-5">
                    <div class="p-3">
                        <i class="fas fa-network-wired fa-3x mb-3 text-warning"></i>
                        <h2 class="font-weight-bold display-4 mb-0 counter text-dark"
                            data-target="{{ $statistik_org['ranting_ipnu'] ?? 0 }}">0</h2>
                        <p class="lead mb-0 font-weight-bold text-dark" style="font-size: 16px;">PR / PK IPNU</p>
                        <small class="text-muted">Pimpinan Ranting & Komisariat</small>
                    </div>
                </div>

                <!-- Item 2: Ranting IPPNU -->
                <div class="col-6 col-md-4 mb-5">
                    <div class="p-3">
                        <i class="fas fa-sitemap fa-3x mb-3 text-warning"></i>
                        <h2 class="font-weight-bold display-4 mb-0 counter text-dark"
                            data-target="{{ $statistik_org['ranting_ippnu'] ?? 0 }}">0</h2>
                        <p class="lead mb-0 font-weight-bold text-dark" style="font-size: 16px;">PR / PK IPPNU</p>
                        <small class="text-muted">Pimpinan Ranting & Komisariat</small>
                    </div>
                </div>

                <!-- Item 3: Anggota IPNU -->
                <div class="col-6 col-md-4 mb-5">
                    <div class="p-3">
                        <i class="fas fa-user-graduate fa-3x mb-3 text-warning"></i>
                        <h2 class="font-weight-bold display-4 mb-0 counter text-dark"
                            data-target="{{ $statistik_org['anggota_ipnu'] ?? 0 }}">0</h2>
                        <p class="lead mb-0 font-weight-bold text-dark" style="font-size: 16px;">Pelajar Putra</p>
                        <small class="text-muted">Kader Anggota IPNU</small>
                    </div>
                </div>

                <!-- Item 4: Anggota IPPNU -->
                <div class="col-6 col-md-4 mb-4 mb-md-0">
                    <div class="p-3">
                        <i class="fas fa-female fa-3x mb-3 text-warning"
                            style="padding-left: 10px; padding-right: 10px;"></i>
                        <h2 class="font-weight-bold display-4 mb-0 counter text-dark"
                            data-target="{{ $statistik_org['anggota_ippnu'] ?? 0 }}">0</h2>
                        <p class="lead mb-0 font-weight-bold text-dark" style="font-size: 16px;">Pelajar Putri</p>
                        <small class="text-muted">Kader Anggota IPPNU</small>
                    </div>
                </div>

                <!-- Item 5: Kegiatan -->
                <div class="col-6 col-md-4 mb-4 mb-md-0">
                    <div class="p-3">
                        <i class="fas fa-calendar-check fa-3x mb-3 text-warning"></i>
                        <h2 class="font-weight-bold display-4 mb-0 counter text-dark"
                            data-target="{{ $statistik_org['total_kegiatan'] ?? 0 }}">0</h2>
                        <p class="lead mb-0 font-weight-bold text-dark" style="font-size: 16px;">Kegiatan Terlaksana</p>
                        <small class="text-muted">Total Program Kerja</small>
                    </div>
                </div>

                <!-- Item 6: Arsip Surat -->
                <div class="col-6 col-md-4 mb-4 mb-md-0">
                    <div class="p-3">
                        <i class="fas fa-envelope-open-text fa-3x mb-3 text-warning"></i>
                        <h2 class="font-weight-bold display-4 mb-0 counter text-dark"
                            data-target="{{ $statistik_org['total_surat'] ?? 0 }}">0</h2>
                        <p class="lead mb-0 font-weight-bold text-dark" style="font-size: 16px;">Arsip Surat</p>
                        <small class="text-muted">Dokumen Administrasi Keluar</small>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
