<?php

namespace App\Http\Controllers\Admin;

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
        $request->validate([
            'message' => 'required|string',
        ]);

        $message = $request->message;
        $taggedUsers = [];

        // Cari mention dengan pattern @username
        preg_match_all('/@([a-zA-Z0-9_]+)/', $message, $matches);

        if (!empty($matches[1])) {
            $usernames = $matches[1];
            $taggedUsers = User::whereIn('name', $usernames)->pluck('id')->toArray();

            // Ubah mention jadi link HTML
            foreach ($usernames as $username) {
                $user = User::where('name', $username)->first();
                if ($user) {
                    $message = str_replace(
                        '@' . $username,
                        '<a href="javascript:void(0)" class="tagged-user" data-user-id="' . $user->id . '">@' . $username . '</a>',
                        $message
                    );
                }
            }
        }

        $messageData = Message::create([
            'progja_id' => $progja->id,
            'user_id' => Auth::id(),
            'message' => $message,
            'tagged_users' => !empty($taggedUsers) ? json_encode($taggedUsers) : null,
        ]);

        $messageData->load('user');

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $messageData->id,
                'message' => $messageData->message,
                'user' => [
                    'name' => $messageData->user->name,
                ],
                'created_at' => $messageData->created_at->format('H:i, d/m/Y'),
            ]
        ]);
    }


    public function getMessages(ProgramKerja $progja)
    {
        $messages = $progja->messages()
            ->with('user')
            ->orderBy('created_at', 'asc')  // <-- ASCENDING = lama di atas
            ->get();

        return response()->json([
            'messages' => $messages->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'message' => $msg->message,
                    'user' => ['name' => $msg->user->name],
                    'created_at' => $msg->created_at->format('H:i, d/m/Y'),
                ];
            })
        ]);
    }
}
