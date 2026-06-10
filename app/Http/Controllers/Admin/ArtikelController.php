<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artikel;
use App\Models\KategoriArtikel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArtikelController extends Controller
{
    /**
     * Menampilkan daftar artikel berdasarkan Role yang login
     */
    public function index()
    {
        $user = auth()->user();

        // 1. JIKA YANG LOGIN ADALAH EDITOR (PAC / Pimpinan Redaksi)
        if ($user->hasRole('editor')) {
            // Tarik SEMUA data artikel dari seluruh organisasi/ranting
            // (Kita tambahkan 'organization' di dalam with() agar koneksi database lebih ringan)
            $artikels = Artikel::with(['kategori', 'user', 'organization'])->latest()->get();
        }
        // 2. JIKA YANG LOGIN ADALAH KONTRIBUTOR / FOTOGRAFER (Ranting)
        else {
            // Tarik HANYA data artikel yang organization_id-nya sama dengan milik user yang login
            $artikels = Artikel::with(['kategori', 'user', 'organization'])
                ->where('organization_id', $user->organization_id)
                ->latest()
                ->get();
        }

        return view('admin.artikel.index', compact('artikels'));
    }

    /**
     * Menampilkan form untuk menulis artikel baru (Khusus Kontributor & Editor)
     */
    public function create()
    {
        $this->authorizeAccess(['kontributor', 'editor', 'super_admin']);

        $kategoris = KategoriArtikel::all();
        return view('admin.artikel.create', compact('kategoris'));
    }

    /**
     * Menyimpan artikel baru dari Kontributor
     */
    public function store(Request $request)
    {
        $this->authorizeAccess(['kontributor', 'editor', 'super_admin']);

        $request->validate([
            'judul'       => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori_artikels,id',
            'isi_artikel' => 'required',
            'gambar_cover' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->except(['_token', 'gambar_cover', 'simpan_draft', 'kirim_review']);
        $data['slug'] = Str::slug($request->judul) . '-' . time();
        $data['user_id'] = auth()->id();
        $data['kontributor'] = auth()->user()->name;
        // Mencatat artikel ini milik organisasi/ranting mana
        $data['organization_id'] = auth()->user()->organization_id;

        // Kontributor bisa memilih langsung ajukan review atau simpan draft biasa
        if ($request->has('kirim_review')) {
            $data['status'] = 'menunggu_review';
        } else {
            $data['status'] = 'draft';
        }

        // Jika foto bersumber dari luar organisasi dan langsung diunggah oleh kontributor
        if ($request->hasFile('gambar_cover')) {
            $data['gambar_cover'] = $request->file('gambar_cover')->store('artikel/cover', 'public');
        }

        Artikel::create($data);

        return redirect()->route('artikel.index')->with('success', 'Artikel berhasil dibuat!');
    }

    /**
     * Menampilkan form edit artikel (Wujud form adaptif sesuai Role)
     */
    public function edit(Artikel $artikel)
    {
        $kategoris = KategoriArtikel::all();
        return view('admin.artikel.edit', compact('artikel', 'kategoris'));
    }

    /**
     * Memproses pergerakan alur kerja redaksi (SOP Workflow)
     */
    public function update(Request $request, Artikel $artikel)
    {
        $user = auth()->user();
        $data = $request->except([
            '_token',
            '_method',
            'gambar_cover',
            'simpan_draft',
            'kirim_review',
            'setujui_publish',
            'tolak_revisi',
            'update_biasa',
            'turunkan_tayangan' // <-- Tambahkan ini
        ]);

        // --- SOP 1: ALUR KONTRIBUTOR ---
        if ($user->hasRole('kontributor')) {
            // Kontributor dilarang mengedit jika artikel sedang di-review atau sudah publish
            if (in_array($artikel->status, ['menunggu_review', 'publish'])) {
                abort(403, 'Artikel sedang dikunci oleh meja redaksi.');
            }

            if ($request->has('kirim_review')) {
                $data['status'] = 'menunggu_review';
            } else {
                $data['status'] = 'draft';
            }

            // Kontributor mengupdate gambar jika bersumber dari luar
            if ($request->hasFile('gambar_cover')) {
                if ($artikel->gambar_cover) {
                    Storage::disk('public')->delete($artikel->gambar_cover);
                }
                $data['gambar_cover'] = $request->file('gambar_cover')->store('artikel/cover', 'public');
            }
        }

        // --- SOP 2: ALUR FOTOGRAFER ---
        if ($user->hasRole('fotografer')) {
            // Fotografer hanya boleh menambahkan foto jika artikel dalam status draft / review
            if (in_array($artikel->status, ['publish'])) {
                abort(403, 'Artikel sudah terbit, foto tidak dapat diubah.');
            }

            if ($request->hasFile('gambar_cover')) {
                if ($artikel->gambar_cover) {
                    Storage::disk('public')->delete($artikel->gambar_cover);
                }
                $data['gambar_cover'] = $request->file('gambar_cover')->store('artikel/cover', 'public');
            }

            $data['fotografer'] = $user->name; // Set pengambil foto otomatis

            // Proteksi backend: hapus input teks agar fotografer tidak bisa memanipulasi tulisan kontributor
            unset($data['judul'], $data['isi_artikel'], $data['kategori_id']);
        }

        // --- SOP 3: ALUR EDITOR ---
        // --- SOP 3: ALUR EDITOR ---
        if ($user->hasRole('editor')) {
            if ($request->has('setujui_publish')) {
                $data['status'] = 'publish';
                $data['published_at'] = now();
                $data['catatan_editor'] = null;
            } elseif ($request->has('tolak_revisi')) {
                $data['status'] = 'revisi';
                $data['published_at'] = null;
            } elseif ($request->has('turunkan_tayangan')) {
                // Fitur untuk unpublish berita yang sudah tayang
                $data['status'] = 'draft';
                $data['published_at'] = null;
            }
            // Jika yang diklik 'update_biasa', sistem hanya akan menyimpan teks tanpa merubah status.

            $data['editor'] = $user->name;
        }

        $artikel->update($data);

        return redirect()->route('artikel.index')->with('success', 'Alur redaksi berhasil diproses!');
    }

    /**
     * Menghapus Artikel (Khusus Editor)
     */
    public function destroy(Artikel $artikel)
    {
        $this->authorizeAccess(['editor', 'super_admin']);

        if ($artikel->gambar_cover) {
            Storage::disk('public')->delete($artikel->gambar_cover);
        }

        $artikel->delete();
        return redirect()->route('artikel.index')->with('success', 'Artikel berhasil dihapus dari sistem.');
    }

    /**
     * Helper internal untuk membatasi hak akses role tertentu di method tertentu
     */
    private function authorizeAccess(array $roles)
    {
        $hasAccess = false;
        foreach ($roles as $role) {
            if (auth()->user()->hasRole($role)) {
                $hasAccess = true;
                break;
            }
        }

        if (!$hasAccess) {
            abort(403, 'Anda tidak memiliki otoritas untuk mengakses fitur ini.');
        }
    }
}
