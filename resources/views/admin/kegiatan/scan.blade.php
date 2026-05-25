@extends('layouts.adminlte')

@section('title', 'Scan QR Code')
@section('page-title', 'Scan QR Code Absensi')

@section('content')
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title">
                        <i class="fas fa-qrcode"></i> Scan QR Code
                    </h3>
                </div>
                <div class="card-body text-center">
                    <div id="reader" style="width: 100%;"></div>

                    <form id="qr-form" action="{{ route('absensi.scan.process') }}" method="POST" style="display: none;">
                        @csrf
                        <input type="hidden" name="qr_data" id="qr_data">
                    </form>

                    <div class="mt-3">
                        <p class="text-muted">
                            <i class="fas fa-info-circle"></i>
                            Arahkan kamera ke QR Code kegiatan untuk melakukan absensi
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script>
        function onScanSuccess(decodedText, decodedResult) {
            // Set value ke form
            document.getElementById('qr_data').value = decodedText;

            // Submit form
            document.getElementById('qr-form').submit();
        }

        function onScanFailure(error) {
            // console.warn(`Code scan error = ${error}`);
        }

        let html5QrcodeScanner = new Html5QrcodeScanner(
            "reader", {
                fps: 10,
                qrbox: {
                    width: 250,
                    height: 250
                }
            }, false);
        html5QrcodeScanner.render(onScanSuccess, onScanFailure);
    </script>
@endsection
