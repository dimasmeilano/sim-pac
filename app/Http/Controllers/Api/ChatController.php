<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    // 1. Mengambil daftar ruangan chat milik User yang sedang login
    public function getRooms()
    {
        $userId = Auth::id(); // Ambil ID user yang sedang login

        $rooms = \App\Models\ChatRoom::whereHas('users', function ($q) use ($userId) {
            $q->where('user_id', $userId); // Ambil ruangan yang di dalamnya ada kita
        })
            ->with(['users' => function ($q) use ($userId) {
                $q->where('user_id', '!=', $userId); // Ambil data lawan bicara kita
            }])
            // 1. TAMBAHAN: Ambil 1 pesan terakhir
            ->with(['messages' => function ($q) {
                $q->latest()->limit(1);
            }])
            // 2. TAMBAHAN: Hitung pesan dari lawan bicara yang belum kita baca
            ->withCount(['messages as unread_count' => function ($q) use ($userId) {
                $q->where('is_read', false)->where('sender_id', '!=', $userId);
            }])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $rooms
        ]);
    }

    // 2. Mengambil riwayat pesan di dalam satu ruangan
    public function getMessages($roomId)
    {
        $messages = ChatMessage::where('chat_room_id', $roomId)
            ->with('sender:id,name') // Bawa nama pengirimnya
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $messages
        ]);
    }

    // 3. Mengirim pesan baru ke ruangan tertentu
    public function sendMessage(Request $request, $roomId)
    {
        $request->validate([
            'message' => 'required|string'
        ]);

        $message = ChatMessage::create([
            'chat_room_id' => $roomId,
            'sender_id' => $request->user()->id,
            'message' => $request->message,
            'is_read' => false
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pesan terkirim',
            'data' => $message
        ]);
    }

    public function markAsRead($roomId)
    {
        $userId = Auth::id();

        // Ubah status is_read menjadi true untuk semua pesan di ruangan ini
        // yang BUKAN dikirim oleh kita sendiri
        ChatMessage::where('chat_room_id', $roomId)
            ->where('sender_id', '!=', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Pesan telah ditandai dibaca'
        ]);
    }

    // Ambil semua daftar user untuk kontak/pilihan chat
    public function getUsers()
    {
        $myId = Auth::id();
        // Ambil semua user kecuali diri sendiri
        $users = \App\Models\User::where('id', '!=', $myId)->get();
        return response()->json(['success' => true, 'data' => $users]);
    }

    // Membuat Room Baru (Bisa Private atau Group)
    public function createRoom(Request $request)
    {
        $request->validate([
            'type' => 'required|in:private,group',
            'user_ids' => 'required|array', // ID user yang diajak chat/masuk grup
            'name' => 'nullable|string',    // Nama grup (jika tipe group)
        ]);

        $myId = Auth::id();
        $userIds = $request->user_ids;
        $userIds[] = $myId; // Masukkan diri kita sendiri ke dalam grup/chat

        // JIKA CHAT PRIBADI: Cek dulu apakah room private antar kedua user ini sudah ada?
        if ($request->type == 'private') {
            $otherUserId = $request->user_ids[0];
            $existingRoom = ChatRoom::where('type', 'private')
                ->whereHas('users', function ($q) use ($myId) {
                    $q->where('user_id', $myId);
                })
                ->whereHas('users', function ($q) use ($otherUserId) {
                    $q->where('user_id', $otherUserId);
                })
                ->first();

            if ($existingRoom) {
                return response()->json(['success' => true, 'data' => $existingRoom]);
            }
        }

        // Buat Ruangan Baru
        $room = ChatRoom::create([
            'name' => $request->name, // null jika private
            'type' => $request->type,
        ]);

        // Hubungkan para user ke ruangan baru ini (tabel pivot chat_room_user)
        $room->users()->attach($userIds);

        return response()->json(['success' => true, 'data' => $room]);
    }

    public function deleteMessage($id)
    {
        $message = \App\Models\Message::find($id); // Sesuaikan nama model Message Anda

        if (!$message) {
            return response()->json(['message' => 'Pesan tidak ditemukan'], 404);
        }

        // Keamanan tambahan: Pastikan user yang login adalah pengirim pesan ini
        if ($message->sender_id !== auth()->id()) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $message->delete();
        return response()->json(['message' => 'Pesan berhasil dihapus'], 200);
    }

    // Fungsi Menghapus Grup / Room
    public function deleteRoom($id)
    {
        $room = \App\Models\ChatRoom::find($id); // Sesuaikan nama model ChatRoom Anda

        if (!$room) {
            return response()->json(['message' => 'Grup tidak ditemukan'], 404);
        }

        // Hapus grup. (Catatan: jika di tabel Database Anda di-set 'cascadeOnDelete()', 
        // maka seluruh pesan dan partisipan di dalamnya akan ikut terhapus otomatis)
        $room->delete();

        return response()->json(['message' => 'Grup berhasil dihapus'], 200);
    }

    public function clearChat($roomId)
    {
        $room = \App\Models\ChatRoom::find($roomId);

        if (!$room) {
            return response()->json(['message' => 'Obrolan tidak ditemukan'], 404);
        }

        // Hapus semua pesan yang memiliki chat_room_id sesuai dengan ruangan ini.
        // Asumsi nama kolom foreign key Anda adalah 'chat_room_id'
        \App\Models\Message::where('chat_room_id', $roomId)->delete();

        return response()->json(['message' => 'Semua pesan berhasil dibersihkan'], 200);
    }

    // Fungsi Info Obrolan (Menampilkan detail grup & anggota)
    public function roomInfo($roomId)
    {
        // Pastikan Anda sudah membuat relasi 'users()' di model ChatRoom yang
        // menghubungkan tabel chat_rooms dan users melalui chat_room_users
        $room = \App\Models\ChatRoom::with('users')->find($roomId);

        if (!$room) {
            return response()->json(['message' => 'Obrolan tidak ditemukan'], 404);
        }

        return response()->json([
            'id' => $room->id,
            'name' => $room->name,
            'type' => $room->type,
            'created_at' => $room->created_at,
            'participants' => $room->users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ];
            })
        ], 200);
    }
}
