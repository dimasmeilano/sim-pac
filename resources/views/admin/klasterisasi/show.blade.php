@extends('layouts.adminlte')

@section('content')
    <div class="container-fluid">
        <!-- PASTI KAN BAGIAN INI DI UPDATE -->
        @php
            // Kita ambil jenis organisasi LANGSUNG dari data yang disimpan di database
            // Jika kolom kosong, kita fallback ke IPNU
            $jenis = $klasterisasi->jenis_organisasi ?? 'ipnu';
        @endphp

        <!-- JUDUL OTOMATIS -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 text-gray-800">
                <i class="fas fa-folder-open text-info"></i>
                Detail Klasterisasi ({{ $jenis }})
            </h1>
            @if ($klasterisasi->status == 'Selesai')
                <a href="{{ route('klasterisasi.cetak', $klasterisasi->id) }}" target="_blank"
                    class="btn btn-warning shadow-sm">
                    <i class="fas fa-print"></i> Cetak Sertifikat
                </a>
            @endif
            <a href="{{ route('klasterisasi.index') }}" class="btn btn-secondary shadow-sm"><i class="fas fa-arrow-left"></i>
                Kembali</a>
        </div>

        <!-- DATA BORANG -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-dark text-white font-weight-bold">
                Data Borang: {{ $klasterisasi->organization->name ?? 'Organisasi Tidak Diketahui' }}
            </div>
            <div class="card-body">

                @if ($jenis == 'ipnu')
                    <!-- DATA REVIEW IPNU -->
                    <h5 class="font-weight-bold text-success border-bottom pb-2"><i class="fas fa-users"></i> Parameter I:
                        Penduduk Muslim</h5>
                    <p>Pilihan Rentang: <strong>{{ $klasterisasi->penduduk_muslim ?? '-' }}%</strong> (Skor:
                        {{ $klasterisasi->skor_penduduk }})</p>

                    <h5 class="font-weight-bold text-success border-bottom pb-2 mt-4"><i class="fas fa-school"></i>
                        Parameter II: Pesantren & Lembaga NU</h5>
                    <p>Kategori: <strong>{{ str_replace('_', ' ', $klasterisasi->jumlah_pesantren ?? '') }}</strong> (Skor:
                        {{ $klasterisasi->skor_pesantren }})</p>

                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered table-sm small">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Nama Lembaga</th>
                                        <th>Alamat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($klasterisasi->p2_tabel_lembaga ?? [] as $lembaga)
                                        <tr>
                                            <td>{{ $lembaga['nama'] ?? '-' }}</td>
                                            <td>{{ $lembaga['alamat'] ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered table-sm small">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Nama Pesantren</th>
                                        <th>Alamat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($klasterisasi->p2_tabel_pesantren ?? [] as $pesantren)
                                        <tr>
                                            <td>{{ $pesantren['nama'] ?? '-' }}</td>
                                            <td>{{ $pesantren['alamat'] ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <!-- DATA REVIEW IPPNU (Form Pimpinan & Proker) -->
                    <h5 class="font-weight-bold text-warning border-bottom pb-2"><i class="fas fa-sitemap"></i> Parameter I:
                        Pimpinan Aktif IPPNU</h5>
                    <p>Persentase Keaktifan: <strong>{{ $klasterisasi->p1_persentase_aktif ?? 0 }}%</strong> (Skor:
                        {{ $klasterisasi->skor_penduduk }})</p>
                    <table class="table table-bordered table-sm small mb-4">
                        <thead class="bg-light">
                            <tr>
                                <th>Desa/Sekolah</th>
                                <th>Nama Pimpinan</th>
                                <th>Status SP</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($klasterisasi->p1_tabel_pimpinan ?? [] as $pim)
                                <tr>
                                    <td>{{ $pim['desa'] ?? '-' }}</td>
                                    <td>{{ $pim['nama'] ?? '-' }}</td>
                                    <td>{{ $pim['sp'] ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">Data kosong</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <h5 class="font-weight-bold text-warning border-bottom pb-2"><i class="fas fa-tasks"></i> Parameter II:
                        Program Kerja IPPNU</h5>
                    <p>Persentase Terlaksana: <strong>{{ $klasterisasi->p2_persentase_proker ?? 0 }}%</strong> (Skor:
                        {{ $klasterisasi->skor_pesantren }})</p>
                    <table class="table table-bordered table-sm small">
                        <thead class="bg-light">
                            <tr>
                                <th>Nama Proker</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($klasterisasi->p2_tabel_proker ?? [] as $proker)
                                <tr>
                                    <td>{{ $proker['nama_proker'] ?? '-' }}</td>
                                    <td>{{ $proker['status'] ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center">Data kosong</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                @endif

                <!-- PARAMETER 3 & 4 (SHARED) -->
                <h5 class="font-weight-bold text-info border-bottom pb-2 mt-4"><i class="fas fa-handshake"></i> Parameter
                    III: Stakeholder & Alumni</h5>
                <p>Tingkat Dukungan: <strong>{{ strtoupper($klasterisasi->dukungan_stakeholder) }}</strong></p>
                <table class="table table-bordered table-sm small mb-4">
                    <thead class="bg-light">
                        <tr>
                            <th>Kegiatan</th>
                            <th>Stakeholder</th>
                            <th>No Surat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($klasterisasi->p3_tabel_mou ?? [] as $mou)
                            <tr>
                                <td>{{ $mou['kegiatan'] }}</td>
                                <td>{{ $mou['stakeholder'] }}</td>
                                <td>{{ $mou['no_surat'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <h5 class="font-weight-bold text-info border-bottom pb-2 mt-4"><i class="fas fa-map-marked-alt"></i>
                    Parameter IV: Kondisi Geografis</h5>
                <p>Akses: <strong>{{ strtoupper($klasterisasi->kondisi_geografis) }}</strong></p>
                <p class="small text-muted mb-0">Infrastruktur: {{ $klasterisasi->p4_infrastruktur ?? '-' }}</p>
                <p class="small text-muted">Transportasi: {{ $klasterisasi->p4_transportasi ?? '-' }}</p>
                @if ($klasterisasi->p4_file_peta)
                    <img src="{{ asset('storage/' . $klasterisasi->p4_file_peta) }}"
                        class="img-fluid rounded border shadow-sm p-1" style="max-height: 150px;">
                @endif
            </div>
        </div>

        <!-- AUDIT TRAIL / JEJAK STATUS -->
        <h5 class="font-weight-bold mb-3"><i class="fas fa-history"></i> Status Penilaian PAC</h5>
        <div class="row text-center mb-5">
            <div class="col-md-6 border-right">
                <p class="small text-muted mb-1">Sekretaris</p>
                <strong
                    class="{{ $klasterisasi->sekretaris ? 'text-success' : 'text-danger' }}">{{ $klasterisasi->sekretaris->name ?? 'Belum diperiksa' }}</strong>
                @if ($klasterisasi->catatan_sekretaris)
                    <div class="mt-2 small bg-light p-2 rounded text-left">"{{ $klasterisasi->catatan_sekretaris }}"</div>
                @endif
            </div>
            <div class="col-md-6">
                <p class="small text-muted mb-1">Ketua PAC</p>
                <strong
                    class="{{ $klasterisasi->ketua ? 'text-success' : 'text-danger' }}">{{ $klasterisasi->ketua->name ?? 'Belum disahkan' }}</strong>
                @if ($klasterisasi->catatan_ketua)
                    <div class="mt-2 small bg-light p-2 rounded text-left">"{{ $klasterisasi->catatan_ketua }}"</div>
                @endif
            </div>
        </div>

        <!-- FORM SEKRETARIS PAC -->
        @if (auth()->user()->hasAnyRole(['sekretaris_pac', 'Sekretaris PAC']) &&
                in_array($klasterisasi->status, ['Menunggu Review Sekretaris', 'Revisi']))
            <form action="{{ route('klasterisasi.storeSekretaris', $klasterisasi->id) }}" method="POST">
                @csrf
                <div class="card shadow border-left-info mb-4 bg-light">
                    <div class="card-body">
                        <label class="font-weight-bold text-info"><i class="fas fa-search"></i> Form Review
                            Sekretaris:</label>
                        <div class="row">
                            <div class="col-md-3 form-group"><label>Tindakan:</label><select name="keputusan"
                                    class="form-control" required>
                                    <option value="Lanjut">Dokumen Valid</option>
                                    <option value="Revisi">Minta Revisi</option>
                                </select></div>
                            <div class="col-md-9 form-group"><label>Catatan Pengecekan:</label>
                                <textarea name="catatan_sekretaris" class="form-control" rows="2" required></textarea>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-info"><i class="fas fa-paper-plane"></i> Proses
                            Dokumen</button>
                    </div>
                </div>
            </form>
        @endif

        <!-- ASISTEN AI & KETUA PAC -->
        @if (auth()->user()->hasAnyRole(['ketua_pac', 'Ketua PAC']) && $klasterisasi->status == 'Menunggu Finalisasi Ketua')
            <!-- PANEL AI -->
            <div class="card shadow border-left-primary mb-4 bg-white">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-robot"></i> Asisten AI Gemini
                        (Anti-Manipulasi)</h6>
                    <form action="{{ route('klasterisasi.ai', $klasterisasi->id) }}" method="POST">
                        @csrf <button type="submit" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-magic"></i>
                            Cek Kesesuaian Fakta</button>
                    </form>
                </div>
                @if (session('ai_recommendation'))
                    <div class="card-body bg-light border-bottom text-dark">
                        <strong class="text-primary"><i class="fas fa-search"></i> Hasil AI:</strong>
                        <div class="mt-2" style="white-space: pre-wrap; font-size: 15px;">
                            {{ session('ai_recommendation') }}</div>
                    </div>
                @else
                    <div class="card-body text-muted small">Klik tombol di atas untuk menyuruh AI memverifikasi kesesuaian
                        tabel bukti dengan pilihan Ranting.</div>
                @endif
            </div>

            <!-- FORM KETUA PAC OVERRIDE -->
            <form action="{{ route('klasterisasi.storeKetua', $klasterisasi->id) }}" method="POST">
                @csrf
                <div class="card shadow border-left-success mb-4">
                    <div class="card-header bg-success text-white font-weight-bold"><i class="fas fa-gavel"></i> Penetapan
                        Nilai Final Ketua PAC</div>
                    <div class="card-body bg-light">
                        <p class="text-danger small mb-3"><i class="fas fa-exclamation-triangle"></i> Jika bukti tidak
                            valid, ubah nilai di bawah ini untuk menurunkan skor Ranting.</p>

                        <div class="row border-bottom pb-3 mb-3">
                            @if ($klasterisasi->jenis_organisasi == 'IPNU')
                                <div class="col-md-3 form-group"><label class="small">1. Penduduk Muslim</label><select
                                        name="penduduk_muslim" class="form-control form-control-sm">
                                        <option value="60-100"
                                            {{ $klasterisasi->penduduk_muslim == '60-100' ? 'selected' : '' }}>60-100%
                                        </option>
                                        <option value="20-59"
                                            {{ $klasterisasi->penduduk_muslim == '20-59' ? 'selected' : '' }}>20-59%
                                        </option>
                                        <option value="0-19"
                                            {{ $klasterisasi->penduduk_muslim == '0-19' ? 'selected' : '' }}>
                                            0-19%</option>
                                    </select></div>
                                <div class="col-md-3 form-group"><label class="small">2. Jml Lembaga</label><select
                                        name="jumlah_pesantren" class="form-control form-control-sm">
                                        <option value="lebih_3"
                                            {{ $klasterisasi->jumlah_pesantren == 'lebih_3' ? 'selected' : '' }}>> 3
                                        </option>
                                        <option value="2_sampai_3"
                                            {{ $klasterisasi->jumlah_pesantren == '2_sampai_3' ? 'selected' : '' }}>2-3
                                        </option>
                                        <option value="kurang_2"
                                            {{ $klasterisasi->jumlah_pesantren == 'kurang_2' ? 'selected' : '' }}>
                                            < 2</option>
                                    </select></div>
                            @else
                                <div class="col-md-3 form-group"><label class="small">1. % Keaktifan</label><input
                                        type="number" step="0.01" name="p1_persentase_aktif"
                                        class="form-control form-control-sm"
                                        value="{{ $klasterisasi->p1_persentase_aktif }}"></div>
                                <div class="col-md-3 form-group"><label class="small">2. % Proker</label><input
                                        type="number" step="0.01" name="p2_persentase_proker"
                                        class="form-control form-control-sm"
                                        value="{{ $klasterisasi->p2_persentase_proker }}"></div>
                            @endif
                            <div class="col-md-3 form-group"><label class="small">3. Stakeholder</label><select
                                    name="dukungan_stakeholder" class="form-control form-control-sm">
                                    <option value="kuat"
                                        {{ $klasterisasi->dukungan_stakeholder == 'kuat' ? 'selected' : '' }}>Kuat</option>
                                    <option value="sedang"
                                        {{ $klasterisasi->dukungan_stakeholder == 'sedang' ? 'selected' : '' }}>Sedang
                                    </option>
                                    <option value="lemah"
                                        {{ $klasterisasi->dukungan_stakeholder == 'lemah' ? 'selected' : '' }}>Lemah
                                    </option>
                                </select></div>
                            <div class="col-md-3 form-group"><label class="small">4. Geografis</label><select
                                    name="kondisi_geografis" class="form-control form-control-sm">
                                    <option value="mudah"
                                        {{ $klasterisasi->kondisi_geografis == 'mudah' ? 'selected' : '' }}>
                                        Mudah</option>
                                    <option value="sedang"
                                        {{ $klasterisasi->kondisi_geografis == 'sedang' ? 'selected' : '' }}>
                                        Sedang</option>
                                    <option value="sulit"
                                        {{ $klasterisasi->kondisi_geografis == 'sulit' ? 'selected' : '' }}>
                                        Sulit</option>
                                </select></div>
                        </div>

                        <div class="form-group"><label class="font-weight-bold text-success">Catatan Pengesahan
                                SK:</label>
                            <textarea name="catatan_ketua" class="form-control" rows="2" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-success"><i class="fas fa-stamp"></i> Sahkan
                            Kluster</button>
                    </div>
                </div>
            </form>
        @endif
    </div>
@endsection
