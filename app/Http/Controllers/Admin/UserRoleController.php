<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserRoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    // Menampilkan daftar seluruh user dan role mereka saat ini
    public function index()
    {
        // Mengambil semua user beserta relasi role-nya
        $users = User::with('roles')->latest()->get();
        return view('admin.user_role.index', compact('users'));
    }

    // Menampilkan form edit role untuk user tertentu
    public function edit(User $user_role)
    {
        // Karena parameter route-nya adalah user_role, variabelnya menyesuaikan
        $user = $user_role;
        $roles = Role::all(); // Mengambil semua daftar role yang ada di database
        return view('admin.user_role.edit', compact('user', 'roles'));
    }

    // Menyimpan perubahan role ke database
    public function update(Request $request, User $user_role)
    {
        $user = $user_role;
        $request->validate([
            'roles' => 'nullable|array' // Boleh kosong jika user dicopot semua jabatannya
        ]);

        // Fitur sakti Spatie: syncRoles akan menghapus role lama dan menggantinya dengan yang baru dicentang
        $user->syncRoles($request->roles);

        return redirect()->route('user-role.index')->with('success', 'Hak akses untuk ' . $user->name . ' berhasil diperbarui!');
    }
}
