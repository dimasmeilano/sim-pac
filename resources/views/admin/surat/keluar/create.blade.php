@extends('layouts.adminlte')

@section('title', 'Buat Surat Umum')
@section('page-title', 'Buat Surat Umum (Bebas)')

@section('content')
    <div class="card card-success shadow-sm">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-envelope-open-text"></i> Formulir Surat Keluar Umum</h3>
        </div>

        <form action="{{ route('surat.keluar.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="row">

                    <div class="col-md-8 border-right">
                        <div class="form-group">
                            <label class="text-success font-weight-bold"><i class="fas fa-edit"></i> Lembar Isi Surat <span
                                    class="text-danger">*</span></label>
                            <textarea name="isi_surat_bebas" id="tinyMceEditor" class="form-control" rows="22"></textarea>
                            <small class="text-muted mt-2 d-block">
                                <i class="fas fa-info-circle"></i> Kop Surat, Header, dan Area Tanda Tangan akan disatukan
                                otomatis oleh sistem.
                            </small>
                        </div>
                    </div>

                    <div class="col-md-4 pl-4">
                        <h5 class="text-secondary font-weight-bold mb-3 border-bottom pb-2"><i class="fas fa-sliders-h"></i>
                            Pengaturan Surat</h5>

                        <div class="form-group">
                            <label>Penerbit Surat <span class="text-danger">*</span></label>
                            <select name="penerbit_surat" id="penerbit_surat" class="form-control"
                                onchange="updateFormDisplay()" required>
                                <option value="mandiri">Pimpinan (Mandiri)</option>
                                <option value="bersama">Surat Bersama (IPNU & IPPNU)</option>
                                <option value="panitia">Panitia Pelaksana</option>
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
                                            placeholder="Contoh: MAKESTA RAYA TAHUN 2026">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-0">
                                        <label>Nama Ketua Panitia</label>
                                        <input type="text" name="nama_ketua_panitia" class="form-control"
                                            placeholder="Contoh: Ahmad Dhani">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-0">
                                        <label>Nama Sekretaris Panitia</label>
                                        <input type="text" name="nama_sekretaris_panitia" class="form-control"
                                            placeholder="Contoh: Maia Estianty">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Klasifikasi Surat <span class="text-danger">*</span></label>
                            <select name="klasifikasi_surat" id="klasifikasi_surat" class="form-control" required>
                                <option value="A">A - Surat untuk lingkungan internal</option>
                                <option value="B">B - Surat untuk pihak eksternal</option>
                                <option value="C">C - Surat untuk NU, Banom, lembaga</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Nomor Surat</label>
                            <input type="text" name="nomor_surat" id="nomorSuratInput"
                                class="form-control bg-light font-weight-bold" value="{{ $nomorSuratOtomatis ?? '' }}"
                                readonly>
                        </div>

                        <div class="form-group">
                            <label>Sifat / Lampiran</label>
                            <input type="text" name="lampiran" class="form-control" value="-">
                        </div>

                        <div class="form-group">
                            <label>Perihal <span class="text-danger">*</span></label>
                            <input type="text" name="perihal" class="form-control" required placeholder="Undangan Rapat">
                        </div>

                        {{-- ========================================== --}}
                        {{-- MULAI INJEKSI SAKLAR TUJUAN INTERNAL/EKSTERNAL --}}
                        {{-- ========================================== --}}
                        <div class="form-group border-top pt-3 mt-3">
                            <label>Kategori Tujuan <span class="text-danger">*</span></label>
                            <div class="custom-control custom-radio mb-1">
                                <input class="custom-control-input" type="radio" id="tujuan_eksternal"
                                    name="kategori_tujuan" value="eksternal" checked>
                                <label for="tujuan_eksternal" class="custom-control-label font-weight-normal">Eksternal
                                    (Teks Manual)</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input class="custom-control-input" type="radio" id="tujuan_internal"
                                    name="kategori_tujuan" value="internal">
                                <label for="tujuan_internal" class="custom-control-label font-weight-normal">Internal (Kirim
                                    ke Ranting/PAC)</label>
                            </div>
                        </div>

                        <div class="form-group" id="grup_eksternal">
                            <label>Tujuan Surat <span class="text-danger">*</span></label>
                            <textarea name="tujuan_surat" id="tujuan_teks" class="form-control" rows="3" required
                                placeholder="Yth. Ketua ..."></textarea>
                        </div>

                        <div class="form-group" id="grup_internal" style="display: none;">
                            <label>Pilih Ranting / PAC Tujuan <span class="text-danger">*</span></label>
                            <select name="tujuan_organization_id" id="tujuan_organization_id" class="form-control">
                                <option value="">-- Pilih Organisasi --</option>
                                @if (isset($organizations))
                                    @foreach ($organizations as $org)
                                        <option value="{{ $org->id }}">{{ $org->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <small class="text-success d-block mt-1"><i class="fas fa-info-circle"></i> Tembus otomatis ke
                                dasbor penerima.</small>
                        </div>
                        {{-- ========================================== --}}

                        <div class="form-group border-top pt-3 mt-3">
                            <label>Tanggal Surat <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_surat" class="form-control" value="{{ date('Y-m-d') }}"
                                required>
                        </div>
                    </div>

                </div>
            </div>

            <div class="card-footer bg-light text-right">
                <a href="{{ route('surat.keluar.index') }}" class="btn btn-secondary mr-2">Batal</a>
                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Simpan Sebagai Draft</button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.tiny.cloud/1/790oy87uninpb887jjodfajw2ivcmbi6dq4vzah5gguz6igm/tinymce/6/tinymce.min.js"
        referrerpolicy="origin"></script>
    <script>
        function updateFormDisplay() {
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
            updateNomorOtomatis();
        }

        function updateNomorOtomatis() {
            const selectPenerbit = document.getElementById('penerbit_surat');
            const indeksSurat = document.getElementById('klasifikasi_surat');
            const inputNomor = document.getElementById('nomorSuratInput');

            if (!indeksSurat || !selectPenerbit || !inputNomor) return;

            let kodeIndeks = indeksSurat.value;
            let penerbit = selectPenerbit.value;
            inputNomor.value = "Memuat nomor...";

            fetch(`{{ route('surat.keluar.nomor-otomatis') }}?kode_indeks=${kodeIndeks}&penerbit=${penerbit}`)
                .then(response => {
                    if (!response.ok) throw new Error('Server merespons dengan error ' + response.status);
                    return response.json();
                })
                .then(data => {
                    if (data.nomor_surat) {
                        inputNomor.value = data.nomor_surat;
                    } else {
                        inputNomor.value = "Gagal memuat nomor";
                    }
                })
                .catch(error => {
                    console.error('Error AJAX Nomor:', error);
                    inputNomor.value = "Terjadi Error Backend (Cek F12)";
                });
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Inisialisasi TinyMCE
            tinymce.init({
                selector: '#tinyMceEditor',
                height: 650,
                menubar: false,
                plugins: 'lists link image table code help wordcount',
                toolbar: 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright justify | bullist numlist | table | removeformat',
                content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; text-align: justify; line-height: 1.6; }'
            });

            // Listener Nomor Surat
            const selectPenerbit = document.getElementById('penerbit_surat');
            const indeksSurat = document.getElementById('klasifikasi_surat');
            if (selectPenerbit) selectPenerbit.addEventListener('change', updateFormDisplay);
            if (indeksSurat) indeksSurat.addEventListener('change', updateNomorOtomatis);
            updateFormDisplay();

            // SAKLAR TUJUAN INTERNAL / EKSTERNAL
            const radioEksternal = document.getElementById('tujuan_eksternal');
            const radioInternal = document.getElementById('tujuan_internal');
            const grupEksternal = document.getElementById('grup_eksternal');
            const grupInternal = document.getElementById('grup_internal');
            const inputTeks = document.getElementById('tujuan_teks');
            const inputOrg = document.getElementById('tujuan_organization_id');

            function toggleTujuan() {
                if (radioInternal.checked) {
                    grupInternal.style.display = 'block';
                    grupEksternal.style.display = 'none';
                    inputOrg.setAttribute('required', 'required');
                    inputTeks.removeAttribute('required');
                } else {
                    grupInternal.style.display = 'none';
                    grupEksternal.style.display = 'block';
                    inputTeks.setAttribute('required', 'required');
                    inputOrg.removeAttribute('required');
                }
            }

            if (radioEksternal) radioEksternal.addEventListener('change', toggleTujuan);
            if (radioInternal) radioInternal.addEventListener('change', toggleTujuan);
            toggleTujuan(); // Eksekusi saat load
        });
    </script>
@endpush
