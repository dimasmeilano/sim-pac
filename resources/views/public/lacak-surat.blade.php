<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lacak Surat - SIM PAC IPNU IPPNU</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .search-box {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 600px;
        }

        .form-control-lg {
            border-radius: 50px;
            padding-left: 25px;
        }

        .btn-lacak {
            border-radius: 50px;
            padding: 10px 30px;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="search-box mx-auto text-center">
            <h2 class="font-weight-bold text-success mb-2">E-OFFICE PAC</h2>
            <p class="text-muted mb-4">Masukkan nomor surat untuk melacak status dan memvalidasi keaslian dokumen.</p>

            <form id="formLacak" action="{{ route('verifikasi.surat') }}" method="GET"
                onsubmit="event.preventDefault(); prosesLacak();">
                <div class="form-group mb-4">
                    <input type="text" id="nomorInput" class="form-control form-control-lg text-center"
                        placeholder="Contoh: 024/PAC/SRP/7354/XVI/V/26" required autocomplete="off">

                    <input type="hidden" name="nomor" id="nomorHidden">
                </div>
                <button type="submit" class="btn btn-success btn-lg btn-lacak shadow-sm">
                    🔍 Lacak Dokumen
                </button>
            </form>

            <script>
                function prosesLacak() {
                    // 1. Ambil teks yang diketik user
                    let inputText = document.getElementById('nomorInput').value;

                    // 2. Ubah menjadi Base64 (btoa adalah fungsi bawaan JS)
                    let base64Text = btoa(inputText);

                    // 3. Masukkan hasil Base64 ke input tersembunyi
                    document.getElementById('nomorHidden').value = base64Text;

                    // 4. Kirim form ke server
                    document.getElementById('formLacak').submit();
                }
            </script>
            <div class="mt-4 text-muted small">
                Atau gunakan kamera <i>smartphone</i> Anda untuk men-<i>scan</i> QR Code yang tertera pada bagian bawah
                surat.
            </div>
        </div>
    </div>

</body>

</html>
