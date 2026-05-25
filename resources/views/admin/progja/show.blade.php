@extends('layouts.adminlte')

@section('title', 'Kanban - ' . $progja->nama)
@section('page-title', $progja->nama)

@section('content')
    <style>
        /* Style untuk tagged user */
        .tagged-user {
            color: #007bff;
            font-weight: bold;
            text-decoration: none;
        }

        .tagged-user:hover {
            text-decoration: underline;
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
        .chat-box {
            scroll-behavior: smooth;
            background: #f0f2f5 !important;
        }

        .chat-message {
            margin-bottom: 20px;
            display: flex;
            animation: fadeIn 0.3s ease;
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

        .chat-message.me {
            justify-content: flex-end;
        }

        .chat-message.other {
            justify-content: flex-start;
        }

        .chat-bubble {
            max-width: 70%;
            padding: 10px 15px;
            border-radius: 18px;
            position: relative;
            word-wrap: break-word;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        /* Bubble untuk pesan sendiri (biru) */
        .chat-message.me .chat-bubble {
            background: #007bff;
            color: white;
            border-bottom-right-radius: 4px;
        }

        /* Bubble untuk pesan orang lain (putih) */
        .chat-message.other .chat-bubble {
            background: white;
            color: #333;
            border: 1px solid #e0e0e0;
            border-bottom-left-radius: 4px;
        }

        .chat-user {
            font-size: 12px;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .chat-message.me .chat-user {
            justify-content: flex-end;
        }

        .chat-user-name {
            font-weight: bold;
        }

        .chat-message.me .chat-user-name {
            color: #ffd700;
        }

        .chat-time {
            font-size: 10px;
            opacity: 0.7;
        }

        .chat-message.other .chat-time {
            color: #999;
        }

        .chat-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #6c757d;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 14px;
        }

        .chat-message.me .chat-avatar {
            order: 2;
            background: #28a745;
            margin-left: 8px;
        }

        .chat-message.other .chat-avatar {
            margin-right: 8px;
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


        /* Typing indicator */
        .typing-indicator {
            display: inline-block;
            background: rgba(255, 255, 255, 0.9);
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 12px;
            color: #666;
        }

        .typing-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #999;
            margin: 0 2px;
            animation: typing 1.4s infinite;
        }

        .typing-dot:nth-child(2) {
            animation-delay: 0.2s;
        }

        .typing-dot:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes typing {

            0%,
            60%,
            100% {
                transform: translateY(0);
                opacity: 0.5;
            }

            30% {
                transform: translateY(-10px);
                opacity: 1;
            }
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
        <!-- Kolom TODO -->
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

        <!-- Kolom PROGRESS -->
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
                                        <i class="fas fa-calendar"></i> {{ date('d/m/Y', strtotime($tugas->deadline)) }}
                                    </small>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Kolom DONE -->
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
                                        <i class="fas fa-calendar"></i> {{ date('d/m/Y', strtotime($tugas->deadline)) }}
                                    </small>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Kolom REVISI -->
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
                                        <i class="fas fa-calendar"></i> {{ date('d/m/Y', strtotime($tugas->deadline)) }}
                                    </small>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <!-- Chat Section Professional -->
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
                    <!-- Chat Messages Area -->
                    <div class="chat-box" id="chat-box"
                        style="height: 450px; overflow-y: auto; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px;">
                        <div class="text-center text-white">Loading pesan...</div>
                    </div>

                    <!-- Chat Input Area -->
                    <div class="p-3 bg-light" style="border-top: 1px solid #dee2e6;">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white" style="border-right: none;">
                                    <i class="fas fa-smile text-warning"></i>
                                </span>
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
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i> Tekan Enter untuk mengirim, @ untuk mention anggota
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Tugas -->
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
                function loadMessages() {
                    $.ajax({
                        url: "{{ route('progja.messages', $progja) }}",
                        type: "GET",
                        dataType: "json",
                        success: function(data) {
                            var chatBox = $('#chat-box');
                            var currentUserId = {{ auth()->id() }};
                            chatBox.empty();

                            var messages = [];
                            if (Array.isArray(data.messages)) {
                                messages = data.messages;
                            } else if (data.messages && typeof data.messages === 'object') {
                                messages = Object.values(data.messages);
                            }

                            // Urutkan dari lama ke baru
                            messages.reverse();

                            if (messages.length > 0) {
                                for (var i = 0; i < messages.length; i++) {
                                    var msg = messages[i];
                                    var isMe = (msg.user.id === currentUserId);
                                    var userInitial = msg.user.name.charAt(0).toUpperCase();

                                    var html = '<div class="chat-message ' + (isMe ? 'me' : 'other') + '">';

                                    if (!isMe) {
                                        html += '<div class="chat-avatar">' + userInitial + '</div>';
                                    }

                                    html += '<div class="chat-bubble">';
                                    html += '<div class="chat-user">';
                                    html += '<span class="chat-user-name">' + escapeHtml(msg.user.name) +
                                        '</span>';
                                    html += '<span class="chat-time">' + msg.created_at + '</span>';
                                    html += '</div>';
                                    html += '<div class="chat-text">' + msg.message + '</div>';
                                    html += '</div>';

                                    if (isMe) {
                                        html += '<div class="chat-avatar">' + userInitial + '</div>';
                                    }

                                    html += '</div>';
                                    chatBox.append(html);
                                }

                                // Scroll ke bawah
                                chatBox.scrollTop(chatBox[0].scrollHeight);
                                $('#chat-count').text(messages.length);
                            } else {
                                chatBox.html(
                                    '<div class="text-center text-white py-5"><i class="fas fa-comments fa-3x mb-3 opacity-50"></i><br>Belum ada pesan<br><small>Mulai diskusi dengan mengetik pesan di bawah</small></div>'
                                );
                            }
                        },
                        error: function(xhr) {
                            $('#chat-box').html(
                                '<div class="text-center text-white py-5"><i class="fas fa-exclamation-triangle fa-3x mb-3"></i><br>Gagal memuat pesan</div>'
                            );
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
                        top: inputPos.top + $input.outerHeight() + 5,
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

                // Kirim pesan dengan animasi
                $('#send-chat').click(function() {
                    var message = $('#chat-input').val();
                    if (!message.trim()) {
                        $('#chat-input').addClass('is-invalid');
                        setTimeout(function() {
                            $('#chat-input').removeClass('is-invalid');
                        }, 1000);
                        return;
                    }

                    var btn = $(this);
                    var originalHtml = btn.html();
                    btn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);

                    $.ajax({
                        url: "{{ route('progja.send-message', $progja) }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            message: message
                        },
                        dataType: "json",
                        success: function(response) {
                            if (response.success) {
                                $('#chat-input').val('');
                                loadMessages();
                                // Play notification sound (opsional)
                                // var audio = new Audio('/sounds/notification.mp3'); audio.play();
                            }
                        },
                        error: function(xhr) {
                            alert('Gagal mengirim pesan');
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

                // Load chat pertama kali
                loadMessages();

                // Auto refresh setiap 5 detik
                setInterval(loadMessages, 5000);
            });
        </script>
    @endpush
@endsection
