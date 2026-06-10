@extends('layouts.adminlte')

@section('title', 'Edit Notulensi')
@section('page-title', 'Edit Notulensi Rapat')

@section('content')
    <div class="card card-warning shadow-sm">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-edit mr-1"></i> Form Edit Notulensi</h3>
        </div>
        <form action="{{ route('notulensi.update', $notulensi) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">
                {{-- INJEKSI DROPDOWN KHUSUS SUPER ADMIN --}}
                @if (auth()->user()->hasRole('super_admin'))
                    <div class="col-md-12 mb-3">
                        <div class="form-group border p-3 bg-light rounded border-warning">
                            <label class="text-primary"><i class="fas fa-sitemap"></i> Pindah Kepemilikan Notulensi (Khusus
                                Super Admin)</label>
                            <select name="organization_id" class="form-control select2" required>
                                @if (isset($organizations))
                                    @foreach ($organizations as $org)
                                        <option value="{{ $org->id }}"
                                            {{ old('organization_id', $notulensi->organization_id) == $org->id ? 'selected' : '' }}>
                                            {{ $org->nama ?? $org->name }} ({{ strtoupper($org->type ?? 'PAC') }})
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                @endif
                {{-- AKHIR INJEKSI --}}
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Agenda / Topik Rapat <span class="text-danger">*</span></label>
                            <input type="text" name="agenda" class="form-control"
                                value="{{ old('agenda', $notulensi->agenda) }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tautkan ke Kegiatan <span class="text-muted">(Opsional)</span></label>
                            <select name="kegiatan_id" class="form-control select2">
                                <option value="">-- Tidak Ditautkan (Rapat Berdiri Sendiri) --</option>
                                @foreach ($kegiatan as $keg)
                                    <option value="{{ $keg->id }}"
                                        {{ old('kegiatan_id', $notulensi->kegiatan_id) == $keg->id ? 'selected' : '' }}>
                                        {{ $keg->nama }} ({{ $keg->tgl_mulai->format('d/m/Y') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row border-top pt-3 mt-1">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Tanggal Rapat <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" class="form-control"
                                value="{{ old('tanggal', $notulensi->tanggal->format('Y-m-d')) }}" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Waktu Mulai</label>
                            <input type="time" name="waktu_mulai" class="form-control"
                                value="{{ old('waktu_mulai', $notulensi->waktu_mulai ? \Carbon\Carbon::parse($notulensi->waktu_mulai)->format('H:i') : '') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Waktu Selesai</label>
                            <input type="time" name="waktu_selesai" class="form-control"
                                value="{{ old('waktu_selesai', $notulensi->waktu_selesai ? \Carbon\Carbon::parse($notulensi->waktu_selesai)->format('H:i') : '') }}">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Tempat Rapat <span class="text-danger">*</span></label>
                            <input type="text" name="tempat" class="form-control"
                                value="{{ old('tempat', $notulensi->tempat) }}" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Pemimpin Rapat <span class="text-danger">*</span></label>
                            <input type="text" name="pemimpin_rapat" class="form-control"
                                value="{{ old('pemimpin_rapat', $notulensi->pemimpin_rapat) }}" required>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="form-group">
                    <label>Isi Pembahasan / Risalah Rapat <span class="text-danger">*</span></label>
                    <textarea name="pembahasan" class="form-control tinyMceEditor" rows="15">{{ old('pembahasan', $notulensi->pembahasan) }}</textarea>
                </div>

                <div class="form-group">
                    <label>Kesimpulan / Keputusan Rapat</label>
                    <textarea name="kesimpulan" class="form-control tinyMceEditor" rows="6">{{ old('kesimpulan', $notulensi->kesimpulan) }}</textarea>
                </div>

            </div>
            <div class="card-footer bg-light text-right">
                <a href="{{ route('notulensi.index') }}" class="btn btn-secondary mr-2">Batal</a>
                <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Update Notulensi</button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.tiny.cloud/1/790oy87uninpb887jjodfajw2ivcmbi6dq4vzah5gguz6igm/tinymce/6/tinymce.min.js"
        referrerpolicy="origin"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            tinymce.init({
                selector: '.tinyMceEditor',
                height: 400,
                menubar: false,
                plugins: 'lists link table code',
                toolbar: 'undo redo | bold italic underline | alignleft aligncenter alignright justify | bullist numlist | table | removeformat',
                content_style: 'body { font-family: "Times New Roman", Times, serif; font-size: 15px; }'
            });
        });
    </script>
@endpush
