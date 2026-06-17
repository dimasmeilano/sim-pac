<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DokumenArsip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DokumenArsipController extends Controller
{

    public function index(Request $request)
    {
        $kategori = $request->get('kategori', 'repository');

        // KODE BARU: Membuka kunci Global Scope SESAAT agar bisa menarik dokumen berstatus "Publik" dari PAC/Ranting lain
        $query = DokumenArsip::withoutGlobalScope('organization')->where('kategori', $kategori);

        if (!auth()->user()->hasRole('super_admin')) {
            $query->where(function ($q) {
                $q->where('organization_id', auth()->user()->organization_id)
                    ->orWhere('hak_akses', 'publik'); // Trik sakti membuka akses publik
            });
        }

        $dokumen = $query->latest()->get();
        $organizations = auth()->user()->hasRole('super_admin') ? \App\Models\Organization::all() : [];

        return view('admin.dokumen.index', compact('dokumen', 'kategori', 'organizations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kategori' => 'required|in:e-library,repository',
            'nama_dokumen' => 'required|string|max:255',
            'hak_akses' => 'required|in:publik,internal,rahasia',
            'file_dokumen' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar|max:10240', // Max 10MB
        ]);

        $file = $request->file('file_dokumen');
        $namaFileOri = $file->getClientOriginalName();
        $tipeFile = $file->getClientOriginalExtension();
        $ukuranFile = round($file->getSize() / 1024);

        $path = $file->storeAs('arsip_dokumen', time() . '_' . str_replace(' ', '_', $namaFileOri));

        DokumenArsip::create([
            'organization_id' => $request->organization_id ?? auth()->user()->organization_id,
            'user_id' => auth()->user()->id,
            'kategori' => $request->kategori,
            'nama_dokumen' => $request->nama_dokumen,
            'deskripsi' => $request->deskripsi,
            'file_path' => $path,
            'tipe_file' => strtolower($tipeFile),
            'ukuran_file' => $ukuranFile,
            'hak_akses' => $request->hak_akses,
        ]);

        return back()->with('success', 'Dokumen berhasil diunggah ke sistem!');
    }

    public function download($id)
    {
        // Pakai FindOrFail tanpa Global Scope agar user tetap bisa klik tombol download pada dokumen publik org lain
        $dokumen = DokumenArsip::withoutGlobalScope('organization')->findOrFail($id);

        if (!auth()->user()->hasRole('super_admin')) {
            if ($dokumen->hak_akses == 'rahasia') {
                if (
                    $dokumen->organization_id != auth()->user()->organization_id ||
                    !auth()->user()->hasAnyRole(['ketua_pac', 'sekretaris_pac', 'ketua_ranting', 'sekretaris_ranting'])
                ) {
                    abort(403, 'Akses Ditolak! Dokumen ini bersifat RAHASIA.');
                }
            } elseif ($dokumen->hak_akses == 'internal' && $dokumen->organization_id != auth()->user()->organization_id) {
                abort(403, 'Akses Ditolak! Dokumen ini hanya untuk internal organisasi pemilik.');
            }
        }

        if (!Storage::exists($dokumen->file_path)) {
            abort(404, 'File fisik tidak ditemukan di server.');
        }

        return Storage::download($dokumen->file_path, $dokumen->nama_dokumen . '.' . $dokumen->tipe_file);
    }

    public function destroy(DokumenArsip $dokumen)
    {
        // ABORT(403) MANUAL DIHAPUS - Laravel Route Binding + Global Scope sudah melindungi ini dari jangkauan Ranting lain!

        if (Storage::exists($dokumen->file_path)) {
            Storage::delete($dokumen->file_path);
        }

        $dokumen->delete();

        return back()->with('success', 'Dokumen beserta file fisiknya berhasil dihapus permanen.');
    }
}
