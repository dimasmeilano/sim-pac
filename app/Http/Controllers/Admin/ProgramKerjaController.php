<?php

namespace App\Http\Controllers\Admin;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\ProgramKerja;
use App\Models\SubTugas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgramKerjaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        \App\Models\ProgramKerja::where('status', 'active')
            ->where('tgl_selesai', '<', now()->toDateString())
            ->update(['status' => 'completed']);
        $programKerja = ProgramKerja::with('organization', 'tugas')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('admin.progja.index', compact('programKerja'));
    }

    public function create()
    {
        return view('admin.progja.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:200',
            'deskripsi' => 'nullable|string',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
            'status' => 'required|in:planning,active,completed,cancelled',
        ]);

        $progja = ProgramKerja::create([
            'organization_id' => auth()->user()->organization_id,
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi,
            'tgl_mulai' => $request->tgl_mulai,
            'tgl_selesai' => $request->tgl_selesai,
            'status' => $request->status,
        ]);

        return redirect()->route('progja.show', $progja)->with('success', 'Program Kerja berhasil dibuat');
    }

    public function show(ProgramKerja $progja)
    {
        $progja->load('tugas.assignee', 'messages.user', 'organization');

        $todos = $progja->tugas->where('status', 'todo');
        $progress = $progja->tugas->where('status', 'progress');
        $done = $progja->tugas->where('status', 'done');
        $revisi = $progja->tugas->where('status', 'revisi');

        $users = User::orderBy('name')->get();

        return view('admin.progja.show', compact('progja', 'todos', 'progress', 'done', 'revisi', 'users'));
    }

    public function edit(ProgramKerja $progja)
    {
        return view('admin.progja.edit', compact('progja'));
    }

    public function update(Request $request, ProgramKerja $progja)
    {
        $request->validate([
            'nama' => 'required|string|max:200',
            'deskripsi' => 'nullable|string',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
            'status' => 'required|in:planning,active,completed,cancelled',
        ]);

        $progja->update($request->all());

        return redirect()->route('progja.show', $progja)->with('success', 'Program Kerja berhasil diupdate');
    }

    public function destroy(ProgramKerja $progja)
    {
        $progja->delete();
        return redirect()->route('progja.index')->with('success', 'Program Kerja berhasil dihapus');
    }

    // ============ TUGAS METHODS ============

    public function tugasStore(Request $request, ProgramKerja $progja)
    {
        $request->validate([
            'nama' => 'required|string|max:200',
            'assignee_id' => 'nullable|exists:users,id',
            'deadline' => 'nullable|date',
        ]);

        $urutan = SubTugas::where('progja_id', $progja->id)->max('urutan') + 1;

        $tugas = SubTugas::create([
            'progja_id' => $progja->id,
            'nama' => $request->nama,
            'assignee_id' => $request->assignee_id,
            'deadline' => $request->deadline,
            'status' => 'todo',
            'urutan' => $urutan,
        ]);

        return response()->json(['success' => true, 'tugas' => $tugas]);
    }

    public function tugasUpdateStatus(Request $request, SubTugas $tugas)
    {
        $request->validate([
            'status' => 'required|in:todo,progress,done,revisi',
        ]);

        $tugas->update(['status' => $request->status]);

        return response()->json(['success' => true]);
    }

    public function tugasUpdateOrder(Request $request)
    {
        foreach ($request->order as $index => $tugasId) {
            SubTugas::where('id', $tugasId)->update(['urutan' => $index]);
        }

        return response()->json(['success' => true]);
    }

    public function tugasDestroy(SubTugas $tugas)
    {
        $tugas->delete();
        return response()->json(['success' => true]);
    }

    // ============ CHAT METHODS ============

    public function sendMessage(Request $request, ProgramKerja $progja)
    {
        // Validasi: Pesan boleh kosong asalkan ada file, atau sebaliknya
        $request->validate([
            'message' => 'nullable|string',
            'file'    => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx|max:5120',
        ]);

        if (!$request->message && !$request->hasFile('file')) {
            return response()->json(['error' => 'Pesan atau file tidak boleh kosong.'], 422);
        }

        // =========================================================
        // (Logika Regex @mention Anda taruh kembali di sini jika ada)
        // =========================================================

        // Proses Upload File jika ada
        $filePath = null;
        $fileName = null;
        $fileType = null;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = $file->getClientOriginalName();
            $fileType = $file->getClientOriginalExtension();
            $filePath = $file->storeAs('chat_files/' . $progja->id, time() . '_' . $fileName, 'public');
        }

        $messageData = Message::create([
            'progja_id'    => $progja->id,
            'user_id'      => Auth::id(),
            'message'      => $request->message ?? '',
            'tagged_users' => null, // Sesuaikan dengan logika tag Anda
            'file_path'    => $filePath,
            'file_name'    => $fileName,
            'file_type'    => $fileType,
            'reply_to_id'  => $request->reply_to_id, // <-- TAMBAHKAN INI
        ]);

        $messageData->load('user');

        // Trigger Broadcast (Jika Reverb/Pusher sudah jalan)
        if (class_exists(\App\Events\MessageSent::class)) {
            broadcast(new \App\Events\MessageSent($messageData))->toOthers();
        }

        return response()->json([
            'success' => true,
            'message' => $messageData
        ]);
    }


    public function getMessages(ProgramKerja $progja)
    {
        // 1. KITA AKTIFKAN KEMBALI 'repliedMessage.user'
        $messages = $progja->messages()
            ->with(['user', 'repliedMessage.user'])
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'messages' => $messages->map(function ($msg) {

                $date = $msg->created_at;
                if ($date->isToday()) {
                    $dateLabel = 'Hari ini';
                } elseif ($date->isYesterday()) {
                    $dateLabel = 'Kemarin';
                } else {
                    $dateLabel = $date->translatedFormat('d F Y');
                }

                // 2. KITA AKTIFKAN KEMBALI DATA REPLY-NYA
                $replyData = null;
                if ($msg->reply_to_id && $msg->repliedMessage) {
                    $replyData = [
                        'name'    => $msg->repliedMessage->user ? $msg->repliedMessage->user->name : 'User',
                        'message' => $msg->repliedMessage->message ? $msg->repliedMessage->message : '📂 Lampiran File'
                    ];
                }

                return [
                    'id'         => $msg->id,
                    'user_id'    => $msg->user_id,
                    'message'    => $msg->message,
                    'file_path'  => $msg->file_path,
                    'file_name'  => $msg->file_name,
                    'file_type'  => $msg->file_type,
                    'user'       => [
                        'id'   => $msg->user->id ?? 0,
                        'name' => $msg->user->name ?? 'Unknown'
                    ],
                    'time'       => $date->format('H:i'),
                    'date_group' => $date->format('Y-m-d'),
                    'date_label' => $dateLabel,
                    'reply_to'   => $replyData, // Data reply dikirim ke Javascript
                ];
            })
        ]);
    }
}
