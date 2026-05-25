<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SignatureController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $organization = auth()->user()->organization;
        return view('admin.signature', compact('organization'));
    }

    public function uploadStempel(Request $request)
    {
        $request->validate([
            'stempel' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $user = auth()->user();
        $org = $user->organization;

        if (!$org || $org->ketua_id != $user->id) {
            return response()->json(['success' => false, 'message' => 'Hanya Ketua yang dapat upload stempel']);
        }

        $file = $request->file('stempel');
        $filename = 'stempel_' . $org->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('signatures/stempel', $filename, 'public');

        $org->stempel = $path;
        $org->save();

        return response()->json(['success' => true]);
    }


    public function save(Request $request)
    {
        $request->validate([
            'role' => 'required|in:ketua,sekretaris',
            'signature' => 'required|string'
        ]);

        $user = auth()->user();
        $org = $user->organization;

        if (!$org) {
            return response()->json(['success' => false, 'message' => 'Anda tidak terdaftar di organisasi mana pun']);
        }

        // Cek apakah user adalah ketua atau sekretaris
        if ($request->role == 'ketua' && $org->ketua_id != $user->id) {
            return response()->json(['success' => false, 'message' => 'Anda bukan Ketua organisasi ini']);
        }

        if ($request->role == 'sekretaris' && $org->sekretaris_id != $user->id) {
            return response()->json(['success' => false, 'message' => 'Anda bukan Sekretaris organisasi ini']);
        }

        // Decode base64 image
        $imageData = $request->signature;
        $imageData = str_replace('data:image/png;base64,', '', $imageData);
        $imageData = base64_decode($imageData);

        $filename = 'ttd_' . $org->id . '_' . $request->role . '_' . time() . '.png';
        $path = 'signatures/' . $filename;

        Storage::disk('public')->put($path, $imageData);

        if ($request->role == 'ketua') {
            $org->ttd_ketua = $path;
        } else {
            $org->ttd_sekretaris = $path;
        }

        $org->save();

        return response()->json(['success' => true]);
    }
}
