<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SIM PAC IPNU-IPPNU')</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,400i,700" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- jQuery UI CSS (untuk drag & drop) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/themes/base/jquery-ui.min.css">

    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- CDN TinyMCE -->
    <script src="https://cdn.tiny.cloud/1/your-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    @stack('styles')
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        <!-- Navbar -->
        @include('layouts.navbar')

        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">@yield('page-title')</h1>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">×</button>
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">×</button>
                            {{ session('error') }}
                        </div>
                    @endif

                    @yield('content')
                </div>
            </section>
        </div>

        <!-- Footer -->
        @include('layouts.footer')
    </div>

    <!-- jQuery (harus pertama) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- jQuery UI (untuk drag & drop) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

    <!-- Bootstrap 4 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- AdminLTE JS -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>


    @stack('scripts')
    @auth
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // 1. Minta izin Notifikasi (jika belum)
                if ("Notification" in window && Notification.permission !== "granted") {
                    Notification.requestPermission();
                }

                // 2. Tunggu sampai Vite benar-benar selesai memuat Echo
                console.log("Antena Global: Menunggu mesin Echo menyala...");

                var waitForEcho = setInterval(function() {
                    if (typeof window.Echo !== 'undefined') {
                        clearInterval(waitForEcho); // Matikan timer penunggu
                        console.log("Antena Global: Echo SIAP! Mengudara di frekuensi global-notif 📡");

                        window.Echo.private('global-notif')
                            .listen('MessageSent', (e) => {
                                console.log("Antena Global menangkap sinyal masuk!", e);
                                var currentUserId = {{ auth()->id() ?? 'null' }};

                                // Jika pesan bukan dari diri sendiri
                                if (currentUserId && e.message.user_id !== currentUserId) {

                                    // Cek apakah user sedang buka halaman chat Progja tersebut
                                    var isCurrentlyOnChatPage = window.location.pathname.includes(
                                        '/progja/' + e.message.progja_id);

                                    // Jika sedang di halaman lain (Dashboard, Absensi, dll)
                                    if (!isCurrentlyOnChatPage) {
                                        console.log("Memunculkan Pop-up Notifikasi Lintas Halaman!");

                                        if (Notification.permission === "granted") {
                                            var notif = new Notification("Pesan Baru: " + e.message.user
                                                .name, {
                                                    body: e.message.message ? e.message.message :
                                                        '📂 Mengirim lampiran',
                                                });

                                            notif.onclick = function() {
                                                // Pindah ke ruang chat jika pop-up diklik
                                                window.location.href = '/progja/' + e.message.progja_id;
                                                this.close();
                                            };
                                        }
                                    } else {
                                        console.log(
                                            "Notifikasi global ditahan karena user sedang di dalam ruangan chat."
                                            );
                                    }
                                }
                            });
                    }
                }, 500); // Cek apakah Echo sudah siap setiap 0,5 detik
            });
        </script>
    @endauth
</body>

</html>
