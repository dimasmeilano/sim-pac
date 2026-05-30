@extends('layouts.adminlte')

@section('title', 'Manajemen Keuangan')
@section('page-title', 'Manajemen Keuangan')

@section('content')
    <div class="row">
        <div class="col-md-3">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>Rp {{ number_format($saldoIpnu ?? 0, 0, ',', '.') }}</h3>
                    <p>Saldo IPNU</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>Rp {{ number_format($saldoIppnu ?? 0, 0, ',', '.') }}</h3>
                    <p>Saldo IPPNU</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>Rp {{ number_format($saldoBersama ?? 0, 0, ',', '.') }}</h3>
                    <p>Saldo Bersama</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>Rp {{ number_format($saldoGabungan ?? 0, 0, ',', '.') }}</h3>
                    <p>Total Saldo (Gabung)</p>
                </div>
            </div>
        </div>
    </div>

    <div class="alert alert-warning">
        <i class="fas fa-info-circle"></i>
        Saldo di atas hanya menghitung transaksi yang sudah <strong>DIVALIDASI</strong>.
        Transaksi dengan status "Menunggu Validasi" belum mempengaruhi saldo.
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Filter Transaksi</h3>
            <div class="card-tools">
                <a href="{{ route('keuangan.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Tambah Transaksi
                </a>
                <a href="{{ route('keuangan.laporan') }}" class="btn btn-info btn-sm">
                    <i class="fas fa-print"></i> Laporan
                </a>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" class="row">
                @if (auth()->user()->hasRole('super_admin'))
                    <div class="col-md-3">
                        <select name="jenis_organisasi" class="form-control">
                            <option value="">Semua Jenis Organisasi</option>
                            <option value="ipnu" {{ request('jenis_organisasi') == 'ipnu' ? 'selected' : '' }}>IPNU
                            </option>
                            <option value="ippnu" {{ request('jenis_organisasi') == 'ippnu' ? 'selected' : '' }}>IPPNU
                            </option>
                            <option value="bersama" {{ request('jenis_organisasi') == 'bersama' ? 'selected' : '' }}>Bersama
                            </option>
                        </select>
                    </div>
                @endif
                <div class="col-md-2">
                    <select name="jenis" class="form-control">
                        <option value="">Semua Jenis</option>
                        <option value="masuk" {{ request('jenis') == 'masuk' ? 'selected' : '' }}>Pemasukan</option>
                        <option value="keluar" {{ request('jenis') == 'keluar' ? 'selected' : '' }}>Pengeluaran</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="bulan" class="form-control">
                        <option value="">Semua Bulan</option>
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="tahun" class="form-control">
                        <option value="">Semua Tahun</option>
                        @for ($i = 2024; $i <= date('Y'); $i++)
                            <option value="{{ $i }}" {{ request('tahun') == $i ? 'selected' : '' }}>
                                {{ $i }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-default">Filter</button>
                    <a href="{{ route('keuangan.index') }}" class="btn btn-link">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Transaksi</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Tanggal</th>
                            <th>Judul</th>
                            <th>Jenis Org</th>
                            <th>Jenis</th>
                            <th>Nominal</th>
                            <th>Status Validasi</th>
                            <th>Dibuat Oleh</th>
                            <th>Divalidasi Oleh</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transaksi as $key => $item)
                            <tr>
                                <td>{{ $transaksi->firstItem() + $key }}</td>
                                <td><code>{{ $item->kode_transaksi }}</code></td>
                                <td>{{ date('d/m/Y', strtotime($item->tanggal)) }}</td>
                                <td>{{ $item->judul }}</td>
                                <td>
                                    @if ($item->jenis_organisasi == 'ipnu')
                                        <span class="badge badge-primary">IPNU</span>
                                    @elseif($item->jenis_organisasi == 'ippnu')
                                        <span class="badge badge-danger">IPPNU</span>
                                    @else
                                        <span class="badge badge-success">Bersama</span>
                                    @endif
                                </td>
                                <td>{!! $item->jenis_text !!}</td>
                                <td class="text-right">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                                <td>{!! $item->status_validasi_text !!}</td>
                                <td>{{ $item->createdBy->name ?? '-' }}</td>
                                <td>
                                    @if ($item->status_validasi == 'disetujui' && $item->validator)
                                        {{ $item->validator->name }}
                                    @elseif($item->status_validasi == 'ditolak' && $item->validator)
                                        <span class="text-danger">{{ $item->validator->name }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $user = auth()->user();
                                        $isSuperAdmin = $user->hasRole('super_admin');
                                        $isBendaharaIpnu =
                                            $user->hasRole('bendahara_pac') &&
                                            $user->organization?->jenis_organisasi == 'ipnu';
                                        $isBendaharaIppnu =
                                            $user->hasRole('bendahara_pac') &&
                                            $user->organization?->jenis_organisasi == 'ippnu';

                                        $canValidate = $isBendaharaIpnu || $isBendaharaIppnu || $isSuperAdmin;
                                    @endphp

                                    @if ($item->status_validasi == 'menunggu' && $canValidate)
                                        @if (
                                            $isSuperAdmin ||
                                                ($item->jenis_organisasi == 'ipnu' && $isBendaharaIpnu) ||
                                                ($item->jenis_organisasi == 'ippnu' && $isBendaharaIppnu) ||
                                                $item->jenis_organisasi == 'bersama')
                                            <button type="button" class="btn btn-success btn-sm" data-toggle="modal"
                                                data-target="#modalValidate" data-id="{{ $item->id }}"
                                                data-judul="{{ $item->judul }}"
                                                data-nominal="Rp {{ number_format($item->nominal, 0, ',', '.') }}"
                                                data-creator="{{ $item->createdBy->name ?? '-' }}"
                                                data-tanggal="{{ date('d/m/Y', strtotime($item->tanggal)) }}"
                                                data-bukti_url="{{ $item->bukti_file ? asset('storage/' . $item->bukti_file) : '' }}">
                                                <i class="fas fa-check-circle"></i> Validasi
                                            </button>
                                        @endif
                                    @endif

                                    @if (
                                        $isSuperAdmin ||
                                            (in_array($item->status_validasi, ['draft', 'menunggu', 'ditolak']) && $user->id == $item->created_by))
                                        <a href="{{ route('keuangan.edit', $item) }}" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <form action="{{ route('keuangan.destroy', $item) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-sm"
                                                onclick="return confirm('Yakin hapus transaksi ini?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif

                                    @if ($item->status_validasi == 'disetujui')
                                        <a href="{{ route('keuangan.show', $item) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $transaksi->appends(request()->query())->links() }}
        </div>
    </div>

    <div class="modal fade" id="modalValidate" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Validasi Transaksi</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form id="formValidate" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="alert alert-info">
                                    <strong>Transaksi:</strong> <span id="transaksi-judul"></span><br>
                                    <strong>Nominal:</strong> <span id="transaksi-nominal"></span><br>
                                    <strong>Dibuat Oleh:</strong> <span id="transaksi-creator"></span><br>
                                    <strong>Tanggal:</strong> <span id="transaksi-tanggal"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-primary text-white">
                                        <strong><i class="fas fa-paperclip"></i> Bukti Transaksi</strong>
                                    </div>
                                    <div class="card-body text-center" id="bukti-container">
                                        <p class="text-muted">Loading...</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Status Validasi</label>
                            <select name="status_validasi" id="status_validasi" class="form-control" required>
                                <option value="">Pilih Status</option>
                                <option value="disetujui">✅ Disetujui</option>
                                <option value="ditolak">❌ Ditolak</option>
                            </select>
                        </div>

                        <div class="form-group" id="catatan-group" style="display: none;">
                            <label>Catatan Penolakan <span class="text-danger">*</span></label>
                            <textarea name="catatan_validasi" id="catatan_validasi" class="form-control" rows="3"
                                placeholder="Wajib diisi jika menolak transaksi..."></textarea>
                            <small class="text-danger" id="catatan-warning" style="display: none;">Catatan penolakan
                                wajib diisi!</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Event ketika modal akan ditampilkan
            $('#modalValidate').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');
                var judul = button.data('judul');
                var nominal = button.data('nominal');
                var creator = button.data('creator');
                var tanggal = button.data('tanggal');
                var buktiUrl = button.data('bukti_url');

                var modal = $(this);
                modal.find('#transaksi-judul').text(judul);
                modal.find('#transaksi-nominal').text(nominal);
                modal.find('#transaksi-creator').text(creator);
                modal.find('#transaksi-tanggal').text(tanggal);

                // FIX 3: Menggunakan helper url() agar kebal terhadap perubahan domain/prefix
                var actionUrl = "{{ url('keuangan') }}/" + id + "/validate";
                modal.find('#formValidate').attr('action', actionUrl);

                // Tampilkan bukti transaksi
                var buktiContainer = modal.find('#bukti-container');
                if (buktiUrl) {
                    var ext = buktiUrl.split('.').pop().toLowerCase();
                    if (ext === 'pdf') {
                        buktiContainer.html('<embed src="' + buktiUrl +
                            '" type="application/pdf" style="width:100%; height:300px;">');
                    } else if (['jpg', 'jpeg', 'png', 'gif'].includes(ext)) {
                        buktiContainer.html('<img src="' + buktiUrl +
                            '" class="img-fluid" style="max-height: 200px;">');
                    } else {
                        buktiContainer.html('<a href="' + buktiUrl +
                            '" target="_blank" class="btn btn-info">Lihat Bukti</a>');
                    }
                } else {
                    buktiContainer.html('<p class="text-muted">Tidak ada bukti transaksi</p>');
                }

                // Reset form
                modal.find('#status_validasi').val('');
                modal.find('#catatan_validasi').val('');
                modal.find('#catatan-group').hide();
                modal.find('#catatan-warning').hide();
            });

            // Tampilkan catatan jika memilih Ditolak
            $(document).on('change', '#status_validasi', function() {
                if ($(this).val() === 'ditolak') {
                    $('#catatan-group').show();
                    $('#catatan_validasi').prop('required', true);
                } else {
                    $('#catatan-group').hide();
                    $('#catatan_validasi').prop('required', false);
                    $('#catatan-warning').hide();
                }
            });

            // Validasi sebelum submit
            $('#formValidate').on('submit', function(e) {
                var status = $('#status_validasi').val();
                var catatan = $('#catatan_validasi').val();

                if (status === 'ditolak' && catatan.trim() === '') {
                    e.preventDefault();
                    $('#catatan-warning').show();
                    $('#catatan_validasi').focus();
                } else {
                    $('#catatan-warning').hide();
                }
            });
        });
    </script>
@endsection
