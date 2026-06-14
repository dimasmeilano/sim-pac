@extends('layouts.public')

@section('title', 'Pelacakan Surat Resmi')

@section('content')
    <div class="container mt-4 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-lg border-0 rounded-lg">
                    {{-- HEADER CARD SERAGAM --}}
                    <div class="card-header bg-success text-white text-center py-4">
                        <h3 class="font-weight-bold mb-0"><i class="fas fa-search mr-2"></i> Lacak Dokumen Resmi</h3>
                        <p class="mb-0 mt-2 text-light">E-OFFICE Pimpinan Anak Cabang</p>
                    </div>

                    <div class="card-body p-4 p-md-5 text-center">
                        <p class="text-muted mb-4">Masukkan nomor surat untuk melacak status dan memvalidasi keaslian dokumen
                            di dalam sistem.</p>

                        <form id="formLacak" action="{{ route('verifikasi.surat') }}" method="GET"
                            onsubmit="event.preventDefault(); prosesLacak();">
                            <div class="form-group mb-4">
                                <input type="text" id="nomorInput"
                                    class="form-control form-control-lg text-center font-weight-bold text-success shadow-sm"
                                    placeholder="Contoh: 024/PAC/SRP/7354/XVI/V/26" style="border: 2px solid #28a745;"
                                    required autocomplete="off">
                                <input type="hidden" name="nomor" id="nomorHidden">
                            </div>
                            <button type="submit" class="btn btn-success btn-lg btn-block shadow-sm font-weight-bold">
                                <i class="fas fa-search mr-1"></i> Lacak Dokumen
                            </button>
                        </form>

                        <hr class="my-4">

                        <div class="text-muted small">
                            <i class="fas fa-qrcode fa-2x mb-2"></i><br>
                            Atau gunakan kamera <i>smartphone</i> Anda untuk men-<i>scan</i> QR Code yang tertera pada
                            bagian bawah surat yang dicetak.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function prosesLacak() {
            let inputText = document.getElementById('nomorInput').value;
            let base64Text = btoa(inputText);
            document.getElementById('nomorHidden').value = base64Text;
            document.getElementById('formLacak').submit();
        }
    </script>
@endsection
