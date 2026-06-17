@extends('layouts.adminlte')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 text-gray-800">Detail Pengajuan:
                {{ $akreditasi->organization->nama ?? $akreditasi->organization->name }}</h1>
            <a href="{{ route('akreditasi.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary text-white d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold">Borang Akreditasi ({{ strtoupper($akreditasi->jenis_borang) }})</h6>
                <span class="badge badge-light text-primary px-3 py-1">{{ $akreditasi->status }}</span>
            </div>

            <div class="card-body">
                <div class="alert alert-info">
                    <strong>Kata Pengantar:</strong><br>
                    {{ $akreditasi->kata_pengantar ?? '-' }}
                    <hr>
                    <strong>Deskripsi Singkat:</strong><br>
                    {{ $akreditasi->deskripsi_singkat ?? '-' }}
                </div>

                @if (strtoupper($akreditasi->jenis_borang) == 'IPNU')
                    @if ($akreditasi->bab5_file_ba)
                        <div class="mb-4">
                            <a href="{{ asset('storage/' . $akreditasi->bab5_file_ba) }}" target="_blank"
                                class="btn btn-danger font-weight-bold">
                                <i class="fas fa-file-pdf"></i> Lihat Dokumen Berita Acara & SK
                            </a>
                            <span class="ml-2 text-muted">No. SP: {{ $akreditasi->bab5_no_sp }}</span>
                        </div>
                    @endif

                    <h5 class="font-weight-bold text-success mt-4">BAB I: Keaswajaan</h5>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>No</th>
                                    <th>Kegiatan</th>
                                    <th>Tanggal</th>
                                    <th>Tempat</th>
                                    <th>Peserta</th>
                                    <th>Link Bukti</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($akreditasi->bab1_keaswajaan ?? [] as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item['kegiatan'] ?? '-' }}</td>
                                        <td>{{ $item['tanggal'] ?? '-' }}</td>
                                        <td>{{ $item['tempat'] ?? '-' }}</td>
                                        <td>{{ $item['peserta'] ?? '-' }}</td>
                                        <td>
                                            @if (!empty($item['link']))
                                                <a href="{{ $item['link'] }}" target="_blank"
                                                    class="btn btn-sm btn-outline-primary">Buka Link</a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Data kosong</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <h5 class="font-weight-bold text-success">BAB II: Pengkaderan</h5>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>No</th>
                                    <th>Kegiatan</th>
                                    <th>Tanggal</th>
                                    <th>Tempat</th>
                                    <th>Narasumber</th>
                                    <th>Peserta</th>
                                    <th>Link Bukti</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($akreditasi->bab2_pengkaderan ?? [] as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item['kegiatan'] ?? '-' }}</td>
                                        <td>{{ $item['tanggal'] ?? '-' }}</td>
                                        <td>{{ $item['tempat'] ?? '-' }}</td>
                                        <td>{{ $item['narasumber'] ?? '-' }}</td>
                                        <td>{{ $item['peserta'] ?? '-' }}</td>
                                        <td>
                                            @if (!empty($item['link']))
                                                <a href="{{ $item['link'] }}" target="_blank"
                                                    class="btn btn-sm btn-outline-primary">Buka Link</a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Data kosong</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <h5 class="font-weight-bold text-success">BAB III: Tim Instruktur</h5>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>No</th>
                                    <th>Kegiatan</th>
                                    <th>Tanggal</th>
                                    <th>Penyelenggara</th>
                                    <th>Instruktur</th>
                                    <th>Link Bukti</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($akreditasi->bab3_instruktur ?? [] as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item['kegiatan'] ?? '-' }}</td>
                                        <td>{{ $item['tanggal'] ?? '-' }}</td>
                                        <td>{{ $item['penyelenggara'] ?? '-' }}</td>
                                        <td>{{ $item['instruktur'] ?? '-' }}</td>
                                        <td>
                                            @if (!empty($item['link']))
                                                <a href="{{ $item['link'] }}" target="_blank"
                                                    class="btn btn-sm btn-outline-primary">Buka Link</a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Data kosong</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <h5 class="font-weight-bold text-success mt-4">BAB IV: Basis Pelajar Umum</h5>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Asal Sekolah</th>
                                    <th>No. HP</th>
                                    <th>Link Bukti</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($akreditasi->bab4_pelajar_umum ?? [] as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item['nama'] ?? '-' }}</td>
                                        <td>{{ $item['sekolah'] ?? '-' }}</td>
                                        <td>{{ $item['hp'] ?? '-' }}</td>
                                        <td>
                                            @if (!empty($item['link']))
                                                <a href="{{ $item['link'] }}" target="_blank"
                                                    class="btn btn-sm btn-outline-primary">Buka Link</a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Data kosong</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <h5 class="font-weight-bold text-success mt-4">BAB VI: Sosial Kemasyarakatan</h5>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>No</th>
                                    <th>Kegiatan</th>
                                    <th>Tanggal</th>
                                    <th>Tempat</th>
                                    <th>Narasumber/Tokoh</th>
                                    <th>Peserta</th>
                                    <th>Link Bukti</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($akreditasi->bab6_sosial ?? [] as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item['kegiatan'] ?? '-' }}</td>
                                        <td>{{ $item['tanggal'] ?? '-' }}</td>
                                        <td>{{ $item['tempat'] ?? '-' }}</td>
                                        <td>{{ $item['narasumber'] ?? '-' }}</td>
                                        <td>{{ $item['peserta'] ?? '-' }}</td>
                                        <td>
                                            @if (!empty($item['link']))
                                                <a href="{{ $item['link'] }}" target="_blank"
                                                    class="btn btn-sm btn-outline-primary">Buka Link</a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Data kosong</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <h5 class="font-weight-bold text-success mt-4">BAB VII: Pasukan CBP</h5>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Anggota CBP</th>
                                    <th>Tempat, Tgl Lahir</th>
                                    <th>Alamat</th>
                                    <th>No. Telp</th>
                                    <th>Tahun Diklat</th>
                                    <th>Link Bukti</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($akreditasi->bab7_cbp ?? [] as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item['nama'] ?? '-' }}</td>
                                        <td>{{ $item['ttl'] ?? '-' }}</td>
                                        <td>{{ $item['alamat'] ?? '-' }}</td>
                                        <td>{{ $item['telp'] ?? '-' }}</td>
                                        <td>{{ $item['tahun'] ?? '-' }}</td>
                                        <td>
                                            @if (!empty($item['link']))
                                                <a href="{{ $item['link'] }}" target="_blank"
                                                    class="btn btn-sm btn-outline-primary">Buka Link</a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Data kosong</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @else
                    <h5 class="font-weight-bold text-warning mt-4">BAB I: Organisasi</h5>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Dokumen</th>
                                    <th>Link Bukti</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($akreditasi->ippnu_bab1_organisasi ?? [] as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item['dokumen'] ?? '-' }}</td>
                                        <td>
                                            @if (!empty($item['link']))
                                                <a href="{{ $item['link'] }}" target="_blank"
                                                    class="btn btn-sm btn-outline-primary">Buka Link</a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">Data kosong</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <h5 class="font-weight-bold text-warning">BAB II: Kaderisasi</h5>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>No</th>
                                    <th>Kegiatan</th>
                                    <th>Tanggal</th>
                                    <th>Link Bukti</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($akreditasi->ippnu_bab2_kaderisasi ?? [] as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item['kegiatan'] ?? '-' }}</td>
                                        <td>{{ $item['tanggal'] ?? '-' }}</td>
                                        <td>
                                            @if (!empty($item['link']))
                                                <a href="{{ $item['link'] }}" target="_blank"
                                                    class="btn btn-sm btn-outline-primary">Buka Link</a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Data kosong</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <h5 class="font-weight-bold text-warning">BAB III: Kelembagaan</h5>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>No</th>
                                    <th>Program</th>
                                    <th>Realisasi</th>
                                    <th>Link Bukti</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($akreditasi->ippnu_bab3_kelembagaan ?? [] as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item['program'] ?? '-' }}</td>
                                        <td>{{ $item['realisasi'] ?? '-' }}</td>
                                        <td>
                                            @if (!empty($item['link']))
                                                <a href="{{ $item['link'] }}" target="_blank"
                                                    class="btn btn-sm btn-outline-primary">Buka Link</a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Data kosong</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <h5 class="font-weight-bold text-warning mt-4">BAB IV: Ke-Aswaja-an</h5>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>No</th>
                                    <th>Kegiatan</th>
                                    <th>Waktu Pelaksanaan</th>
                                    <th>Link Bukti</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($akreditasi->ippnu_bab4_aswaja ?? [] as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item['kegiatan'] ?? '-' }}</td>
                                        <td>{{ $item['waktu'] ?? '-' }}</td>
                                        <td>
                                            @if (!empty($item['link']))
                                                <a href="{{ $item['link'] }}" target="_blank"
                                                    class="btn btn-sm btn-outline-primary">Buka Link</a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Data kosong</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <h5 class="font-weight-bold text-warning mt-4">BAB V: KPP</h5>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>No</th>
                                    <th>Data Anggota / Kegiatan</th>
                                    <th>Keterangan</th>
                                    <th>Link Bukti</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($akreditasi->ippnu_bab5_kpp ?? [] as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item['data'] ?? '-' }}</td>
                                        <td>{{ $item['keterangan'] ?? '-' }}</td>
                                        <td>
                                            @if (!empty($item['link']))
                                                <a href="{{ $item['link'] }}" target="_blank"
                                                    class="btn btn-sm btn-outline-primary">Buka Link</a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Data kosong</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <h5 class="font-weight-bold text-warning mt-4">BAB VI: Media Sosial & Informasi</h5>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>No</th>
                                    <th>Platform Media</th>
                                    <th>Nama Akun</th>
                                    <th>Link Akun / Bukti</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($akreditasi->ippnu_bab6_media ?? [] as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item['platform'] ?? '-' }}</td>
                                        <td>{{ $item['akun'] ?? '-' }}</td>
                                        <td>
                                            @if (!empty($item['link']))
                                                <a href="{{ $item['link'] }}" target="_blank"
                                                    class="btn btn-sm btn-outline-primary">Buka Link</a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Data kosong</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @endif

                <hr>

                <h5 class="font-weight-bold mt-4 mb-3"><i class="fas fa-history"></i> Status Penilaian PAC</h5>
                <div class="row text-center mb-4">
                    <div class="col-md-6 border-right">
                        <p class="small text-muted mb-1">Verifikator (Sekretaris)</p>
                        <strong class="{{ $akreditasi->sekretaris ? 'text-success' : 'text-danger' }}">
                            {{ $akreditasi->sekretaris->name ?? 'Belum diverifikasi' }}
                        </strong>
                        @if ($akreditasi->catatan_sekretaris)
                            <div class="mt-2 small bg-light p-2 rounded">
                                "{{ $akreditasi->catatan_sekretaris }}"
                            </div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <p class="small text-muted mb-1">Pengesah (Ketua)</p>
                        <strong class="{{ $akreditasi->ketua ? 'text-success' : 'text-danger' }}">
                            {{ $akreditasi->ketua->name ?? 'Belum disahkan' }}
                        </strong>
                        @if ($akreditasi->grade_akhir)
                            <div class="mt-2">
                                <span class="badge badge-dark h5 px-3 py-1">GRADE {{ $akreditasi->grade_akhir }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                @if (auth()->user()->hasAnyRole(['sekretaris_pac', 'Sekretaris PAC']) &&
                        in_array($akreditasi->status, ['Menunggu Penilaian PAC', 'Menunggu Review']))
                    <form action="{{ route('akreditasi.review', $akreditasi->id) }}" method="POST">
                        @csrf
                        <div class="card shadow border-left-info bg-light">
                            <div class="card-body">
                                <label class="font-weight-bold text-info"><i class="fas fa-edit"></i> Form Verifikasi
                                    Sekretaris:</label>
                                <textarea name="catatan_sekretaris" class="form-control" rows="3"
                                    placeholder="Tulis catatan kelengkapan dokumen untuk bahan pertimbangan Ketua..." required></textarea>
                                <button type="submit" class="btn btn-info mt-3"><i class="fas fa-check"></i> Simpan &
                                    Teruskan ke Ketua</button>
                            </div>
                        </div>
                    </form>
                @endif

                <!-- ASISTEN AI (Khusus Ketua) -->
                @if (auth()->user()->hasAnyRole(['ketua_pac', 'Ketua PAC']) && $akreditasi->status == 'Menunggu Finalisasi Ketua')
                    <div class="card shadow border-left-primary mb-4">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-robot"></i> Asisten AI Penilai
                            </h6>
                            <form action="{{ route('akreditasi.ai', $akreditasi->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <i class="fas fa-magic"></i> Analisis & Beri Rekomendasi Grade
                                </button>
                            </form>
                        </div>

                        @if (session('ai_recommendation'))
                            <div class="card-body bg-light">
                                <strong class="text-primary">Hasil Analisis AI:</strong>
                                <div class="mt-2 text-dark" style="white-space: pre-wrap;">
                                    {{ session('ai_recommendation') }}</div>
                            </div>
                        @endif
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger m-3">
                        <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
                    </div>
                @endif

                @if (auth()->user()->hasAnyRole(['ketua_pac', 'Ketua PAC']) && $akreditasi->status == 'Menunggu Finalisasi Ketua')
                    <form action="{{ route('akreditasi.finalisasi', $akreditasi->id) }}" method="POST">
                        @csrf
                        <div class="card shadow border-left-success bg-light">
                            <div class="card-body">
                                <label class="font-weight-bold text-success"><i class="fas fa-gavel"></i> Form Penilaian
                                    Akhir Ketua:</label>
                                <div class="row">
                                    <div class="col-md-3">
                                        <label>Berikan Grade:</label>
                                        <select name="grade_akhir" class="form-control font-weight-bold" required>
                                            <option value="">-- Pilih --</option>
                                            <option value="A">Grade A</option>
                                            <option value="B">Grade B</option>
                                            <option value="C">Grade C</option>
                                        </select>
                                    </div>
                                    <div class="col-md-9">
                                        <label>Catatan Pengesahan / Rekomendasi:</label>
                                        <textarea name="catatan_pac" class="form-control" rows="2"
                                            placeholder="Tuliskan evaluasi akhir untuk Ranting..." required></textarea>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-success mt-3"><i class="fas fa-stamp"></i> Sahkan
                                    Nilai Akreditasi</button>
                            </div>
                        </div>
                    </form>
                @endif

            </div>
        </div>
    </div>
@endsection
