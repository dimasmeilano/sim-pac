<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OrganizationSettingController extends Controller
{
    public function edit(Organization $organization)
    {
        $users = User::orderBy('name')->get();
        return view('admin.organizations.setting', compact('organization', 'users'));
    }

    public function update(Request $request, Organization $organization)
    {
        $request->validate([
            'ketua_id' => 'nullable|exists:users,id',
            'wakil_ketua_1_id' => 'nullable|exists:users,id',
            'wakil_ketua_2_id' => 'nullable|exists:users,id',
            'wakil_ketua_3_id' => 'nullable|exists:users,id',
            'wakil_ketua_4_id' => 'nullable|exists:users,id',
            'wakil_ketua_5_id' => 'nullable|exists:users,id',
            'sekretaris_id' => 'nullable|exists:users,id',
            'wakil_sekretaris_1_id' => 'nullable|exists:users,id',
            'wakil_sekretaris_2_id' => 'nullable|exists:users,id',
            'wakil_sekretaris_3_id' => 'nullable|exists:users,id',
            'wakil_sekretaris_4_id' => 'nullable|exists:users,id',
            'wakil_sekretaris_5_id' => 'nullable|exists:users,id',
            'bendahara_id' => 'nullable|exists:users,id',
            'wakil_bendahara_1_id' => 'nullable|exists:users,id',
            'wakil_bendahara_2_id' => 'nullable|exists:users,id',
            'wakil_bendahara_3_id' => 'nullable|exists:users,id',
        ]);

        // Validasi jumlah minimal wakil ketua (minimal 3 terisi)
        $wakilKetuaTerisi = 0;
        for ($i = 1; $i <= 5; $i++) {
            if ($request->input("wakil_ketua_{$i}_id")) $wakilKetuaTerisi++;
        }
        if ($wakilKetuaTerisi < 3) {
            return back()->with('error', 'Minimal 3 Wakil Ketua harus diisi');
        }

        // Validasi jumlah minimal wakil sekretaris (minimal 3 terisi)
        $wakilSekretarisTerisi = 0;
        for ($i = 1; $i <= 5; $i++) {
            if ($request->input("wakil_sekretaris_{$i}_id")) $wakilSekretarisTerisi++;
        }
        if ($wakilSekretarisTerisi < 3) {
            return back()->with('error', 'Minimal 3 Wakil Sekretaris harus diisi');
        }

        $organization->update($request->all());

        // Sinkronisasi role untuk setiap jabatan
        $this->syncRoles($organization, $request);

        return redirect()->route('organizations.index')->with('success', 'Pengurus berhasil diupdate');
    }

    private function syncRoles($organization, $request)
    {
        // Sync Ketua
        $this->assignRoleToUser($request->ketua_id, 'ketua_pac');

        // Sync Wakil Ketua
        for ($i = 1; $i <= 5; $i++) {
            $this->assignRoleToUser($request->input("wakil_ketua_{$i}_id"), 'wakil_ketua_pac');
        }

        // Sync Sekretaris
        $this->assignRoleToUser($request->sekretaris_id, 'sekretaris_pac');

        // Sync Wakil Sekretaris
        for ($i = 1; $i <= 5; $i++) {
            $this->assignRoleToUser($request->input("wakil_sekretaris_{$i}_id"), 'wakil_sekretaris_pac');
        }

        // Sync Bendahara
        $this->assignRoleToUser($request->bendahara_id, 'bendahara_pac', true);

        // Sync Wakil Bendahara
        for ($i = 1; $i <= 3; $i++) {
            $this->assignRoleToUser($request->input("wakil_bendahara_{$i}_id"), 'wakil_bendahara_pac');
        }
    }

    private function assignRoleToUser($userId, $roleName, $withPermission = false)
    {
        if ($userId) {
            $user = User::find($userId);
            if ($user && !$user->hasRole($roleName)) {
                $user->assignRole($roleName);
                if ($withPermission) {
                    $user->givePermissionTo('view_keuangan');
                }
            }
        }
    }

    public function profileUpdate(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'no_hp' => 'nullable|string|max:15',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                Storage::disk('public')->delete($user->foto);
            }
            $foto = $request->file('foto');
            $filename = 'foto_' . $user->id . '_' . time() . '.' . $foto->getClientOriginalExtension();
            $fotoPath = $foto->storeAs('profile/foto', $filename, 'public');
            $user->foto = $fotoPath;
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->no_hp = $request->no_hp;
        $user->save();

        return redirect()->route('profile')->with('success', 'Profil berhasil diupdate');
    }
}
