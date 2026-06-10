<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Anggota - SIM PAC</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body class="hold-transition login-page bg-light">
    <div class="login-box" style="width: 450px;">
        <div class="card card-outline card-success shadow-sm">
            <div class="card-header text-center pt-4">
                <h1 class="h3 font-weight-bold">Halo, {{ auth()->user()->name }}! 👋</h1>
            </div>
            <div class="card-body text-center pb-4">
                <i class="fas fa-mobile-alt text-success mb-3" style="font-size: 60px;"></i>
                <h5 class="font-weight-bold text-dark">Gunakan Aplikasi Mobile</h5>
                <p class="text-muted">
                    Website ini adalah ruang kerja digital (ERP) yang dikhususkan untuk **Pengurus Organisasi**.<br><br>
                    Sebagai Anggota, Anda akan mendapatkan pengalaman yang lebih baik (KTA Digital, Absensi QR, Jadwal
                    Kegiatan) melalui **Aplikasi Mobile** kami yang akan segera rilis.
                </p>

                <a href="#" class="btn btn-success btn-block mt-4 mb-2 disabled">
                    <i class="fab fa-google-play mr-2"></i> Download via PlayStore (Segera)
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-block">
                        <i class="fas fa-sign-out-alt mr-2"></i> Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
