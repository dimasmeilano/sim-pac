<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installer SIM PAC IPNU-IPPNU</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center">
                        <h4>SIM PAC IPNU-IPPNU</h4>
                        <small>Sistem Informasi Manajemen</small>
                    </div>
                    <div class="card-body text-center">
                        <img src="{{ asset('images/logo-ipnu-ippnu.png') }}" alt="Logo" style="max-width: 150px;"
                            class="mb-3">
                        <h5>Selamat Datang!</h5>
                        <p>Installer akan membantu Anda mengkonfigurasi sistem SIM PAC IPNU-IPPNU.</p>
                        <p>Pastikan Anda sudah menyiapkan:</p>
                        <ul class="text-start">
                            <li>Database PostgreSQL</li>
                            <li>Informasi koneksi database (host, port, nama DB, user, password)</li>
                            <li>Data admin utama (nama, email, password, NIK, no HP)</li>
                        </ul>
                        <a href="{{ url('/install/requirements') }}" class="btn btn-primary mt-3">Mulai Install →</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
