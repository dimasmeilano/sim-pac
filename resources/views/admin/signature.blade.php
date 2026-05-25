@extends('layouts.adminlte')

@section('title', 'Tanda Tangan Digital')
@section('page-title', 'Tanda Tangan Digital - ' . $organization->name)

@section('content')
    <div class="row">
        @if ($organization->ketua_id == auth()->id())
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title">Tanda Tangan Ketua {{ $organization->name }}</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="signatureCanvasKetua" width="400" height="200"
                            style="border:1px solid #ccc; background:white;"></canvas>
                        <div class="mt-2">
                            <button class="btn btn-danger btn-sm" onclick="clearSignature('ketua')">Clear</button>
                            <button class="btn btn-success btn-sm" onclick="saveSignature('ketua')">Simpan Tanda
                                Tangan</button>
                        </div>
                        @if ($organization->ttd_ketua)
                            <div class="mt-3">
                                <p>Tanda tangan saat ini:</p>
                                <img src="{{ asset('storage/' . $organization->ttd_ketua) }}" style="max-height: 80px;">
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        @if ($organization->sekretaris_id == auth()->id())
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h3 class="card-title">Tanda Tangan Sekretaris {{ $organization->name }}</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="signatureCanvasSekretaris" width="400" height="200"
                            style="border:1px solid #ccc; background:white;"></canvas>
                        <div class="mt-2">
                            <button class="btn btn-danger btn-sm" onclick="clearSignature('sekretaris')">Clear</button>
                            <button class="btn btn-success btn-sm" onclick="saveSignature('sekretaris')">Simpan Tanda
                                Tangan</button>
                        </div>
                        @if ($organization->ttd_sekretaris)
                            <div class="mt-3">
                                <p>Tanda tangan saat ini:</p>
                                <img src="{{ asset('storage/' . $organization->ttd_sekretaris) }}"
                                    style="max-height: 80px;">
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <script>
        let signaturePads = {};

        @if ($organization->ketua_id == auth()->id())
            const canvasKetua = document.getElementById('signatureCanvasKetua');
            signaturePads['ketua'] = new SignaturePad(canvasKetua, {
                backgroundColor: 'white',
                penColor: 'black'
            });
        @endif

        @if ($organization->sekretaris_id == auth()->id())
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
@endsection
