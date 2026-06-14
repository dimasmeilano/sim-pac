<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Artikel;
use App\Models\Komentar;
use Illuminate\Http\Request;

class ArtikelController extends Controller
{
    // 1. Menampilkan isi artikel
    public function show($slug)
    {
        // Cari artikel berdasarkan slug dan pastikan statusnya publish
        $artikel = Artikel::with(['user', 'kategori', 'organization'])
            ->where('slug', $slug)
            ->where('status', 'publish')
            ->firstOrFail();

        // Fitur 1: Tambah jumlah tayangan (views) secara otomatis
        $artikel->increment('dilihat');

        // Tarik semua komentar milik artikel ini
        $komentars = Komentar::where('artikel_id', $artikel->id)->latest()->get();

        // Tarik 4 berita terbaru lainnya untuk rekomendasi bacaan di sidebar
        $berita_lain = Artikel::where('status', 'publish')
            ->where('id', '!=', $artikel->id)
            ->latest()
            ->take(4)
            ->get();

        return view('public.artikel_detail', compact('artikel', 'komentars', 'berita_lain'));
    }

    // 2. Menyimpan komentar dari pengunjung
    public function storeKomentar(Request $request, $slug)
    {
        $artikel = Artikel::where('slug', $slug)->firstOrFail();

        $request->validate([
            'nama_pengunjung' => 'required|string|max:255',
            'email'           => 'nullable|email|max:255',
            'isi_komentar'    => 'required|string'
        ]);

        // Fitur 2: Komentar langsung tayang (insert ke database)
        Komentar::create([
            'artikel_id'      => $artikel->id,
            'nama_pengunjung' => $request->nama_pengunjung,
            'email'           => $request->email,
            'isi_komentar'    => $request->isi_komentar
        ]);

        return redirect()->back()->with('success_komentar', 'Komentar Anda berhasil ditayangkan!');
    }
}
