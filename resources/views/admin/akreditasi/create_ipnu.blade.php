@extends('layouts.adminlte')

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
            <h1 class="h3 text-gray-800">Form Pengajuan Akreditasi Digital</h1>
            <a href="{{ route('akreditasi.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
        </div>

        <form action="{{ route('akreditasi.store') }}" method="POST" enctype="multipart/form-data" id="formAkreditasi">
            @csrf

            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white p-0">
                    <ul class="nav nav-tabs card-header-tabs m-0 border-bottom-0" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active text-dark font-weight-bold" id="lapis1-tab" data-toggle="tab"
                                href="#lapis1" role="tab"><i class="fas fa-envelope"></i> Surat</a>
                        </li>
                        <li class="nav-item"><a class="nav-link text-white" id="bab1-tab" data-toggle="tab" href="#bab1"
                                role="tab">BAB I</a></li>
                        <li class="nav-item"><a class="nav-link text-white" id="bab2-tab" data-toggle="tab" href="#bab2"
                                role="tab">BAB II</a></li>
                        <li class="nav-item"><a class="nav-link text-white" id="bab3-tab" data-toggle="tab" href="#bab3"
                                role="tab">BAB III</a></li>
                        <li class="nav-item"><a class="nav-link text-white" id="bab4-tab" data-toggle="tab" href="#bab4"
                                role="tab">BAB IV</a></li>
                        <li class="nav-item"><a class="nav-link text-white" id="bab5-tab" data-toggle="tab" href="#bab5"
                                role="tab">BAB V</a></li>
                        <li class="nav-item"><a class="nav-link text-white" id="bab6-tab" data-toggle="tab" href="#bab6"
                                role="tab">BAB VI</a></li>
                        <li class="nav-item"><a class="nav-link text-white" id="bab7-tab" data-toggle="tab" href="#bab7"
                                role="tab">BAB VII</a></li>
                    </ul>
                </div>

                <div class="card-body bg-white">
                    <div class="tab-content" id="myTabContent">

                        <div class="tab-pane fade show active" id="lapis1" role="tabpanel">
                            <h5 class="border-bottom pb-2 text-primary font-weight-bold">Integrasi Surat Organisasi</h5>
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
                            <div class="text-right">
                                <button type="button" class="btn btn-primary next-tab"
                                    onclick="$('#bab1-tab').tab('show')">Lanjut ke BAB I <i
                                        class="fas fa-arrow-right"></i></button>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="bab1" role="tabpanel">
                            <h5 class="border-bottom pb-2 text-primary font-weight-bold">BAB I: Pemantapan Ideologi
                                Keaswajaan</h5>
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered" id="table-bab1">
                                    <thead class="bg-light text-center">
                                        <tr>
                                            <th>Nama Kegiatan</th>
                                            <th>Tanggal</th>
                                            <th>Tempat</th>
                                            <th>Jml Peserta</th>
                                            <th>Link Bukti / Foto</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><input type="text" name="bab1_kegiatan[]"
                                                    class="form-control form-control-sm"></td>
                                            <td><input type="date" name="bab1_tanggal[]"
                                                    class="form-control form-control-sm"></td>
                                            <td><input type="text" name="bab1_tempat[]"
                                                    class="form-control form-control-sm"></td>
                                            <td><input type="number" name="bab1_peserta[]"
                                                    class="form-control form-control-sm"></td>
                                            <td><input type="url" name="bab1_link[]"
                                                    class="form-control form-control-sm" placeholder="https://drive...">
                                            </td>
                                            <td class="text-center"><button type="button"
                                                    class="btn btn-sm btn-danger remove-row"><i
                                                        class="fas fa-trash"></i></button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" class="btn btn-sm btn-success add-row" data-table="table-bab1"
                                data-prefix="bab1"><i class="fas fa-plus"></i> Tambah Kegiatan</button>
                            <div class="text-right mt-3"><button type="button" class="btn btn-primary"
                                    onclick="$('#bab2-tab').tab('show')">Lanjut ke BAB II <i
                                        class="fas fa-arrow-right"></i></button></div>
                        </div>

                        <div class="tab-pane fade" id="bab2" role="tabpanel">
                            <h5 class="border-bottom pb-2 text-primary font-weight-bold">BAB II: Kegiatan Penguatan
                                Kaderisasi</h5>
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered" id="table-bab2">
                                    <thead class="bg-light text-center">
                                        <tr>
                                            <th>Kegiatan (Makesta, dll)</th>
                                            <th>Tanggal</th>
                                            <th>Tempat</th>
                                            <th>Narasumber/Instruktur</th>
                                            <th>Peserta</th>
                                            <th>Link Bukti</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><input type="text" name="bab2_kegiatan[]"
                                                    class="form-control form-control-sm"></td>
                                            <td><input type="date" name="bab2_tanggal[]"
                                                    class="form-control form-control-sm"></td>
                                            <td><input type="text" name="bab2_tempat[]"
                                                    class="form-control form-control-sm"></td>
                                            <td><input type="text" name="bab2_narasumber[]"
                                                    class="form-control form-control-sm"></td>
                                            <td><input type="number" name="bab2_peserta[]"
                                                    class="form-control form-control-sm"></td>
                                            <td><input type="url" name="bab2_link[]"
                                                    class="form-control form-control-sm"></td>
                                            <td class="text-center"><button type="button"
                                                    class="btn btn-sm btn-danger remove-row"><i
                                                        class="fas fa-trash"></i></button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" class="btn btn-sm btn-success add-row" data-table="table-bab2"
                                data-prefix="bab2"><i class="fas fa-plus"></i> Tambah Kegiatan</button>
                            <div class="text-right mt-3"><button type="button" class="btn btn-primary"
                                    onclick="$('#bab3-tab').tab('show')">Lanjut ke BAB III <i
                                        class="fas fa-arrow-right"></i></button></div>
                        </div>

                        <div class="tab-pane fade" id="bab3" role="tabpanel">
                            <h5 class="border-bottom pb-2 text-primary font-weight-bold">BAB III: Pendelegasian Instruktur
                                Pengkaderan</h5>
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered" id="table-bab3">
                                    <thead class="bg-light text-center">
                                        <tr>
                                            <th>Kegiatan</th>
                                            <th>Tanggal / Waktu</th>
                                            <th>Penyelenggara (PR/PK)</th>
                                            <th>Nama Instruktur Diutus</th>
                                            <th>Link Bukti/Surat Tugas</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><input type="text" name="bab3_kegiatan[]"
                                                    class="form-control form-control-sm"></td>
                                            <td><input type="date" name="bab3_tanggal[]"
                                                    class="form-control form-control-sm"></td>
                                            <td><input type="text" name="bab3_penyelenggara[]"
                                                    class="form-control form-control-sm"></td>
                                            <td><input type="text" name="bab3_instruktur[]"
                                                    class="form-control form-control-sm"></td>
                                            <td><input type="url" name="bab3_link[]"
                                                    class="form-control form-control-sm"></td>
                                            <td class="text-center"><button type="button"
                                                    class="btn btn-sm btn-danger remove-row"><i
                                                        class="fas fa-trash"></i></button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" class="btn btn-sm btn-success add-row" data-table="table-bab3"
                                data-prefix="bab3"><i class="fas fa-plus"></i> Tambah Data</button>
                            <div class="text-right mt-3"><button type="button" class="btn btn-primary"
                                    onclick="$('#bab4-tab').tab('show')">Lanjut ke BAB IV <i
                                        class="fas fa-arrow-right"></i></button></div>
                        </div>

                        <div class="tab-pane fade" id="bab4" role="tabpanel">
                            <h5 class="border-bottom pb-2 text-primary font-weight-bold">BAB IV: Merekrut Pelajar Sekolah
                                Umum Negeri</h5>
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered" id="table-bab4">
                                    <thead class="bg-light text-center">
                                        <tr>
                                            <th>Nama Pelajar</th>
                                            <th>Asal Sekolah</th>
                                            <th>No. HP</th>
                                            <th>Link Bukti / Form</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><input type="text" name="bab4_nama[]"
                                                    class="form-control form-control-sm"></td>
                                            <td><input type="text" name="bab4_sekolah[]"
                                                    class="form-control form-control-sm"></td>
                                            <td><input type="text" name="bab4_hp[]"
                                                    class="form-control form-control-sm"></td>
                                            <td><input type="url" name="bab4_link[]"
                                                    class="form-control form-control-sm"></td>
                                            <td class="text-center"><button type="button"
                                                    class="btn btn-sm btn-danger remove-row"><i
                                                        class="fas fa-trash"></i></button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" class="btn btn-sm btn-success add-row" data-table="table-bab4"
                                data-prefix="bab4"><i class="fas fa-plus"></i> Tambah Pelajar</button>
                            <div class="text-right mt-3"><button type="button" class="btn btn-primary"
                                    onclick="$('#bab5-tab').tab('show')">Lanjut ke BAB V <i
                                        class="fas fa-arrow-right"></i></button></div>
                        </div>

                        <div class="tab-pane fade" id="bab5" role="tabpanel">
                            <h5 class="border-bottom pb-2 text-primary font-weight-bold">BAB V: Melengkapi Kepengurusan
                                Ranting</h5>
                            <div class="alert alert-info small mt-3">Karena Anda adalah Ranting, bagian ini hanya
                                membutuhkan Nomor Surat Pengesahan (SP) Kepengurusan Anda saat ini beserta unggahan file
                                Berita Acara pembentukan pengurus.</div>
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>Nomor Surat Pengesahan (SP) Aktif</label>
                                    <input type="text" name="bab5_no_sp" class="form-control"
                                        placeholder="Contoh: 123/PR/A/XI/2025">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Upload Berita Acara Konferensi (PDF)</label>
                                    <input type="file" name="bab5_file_ba" class="form-control-file" accept=".pdf">
                                </div>
                            </div>
                            <div class="text-right mt-3"><button type="button" class="btn btn-primary"
                                    onclick="$('#bab6-tab').tab('show')">Lanjut ke BAB VI <i
                                        class="fas fa-arrow-right"></i></button></div>
                        </div>

                        <div class="tab-pane fade" id="bab6" role="tabpanel">
                            <h5 class="border-bottom pb-2 text-primary font-weight-bold">BAB VI: Kegiatan Sosial
                                Kemasyarakatan, Kepemudaan & Pendidikan</h5>
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered" id="table-bab6">
                                    <thead class="bg-light text-center">
                                        <tr>
                                            <th>Nama Kegiatan</th>
                                            <th>Tanggal</th>
                                            <th>Tempat</th>
                                            <th>Narasumber / Tokoh</th>
                                            <th>Jml Peserta</th>
                                            <th>Link Bukti</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><input type="text" name="bab6_kegiatan[]"
                                                    class="form-control form-control-sm"></td>
                                            <td><input type="date" name="bab6_tanggal[]"
                                                    class="form-control form-control-sm"></td>
                                            <td><input type="text" name="bab6_tempat[]"
                                                    class="form-control form-control-sm"></td>
                                            <td><input type="text" name="bab6_narasumber[]"
                                                    class="form-control form-control-sm"></td>
                                            <td><input type="number" name="bab6_peserta[]"
                                                    class="form-control form-control-sm"></td>
                                            <td><input type="url" name="bab6_link[]"
                                                    class="form-control form-control-sm"></td>
                                            <td class="text-center"><button type="button"
                                                    class="btn btn-sm btn-danger remove-row"><i
                                                        class="fas fa-trash"></i></button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" class="btn btn-sm btn-success add-row" data-table="table-bab6"
                                data-prefix="bab6"><i class="fas fa-plus"></i> Tambah Kegiatan</button>
                            <div class="text-right mt-3"><button type="button" class="btn btn-primary"
                                    onclick="$('#bab7-tab').tab('show')">Lanjut ke BAB VII <i
                                        class="fas fa-arrow-right"></i></button></div>
                        </div>

                        <div class="tab-pane fade" id="bab7" role="tabpanel">
                            <h5 class="border-bottom pb-2 text-primary font-weight-bold">BAB VII: Pengembangan CBP / KPP
                            </h5>
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered" id="table-bab7">
                                    <thead class="bg-light text-center">
                                        <tr>
                                            <th>Nama Lengkap</th>
                                            <th>Tempat, Tgl Lahir</th>
                                            <th>Alamat</th>
                                            <th>No. Telp</th>
                                            <th>Tahun Diklatama</th>
                                            <th>Link Foto</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><input type="text" name="bab7_nama[]"
                                                    class="form-control form-control-sm"></td>
                                            <td><input type="text" name="bab7_ttl[]"
                                                    class="form-control form-control-sm"></td>
                                            <td><input type="text" name="bab7_alamat[]"
                                                    class="form-control form-control-sm"></td>
                                            <td><input type="text" name="bab7_telp[]"
                                                    class="form-control form-control-sm"></td>
                                            <td><input type="number" name="bab7_tahun[]"
                                                    class="form-control form-control-sm" placeholder="2024"></td>
                                            <td><input type="url" name="bab7_link[]"
                                                    class="form-control form-control-sm"
                                                    placeholder="Link GDrive Pas Foto"></td>
                                            <td class="text-center"><button type="button"
                                                    class="btn btn-sm btn-danger remove-row"><i
                                                        class="fas fa-trash"></i></button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" class="btn btn-sm btn-success add-row mb-4" data-table="table-bab7"
                                data-prefix="bab7"><i class="fas fa-plus"></i> Tambah Anggota CBP</button>

                            <hr class="mt-5">
                            <div class="alert alert-warning text-center">
                                Pastikan Anda telah mengisi semua form di setiap BAB sebelum menekan tombol simpan. Sistem
                                akan otomatis memproses data ini menjadi laporan Akreditasi lengkap.
                            </div>
                            <div class="form-group mb-0 text-center mt-4">
                                <button type="submit" class="btn btn-primary btn-lg font-weight-bold shadow px-5">
                                    <i class="fas fa-paper-plane"></i> Ajukan Akreditasi Sekarang
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
                selector: '.tinymce-editor', // Targetkan class ini
                height: 250,
                menubar: false,
                plugins: [
                    'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                    'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                    'insertdatetime', 'media', 'table', 'help', 'wordcount'
                ],
                toolbar: 'undo redo | blocks | ' +
                    'bold italic underline | alignleft aligncenter ' +
                    'alignright alignjustify | bullist numlist outdent indent | ' +
                    'removeformat | help',
                content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size:14px }',
                branding: false, // Menghilangkan tulisan "Powered by TinyMCE"
                promotion: false // Menghilangkan tombol upgrade
            });

            // 2. Tab styling toggler
            $('.nav-link').on('click', function() {
                $('.nav-link').removeClass('text-dark font-weight-bold').addClass('text-white');
                $(this).removeClass('text-white').addClass('text-dark font-weight-bold');
            });

            // 3. Add Row Logic untuk semua BAB
            $('.add-row').click(function() {
                // ... (Kode Add Row Anda yang sebelumnya biarkan saja di sini, tidak perlu diubah) ...
                var tableId = $(this).data('table');
                var prefix = $(this).data('prefix');
                var newRow = '';

                if (prefix === 'bab1') {
                    newRow = `<tr>
                            <td><input type="text" name="${prefix}_kegiatan[]" class="form-control form-control-sm"></td>
                            <td><input type="date" name="${prefix}_tanggal[]" class="form-control form-control-sm"></td>
                            <td><input type="text" name="${prefix}_tempat[]" class="form-control form-control-sm"></td>
                            <td><input type="number" name="${prefix}_peserta[]" class="form-control form-control-sm"></td>
                            <td><input type="url" name="${prefix}_link[]" class="form-control form-control-sm"></td>
                            <td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-row"><i class="fas fa-trash"></i></button></td>
                          </tr>`;
                } else if (prefix === 'bab2' || prefix === 'bab6') {
                    newRow = `<tr>
                            <td><input type="text" name="${prefix}_kegiatan[]" class="form-control form-control-sm"></td>
                            <td><input type="date" name="${prefix}_tanggal[]" class="form-control form-control-sm"></td>
                            <td><input type="text" name="${prefix}_tempat[]" class="form-control form-control-sm"></td>
                            <td><input type="text" name="${prefix}_narasumber[]" class="form-control form-control-sm"></td>
                            <td><input type="number" name="${prefix}_peserta[]" class="form-control form-control-sm"></td>
                            <td><input type="url" name="${prefix}_link[]" class="form-control form-control-sm"></td>
                            <td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-row"><i class="fas fa-trash"></i></button></td>
                          </tr>`;
                } else if (prefix === 'bab3') {
                    newRow = `<tr>
                            <td><input type="text" name="${prefix}_kegiatan[]" class="form-control form-control-sm"></td>
                            <td><input type="date" name="${prefix}_tanggal[]" class="form-control form-control-sm"></td>
                            <td><input type="text" name="${prefix}_penyelenggara[]" class="form-control form-control-sm"></td>
                            <td><input type="text" name="${prefix}_instruktur[]" class="form-control form-control-sm"></td>
                            <td><input type="url" name="${prefix}_link[]" class="form-control form-control-sm"></td>
                            <td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-row"><i class="fas fa-trash"></i></button></td>
                          </tr>`;
                } else if (prefix === 'bab4') {
                    newRow = `<tr>
                            <td><input type="text" name="${prefix}_nama[]" class="form-control form-control-sm"></td>
                            <td><input type="text" name="${prefix}_sekolah[]" class="form-control form-control-sm"></td>
                            <td><input type="text" name="${prefix}_hp[]" class="form-control form-control-sm"></td>
                            <td><input type="url" name="${prefix}_link[]" class="form-control form-control-sm"></td>
                            <td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-row"><i class="fas fa-trash"></i></button></td>
                          </tr>`;
                } else if (prefix === 'bab7') {
                    newRow = `<tr>
                            <td><input type="text" name="${prefix}_nama[]" class="form-control form-control-sm"></td>
                            <td><input type="text" name="${prefix}_ttl[]" class="form-control form-control-sm"></td>
                            <td><input type="text" name="${prefix}_alamat[]" class="form-control form-control-sm"></td>
                            <td><input type="text" name="${prefix}_telp[]" class="form-control form-control-sm"></td>
                            <td><input type="number" name="${prefix}_tahun[]" class="form-control form-control-sm" placeholder="2024"></td>
                            <td><input type="url" name="${prefix}_link[]" class="form-control form-control-sm"></td>
                            <td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-row"><i class="fas fa-trash"></i></button></td>
                          </tr>`;
                }

                $('#' + tableId + ' tbody').append(newRow);
            });

            // Remove Row
            $(document).on('click', '.remove-row', function() {
                if ($(this).closest('tbody').find('tr').length > 1) {
                    $(this).closest('tr').remove();
                } else {
                    alert(
                        'Minimal harus ada 1 baris isian. Jika tidak ada data, kosongi saja kotaknya.');
                }
            });
        });
    </script>
@endsection
