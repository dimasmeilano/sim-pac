<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KategoriArtikel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KategoriArtikelController extends Controller
{
    public function index()
    {
        // Menampilkan daftar kategori
        $kategoris = KategoriArtikel::latest()->get();
        return view('admin.kategori.index', compact('kategoris'));
    }

    public function store(Request $request)
    {
        // Validasi dan Simpan Kategori Baru
        $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori_artikels,nama_kategori'
        ]);

        KategoriArtikel::create([
            'nama_kategori' => $request->nama_kategori,
            'slug'          => Str::slug($request->nama_kategori)
        ]);

        return redirect()->route('kategori.index')->with('success', 'Kategori baru berhasil ditambahkan!');
    }

    public function update(Request $request, KategoriArtikel $kategori)
    {
        // Validasi dan Update Kategori
        $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori_artikels,nama_kategori,' . $kategori->id
        ]);

        $kategori->update([
            'nama_kategori' => $request->nama_kategori,
            'slug'          => Str::slug($request->nama_kategori)
        ]);

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil diperbarui!');
    }

    public function destroy(KategoriArtikel $kategori)
    {
        // Hapus Kategori
        $kategori->delete();
        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil dihapus!');
    }
}
