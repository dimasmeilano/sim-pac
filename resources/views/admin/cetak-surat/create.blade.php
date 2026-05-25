@extends('layouts.adminlte')

@section('title', 'Buat Surat - ' . $template->nama)
@section('page-title', 'Buat Surat: ' . $template->nama)

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Form Pengisian Surat</h3>
            <div class="card-tools">
                <span class="badge badge-info">{{ $template->kode }}</span>
                @if ($template->lampiran)
                    <span class="badge badge-warning">Lampiran: {{ $template->lampiran }}</span>
                @endif
            </div>
        </div>
        <form id="formSurat" action="{{ route('cetak-surat.store', $template->id) }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Template:</strong> {{ $template->nama }}<br>
                    <strong>Kode Surat:</strong> {{ $template->kode }}
                </div>

                <div class="alert alert-secondary">
                    <strong>Informasi Organisasi:</strong><br>
                    {{ $organisasi->tingkat_text }}<br>
                    Ikatan Pelajar Nahdlatul Ulama<br>
                    {{ $organisasi->jenis_organisasi == 'ipnu' ? 'IPNU' : ($organisasi->jenis_organisasi == 'ippnu' ? 'IPPNU' : 'IPNU - IPPNU') }}<br>
                    <strong>{{ ucwords(strtolower($organisasi->nama_wilayah)) }}</strong><br>
                    <strong class="text-primary">Nama Lengkap:
                        {{ $defaultData['nama_organisasi_lengkap'] ?? $organisasi->nama_organisasi_lengkap }}</strong><br>
                    {{ $organisasi->alamat ?? '-' }}<br>
                    Email: {{ $organisasi->email ?? '-' }}
                </div>
                <input type="hidden" name="fields[kop_organisasi]" value="{{ $defaultData['kop_organisasi'] ?? '' }}">
                <input type="hidden" name="fields[nama_organisasi_lengkap]"
                    value="{{ $defaultData['nama_organisasi_lengkap'] ?? '' }}">
                <input type="hidden" name="fields[pembuka_surat]" value="{{ $defaultData['pembuka_surat'] ?? '' }}">
                <input type="hidden" name="fields[tanggal_masehi]" value="{{ date('Y-m-d') }}">
                <input type="hidden" name="fields[tanggal_hijriah]" value="{{ $defaultData['tanggal_hijriah'] ?? '' }}">

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Organisasi</label>
                            <input type="text" class="form-control"
                                value="{{ $organisasi->nama_lengkap ?? $organisasi->name }}" readonly disabled>
                            <input type="hidden" name="organisasi"
                                value="{{ $organisasi->jenis_organisasi ?? 'bersama' }}">
                            <input type="hidden" name="tingkat" value="{{ $organisasi->type ?? 'pac' }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Tingkat Kepengurusan</label>
                            <input type="text" class="form-control" value="{{ strtoupper($organisasi->type ?? 'PAC') }}"
                                readonly disabled>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Periode</label>
                            <input type="text" class="form-control" value="{{ $organisasi->periode ?? 'XVI' }}" readonly
                                disabled>
                            <input type="hidden" name="periode" value="{{ $organisasi->periode ?? 'XVI' }}">
                        </div>
                    </div>
                </div>

                @if (auth()->user()->hasRole('super_admin') && !empty($organizations))
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Pilih Organisasi (Override)</label>
                                <select name="organization_override" id="organization_override" class="form-control">
                                    <option value="">- Gunakan organisasi user -</option>
                                    @foreach ($organizations as $org)
                                        <option value="{{ $org->id }}" data-jenis="{{ $org->jenis_organisasi }}"
                                            data-type="{{ $org->type }}" data-periode="{{ $org->periode ?? 'XVI' }}">
                                            {{ $org->name }} ({{ $org->jenis_organisasi_text }} -
                                            {{ strtoupper($org->type) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Nomor Surat</label>
                            <div class="input-group">
                                <input type="text" name="nomor_surat" id="nomor_surat" class="form-control"
                                    value="{{ old('nomor_surat') }}"
                                    placeholder="Klik tombol Generate untuk membuat nomor">
                                <div class="input-group-append">
                                    <button type="button" id="generate_nomor" class="btn btn-primary">
                                        <i class="fas fa-sync-alt"></i> Generate
                                    </button>
                                </div>
                            </div>
                            <small class="text-muted">Format: KODE/NO/BULAN/TAHUN</small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    @php $fields = $template->fields ?? []; @endphp
                    @foreach ($fields as $field => $type)
                        @if ($type == 'hidden')
                            <input type="hidden" name="fields[{{ $field }}]"
                                value="{{ $defaultData[$field] ?? '' }}">
                        @else
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ ucfirst(str_replace('_', ' ', $field)) }}</label>

                                    @if ($field == 'status_desa')
                                        <select name="fields[{{ $field }}]" class="form-control">
                                            <option value="DESA"
                                                {{ old("fields.$field", $defaultData['status_desa'] ?? 'DESA') == 'DESA' ? 'selected' : '' }}>
                                                DESA
                                            </option>
                                            <option value="KELURAHAN"
                                                {{ old("fields.$field", $defaultData['status_desa'] ?? 'DESA') == 'KELURAHAN' ? 'selected' : '' }}>
                                                KELURAHAN
                                            </option>
                                        </select>
                                    @elseif($field == 'nama_desa')
                                        <input type="text" name="fields[{{ $field }}]" class="form-control"
                                            value="{{ old("fields.$field", $defaultData['nama_desa'] ?? '') }}"
                                            placeholder="Contoh: SUKOREJO (Huruf besar semua)">
                                    @elseif($field == 'nama_desa_lower')
                                        <input type="text" name="fields[{{ $field }}]" class="form-control"
                                            value="{{ old("fields.$field", $defaultData['nama_desa_lower'] ?? '') }}"
                                            placeholder="Contoh: Sukorejo (Huruf kapital awal)">
                                    @elseif($field == 'masa_bhakti')
                                        <input type="text" name="fields[{{ $field }}]" class="form-control"
                                            value="{{ old("fields.$field", $defaultData['masa_bhakti'] ?? '') }}"
                                            placeholder="Contoh: 2026-2028">
                                    @elseif($field == 'surat_ranting_nomor')
                                        <input type="text" name="fields[{{ $field }}]" class="form-control"
                                            value="{{ old("fields.$field") }}" placeholder="Contoh: 001/PR/...">
                                    @elseif($field == 'surat_ranting_tanggal')
                                        <input type="date" name="fields[{{ $field }}]" class="form-control"
                                            value="{{ old("fields.$field", date('Y-m-d')) }}">
                                    @elseif($field == 'surat_prnu_nomor')
                                        <input type="text" name="fields[{{ $field }}]" class="form-control"
                                            value="{{ old("fields.$field") }}" placeholder="Contoh: 01/PRNU/...">
                                    @elseif($field == 'nama_ketua')
                                        <input type="text" name="fields[{{ $field }}]" class="form-control"
                                            value="{{ old("fields.$field", $defaultData['nama_ketua'] ?? '') }}" readonly>
                                    @elseif($field == 'nia_ketua')
                                        <input type="text" name="fields[{{ $field }}]" class="form-control"
                                            value="{{ old("fields.$field", $defaultData['nia_ketua'] ?? '') }}" readonly>
                                    @elseif($field == 'nama_sekretaris')
                                        <input type="text" name="fields[{{ $field }}]" class="form-control"
                                            value="{{ old("fields.$field", $defaultData['nama_sekretaris'] ?? '') }}"
                                            readonly>
                                    @elseif($field == 'nia_sekretaris')
                                        <input type="text" name="fields[{{ $field }}]" class="form-control"
                                            value="{{ old("fields.$field", $defaultData['nia_sekretaris'] ?? '') }}"
                                            readonly>
                                    @elseif($field == 'jenis_kelamin')
                                        <select name="fields[{{ $field }}]" class="form-control">
                                            <option value="Laki-laki">Laki-laki</option>
                                            <option value="Perempuan">Perempuan</option>
                                        </select>
                                    @elseif(in_array($field, ['tanggal_lahir', 'tanggal_penetapan', 'tanggal_pernyataan', 'tanggal_surat']))
                                        <input type="date" name="fields[{{ $field }}]" class="form-control"
                                            value="{{ old("fields.$field", date('Y-m-d')) }}">
                                    @else
                                        <textarea name="fields[{{ $field }}]" class="form-control" rows="3"
                                            placeholder="Isi {{ str_replace('_', ' ', $field) }}">{{ old("fields.$field") }}</textarea>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                @if ($template->has_attachment)
                    <div class="row border-top pt-3 mt-3">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="text-danger"><i class="fas fa-paperclip"></i> Lampiran Susunan
                                    Pengurus</label>
                                <div class="custom-file">
                                    <input type="file" name="file_lampiran" class="custom-file-input"
                                        id="fileLampiran" accept=".pdf,.jpg,.jpeg,.png">
                                    <label class="custom-file-label" for="fileLampiran">Pilih file (PDF/JPG/PNG)</label>
                                </div>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i>
                                    Upload berkas susunan pengurus dari tingkatan ranting (format PDF/JPG/PNG). Berkas akan
                                    otomatis digabungkan di halaman belakang surat utama.
                                </small>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
            <div class="card-footer">
                <button type="button" id="previewBtn" class="btn btn-info">
                    <i class="fas fa-eye"></i> Preview Surat
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan & Cetak
                </button>
                <a href="{{ route('cetak-surat.index') }}" class="btn btn-default">Batal</a>
            </div>
        </form>
    </div>

    <div id="preview_konten" style="display:none;"></div>

    @push('scripts')
        <script>
            // Pastikan meta tag CSRF ada di layout.adminlte Anda!
            // <meta name="csrf-token" content="{{ csrf_token() }}">

            const templateKonten = `{!! addslashes($template->konten ?? '') !!}`;
            const fields = @json(array_keys($template->fields ?? []));

            // -- GENERATE NOMOR --
            document.getElementById('generate_nomor').addEventListener('click', function() {
                let btn = this;
                let originalHtml = btn.innerHTML;
                let jenisSurat = '{{ $template->kode }}';
                let orgId = document.getElementById('organization_override')?.value || '';

                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                btn.disabled = true;

                fetch('{{ route('cetak-surat.generate-nomor') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            jenis_surat: jenisSurat,
                            organization_id: orgId
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            document.getElementById('nomor_surat').value = data.nomor;
                            updatePreview();
                        } else {
                            alert('Gagal generate nomor: ' + data.message);
                        }
                    })
                    .finally(() => {
                        btn.innerHTML = originalHtml;
                        btn.disabled = false;
                    });
            });

            // -- PREVIEW --
            document.getElementById('previewBtn').addEventListener('click', function() {
                let btn = this;
                let originalHtml = btn.innerHTML;

                // ==========================================
                // UBAH BARIS INI: Ambil berdasarkan ID, bukan sembarang form
                let formElement = document.getElementById('formSurat');
                let formData = new FormData(formElement);
                // ==========================================

                formData.append('template_id', '{{ $template->id }}');

                // Cek lagi di console, pastikan field-nya sekarang muncul!
                for (let pair of formData.entries()) {
                    console.log("Data: ", pair[0] + ' = ' + pair[1]);
                }

                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
                btn.disabled = true;

                fetch('{{ route('cetak-surat.preview') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Status Error: ' + response.status);
                        return response.text();
                    })
                    .then(html => {
                        let win = window.open();
                        win.document.open();
                        win.document.write(html);
                        win.document.close();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Gagal mengambil preview. Cek tab Network untuk detailnya.');
                    })
                    .finally(() => {
                        btn.innerHTML = originalHtml;
                        btn.disabled = false;
                    });
            });
        </script>
    @endpush
@endsection
