@extends('layouts.adminlte')

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Edit Event Makesta</h1>
            <a href="{{ route('makesta-event.index') }}" class="btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50 mr-1"></i> Kembali
            </a>
        </div>

        <div class="card shadow mb-4 border-bottom-warning">
            <div class="card-header py-3 bg-light">
                <h6 class="m-0 font-weight-bold text-warning">Formulir Edit Event</h6>
            </div>
            <div class="card-body">
                <!-- Form Edit Membutuhkan Method PUT -->
                <form action="{{ route('makesta-event.update', $event->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <!-- Kolom Kiri -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Tema Makesta <span class="text-danger">*</span></label>
                                <input type="text" name="tema" class="form-control" value="{{ $event->tema }}"
                                    required>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Lokasi Pelaksanaan <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="lokasi" class="form-control" value="{{ $event->lokasi }}"
                                    required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label class="font-weight-bold">Biaya Infaq</label>
                                    <input type="text" name="biaya" class="form-control" value="{{ $event->biaya }}">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="font-weight-bold">Narahubung (WA)</label>
                                    <input type="text" name="contact_person" class="form-control"
                                        value="{{ $event->contact_person }}">
                                </div>
                            </div>
                        </div>

                        <!-- Kolom Kanan -->
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label class="font-weight-bold">Tanggal Mulai <span class="text-danger">*</span></label>
                                    <input type="date" name="tgl_mulai" class="form-control"
                                        value="{{ $event->tgl_mulai }}" required>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="font-weight-bold">Tanggal Selesai <span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="tgl_selesai" class="form-control"
                                        value="{{ $event->tgl_selesai }}" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Kuota Peserta</label>
                                <input type="number" name="kuota_peserta" class="form-control"
                                    value="{{ $event->kuota_peserta }}">
                            </div>

                            <div class="form-group border p-3 bg-light" style="border-radius: 8px;">
                                <label class="font-weight-bold">Update Proposal Baru?</label>
                                <input type="file" name="berkas_proposal" class="form-control-file" accept=".pdf">
                                <small class="text-muted d-block mt-1">* Kosongkan jika tidak ingin mengubah proposal yang
                                    sudah diupload.</small>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">Fasilitas Peserta</label>
                            <textarea name="fasilitas" class="form-control" rows="2">{{ $event->fasilitas }}</textarea>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">Persyaratan Khusus</label>
                            <textarea name="persyaratan" class="form-control" rows="2">{{ $event->persyaratan }}</textarea>
                        </div>
                    </div>

                    <hr>
                    <div class="text-right">
                        <button type="submit" class="btn btn-warning font-weight-bold"><i class="fas fa-save mr-1"></i>
                            Update Event</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
