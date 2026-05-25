<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class MemberController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $members = User::with('organization')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.members.index', compact('members'));
    }

    public function create()
    {
        $organizations = Organization::orderBy('type')->orderBy('name')->get();
        return view('admin.members.create', compact('organizations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'nik' => 'required|string|size:16|unique:users',
            'no_hp' => 'required|string|max:15',
            'organization_id' => 'nullable|exists:organizations,id',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'jk' => 'nullable|in:L,P',
            'pendidikan' => 'nullable|string|max:50',
            'status_anggota' => 'required|in:aktif,nonaktif,meninggal,keluar',
            'tgl_bergabung' => 'nullable|date',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // Upload foto
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            $filename = time() . '_' . $foto->getClientOriginalName();
            $fotoPath = $foto->storeAs('anggota/foto', $filename, 'public');
        }

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'nik' => $request->nik,
            'no_hp' => $request->no_hp,
            'organization_id' => $request->organization_id,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jk' => $request->jk,
            'pendidikan' => $request->pendidikan,
            'status_anggota' => $request->status_anggota,
            'tgl_bergabung' => $request->tgl_bergabung ?? date('Y-m-d'),
            'foto' => $fotoPath,
        ]);

        // Assign role default
        $user->assignRole('anggota_biasa');

        return redirect()->route('members.index')->with('success', 'Anggota berhasil ditambahkan');
    }

    public function edit(User $member)
    {
        $organizations = Organization::orderBy('type')->orderBy('name')->get();
        return view('admin.members.edit', compact('member', 'organizations'));
    }

    public function update(Request $request, User $member)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $member->id,
            'nik' => 'required|string|size:16|unique:users,nik,' . $member->id,
            'no_hp' => 'required|string|max:15',
            'organization_id' => 'nullable|exists:organizations,id',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'jk' => 'nullable|in:L,P',
            'pendidikan' => 'nullable|string|max:50',
            'status_anggota' => 'required|in:aktif,nonaktif,meninggal,keluar',
            'tgl_bergabung' => 'nullable|date',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // Upload foto baru
        if ($request->hasFile('foto')) {
            if ($member->foto && Storage::disk('public')->exists($member->foto)) {
                Storage::disk('public')->delete($member->foto);
            }

            $foto = $request->file('foto');
            $filename = time() . '_' . $foto->getClientOriginalName();
            $fotoPath = $foto->storeAs('anggota/foto', $filename, 'public');
            $member->foto = $fotoPath;
        }

        // Update password jika diisi
        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8|confirmed']);
            $member->password = Hash::make($request->password);
        }

        $member->update([
            'name' => $request->name,
            'email' => $request->email,
            'nik' => $request->nik,
            'no_hp' => $request->no_hp,
            'organization_id' => $request->organization_id,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jk' => $request->jk,
            'pendidikan' => $request->pendidikan,
            'status_anggota' => $request->status_anggota,
            'tgl_bergabung' => $request->tgl_bergabung,
        ]);

        return redirect()->route('members.index')->with('success', 'Anggota berhasil diupdate');
    }

    public function destroy(User $member)
    {
        if ($member->foto && Storage::disk('public')->exists($member->foto)) {
            Storage::disk('public')->delete($member->foto);
        }

        $member->delete();

        return redirect()->route('members.index')->with('success', 'Anggota berhasil dihapus');
    }

    public function show(User $member)
    {
        $member->load('organization');
        return view('admin.members.show', compact('member'));
    }
}
