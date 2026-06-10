@extends('layouts.adminlte')

@section('title', 'Kanban - ' . $progja->nama)
@section('page-title', 'Papan Tugas: ' . $progja->nama)

@section('content')
    <style>
        /* CSS KHUSUS KANBAN BOARD SAJA */
        .kanban-column {
            min-height: 500px;
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 10px;
        }

        .kanban-card {
            cursor: grab;
            transition: all 0.2s;
            border-left: 4px solid #007bff;
        }

        .kanban-card:active {
            cursor: grabbing;
        }

        .kanban-card.dragging {
            opacity: 0.5;
        }

        .kanban-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .drop-zone {
            transition: all 0.2s;
        }

        .drop-zone.drag-over {
            background-color: #e2e6ea;
            border: 2px dashed #007bff;
        }
    </style>

    <div class="row">
        <div class="col-md-12 mb-3">
            <div class="card card-primary card-outline shadow-sm">
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
                            <p class="mb-1"><strong>Deskripsi:</strong> {{ $progja->deskripsi ?: 'Tidak ada deskripsi.' }}
                            </p>
                            <p class="mb-1"><strong>Periode:</strong> {{ date('d M Y', strtotime($progja->tgl_mulai)) }} -
                                {{ date('d M Y', strtotime($progja->tgl_selesai)) }}</p>
                            <p class="mb-0"><strong>Estimasi Anggaran:</strong> <span
                                    class="text-success font-weight-bold">Rp
                                    {{ number_format($progja->estimasi_anggaran, 0, ',', '.') }}</span></p>
                        </div>
                        <div class="col-md-4 text-right">
                            <a href="{{ route('progja.edit', $progja) }}" class="btn btn-warning shadow-sm">
                                <i class="fas fa-edit"></i> Edit Progja
                            </a>
                            <button type="button" class="btn btn-primary shadow-sm" data-toggle="modal"
                                data-target="#modalTugas">
                                <i class="fas fa-plus"></i> Tambah Tugas Baru
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- PAPAN KANBAN (DRAG & DROP) --}}
    <div class="row">
        {{-- KOLOM TO DO --}}
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-secondary text-white rounded-top">
                    <h6 class="mb-0 font-weight-bold"><i class="fas fa-list-ul mr-1"></i> To Do</h6>
                    <span class="badge badge-light float-right">{{ $todos->count() }}</span>
                </div>
                <div class="card-body kanban-column drop-zone" data-status="todo" id="column-todo">
                    @foreach ($todos as $tugas)
                        <div class="card kanban-card mb-2" data-id="{{ $tugas->id }}"
                            style="border-left-color: #6c757d;">
                            <div class="card-body p-2">
                                <div class="d-flex justify-content-between">
                                    <strong class="text-dark">{{ $tugas->nama }}</strong>
                                    <button class="btn btn-sm text-danger delete-tugas p-0" data-id="{{ $tugas->id }}"
                                        title="Hapus Tugas">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <hr class="my-1">
                                @if ($tugas->assignee)
                                    <small class="text-primary font-weight-bold"><i class="fas fa-user-circle mr-1"></i>
                                        {{ $tugas->assignee->name }}</small>
                                @endif
                                @if ($tugas->deadline)
                                    <br><small class="text-danger"><i class="far fa-clock mr-1"></i>
                                        {{ date('d M Y', strtotime($tugas->deadline)) }}</small>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- KOLOM PROGRESS --}}
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white rounded-top">
                    <h6 class="mb-0 font-weight-bold"><i class="fas fa-spinner fa-spin mr-1"></i> In Progress</h6>
                    <span class="badge badge-light float-right">{{ $progress->count() }}</span>
                </div>
                <div class="card-body kanban-column drop-zone" data-status="progress" id="column-progress">
                    @foreach ($progress as $tugas)
                        <div class="card kanban-card mb-2" data-id="{{ $tugas->id }}"
                            style="border-left-color: #007bff;">
                            <div class="card-body p-2">
                                <div class="d-flex justify-content-between">
                                    <strong class="text-dark">{{ $tugas->nama }}</strong>
                                    <button class="btn btn-sm text-danger delete-tugas p-0" data-id="{{ $tugas->id }}">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <hr class="my-1">
                                @if ($tugas->assignee)
                                    <small class="text-primary font-weight-bold"><i class="fas fa-user-circle mr-1"></i>
                                        {{ $tugas->assignee->name }}</small>
                                @endif
                                @if ($tugas->deadline)
                                    <br><small class="text-danger"><i class="far fa-clock mr-1"></i>
                                        {{ date('d M Y', strtotime($tugas->deadline)) }}</small>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- KOLOM DONE --}}
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-success text-white rounded-top">
                    <h6 class="mb-0 font-weight-bold"><i class="fas fa-check-double mr-1"></i> Done</h6>
                    <span class="badge badge-light float-right">{{ $done->count() }}</span>
                </div>
                <div class="card-body kanban-column drop-zone" data-status="done" id="column-done">
                    @foreach ($done as $tugas)
                        <div class="card kanban-card mb-2 bg-light" data-id="{{ $tugas->id }}"
                            style="border-left-color: #28a745;">
                            <div class="card-body p-2">
                                <div class="d-flex justify-content-between">
                                    <strong class="text-muted"
                                        style="text-decoration: line-through;">{{ $tugas->nama }}</strong>
                                    <button class="btn btn-sm text-danger delete-tugas p-0" data-id="{{ $tugas->id }}">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <hr class="my-1">
                                @if ($tugas->assignee)
                                    <small class="text-muted"><i class="fas fa-user-circle mr-1"></i>
                                        {{ $tugas->assignee->name }}</small>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- KOLOM REVISI --}}
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-warning rounded-top">
                    <h6 class="mb-0 font-weight-bold text-dark"><i class="fas fa-exclamation-triangle mr-1"></i> Revisi</h6>
                    <span class="badge badge-dark float-right">{{ $revisi->count() }}</span>
                </div>
                <div class="card-body kanban-column drop-zone" data-status="revisi" id="column-revisi">
                    @foreach ($revisi as $tugas)
                        <div class="card kanban-card mb-2" data-id="{{ $tugas->id }}"
                            style="border-left-color: #ffc107;">
                            <div class="card-body p-2">
                                <div class="d-flex justify-content-between">
                                    <strong class="text-dark">{{ $tugas->nama }}</strong>
                                    <button class="btn btn-sm text-danger delete-tugas p-0"
                                        data-id="{{ $tugas->id }}">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <hr class="my-1">
                                @if ($tugas->assignee)
                                    <small class="text-primary font-weight-bold"><i class="fas fa-user-circle mr-1"></i>
                                        {{ $tugas->assignee->name }}</small>
                                @endif
                                @if ($tugas->deadline)
                                    <br><small class="text-danger"><i class="far fa-clock mr-1"></i>
                                        {{ date('d M Y', strtotime($tugas->deadline)) }}</small>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL TAMBAH TUGAS --}}
    <div class="modal fade" id="modalTugas" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-clipboard-check mr-2"></i> Tambah Tugas Baru</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <form id="formTugas">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nama Tugas <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control"
                                placeholder="Contoh: Buat desain banner" required>
                        </div>
                        <div class="form-group">
                            <label>Delegasikan Ke (Assignee)</label>
                            <select name="assignee_id" class="form-control">
                                <option value="">-- Biarkan kosong jika belum ada --</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Batas Waktu (Deadline)</label>
                            <input type="date" name="deadline" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Simpan
                            Tugas</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(function() {
                // Drag & Drop Logic
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

                        // Efek loading sementara saat drag
                        ui.draggable.css('opacity', '0.5');

                        $.ajax({
                            url: "/progja/tugas/" + tugasId + "/status",
                            type: "PUT",
                            data: {
                                _token: "{{ csrf_token() }}",
                                status: newStatus
                            },
                            success: function(response) {
                                if (response.success) {
                                    location.reload(); // Reload agar badge counter update
                                } else {
                                    alert("Gagal memindahkan tugas.");
                                    ui.draggable.css('opacity', '1');
                                }
                            },
                            error: function() {
                                alert("Terjadi kesalahan sistem.");
                                ui.draggable.css('opacity', '1');
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

                // Form Submit via AJAX
                $("#formTugas").submit(function(e) {
                    e.preventDefault();
                    var btn = $(this).find('button[type="submit"]');
                    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');

                    $.ajax({
                        url: "{{ route('progja.tugas.store', $progja) }}",
                        type: "POST",
                        data: $(this).serialize(),
                        success: function() {
                            location.reload();
                        },
                        error: function(xhr) {
                            alert("Error: " + (xhr.responseJSON?.message ||
                                "Gagal menyimpan tugas."));
                            btn.prop('disabled', false).html(
                                '<i class="fas fa-save mr-1"></i> Simpan Tugas');
                        }
                    });
                });

                // Hapus Tugas
                $(".delete-tugas").click(function() {
                    if (!confirm("Apakah Anda yakin ingin menghapus tugas ini?")) return;
                    var tugasId = $(this).data("id");
                    var card = $(this).closest('.kanban-card');

                    card.css('opacity', '0.5');

                    $.ajax({
                        url: "/progja/tugas/" + tugasId,
                        type: "DELETE",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function() {
                            location.reload();
                        },
                        error: function() {
                            alert("Gagal menghapus tugas.");
                            card.css('opacity', '1');
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection
