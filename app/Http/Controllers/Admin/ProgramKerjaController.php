<?php

namespace App\Http\Controllers\Admin;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\ProgramKerja;
use App\Models\SubTugas;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgramKerjaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // 1. Update status otomatis
        ProgramKerja::where('status', 'active')
            ->where('tgl_selesai', '<', now()->toDateString())
            ->update(['status' => 'completed']);

        // 2. Query dasar (Global Scope sudah mengunci ke Ranting masing-masing secara gaib!)
        $user = auth()->user();
        $query = ProgramKerja::with('organization', 'tugas');

        // 3. Filter "Jenis Kelamin" Organisasi (IPNU/IPPNU)
        if (!$user->hasRole('super_admin') && $user->organization) {
            $jenisOrgUser = $user->organization->jenis_organisasi;
            $query->whereIn('jenis', [$jenisOrgUser, 'bersama']);
        }

        $programKerja = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.progja.index', compact('programKerja'));
    }

    public function create()
    {
        $organizations = auth()->user()->hasRole('super_admin') ? \App\Models\Organization::all() : [];
        return view('admin.progja.create', compact('organizations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:200',
            'deskripsi' => 'nullable|string',
            'jenis' => 'required|in:ipnu,ippnu,bersama',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
            'status' => 'required|in:planning,active,completed,cancelled',
            'estimasi_anggaran' => 'nullable|numeric',
        ]);

        $progja = ProgramKerja::create([
            'organization_id' => $request->organization_id ?? auth()->user()->organization_id,
            'nama' => $request->nama,
            'jenis' => $request->jenis,
            'deskripsi' => $request->deskripsi,
            'tgl_mulai' => $request->tgl_mulai,
            'tgl_selesai' => $request->tgl_selesai,
            'status' => $request->status,
            'estimasi_anggaran' => $request->estimasi_anggaran ?? 0,
        ]);

        return redirect()->route('progja.show', $progja)->with('success', 'Program Kerja berhasil dibuat');
    }

    public function edit(ProgramKerja $progja)
    {
        // ABORT(403) MANUAL DIHAPUS
        $organizations = auth()->user()->hasRole('super_admin') ? \App\Models\Organization::all() : [];
        return view('admin.progja.edit', compact('progja', 'organizations'));
    }

    public function update(Request $request, ProgramKerja $progja)
    {
        // ABORT(403) MANUAL DIHAPUS
        $request->validate([
            'nama' => 'required|string|max:200',
            'deskripsi' => 'nullable|string',
            'jenis' => 'required|in:ipnu,ippnu,bersama',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
            'status' => 'required|in:planning,active,completed,cancelled',
            'estimasi_anggaran' => 'nullable|numeric',
        ]);

        if (auth()->user()->hasRole('super_admin') && $request->has('organization_id')) {
            $progja->organization_id = $request->organization_id;
        }

        $progja->update([
            'nama' => $request->nama,
            'jenis' => $request->jenis,
            'deskripsi' => $request->deskripsi,
            'tgl_mulai' => $request->tgl_mulai,
            'tgl_selesai' => $request->tgl_selesai,
            'status' => $request->status,
            'estimasi_anggaran' => $request->estimasi_anggaran ?? 0,
        ]);

        return redirect()->route('progja.show', $progja)->with('success', 'Program Kerja berhasil diupdate');
    }

    public function show(ProgramKerja $progja)
    {
        // ABORT(403) MANUAL DIHAPUS
        $progja->load('tugas.assignee', 'messages.user', 'organization');

        $todos = $progja->tugas->where('status', 'todo');
        $progress = $progja->tugas->where('status', 'progress');
        $done = $progja->tugas->where('status', 'done');
        $revisi = $progja->tugas->where('status', 'revisi');

        $users = User::where('organization_id', $progja->organization_id)->orderBy('name')->get();

        return view('admin.progja.show', compact('progja', 'todos', 'progress', 'done', 'revisi', 'users'));
    }

    public function destroy(ProgramKerja $progja)
    {
        // ABORT(403) MANUAL DIHAPUS
        $progja->delete();
        return redirect()->route('progja.index')->with('success', 'Program Kerja berhasil dihapus');
    }

    // =========================================================
    // KODE DI BAWAH INI SAMA (Tugas, Chat, Cetak LPJ)
    // Ingat, HAPUS SAJA baris pengecekan:
    // if (!$user->hasRole('super_admin') && $programKerja->organization_id != ...) { abort(403); } 
    // dari dalam fungsi
}
