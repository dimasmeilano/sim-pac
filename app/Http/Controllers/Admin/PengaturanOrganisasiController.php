<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PengaturanOrganisasiController extends Controller
{
    // 1. Menampilkan halaman form pengaturan organisasi sendiri
    public function edit()
    {
        // Kunci utama: Otomatis mengambil data organisasi dari user yang sedang login
        $organisasi = auth()->user()->organization;

        if (!$organisasi) {
            return redirect()->route('dashboard')->with('error', 'Akun Anda belum dikaitkan dengan organisasi manapun.');
        }

        return view('admin.organizations.pengaturan', compact('organisasi'));
    }

    // 2. Memproses pembaruan data & upload file atribut
    public function update(Request $request)
    {
        $organisasi = auth()->user()->organization;

        $request->validate([
            'alamat'            => 'nullable|string',
            'kontak'            => 'nullable|string|max:50',
            'email'             => 'nullable|email|max:100',
            'website'           => 'nullable|string|max:100',
            'logo'              => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'stempel'           => 'nullable|image|mimes:png|max:2048', // Disarankan PNG transparan
            'kop_surat_ipnu'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'kop_surat_ippnu'   => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'kop_surat_bersama' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Ambil data teks terlebih dahulu
        $data = $request->only(['alamat', 'kontak', 'email', 'website']);

        // Logika upload file dinamis (Otomatis hapus file lama jika update baru)
        $fileFields = ['logo', 'stempel', 'kop_surat_ipnu', 'kop_surat_ippnu', 'kop_surat_bersama'];
        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                // Hapus file lama jika ada di storage
                if ($organisasi->$field && Storage::disk('public')->exists($organisasi->$field)) {
                    Storage::disk('public')->delete($organisasi->$field);
                }

                $file = $request->file($field);
                $filename = time() . '_' . $field . '.' . $file->getClientOriginalExtension();

                // Simpan ke folder public/organisasi
                $data[$field] = $file->storeAs('organisasi', $filename, 'public');
            }
        }

        // Eksekusi update ke database
        $organisasi->update($data);

        return redirect()->back()->with('success', 'Alhamdulillah, identitas dan kelengkapan atribut organisasi berhasil diperbarui!');
    }
}
