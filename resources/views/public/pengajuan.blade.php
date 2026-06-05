@extends('layouts.public') {{-- Ganti dengan layout public/frontend Anda jika ada --}}

@section('title', 'Pengajuan Rekomendasi Pengesahan')

@section('content')
    <div class="container mt-4 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow-lg border-0 rounded-lg">
                    <div class="card-header bg-success text-white text-center py-4">
                        <h3 class="font-weight-bold mb-0"><i class="fas fa-file-signature mr-2"></i> Form Pengajuan
                            Rekomendasi Pengesahan</h3>
                        <p class="mb-0 mt-2 text-light">Pimpinan Ranting / Pimpinan Komisariat IPNU IPPNU</p>
                    </div>

                    <div class="card-body p-5">
                        {{-- Alert Sukses --}}
                        @if (session('success_pengajuan'))
                            <div class="alert alert-success shadow-sm mb-4">
                                <h5 class="font-weight-bold mb-1"><i class="fas fa-check-circle mr-2"></i> Berhasil!</h5>
                                {{ session('success_pengajuan') }}
                            </div>
                        @endif
                        {{-- Progress Bar --}}
                        <div class="progress mb-4" style="height: 25px;">
                            <div id="progressBar"
                                class="progress-bar bg-warning progress-bar-striped progress-bar-animated font-weight-bold"
                                role="progressbar" style="width: 33%;" aria-valuenow="33" aria-valuemin="0"
                                aria-valuemax="100">Langkah 1 dari 3</div>
                        </div>

                        {{-- FORM START --}}
                        <form id="formPengajuan" action="{{ route('pengajuan.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf

                            {{-- ============================================== --}}
                            {{-- STEP 1: DATA ORGANISASI                        --}}
                            {{-- ============================================== --}}
                            <div class="form-step" id="step1">
                                <h4 class="text-success border-bottom pb-2 mb-4"><i class="fas fa-sitemap mr-2"></i> Langkah
                                    1: Profil Organisasi</h4>

                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label>Tingkat Kepengurusan <span class="text-danger">*</span></label>
                                        <select name="type" class="form-control" required>
                                            <option value="">-- Pilih Tingkat --</option>
                                            <option value="ranting">Pimpinan Ranting (Desa/Kelurahan)</option>
                                            <option value="komisariat">Pimpinan Komisariat (Sekolah/Ponpes)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label>Jenis Organisasi <span class="text-danger">*</span></label>
                                        <select name="jenis_organisasi" class="form-control" required>
                                            <option value="">-- Pilih Organisasi --</option>
                                            <option value="ipnu">IPNU Saja</option>
                                            <option value="ippnu">IPPNU Saja</option>
                                            <option value="bersama">IPNU & IPPNU (Bersama)</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Nama Ranting / Komisariat <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" required
                                        placeholder="Contoh: PR IPNU IPPNU Sukorejo">
                                </div>

                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label>Masa Bhakti / Periode <span class="text-danger">*</span></label>
                                        <input type="text" name="periode" class="form-control" required
                                            placeholder="Contoh: 2026-2028">
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label>Email Resmi Organisasi <span class="text-muted">(Opsional)</span></label>
                                        <input type="email" name="email_organisasi" class="form-control"
                                            placeholder="rantingsukorejo@gmail.com">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Alamat Sekretariat Lengkap <span class="text-danger">*</span></label>
                                    <textarea name="alamat" class="form-control" rows="2" required placeholder="Jl. Raya ..."></textarea>
                                </div>

                                <div class="text-right mt-4">
                                    <button type="button" class="btn btn-success px-4 btn-next">Selanjutnya <i
                                            class="fas fa-arrow-right ml-1"></i></button>
                                </div>
                            </div>

                            {{-- ============================================== --}}
                            {{-- STEP 2: DATA PENGURUS UTAMA                    --}}
                            {{-- ============================================== --}}
                            <div class="form-step" id="step2" style="display: none;">
                                <h4 class="text-success border-bottom pb-2 mb-4"><i class="fas fa-users mr-2"></i> Langkah
                                    2: Data Pengurus Utama</h4>

                                <div class="alert alert-info bg-light border-info">
                                    <i class="fas fa-info-circle mr-1"></i> <strong>Perhatian:</strong> Email Ketua dan
                                    Sekretaris di bawah ini akan digunakan sebagai jalur pengiriman <strong>Username &
                                        Password</strong> jika pengajuan disahkan. Pastikan email aktif!
                                </div>

                                <div class="row">
                                    {{-- KETUA --}}
                                    <div class="col-md-6">
                                        <div class="card bg-light border-0 shadow-sm">
                                            <div class="card-body">
                                                <h5 class="font-weight-bold text-primary mb-3">Data Ketua</h5>
                                                <div class="form-group">
                                                    <label>Nama Lengkap <span class="text-danger">*</span></label>
                                                    <input type="text" name="ketua_name"
                                                        class="form-control form-control-sm" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Email (Aktif) <span class="text-danger">*</span></label>
                                                    <input type="email" name="ketua_email"
                                                        class="form-control form-control-sm" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>No. WhatsApp <span class="text-danger">*</span></label>
                                                    <input type="text" name="ketua_no_hp"
                                                        class="form-control form-control-sm" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Jenis Kelamin <span class="text-danger">*</span></label>
                                                    <select name="ketua_jk" class="form-control form-control-sm" required>
                                                        <option value="Laki-laki">Laki-laki</option>
                                                        <option value="Perempuan">Perempuan</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- SEKRETARIS --}}
                                    <div class="col-md-6">
                                        <div class="card bg-light border-0 shadow-sm">
                                            <div class="card-body">
                                                <h5 class="font-weight-bold text-info mb-3">Data Sekretaris</h5>
                                                <div class="form-group">
                                                    <label>Nama Lengkap <span class="text-danger">*</span></label>
                                                    <input type="text" name="sekretaris_name"
                                                        class="form-control form-control-sm" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Email (Aktif) <span class="text-danger">*</span></label>
                                                    <input type="email" name="sekretaris_email"
                                                        class="form-control form-control-sm" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>No. WhatsApp <span class="text-danger">*</span></label>
                                                    <input type="text" name="sekretaris_no_hp"
                                                        class="form-control form-control-sm" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Jenis Kelamin <span class="text-danger">*</span></label>
                                                    <select name="sekretaris_jk" class="form-control form-control-sm"
                                                        required>
                                                        <option value="Laki-laki">Laki-laki</option>
                                                        <option value="Perempuan">Perempuan</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mt-4">
                                    <button type="button" class="btn btn-secondary px-4 btn-prev"><i
                                            class="fas fa-arrow-left mr-1"></i> Kembali</button>
                                    <button type="button" class="btn btn-success px-4 btn-next">Selanjutnya <i
                                            class="fas fa-arrow-right ml-1"></i></button>
                                </div>
                            </div>

                            {{-- ============================================== --}}
                            {{-- STEP 3: UNGGAH BERKAS (10 SYARAT KONBES)       --}}
                            {{-- ============================================== --}}
                            <div class="form-step" id="step3" style="display: none;">
                                <h4 class="text-success border-bottom pb-2 mb-4"><i class="fas fa-file-upload mr-2"></i>
                                    Langkah 3: Unggah Berkas Persyaratan</h4>

                                <p class="text-muted small mb-4">Pastikan seluruh berkas yang diunggah berformat
                                    <strong>PDF</strong> dan dapat terbaca dengan jelas. Maksimal ukuran per file adalah
                                    2MB.
                                </p>

                                <div class="row">
                                    @php
                                        $berkasList = [
                                            [
                                                'name' => 'file_surat_permohonan',
                                                'label' => '1. Surat Permohonan Pengesahan',
                                            ],
                                            [
                                                'name' => 'file_sk_konferensi',
                                                'label' => '2. SK Konferensi Ranting/Komisariat',
                                            ],
                                            ['name' => 'file_ba_formatur', 'label' => '3. Berita Acara Tim Formatur'],
                                            ['name' => 'file_sk_formatur', 'label' => '4. SK Tim Formatur'],
                                            [
                                                'name' => 'file_susunan_pengurus',
                                                'label' => '5. Susunan Lengkap Pengurus',
                                            ],
                                            [
                                                'name' => 'file_rekomendasi_nu',
                                                'label' => '6. Rekomendasi PRNU / Sekolah',
                                            ],
                                            [
                                                'name' => 'file_biodata_pengurus',
                                                'label' => '7. Biodata Pengurus (PR/PK)',
                                            ],
                                            [
                                                'name' => 'file_hasil_konferensi_lpj',
                                                'label' => '8. Hasil Konferensi & LPJ Demisioner',
                                            ],
                                            ['name' => 'file_dokumentasi', 'label' => '9. Dokumentasi Konferensi'],
                                            ['name' => 'file_profil_organisasi', 'label' => '10. Profil PR/PK'],
                                        ];
                                    @endphp

                                    @foreach ($berkasList as $berkas)
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group border p-3 rounded bg-light h-100">
                                                <label class="mb-2">{{ $berkas['label'] }} <span
                                                        class="text-danger">*</span></label>
                                                <input type="file" name="{{ $berkas['name'] }}"
                                                    class="form-control-file" accept=".pdf" required>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="alert alert-warning mt-3">
                                    <i class="fas fa-exclamation-triangle mr-1"></i> Dengan menekan tombol kirim, kami
                                    menyatakan bahwa seluruh data dan dokumen yang dilampirkan adalah benar dan sah secara
                                    kelembagaan.
                                </div>

                                <div class="d-flex justify-content-between mt-4">
                                    <button type="button" class="btn btn-secondary px-4 btn-prev"><i
                                            class="fas fa-arrow-left mr-1"></i> Kembali</button>
                                    <button type="submit" class="btn btn-primary px-5 font-weight-bold btn-submit"><i
                                            class="fas fa-paper-plane mr-1"></i> Kirim Pengajuan</button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const steps = document.querySelectorAll('.form-step');
            const nextBtns = document.querySelectorAll('.btn-next');
            const prevBtns = document.querySelectorAll('.btn-prev');
            const progressBar = document.getElementById('progressBar');
            const form = document.getElementById('formPengajuan');

            let currentStep = 0;

            function showStep(index) {
                steps.forEach((step, i) => {
                    step.style.display = (i === index) ? 'block' : 'none';
                });

                // Update Progress Bar
                let percent = ((index + 1) / steps.length) * 100;
                progressBar.style.width = percent + '%';
                progressBar.setAttribute('aria-valuenow', percent);
                progressBar.innerText = 'Langkah ' + (index + 1) + ' dari 3';
            }

            function validateStep(index) {
                // Validasi sederhana: pastikan input dengan required di step ini sudah diisi
                const inputs = steps[index].querySelectorAll(
                    'input[required], select[required], textarea[required]');
                let valid = true;
                inputs.forEach(input => {
                    if (!input.value) {
                        input.classList.add('is-invalid');
                        valid = false;
                    } else {
                        input.classList.remove('is-invalid');
                    }
                });

                if (!valid) {
                    alert('Mohon lengkapi semua kolom yang bertanda bintang (*) sebelum melanjutkan.');
                }
                return valid;
            }

            nextBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    if (validateStep(currentStep)) {
                        currentStep++;
                        if (currentStep >= steps.length) currentStep = steps.length - 1;
                        showStep(currentStep);
                    }
                });
            });

            prevBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    currentStep--;
                    if (currentStep < 0) currentStep = 0;
                    showStep(currentStep);
                });
            });

            // Hapus styling invalid saat user mulai mengetik
            form.addEventListener('input', function(e) {
                if (e.target.hasAttribute('required') && e.target.value) {
                    e.target.classList.remove('is-invalid');
                }
            });

            // Pencegahan klik ganda saat submit
            form.addEventListener('submit', function() {
                const submitBtn = document.querySelector('.btn-submit');
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Sedang Mengirim...';
                submitBtn.setAttribute('disabled', 'true');
            });
        });
    </script>
@endpush
