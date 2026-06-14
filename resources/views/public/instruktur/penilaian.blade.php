<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Lembar Penilaian - {{ $materi->nama_materi }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #f4f7f6;
            padding-bottom: 90px;
            font-size: 14px;
        }

        .bg-nu {
            background-color: #00723b;
            color: white;
        }

        /* Modifikasi Input Tabel */
        .table th {
            background-color: #e9ecef;
            vertical-align: middle !important;
        }

        .input-angka {
            width: 70px;
            text-align: center;
            font-weight: bold;
        }

        .input-abjad {
            width: 60px;
            text-align: center;
            font-weight: bold;
            background-color: #fff3cd;
        }

        .input-catatan {
            min-width: 150px;
        }

        .floating-bottom {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.95);
            padding: 15px;
            box-shadow: 0 -4px 10px rgba(0, 0, 0, 0.05);
            z-index: 1030;
        }
    </style>
</head>

<body>

    <div class="bg-nu pt-4 pb-4 shadow-sm">
        <div class="container text-center">
            <h5 class="font-weight-bold mb-1 text-uppercase">LEMBAR OBSERVASI & PENILAIAN</h5>
            <h6 class="mb-2 text-warning">{{ $materi->nama_materi }}</h6>
            <small class="d-block text-white">Instruktur: <strong>{{ $materi->nama_instruktur }}</strong></small>
        </div>
    </div>

    <div class="container-fluid mt-3">
        @if (session('success'))
            <div class="alert alert-success shadow-sm text-center font-weight-bold">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('instruktur.penilaian.store', $materi->token_rahasia) }}" method="POST">
            @csrf
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0">
                            <thead class="text-center">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="20%">Nama Peserta</th>
                                    <th>Kognitif</th>
                                    <th>Keaktifan</th>
                                    <th>Nilai Akhir</th>
                                    <th>Abjad</th>
                                    <th>Catatan / Deskripsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($materi->event->pesertas as $index => $peserta)
                                    @php
                                        $data = $nilai_sebelumnya[$peserta->id] ?? null;
                                    @endphp
                                    <tr>
                                        <td class="text-center align-middle">{{ $index + 1 }}</td>
                                        <td class="align-middle font-weight-bold">{{ $peserta->nama_lengkap }}</td>
                                        <td class="text-center align-middle">
                                            <input type="number" name="nilai[{{ $peserta->id }}][kognitif]"
                                                class="form-control form-control-sm mx-auto input-angka item-kognitif"
                                                data-id="{{ $peserta->id }}" value="{{ $data->kognitif ?? '' }}"
                                                min="0" max="100">
                                        </td>
                                        <td class="text-center align-middle">
                                            <input type="number" name="nilai[{{ $peserta->id }}][keaktifan]"
                                                class="form-control form-control-sm mx-auto input-angka item-keaktifan"
                                                data-id="{{ $peserta->id }}" value="{{ $data->keaktifan ?? '' }}"
                                                min="0" max="100">
                                        </td>
                                        <td class="text-center align-middle">
                                            <input type="number" name="nilai[{{ $peserta->id }}][nilai_akhir]"
                                                id="akhir_{{ $peserta->id }}"
                                                class="form-control form-control-sm mx-auto input-angka bg-light"
                                                value="{{ $data->nilai_akhir ?? '' }}" readonly>
                                        </td>
                                        <td class="text-center align-middle">
                                            <input type="text" name="nilai[{{ $peserta->id }}][abjad]"
                                                id="abjad_{{ $peserta->id }}"
                                                class="form-control form-control-sm mx-auto input-abjad"
                                                value="{{ $data->abjad ?? '' }}" readonly>
                                        </td>
                                        <td class="align-middle">
                                            <input type="text" name="nilai[{{ $peserta->id }}][catatan]"
                                                class="form-control form-control-sm input-catatan"
                                                placeholder="Ketik catatan..." value="{{ $data->catatan ?? '' }}">
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">Belum ada peserta.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if ($materi->event->pesertas->count() > 0)
                <div class="floating-bottom text-center">
                    <button type="submit" class="btn btn-warning px-5 font-weight-bold shadow rounded-pill text-dark">
                        <i class="fas fa-save mr-2"></i> SIMPAN PENILAIAN
                    </button>
                </div>
            @endif
        </form>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const kognitifInputs = document.querySelectorAll('.item-kognitif');
            const keaktifanInputs = document.querySelectorAll('.item-keaktifan');

            function hitungNilaiAkhir(id) {
                // Ambil elemen input berdasarkan ID peserta
                let kognitifEl = document.querySelector(`.item-kognitif[data-id='${id}']`);
                let keaktifanEl = document.querySelector(`.item-keaktifan[data-id='${id}']`);
                let akhirEl = document.getElementById(`akhir_${id}`);
                let abjadEl = document.getElementById(`abjad_${id}`);

                let kognitif = parseFloat(kognitifEl.value) || 0;
                let keaktifan = parseFloat(keaktifanEl.value) || 0;

                // Hitung rata-rata jika salah satu atau keduanya diisi
                if (kognitifEl.value !== "" || keaktifanEl.value !== "") {
                    // Rumus: (Kognitif + Keaktifan) dibagi 2
                    let nilaiAkhir = Math.round((kognitif + keaktifan) / 2);
                    akhirEl.value = nilaiAkhir;

                    // Tentukan Abjad (Bisa disesuaikan dengan standar PAC Anda)
                    if (nilaiAkhir >= 85) {
                        abjadEl.value = 'A';
                    } else if (nilaiAkhir >= 75) {
                        abjadEl.value = 'B';
                    } else if (nilaiAkhir >= 60) {
                        abjadEl.value = 'C';
                    } else {
                        abjadEl.value = 'D';
                    }
                } else {
                    akhirEl.value = "";
                    abjadEl.value = "";
                }
            }

            // Pasang pendeteksi ketikan di setiap kolom kognitif dan keaktifan
            kognitifInputs.forEach(input => {
                input.addEventListener('input', function() {
                    hitungNilaiAkhir(this.getAttribute('data-id'));
                });
            });
            keaktifanInputs.forEach(input => {
                input.addEventListener('input', function() {
                    hitungNilaiAkhir(this.getAttribute('data-id'));
                });
            });
        });
    </script>

</body>

</html>
