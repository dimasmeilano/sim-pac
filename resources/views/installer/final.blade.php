<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalasi Selesai - SIM PAC IPNU-IPPNU</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-success text-white text-center py-3">
                        <h5 class="mb-0">✓ Instalasi Berhasil!</h5>
                    </div>
                    <div class="card-body p-4 text-center">
                        <img src="{{ asset('images/checkmark.gif') }}" alt="Success" style="width: 80px;"
                            class="mb-3">
                        <p>SIM PAC IPNU-IPPNU telah berhasil diinstall.</p>
                        <a href="{{ url('/login') }}" class="btn btn-primary btn-lg mt-3">Login ke Sistem →</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
