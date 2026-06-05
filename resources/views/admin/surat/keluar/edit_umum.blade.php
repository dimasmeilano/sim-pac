@extends('layouts.adminlte')

@section('title', 'Edit Surat Umum')
@section('page-title', 'Edit Surat Umum (Bebas)')

@section('content')
    <div class="card card-warning shadow-sm">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-edit"></i> Edit Formulir Surat Keluar</h3>
        </div>

        <form action="{{ route('surat.keluar.update.umum', $suratKeluar->id) }}" method="POST" enctype="multipart/form-data">
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

                        {{-- ========================================== --}}
                        {{-- MULAI INJEKSI SAKLAR TUJUAN (MODE EDIT) --}}
                        {{-- ========================================== --}}
                        @php
                            $isInternal = !is_null($suratKeluar->tujuan_organization_id);
                        @endphp

                        <div class="form-group border-top pt-3 mt-3">
                            <label>Kategori Tujuan Pengiriman</label>
                            <div class="custom-control custom-radio mb-1">
                                <input class="custom-control-input" type="radio" id="tujuan_eksternal"
                                    name="kategori_tujuan" value="eksternal" {{ !$isInternal ? 'checked' : '' }}>
                                <label for="tujuan_eksternal" class="custom-control-label font-weight-normal">Teks Manual
                                    (SK, Surat Tugas, Eksternal)</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input class="custom-control-input" type="radio" id="tujuan_internal"
                                    name="kategori_tujuan" value="internal" {{ $isInternal ? 'checked' : '' }}>
                                <label for="tujuan_internal" class="custom-control-label font-weight-normal">Internal (Kirim
                                    ke Ranting/PAC)</label>
                            </div>
                        </div>

                        <div class="form-group" id="grup_eksternal"
                            style="display: {{ !$isInternal ? 'block' : 'none' }};">
                            <label>Tujuan Surat <span class="text-muted">(Opsional)</span></label>
                            <textarea name="tujuan_surat" id="tujuan_teks" class="form-control" rows="2"
                                placeholder="Contoh: Yth. Kepala Desa / Terlampir">{{ old('tujuan_surat', !$isInternal ? $suratKeluar->tujuan : '') }}</textarea>
                            <small class="text-muted">Bisa dikosongkan jika ini adalah SK atau Surat Tugas.</small>
                        </div>

                        <div class="form-group" id="grup_internal"
                            style="display: {{ $isInternal ? 'block' : 'none' }};">
                            <label>Pilih Ranting / PAC Tujuan <span class="text-danger">*</span></label>
                            <select name="tujuan_organization_id" id="tujuan_organization_id"
                                class="form-control select2" style="width: 100%;">
                                <option value="">-- Pilih Organisasi --</option>
                                @if (isset($organizations))
                                    @foreach ($organizations as $org)
                                        <option value="{{ $org->id }}"
                                            {{ $suratKeluar->tujuan_organization_id == $org->id ? 'selected' : '' }}>
                                            {{ $org->nama ?? $org->name }}
                                            ({{ strtoupper($org->type ?? $org->jenis_organisasi) }})
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            <small class="text-success d-block mt-1"><i class="fas fa-info-circle"></i> Tembus otomatis ke
                                dasbor penerima saat disahkan.</small>
                        </div>
                        {{-- ========================================== --}}

                        <div class="form-group border-top pt-3 mt-3">
                            <label>Tanggal Surat <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_surat" class="form-control"
                                value="{{ old('tanggal_surat', \Carbon\Carbon::parse($suratKeluar->tanggal_surat)->format('Y-m-d')) }}"
                                required>
                        </div>

                        <div class="form-group">
                            <label for="file_lampiran">File Lampiran <span class="text-muted">(Ganti jika
                                    perlu)</span></label>
                            <input type="file" name="file_lampiran" class="form-control-file"
                                accept=".pdf,.jpg,.jpeg,.png">
                            @if ($suratKeluar->file_lampiran)
                                <small class="text-info d-block mt-1"><i class="fas fa-check"></i> File lama sudah
                                    terlampir. Biarkan kosong jika tidak ingin mengubah.</small>
                            @endif
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

            if (selectPenerbit) selectPenerbit.addEventListener('change', () => updateFormDisplay(false));
            if (indeksSurat) indeksSurat.addEventListener('change', updateNomorOtomatis);

            // Inisialisasi awal form penerbit
            updateFormDisplay(true);

            // -- SAKLAR TUJUAN INTERNAL/EKSTERNAL --
            const radioEksternal = document.getElementById('tujuan_eksternal');
            const radioInternal = document.getElementById('tujuan_internal');
            const grupEksternal = document.getElementById('grup_eksternal');
            const grupInternal = document.getElementById('grup_internal');
            const inputTeks = document.getElementById('tujuan_teks');
            const inputOrg = document.getElementById('tujuan_organization_id');

            function toggleTujuan() {
                if (radioInternal.checked) {
                    if (grupInternal) grupInternal.style.display = 'block';
                    if (grupEksternal) grupEksternal.style.display = 'none';
                    if (inputOrg) inputOrg.setAttribute('required', 'required');
                    if (inputTeks) inputTeks.removeAttribute('required');
                } else {
                    if (grupInternal) grupInternal.style.display = 'none';
                    if (grupEksternal) grupEksternal.style.display = 'block';
                    if (inputTeks) inputTeks.removeAttribute('required');
                    if (inputOrg) inputOrg.removeAttribute('required');
                }
            }

            if (radioEksternal) radioEksternal.addEventListener('change', toggleTujuan);
            if (radioInternal) radioInternal.addEventListener('change', toggleTujuan);
            toggleTujuan(); // Eksekusi saat diload
        });
    </script>
@endpush
