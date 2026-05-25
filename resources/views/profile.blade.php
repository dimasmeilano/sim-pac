@extends('layouts.adminlte')

@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <img class="profile-user-img img-fluid img-circle"
                            src="{{ Auth::user()->foto ?? asset('images/default-avatar.png') }}" alt="User profile picture">
                    </div>
                    <h3 class="profile-username text-center">{{ Auth::user()->name }}</h3>
                    <p class="text-muted text-center">
                        @foreach (Auth::user()->getRoleNames() as $role)
                            {{ ucfirst(str_replace('_', ' ', $role)) }}
                        @endforeach
                    </p>
                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>Email</b> <a class="float-right">{{ Auth::user()->email }}</a>
                        </li>
                        <li class="list-group-item">
                            <b>NIK</b> <a class="float-right">{{ Auth::user()->nik ?? '-' }}</a>
                        </li>
                        <li class="list-group-item">
                            <b>No HP</b> <a class="float-right">{{ Auth::user()->no_hp ?? '-' }}</a>
                        </li>
                    </ul>
                    <a href="{{ route('logout') }}" class="btn btn-danger btn-block"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <!-- Form Edit Profil -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Profil</h3>
                </div>
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="form-group">
                            <label>Nama Lengkap</label>
                            <input type="text" name="name" class="form-control"
                                value="{{ old('name', Auth::user()->name) }}" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control"
                                value="{{ old('email', Auth::user()->email) }}" required>
                        </div>
                        <div class="form-group">
                            <label>No HP</label>
                            <input type="text" name="no_hp" class="form-control"
                                value="{{ old('no_hp', Auth::user()->no_hp) }}">
                        </div>
                        <div class="form-group">
                            <label>Foto Profil</label>
                            <input type="file" name="foto" class="form-control" accept="image/*">
                            <small class="text-muted">Kosongkan jika tidak ingin mengubah foto</small>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Update Profil</button>
                    </div>
                </form>
            </div>

            <!-- ========== TANDA TANGAN DIGITAL ========== -->
            @php
                $org = Auth::user()->organization;
                $isKetua = $org && $org->ketua_id == Auth::id();
                $isSekretaris = $org && $org->sekretaris_id == Auth::id();
            @endphp

            @if ($isKetua || $isSekretaris)
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h3 class="card-title">
                            <i class="fas fa-signature"></i> Tanda Tangan Digital
                        </h3>
                    </div>
                    <div class="card-body">
                        @if ($isKetua)
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Tanda Tangan Ketua</h5>
                                    <canvas id="signatureCanvasKetua" width="350" height="150"
                                        style="border:1px solid #ccc; background:white;"></canvas>
                                    <div class="mt-2">
                                        <button class="btn btn-danger btn-sm"
                                            onclick="clearSignature('ketua')">Clear</button>
                                        <button class="btn btn-success btn-sm" onclick="saveSignature('ketua')">Simpan TTD
                                            Ketua</button>
                                    </div>
                                    @if ($org->ttd_ketua)
                                        <div class="mt-3">
                                            <p>TTD Saat Ini:</p>
                                            <img src="{{ asset('storage/' . $org->ttd_ketua) }}"
                                                style="max-height: 60px; border:1px solid #ccc;">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if ($isSekretaris)
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <h5>Tanda Tangan Sekretaris</h5>
                                    <canvas id="signatureCanvasSekretaris" width="350" height="150"
                                        style="border:1px solid #ccc; background:white;"></canvas>
                                    <div class="mt-2">
                                        <button class="btn btn-danger btn-sm"
                                            onclick="clearSignature('sekretaris')">Clear</button>
                                        <button class="btn btn-success btn-sm" onclick="saveSignature('sekretaris')">Simpan
                                            TTD Sekretaris</button>
                                    </div>
                                    @if ($org->ttd_sekretaris)
                                        <div class="mt-3">
                                            <p>TTD Saat Ini:</p>
                                            <img src="{{ asset('storage/' . $org->ttd_sekretaris) }}"
                                                style="max-height: 60px; border:1px solid #ccc;">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                        @if ($isKetua)
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <h5>Stempel Organisasi</h5>
                                    <input type="file" name="stempel" id="stempelInput" class="form-control"
                                        accept="image/*">
                                    <button class="btn btn-primary btn-sm mt-2" onclick="uploadStempel()">Upload
                                        Stempel</button>
                                    @if ($org->stempel)
                                        <div class="mt-2">
                                            <img src="{{ asset('storage/' . $org->stempel) }}" style="max-height: 80px;">
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <script>
                                function uploadStempel() {
                                    let file = document.getElementById('stempelInput').files[0];
                                    if (!file) {
                                        alert('Pilih file stempel terlebih dahulu');
                                        return;
                                    }

                                    let formData = new FormData();
                                    formData.append('stempel', file);
                                    formData.append('_token', '{{ csrf_token() }}');

                                    fetch('{{ route('signature.stempel') }}', {
                                            method: 'POST',
                                            body: formData
                                        })
                                        .then(response => response.json())
                                        .then(data => {
                                            if (data.success) {
                                                alert('Stempel berhasil diupload!');
                                                location.reload();
                                            } else {
                                                alert('Gagal: ' + data.message);
                                            }
                                        });
                                }
                            </script>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <script>
        let signaturePads = {};

        @if ($isKetua)
            const canvasKetua = document.getElementById('signatureCanvasKetua');
            signaturePads['ketua'] = new SignaturePad(canvasKetua, {
                backgroundColor: 'white',
                penColor: 'black'
            });
        @endif

        @if ($isSekretaris)
            const canvasSekretaris = document.getElementById('signatureCanvasSekretaris');
            signaturePads['sekretaris'] = new SignaturePad(canvasSekretaris, {
                backgroundColor: 'white',
                penColor: 'black'
            });
        @endif

        function clearSignature(role) {
            if (signaturePads[role]) {
                signaturePads[role].clear();
            }
        }

        function saveSignature(role) {
            const pad = signaturePads[role];
            if (!pad || pad.isEmpty()) {
                alert('Silakan gambar tanda tangan terlebih dahulu!');
                return;
            }

            const dataURL = pad.toDataURL('image/png');

            fetch('{{ route('signature.save') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        role: role,
                        signature: dataURL
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Tanda tangan berhasil disimpan!');
                        location.reload();
                    } else {
                        alert('Gagal menyimpan: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan');
                });
        }
    </script>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
@endsection
