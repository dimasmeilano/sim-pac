@extends('layouts.adminlte')

@push('styles')
    <style>
        .form-group label {
            font-weight: normal;
            margin-bottom: 5px;
        }

        .btn-group-custom {
            margin-top: 30px;
        }

        @media (max-width: 768px) {
            .btn-group-custom {
                margin-top: 0;
            }
        }
    </style>
@endpush

@section('title', 'Laporan Keuangan')
@section('page-title', 'Laporan Keuangan')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Filter Laporan</h3>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('keuangan.laporan') }}" class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Dari Tanggal</label>
                        <input type="date" name="start_date" class="form-control"
                            value="{{ request('start_date', $startDate ?? date('Y-m-01')) }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Sampai Tanggal</label>
                        <input type="date" name="end_date" class="form-control"
                            value="{{ request('end_date', $endDate ?? date('Y-m-t')) }}">
                    </div>
                </div>

                @if (auth()->user()->hasRole('super_admin'))
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Jenis Laporan</label>
                            <select name="jenis_organisasi" class="form-control">
                                <option value="">Semua Jenis</option>
                                <option value="ipnu" {{ request('jenis_organisasi') == 'ipnu' ? 'selected' : '' }}>IPNU
                                </option>
                                <option value="ippnu" {{ request('jenis_organisasi') == 'ippnu' ? 'selected' : '' }}>IPPNU
                                </option>
                                <option value="bersama" {{ request('jenis_organisasi') == 'bersama' ? 'selected' : '' }}>
                                    Bersama</option>
                            </select>
                        </div>
                    </div>
                @else
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Jenis Laporan</label>
                            <input type="text" class="form-control"
                                value="{{ $organization->jenisOrganisasiText ?? '-' }}" readonly disabled>
                            <input type="hidden" name="jenis_organisasi"
                                value="{{ $organization->jenis_organisasi ?? '' }}">
                        </div>
                    </div>
                @endif

                <div class="col-md-3">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Tampilkan
                            </button>
                            <a href="{{ route('keuangan.laporan.pdf', request()->all()) }}" class="btn btn-success"
                                target="_blank">
                                <i class="fas fa-file-pdf"></i> Download PDF
                            </a>
                            <a href="{{ route('keuangan.laporan') }}" class="btn btn-default">
                                <i class="fas fa-sync-alt"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if (isset($transaksi) && $transaksi->count() > 0)
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    Preview Laporan
                    @if (isset($jenisOrganisasi) && $jenisOrganisasi)
                        <span class="badge badge-info ml-2">
                            @if ($jenisOrganisasi == 'ipnu')
                                IPNU
                            @elseif($jenisOrganisasi == 'ippnu')
                                IPPNU
                            @else
                                Bersama
                            @endif
                        </span>
                    @endif
                </h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr style="background-color: #f0f0f0;">
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Kode</th>
                                <th>Uraian</th>
                                <th class="text-right">Masuk (Rp)</th>
                                <th class="text-right">Keluar (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @foreach ($transaksi as $item)
                                <tr>
                                    <td class="text-center">{{ $no++ }}</td>
                                    <td class="text-center">{{ date('d/m/Y', strtotime($item->tanggal)) }}</td>
                                    <td class="text-center">{{ $item->kode_transaksi }}</td>
                                    <td>{{ $item->judul }}</td>
                                    <td class="text-right">
                                        {{ $item->jenis == 'masuk' ? number_format($item->nominal, 0, ',', '.') : '-' }}
                                    </td>
                                    <td class="text-right">
                                        {{ $item->jenis == 'keluar' ? number_format($item->nominal, 0, ',', '.') : '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr style="background-color: #e9ecef;">
                                <td colspan="4" class="text-right"><strong>TOTAL</strong></td>
                                <td class="text-right"><strong>{{ number_format($totalMasuk, 0, ',', '.') }}</strong></td>
                                <td class="text-right"><strong>{{ number_format($totalKeluar, 0, ',', '.') }}</strong></td>
                            </tr>
                            <tr style="background-color: #d1d7e0;">
                                <td colspan="4" class="text-right"><strong>SALDO</strong></td>
                                <td colspan="2" class="text-right">
                                    <strong>{{ number_format($saldo, 0, ',', '.') }}</strong>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    @elseif(isset($transaksi))
        <div class="alert alert-warning">
            <i class="fas fa-info-circle"></i> Tidak ada data transaksi pada periode ini.
        </div>
    @endif
@endsection
