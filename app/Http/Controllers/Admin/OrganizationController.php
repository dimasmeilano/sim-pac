<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OrganizationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $organizations = Organization::with('parent')
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        return view('admin.organizations.index', compact('organizations'));
    }

    public function create()
    {
        $parents = Organization::where('type', 'pac')->get();
        return view('admin.organizations.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:pac,ranting,departemen,lembaga',
            'parent_id' => 'nullable|exists:organizations,id',
            'periode' => 'nullable|string|max:10',
            'alamat' => 'nullable|string',
            'kontak' => 'nullable|string|max:20',
            'jenis_organisasi' => 'required|in:ipnu,ippnu,bersama',
            'kop_surat_ipnu' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'kop_surat_ippnu' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'kop_surat_bersama' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->except(['kop_surat_ipnu', 'kop_surat_ippnu', 'kop_surat_bersama']);

        // Upload kop IPNU
        if ($request->hasFile('kop_surat_ipnu')) {
            $data['kop_surat_ipnu'] = $request->file('kop_surat_ipnu')->store('organisasi/kop', 'public');
        }

        // Upload kop IPPNU
        if ($request->hasFile('kop_surat_ippnu')) {
            $data['kop_surat_ippnu'] = $request->file('kop_surat_ippnu')->store('organisasi/kop', 'public');
        }

        // Upload kop Bersama
        if ($request->hasFile('kop_surat_bersama')) {
            $data['kop_surat_bersama'] = $request->file('kop_surat_bersama')->store('organisasi/kop', 'public');
        }

        Organization::create($data);

        return redirect()->route('organizations.index')->with('success', 'Organisasi berhasil ditambahkan');
    }

    public function edit(Organization $organization)
    {
        $parents = Organization::where('type', 'pac')
            ->where('id', '!=', $organization->id)
            ->get();

        return view('admin.organizations.edit', compact('organization', 'parents'));
    }

    public function update(Request $request, Organization $organization)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:pac,ranting,departemen,lembaga',
            'jenis_organisasi' => 'required|in:ipnu,ippnu,bersama',
            'parent_id' => 'nullable|exists:organizations,id',
            'periode' => 'nullable|string|max:10',
            'alamat' => 'nullable|string',
            'kontak' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'website' => 'nullable|string|max:100',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'kop_surat_ipnu' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'kop_surat_ippnu' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'kop_surat_bersama' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Cegah parent_id指向自己
        if ($request->parent_id == $organization->id) {
            return back()->with('error', 'Organisasi tidak bisa menjadi parent dari dirinya sendiri');
        }

        $data = $request->except(['logo', 'kop_surat_ipnu', 'kop_surat_ippnu', 'kop_surat_bersama']);

        // Handle upload Logo
        if ($request->hasFile('logo')) {
            // Hapus logo lama
            if ($organization->logo && Storage::disk('public')->exists($organization->logo)) {
                Storage::disk('public')->delete($organization->logo);
            }
            $data['logo'] = $request->file('logo')->store('organisasi/logo', 'public');
        }

        // Handle upload Kop Surat IPNU
        if ($request->hasFile('kop_surat_ipnu')) {
            if ($organization->kop_surat_ipnu && Storage::disk('public')->exists($organization->kop_surat_ipnu)) {
                Storage::disk('public')->delete($organization->kop_surat_ipnu);
            }
            $data['kop_surat_ipnu'] = $request->file('kop_surat_ipnu')->store('organisasi/kop', 'public');
        }

        // Handle upload Kop Surat IPPNU
        if ($request->hasFile('kop_surat_ippnu')) {
            if ($organization->kop_surat_ippnu && Storage::disk('public')->exists($organization->kop_surat_ippnu)) {
                Storage::disk('public')->delete($organization->kop_surat_ippnu);
            }
            $data['kop_surat_ippnu'] = $request->file('kop_surat_ippnu')->store('organisasi/kop', 'public');
        }

        // Handle upload Kop Surat Bersama
        if ($request->hasFile('kop_surat_bersama')) {
            if ($organization->kop_surat_bersama && Storage::disk('public')->exists($organization->kop_surat_bersama)) {
                Storage::disk('public')->delete($organization->kop_surat_bersama);
            }
            $data['kop_surat_bersama'] = $request->file('kop_surat_bersama')->store('organisasi/kop', 'public');
        }

        $organization->update($data);

        return redirect()->route('organizations.index')
            ->with('success', 'Organisasi berhasil diupdate');
    }

    public function destroy(Organization $organization)
    {
        // Cek apakah punya anak
        if ($organization->children()->count() > 0) {
            return back()->with('error', 'Hapus sub-organisasi terlebih dahulu');
        }

        // Hapus file logo jika ada
        if ($organization->logo && Storage::disk('public')->exists($organization->logo)) {
            Storage::disk('public')->delete($organization->logo);
        }

        // Hapus file kop surat jika ada
        if ($organization->kop_surat_ipnu && Storage::disk('public')->exists($organization->kop_surat_ipnu)) {
            Storage::disk('public')->delete($organization->kop_surat_ipnu);
        }
        if ($organization->kop_surat_ippnu && Storage::disk('public')->exists($organization->kop_surat_ippnu)) {
            Storage::disk('public')->delete($organization->kop_surat_ippnu);
        }
        if ($organization->kop_surat_bersama && Storage::disk('public')->exists($organization->kop_surat_bersama)) {
            Storage::disk('public')->delete($organization->kop_surat_bersama);
        }

        $organization->delete();

        return redirect()->route('organizations.index')
            ->with('success', 'Organisasi berhasil dihapus');
    }

    public function show(Organization $organization)
    {
        $organization->load('parent', 'children', 'users');
        return view('admin.organizations.show', compact('organization'));
    }
}
