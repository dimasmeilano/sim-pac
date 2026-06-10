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

        $query = DokumenArsip::where('kategori', $kategori);

        if (!auth()->user()->hasRole('super_admin')) {
            $query->where(function ($q) {
                $q->where('organization_id', auth()->user()->organization_id)
                    ->orWhere('hak_akses', 'publik');
            });
        }

        $dokumen = $query->latest()->get();

        // TAMBAHAN: Ambil data organisasi khusus untuk Super Admin
        $organizations = auth()->user()->hasRole('super_admin') ? \App\Models\Organization::all() : [];

        // Jangan lupa tambahkan 'organizations' di compact
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
        $ukuranFile = round($file->getSize() / 1024); // Convert ke KB

        // Simpan file ke folder private secara aman (storage/app/arsip_dokumen)
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

    public function download(DokumenArsip $dokumen)
    {
        // Proteksi Gembok Akses
        if (!auth()->user()->hasRole('super_admin')) {
            // 1. Jika dokumen rahasia, hanya ketua/sekretaris organisasi itu yang boleh download
            if ($dokumen->hak_akses == 'rahasia') {
                if (
                    $dokumen->organization_id != auth()->user()->organization_id ||
                    !auth()->user()->hasAnyRole(['ketua_pac', 'sekretaris_pac', 'ketua_ranting', 'sekretaris_ranting'])
                ) {
                    abort(403, 'Akses Ditolak! Dokumen ini bersifat RAHASIA.');
                }
            }
            // 2. Jika internal, hanya anggota organisasi tersebut yang boleh download
            elseif ($dokumen->hak_akses == 'internal' && $dokumen->organization_id != auth()->user()->organization_id) {
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
        if (!auth()->user()->hasRole('super_admin') && $dokumen->organization_id != auth()->user()->organization_id) {
            abort(403, 'Anda tidak berhak menghapus dokumen ini!');
        }

        // Hapus file fisik dari storage
        if (Storage::exists($dokumen->file_path)) {
            Storage::delete($dokumen->file_path);
        }

        $dokumen->delete();

        return back()->with('success', 'Dokumen beserta file fisiknya berhasil dihapus permanen.');
    }
}
