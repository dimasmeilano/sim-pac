<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Form Evaluasi Digital</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #f4f7f6;
            padding-bottom: 90px;
        }

        .bg-nu {
            background-color: #00723b;
            color: white;
        }

        .card-header {
            cursor: pointer;
        }

        .input-nilai {
            width: 80px;
            text-align: center;
            font-weight: bold;
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
        <div class="container">
            <h5 class="font-weight-bold mb-1"><i class="fas fa-user-graduate mr-2"></i>Halo,
                {{ explode(' ', $peserta->nama_lengkap)[0] }}!</h5>
            <small class="text-warning">Silakan isi evaluasi harian Anda dengan jujur dan objektif.</small>
        </div>
    </div>

    <div class="container mt-3">
        @if (session('success'))
            <div class="alert alert-success shadow-sm rounded font-weight-bold">
                <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('peserta.evaluasi.store', $event->id) }}" method="POST">
            @csrf

            <div class="alert alert-info shadow-sm border-left-info font-weight-bold mb-3">
                <i class="fas fa-calendar-day mr-2"></i> Evaluasi Kegiatan - Hari Ke-{{ $hari_ke }}
                <div class="small mt-1 text-muted">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</div>
                <!-- Simpan hari_ke secara diam-diam (hidden) untuk dikirim ke database -->
                <input type="hidden" name="hari_ke" value="{{ $hari_ke }}">
            </div>

            <div class="accordion" id="accordionEvaluasi">

                <!-- 1. EVALUASI PEMATERI -->
                <div class="card shadow-sm border-0 mb-2">
                    <div class="card-header bg-white" data-toggle="collapse" data-target="#collapsePemateri">
                        <h6 class="mb-0 font-weight-bold text-primary"><i class="fas fa-chalkboard-teacher mr-2"></i>1.
                            Penilaian Pemateri</h6>
                    </div>
                    <div id="collapsePemateri" class="collapse show" data-parent="#accordionEvaluasi">
                        <div class="card-body bg-light">
                            <small class="text-muted d-block mb-3">Skala Nilai 50-90. Biarkan kosong jika materi
                                tersebut tidak ada di hari ini.</small>

                            <!-- Auto Load Materi dari Database -->
                            <!-- Auto Load Materi Hari Ini Saja -->
                            @forelse($materi_hari_ini as $materi)
                                <div class="bg-white p-3 rounded shadow-sm mb-3 border">
                                    <h6 class="font-weight-bold mb-1">{{ $materi->nama_materi }}</h6>
                                    <p class="small text-muted mb-2"><i
                                            class="fas fa-chalkboard-teacher mr-1"></i>{{ $materi->nama_pemateri ?? 'Nama Pemateri Belum Diinput' }}
                                    </p>

                                    <div class="d-flex align-items-center mb-2">
                                        <label class="mb-0 mr-2 small font-weight-bold">Nilai:</label>
                                        <input type="number" name="pemateri[{{ $materi->id }}][nilai]"
                                            class="form-control form-control-sm input-nilai" min="50"
                                            max="90" placeholder="50-90">
                                    </div>
                                    <input type="text" name="pemateri[{{ $materi->id }}][catatan]"
                                        class="form-control form-control-sm" placeholder="Catatan khusus...">
                                </div>
                            @empty
                                <div class="text-center p-3">
                                    <i class="fas fa-bed text-muted fa-2x mb-2"></i>
                                    <p class="text-muted small mb-0">Tidak ada jadwal materi untuk hari ini.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- 2. EVALUASI PANITIA -->
                <div class="card shadow-sm border-0 mb-2">
                    <div class="card-header bg-white" data-toggle="collapse" data-target="#collapsePanitia">
                        <h6 class="mb-0 font-weight-bold text-success"><i class="fas fa-users-cog mr-2"></i>2. Penilaian
                            Panitia</h6>
                    </div>
                    <div id="collapsePanitia" class="collapse" data-parent="#accordionEvaluasi">
                        <div class="card-body bg-light">
                            <div class="bg-white p-3 rounded shadow-sm border">
                                <label class="small font-weight-bold">Nilai Kinerja Panitia Hari Ini (50-90):</label>
                                <input type="number" name="panitia[nilai]" class="form-control mb-2" min="50"
                                    max="90" required>

                                <label class="small font-weight-bold">Catatan / Saran untuk Panitia:</label>
                                <textarea name="panitia[catatan]" class="form-control" rows="2" placeholder="Tulis masukan Anda..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. EVALUASI INSTRUKTUR/PELATIH -->
                <div class="card shadow-sm border-0 mb-2">
                    <div class="card-header bg-white" data-toggle="collapse" data-target="#collapseInstruktur">
                        <h6 class="mb-0 font-weight-bold text-info"><i class="fas fa-user-shield mr-2"></i>3. Penilaian
                            Instruktur Pelatih</h6>
                    </div>
                    <div id="collapseInstruktur" class="collapse" data-parent="#accordionEvaluasi">
                        <div class="card-body bg-light">
                            <div class="bg-white p-3 rounded shadow-sm border">
                                <label class="small font-weight-bold">Nilai Pendampingan Instruktur Hari Ini
                                    (50-90):</label>
                                <input type="number" name="instruktur[nilai]" class="form-control mb-2" min="50"
                                    max="90" required>

                                <label class="small font-weight-bold">Catatan / Keluhan / Apresiasi:</label>
                                <textarea name="instruktur[catatan]" class="form-control" rows="2" placeholder="Tulis masukan Anda..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. REFLEKSI HARIAN -->
                <div class="card shadow-sm border-0 mb-2">
                    <div class="card-header bg-white" data-toggle="collapse" data-target="#collapseRefleksi">
                        <h6 class="mb-0 font-weight-bold text-danger"><i class="fas fa-brain mr-2"></i>4. Refleksi
                            Harian</h6>
                    </div>
                    <div id="collapseRefleksi" class="collapse" data-parent="#accordionEvaluasi">
                        <div class="card-body bg-light">
                            <div class="bg-white p-3 rounded shadow-sm border mb-2">
                                <label class="small font-weight-bold">1. Pengalaman belajar apa yang Anda peroleh hari
                                    ini yang paling bermanfaat?</label>
                                <textarea name="refleksi[pengalaman]" class="form-control form-control-sm" rows="2" required></textarea>
                            </div>
                            <div class="bg-white p-3 rounded shadow-sm border mb-2">
                                <label class="small font-weight-bold">2. Bagaimana tingkat partisipasi Anda hari
                                    ini?</label>
                                <textarea name="refleksi[partisipasi]" class="form-control form-control-sm" rows="2" required></textarea>
                            </div>
                            <div class="bg-white p-3 rounded shadow-sm border mb-2">
                                <label class="small font-weight-bold">3. Adakah hal yang menghambat/mendorong Anda
                                    berpartisipasi hari ini?</label>
                                <textarea name="refleksi[hambatan]" class="form-control form-control-sm" rows="2" required></textarea>
                            </div>
                            <div class="bg-white p-3 rounded shadow-sm border mb-2">
                                <label class="small font-weight-bold">4. Pengetahuan apa sajakah yang Anda peroleh hari
                                    ini?</label>
                                <textarea name="refleksi[pengetahuan]" class="form-control form-control-sm" rows="2" required></textarea>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Tombol Simpan Melayang di Bawah -->
            <div class="floating-bottom text-center">
                <button type="submit"
                    class="btn btn-warning btn-lg btn-block font-weight-bold shadow rounded-pill text-dark">
                    <i class="fas fa-paper-plane mr-2"></i> KIRIM EVALUASI
                </button>
            </div>
        </form>
    </div>

    <!-- Script Bootstrap untuk Accordion -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
