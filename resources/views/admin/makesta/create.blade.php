@extends('layouts.adminlte') {{-- Sesuaikan dengan layout admin Anda --}}

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Buat Event Makesta Baru</h1>
        </div>
        <div class="card shadow mb-4 border-bottom-success">
            <div class="card-header py-3 bg-light">
                <h6 class="m-0 font-weight-bold text-success">Formulir Pendaftaran Event</h6>
            </div>
            <div class="card-body">
                <!-- PENDETEKSI ERROR VALIDASI -->
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <h6 class="font-weight-bold mb-1"><i class="fas fa-exclamation-triangle mr-2"></i>Oops! Ada isian
                            yang kurang tepat:</h6>
                        <ul class="mb-0 pl-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                <!-- Tambahkan enctype agar form bisa menerima file upload -->
                <form action="{{ route('makesta-event.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <!-- Kolom Kiri -->
                        <div class="col-md-6">

                            <div class="form-group">
                                <label class="font-weight-bold">Tema Makesta <span class="text-danger">*</span></label>
                                <input type="text" name="tema" class="form-control"
                                    placeholder="Contoh: Mencetak Kader Militan di Era Digital" required>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Lokasi Pelaksanaan <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="lokasi" class="form-control"
                                    placeholder="Contoh: MI Nahdlatul Ulama, Gresik" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label class="font-weight-bold">Biaya Infaq</label>
                                    <input type="text" name="biaya" class="form-control"
                                        placeholder="Misal: Rp 20.000 / Gratis">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="font-weight-bold">Narahubung (WA)</label>
                                    <input type="text" name="contact_person" class="form-control"
                                        placeholder="Misal: 0812... (Fulan)">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Fasilitas Peserta</label>
                                <textarea name="fasilitas" class="form-control" rows="2" placeholder="Sertifikat, ID Card, Konsumsi, dll"></textarea>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Persyaratan Khusus</label>
                                <textarea name="persyaratan" class="form-control" rows="2"
                                    placeholder="Membawa surat rekomendasi, pas foto 3x4, dll"></textarea>
                            </div>
                        </div>

                        <!-- Kolom Kanan -->
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label class="font-weight-bold">Tanggal Mulai <span class="text-danger">*</span></label>
                                    <input type="date" name="tgl_mulai" class="form-control" required>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="font-weight-bold">Tanggal Selesai <span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="tgl_selesai" class="form-control" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Kuota Peserta</label>
                                <input type="number" name="kuota_peserta" class="form-control"
                                    placeholder="Kosongkan jika tidak dibatasi">
                                <small class="text-muted">Jumlah maksimal peserta yang bisa mendaftar.</small>
                            </div>

                            <div class="form-group border p-3 bg-light" style="border-radius: 8px;">
                                <label class="font-weight-bold">Upload Proposal (Khusus Ranting)</label>
                                <input type="file" name="berkas_proposal" class="form-control-file" accept=".pdf">
                                <small class="text-danger d-block mt-1">* Format PDF, Maksimal 2MB. Diabaikan jika
                                    penyelenggara adalah PAC.</small>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <div class="text-right">
                        <button type="reset" class="btn btn-secondary mr-2">Reset</button>
                        <button type="submit" class="btn btn-success"><i class="fas fa-save mr-1"></i> Simpan
                            Event</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
