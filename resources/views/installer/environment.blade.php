<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        Konfigurasi Installer - SIM PAC IPNU-IPPNU
    </title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</head>

<body class="bg-light">

    <div class="container py-5">

        <div class="row justify-content-center">

            <div class="col-lg-9">

                <div class="card border-0 shadow-lg">

                    {{-- Header --}}
                    <div class="card-header bg-primary text-white py-3">

                        <div class="d-flex justify-content-between align-items-center">

                            <div>
                                <h4 class="mb-1">
                                    SIM PAC IPNU-IPPNU
                                </h4>

                                <small>
                                    Enterprise Installer
                                </small>
                            </div>

                            <div class="text-end">
                                <small>Step 2 / 3</small>
                            </div>

                        </div>

                    </div>

                    {{-- Body --}}
                    <div class="card-body p-4">

                        {{-- Error Session --}}
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show">

                                {{ session('error') }}

                                <button type="button" class="btn-close" data-bs-dismiss="alert">
                                </button>

                            </div>
                        @endif

                        {{-- Validation Errors --}}
                        @if ($errors->any())

                            <div class="alert alert-danger">

                                <h6 class="fw-bold">
                                    Validasi Gagal
                                </h6>

                                <ul class="mb-0">

                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach

                                </ul>

                            </div>

                        @endif

                        {{-- Installation Form --}}
                        <form method="POST" action="{{ route('installer.process') }}" id="installForm">

                            @csrf

                            {{-- ===================================================== --}}
                            {{-- APP CONFIGURATION --}}
                            {{-- ===================================================== --}}

                            <div class="mb-4">

                                <h5 class="fw-bold mb-3">
                                    🔧 Konfigurasi Aplikasi
                                </h5>

                                <div class="row">

                                    <div class="col-md-6 mb-3">

                                        <label class="form-label">
                                            Nama Aplikasi
                                        </label>

                                        <input type="text" name="app_name" class="form-control"
                                            value="{{ old('app_name', 'SIM PAC IPNU-IPPNU') }}" required>

                                    </div>

                                    <div class="col-md-6 mb-3">

                                        <label class="form-label">
                                            URL Aplikasi
                                        </label>

                                        <input type="url" name="app_url" class="form-control"
                                            value="{{ old('app_url', url('/')) }}" required>

                                    </div>

                                </div>

                            </div>

                            {{-- ===================================================== --}}
                            {{-- DATABASE CONFIG --}}
                            {{-- ===================================================== --}}

                            <div class="mb-4">

                                <h5 class="fw-bold mb-3">
                                    🗄️ PostgreSQL Database
                                </h5>

                                <div class="row">

                                    <div class="col-md-3 mb-3">

                                        <label class="form-label">
                                            Host
                                        </label>

                                        <input type="text" name="db_host" class="form-control"
                                            value="{{ old('db_host', '127.0.0.1') }}" required>

                                    </div>

                                    <div class="col-md-2 mb-3">

                                        <label class="form-label">
                                            Port
                                        </label>

                                        <input type="text" name="db_port" class="form-control"
                                            value="{{ old('db_port', '5432') }}" required>

                                    </div>

                                    <div class="col-md-4 mb-3">

                                        <label class="form-label">
                                            Nama Database
                                        </label>

                                        <input type="text" name="db_name" class="form-control"
                                            value="{{ old('db_name') }}" placeholder="sim_pac" required>

                                    </div>

                                    <div class="col-md-3 mb-3">

                                        <label class="form-label">
                                            Username
                                        </label>

                                        <input type="text" name="db_user" class="form-control"
                                            value="{{ old('db_user', 'postgres') }}" required>

                                    </div>

                                    <div class="col-md-6 mb-3">

                                        <label class="form-label">
                                            Password Database
                                        </label>

                                        <input type="password" name="db_password" class="form-control"
                                            autocomplete="new-password">

                                    </div>

                                </div>

                            </div>

                            {{-- ===================================================== --}}
                            {{-- SUPER ADMIN --}}
                            {{-- ===================================================== --}}

                            <div class="mb-4">

                                <h5 class="fw-bold mb-3">
                                    👑 Super Admin
                                </h5>

                                <div class="row">

                                    <div class="col-md-6 mb-3">

                                        <label class="form-label">
                                            Nama Lengkap
                                        </label>

                                        <input type="text" name="admin_name" class="form-control"
                                            value="{{ old('admin_name') }}" required>

                                    </div>

                                    <div class="col-md-6 mb-3">

                                        <label class="form-label">
                                            Email
                                        </label>

                                        <input type="email" name="admin_email" class="form-control"
                                            value="{{ old('admin_email') }}" required>

                                    </div>

                                    <div class="col-md-4 mb-3">

                                        <label class="form-label">
                                            NIK
                                        </label>

                                        <input type="text" name="admin_nik" class="form-control"
                                            value="{{ old('admin_nik') }}" maxlength="16" pattern="[0-9]{16}"
                                            inputmode="numeric" required>

                                    </div>

                                    <div class="col-md-4 mb-3">

                                        <label class="form-label">
                                            No HP
                                        </label>

                                        <input type="text" name="admin_phone" class="form-control"
                                            value="{{ old('admin_phone') }}" required>

                                    </div>

                                    <div class="col-md-4 mb-3">

                                        <label class="form-label">
                                            Password
                                        </label>

                                        <input type="password" name="admin_password" id="adminPassword"
                                            class="form-control" autocomplete="new-password" required>

                                        {{-- Password Strength --}}
                                        <div class="progress mt-2" style="height: 5px;">

                                            <div class="progress-bar" id="passwordStrength" style="width:0%">
                                            </div>

                                        </div>

                                    </div>

                                    <div class="col-md-4 mb-3">

                                        <label class="form-label">
                                            Konfirmasi Password
                                        </label>

                                        <input type="password" name="admin_password_confirmation"
                                            class="form-control" autocomplete="new-password" required>

                                    </div>

                                </div>

                            </div>

                            {{-- ===================================================== --}}
                            {{-- SUBMIT --}}
                            {{-- ===================================================== --}}

                            <div class="d-grid">

                                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">

                                    <span id="btnText">
                                        Install Sekarang →
                                    </span>

                                </button>

                            </div>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>

    {{-- ========================================================= --}}
    {{-- JAVASCRIPT --}}
    {{-- ========================================================= --}}

    <script>
        /*
                |--------------------------------------------------------------------------
                | Disable Double Submit
                |--------------------------------------------------------------------------
                */

        document.getElementById('installForm')
            .addEventListener('submit', function() {

                const btn = document.getElementById('submitBtn');

                const text = document.getElementById('btnText');

                btn.disabled = true;

                text.innerHTML = `
                <span class="spinner-border spinner-border-sm"></span>
                Memproses Instalasi...
            `;
            });

        /*
        |--------------------------------------------------------------------------
        | Password Strength Indicator
        |--------------------------------------------------------------------------
        */

        const passwordInput =
            document.getElementById('adminPassword');

        const strengthBar =
            document.getElementById('passwordStrength');

        passwordInput.addEventListener('input', function() {

            let strength = 0;

            if (this.value.length >= 8) {
                strength += 25;
            }

            if (/[A-Z]/.test(this.value)) {
                strength += 25;
            }

            if (/[0-9]/.test(this.value)) {
                strength += 25;
            }

            if (/[^A-Za-z0-9]/.test(this.value)) {
                strength += 25;
            }

            strengthBar.style.width = strength + '%';
        });
    </script>

</body>

</html>
