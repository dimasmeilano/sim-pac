<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIM PAC</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #f4f7f6;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .bg-nu {
            background-color: #00723b;
        }

        .text-nu {
            color: #00723b;
        }

        .btn-nu {
            background-color: #00723b;
            color: white;
            transition: 0.3s;
        }

        .btn-nu:hover {
            background-color: #005a2e;
            color: white;
            transform: scale(1.02);
        }

        .card {
            border-radius: 15px;
            overflow: hidden;
            border: none;
        }

        .login-side {
            background: linear-gradient(135deg, #00723b 0%, #004d26 100%);
            color: white;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="card shadow-lg my-5">
                    <div class="card-body p-0">
                        <div class="row">
                            <!-- Sisi Kiri (Gambar / Branding) -->
                            <div
                                class="col-lg-6 d-none d-lg-flex login-side align-items-center justify-content-center p-5">
                                <div class="text-center">
                                    <i class="fas fa-users fa-5x mb-4 text-warning"></i>
                                    <h2 class="font-weight-bold">SIM PAC</h2>
                                    <p class="lead">Sistem Informasi Manajemen<br>Pimpinan Anak Cabang</p>
                                </div>
                            </div>

                            <!-- Sisi Kanan (Form Login) -->
                            <div class="col-lg-6 p-5 bg-white">
                                <div class="text-center">
                                    <h4 class="text-gray-900 mb-4 font-weight-bold text-nu">Selamat Datang!</h4>
                                </div>

                                <form method="POST" action="{{ route('login') }}" class="user">
                                    @csrf

                                    <div class="form-group mb-4">
                                        <label class="font-weight-bold text-muted small">Email Address</label>
                                        <input type="email" name="email"
                                            class="form-control form-control-lg bg-light border-0 @error('email') is-invalid @enderror"
                                            value="{{ old('email') }}" required autofocus
                                            placeholder="Masukkan email...">
                                        @error('email')
                                            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-4">
                                        <label class="font-weight-bold text-muted small">Password</label>
                                        <input type="password" name="password"
                                            class="form-control form-control-lg bg-light border-0 @error('password') is-invalid @enderror"
                                            required placeholder="Masukkan password...">
                                        @error('password')
                                            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-4">
                                        <div class="custom-control custom-checkbox small">
                                            <input type="checkbox" class="custom-control-input" name="remember"
                                                id="remember" {{ old('remember') ? 'checked' : '' }}>
                                            <label class="custom-control-label font-weight-bold text-muted"
                                                for="remember">Ingat Saya</label>
                                        </div>
                                    </div>

                                    <button type="submit"
                                        class="btn btn-nu btn-lg btn-block font-weight-bold rounded-pill shadow-sm">
                                        Login Sistem <i class="fas fa-sign-in-alt ml-1"></i>
                                    </button>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
