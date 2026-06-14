<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IdentitasWeb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class IdentitasWebController extends Controller
{
    // Menampilkan form pengaturan
    public function index()
    {
        // Ambil data pertama. Jika tabel masih kosong, buat data default otomatis.
        $identitas = IdentitasWeb::firstOrCreate(
            ['id' => 1],
            ['nama_web' => 'SIM PAC IPNU IPPNU']
        );

        return view('admin.identitas_web.index', compact('identitas'));
    }

    // Menyimpan pembaruan data
    public function update(Request $request)
    {
        $identitas = IdentitasWeb::first();

        $request->validate([
            'nama_web' => 'required|string|max:255',
            'logo'     => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'email'    => 'nullable|email',
            'telepon'  => 'nullable|string|max:20',

            // Kolom baru kita:
            'sejarah_singkat'  => 'nullable|string',
            'visi_misi'        => 'nullable|string',
            'makna_lambang_ipnu'   => 'nullable|string',
            'makna_lambang_ippnu'  => 'nullable|string',
        ]);

        $data = $request->except(['_token', 'logo']);

        // Jika ada unggahan logo baru
        if ($request->hasFile('logo')) {
            // Hapus logo lama jika ada
            if ($identitas->logo) {
                Storage::disk('public')->delete($identitas->logo);
            }
            // Simpan logo baru
            $data['logo'] = $request->file('logo')->store('identitas', 'public');
        }

        $identitas->update($data);

        return redirect()->back()->with('success', 'Identitas Website berhasil diperbarui!');
    }
}
