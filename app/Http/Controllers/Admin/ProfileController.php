<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    // ==========================================================
    // 1. PROSES UPDATE DATA DIRI & FOTO PROFIL
    // ==========================================================
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        // Validasi inputan form
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'no_hp' => 'nullable|string|max:20',
            'foto'  => 'nullable|image|mimes:jpeg,jpg,png|max:2048', // Maksimal 2MB
        ]);

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
            'no_hp' => $request->no_hp,
        ];

        // Logika jika pengguna mengunggah foto profil baru
        if ($request->hasFile('foto')) {
            // Bersihkan foto lama di folder storage jika ada (biar tidak jadi sampah)
            if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                Storage::disk('public')->delete($user->foto);
            }

            $file = $request->file('foto');
            $filename = time() . '_avatar.' . $file->getClientOriginalExtension();

            // Simpan ke storage/app/public/avatars
            $data['foto'] = $file->storeAs('avatars', $filename, 'public');
        }

        $user->update($data);

        return redirect()->back()->with('success', 'Alhamdulillah, profil Anda berhasil diperbarui!');
    }

    // ==========================================================
    // 2. PROSES UBAH PASSWORD (BENTENG KEAMANAN)
    // ==========================================================
    public function updatePassword(Request $request)
    {
        // Validasi kecocokan password & konfirmasi password
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|string|min:8|confirmed', // 'confirmed' otomatis mengecek field password_confirmation
        ]);

        $user = auth()->user();

        // Cek apakah password lama yang dimasukkan cocok dengan di database
        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Password saat ini yang Anda masukkan salah.',
            ]);
        }

        // Amankan password baru dengan Hash dan simpan
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->back()->with('success_password', 'Hore! Password Anda berhasil diubah. Gunakan password baru ini untuk login berikutnya.');
    }
}
