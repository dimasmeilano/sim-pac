@extends('layouts.adminlte')

@section('content')
    <div class="container-fluid">

        <!-- Header & Tombol Kembali -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Detail Event Makesta</h1>
            <a href="{{ route('makesta-event.index') }}" class="btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50 mr-1"></i> Kembali ke Daftar
            </a>
        </div>

        <div class="row">
            <!-- KOLOM KIRI: Informasi Event -->
            <div class="col-lg-8">
                <div class="card shadow mb-4 border-left-success">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-light">
                        <h6 class="m-0 font-weight-bold text-success"><i class="fas fa-info-circle mr-2"></i>Informasi
                            Kegiatan</h6>

                        <!-- Badge Status -->
                        @if ($event->status == 'Menunggu Verifikasi')
                            <span class="badge badge-warning px-3 py-2" style="font-size: 14px;">Menunggu Verifikasi</span>
                        @elseif($event->status == 'Disetujui')
                            <span class="badge badge-primary px-3 py-2" style="font-size: 14px;"><i
                                    class="fas fa-check-circle mr-1"></i> Disetujui</span>
                        @endif
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <th width="30%" class="text-muted">Penyelenggara</th>
                                <td class="font-weight-bold h5 text-dark">
                                    {{ $event->organization->name ?? 'Tidak Diketahui' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Tema Makesta</th>
                                <td>{{ $event->tema }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Lokasi</th>
                                <td>{{ $event->lokasi }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Waktu Pelaksanaan</th>
                                <td>
                                    {{ \Carbon\Carbon::parse($event->tgl_mulai)->translatedFormat('d F Y') }}
                                    <strong>s/d</strong>
                                    {{ \Carbon\Carbon::parse($event->tgl_selesai)->translatedFormat('d F Y') }}
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted">Biaya / Infaq</th>
                                <td>{{ $event->biaya ?? 'Gratis / Tidak disebutkan' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Narahubung (WA)</th>
                                <td>{{ $event->contact_person ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Fasilitas</th>
                                <td>{{ $event->fasilitas ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Persyaratan</th>
                                <td>{{ $event->persyaratan ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <!-- PANEL MATERI & MAGIC LINK INSTRUKTUR -->
                <div class="card shadow mb-4 border-left-info mt-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-light">
                        <h6 class="m-0 font-weight-bold text-info"><i class="fas fa-book-open mr-2"></i>Jadwal Materi &
                            Instruktur PC</h6>
                        <!-- Tombol Tambah hanya muncul jika Event sudah disetujui -->
                        @if ($event->status == 'Disetujui' || $event->status == 'Berjalan')
                            <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#modalTambahMateri">
                                <i class="fas fa-plus mr-1"></i> Tambah Materi
                            </button>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                                <thead class="bg-primary text-white text-center">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Nama Materi</th>
                                        <th>Pemateri / Narasumber</th> {{-- Tambahan Baru --}}
                                        <th>Instruktur PC</th>
                                        <th>Waktu Materi</th>
                                        <th>PIN Instruktur</th>
                                        <th width="15%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($event->materis as $index => $materi)
                                        <tr>
                                            <td class="text-center align-middle">{{ $index + 1 }}</td>
                                            <td class="font-weight-bold align-middle">{{ $materi->nama_materi }}</td>

                                            <td class="align-middle text-dark">{{ $materi->nama_pemateri ?? '-' }}</td>

                                            <td class="align-middle">
                                                <span
                                                    class="font-weight-bold text-success d-block mb-1">{{ $materi->nama_instruktur }}</span>

                                                <button
                                                    class="btn btn-sm btn-outline-success py-0 px-2 font-weight-bold shadow-sm"
                                                    style="font-size: 11px; border-radius: 50px;"
                                                    onclick="copyLinkInstruktur('{{ route('instruktur.penilaian', $materi->token_rahasia) }}', '{{ $materi->nama_instruktur }}')">
                                                    <i class="fas fa-copy mr-1"></i> Copy Magic Link
                                                </button>
                                            </td>

                                            <td class="text-center align-middle">
                                                {{ \Carbon\Carbon::parse($materi->waktu_materi)->translatedFormat('d M Y, H:i') }}
                                                WIB
                                            </td>
                                            <td class="text-center align-middle">
                                                <span class="badge badge-dark p-2"
                                                    style="font-size: 13px; letter-spacing: 1px;">
                                                    <i class="fas fa-key mr-1 text-warning"></i>
                                                    {{ $materi->pin_instruktur }}
                                                </span>
                                            </td>
                                            <td class="text-center align-middle">
                                                <!-- Tombol Hapus Materi (Hanya ini yang kita butuhkan) -->
                                                <form action="{{ route('makesta-materi.destroy', $materi->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger btn-circle"
                                                        onclick="return confirm('Yakin ingin menghapus materi ini?')"
                                                        title="Hapus Materi">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4 text-muted">
                                                <i class="fas fa-calendar-times fa-2x mb-2 d-block"></i> Belum ada jadwal
                                                materi yang ditambahkan.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>


            <!-- KOLOM KANAN: Panel Aksi & Verifikasi -->
            <div class="col-lg-4">
                <!-- Panel Verifikasi (Membaca Proposal Ranting) -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-light">
                        <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-tasks mr-2"></i>Aksi & Verifikasi</h6>
                    </div>
                    <div class="card-body text-center">

                        <!-- Cek Apakah Ada Proposal -->
                        @if ($event->berkas_proposal)
                            <p class="small text-muted mb-2">Penyelenggara melampirkan berkas permohonan/proposal.</p>
                            <a href="{{ asset('storage/' . $event->berkas_proposal) }}" target="_blank"
                                class="btn btn-outline-danger btn-block mb-3">
                                <i class="fas fa-file-pdf mr-1"></i> Lihat Berkas Proposal
                            </a>
                        @else
                            <p class="small text-muted mb-3">Tidak ada berkas proposal yang dilampirkan.</p>
                        @endif

                        <hr>

                        <!-- Tombol Verifikasi (HANYA UNTUK PAC) -->
                        <!-- Menggunakan fitur bawaan Spatie Laravel Permission -->
                        @if (auth()->user()->hasRole('sekretaris_pac') || auth()->user()->hasRole('super_admin'))
                            <!-- KODE SEMENTARA UNTUK CEK ROLE -->
                            @if ($event->status == 'Menunggu Verifikasi')
                                <form action="{{ route('makesta-event.verifikasi', $event->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-block font-weight-bold"
                                        onclick="return confirm('Apakah Anda yakin dokumen sudah lengkap dan menyetujui event ini?')">
                                        <i class="fas fa-check-circle mr-1"></i> Verifikasi & Sahkan Event
                                    </button>
                                </form>
                            @else
                                <div class="alert alert-success mb-0 py-2">
                                    <i class="fas fa-check-circle mr-1"></i> Event telah disahkan.
                                </div>
                            @endif
                        @else
                            <!-- TAMPILAN UNTUK RANTING / PENGUSUL -->
                            @if ($event->status == 'Menunggu Verifikasi')
                                <div class="alert alert-warning mb-0 py-2 border-warning">
                                    <i class="fas fa-hourglass-half mr-1"></i> Sedang ditinjau oleh PAC...
                                </div>
                                <small class="text-muted d-block mt-2">Harap tunggu Sekretaris PAC memverifikasi proposal
                                    Anda.</small>
                            @else
                                <div class="alert alert-success mb-0 py-2">
                                    <i class="fas fa-check-circle mr-1"></i> Event Anda Telah Disetujui PAC!
                                </div>
                            @endif
                        @endif

                        <!-- Panel Pendaftaran Publik -->
                        @if ($event->status == 'Disetujui' || $event->status == 'Berjalan')
                            <div class="card shadow mb-4">
                                <div class="card-header py-3 bg-primary text-white">
                                    <h6 class="m-0 font-weight-bold"><i class="fas fa-link mr-2"></i>Link Pendaftaran</h6>
                                </div>
                                <div class="card-body">
                                    <p class="small text-muted mb-2">Sebarkan link ini ke calon peserta Makesta:</p>
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control bg-light"
                                            value="{{ url('/makesta/daftar/' . $event->id) }}" readonly id="linkDaftar">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button" onclick="copyLink()"
                                                title="Salin Link">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Materi -->
    <div class="modal fade" id="modalTambahMateri" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('makesta-materi.store', $event->id) }}" method="POST">
                    @csrf
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title font-weight-bold"><i class="fas fa-plus-circle mr-1"></i> Tambah Jadwal
                            Materi</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="font-weight-bold">Nama Materi <span class="text-danger">*</span></label>
                            <input type="text" name="nama_materi" class="form-control"
                                placeholder="Contoh: Ke-NU-an / Ke-IPNU-an" required>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Nama Pemateri / Narasumber</label>
                            <input type="text" name="nama_pemateri" class="form-control shadow-sm"
                                placeholder="Contoh: Dr. KH. Ahmad Fauzi" required>
                            <small class="text-muted">Orang yang menyampaikan materi di kelas.</small>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Nama Instruktur Pendamping (PC)</label>
                            <input type="text" name="nama_instruktur" class="form-control shadow-sm"
                                placeholder="Contoh: Rekan Dimas" required>
                            <small class="text-muted">Instruktur PC yang mengawal forum & memegang PIN penilaian.</small>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Waktu Pelaksanaan <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="waktu_materi" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-info font-weight-bold"><i class="fas fa-magic mr-1"></i>
                            Simpan & Buat Link</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Script Copy Link Instruktur -->
    <script>
        function copyLinkInstruktur(url, namaInstruktur) {
            // Membuat elemen teks tiruan secara instan
            var el = document.createElement('textarea');
            el.value = url;
            el.setAttribute('readonly', '');
            el.style.position = 'absolute';
            el.style.left = '-9999px';
            document.body.appendChild(el);

            // Pilih dan copy teksnya
            el.select();
            document.execCommand('copy');
            document.body.removeChild(el);

            // Tampilkan notifikasi sukses yang informatif
            alert("✅ Magic Link untuk " + namaInstruktur +
                " berhasil di-copy!\n\nSilakan langsung paste (tempel) ke WhatsApp Instruktur yang bersangkutan.");
        }
    </script>

    <!-- Script Copy Link -->
    <script>
        function copyLink() {
            var copyText = document.getElementById("linkDaftar");
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            document.execCommand("copy");
            alert("Link pendaftaran berhasil disalin: " + copyText.value);
        }
    </script>
@endsection
