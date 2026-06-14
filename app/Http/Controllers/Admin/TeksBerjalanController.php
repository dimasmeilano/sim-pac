<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeksBerjalan;
use Illuminate\Http\Request;

class TeksBerjalanController extends Controller
{
    public function index()
    {
        $teks_berjalans = TeksBerjalan::latest()->get();
        return view('admin.teks_berjalan.index', compact('teks_berjalans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'isi_teks' => 'required|string|max:1000'
        ]);

        TeksBerjalan::create([
            'isi_teks'     => $request->isi_teks,
            'status_aktif' => 1 // Langsung aktif saat dibuat
        ]);

        return redirect()->route('teks-berjalan.index')->with('success', 'Teks berjalan berhasil ditambahkan!');
    }

    public function update(Request $request, TeksBerjalan $teks_berjalan)
    {
        $request->validate([
            'isi_teks'     => 'required|string|max:1000',
            'status_aktif' => 'required|boolean'
        ]);

        $teks_berjalan->update([
            'isi_teks'     => $request->isi_teks,
            'status_aktif' => $request->status_aktif
        ]);

        return redirect()->route('teks-berjalan.index')->with('success', 'Pengaturan teks berjalan diperbarui!');
    }

    public function destroy(TeksBerjalan $teks_berjalan)
    {
        $teks_berjalan->delete();
        return redirect()->route('teks-berjalan.index')->with('success', 'Teks berjalan dihapus!');
    }
}
