@extends('layouts.adminlte')

@section('title', 'Edit Surat Umum')
@section('page-title', 'Edit Surat Umum (Bebas)')

@section('content')
    <div class="card card-warning shadow-sm">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-edit"></i> Edit Formulir Surat Keluar</h3>
        </div>

        <form action="{{ route('surat.keluar.update.umum', $suratKeluar->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card-body">
                <div class="row">

                    <div class="col-md-8 border-right">
                        <div class="form-group">
                            @php
                                // 1. Kita tarik history JSON dari database
                                $dataHistory = is_string($suratKeluar->data_surat)
                                    ? json_decode($suratKeluar->data_surat, true)
                                    : $suratKeluar->data_surat ?? [];

                                // 2. Kita ambil KHUSUS teks paragraf bebasnya saja
                                $isiTeksBebasLama = $dataHistory['isi_teks_bebas'] ?? '';
                            @endphp

                            <div class="form-group">
                                <label>Lembar Isi Surat <span class="text-danger">*</span></label>

                                <textarea name="isi_surat_bebas" class="form-control" id="tinyMceEditor" rows="15"> {{ old('isi_surat_bebas', $isiTeksBebasLama) }}</textarea>
                            </div>
                            <small class="text-muted mt-2 d-block">
                                <i class="fas fa-info-circle"></i> Kop Surat, Header, dan Area Tanda Tangan akan disatukan
                                otomatis oleh sistem saat disimpan.
                            </small>
                        </div>
                    </div>

                    <div class="col-md-4 pl-4">
                        <h5 class="text-secondary font-weight-bold mb-3 border-bottom pb-2"><i class="fas fa-sliders-h"></i>
                            Pengaturan Surat</h5>

                        <div class="form-group">
                            <label>Penerbit Surat <span class="text-danger">*</span></label>
                            <select name="penerbit_surat" id="penerbit_surat" class="form-control" required>
                                <option value="mandiri" {{ $suratKeluar->penerbit_surat == 'mandiri' ? 'selected' : '' }}>
                                    Pimpinan (Mandiri)</option>
                                <option value="bersama" {{ $suratKeluar->penerbit_surat == 'bersama' ? 'selected' : '' }}>
                                    Surat Bersama (IPNU & IPPNU)</option>
                                <option value="panitia" {{ $suratKeluar->penerbit_surat == 'panitia' ? 'selected' : '' }}>
                                    Panitia Pelaksana</option>
                            </select>
                        </div>

                        <div id="form-panitia" style="display: none;" class="p-3 mb-3 bg-light border rounded">
                            <h6 class="font-weight-bold text-info"><i class="fas fa-users-cog"></i> Detail Panitia Pelaksana
                            </h6>
                            <div class="row mt-2">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Nama Kegiatan (Opsional)</label>
                                        <input type="text" name="nama_kegiatan_panitia" class="form-control"
                                            placeholder="Tulis ulang jika diubah...">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-0">
                                        <label>Nama Ketua Panitia</label>
                                        <input type="text" name="nama_ketua_panitia" class="form-control"
                                            placeholder="Ketik nama...">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-0">
                                        <label>Nama Sekretaris Panitia</label>
                                        <input type="text" name="nama_sekretaris_panitia" class="form-control"
                                            placeholder="Ketik nama...">
                                    </div>
                                </div>
                            </div>
                            <small class="text-muted"><i class="fas fa-info-circle"></i> Catatan: Karena alasan keamanan
                                TTE, nama panitia harus diketik ulang jika Anda mengubahnya.</small>
                        </div>

                        @php
                            $parts = explode('/', $suratKeluar->nomor_surat);
                            $klasifikasiLama = $parts[2] ?? 'A';
                        @endphp

                        <div class="form-group">
                            <label>Klasifikasi Surat <span class="text-danger">*</span></label>
                            <select name="klasifikasi_surat" id="klasifikasi_surat" class="form-control" required>
                                <option value="A" {{ $klasifikasiLama == 'A' ? 'selected' : '' }}>A - Surat untuk
                                    lingkungan internal</option>
                                <option value="B" {{ $klasifikasiLama == 'B' ? 'selected' : '' }}>B - Surat untuk pihak
                                    eksternal</option>
                                <option value="C" {{ $klasifikasiLama == 'C' ? 'selected' : '' }}>C - Surat untuk NU,
                                    Banom, lembaga</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Nomor Surat</label>
                            <input type="text" name="nomor_surat" id="nomorSuratInput"
                                class="form-control bg-light font-weight-bold"
                                value="{{ old('nomor_surat', $suratKeluar->nomor_surat) }}" readonly>
                        </div>

                        <div class="form-group">
                            <label>Sifat / Lampiran</label>
                            <input type="text" name="lampiran" class="form-control" value="{{ old('lampiran', '-') }}">
                        </div>

                        <div class="form-group">
                            <label>Perihal <span class="text-danger">*</span></label>
                            <input type="text" name="perihal" class="form-control" required
                                value="{{ old('perihal', $suratKeluar->perihal) }}">
                        </div>

                        <div class="form-group">
                            <label>Tujuan Surat <span class="text-danger">*</span></label>
                            <textarea name="tujuan_surat" class="form-control" rows="3" required>{{ old('tujuan_surat', $suratKeluar->tujuan) }}</textarea>
                        </div>

                        <div class="form-group">
                            <label>Tanggal Surat <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_surat" class="form-control"
                                value="{{ old('tanggal_surat', \Carbon\Carbon::parse($suratKeluar->tanggal_surat)->format('Y-m-d')) }}"
                                required>
                        </div>
                    </div>

                </div>
            </div>

            <div class="card-footer bg-light text-right">
                <a href="{{ route('surat.keluar.show', $suratKeluar->id) }}" class="btn btn-secondary mr-2">Batal</a>
                <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Perbarui Surat</button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.tiny.cloud/1/790oy87uninpb887jjodfajw2ivcmbi6dq4vzah5gguz6igm/tinymce/6/tinymce.min.js"
        referrerpolicy="origin"></script>
    <script>
        function updateFormDisplay(isInitialLoad = false) {
            const selectPenerbit = document.getElementById('penerbit_surat');
            const formPanitia = document.getElementById('form-panitia');

            if (!selectPenerbit) return;

            if (selectPenerbit.value === 'panitia') {
                if (formPanitia) formPanitia.style.display = 'block';
            } else {
                if (formPanitia) formPanitia.style.display = 'none';

                let inputKegiatan = document.querySelector('input[name="nama_kegiatan_panitia"]');
                let inputKetua = document.querySelector('input[name="nama_ketua_panitia"]');
                let inputSekretaris = document.querySelector('input[name="nama_sekretaris_panitia"]');
                if (inputKegiatan) inputKegiatan.value = '';
                if (inputKetua) inputKetua.value = '';
                if (inputSekretaris) inputSekretaris.value = '';
            }

            // PENTING UNTUK EDIT:
            // Jangan memanggil AJAX nomor otomatis saat halaman pertama kali dimuat. 
            // Biarkan nomor yang sudah ada (dari database) tetap utuh.
            if (!isInitialLoad) {
                updateNomorOtomatis();
            }
        }

        function updateNomorOtomatis() {
            const selectPenerbit = document.getElementById('penerbit_surat');
            const indeksSurat = document.getElementById('klasifikasi_surat');
            const inputNomor = document.getElementById('nomorSuratInput');

            if (!indeksSurat || !selectPenerbit || !inputNomor) return;

            let kodeIndeks = indeksSurat.value;
            let penerbit = selectPenerbit.value;

            inputNomor.value = "Menghitung...";

            fetch(`{{ route('surat.keluar.nomor-otomatis') }}?kode_indeks=${kodeIndeks}&penerbit=${penerbit}`)
                .then(response => response.json())
                .then(data => {
                    inputNomor.value = data.nomor_surat;
                })
                .catch(error => {
                    console.error('Error AJAX Nomor:', error);
                    inputNomor.value = "Gagal memuat nomor";
                });
        }

        document.addEventListener('DOMContentLoaded', function() {
            tinymce.init({
                selector: '#tinyMceEditor',
                height: 650,
                menubar: false,
                plugins: 'lists link image table code help wordcount',
                toolbar: 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright justify | bullist numlist | table | removeformat',
                content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; text-align: justify; line-height: 1.6; }'
            });

            const selectPenerbit = document.getElementById('penerbit_surat');
            const indeksSurat = document.getElementById('klasifikasi_surat');

            // Jika user sengaja mengganti penerbit/klasifikasi, baru trigger (isInitialLoad = false)
            if (selectPenerbit) selectPenerbit.addEventListener('change', () => updateFormDisplay(false));
            if (indeksSurat) indeksSurat.addEventListener('change', updateNomorOtomatis);

            // Saat halaman baru dibuka (Edit mode), set isInitialLoad = true agar nomor tak berubah
            updateFormDisplay(true);
        });
    </script>
@endpush
