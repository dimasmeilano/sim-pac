<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil data user yang sedang request (yang sedang login)
        $currentUser = $request->user();

        // 2. Tarik data user lain yang satu organisasi, KECUALI dirinya sendiri
        $contacts = User::where('organization_id', $currentUser->organization_id)
            ->where('id', '!=', $currentUser->id)
            ->select('id', 'name', 'email') // Sesuaikan jika ada kolom 'no_hp', 'jabatan', atau 'avatar'
            ->get();

        // 3. Kembalikan data dalam format JSON
        return response()->json([
            'success' => true,
            'message' => 'Daftar kontak berhasil ditarik',
            'data' => $contacts
        ], 200);
    }
}
