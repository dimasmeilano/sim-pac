<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalasi Selesai - SIM PAC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-success text-white text-center">
                        <h5>✓ Instalasi Berhasil!</h5>
                    </div>
                    <div class="card-body text-center">
                        <img src="{{ asset('images/checkmark.gif') }}" alt="Success" style="width: 100px;"
                            class="mb-3">
                        <p>SIM PAC IPNU-IPPNU telah berhasil diinstall.</p>
                        <p>Anda sekarang bisa login menggunakan akun Super Admin yang sudah dibuat.</p>
                        <a href="{{ url('/login') }}" class="btn btn-primary mt-3">Login ke Sistem →</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
