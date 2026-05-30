@extends('layouts.adminlte')

@section('title', 'Buat Surat - ' . $template->nama)
@section('page-title', 'Buat Surat: ' . $template->nama)

@section('content')
    <div class="card card-primary shadow-sm">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-file-signature"></i> Form Pengisian Surat Baku</h3>
            <div class="card-tools">
                <span class="badge badge-light text-primary border"><i class="fas fa-tag"></i> {{ $template->kode }}</span>
                @if ($template->lampiran)
                    <span class="badge badge-warning"><i class="fas fa-paperclip"></i> Lampiran:
                        {{ $template->lampiran }}</span>
                @endif
            </div>
        </div>

        <form id="formSurat" action="{{ route('cetak-surat.store', $template->id) }}" method="POST"
            enctype="multipart/form-data">
            @csrf

            <input type="hidden" name="organisasi" value="{{ $organisasi->jenis_organisasi ?? 'bersama' }}">
            <input type="hidden" name="tingkat" value="{{ $organisasi->type ?? 'pac' }}">
            <input type="hidden" name="periode" value="{{ $organisasi->periode ?? 'XVI' }}">
            <input type="hidden" name="fields[kop_organisasi]" value="{{ $defaultData['kop_organisasi'] ?? '' }}">
            <input type="hidden" name="fields[nama_organisasi_lengkap]"
                value="{{ $defaultData['nama_organisasi_lengkap'] ?? '' }}">
            <input type="hidden" name="fields[pembuka_surat]" value="{{ $defaultData['pembuka_surat'] ?? '' }}">
            <input type="hidden" name="fields[tanggal_masehi]" value="{{ date('Y-m-d') }}">
            <input type="hidden" name="fields[tanggal_hijriah]" value="{{ $defaultData['tanggal_hijriah'] ?? '' }}">

            <div class="card-body">
                <div class="row">

                    <div class="col-md-8 border-right pr-4">
                        <h5 class="text-primary font-weight-bold mb-3 border-bottom pb-2"><i class="fas fa-edit"></i>
                            Lengkapi Data Surat</h5>

                        <div class="alert alert-info bg-light text-dark border-info shadow-sm mb-4">
                            <h6 class="font-weight-bold mb-1"><i class="fas fa-info-circle text-info"></i> Template:
                                {{ $template->nama }}</h6>
                            <hr class="mt-1 mb-2">
                            <small>
                                <strong>Penerbit:</strong> Pimpinan {{ ucwords(strtolower($organisasi->nama_wilayah)) }}
                                ({{ $organisasi->jenis_organisasi == 'ipnu' ? 'IPNU' : ($organisasi->jenis_organisasi == 'ippnu' ? 'IPPNU' : 'IPNU & IPPNU') }})<br>
                                <strong>Alamat:</strong> {{ $organisasi->alamat ?? '-' }}
                            </small>
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
                                            <label
                                                class="text-secondary">{{ ucfirst(str_replace('_', ' ', $field)) }}</label>

                                            @if ($field == 'status_desa')
                                                <select name="fields[{{ $field }}]" class="form-control">
                                                    <option value="DESA"
                                                        {{ old("fields.$field", $defaultData['status_desa'] ?? 'DESA') == 'DESA' ? 'selected' : '' }}>
                                                        DESA</option>
                                                    <option value="KELURAHAN"
                                                        {{ old("fields.$field", $defaultData['status_desa'] ?? 'DESA') == 'KELURAHAN' ? 'selected' : '' }}>
                                                        KELURAHAN</option>
                                                </select>
                                            @elseif($field == 'nama_desa')
                                                <input type="text" name="fields[{{ $field }}]"
                                                    class="form-control"
                                                    value="{{ old("fields.$field", $defaultData['nama_desa'] ?? '') }}"
                                                    placeholder="Contoh: SUKOREJO (KAPITAL)">
                                            @elseif($field == 'nama_desa_lower')
                                                <input type="text" name="fields[{{ $field }}]"
                                                    class="form-control"
                                                    value="{{ old("fields.$field", $defaultData['nama_desa_lower'] ?? '') }}"
                                                    placeholder="Contoh: Sukorejo">
                                            @elseif($field == 'masa_bhakti')
                                                <input type="text" name="fields[{{ $field }}]"
                                                    class="form-control"
                                                    value="{{ old("fields.$field", $defaultData['masa_bhakti'] ?? '') }}"
                                                    placeholder="Contoh: 2026-2028">
                                            @elseif($field == 'surat_ranting_nomor')
                                                <input type="text" name="fields[{{ $field }}]"
                                                    class="form-control" value="{{ old("fields.$field") }}"
                                                    placeholder="Contoh: 001/PR/...">
                                            @elseif($field == 'surat_ranting_tanggal')
                                                <input type="date" name="fields[{{ $field }}]"
                                                    class="form-control" value="{{ old("fields.$field", date('Y-m-d')) }}">
                                            @elseif($field == 'surat_prnu_nomor')
                                                <input type="text" name="fields[{{ $field }}]"
                                                    class="form-control" value="{{ old("fields.$field") }}"
                                                    placeholder="Contoh: 01/PRNU/...">
                                            @elseif($field == 'nama_ketua' || $field == 'nia_ketua' || $field == 'nama_sekretaris' || $field == 'nia_sekretaris')
                                                <input type="text" name="fields[{{ $field }}]"
                                                    class="form-control bg-light"
                                                    value="{{ old("fields.$field", $defaultData[$field] ?? '') }}"
                                                    readonly>
                                            @elseif($field == 'jenis_kelamin')
                                                <select name="fields[{{ $field }}]" class="form-control">
                                                    <option value="Laki-laki">Laki-laki</option>
                                                    <option value="Perempuan">Perempuan</option>
                                                </select>
                                            @elseif(in_array($field, ['tanggal_lahir', 'tanggal_penetapan', 'tanggal_pernyataan', 'tanggal_surat']))
                                                <input type="date" name="fields[{{ $field }}]"
                                                    class="form-control" value="{{ old("fields.$field", date('Y-m-d')) }}">
                                            @else
                                                <textarea name="fields[{{ $field }}]" class="form-control" rows="2"
                                                    placeholder="Isi {{ str_replace('_', ' ', $field) }}">{{ old("fields.$field") }}</textarea>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        @if ($template->has_attachment)
                            <div class="form-group mt-3 p-3 border rounded bg-light">
                                <label class="text-danger"><i class="fas fa-paperclip"></i> Lampiran Susunan
                                    Pengurus</label>
                                <div class="custom-file mb-2">
                                    <input type="file" name="file_lampiran" class="custom-file-input"
                                        id="fileLampiran" accept=".pdf,.jpg,.jpeg,.png">
                                    <label class="custom-file-label" for="fileLampiran">Pilih file (PDF/JPG/PNG)</label>
                                </div>
                                <small class="text-muted d-block">
                                    <i class="fas fa-info-circle"></i> Berkas akan otomatis digabungkan di halaman belakang
                                    surat utama.
                                </small>
                            </div>
                        @endif
                    </div>

                    <div class="col-md-4 pl-4">
                        <h5 class="text-secondary font-weight-bold mb-3 border-bottom pb-2"><i
                                class="fas fa-sliders-h"></i> Pengaturan Surat</h5>

                        <div class="form-group">
                            <label>Nomor Surat <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" name="nomor_surat" id="nomor_surat"
                                    class="form-control font-weight-bold text-dark bg-light"
                                    value="{{ old('nomor_surat') }}" placeholder="Klik tombol Generate" readonly>
                                <div class="input-group-append">
                                    <button type="button" id="generate_nomor" class="btn btn-primary"
                                        title="Generate Nomor Otomatis">
                                        <i class="fas fa-magic"></i> Buat
                                    </button>
                                </div>
                            </div>
                            <small class="text-success mt-1 d-block"><i class="fas fa-check-circle"></i> Sesuai format
                                PPAO otomatis</small>
                        </div>

                        @if (auth()->user()->hasRole('super_admin') && !empty($organizations))
                            <div class="form-group border-top pt-3 mt-3">
                                <label class="text-danger"><i class="fas fa-user-shield"></i> Override Organisasi (Super
                                    Admin)</label>
                                <select name="organization_override" id="organization_override"
                                    class="form-control form-control-sm">
                                    <option value="">- Gunakan organisasi user -</option>
                                    @foreach ($organizations as $org)
                                        <option value="{{ $org->id }}" data-jenis="{{ $org->jenis_organisasi }}"
                                            data-type="{{ $org->type }}" data-periode="{{ $org->periode ?? 'XVI' }}">
                                            {{ $org->name }} ({{ strtoupper($org->type) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="form-group border-top pt-3 mt-3">
                            <label class="text-muted small text-uppercase">Organisasi Penerbit</label>
                            <input type="text" class="form-control form-control-sm bg-light"
                                value="{{ $organisasi->nama_lengkap ?? $organisasi->name }}" readonly disabled>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="text-muted small text-uppercase">Tingkat</label>
                                    <input type="text" class="form-control form-control-sm bg-light text-center"
                                        value="{{ strtoupper($organisasi->type ?? 'PAC') }}" readonly disabled>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="text-muted small text-uppercase">Periode</label>
                                    <input type="text" class="form-control form-control-sm bg-light text-center"
                                        value="{{ $organisasi->periode ?? 'XVI' }}" readonly disabled>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="card-footer bg-light d-flex justify-content-between align-items-center">
                <button type="button" id="previewBtn" class="btn btn-outline-info">
                    <i class="fas fa-eye"></i> Tinjau Draft (Preview)
                </button>
                <div>
                    <a href="{{ route('cetak-surat.index') }}" class="btn btn-secondary mr-2">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Sebagai Draft
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div id="preview_konten" style="display:none;"></div>

    @push('scripts')
        <script>
            // Pastikan meta tag CSRF ada di layout.adminlte Anda!
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
                let formElement = document.getElementById('formSurat');
                let formData = new FormData(formElement);
                formData.append('template_id', '{{ $template->id }}');

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
