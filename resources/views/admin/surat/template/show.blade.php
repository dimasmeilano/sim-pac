@extends('layouts.adminlte')

@section('title', 'Detail Template')
@section('page-title', 'Detail Template: ' . $template->nama)

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Informasi Template</h3>
            <div class="card-tools">
                <a href="{{ route('surat.template.edit', $template) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('surat.template.index') }}" class="btn btn-default btn-sm">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <th width="30%">Nama Template</th>
                    <td>{{ $template->nama }}</td>
                </tr>
                <tr>
                    <th>Kode</th>
                    <td><code>{{ $template->kode }}</code></td>
                </tr>
                <tr>
                    <th>Jenis Surat</th>
                    <td>{{ $template->jenis == 'keluar' ? 'Surat Keluar' : 'Surat Masuk' }}</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>{{ $template->status == 'aktif' ? 'Aktif' : 'Nonaktif' }}</td>
                </tr>
                <tr>
                    <th>Placeholder</th>
                    <td>
                        @foreach ($template->placeholder_list as $p)
                            <code>{!! $p !!}</code>
                        @endforeach
                    </td>
                </tr>
                <tr>
                    <th>Konten Template</th>
                    <td>
                        <div style="border:1px solid #ddd; padding:15px; border-radius:5px; background:#f9f9f9;">
                            {!! nl2br(e($template->konten)) !!}
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
@endsection
