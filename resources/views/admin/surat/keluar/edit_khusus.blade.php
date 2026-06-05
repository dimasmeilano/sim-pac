@extends('layouts.adminlte')

@section('title', 'Edit Surat: ' . ($template->nama ?? 'Khusus'))
@section('page-title', 'Edit Surat: ' . ($template->nama ?? 'Khusus'))

@section('content')
    <div class="card card-primary shadow-sm">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-edit"></i> Edit Form Isian Surat</h3>
        </div>

        <form action="{{ route('surat.keluar.update.khusus', $suratKeluar->id) }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="card-body">
                <div class="row">
                    <div class="col-md-5 border-right">
                        <h5 class="text-secondary font-weight-bold mb-3 border-bottom pb-2">Informasi Dasar</h5>

                        <div class="form-group">
                            <label>Nomor Surat <span class="text-danger">*</span></label>
                            <input type="text" name="nomor_surat" class="form-control"
                                value="{{ old('nomor_surat', $suratKeluar->nomor_surat) }}" required>
                        </div>

                        <div class="form-group">
                            <label>Perihal <span class="text-danger">*</span></label>
                            <input type="text" name="perihal" class="form-control"
                                value="{{ old('perihal', $suratKeluar->perihal) }}" required>
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
                                    otomatis ke Dasbor)</label>
                            </div>
                        </div>

                        <div class="form-group" id="grup_eksternal" style="display: {{ !$isInternal ? 'block' : 'none' }};">
                            <label>Tujuan Surat <span class="text-muted">(Opsional)</span></label>
                            <textarea name="tujuan" id="tujuan_teks" class="form-control" rows="2"
                                placeholder="Contoh: Yth. Kepala Desa / Terlampir">{{ old('tujuan', !$isInternal ? $suratKeluar->tujuan : '') }}</textarea>
                            <small class="text-muted">Kosongkan jika ini adalah SK atau Surat Tugas.</small>
                        </div>

                        <div class="form-group" id="grup_internal" style="display: {{ $isInternal ? 'block' : 'none' }};">
                            <label>Pilih Organisasi Tujuan <span class="text-danger">*</span></label>
                            <select name="tujuan_organization_id" id="tujuan_organization_id" class="form-control select2"
                                style="width: 100%;">
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
                            <small class="text-success d-block mt-1"><i class="fas fa-info-circle"></i> Tembus otomatis saat
                                disahkan.</small>
                        </div>

                        <div class="form-group border-top pt-3 mt-3">
                            <label for="file_lampiran">File Lampiran <span class="text-muted">(Ganti jika
                                    perlu)</span></label>
                            <input type="file" name="file_lampiran" class="form-control-file"
                                accept=".pdf,.jpg,.jpeg,.png">
                            @if ($suratKeluar->file_lampiran)
                                <small class="text-info d-block mt-1"><i class="fas fa-check"></i> File lama sudah
                                    terlampir. Biarkan kosong jika tidak ingin mengubah.</small>
                            @endif
                        </div>
                        {{-- ========================================== --}}

                    </div>

                    <div class="col-md-7 pl-4">
                        <h5 class="text-primary font-weight-bold mb-3 border-bottom pb-2">Isian Form
                            ({{ $template->nama ?? 'Dinamis' }})</h5>

                        @if ($template && $template->fields)
                            @foreach ($template->fields as $field => $type)
                                @if ($type == 'hidden')
                                    @continue
                                @endif

                                @php
                                    // Rapikan nama field. Misal: 'nama_diberi_tugas' jadi 'Nama Diberi Tugas'
                                    $label = ucwords(str_replace('_', ' ', $field));

                                    // Ambil nilai lama yang disimpan di JSON
                                    $nilaiLama = $suratKeluar->data_surat[$field] ?? '';
                                @endphp

                                <div class="form-group">
                                    <label>{{ $label }}</label>

                                    @if ($type == 'textarea')
                                        <textarea name="fields[{{ $field }}]" class="form-control" rows="3">{{ old('fields.' . $field, $nilaiLama) }}</textarea>
                                    @elseif($type == 'date')
                                        <input type="date" name="fields[{{ $field }}]" class="form-control"
                                            value="{{ old('fields.' . $field, $nilaiLama) }}">
                                    @elseif(str_starts_with($type, 'select:'))
                                        @php
                                            // Memotong teks 'select:' dan memecah sisanya berdasarkan koma
                                            $optionsString = substr($type, 7);
                                            $opsiArray = explode(',', $optionsString);
                                        @endphp
                                        <select name="fields[{{ $field }}]" class="form-control">
                                            <option value="">-- Pilih {{ $label }} --</option>
                                            @foreach ($opsiArray as $opsi)
                                                @php $opsi = trim($opsi); @endphp
                                                <option value="{{ $opsi }}"
                                                    {{ old('fields.' . $field, $nilaiLama) == $opsi ? 'selected' : '' }}>
                                                    {{ $opsi }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input type="text" name="fields[{{ $field }}]" class="form-control"
                                            value="{{ old('fields.' . $field, $nilaiLama) }}">
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i> Data variabel template tidak ditemukan.
                            </div>
                        @endif

                    </div>
                </div>
            </div>

            <div class="card-footer bg-light text-right">
                <a href="{{ route('surat.keluar.show', $suratKeluar->id) }}" class="btn btn-secondary mr-2">Batal</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Perbarui Surat</button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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

            // Panggil fungsi saat halaman diload agar menyesuaikan status
            toggleTujuan();
        });
    </script>
@endpush
