@extends('layouts.adminlte')

@section('title', 'Detail Template Surat')
@section('page-title', 'Detail Template: ' . $template->nama)

@section('content')
    <div class="card card-info shadow-sm">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-file-invoice"></i> Informasi Detail Template</h3>
            <div class="card-tools">
                <a href="{{ route('surat.template.index') }}" class="btn btn-default btn-sm mr-1">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>

                @if (auth()->user()->hasRole('sekretaris_pac') ||
                        auth()->user()->hasRole('sekretaris_ranting') ||
                        auth()->user()->hasRole('sekretaris_komisariat') ||
                        auth()->user()->hasRole('super_admin'))
                    <a href="{{ route('surat.template.edit', $template) }}" class="btn btn-warning btn-sm font-weight-bold">
                        <i class="fas fa-edit"></i> Edit Template
                    </a>
                @endif
            </div>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-6 border-right">
                    <h5 class="text-info font-weight-bold mb-3 border-bottom pb-2">Informasi Dasar</h5>
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th width="35%" class="text-muted">Nama Template</th>
                            <td>: <span class="font-weight-bold">{{ $template->nama }}</span></td>
                        </tr>
                        <tr>
                            <th class="text-muted">Kode</th>
                            <td>: <code>{{ $template->kode }}</code></td>
                        </tr>
                        <tr>
                            <th class="text-muted">Kategori Surat</th>
                            <td>:
                                <span
                                    class="badge badge-secondary text-uppercase">{{ $template->jenis_surat ?? 'umum' }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted">Arus Surat</th>
                            <td>:
                                @if ($template->jenis == 'keluar')
                                    <span class="badge badge-primary">Surat Keluar</span>
                                @else
                                    <span class="badge badge-info">Surat Masuk</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted">Status</th>
                            <td>:
                                @if ($template->status == 'aktif')
                                    <span class="badge badge-success"><i class="fas fa-check-circle"></i> Aktif</span>
                                @else
                                    <span class="badge badge-danger"><i class="fas fa-times-circle"></i> Nonaktif</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted">Urutan Tampil</th>
                            <td>: {{ $template->urutan ?? 0 }}</td>
                        </tr>
                    </table>
                </div>

                <div class="col-md-6 pl-md-4">
                    <h5 class="text-success font-weight-bold mb-3 border-bottom pb-2">Atribut Spesifik</h5>
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th width="35%" class="text-muted">Klasifikasi</th>
                            <td>: {{ $template->klasifikasi ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Lampiran Bawaan</th>
                            <td>: {{ $template->lampiran ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Aturan Input Form<br><small>(Fields Dinamis)</small></th>
                            <td>
                                @php
                                    $fields = $template->fields;
                                    // Jika data berupa string JSON, kita decode. Jika sudah array, pakai langsung.
                                    if (is_string($fields)) {
                                        $fieldsArray = json_decode($fields, true);
                                    } else {
                                        $fieldsArray = $fields;
                                    }
                                @endphp

                                @if (!empty($fieldsArray) && is_array($fieldsArray))
                                    <ul class="list-unstyled mb-0 pl-2" style="border-left: 3px solid #28a745;">
                                        @foreach ($fieldsArray as $key => $type)
                                            <li>
                                                <code>{{ $key }}</code>
                                                <i class="fas fa-arrow-right text-muted mx-1" style="font-size: 10px;"></i>
                                                <span class="badge badge-light border">{{ $type }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-muted"><em>Tidak ada (Template Teks Bebas)</em></span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <h5 class="text-primary font-weight-bold mt-5 border-bottom pb-2">Visual Preview (Hasil Render Template)</h5>
            <div class="alert alert-light border shadow-sm mb-4">
                <i class="fas fa-info-circle text-primary"></i>
                <strong>Info:</strong> Tampilan di bawah ini adalah pratinjau kasar. Kata yang berada di dalam tanda kurung
                kurawal seperti <code>{nama_variabel}</code> akan otomatis diubah menjadi teks asli saat surat dicetak.
            </div>

            <div class="p-4 border rounded bg-white shadow-sm" style="min-height: 300px; color: #000; overflow-x: auto;">
                {!! $template->konten !!}
            </div>

            <h5 class="text-secondary font-weight-bold mt-5 border-bottom pb-2">Source Code (HTML Mentah)</h5>
            <div class="bg-dark p-3 rounded shadow-sm">
                <pre class="text-white mb-0" style="white-space: pre-wrap; font-size: 14px;">{{ $template->konten }}</pre>
            </div>

        </div>
    </div>
@endsection
