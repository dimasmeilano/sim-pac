<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Evaluasi - {{ $event->tema }}</title>
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
    </style>
</head>

<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-lg border-0 rounded-lg">
                    <div class="card-body bg-nu text-center rounded-top py-4">
                        <i class="fas fa-clipboard-check fa-3x mb-3 text-warning"></i>
                        <h5 class="font-weight-bold mb-0">Portal Evaluasi Makesta</h5>
                    </div>
                    <div class="card-body p-4 bg-white rounded-bottom">
                        <div class="text-center mb-4">
                            <p class="small text-muted mb-0">{{ $event->tema }}</p>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger small text-center font-weight-bold rounded">
                                <i class="fas fa-exclamation-circle mr-1"></i> {{ $errors->first() }}
                            </div>
                        @endif

                        <form action="{{ route('peserta.evaluasi.authenticate', $event->id) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label class="font-weight-bold small text-muted">NAMA PESERTA</label>
                                <select name="peserta_id" class="form-control form-control-lg shadow-sm" required>
                                    <option value="">-- Pilih Nama Anda --</option>
                                    @foreach ($event->pesertas->sortBy('nama_lengkap') as $p)
                                        <option value="{{ $p->id }}">{{ $p->nama_lengkap }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mt-3">
                                <label class="font-weight-bold small text-muted">NOMOR WHATSAPP (SEBAGAI
                                    PASSWORD)</label>
                                <input type="text" inputmode="numeric" name="no_wa"
                                    class="form-control form-control-lg shadow-sm" placeholder="Contoh: 08123456789"
                                    required autocomplete="off">
                                <small class="text-info mt-1 d-block"><i class="fas fa-info-circle"></i> Gunakan nomor
                                    WA yang Anda daftarkan.</small>
                            </div>
                            <button type="submit"
                                class="btn btn-nu btn-block btn-lg font-weight-bold shadow mt-4 rounded-pill">Mulai
                                Evaluasi <i class="fas fa-arrow-right ml-1"></i></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
