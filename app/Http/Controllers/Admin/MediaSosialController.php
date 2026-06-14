<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MediaSosial;
use Illuminate\Http\Request;

class MediaSosialController extends Controller
{
    public function index()
    {
        // HANYA tarik medsos milik organisasi user yang sedang login
        $medsos = MediaSosial::where('organization_id', auth()->user()->organization_id)->latest()->get();
        return view('admin.media_sosial.index', compact('medsos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_platform' => 'required|string|max:50',
            'url_link'      => 'required|url|max:255'
        ]);

        MediaSosial::create([
            'nama_platform'   => $request->nama_platform,
            'url_link'        => $request->url_link,
            'organization_id' => auth()->user()->organization_id // Kunci ke Ranting masing-masing
        ]);

        return redirect()->route('media-sosial.index')->with('success', 'Media Sosial berhasil ditambahkan!');
    }

    public function update(Request $request, MediaSosial $media_sosial)
    {
        // Proteksi keamanan ganda: Pastikan yang diedit benar-benar milik organisasinya
        if ($media_sosial->organization_id !== auth()->user()->organization_id) {
            abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'nama_platform' => 'required|string|max:50',
            'url_link'      => 'required|url|max:255'
        ]);

        $media_sosial->update([
            'nama_platform' => $request->nama_platform,
            'url_link'      => $request->url_link,
        ]);

        return redirect()->route('media-sosial.index')->with('success', 'Tautan Media Sosial berhasil diperbarui!');
    }

    public function destroy(MediaSosial $media_sosial)
    {
        if ($media_sosial->organization_id !== auth()->user()->organization_id) {
            abort(403, 'Akses ditolak.');
        }

        $media_sosial->delete();
        return redirect()->route('media-sosial.index')->with('success', 'Media Sosial berhasil dihapus!');
    }
}
