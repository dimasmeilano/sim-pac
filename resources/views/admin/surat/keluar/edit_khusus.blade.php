@extends('layouts.adminlte')

@section('title', 'Edit Surat: ' . ($template->nama ?? 'Khusus'))
@section('page-title', 'Edit Surat: ' . ($template->nama ?? 'Khusus'))

@section('content')
    <div class="card card-primary shadow-sm">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-edit"></i> Edit Form Isian Surat</h3>
        </div>

        <form action="{{ route('surat.keluar.update.khusus', $suratKeluar->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card-body">
                <div class="row">
                    <!-- KOLOM KIRI: Data Basic -->
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
                    </div>

                    <!-- KOLOM KANAN: Form Dinamis Berdasarkan Template -->
                    <div class="col-md-7 pl-4">
                        <h5 class="text-primary font-weight-bold mb-3 border-bottom pb-2">Isian Form
                            ({{ $template->nama ?? 'Dinamis' }})</h5>

                        @if ($template && $template->fields)
                            @foreach ($template->fields as $field => $type)
                                <!-- Jangan tampilkan form untuk tipe hidden -->
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

                                        <!-- [BARU] LOGIKA UNTUK DROPDOWN -->
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
