<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Requirements - SIM PAC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5>Pengecekan Requirements</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            @foreach ($requirements as $requirement => $passed)
                                <tr>
                                    <td>{{ $requirement }}</td>
                                    <td class="text-center">
                                        @if ($passed)
                                            <span class="badge bg-success">✓ OK</span>
                                        @else
                                            <span class="badge bg-danger">✗ Failed</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </table>

                        @if ($allPassed)
                            <div class="alert alert-success">
                                ✅ Semua requirements terpenuhi. Klik Next untuk melanjutkan.
                            </div>
                            <a href="{{ url('/install/environment') }}" class="btn btn-primary w-100">Next →</a>
                        @else
                            <div class="alert alert-danger">
                                ❌ Ada requirements yang belum terpenuhi. Silakan lengkapi server Anda.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
