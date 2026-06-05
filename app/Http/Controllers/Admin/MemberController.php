<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class MemberController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        // 1. Mulai Query dasar
        $query = User::with('organization');

        // 2. Logika Multi-Tenant (Pembatasan Hak Akses)
        if (!$user->hasRole('super_admin') && !$user->hasRole('sekretaris_pac')) {
            $query->where('organization_id', $user->organization_id);
        }

        // 3. Fitur Filter Organisasi (Hanya berlaku untuk PAC/Super Admin)
        if ($request->filled('organization_id') && ($user->hasRole('super_admin') || $user->hasRole('sekretaris_pac'))) {
            $query->where('organization_id', $request->organization_id);
        }

        // 4. Fitur Search (Nama, NIK, atau Email)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('tempat_lahir', 'like', "%{$search}%");
            });
        }

        // 5. Eksekusi Query dengan Pagination (tambahkan withQueryString agar search tidak hilang saat pindah halaman)
        $members = $query->latest('id')->paginate(10)->withQueryString();

        // 6. Ambil data organisasi untuk di-looping di dropdown filter PAC
        $organizations = \App\Models\Organization::orderBy('name', 'asc')->get();

        return view('admin.members.index', compact('members', 'organizations'));
    }

    public function create()
    {
        $organizations = Organization::orderBy('type')->orderBy('name')->get();
        return view('admin.members.create', compact('organizations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'nik' => 'required|string|size:16|unique:users',
            'no_hp' => 'required|string|max:15',
            'organization_id' => 'nullable|exists:organizations,id',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'jk' => 'nullable|in:L,P',
            'pendidikan' => 'nullable|string|max:50',
            'status_anggota' => 'required|in:aktif,nonaktif,meninggal,keluar',
            'tgl_bergabung' => 'nullable|date',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // Upload foto
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            $filename = time() . '_' . $foto->getClientOriginalName();
            $fotoPath = $foto->storeAs('anggota/foto', $filename, 'public');
        }

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'nik' => $request->nik,
            'no_hp' => $request->no_hp,
            'organization_id' => $request->organization_id,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jk' => $request->jk,
            'pendidikan' => $request->pendidikan,
            'status_anggota' => $request->status_anggota,
            'tgl_bergabung' => $request->tgl_bergabung ?? date('Y-m-d'),
            'foto' => $fotoPath,
        ]);

        // Assign role default
        $user->assignRole('anggota_biasa');

        return redirect()->route('members.index')->with('success', 'Anggota berhasil ditambahkan');
    }

    public function edit(User $member)
    {
        $organizations = Organization::orderBy('type')->orderBy('name')->get();
        return view('admin.members.edit', compact('member', 'organizations'));
    }

    public function update(Request $request, User $member)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $member->id,
            'nik' => 'required|string|size:16|unique:users,nik,' . $member->id,
            'no_hp' => 'required|string|max:15',
            'organization_id' => 'nullable|exists:organizations,id',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'jk' => 'nullable|in:L,P',
            'pendidikan' => 'nullable|string|max:50',
            'status_anggota' => 'required|in:aktif,nonaktif,meninggal,keluar',
            'tgl_bergabung' => 'nullable|date',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // Upload foto baru
        if ($request->hasFile('foto')) {
            if ($member->foto && Storage::disk('public')->exists($member->foto)) {
                Storage::disk('public')->delete($member->foto);
            }

            $foto = $request->file('foto');
            $filename = time() . '_' . $foto->getClientOriginalName();
            $fotoPath = $foto->storeAs('anggota/foto', $filename, 'public');
            $member->foto = $fotoPath;
        }

        // Update password jika diisi
        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8|confirmed']);
            $member->password = Hash::make($request->password);
        }

        $member->update([
            'name' => $request->name,
            'email' => $request->email,
            'nik' => $request->nik,
            'no_hp' => $request->no_hp,
            'organization_id' => $request->organization_id,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jk' => $request->jk,
            'pendidikan' => $request->pendidikan,
            'status_anggota' => $request->status_anggota,
            'tgl_bergabung' => $request->tgl_bergabung,
        ]);

        return redirect()->route('members.index')->with('success', 'Anggota berhasil diupdate');
    }

    public function destroy(User $member)
    {
        if ($member->foto && Storage::disk('public')->exists($member->foto)) {
            Storage::disk('public')->delete($member->foto);
        }

        $member->delete();

        return redirect()->route('members.index')->with('success', 'Anggota berhasil dihapus');
    }

    public function show(User $member)
    {
        $member->load('organization');
        return view('admin.members.show', compact('member'));
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        try {
            Excel::import(new \App\Imports\MembersImport, $request->file('file_excel'));
            return back()->with('success', 'Alhamdulillah, data anggota dari Excel berhasil diimpor!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengimpor data. Pastikan format Excel sesuai dengan template. Error: ' . $e->getMessage());
        }
    }

    // Fungsi cerdas untuk membuat template on-the-fly (tanpa perlu simpan file fisik)
    public function downloadTemplate()
    {
        // Kita gunakan fitur array export sederhana untuk template
        $export = new class implements \Maatwebsite\Excel\Concerns\FromArray {
            public function array(): array
            {
                return [
                    ['nama_lengkap', 'email', 'nik', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin_l_p', 'no_hp', 'pendidikan'],
                    ['Hafid Sang Juara', 'hafid@gmail.com', '3525012345678901', 'Gresik', '2001-12-30', 'L', '081234567890', 'S1'],
                    ['Siti Aminah', 'aminah@gmail.com', '3525012345678902', 'Surabaya', '2002-05-15', 'P', '081298765432', 'SMA']
                ];
            }
        };

        return Excel::download($export, 'Template_Import_Anggota.xlsx');
    }

    public function exportExcel()
    {
        $namaFile = 'Data_Anggota_' . auth()->user()->organization->name . '_' . date('Y-m-d') . '.xlsx';
        return Excel::download(new \App\Exports\MembersExport, str_replace(' ', '_', $namaFile));
    }

    public function exportPdf()
    {
        $user = auth()->user();

        // 1. Ambil data anggota sesuai role (Multi-Tenant)
        if ($user->hasRole('super_admin') || $user->hasRole('sekretaris_pac')) {
            $members = User::whereNotNull('organization_id')->with('organization')->get();
        } else {
            $members = User::where('organization_id', $user->organization_id)->with('organization')->get();
        }

        $org = $user->organization;

        // 2. Ekstrak Nama Wilayah (Buang kata PR, PAC, IPNU, Ranting, dll)
        $kata_hapus = ['PR IPNU Ranting', 'PR IPPNU Ranting', 'PR IPNU', 'PR IPPNU', 'PAC IPNU', 'PAC IPPNU', 'Pimpinan Ranting', 'Ranting', 'PAC', 'Komisariat'];
        $lokasi = trim(str_replace($kata_hapus, '', $org->name ?? 'Gresik'));

        // 3. Tarik data Ketua dan Sekretaris yang aktif di organisasi tersebut
        // Pastikan nama role sesuai dengan yang ada di tabel Spatie Anda
        $ketua = clone $members; // Kita clone agar query utama tidak rusak
        // 3. Tarik data Ketua dan Sekretaris menggunakan whereHas (Lebih akurat membaca tabel relasi Spatie)
        $ketua = User::where('organization_id', $org->id)
            ->whereHas('roles', function ($query) {
                // Masukkan kemungkinan nama role ketua di sini
                $query->whereIn('name', ['ketua_ranting', 'ketua_pac', 'ketua']);
            })->first();

        $sekretaris = User::where('organization_id', $org->id)
            ->whereHas('roles', function ($query) {
                // Masukkan kemungkinan nama role sekretaris di sini
                $query->whereIn('name', ['sekretaris_ranting', 'sekretaris_pac', 'sekretaris']);
            })->first();

        // 4. Load View DOMPDF
        $pdf = Pdf::loadView('admin.members.pdf', compact('members', 'org', 'lokasi', 'ketua', 'sekretaris'))
            ->setPaper('a4', 'landscape');

        $namaFile = 'Data_Anggota_' . ($lokasi) . '_' . date('d-m-Y') . '.pdf';

        return $pdf->download(str_replace(' ', '_', $namaFile));
    }
}
