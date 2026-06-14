<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | E-Office & Portal PAC IPNU IPPNU</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Merriweather:wght@400;700;900&display=swap"
        rel="stylesheet">

    <!-- Bootstrap 4.6 & FontAwesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- Custom CSS Kita -->
    <link rel="stylesheet" href="{{ asset('themes/css/custom.css') }}">
</head>

<body>

    {{-- TARIK DATA IDENTITAS & MEDSOS DARI DATABASE --}}
    @php
        $identitas = \App\Models\IdentitasWeb::first();
        $medsos = \App\Models\MediaSosial::all();
    @endphp
    <!-- TOP BAR (Teks Berjalan) -->
    @if (isset($pengumumans) && $pengumumans->count() > 0)
        <div class="bg-nu py-2 shadow-sm" style="font-size: 14px;">
            <div class="container d-flex align-items-center">
                <span class="badge badge-warning text-dark mr-3 px-2 py-1 font-weight-bold">INFO TEEBARU</span>
                <marquee behavior="scroll" direction="left" class="mb-0 text-white font-weight-bold">
                    @foreach ($pengumumans as $item)
                        <i class="fas fa-circle mx-2 text-warning" style="font-size: 8px; vertical-align: middle;"></i>
                        {{ $item->isi_teks }}
                    @endforeach
                </marquee>
            </div>
        </div>
    @endif

    <!-- NAVBAR UTAMA -->
    <div class="bg-success text-white py-2 d-none d-lg-block">
        <div class="container">
            <div class="row">
                <div class="col-12 text-right">
                    <small class="font-weight-bold" style="letter-spacing: 0.5px;">
                        <i class="far fa-calendar-alt mr-1 text-warning"></i>
                        {{ $penanggalan_nu }}
                    </small>
                </div>
            </div>
        </div>
    </div>

    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand font-weight-bold text-success" href="/">
                <i class="fas fa-globe-asia mr-1"></i> SIM PAC IPNU IPPNU
            </a>

            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link text-dark font-weight-bold" href="/">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark font-weight-bold" href="/profil-organisasi">Profil Organisasi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark font-weight-bold" href="/lacak-surat">Lacak Surat</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark font-weight-bold" href="/pengesahan">Pengesahan</a>
                    </li>
                </ul>

                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a href="/login" class="btn btn-success text-white font-weight-bold"
                            style="border-radius: 20px; padding: 5px 20px;">
                            <i class="fas fa-sign-in-alt mr-1"></i> Login Pengurus
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- AREA KONTEN -->
    <main>
        @yield('content')
    </main>

    <!-- FOOTER ELEGAN -->
    <footer class="bg-dark text-white pt-5 pb-4 mt-5 border-top"
        style="border-top-color: var(--nu-green) !important; border-top-width: 4px !important;">
        <div class="container">
            <div class="row">

                <!-- Kolom 1: Profil & Deskripsi -->
                <div class="col-md-5 mb-4">
                    <h4 class="font-serif mb-3 text-success">{{ $identitas->nama_web ?? 'SIM PAC IPNU IPPNU' }}</h4>
                    <p class="text-muted pr-md-4">
                        {{ $identitas->deskripsi_web ?? 'Pusat Informasi, Layanan Administrasi, dan Berita Kegiatan Ikatan Pelajar Nahdlatul Ulama dan Ikatan Pelajar Putri Nahdlatul Ulama.' }}
                    </p>
                </div>

                <!-- Kolom 2: Tautan Cepat -->
                <div class="col-md-3 mb-4">
                    <h5 class="font-serif mb-3">Tautan Cepat</h5>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('beranda') }}"
                                class="text-muted text-decoration-none d-block mb-2">Beranda</a></li>
                        <li><a href="{{ route('lacak.surat') }}"
                                class="text-muted text-decoration-none d-block mb-2">Lacak Surat</a></li>
                        <li><a href="{{ route('pengajuan.create') }}"
                                class="text-muted text-decoration-none d-block mb-2">Pengajuan Pengesahan</a></li>
                    </ul>
                </div>

                <!-- Kolom 3: Kontak & Medsos Dinamis -->
                <div class="col-md-4 mb-4">
                    <h5 class="font-serif mb-3">Hubungi Kami</h5>
                    <p class="text-muted mb-1"><i class="fas fa-map-marker-alt mr-2 text-success"></i>
                        {{ $identitas->alamat ?? 'Kantor MWCNU Setempat' }}</p>
                    <p class="text-muted mb-1"><i class="fas fa-envelope mr-2 text-success"></i>
                        {{ $identitas->email ?? 'admin@domain.com' }}</p>
                    <p class="text-muted mb-2"><i class="fas fa-phone-alt mr-2 text-success"></i>
                        {{ $identitas->nomor_telepon ?? '-' }}</p>

                    <div class="mt-3">
                        @forelse($medsos as $sosmed)
                            <a href="{{ $sosmed->url }}" target="_blank"
                                class="btn btn-sm btn-outline-light rounded-circle mr-1 mb-1"
                                title="{{ $sosmed->nama_platform }}">
                                {{-- Kita asumsikan field icon menyimpan class fontawesome seperti 'fab fa-instagram' --}}
                                <i class="{{ $sosmed->icon ?? 'fas fa-link' }}"></i>
                            </a>
                        @empty
                            <span class="text-muted small">Belum ada media sosial terkait.</span>
                        @endforelse
                    </div>
                </div>

            </div>
            <div class="text-center text-muted border-top border-secondary pt-3 mt-3" style="font-size: 14px;">
                &copy; {{ date('Y') }} {{ $identitas->nama_web ?? 'SIM PAC IPNU IPPNU' }}. Hak Cipta Dilindungi.
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Script Animasi Counter -->
    <script>
        $(document).ready(function() {
            $('.counter').each(function() {
                $(this).prop('Counter', 0).animate({
                    Counter: $(this).data('target')
                }, {
                    duration: 2000,
                    easing: 'swing',
                    step: function(now) {
                        $(this).text(Math.ceil(now));
                    }
                });
            });
        });
    </script>
</body>

</html>
