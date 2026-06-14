<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Instruktur - {{ $materi->nama_materi }}</title>
    <!-- Kita pakai Bootstrap 4 agar otomatis responsif di HP -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #f4f7f6;
            display: flex;
            align-items: center;
            min-height: 100vh;
        }

        .bg-nu {
            background-color: #00723b;
            color: white;
        }

        .btn-nu {
            background-color: #00723b;
            color: white;
            border: none;
        }

        .btn-nu:hover {
            background-color: #005a2e;
            color: white;
        }

        /* Memperbesar inputan PIN agar mudah diketik di HP */
        .pin-input {
            font-size: 2.5rem;
            text-align: center;
            letter-spacing: 15px;
            border-radius: 10px;
            font-weight: bold;
            color: #00723b;
        }

        .pin-input::placeholder {
            color: #ccc;
            letter-spacing: 5px;
            font-weight: normal;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-lg border-0 rounded-lg">
                    <div class="card-body bg-nu text-center rounded-top py-4">
                        <i class="fas fa-lock fa-3x mb-3 text-warning"></i>
                        <h5 class="font-weight-bold mb-0">Portal Penilaian Instruktur</h5>
                    </div>
                    <div class="card-body p-4 bg-white rounded-bottom">
                        <div class="text-center mb-4">
                            <h5 class="font-weight-bold text-dark">{{ $materi->nama_materi }}</h5>
                            <p class="small text-muted mb-0">{{ $materi->event->tema }}</p>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger small text-center font-weight-bold rounded">
                                <i class="fas fa-exclamation-circle mr-1"></i> {{ $errors->first() }}
                            </div>
                        @endif

                        <form action="{{ route('instruktur.authenticate', $materi->token_rahasia) }}" method="POST">
                            @csrf
                            <div class="form-group text-center">
                                <label class="font-weight-bold text-muted small">MASUKKAN PIN (4 DIGIT)</label>
                                <!-- Input khusus angka (memunculkan numpad di HP) -->
                                <input type="text" inputmode="numeric" pattern="[0-9]{4}" maxlength="4"
                                    name="pin" class="form-control form-control-lg pin-input shadow-sm"
                                    placeholder="••••" required autofocus autocomplete="off">
                            </div>
                            <button type="submit"
                                class="btn btn-nu btn-block btn-lg font-weight-bold shadow mt-4 rounded-pill">Buka
                                Daftar Peserta <i class="fas fa-arrow-right ml-1"></i></button>
                        </form>
                    </div>
                </div>
                <div class="text-center mt-3 text-muted small">
                    &copy; {{ date('Y') }} SIM PAC IPNU-IPPNU
                </div>
            </div>
        </div>
    </div>
</body>

</html>
