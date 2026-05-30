<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Preview Surat - {{ $template->nama }}</title>

    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

    <style>
        body {
            background-color: #f4f6f9;
            /* Warna background khas AdminLTE */
            font-family: 'Source Sans Pro', sans-serif;
            margin: 0;
            padding: 0;
        }

        /* TOOLBAR ATAS (Sticky Control Panel) */
        .toolbar-header {
            background: #ffffff;
            padding: 15px 30px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
            position: sticky;
            top: 0;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #007bff;
        }

        .toolbar-title h4 {
            margin: 0;
            font-weight: 600;
            color: #343a40;
            font-size: 1.25rem;
        }

        /* KERTAS A4 ILLUSION */
        .surat-container {
            max-width: 210mm;
            /* Lebar kertas A4 */
            min-height: 297mm;
            /* Tinggi kertas A4 */
            background: white;
            margin: 40px auto;
            padding: 2cm;
            /* Margin standar surat */
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
            border-radius: 4px;
        }

        /* PENGATURAN TYPOGRAPHY SURAT (Penting agar rapi) */
        .surat-content {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.35 !important;
            color: #000;
        }

        .surat-content p,
        .surat-content div,
        .tox-edit-area p {
            margin: 0 0 2px 0 !important;
            padding: 0 !important;
            line-height: 1.35 !important;
        }

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

        /* EDITOR TINYMCE */
        #editModeContainer {
            margin-top: -10px;
        }

        /* MODE PRINT */
        @media print {
            @page {
                size: A4 portrait;
                margin: 2cm;
            }

            body {
                background-color: white;
            }

            .toolbar-header,
            .no-print,
            .tox-tinymce {
                display: none !important;
            }

            .surat-container {
                box-shadow: none;
                border: none;
                margin: 0;
                padding: 0;
                width: 100%;
                max-width: none;
                min-height: auto;
            }
        }
    </style>
</head>

<body>

    <div class="toolbar-header no-print">
        <div class="toolbar-title d-flex align-items-center">
            <i class="fas fa-file-contract text-primary mr-2 fa-lg"></i>
            <div>
                <h4>Preview Surat</h4>
                <small class="text-muted"><span class="badge badge-primary">{{ $template->kode }}</span>
                    {{ $template->nama }}</small>
            </div>
        </div>
        <div class="toolbar-actions">
            <button onclick="window.close();" class="btn btn-default mr-2 shadow-sm">
                <i class="fas fa-times text-danger"></i> Tutup
            </button>

            <button id="toggleEditBtn" class="btn btn-warning mr-2 shadow-sm">
                <i class="fas fa-edit"></i> Edit Mode
            </button>

            <button onclick="window.print()" class="btn btn-info mr-2 shadow-sm">
                <i class="fas fa-print"></i> Cetak Dokumen
            </button>

            <button type="button" onclick="saveAndClose(event)" class="btn btn-success shadow-sm">
                <i class="fas fa-save"></i> Simpan Draft
            </button>
        </div>
    </div>

    <div class="container mt-3 no-print">
        @if ($errors->any())
            <div class="alert alert-danger shadow-sm">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger shadow-sm">
                {{ session('error') }}
            </div>
        @endif
    </div>

    <div class="surat-container">
        <div id="viewMode" class="surat-content">
            {!! $isiSurat !!}
        </div>

        <div id="editModeContainer" style="display: none;">
            <textarea id="editMode" style="width:100%; height:800px;">{{ $isiSurat }}</textarea>
        </div>
    </div>

    <form id="saveForm" action="{{ route('cetak-surat.store', $template->id) }}" method="POST">
        @csrf
        <input type="hidden" id="edited_content" name="edited_content" value="">
        <input type="hidden" id="nomor_surat" name="nomor_surat" value="{{ $nomorSurat }}">
        @foreach ($dataSurat as $key => $value)
            <input type="hidden" name="fields[{{ $key }}]"
                value="{{ is_array($value) ? json_encode($value) : $value }}">
        @endforeach
    </form>

    <script>
        let editModeActive = false;
        let tinymceInitialized = false;
        let pendingContent = null;
        let isDirty = false;

        function initTinyMCE(content) {
            if (typeof tinymce !== 'undefined' && !tinymceInitialized) {
                tinymce.init({
                    selector: '#editMode',
                    height: 850, // Ditinggikan agar menyerupai A4
                    menubar: false,
                    plugins: 'lists link table code',
                    toolbar: 'undo redo | bold italic underline | alignleft aligncenter alignright justify | bullist numlist | table | removeformat code',
                    content_style: `
                        body { font-family: "Times New Roman", Times, serif; font-size: 12pt; line-height: 1.35; padding: 20px; } 
                        p, div { margin: 0 0 2px 0 !important; padding: 0 !important; line-height: 1.35 !important; } 
                        table { border-collapse: collapse; width: 100%; border: none !important; } 
                        td { border: none !important; padding: 2px 4px !important; }
                    `,
                    setup: function(editor) {
                        editor.on('init', function() {
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

        // SAKLAR TOGGLE EDIT MODE
        document.getElementById('toggleEditBtn').addEventListener('click', function() {
            let viewMode = document.getElementById('viewMode');
            let editModeContainer = document.getElementById('editModeContainer');
            let btn = this;

            if (!editModeActive) {
                let konten = viewMode.innerHTML;
                viewMode.style.display = 'none';
                editModeContainer.style.display = 'block';

                btn.innerHTML = '<i class="fas fa-eye"></i> Tinjau Surat';
                btn.className = "btn btn-info mr-2 shadow-sm";
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
                btn.className = "btn btn-warning mr-2 shadow-sm";
                editModeActive = false;
            }
        });

        // SIMPAN & TUTUP TAB (AJAX)
        function saveAndClose() {
            let saveForm = document.getElementById('saveForm');
            let editedContentInput = document.getElementById('edited_content');

            if (!saveForm || !editedContentInput) return;

            let content = (editModeActive && tinymce.get('editMode')) ?
                tinymce.get('editMode').getContent() :
                document.getElementById('viewMode').innerHTML;

            editedContentInput.value = content;

            let formData = new FormData(saveForm);

            // Ganti tombol menjadi loading
            let btnSave = event.currentTarget;
            let originalBtnHtml = btnSave.innerHTML;
            btnSave.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
            btnSave.disabled = true;

            fetch(saveForm.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => {
                    if (!response.ok) throw new Error('Server returned ' + response.status);
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success') {
                        // Jika berhasil, beri alert lalu tutup tab
                        alert('Draft Surat berhasil disimpan ke database!');

                        // Coba muat ulang halaman asal (parent) agar tabel ter-refresh
                        if (window.opener && !window.opener.closed) {
                            window.opener.location.href = '{{ route('surat.keluar.index') }}';
                        }
                        window.close(); // Tutup tab preview
                    } else {
                        alert('Gagal menyimpan: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menyimpan dokumen.');
                })
                .finally(() => {
                    btnSave.innerHTML = originalBtnHtml;
                    btnSave.disabled = false;
                });
        }
    </script>
</body>

</html>
