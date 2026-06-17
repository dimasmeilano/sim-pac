<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alumni;
use App\Models\Organization; // <-- Pastikan import model ini
use Illuminate\Http\Request;

class AlumniController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Alumni::latest();

        if ($request->filled('jenis_organisasi')) {
            $query->where('jenis_organisasi', $request->jenis_organisasi);
        }

        $alumnis = $query->paginate(15);
        return view('admin.alumni.index', compact('alumnis'));
    }

    public function create()
    {
        // Ambil semua organisasi jika yang login adalah super_admin
        $organizations = auth()->user()->hasRole('super_admin') ? Organization::all() : [];
        return view('admin.alumni.create', compact('organizations'));
    }

    public function store(Request $request)
    {
        $rules = [
            'nama_lengkap' => 'required|string|max:255',
            'jenis_organisasi' => 'required|in:ipnu,ippnu',
            'no_hp' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'tahun_angkatan' => 'nullable|string|max:4',
            'jabatan_terakhir' => 'nullable|string|max:255',
            'profesi' => 'nullable|string|max:255',
            'instansi_pekerjaan' => 'nullable|string|max:255',
            'alamat_domisili' => 'nullable|string',
            'bersedia_menjadi_donatur' => 'required|boolean',
        ];

        // Jika super_admin wajib memilih organisasi tujuan
        if (auth()->user()->hasRole('super_admin')) {
            $rules['organization_id'] = 'required|exists:organizations,id';
        }

        $request->validate($rules);

        // Tentukan ID Organisasi berdasarkan role
        $orgId = auth()->user()->hasRole('super_admin') ? $request->organization_id : auth()->user()->organization_id;

        Alumni::create(array_merge($request->except(['_token', 'organization_id']), [
            'organization_id' => $orgId,
        ]));

        return redirect()->route('alumni.index')->with('success', 'Data alumni berhasil ditambahkan! Jaringan organisasi semakin kuat.');
    }

    public function edit(Alumni $alumni)
    {
        $organizations = auth()->user()->hasRole('super_admin') ? Organization::all() : [];
        return view('admin.alumni.edit', compact('alumni', 'organizations'));
    }

    public function update(Request $request, Alumni $alumni)
    {
        $rules = [
            'nama_lengkap' => 'required|string|max:255',
            'jenis_organisasi' => 'required|in:ipnu,ippnu',
            'no_hp' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'tahun_angkatan' => 'nullable|string|max:4',
            'jabatan_terakhir' => 'nullable|string|max:255',
            'profesi' => 'nullable|string|max:255',
            'instansi_pekerjaan' => 'nullable|string|max:255',
            'bersedia_menjadi_donatur' => 'required|boolean',
        ];

        if (auth()->user()->hasRole('super_admin')) {
            $rules['organization_id'] = 'required|exists:organizations,id';
        }

        $request->validate($rules);

        $data = $request->except(['_token', '_method']);
        if (auth()->user()->hasRole('super_admin')) {
            $data['organization_id'] = $request->organization_id;
        }

        $alumni->update($data);

        return redirect()->route('alumni.index')->with('success', 'Data profil alumni berhasil diperbarui.');
    }

    public function destroy(Alumni $alumni)
    {
        $alumni->delete();
        return redirect()->route('alumni.index')->with('success', 'Data alumni berhasil dihapus.');
    }
}
