@extends('layouts.admin')

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 text-success font-weight-bold">Form Akreditasi Ekosistem Digital - IPPNU</h1>
            <a href="{{ route('akreditasi.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
        </div>

        <form action="{{ route('akreditasi.store') }}" method="POST" enctype="multipart/form-data" id="formAkreditasiIPPNU">
            @csrf

            <div class="card shadow mb-4 border-bottom-success">
                <div class="card-header bg-success text-white p-0">
                    <ul class="nav nav-tabs card-header-tabs m-0 border-bottom-0" id="myTab" role="tablist">
                        <li class="nav-item"><a class="nav-link active text-dark font-weight-bold" data-toggle="tab"
                                href="#lapis1"><i class="fas fa-envelope"></i> Surat</a></li>
                        <li class="nav-item"><a class="nav-link text-white" data-toggle="tab" href="#bab1">BAB I
                                (Organisasi)</a></li>
                        <li class="nav-item"><a class="nav-link text-white" data-toggle="tab" href="#bab2">BAB II
                                (Kaderisasi)</a></li>
                        <li class="nav-item"><a class="nav-link text-white" data-toggle="tab" href="#bab3">BAB III
                                (Kelembagaan)</a></li>
                        <li class="nav-item"><a class="nav-link text-white" data-toggle="tab" href="#bab4">BAB IV
                                (Aswaja)</a></li>
                        <li class="nav-item"><a class="nav-link text-white" data-toggle="tab" href="#bab5">BAB V (KPP)</a>
                        </li>
                        <li class="nav-item"><a class="nav-link text-white" data-toggle="tab" href="#bab6">BAB VI
                                (Media)</a></li>
                    </ul>
                </div>

                <div class="card-body bg-white">
                    <div class="tab-content">

                        <div class="tab-pane fade show active" id="lapis1">
                            <h5 class="border-bottom pb-2 text-success font-weight-bold">Integrasi Surat Organisasi</h5>
                            <div class="row mt-3">
                                <div class="col-md-6 form-group">
                                    <label>Pilih Surat Permohonan <span class="text-danger">*</span></label>
                                    <select name="surat_permohonan_id" class="form-control" required>
                                        <option value="">-- Pilih Surat Keluar --</option>
                                        @foreach ($suratKeluars ?? [] as $surat)
                                            <option value="{{ $surat->id }}">{{ $surat->nomor_surat }} -
                                                {{ $surat->perihal }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Pilih Surat Pernyataan <span class="text-danger">*</span></label>
                                    <select name="surat_pernyataan_id" class="form-control" required>
                                        <option value="">-- Pilih Surat Keluar --</option>
                                        @foreach ($suratKeluars ?? [] as $surat)
                                            <option value="{{ $surat->id }}">{{ $surat->nomor_surat }} -
                                                {{ $surat->perihal }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group mt-2">
                                <label>Kata Pengantar</label>
                                <textarea name="kata_pengantar" class="form-control tinymce-editor" placeholder="Tuliskan kata pengantar singkat..."></textarea>
                            </div>
                            <div class="form-group">
                                <label>Deskripsi Singkat Pengajuan</label>
                                <textarea name="deskripsi_singkat" class="form-control tinymce-editor"
                                    placeholder="Tuliskan gambaran umum periode kepengurusan ini..."></textarea>
                            </div>
                            <div class="text-right"><button type="button" class="btn btn-success"
                                    onclick="$('#bab1-tab').tab('show')">Lanjut ke BAB I <i
                                        class="fas fa-arrow-right"></i></button></div>
                        </div>

                        <div class="tab-pane fade" id="bab1">
                            <h5 class="border-bottom pb-2 text-success font-weight-bold">BAB I: Penguatan Organisasi,
                                Administrasi dan Stakeholder</h5>
                            <p class="small text-muted">Lampirkan tautan/link scan dokumen administrasi (Buku Agenda, Buku
                                Inventaris, dll).</p>
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered" id="table-ippnu-bab1">
                                    <thead class="bg-light text-center">
                                        <tr>
                                            <th>Nama Dokumen / Administrasi</th>
                                            <th>Link Scan (G-Drive / PDF)</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><input type="text" name="ippnu_bab1_dokumen[]"
                                                    class="form-control form-control-sm"
                                                    placeholder="Contoh: Buku Agenda Kegiatan" required></td>
                                            <td><input type="url" name="ippnu_bab1_link[]"
                                                    class="form-control form-control-sm"
                                                    placeholder="https://drive.google.com/..." required></td>
                                            <td class="text-center"><button type="button"
                                                    class="btn btn-sm btn-danger remove-row"><i
                                                        class="fas fa-trash"></i></button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" class="btn btn-sm btn-success add-row" data-table="table-ippnu-bab1"
                                data-prefix="ippnu_bab1"><i class="fas fa-plus"></i> Tambah Dokumen</button>
                            <div class="text-right mt-3"><button type="button" class="btn btn-success"
                                    onclick="$('#bab2-tab').tab('show')">Lanjut ke BAB II <i
                                        class="fas fa-arrow-right"></i></button></div>
                        </div>

                        <div class="tab-pane fade" id="bab2">
                            <h5 class="border-bottom pb-2 text-success font-weight-bold">BAB II: Kaderisasi dan
                                Pengembangan SDM</h5>
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered" id="table-ippnu-bab2">
                                    <thead class="bg-light text-center">
                                        <tr>
                                            <th>Nama Kegiatan (Makesta/Lakmud/Inovasi)</th>
                                            <th>Tanggal</th>
                                            <th>Link Dokumen (TOR/LPJ/Sertifikat)</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><input type="text" name="ippnu_bab2_kegiatan[]"
                                                    class="form-control form-control-sm"></td>
                                            <td><input type="date" name="ippnu_bab2_tanggal[]"
                                                    class="form-control form-control-sm"></td>
                                            <td><input type="url" name="ippnu_bab2_link[]"
                                                    class="form-control form-control-sm"></td>
                                            <td class="text-center"><button type="button"
                                                    class="btn btn-sm btn-danger remove-row"><i
                                                        class="fas fa-trash"></i></button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" class="btn btn-sm btn-success add-row" data-table="table-ippnu-bab2"
                                data-prefix="ippnu_bab2"><i class="fas fa-plus"></i> Tambah Kegiatan Kaderisasi</button>
                            <div class="text-right mt-3"><button type="button" class="btn btn-success"
                                    onclick="$('#bab3-tab').tab('show')">Lanjut ke BAB III <i
                                        class="fas fa-arrow-right"></i></button></div>
                        </div>

                        <div class="tab-pane fade" id="bab3">
                            <h5 class="border-bottom pb-2 text-success font-weight-bold">BAB III: Penguatan Kelembagaan
                            </h5>
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered" id="table-ippnu-bab3">
                                    <thead class="bg-light text-center">
                                        <tr>
                                            <th>Program Inovasi (Budaya/Penelitian/Ekonomi)</th>
                                            <th>Realisasi / Keterangan</th>
                                            <th>Link Dokumen / Foto</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><input type="text" name="ippnu_bab3_program[]"
                                                    class="form-control form-control-sm"></td>
                                            <td><input type="text" name="ippnu_bab3_realisasi[]"
                                                    class="form-control form-control-sm"></td>
                                            <td><input type="url" name="ippnu_bab3_link[]"
                                                    class="form-control form-control-sm"></td>
                                            <td class="text-center"><button type="button"
                                                    class="btn btn-sm btn-danger remove-row"><i
                                                        class="fas fa-trash"></i></button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" class="btn btn-sm btn-success add-row" data-table="table-ippnu-bab3"
                                data-prefix="ippnu_bab3"><i class="fas fa-plus"></i> Tambah Program Inovasi</button>
                            <div class="text-right mt-3"><button type="button" class="btn btn-success"
                                    onclick="$('#bab4-tab').tab('show')">Lanjut ke BAB IV <i
                                        class="fas fa-arrow-right"></i></button></div>
                        </div>

                        <div class="tab-pane fade" id="bab4">
                            <h5 class="border-bottom pb-2 text-success font-weight-bold">BAB IV: Penguatan Faham Aswaja &
                                Ke-NU-an</h5>
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered" id="table-ippnu-bab4">
                                    <thead class="bg-light text-center">
                                        <tr>
                                            <th>Kegiatan Keagamaan / Sosial / Women Inspiration</th>
                                            <th>Waktu Pelaksanaan</th>
                                            <th>Link Laporan / CV Figur</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><input type="text" name="ippnu_bab4_kegiatan[]"
                                                    class="form-control form-control-sm"></td>
                                            <td><input type="text" name="ippnu_bab4_waktu[]"
                                                    class="form-control form-control-sm"></td>
                                            <td><input type="url" name="ippnu_bab4_link[]"
                                                    class="form-control form-control-sm"></td>
                                            <td class="text-center"><button type="button"
                                                    class="btn btn-sm btn-danger remove-row"><i
                                                        class="fas fa-trash"></i></button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" class="btn btn-sm btn-success add-row" data-table="table-ippnu-bab4"
                                data-prefix="ippnu_bab4"><i class="fas fa-plus"></i> Tambah Kegiatan Aswaja</button>
                            <div class="text-right mt-3"><button type="button" class="btn btn-success"
                                    onclick="$('#bab5-tab').tab('show')">Lanjut ke BAB V <i
                                        class="fas fa-arrow-right"></i></button></div>
                        </div>

                        <div class="tab-pane fade" id="bab5">
                            <h5 class="border-bottom pb-2 text-success font-weight-bold">BAB V: Penguatan Korp Pelajar
                                Putri (KPP)</h5>
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered" id="table-ippnu-bab5">
                                    <thead class="bg-light text-center">
                                        <tr>
                                            <th>Data / Administrasi KPP (Diklatama, dll)</th>
                                            <th>Keterangan</th>
                                            <th>Link Dokumen / Stempel / dll</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><input type="text" name="ippnu_bab5_data[]"
                                                    class="form-control form-control-sm"></td>
                                            <td><input type="text" name="ippnu_bab5_keterangan[]"
                                                    class="form-control form-control-sm"></td>
                                            <td><input type="url" name="ippnu_bab5_link[]"
                                                    class="form-control form-control-sm"></td>
                                            <td class="text-center"><button type="button"
                                                    class="btn btn-sm btn-danger remove-row"><i
                                                        class="fas fa-trash"></i></button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" class="btn btn-sm btn-success add-row" data-table="table-ippnu-bab5"
                                data-prefix="ippnu_bab5"><i class="fas fa-plus"></i> Tambah Data KPP</button>
                            <div class="text-right mt-3"><button type="button" class="btn btn-success"
                                    onclick="$('#bab6-tab').tab('show')">Lanjut ke BAB VI <i
                                        class="fas fa-arrow-right"></i></button></div>
                        </div>

                        <div class="tab-pane fade" id="bab6">
                            <h5 class="border-bottom pb-2 text-success font-weight-bold">BAB VI: Pengembangan Media</h5>
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered" id="table-ippnu-bab6">
                                    <thead class="bg-light text-center">
                                        <tr>
                                            <th>Platform Sosial Media / Perangkat Pendukung</th>
                                            <th>Nama Akun / Nama Perangkat</th>
                                            <th>Link Profil / Screenshoot Bukti</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><input type="text" name="ippnu_bab6_platform[]"
                                                    class="form-control form-control-sm"
                                                    placeholder="Contoh: Instagram / Laptop"></td>
                                            <td><input type="text" name="ippnu_bab6_akun[]"
                                                    class="form-control form-control-sm" placeholder="@pcippnugresik">
                                            </td>
                                            <td><input type="url" name="ippnu_bab6_link[]"
                                                    class="form-control form-control-sm"></td>
                                            <td class="text-center"><button type="button"
                                                    class="btn btn-sm btn-danger remove-row"><i
                                                        class="fas fa-trash"></i></button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" class="btn btn-sm btn-success add-row mb-4"
                                data-table="table-ippnu-bab6" data-prefix="ippnu_bab6"><i class="fas fa-plus"></i> Tambah
                                Media / Perangkat</button>

                            <hr class="mt-5 border-success">
                            <div class="alert alert-success text-center">
                                Pastikan Anda telah mengisi semua form dan melampirkan tautan bukti sebelum menekan tombol
                                simpan.
                            </div>
                            <div class="form-group mb-0 text-center mt-4">
                                <button type="submit" class="btn btn-success btn-lg font-weight-bold shadow px-5">
                                    <i class="fas fa-paper-plane"></i> Ajukan Akreditasi IPPNU Sekarang
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js"></script>
    <script>
        $(document).ready(function() {

            // 1. Inisialisasi TinyMCE
            tinymce.init({
                selector: '.tinymce-editor',
                height: 250,
                menubar: false,
                plugins: ['advlist', 'autolink', 'lists', 'link'],
                toolbar: 'undo redo | bold italic underline | bullist numlist',
                branding: false,
                promotion: false
            });

            // 2. Tab styling toggler
            $('.nav-link').on('click', function() {
                $('.nav-link').removeClass('text-dark font-weight-bold').addClass('text-white');
                $(this).removeClass('text-white').addClass('text-dark font-weight-bold');
            });

            // 3. Add Row Logic untuk Form Dinamis IPPNU
            $('.add-row').click(function() {
                var tableId = $(this).data('table');
                var prefix = $(this).data('prefix');
                var newRow = '';

                if (prefix === 'ippnu_bab1') {
                    newRow = `<tr>
                            <td><input type="text" name="${prefix}_dokumen[]" class="form-control form-control-sm" required></td>
                            <td><input type="url" name="${prefix}_link[]" class="form-control form-control-sm" required></td>
                            <td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-row"><i class="fas fa-trash"></i></button></td>
                          </tr>`;
                } else if (prefix === 'ippnu_bab2') {
                    newRow = `<tr>
                            <td><input type="text" name="${prefix}_kegiatan[]" class="form-control form-control-sm"></td>
                            <td><input type="date" name="${prefix}_tanggal[]" class="form-control form-control-sm"></td>
                            <td><input type="url" name="${prefix}_link[]" class="form-control form-control-sm"></td>
                            <td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-row"><i class="fas fa-trash"></i></button></td>
                          </tr>`;
                } else if (prefix === 'ippnu_bab3') {
                    newRow = `<tr>
                            <td><input type="text" name="${prefix}_program[]" class="form-control form-control-sm"></td>
                            <td><input type="text" name="${prefix}_realisasi[]" class="form-control form-control-sm"></td>
                            <td><input type="url" name="${prefix}_link[]" class="form-control form-control-sm"></td>
                            <td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-row"><i class="fas fa-trash"></i></button></td>
                          </tr>`;
                } else if (prefix === 'ippnu_bab4') {
                    newRow = `<tr>
                            <td><input type="text" name="${prefix}_kegiatan[]" class="form-control form-control-sm"></td>
                            <td><input type="text" name="${prefix}_waktu[]" class="form-control form-control-sm"></td>
                            <td><input type="url" name="${prefix}_link[]" class="form-control form-control-sm"></td>
                            <td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-row"><i class="fas fa-trash"></i></button></td>
                          </tr>`;
                } else if (prefix === 'ippnu_bab5') {
                    newRow = `<tr>
                            <td><input type="text" name="${prefix}_data[]" class="form-control form-control-sm"></td>
                            <td><input type="text" name="${prefix}_keterangan[]" class="form-control form-control-sm"></td>
                            <td><input type="url" name="${prefix}_link[]" class="form-control form-control-sm"></td>
                            <td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-row"><i class="fas fa-trash"></i></button></td>
                          </tr>`;
                } else if (prefix === 'ippnu_bab6') {
                    newRow = `<tr>
                            <td><input type="text" name="${prefix}_platform[]" class="form-control form-control-sm"></td>
                            <td><input type="text" name="${prefix}_akun[]" class="form-control form-control-sm"></td>
                            <td><input type="url" name="${prefix}_link[]" class="form-control form-control-sm"></td>
                            <td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-row"><i class="fas fa-trash"></i></button></td>
                          </tr>`;
                }

                $('#' + tableId + ' tbody').append(newRow);
            });

            // 4. Remove Row Logic
            $(document).on('click', '.remove-row', function() {
                if ($(this).closest('tbody').find('tr').length > 1) {
                    $(this).closest('tr').remove();
                } else {
                    alert('Minimal harus ada 1 baris isian!');
                }
            });
        });
    </script>
@endsection
