<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - SIM PAC</title>

    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <style>
        body {
            background-color: #f4f6f9;
            /* Warna abu-abu muda khas halaman login */
            font-family: 'Source Sans Pro', sans-serif;
        }

        .public-navbar {
            background-color: #ffffff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, .04);
        }
    </style>
</head>

<body>

    {{-- Navbar Simpel untuk Publik --}}
    <nav class="navbar navbar-expand-md navbar-light public-navbar mb-4">
        <div class="container">
            <a class="navbar-brand font-weight-bold text-success" href="{{ url('/') }}">
                <i class="fas fa-leaf"></i> SIM PAC IPNU IPPNU
            </a>

            <div class="ml-auto">
                <a href="{{ route('login') }}" class="btn btn-outline-success btn-sm font-weight-bold">
                    <i class="fas fa-sign-in-alt"></i> Login Pengurus
                </a>
            </div>
        </div>
    </nav>

    {{-- Area Konten Utama --}}
    <div class="content">
        @yield('content')
    </div>

    {{-- Footer --}}
    <footer class="text-center mt-5 mb-3 text-muted small">
        <strong>Copyright &copy; {{ date('Y') }} SIM PAC.</strong> Hak cipta dilindungi.
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Tempat untuk script tambahan dari halaman --}}
    @stack('scripts')
</body>

</html>
