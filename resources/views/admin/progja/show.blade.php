    @extends('layouts.adminlte')

    @section('title', 'Kanban - ' . $progja->nama)
    @section('page-title', $progja->nama)

    @section('content')
        <style>
            /* Style untuk tagged user */
            .tagged-user {
                color: #0056b3 !important;
                /* Warna teks biru gelap agar kontras */
                font-weight: 800;
                text-decoration: none;
                background-color: rgba(0, 123, 255, 0.15) !important;
                /* Latar biru transparan */
                padding: 2px 6px;
                border-radius: 6px;
                display: inline-block;
                transition: all 0.2s;
            }

            .chat-quote {
                background-color: rgba(0, 0, 0, 0.05);
                border-left: 4px solid #25D366;
                /* Hijau WA */
                padding: 6px 10px;
                border-radius: 4px;
                margin-bottom: 6px;
                font-size: 12px;
            }

            .chat-quote-name {
                font-weight: bold;
                color: #25D366;
                margin-bottom: 2px;
            }

            .reply-btn {
                cursor: pointer;
                color: #888;
                transition: 0.2s;
            }

            .reply-btn:hover {
                color: #007bff;
            }

            .tagged-user:hover {
                background-color: rgba(0, 123, 255, 0.3) !important;
                text-decoration: none;
            }

            /* Autocomplete mention */
            .mention-autocomplete {
                position: absolute;
                background: white;
                border: 1px solid #ddd;
                border-radius: 4px;
                max-height: 200px;
                overflow-y: auto;
                z-index: 1000;
                display: none;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            }

            .mention-autocomplete .mention-item {
                padding: 8px 12px;
                cursor: pointer;
            }

            .mention-autocomplete .mention-item:hover {
                background-color: #f0f0f0;
            }

            .kanban-column {
                min-height: 500px;
                background-color: #f8f9fa;
                border-radius: 8px;
                padding: 10px;
            }

            .kanban-card {
                cursor: grab;
                transition: all 0.2s;
            }

            .kanban-card:active {
                cursor: grabbing;
            }

            .kanban-card.dragging {
                opacity: 0.5;
            }

            .kanban-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }

            /* Professional Chat Styles - Clean Version */
            .chat-message {
                display: flex;
                margin-bottom: 15px;
                width: 100%;
                align-items: flex-end;
            }

            /* Paksa pesan sendiri ke KANAN */
            .chat-message.me {
                justify-content: flex-end;
            }

            /* Paksa pesan orang lain ke KIRI */
            .chat-message.other {
                justify-content: flex-start;
            }

            .chat-bubble {
                padding: 8px 14px;
                border-radius: 12px;
                max-width: 75%;
                box-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
                font-size: 14px;
                color: #212529;
                /* Warna teks gelap agar terbaca */
            }

            .chat-message.other .chat-bubble {
                background-color: #ffffff;
                border-bottom-left-radius: 0;
                /* Sudut lancip di kiri bawah */
            }

            .chat-message.me .chat-bubble {
                background-color: #dcf8c6;
                /* Hijau WA */
                border-bottom-right-radius: 0;
                /* Sudut lancip di kanan bawah */
            }

            .chat-avatar {
                width: 35px;
                height: 35px;
                border-radius: 50%;
                color: #fff;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: bold;
                flex-shrink: 0;
                font-size: 14px;
            }

            .chat-message.other .chat-avatar {
                background-color: #6c757d;
                margin-right: 10px;
                /* Jarak dengan bubble (kiri) */
            }

            .chat-message.me .chat-avatar {
                background-color: #28a745;
                margin-left: 10px;
                /* Jarak dengan bubble (kanan) */
            }

            .chat-user-name {
                font-weight: bold;
                font-size: 12px;
                color: #075e54;
                margin-bottom: 4px;
                display: block;
            }

            .chat-time {
                font-size: 10px;
                color: #888;
                display: block;
                text-align: right;
                margin-top: 5px;
            }

            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(10px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            /* Tagged user style */
            .tagged-user {
                color: #ffd700;
                font-weight: bold;
                text-decoration: none;
                background: rgba(255, 215, 0, 0.2);
                padding: 2px 6px;
                border-radius: 12px;
                display: inline-block;
            }

            .chat-message.other .tagged-user {
                color: #007bff;
                background: rgba(0, 123, 255, 0.1);
            }

            .tagged-user:hover {
                text-decoration: underline;
            }

            /* Scrollbar styling */
            .chat-box::-webkit-scrollbar {
                width: 6px;
            }

            .chat-box::-webkit-scrollbar-track {
                background: #e0e0e0;
                border-radius: 3px;
            }

            .chat-box::-webkit-scrollbar-thumb {
                background: #b0b0b0;
                border-radius: 3px;
            }

            .chat-box::-webkit-scrollbar-thumb:hover {
                background: #888;
            }

            .drop-zone {
                transition: all 0.2s;
            }

            .drop-zone.drag-over {
                background-color: #e9ecef;
                border: 2px dashed #007bff;
            }
        </style>

        <div class="row">
            <div class="col-md-12 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            @if ($progja->jenis == 'ipnu')
                                <span class="badge badge-primary badge-lg">IPNU</span>
                            @elseif($progja->jenis == 'ippnu')
                                <span class="badge badge-danger badge-lg">IPPNU</span>
                            @else
                                <span class="badge badge-success badge-lg">Bersama IPNU & IPPNU</span>
                            @endif
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <p><strong>Deskripsi:</strong> {{ $progja->deskripsi ?: '-' }}</p>
                                <p><strong>Periode:</strong> {{ date('d/m/Y', strtotime($progja->tgl_mulai)) }} -
                                    {{ date('d/m/Y', strtotime($progja->tgl_selesai)) }}</p>
                            </div>
                            <div class="col-md-4 text-right">
                                <a href="{{ route('progja.edit', $progja) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit Progja
                                </a>
                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                                    data-target="#modalTugas">
                                    <i class="fas fa-plus"></i> Tambah Tugas
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h6 class="mb-0">📋 To Do</h6>
                        <span class="badge badge-light float-right">{{ $todos->count() }}</span>
                    </div>
                    <div class="card-body kanban-column drop-zone" data-status="todo" id="column-todo">
                        @foreach ($todos as $tugas)
                            <div class="card kanban-card mb-2" data-id="{{ $tugas->id }}">
                                <div class="card-body p-2">
                                    <div class="d-flex justify-content-between">
                                        <strong>{{ $tugas->nama }}</strong>
                                        <button class="btn btn-sm btn-danger delete-tugas" data-id="{{ $tugas->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    @if ($tugas->assignee)
                                        <small class="text-muted">
                                            <i class="fas fa-user"></i> {{ $tugas->assignee->name }}
                                        </small>
                                    @endif
                                    @if ($tugas->deadline)
                                        <br><small class="text-muted">
                                            <i class="fas fa-calendar"></i> {{ date('d/m/Y', strtotime($tugas->deadline)) }}
                                        </small>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0">⚙️ Progress</h6>
                        <span class="badge badge-light float-right">{{ $progress->count() }}</span>
                    </div>
                    <div class="card-body kanban-column drop-zone" data-status="progress" id="column-progress">
                        @foreach ($progress as $tugas)
                            <div class="card kanban-card mb-2" data-id="{{ $tugas->id }}">
                                <div class="card-body p-2">
                                    <div class="d-flex justify-content-between">
                                        <strong>{{ $tugas->nama }}</strong>
                                        <button class="btn btn-sm btn-danger delete-tugas" data-id="{{ $tugas->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    @if ($tugas->assignee)
                                        <small class="text-muted">
                                            <i class="fas fa-user"></i> {{ $tugas->assignee->name }}
                                        </small>
                                    @endif
                                    @if ($tugas->deadline)
                                        <br><small class="text-muted">
                                            <i class="fas fa-calendar"></i>
                                            {{ date('d/m/Y', strtotime($tugas->deadline)) }}
                                        </small>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0">✅ Done</h6>
                        <span class="badge badge-light float-right">{{ $done->count() }}</span>
                    </div>
                    <div class="card-body kanban-column drop-zone" data-status="done" id="column-done">
                        @foreach ($done as $tugas)
                            <div class="card kanban-card mb-2" data-id="{{ $tugas->id }}">
                                <div class="card-body p-2">
                                    <div class="d-flex justify-content-between">
                                        <strong>{{ $tugas->nama }}</strong>
                                        <button class="btn btn-sm btn-danger delete-tugas" data-id="{{ $tugas->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    @if ($tugas->assignee)
                                        <small class="text-muted">
                                            <i class="fas fa-user"></i> {{ $tugas->assignee->name }}
                                        </small>
                                    @endif
                                    @if ($tugas->deadline)
                                        <br><small class="text-muted">
                                            <i class="fas fa-calendar"></i>
                                            {{ date('d/m/Y', strtotime($tugas->deadline)) }}
                                        </small>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card">
                    <div class="card-header bg-warning">
                        <h6 class="mb-0">🔄 Revisi</h6>
                        <span class="badge badge-dark float-right">{{ $revisi->count() }}</span>
                    </div>
                    <div class="card-body kanban-column drop-zone" data-status="revisi" id="column-revisi">
                        @foreach ($revisi as $tugas)
                            <div class="card kanban-card mb-2" data-id="{{ $tugas->id }}">
                                <div class="card-body p-2">
                                    <div class="d-flex justify-content-between">
                                        <strong>{{ $tugas->nama }}</strong>
                                        <button class="btn btn-sm btn-danger delete-tugas" data-id="{{ $tugas->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    @if ($tugas->assignee)
                                        <small class="text-muted">
                                            <i class="fas fa-user"></i> {{ $tugas->assignee->name }}
                                        </small>
                                    @endif
                                    @if ($tugas->deadline)
                                        <br><small class="text-muted">
                                            <i class="fas fa-calendar"></i>
                                            {{ date('d/m/Y', strtotime($tugas->deadline)) }}
                                        </small>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-comments"></i> Diskusi Program Kerja
                            <span class="badge badge-light float-right" id="chat-count">0</span>
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="chat-box" id="chat-box"
                            style="height: 450px; overflow-y: auto; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px;">
                            <div class="text-center text-white">Loading pesan...</div>
                        </div>

                        <div class="p-3 bg-light" style="border-top: 1px solid #dee2e6;">
                            <!-- KOTAK PREVIEW REPLY (Muncul saat tombol balas diklik) -->
                            <div id="reply-preview-container" class="bg-white p-2 border-top"
                                style="display: none; border-left: 4px solid #25D366; position: relative;">
                                <div style="font-weight: bold; font-size: 12px; color: #25D366;" id="reply-preview-name">
                                </div>
                                <div style="font-size: 12px; color: #666; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; padding-right: 20px;"
                                    id="reply-preview-text"></div>
                                <button type="button" class="close" id="cancel-reply"
                                    style="position: absolute; right: 10px; top: 10px; font-size: 18px;">&times;</button>
                            </div>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <button class="btn btn-light border" type="button"
                                        onclick="document.getElementById('file-input').click()" title="Lampirkan File">
                                        <i class="fas fa-paperclip text-secondary"></i>
                                    </button>
                                    <input type="file" id="file-input" style="display: none;"
                                        accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xlsx,.xls">
                                </div>
                                <input type="text" id="chat-input" class="form-control"
                                    placeholder="Tulis pesan... Gunakan @nama untuk mention anggota"
                                    style="border-left: none; border-right: none;">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" id="send-chat" type="button">
                                        <i class="fas fa-paper-plane"></i> Kirim
                                    </button>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-1">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i> Tekan Enter untuk mengirim, @ untuk mention anggota
                                </small>
                                <small class="text-primary font-weight-bold" id="file-name-display"
                                    style="display:none;"></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalTugas" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Tugas Baru</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <form id="formTugas">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Nama Tugas</label>
                                <input type="text" name="nama" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Assign ke</label>
                                <select name="assignee_id" class="form-control">
                                    <option value="">Pilih Anggota</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Deadline</label>
                                <input type="date" name="deadline" class="form-control">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @push('scripts')
            <script>
                $(function() {
                    // Drag & Drop
                    $(".kanban-card").draggable({
                        revert: "invalid",
                        cursor: "grab",
                        helper: "clone",
                        start: function(e, ui) {
                            $(this).addClass("dragging");
                        },
                        stop: function(e, ui) {
                            $(this).removeClass("dragging");
                        }
                    });

                    $(".drop-zone").droppable({
                        accept: ".kanban-card",
                        drop: function(e, ui) {
                            var tugasId = ui.draggable.data("id");
                            var newStatus = $(this).data("status");
                            if (!tugasId) return;
                            $.ajax({
                                url: "/progja/tugas/" + tugasId + "/status",
                                type: "PUT",
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    status: newStatus
                                },
                                success: function(response) {
                                    if (response.success) location.reload();
                                    else alert("Gagal update status");
                                }
                            });
                        },
                        over: function() {
                            $(this).addClass("drag-over");
                        },
                        out: function() {
                            $(this).removeClass("drag-over");
                        }
                    });

                    // Form Tambah Tugas
                    $("#formTugas").submit(function(e) {
                        e.preventDefault();
                        $.ajax({
                            url: "{{ route('progja.tugas.store', $progja) }}",
                            type: "POST",
                            data: $(this).serialize(),
                            success: function() {
                                location.reload();
                            },
                            error: function(xhr) {
                                alert("Error: " + xhr.responseJSON?.message);
                            }
                        });
                    });

                    // Hapus Tugas
                    $(".delete-tugas").click(function() {
                        if (!confirm("Yakin hapus tugas ini?")) return;
                        var tugasId = $(this).data("id");
                        $.ajax({
                            url: "/progja/tugas/" + tugasId,
                            type: "DELETE",
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function() {
                                location.reload();
                            }
                        });
                    });

                    // ========== CHAT FUNCTION ==========

                    // Event saat file dipilih (Tampilkan nama file)
                    $('#file-input').change(function() {
                        if (this.files && this.files[0]) {
                            $('#file-name-display').text('📁 ' + this.files[0].name).show();
                        } else {
                            $('#file-name-display').hide();
                        }
                    });
                    var replyToId = ''; // Menyimpan ID pesan yang mau dibalas

                    window.setReply = function(id, name, text) {
                        replyToId = id;
                        $('#reply-preview-name').text(name);
                        $('#reply-preview-text').text(text);
                        $('#reply-preview-container').slideDown(200); // Tampilkan kotak preview
                        $('#chat-input').focus();
                    };

                    $('#cancel-reply').click(function() {
                        replyToId = '';
                        $('#reply-preview-container').slideUp(200); // Sembunyikan kotak
                    });

                    function loadMessages() {
                        $.ajax({
                            url: "{{ route('progja.messages', $progja) }}",
                            type: "GET",
                            dataType: "json",
                            success: function(data) {
                                var chatBox = $('#chat-box');

                                // Ambil data user yang sedang login
                                var currentUserId = {{ auth()->id() ?? 0 }};
                                var currentUserName = "{{ auth()->user()->name ?? '' }}";

                                chatBox.empty();

                                var messages = Array.isArray(data.messages) ? data.messages : Object.values(data
                                    .messages);

                                if (messages.length > 0) {
                                    var lastDate = null;

                                    messages.forEach(function(msg) {
                                        // 1. PEMISAH TANGGAL
                                        if (msg.date_group !== lastDate) {
                                            var separator =
                                                '<div class="d-flex justify-content-center my-3"><span class="px-3 py-1 shadow-sm" style="background-color: #f0f2f5; color: #54656f; border-radius: 10px; font-size: 12px; font-weight: 500;">' +
                                                msg.date_label + '</span></div>';
                                            chatBox.append(separator);
                                            lastDate = msg.date_group;
                                        }

                                        var msgUserId = msg.user_id || (msg.user ? msg.user.id : 0);
                                        var isMe = (msgUserId == currentUserId);
                                        var userInitial = msg.user ? msg.user.name.charAt(0)
                                            .toUpperCase() : '?';

                                        var alignmentClass = isMe ? 'justify-content-end' :
                                            'justify-content-start';
                                        var html = '<div class="d-flex w-100 mb-3 ' + alignmentClass +
                                            '">';

                                        // 2. AVATAR KIRI (Orang Lain)
                                        if (!isMe) {
                                            html +=
                                                '<div class="mr-2 rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center font-weight-bold" style="width: 35px; height: 35px; flex-shrink: 0;">' +
                                                userInitial + '</div>';
                                        }

                                        var bubbleBg = isMe ? '#dcf8c6' : '#ffffff';
                                        var bubbleRadius = isMe ? '12px 0px 12px 12px' :
                                            '0px 12px 12px 12px';

                                        // 3. LOGIKA MENTION (Agar tidak error saat dipanggil)
                                        var safeMessage = msg.message ? escapeHtml(msg.message) : '';
                                        var isMentioned = false;

                                        if (currentUserName && safeMessage) {
                                            var mentionTarget = '@' + currentUserName;
                                            if (!isMe && safeMessage.toLowerCase().includes(
                                                    mentionTarget.toLowerCase())) {
                                                isMentioned = true;
                                                bubbleBg =
                                                '#fffae6'; // Warna gelembung saat ditandai (kuning pucat)
                                            }
                                            // Highlight teks @mention
                                            var regex = new RegExp(mentionTarget, 'gi');
                                            safeMessage = safeMessage.replace(regex,
                                                '<span class="tagged-user">$&</span>');
                                        }

                                        // 4. BUKA BUBBLE PESAN
                                        html +=
                                            '<div class="p-2 shadow-sm" style="max-width: 75%; background-color: ' +
                                            bubbleBg + '; border-radius: ' + bubbleRadius + ';">';

                                        // 5. KOTAK QUOTE (Jika membalas pesan lain)
                                        if (msg.reply_to) {
                                            html +=
                                                '<div class="chat-quote" style="background-color: rgba(0, 0, 0, 0.05); border-left: 4px solid #25D366; padding: 6px 10px; border-radius: 4px; margin-bottom: 6px; font-size: 12px;">';
                                            html +=
                                                '<div style="font-weight: bold; color: #25D366; margin-bottom: 2px;">' +
                                                escapeHtml(msg.reply_to.name) + '</div>';

                                            // Potong teks jika balasan terlalu panjang
                                            var replyText = msg.reply_to.message;
                                            if (replyText.length > 50) replyText = replyText.substring(
                                                0, 50) + '...';

                                            html += '<div style="color: #666; font-style: italic;">' +
                                                escapeHtml(replyText) + '</div>';
                                            html += '</div>';
                                        }

                                        // 6. LABEL MENTION KITA
                                        if (isMentioned) {
                                            html +=
                                                '<div style="font-size: 11px; color: #dc3545; font-weight: bold; margin-bottom: 4px; padding-bottom: 4px; border-bottom: 1px solid #ffdf7e;"><i class="fas fa-at"></i> Kamu ditandai</div>';
                                        }

                                        // 7. NAMA PENGIRIM (Hanya orang lain)
                                        if (!isMe) {
                                            html +=
                                                '<div style="font-weight: bold; font-size: 12px; color: #075e54; margin-bottom: 4px;">' +
                                                escapeHtml(msg.user.name) + '</div>';
                                        }

                                        // 8. ISI PESAN TULISAN
                                        if (safeMessage) {
                                            html +=
                                                '<div style="font-size: 14px; color: #212529; word-break: break-word;">' +
                                                safeMessage + '</div>';
                                        }

                                        // 9. LAMPIRAN FILE
                                        if (msg.file_path) {
                                            var fileExt = msg.file_type ? msg.file_type.toLowerCase() :
                                                '';
                                            if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExt)) {
                                                html += '<div class="mt-2"><img src="/storage/' + msg
                                                    .file_path +
                                                    '" style="max-width:100%; border-radius:8px; border:1px solid #ddd;"></div>';
                                            } else {
                                                html += '<div class="mt-2"><a href="/storage/' + msg
                                                    .file_path +
                                                    '" target="_blank" class="btn btn-sm btn-light border"><i class="fas fa-file-download text-primary"></i> ' +
                                                    (msg.file_name || 'Download') + '</a></div>';
                                            }
                                        }

                                        // 10. WAKTU & TOMBOL REPLY
                                        var textToReply = msg.message ? escapeHtml(msg.message).replace(
                                            /'/g, "\\'") : '📂 File Lampiran';
                                        var nameToReply = escapeHtml(msg.user.name).replace(/'/g,
                                        "\\'");

                                        html +=
                                            '<div class="d-flex justify-content-end align-items-center mt-1" style="font-size: 10px; color: #888;">';
                                        html += '<span>' + (msg.time || msg.created_at) + '</span>';
                                        html += '<span class="ml-2 reply-btn" onclick="setReply(' + msg
                                            .id + ', \'' + nameToReply + '\', \'' + textToReply +
                                            '\')" style="cursor:pointer;" title="Balas"><i class="fas fa-reply"></i></span>';
                                        html += '</div>';

                                        // TUTUP BUBBLE
                                        html += '</div>';

                                        // 11. AVATAR KANAN (Diri Sendiri)
                                        if (isMe) {
                                            html +=
                                                '<div class="ml-2 rounded-circle bg-success text-white d-flex align-items-center justify-content-center font-weight-bold" style="width: 35px; height: 35px; flex-shrink: 0;">' +
                                                userInitial + '</div>';
                                        }

                                        html += '</div>'; // Tutup wrapper
                                        chatBox.append(html);
                                    });

                                    chatBox.scrollTop(chatBox[0].scrollHeight);
                                    $('#chat-count').text(messages.length);
                                } else {
                                    chatBox.html(
                                        '<div class="text-center text-white py-5"><i class="fas fa-comments fa-3x mb-3 opacity-50"></i><br>Belum ada pesan</div>'
                                        );
                                }
                            }
                        });
                    }

                    function escapeHtml(text) {
                        if (!text) return '';
                        return text.replace(/[&<>]/g, function(m) {
                            if (m === '&') return '&amp;';
                            if (m === '<') return '&lt;';
                            if (m === '>') return '&gt;';
                            return m;
                        });
                    }

                    // Data anggota untuk autocomplete mention
                    var members = [];
                    @foreach ($users as $user)
                        members.push({
                            id: {{ $user->id }},
                            name: "{{ addslashes($user->name) }}"
                        });
                    @endforeach

                    var mentionStartPos = 0;

                    // Autocomplete mention
                    $('#chat-input').on('input', function(e) {
                        var value = $(this).val();
                        var cursorPos = this.selectionStart;
                        var textBeforeCursor = value.substring(0, cursorPos);
                        var lastAtPos = textBeforeCursor.lastIndexOf('@');

                        if (lastAtPos !== -1) {
                            var textAfterAt = textBeforeCursor.substring(lastAtPos + 1);
                            if (!textAfterAt.includes(' ')) {
                                mentionStartPos = lastAtPos;
                                showMentionAutocomplete(textAfterAt.toLowerCase(), cursorPos);
                                return;
                            }
                        }
                        hideMentionAutocomplete();
                    });

                    function showMentionAutocomplete(query, cursorPos) {
                        var filtered = members.filter(function(m) {
                            return m.name.toLowerCase().includes(query);
                        }).slice(0, 5);

                        if (filtered.length === 0) {
                            hideMentionAutocomplete();
                            return;
                        }

                        var $autocomplete = $('#mention-autocomplete');
                        if ($autocomplete.length === 0) {
                            $autocomplete = $('<div id="mention-autocomplete" class="mention-autocomplete"></div>');
                            $('body').append($autocomplete);
                        }

                        $autocomplete.empty();
                        for (var i = 0; i < filtered.length; i++) {
                            $autocomplete.append('<div class="mention-item" data-name="' + filtered[i].name + '">' +
                                filtered[i].name + '</div>');
                        }

                        var $input = $('#chat-input');
                        var inputPos = $input.offset();
                        $autocomplete.css({
                            top: inputPos.top - $autocomplete.outerHeight() - 5, // Tampil di atas input
                            left: inputPos.left,
                            minWidth: $input.outerWidth()
                        }).show();

                        $('.mention-item').off('click').on('click', function() {
                            var name = $(this).data('name');
                            var currentValue = $('#chat-input').val();
                            var beforeMention = currentValue.substring(0, mentionStartPos);
                            var afterCursor = currentValue.substring(cursorPos);
                            $('#chat-input').val(beforeMention + '@' + name + ' ' + afterCursor);
                            hideMentionAutocomplete();
                            $('#chat-input').focus();
                        });
                    }

                    function hideMentionAutocomplete() {
                        $('#mention-autocomplete').remove();
                    }

                    // Kirim pesan dengan FormData (Mendukung File)
                    $('#send-chat').click(function() {
                        var message = $('#chat-input').val();
                        var fileInput = $('#file-input')[0].files[0];

                        if (!message.trim() && !fileInput) {
                            $('#chat-input').addClass('is-invalid');
                            setTimeout(function() {
                                $('#chat-input').removeClass('is-invalid');
                            }, 1000);
                            return;
                        }

                        var btn = $(this);
                        var originalHtml = btn.html();
                        btn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);

                        var formData = new FormData();
                        formData.append('_token', "{{ csrf_token() }}");
                        if (message.trim()) formData.append('message', message);
                        if (fileInput) formData.append('file', fileInput);
                        if (replyToId) formData.append('reply_to_id', replyToId);

                        $.ajax({
                            url: "{{ route('progja.send-message', $progja) }}",
                            type: "POST",
                            data: formData,
                            processData: false,
                            contentType: false,
                            // 1. TAMBAHKAN HEADER INI
                            headers: {
                                'Accept': 'application/json'
                            },
                            success: function(response) {
                                if (response.success) {
                                    $('#chat-input').val('');
                                    $('#file-input').val('');
                                    $('#file-name-display').hide();
                                    $('#cancel-reply').click();
                                    loadMessages();
                                }
                            },
                            // 2. UBAH BAGIAN ERROR INI
                            error: function(xhr) {
                                console.log("Error Detail:", xhr.responseText);
                                var msg = xhr.responseJSON ? (xhr.responseJSON.message || xhr
                                    .responseJSON.error) : 'Terjadi kesalahan pada server';
                                alert('Gagal: ' + msg);
                            },
                            complete: function() {
                                btn.html(originalHtml).prop('disabled', false);
                            }
                        });
                    });

                    // Enter untuk kirim
                    $('#chat-input').keypress(function(e) {
                        if (e.which === 13) {
                            e.preventDefault();
                            $('#send-chat').click();
                        }
                    });

                    // 1. Load data saat pertama buka
                    loadMessages();

                    // 2. Minta izin Notifikasi ke Browser (DIPERBAIKI)
                    if ("Notification" in window && Notification.permission !== "granted") {
                        Notification.requestPermission();
                    }

                    // 3. Listener Real-Time dari Laravel Echo
                    if (typeof window.Echo !== 'undefined') {
                        window.Echo.private('progja-chat.' + {{ $progja->id }})
                            .listen('MessageSent', (e) => {
                                // Update chat otomatis
                                loadMessages();

                                // Siapkan ID dan Nama kita
                                var currentUserId = {{ auth()->id() ?? 0 }};
                                var currentUserName = "{{ auth()->user()->name ?? '' }}";

                                // Jika user sedang buka tab lain DAN izin notif diberikan DAN ini bukan pesan kita sendiri
                                if (document.hidden && Notification.permission === "granted" && e.message.user_id !==
                                    currentUserId) {

                                    var safeMessage = e.message.message ? e.message.message : '';
                                    var notifTitle = "Pesan Baru dari " + e.message.user.name;
                                    var notifBody = safeMessage ? safeMessage : '📂 Mengirim lampiran';

                                    // ==========================================
                                    // CEK MENTION UNTUK NOTIFIKASI POP-UP
                                    // ==========================================
                                    if (currentUserName && safeMessage) {
                                        var mentionTarget = '@' + currentUserName;

                                        // Jika pesan mengandung @NamaKita
                                        if (safeMessage.toLowerCase().includes(mentionTarget.toLowerCase())) {
                                            notifTitle = "📌 " + e.message.user.name + " menyebut Anda!";
                                        }
                                    }
                                    // ==========================================

                                    // Tembakkan notifikasi ke laptop
                                    var notif = new Notification(notifTitle, {
                                        body: notifBody,
                                        // icon: '/logo-web.png' // <-- Opsional: Kalau Anda punya icon web, hapus // di depannya
                                    });

                                    notif.onclick = function() {
                                        window.focus(); // Bawa user ke tab obrolan saat notif diklik
                                        this.close();
                                    };
                                }
                            });
                    }
                });
            </script>
        @endpush
    @endsection
