<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    // Fungsi untuk Login Mobile
    public function login(Request $request)
    {
        // 1. Validasi input dari Flutter (terima data dengan nama 'email' dan 'password')
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginInput = $request->input('email');
        $password = $request->input('password');

        // 2. Deteksi otomatis: apakah ini Email atau Username?
        // Jika teks mengandung '@', maka cari di kolom 'email', jika tidak cari di 'username'
        $fieldType = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // 3. Gabungkan kredensial untuk dicocokkan oleh Laravel
        $credentials = [
            $fieldType => $loginInput,
            'password' => $password
        ];

        // 4. Coba lakukan proses Login
        if (Auth::attempt($credentials)) {
            // Ambil user secara spesifik dari Model User
            $user = \App\Models\User::where($fieldType, $loginInput)->first();

            // Hapus semua token lama agar tidak menumpuk (opsional, tapi disarankan)
            $user->tokens()->delete();

            // Buat token baru
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login Berhasil',
                'user'    => $user,
                'token'   => $token
            ], 200);
        }

        // 6. Jika Password/Email salah
        return response()->json([
            'success' => false,
            'message' => 'Email/Username atau Password salah.'
        ], 401);
    }

    // Fungsi untuk Logout (Menghapus Token)
    public function logout(Request $request)
    {
        // Hapus token yang sedang digunakan untuk request ini
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil, token telah dihapus.'
        ], 200);
    }

    // Fungsi untuk mengambil Profil User saat ini (Untuk Tab Profile)
    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->user()->load('organization') // load() untuk menarik relasi tabel
        ], 200);
    }
}
