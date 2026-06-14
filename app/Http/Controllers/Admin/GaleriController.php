<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Galeri;
use App\Models\Kegiatan;
use App\Models\User;
use App\Models\WorkspaceFolder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GaleriController extends Controller
{
    public function __construct()
    {
        // Izinkan tamu mengakses halaman publik
        $this->middleware('auth')->except(['publicFolder', 'publicUpload']);
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        // 1. Ambil direktori Kegiatan (Sidebar Kiri)
        $kegiatanQuery = Kegiatan::orderBy('tgl_mulai', 'desc');
        if (!$user->hasRole('super_admin')) {
            $kegiatanQuery->where('organization_id', $user->organization_id);
        }
        $kegiatans = $kegiatanQuery->get();

        $selectedKegiatan = Kegiatan::find($request->kegiatan_id) ?? $kegiatans->first();

        // 2. Ambil Folder di dalam Kegiatan (Hanya yang dia punya akses!)
        $folders = collect();
        if ($selectedKegiatan) {
            if ($user->hasRole('super_admin')) {
                $folders = WorkspaceFolder::where('kegiatan_id', $selectedKegiatan->id)->get();
            } else {
                $folders = WorkspaceFolder::where('kegiatan_id', $selectedKegiatan->id)
                    ->where(function ($q) use ($user) {
                        // Tampilkan jika publik, atau dia pembuatnya, atau dia di-invite
                        $q->where('tipe_akses', 'public')
                            ->orWhere('created_by', $user->id)
                            ->orWhereHas('authorizedUsers', function ($q2) use ($user) {
                                $q2->where('users.id', $user->id);
                            });
                    })->get();
            }
        }

        // 3. Jika masuk ke dalam folder, ambil file-nya
        $selectedFolder = WorkspaceFolder::find($request->folder_id);
        $files = $selectedFolder ? $selectedFolder->galeris()->latest()->get() : collect();

        // Data pengurus untuk fitur "Share ke orang tertentu"
        $users = User::where('id', '!=', $user->id)->orderBy('name')->get();

        return view('admin.galeri.index', compact('kegiatans', 'selectedKegiatan', 'folders', 'selectedFolder', 'files', 'users'));
    }

    // Buat Folder Baru
    public function storeFolder(Request $request)
    {
        $request->validate([
            'kegiatan_id' => 'required|exists:kegiatan,id',
            'nama_folder' => 'required|string|max:100',
            'tipe_akses' => 'required|in:private,public',
        ]);

        $folder = WorkspaceFolder::create([
            'kegiatan_id' => $request->kegiatan_id,
            'nama_folder' => $request->nama_folder,
            'deskripsi' => $request->deskripsi,
            'tipe_akses' => $request->tipe_akses,
            // GUNAKAN BOOLEAN AGAR PASTI TERBACA 'TRUE' OLEH POSTGRESQL
            'izinkan_upload_publik' => $request->boolean('izinkan_upload_publik'),
            'share_token' => $request->tipe_akses == 'public' ? Str::random(40) : null,
            'created_by' => auth()->id(),
        ]);

        // Jika private dan ada user yang diinvite
        if ($request->tipe_akses == 'private' && $request->has('authorized_users')) {
            $folder->authorizedUsers()->attach($request->authorized_users);
        }

        return redirect()->route('galeri.index', ['kegiatan_id' => $request->kegiatan_id, 'folder_id' => $folder->id])
            ->with('success', 'Folder berhasil dibuat!');
    }

    // Edit Pengaturan Folder
    public function updateFolder(Request $request, WorkspaceFolder $folder)
    {
        // Keamanan: Hanya pembuat folder atau Super Admin yang boleh edit
        $user = auth()->user();
        if (!$user->hasRole('super_admin') && $folder->created_by != $user->id) {
            abort(403, 'Akses Ditolak! Hanya pembuat folder yang dapat mengubah pengaturan.');
        }

        $request->validate([
            'nama_folder' => 'required|string|max:100',
            'tipe_akses' => 'required|in:private,public',
        ]);

        // Logika pembuatan/penghapusan Token Share
        $shareToken = $folder->share_token;
        if ($request->tipe_akses == 'public' && !$shareToken) {
            $shareToken = \Illuminate\Support\Str::random(40); // Buat token baru jika pindah ke public
        } elseif ($request->tipe_akses == 'private') {
            $shareToken = null; // Hapus token jika ditutup jadi private kembali
        }

        // Update data foldernya
        $folder->update([
            'nama_folder' => $request->nama_folder,
            'tipe_akses' => $request->tipe_akses,
            'izinkan_upload_publik' => $request->boolean('izinkan_upload_publik'),
            'share_token' => $shareToken,
        ]);

        // Atur ulang Hak Akses User (Private Mode)
        if ($request->tipe_akses == 'private' && $request->has('authorized_users')) {
            $folder->authorizedUsers()->sync($request->authorized_users);
        } else {
            // Jika jadi publik, bersihkan gembok spesifik orangnya
            $folder->authorizedUsers()->detach();
        }

        return back()->with('success', 'Pengaturan folder berhasil diperbarui!');
    }

    // Upload File ke dalam Folder
    public function storeFile(Request $request)
    {
        $request->validate([
            'workspace_folder_id' => 'required|exists:workspace_folders,id',
            'file' => 'required|file|max:5120',
        ]);

        $folder = WorkspaceFolder::findOrFail($request->workspace_folder_id);
        $file = $request->file('file');

        $safeName = time() . '_' . preg_replace('/[^A-Za-z0-9\-]/', '_', pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs('workspace/' . $folder->kegiatan_id . '/' . $folder->id, $safeName, 'public');

        Galeri::create([
            'workspace_folder_id' => $folder->id,
            'file_path' => $filePath,
            'nama_file' => $safeName,
            'keterangan' => $request->keterangan,
            'uploaded_by' => auth()->id(),
        ]);

        return redirect()->route('galeri.index', ['kegiatan_id' => $folder->kegiatan_id, 'folder_id' => $folder->id])
            ->with('success', 'File berhasil diunggah!');
    }

    // Hapus File
    public function destroyFile(Galeri $file)
    {
        $kegiatanId = $file->folder->kegiatan_id;
        $folderId = $file->workspace_folder_id;

        if (Storage::disk('public')->exists($file->file_path)) {
            Storage::disk('public')->delete($file->file_path);
        }
        $file->delete();

        return redirect()->route('galeri.index', ['kegiatan_id' => $kegiatanId, 'folder_id' => $folderId])
            ->with('success', 'File dihapus.');
    }

    public function publicFolder($token)
    {
        // Cari folder berdasarkan token rahasianya
        $folder = WorkspaceFolder::where('share_token', $token)->firstOrFail();

        // Ambil isi filenya
        $files = $folder->galeris()->latest()->get();

        return view('admin.galeri.public-folder', compact('folder', 'files'));
    }

    // 2. Terima Upload dari Publik (Jika diizinkan)
    public function publicUpload(Request $request, $token)
    {
        $folder = WorkspaceFolder::where('share_token', $token)->firstOrFail();

        // Cek Keamanan: Apakah admin mengizinkan orang luar upload?
        if (!$folder->izinkan_upload_publik) {
            abort(403, 'Maaf, folder ini hanya bisa dilihat (Read-Only). Anda tidak diizinkan mengunggah file.');
        }

        $request->validate([
            'file' => 'required|file|max:5120', // Maks 5MB
        ]);

        $file = $request->file('file');
        $safeName = time() . '_' . preg_replace('/[^A-Za-z0-9\-]/', '_', pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();

        $filePath = $file->storeAs('workspace/' . $folder->kegiatan_id . '/' . $folder->id, $safeName, 'public');

        Galeri::create([
            'workspace_folder_id' => $folder->id,
            'file_path' => $filePath,
            'nama_file' => $safeName,
            'keterangan' => $request->keterangan ?? 'Upload dari Publik',
            'uploaded_by' => null, // Dikosongkan karena tidak punya akun
        ]);

        return back()->with('success', 'Terima kasih! File Anda berhasil ditambahkan ke folder.');
    }

    public function togglePublik(Request $request)
    {
        $galeri = \App\Models\Galeri::find($request->id);

        if ($galeri) {
            $galeri->tampil_di_publik = $request->status;
            $galeri->save();

            return response()->json(['success' => true, 'message' => 'Status foto berhasil diperbarui!']);
        }

        return response()->json(['success' => false, 'message' => 'Foto tidak ditemukan!']);
    }
}
