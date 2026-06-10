@extends('layouts.adminlte') {{-- Sesuaikan jika nama file layout Anda berbeda --}}

@section('title', 'Buat LPJ - ' . $progja->nama)
@section('page-title', 'Buat Laporan Pertanggungjawaban')

@section('content')
    <div class="card shadow border-0">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
            <h4 class="font-weight-bold text-dark mb-0">{{ $progja->nama }}</h4>
            <p class="text-muted">Isi formulir narasi di bawah ini. Sistem akan menggabungkannya dengan data absensi dan
                keuangan secara otomatis.</p>
        </div>

        <form action="{{ route('lpj.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="program_kerja_id" value="{{ $progja->id }}">

            <div class="card-body">
                {{-- DATA SINGKAT --}}
                <h5 class="text-primary font-weight-bold border-bottom pb-2 mb-3">1. Data Kegiatan & Panitia</h5>
                <div class="row">
                    <div class="col-md-12 form-group">
                        <label>Tema Kegiatan</label>
                        <input type="text" name="tema_kegiatan" class="form-control"
                            placeholder='Contoh: "Membangun Khidmah..."' required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Tempat Pelaksanaan</label>
                        <input type="text" name="tempat_kegiatan" class="form-control"
                            placeholder="Cth: Gedung NU Lantai 2">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Jam Pelaksanaan</label>
                        <input type="text" name="jam_kegiatan" class="form-control"
                            placeholder="Cth: 08.00 WIB - Selesai">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Nama Ketua Panitia <span class="text-danger">*</span></label>
                        <input type="text" name="nama_ketua_panitia" class="form-control" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Nama Sekretaris Panitia <span class="text-danger">*</span></label>
                        <input type="text" name="nama_sekretaris" class="form-control" required>
                    </div>
                </div>

                {{-- NARASI PANJANG --}}
                <h5 class="text-primary font-weight-bold border-bottom pb-2 mt-4 mb-3">2. Narasi Laporan</h5>
                <div class="form-group">
                    <label>Latar Belakang</label>
                    <textarea name="latar_belakang" rows="4" class="form-control tinymce-editor"
                        placeholder="Tulis latar belakang kegiatan..."></textarea>
                </div>
                <div class="form-group">
                    <label>Dasar Pelaksanaan</label>
                    <textarea name="dasar_pelaksanaan" rows="3" class="form-control tinymce-editor"
                        placeholder="1. Peraturan Dasar... &#10;2. Program Kerja..."></textarea>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Tujuan Kegiatan</label>
                        <textarea name="tujuan_kegiatan" rows="4" class="form-control tinymce-editor"></textarea>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Output Kegiatan</label>
                        <textarea name="output_kegiatan" rows="4" class="form-control tinymce-editor"></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Materi Kegiatan</label>
                        <textarea name="materi_kegiatan" rows="4" class="form-control tinymce-editor"></textarea>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Hambatan & Harapan</label>
                        <textarea name="hambatan_harapan" rows="4" class="form-control tinymce-editor"
                            placeholder="Sebutkan kendala dan solusi untuk kegiatan mendatang..."></textarea>
                    </div>
                </div>

                {{-- UPLOAD LAMPIRAN --}}
                <h5 class="text-primary font-weight-bold border-bottom pb-2 mt-4 mb-3">3. Upload Gambar Lampiran</h5>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Unggah lampiran dalam format gambar <strong>(JPG/PNG)</strong> agar
                    bisa dicetak langsung ke dalam PDF.
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Lampiran II: Susunan Panitia (Foto/Screenshot)</label>
                        <input type="file" name="file_lampiran_panitia" class="form-control-file" accept="image/*">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Lampiran III: Susunan Acara (Foto/Screenshot)</label>
                        <input type="file" name="file_lampiran_acara" class="form-control-file" accept="image/*">
                    </div>
                </div>

                {{-- PILIH DOKUMENTASI --}}
                <h5 class="text-primary font-weight-bold border-bottom pb-2 mt-4 mb-3">
                    4. Lampiran V: Pilih Dokumentasi (Maks. 4 Foto)
                    <span id="counterFoto" class="badge badge-success ml-2">0/4 Terpilih</span>
                </h5>

                <div class="row bg-light p-3 rounded border">
                    @php $adaFoto = false; @endphp
                    @foreach ($progja->kegiatans as $keg)
                        @foreach ($keg->folders as $folder)
                            @foreach ($folder->galeris as $foto)
                                @if (in_array(strtolower(pathinfo($foto->file_path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png']))
                                    @php $adaFoto = true; @endphp
                                    <div class="col-md-3 col-6 mb-3">
                                        <label class="w-100 m-0" style="cursor: pointer;">
                                            <div class="card h-100 border-0 shadow-sm photo-card"
                                                id="card_{{ $foto->id }}">
                                                <img src="{{ asset('storage/' . $foto->file_path) }}" class="card-img-top"
                                                    style="height: 150px; object-fit: cover;">
                                                <div class="card-body p-2 text-center bg-white">
                                                    <input type="checkbox" name="foto_dokumentasi_terpilih[]"
                                                        value="{{ $foto->file_path }}" class="foto-checkbox d-none"
                                                        id="chk_{{ $foto->id }}"
                                                        onchange="togglePhoto('{{ $foto->id }}')">
                                                    <i class="fas fa-check-circle text-success d-none fa-2x position-absolute"
                                                        style="top: 10px; right: 10px;" id="icon_{{ $foto->id }}"></i>
                                                    <small class="text-muted">{{ $keg->nama }}</small>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                @endif
                            @endforeach
                        @endforeach
                    @endforeach

                    @if (!$adaFoto)
                        <div class="col-12 text-center py-4">
                            <i class="fas fa-images fa-3x text-muted mb-2"></i>
                            <p class="text-muted">Tidak ada foto di galeri. Pastikan Anda sudah mengunggah foto kegiatan di
                                menu Galeri Workspace.</p>
                        </div>
                    @endif
                </div>

            </div>
            <div class="card-footer bg-white text-right">
                <a href="{{ route('progja.index') }}" class="btn btn-secondary mr-2">Batal</a>
                <button type="submit" class="btn btn-primary font-weight-bold"><i class="fas fa-save mr-1"></i> Simpan
                    LPJ</button>
            </div>
        </form>
    </div>

    @push('scripts')
        <script src="https://cdn.tiny.cloud/1/790oy87uninpb887jjodfajw2ivcmbi6dq4vzah5gguz6igm/tinymce/6/tinymce.min.js"
            referrerpolicy="origin"></script>
        {{-- 2. INISIALISASI TINYMCE --}}
        <script>
            tinymce.init({
                selector: 'textarea.tinymce-editor', // Targetkan class yang kita buat
                height: 250, // Tinggi kotak
                menubar: false, // Sembunyikan menu bar agar terlihat bersih
                plugins: [
                    'advlist', 'autolink', 'lists', 'link', 'charmap', 'preview',
                    'searchreplace', 'visualblocks', 'code', 'fullscreen',
                    'insertdatetime', 'table', 'wordcount'
                ],
                toolbar: 'undo redo | blocks | bold italic underline | ' +
                    'alignleft aligncenter alignright alignjustify | ' +
                    'bullist numlist outdent indent | removeformat | help',
                content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial; font-size: 14px; }',
                branding: false // Sembunyikan tulisan "Powered by TinyMCE"
            });
        </script>
        <script>
            // Logika pembatasan maksimal 4 foto
            let maxPhotos = 4;

            function togglePhoto(id) {
                let checkbox = document.getElementById('chk_' + id);
                let card = document.getElementById('card_' + id);
                let icon = document.getElementById('icon_' + id);

                let checkedCount = $('.foto-checkbox:checked').length;

                // Jika user mencoba mencentang foto ke-5
                if (checkedCount > maxPhotos) {
                    checkbox.checked = false; // Batalkan centang
                    alert('Maksimal hanya 4 foto yang dapat dipilih untuk masuk ke laporan PDF!');
                    return;
                }

                // Update UI Card
                if (checkbox.checked) {
                    card.classList.add('border-primary', 'bg-primary');
                    card.style.borderWidth = '3px';
                    icon.classList.remove('d-none');
                } else {
                    card.classList.remove('border-primary', 'bg-primary');
                    card.style.borderWidth = '0';
                    icon.classList.add('d-none');
                }

                // Update Counter Badge
                document.getElementById('counterFoto').innerText = checkedCount + '/' + maxPhotos + ' Terpilih';

                if (checkedCount == maxPhotos) {
                    document.getElementById('counterFoto').classList.replace('badge-success', 'badge-warning');
                } else {
                    document.getElementById('counterFoto').classList.replace('badge-warning', 'badge-success');
                }
            }
        </script>
    @endpush
@endsection
