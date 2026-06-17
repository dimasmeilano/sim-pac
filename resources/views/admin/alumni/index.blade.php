@extends('layouts.adminlte')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 text-gray-800">Manajemen Alumni</h1>
            <a href="{{ route('alumni.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Alumni
            </a>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Daftar Majelis Alumni & Forum Alumni</h6>

                <form action="{{ route('alumni.index') }}" method="GET" class="form-inline">
                    <select name="jenis_organisasi" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                        <option value="">Semua Organisasi</option>
                        <option value="ipnu" {{ request('jenis_organisasi') == 'ipnu' ? 'selected' : '' }}>Majelis Alumni
                            IPNU</option>
                        <option value="ippnu" {{ request('jenis_organisasi') == 'ippnu' ? 'selected' : '' }}>Forum Alumni
                            IPPNU</option>
                    </select>
                </form>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th>Nama Lengkap</th>
                                <th>Asal Organisasi</th>
                                <th>Angkatan / Jabatan</th>
                                <th>Profesi / Instansi</th>
                                <th>Kontak</th>
                                <th>Donatur?</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($alumnis as $index => $alumni)
                                <tr>
                                    <td>{{ $alumnis->firstItem() + $index }}</td>
                                    <td><strong>{{ $alumni->nama_lengkap }}</strong></td>
                                    <td>
                                        @if ($alumni->jenis_organisasi == 'ipnu')
                                            <span class="badge badge-success">IPNU</span>
                                        @else
                                            <span class="badge badge-warning">IPPNU</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $alumni->tahun_angkatan ?? '-' }} <br>
                                        <small class="text-muted">{{ $alumni->jabatan_terakhir }}</small>
                                    </td>
                                    <td>
                                        {{ $alumni->profesi ?? '-' }} <br>
                                        <small class="text-muted">{{ $alumni->instansi_pekerjaan }}</small>
                                    </td>
                                    <td>
                                        {{ $alumni->no_hp }} <br>
                                        <small>{{ $alumni->email }}</small>
                                    </td>
                                    <td>
                                        @if ($alumni->bersedia_menjadi_donatur)
                                            <span class="badge badge-primary">Ya</span>
                                        @else
                                            <span class="badge badge-secondary">Tidak</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('alumni.edit', $alumni->id) }}"
                                            class="btn btn-sm btn-warning mb-1">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('alumni.destroy', $alumni->id) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Yakin ingin menghapus data alumni ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger mb-1">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">Belum ada data alumni yang didaftarkan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $alumnis->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
