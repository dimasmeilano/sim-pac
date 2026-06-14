@extends('layouts.public') {{-- Sesuaikan nama layout publik Anda --}}

{{-- Jika layout Anda mendukung @stack('styles') atau @yield('styles') --}}
@push('styles')
    <style>
        .bg-nu {
            background-color: #00723b !important;
            color: white;
        }

        .text-nu {
            color: #00723b !important;
        }

        .btn-nu {
            background-color: #00723b;
            color: white;
            border: none;
        }

        .btn-nu:hover {
            background-color: #005a2e;
            color: white;
        }

        .card-shadow {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
@endpush

@section('content')
    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <!-- Banner & Info Event -->
                <div class="card card-shadow mb-4 border-0">
                    <div class="card-body bg-nu rounded-top text-center py-4">
                        <h2 class="font-weight-bold mb-1 text-white">Formulir Pendaftaran MAKESTA</h2>
                        <h5 class="mb-0 text-white">{{ $event->organization->name ?? 'Pimpinan IPNU-IPPNU' }}</h5>
                    </div>
                    <div class="card-body bg-white rounded-bottom">
                        <h5 class="text-nu font-weight-bold">{{ $event->tema }}</h5>
                        <hr>
                        <div class="row small">
                            <div class="col-sm-6 mb-2">
                                <i class="far fa-calendar-alt text-nu mr-2"></i>
                                {{ \Carbon\Carbon::parse($event->tgl_mulai)->format('d M Y') }} s/d
                                {{ \Carbon\Carbon::parse($event->tgl_selesai)->format('d M Y') }}
                            </div>
                            <div class="col-sm-6 mb-2">
                                <i class="fas fa-map-marker-alt text-danger mr-2"></i> {{ $event->lokasi }}
                            </div>
                            <div class="col-sm-6 mb-2">
                                <i class="fas fa-money-bill-wave text-success mr-2"></i> Infaq:
                                {{ $event->biaya ?? 'Gratis' }}
                            </div>
                            <div class="col-sm-6 mb-2">
                                <i class="fab fa-whatsapp text-success mr-2"></i> WA: {{ $event->contact_person ?? '-' }}
                            </div>
                        </div>
                        @if ($event->persyaratan)
                            <div class="mt-3 p-3 bg-light rounded border">
                                <strong>Persyaratan Peserta:</strong><br>
                                {{ $event->persyaratan }}
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Pesan Sukses -->
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show card-shadow text-center py-4"
                        role="alert">
                        <i class="fas fa-check-circle fa-3x mb-3 text-success d-block"></i>
                        <h5 class="font-weight-bold text-dark">{{ session('success') }}</h5>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <!-- Pesan Error Validasi -->
                @if ($errors->any())
                    <div class="alert alert-danger card-shadow">
                        <strong>Mohon periksa kembali form Anda:</strong>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Form Pendaftaran -->
                @if (!session('success'))
                    <div class="card card-shadow border-0">
                        <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                            <h5 class="font-weight-bold text-dark"><i class="fas fa-user-edit mr-2 text-nu"></i> Data Diri
                                Calon Peserta</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('makesta.daftar.store', $event->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label class="font-weight-bold">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_lengkap" class="form-control"
                                        value="{{ old('nama_lengkap') }}" required
                                        placeholder="Masukkan nama lengkap sesuai ijazah/KTP">
                                </div>

                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold">Tempat Lahir <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="tempat_lahir" class="form-control"
                                            value="{{ old('tempat_lahir') }}" required>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold">Tanggal Lahir <span
                                                class="text-danger">*</span></label>
                                        <input type="date" name="tgl_lahir" class="form-control"
                                            value="{{ old('tgl_lahir') }}" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold">Jenis Kelamin <span
                                                class="text-danger">*</span></label>
                                        <select name="jenis_kelamin" class="form-control" required>
                                            <option value="">-- Pilih --</option>
                                            <option value="Laki-laki"
                                                {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki
                                            </option>
                                            <option value="Perempuan"
                                                {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold">Nomor WhatsApp <span
                                                class="text-danger">*</span></label>
                                        <input type="number" name="no_wa" class="form-control"
                                            value="{{ old('no_wa') }}" required placeholder="Contoh: 081234...">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-bold">Utusan / Delegasi (Asal Sekolah/Ranting) <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="utusan" class="form-control" value="{{ old('utusan') }}"
                                        required placeholder="Contoh: PR IPNU Desa Makmur / PK MA Al-Huda">
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-bold">Alamat Lengkap Domisili <span
                                            class="text-danger">*</span></label>
                                    <textarea name="alamat" class="form-control" rows="3" required placeholder="Jalan, RT/RW, Desa, Kecamatan">{{ old('alamat') }}</textarea>
                                </div>

                                <div class="form-group border p-3 rounded bg-light">
                                    <label class="font-weight-bold">Upload Berkas Persyaratan</label>
                                    <input type="file" name="berkas_syarat" class="form-control-file"
                                        accept=".pdf,.jpg,.jpeg,.png">
                                    <small class="text-muted d-block mt-2">Upload surat rekomendasi pimpinan/foto/bukti
                                        infaq sesuai instruksi panitia. (Format PDF/JPG/PNG, Maks 2MB).</small>
                                </div>

                                <button type="submit"
                                    class="btn btn-nu btn-block btn-lg mt-4 font-weight-bold shadow-sm">KIRIM PENDAFTARAN
                                    <i class="fas fa-paper-plane ml-2"></i></button>
                            </form>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
@endsection
