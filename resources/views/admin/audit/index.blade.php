@extends('layouts.adminlte')

@section('title', 'Log Aktivitas Sistem')
@section('page-title', 'Monitor CCTV (Audit Trail)')

@section('content')
    <div class="alert alert-info shadow-sm border-0">
        <h5 class="font-weight-bold mb-1"><i class="fas fa-video mr-2"></i> Ruang Pantau Aktivitas</h5>
        <p class="mb-0 small">Semua aktivitas perubahan data (Tambah, Edit, Hapus) yang dilakukan oleh pengurus terekam di
            halaman ini. Hanya Super Admin yang memiliki akses ke log ini.</p>
    </div>

    <div class="card card-dark shadow-sm">
        <div class="card-header border-0 pt-3 pb-2">
            <h3 class="card-title mt-1"><i class="fas fa-history mr-2"></i> Riwayat Perubahan Data</h3>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-striped align-middle text-sm">
                <thead class="bg-light">
                    <tr>
                        <th style="width: 15%">Waktu Kejadian</th>
                        <th style="width: 15%">Aktor (Pelaku)</th>
                        <th style="width: 10%">Aksi</th>
                        <th style="width: 25%">Deskripsi</th>
                        <th style="width: 35%">Detail Perubahan Data</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td>
                                <strong>{{ $log->created_at->format('d M Y') }}</strong><br>
                                <span class="text-muted"><i class="far fa-clock"></i>
                                    {{ $log->created_at->format('H:i:s') }} WIB</span>
                            </td>
                            <td>
                                @if ($log->causer)
                                    <strong>{{ $log->causer->name }}</strong><br>
                                    <span
                                        class="text-muted text-xs">{{ $log->causer->organization->name ?? 'Super Admin' }}</span>
                                @else
                                    <span class="text-muted">Sistem / Guest</span>
                                @endif
                            </td>
                            <td>
                                @if ($log->event == 'created')
                                    <span class="badge badge-success"><i class="fas fa-plus-circle"></i> Tambah</span>
                                @elseif($log->event == 'updated')
                                    <span class="badge badge-warning"><i class="fas fa-edit"></i> Edit</span>
                                @elseif($log->event == 'deleted')
                                    <span class="badge badge-danger"><i class="fas fa-trash"></i> Hapus</span>
                                @else
                                    <span class="badge badge-secondary">{{ ucfirst($log->event) }}</span>
                                @endif
                            </td>
                            <td>
                                <strong>{{ class_basename($log->subject_type) }}</strong> (ID: {{ $log->subject_id }})<br>
                                <span class="text-muted">{{ $log->description }}</span>
                            </td>
                            <td>
                                @if ($log->properties->count() > 0)
                                    {{-- JIKA AKSI EDIT: Tampilkan Lama vs Baru --}}
                                    @if (isset($log->properties['old']) && isset($log->properties['attributes']))
                                        <button class="btn btn-xs btn-outline-info" type="button" data-toggle="collapse"
                                            data-target="#detail-{{ $log->id }}">
                                            <i class="fas fa-exchange-alt mr-1"></i> Lihat Perubahan
                                        </button>
                                        <div class="collapse mt-2" id="detail-{{ $log->id }}">
                                            <div class="p-2 bg-dark text-white rounded text-sm shadow-sm">
                                                <div class="text-warning mb-1 border-bottom border-secondary pb-1"><i
                                                        class="fas fa-history"></i> <strong>DATA LAMA</strong></div>
                                                <ul class="list-unstyled mb-2 pl-2">
                                                    @foreach ($log->properties['old'] as $key => $value)
                                                        @if (isset($log->properties['attributes'][$key]) &&
                                                                $log->properties['attributes'][$key] != $value &&
                                                                !in_array($key, ['updated_at']))
                                                            <li>{{ ucwords(str_replace('_', ' ', $key)) }}: <span
                                                                    class="text-danger"><del>{{ $value ?: '(Kosong)' }}</del></span>
                                                            </li>
                                                        @endif
                                                    @endforeach
                                                </ul>

                                                <div class="text-success mt-2 mb-1 border-bottom border-secondary pb-1"><i
                                                        class="fas fa-check"></i> <strong>DATA BARU</strong></div>
                                                <ul class="list-unstyled mb-0 pl-2">
                                                    @foreach ($log->properties['attributes'] as $key => $value)
                                                        @if (isset($log->properties['old'][$key]) && $log->properties['old'][$key] != $value && !in_array($key, ['updated_at']))
                                                            <li>{{ ucwords(str_replace('_', ' ', $key)) }}: <span
                                                                    class="text-success">{{ $value ?: '(Kosong)' }}</span>
                                                            </li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>

                                        {{-- JIKA AKSI TAMBAH: Tampilkan List Data Rapi --}}
                                    @elseif(isset($log->properties['attributes']))
                                        <button class="btn btn-xs btn-outline-success" type="button" data-toggle="collapse"
                                            data-target="#detail-{{ $log->id }}">
                                            <i class="fas fa-list mr-1"></i> Lihat Data
                                        </button>
                                        <div class="collapse mt-2" id="detail-{{ $log->id }}">
                                            <div class="p-2 bg-light rounded text-sm border shadow-sm">
                                                <ul class="list-unstyled mb-0 pl-1">
                                                    @foreach ($log->properties['attributes'] as $key => $value)
                                                        {{-- Sembunyikan kolom teknis yang tidak penting untuk dibaca --}}
                                                        @if (!in_array($key, ['id', 'organization_id', 'created_at', 'updated_at']))
                                                            <li class="border-bottom py-1">
                                                                <strong class="text-muted"
                                                                    style="display: inline-block; width: 130px;">
                                                                    {{ ucwords(str_replace('_', ' ', $key)) }}
                                                                </strong>
                                                                <span class="text-dark font-weight-bold">:
                                                                    {{ $value ?: '-' }}</span>
                                                            </li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    @endif
                                @else
                                    <span class="text-muted italic">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="fas fa-shield-alt mb-2" style="font-size: 32px;"></i><br>
                                Belum ada aktivitas yang terekam di sistem.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($logs->hasPages())
            <div class="card-footer bg-white">
                <div class="float-right">{{ $logs->links() }}</div>
            </div>
        @endif
    </div>
@endsection
