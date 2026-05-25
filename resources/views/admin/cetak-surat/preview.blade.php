<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Preview Surat - {{ $template->nama }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <script src="https://cdn.tiny.cloud/1/790oy87uninpb887jjodfajw2ivcmbi6dq4vzah5gguz6igm/tinymce/6/tinymce.min.js"
        referrerpolicy="origin"></script>

    <style>
        body {
            background-color: #f0f2f5;
            padding: 20px;
        }

        .surat-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* PERBAIKAN 1: Konsolidasi CSS Utama & Kunci Spasi Rapat */
        .surat-content {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.35 !important;
        }

        /* Pastikan semua elemen paragraf dan div di dalam cetak surat tidak memiliki margin default */
        .surat-content p,
        .surat-content div,
        .tox-edit-area p {
            margin: 0 0 2px 0 !important;
            padding: 0 !important;
            line-height: 1.35 !important;
        }

        /* Bersihkan border tabel default bawaan bootstrap agar mengikuti style inline seeder */
        .surat-content table {
            border-collapse: collapse;
            width: 100%;
            margin: 5px 0;
            border: none !important;
        }

        .surat-content th,
        .surat-content td {
            border: none !important;
            padding: 2px 4px !important;
            line-height: 1.35 !important;
        }

        .surat-header {
            background: #007bff;
            color: white;
            padding: 15px 20px;
            border-radius: 8px 8px 0 0;
        }

        .surat-body {
            padding: 30px;
            min-height: 500px;
        }

        .surat-footer {
            padding: 15px 20px;
            border-top: 1px solid #ddd;
            background: #f8f9fa;
            border-radius: 0 0 8px 8px;
        }

        .btn-group-custom {
            margin-bottom: 20px;
        }

        #viewMode {
            width: 100%;
            min-height: 500px;
        }

        @media print {

            .btn-group-custom,
            .btn,
            .no-print,
            .tox-tinymce,
            .surat-header,
            .surat-footer {
                display: none !important;
            }

            body {
                padding: 0;
                margin: 0;
                background-color: white;
            }

            .surat-container {
                box-shadow: none;
                border: none;
            }

            .surat-body {
                padding: 0mm;
                /* Sesuai margin kertas fisik print */
            }
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        <div class="btn-group-custom mb-3">
            <a href="{{ route('cetak-surat.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>

            <button type="button" onclick="saveAndClose(event)" class="btn btn-success">
                <i class="fas fa-save"></i> Simpan & Kembali
            </button>

            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print"></i> Cetak Surat
            </button>
            <button id="toggleEditBtn" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit Mode
            </button>
        </div>

        <div class="surat-container">
            <div class="surat-header">
                <h4 class="mb-0">
                    <i class="fas fa-file-alt"></i> {{ $template->nama }}
                    <span class="badge badge-light float-right">{{ $template->kode }}</span>
                </h4>
            </div>
            <div class="surat-body">
                <div id="viewMode" class="surat-content">
                    {!! $isiSurat !!}
                </div>

                <div id="editModeContainer" style="display: none;">
                    <textarea id="editMode" style="width:100%; height:500px;">{{ $isiSurat }}</textarea>
                </div>
            </div>
            <div class="surat-footer">
                <div class="row">
                    <div class="col-md-6">
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i>
                            Preview surat ini dapat diedit sebelum disimpan.
                        </small>
                    </div>
                    <div class="col-md-6 text-right">
                        <small class="text-muted">
                            <i class="fas fa-clock"></i>
                            Terakhir diupdate: <span id="lastUpdate">{{ date('H:i:s') }}</span>
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <form id="saveForm" action="{{ route('cetak-surat.store', $template->id) }}" method="POST">
            @csrf
            <input type="hidden" id="edited_content" name="edited_content" value="">
            <input type="hidden" id="nomor_surat" name="nomor_surat" value="{{ $nomorSurat }}">

            @foreach ($dataSurat as $key => $value)
                <input type="hidden" name="fields[{{ $key }}]" value="{{ $value }}">
            @endforeach
        </form>
    </div>

    <script>
        let editModeActive = false;
        let tinymceInitialized = false;
        let pendingContent = null;
        let isDirty = false;

        function initTinyMCE(content) {
            if (typeof tinymce !== 'undefined' && !tinymceInitialized) {
                tinymce.init({
                    selector: '#editMode',
                    height: 550,
                    menubar: false,
                    plugins: 'lists link table code',
                    toolbar: 'bold italic underline | bullist numlist | table | removeformat code',
                    // PERBAIKAN 3: Samakan line-height editor TinyMCE menjadi 1.35 agar saat mengedit, layout tidak bergeser melar
                    content_style: 'body { font-family: "Times New Roman", Times, serif; font-size: 12pt; line-height: 1.35; padding: 20px; } p, div { margin: 0 0 2px 0 !important; padding: 0 !important; line-height: 1.35 !important; } table { border-collapse: collapse; width: 100%; border: none !important; } td { border: none !important; padding: 2px 4px !important; }',
                    setup: function(editor) {
                        editor.on('init', function() {
                            console.log('TinyMCE siap!');
                            tinymceInitialized = true;

                            if (pendingContent !== null) {
                                editor.setContent(pendingContent);
                                pendingContent = null;
                            }
                        });
                        editor.on('change NodeChange keyup', function() {
                            isDirty = true;
                        });
                    }
                });
            }

            if (tinymceInitialized && tinymce.get('editMode')) {
                tinymce.get('editMode').setContent(content || '');
            } else if (content !== null) {
                pendingContent = content;
            }
        }

        // Toggle Edit Mode
        document.getElementById('toggleEditBtn').addEventListener('click', function() {
            let viewMode = document.getElementById('viewMode');
            let editModeContainer = document.getElementById('editModeContainer');
            let btn = this;

            if (!editModeActive) {
                let konten = viewMode.innerHTML;

                viewMode.style.display = 'none';
                editModeContainer.style.display = 'block';
                btn.innerHTML = '<i class="fas fa-eye"></i> View Mode';
                btn.className = "btn btn-info";
                editModeActive = true;

                initTinyMCE(konten);
            } else {
                let content = '';
                if (tinymceInitialized && tinymce.get('editMode')) {
                    content = tinymce.get('editMode').getContent();
                } else {
                    content = document.getElementById('editMode').value;
                }

                viewMode.innerHTML = content;
                viewMode.style.display = 'block';
                editModeContainer.style.display = 'none';
                btn.innerHTML = '<i class="fas fa-edit"></i> Edit Mode';
                btn.className = "btn btn-warning";
                editModeActive = false;
            }
        });

        // Save and close
        function saveAndClose() {
            let saveForm = document.getElementById('saveForm');
            let editedContentInput = document.getElementById('edited_content');

            if (!saveForm || !editedContentInput) {
                console.error('Form atau edited_content tidak ditemukan!');
                return;
            }

            // Ambil konten editor (TinyMCE atau ViewMode)
            let content = (typeof editModeActive !== 'undefined' && editModeActive && tinymce.get('editMode')) ?
                tinymce.get('editMode').getContent() :
                document.getElementById('viewMode').innerHTML;

            editedContentInput.value = content;

            // Siapkan FormData
            let formData = new FormData(saveForm);

            // Kirim AJAX
            fetch(saveForm.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest' // PENTING agar Controller tahu ini AJAX
                    },
                    body: formData
                })
                .then(response => {
                    if (!response.ok) throw new Error('Server returned ' + response.status);
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success') {
                        window.location.href = '{{ route('cetak-surat.index') }}';
                    } else {
                        alert('Gagal menyimpan: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan jaringan atau server.');
                });
        }
    </script>
</body>

</html>
