<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installer SIM PAC IPNU-IPPNU</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-primary text-white text-center py-3">
                        <h4 class="mb-0">SIM PAC IPNU-IPPNU</h4>
                        <small>Enterprise Installer</small>
                    </div>
                    <div class="card-body p-4 text-center">
                        <img src="{{ asset('images/logo-ipnu-ippnu.png') }}" alt="Logo" style="max-width: 120px;"
                            class="mb-3">
                        <h5>Selamat Datang!</h5>
                        <p class="text-muted">Installer ini akan membantu Anda mengkonfigurasi sistem.</p>
                        <ul class="text-start d-inline-block">
                            <li>Database PostgreSQL</li>
                            <li>Informasi koneksi database</li>
                            <li>Data akun Super Admin</li>
                        </ul>
                        <div class="d-grid mt-3">
                            <a href="{{ url('/install/requirements') }}" class="btn btn-primary btn-lg">Mulai Install
                                →</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
