<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventaris;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InventarisController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        $query = Inventaris::with('organization');

        // Gembok Multi-Tenant
        if (!$user->hasRole('super_admin')) {
            $query->where('organization_id', $user->organization_id);
        }

        $inventaris = $query->latest()->paginate(10);
        return view('admin.inventaris.index', compact('inventaris'));
    }

    public function create()
    {
        $user = auth()->user();

        // Ambil daftar organisasi KHUSUS untuk super admin
        $organizations = $user->hasRole('super_admin') ? Organization::all() : [];
        // 1. Buat Prefix Kode (Contoh: INV/PAC/2026/06/...);
        $orgType = strtoupper($user->organization->type ?? 'PAC');
        $tahunBulan = date('Y/m');
        $prefix = "INV/{$orgType}/{$tahunBulan}/";

        // 2. Cari kode terakhir di bulan ini untuk organisasi tersebut
        $lastBarang = Inventaris::where('organization_id', $user->organization_id);
        if (!$user->hasRole('super_admin')) {
            $lastBarang->where('organization_id', $user->organization_id);
        }
        $lastBarang = $lastBarang->orderBy('id', 'desc')->first();

        // 3. Buat nomor urut baru (4 digit)
        if ($lastBarang) {
            $lastUrutan = (int) substr($lastBarang->kode_barang, -4);
            $nextUrutan = str_pad($lastUrutan + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextUrutan = '0001';
        }

        $kodeOtomatis = $prefix . $nextUrutan;

        return view('admin.inventaris.create', compact('kodeOtomatis', 'organizations'));
    }

    public function store(Request $request)
    {
        // 1. Siapkan Aturan Validasi
        $rules = [
            'kode_barang' => 'required|string|unique:inventaris,kode_barang',
            'nama_barang' => 'required|string|max:200',
            'jumlah' => 'required|integer|min:1',
            'kondisi' => 'required|in:baik,rusak_ringan,rusak_berat',
            'sumber_dana' => 'nullable|string',
            'tahun_perolehan' => 'nullable|digits:4',
            'foto_barang' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];

        // Jika Super Admin, wajib pilih organisasi
        if (auth()->user()->hasRole('super_admin')) {
            $rules['organization_id'] = 'required|exists:organizations,id';
        }

        $request->validate($rules);

        // 2. Proses Foto
        $fotoPath = null;
        if ($request->hasFile('foto_barang')) {
            $fotoPath = $request->file('foto_barang')->store('inventaris_foto', 'public');
        }

        // 3. Tentukan Pemilik Barang
        $orgId = auth()->user()->hasRole('super_admin') ? $request->organization_id : auth()->user()->organization_id;

        // 4. Simpan Data
        Inventaris::create([
            'organization_id' => $orgId,
            'kode_barang' => $request->kode_barang,
            'nama_barang' => $request->nama_barang,
            'jumlah' => $request->jumlah,
            'kondisi' => $request->kondisi,
            'sumber_dana' => $request->sumber_dana,
            'tahun_perolehan' => $request->tahun_perolehan,
            'keterangan' => $request->keterangan,
            'foto_barang' => $fotoPath,
        ]);

        return redirect()->route('inventaris.index')->with('success', 'Data aset/inventaris berhasil ditambahkan!');
    }

    public function edit(Inventaris $inventaris)
    {
        // Proteksi Edit
        if (!auth()->user()->hasRole('super_admin') && $inventaris->organization_id != auth()->user()->organization_id) {
            abort(403, 'Anda tidak memiliki akses ke aset ini.');
        }

        return view('admin.inventaris.edit', compact('inventaris'));
    }

    public function update(Request $request, Inventaris $inventaris)
    {
        // Proteksi Update
        if (!auth()->user()->hasRole('super_admin') && $inventaris->organization_id != auth()->user()->organization_id) {
            abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'kode_barang' => 'required|string|unique:inventaris,kode_barang,' . $inventaris->id,
            'nama_barang' => 'required|string|max:200',
            'jumlah' => 'required|integer|min:1',
            'kondisi' => 'required|in:baik,rusak_ringan,rusak_berat',
            'foto_barang' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $fotoPath = $inventaris->foto_barang;
        if ($request->hasFile('foto_barang')) {
            if ($fotoPath && Storage::disk('public')->exists($fotoPath)) {
                Storage::disk('public')->delete($fotoPath);
            }
            $fotoPath = $request->file('foto_barang')->store('inventaris_foto', 'public');
        }

        $inventaris->update([
            'kode_barang' => $request->kode_barang,
            'nama_barang' => $request->nama_barang,
            'jumlah' => $request->jumlah,
            'kondisi' => $request->kondisi,
            'sumber_dana' => $request->sumber_dana,
            'tahun_perolehan' => $request->tahun_perolehan,
            'keterangan' => $request->keterangan,
            'foto_barang' => $fotoPath,
        ]);

        return redirect()->route('inventaris.index')->with('success', 'Data aset berhasil diperbarui!');
    }

    public function destroy(Inventaris $inventaris)
    {
        // Proteksi Hapus
        if (!auth()->user()->hasRole('super_admin') && $inventaris->organization_id != auth()->user()->organization_id) {
            abort(403, 'Akses ditolak.');
        }

        if ($inventaris->foto_barang && Storage::disk('public')->exists($inventaris->foto_barang)) {
            Storage::disk('public')->delete($inventaris->foto_barang);
        }

        $inventaris->delete();
        return redirect()->route('inventaris.index')->with('success', 'Data aset berhasil dihapus!');
    }

    public function cetakLabel($id = null)
    {
        $user = auth()->user();
        $query = Inventaris::with('organization');

        // Gembok Multi-Tenant: Hanya boleh cetak aset miliknya sendiri
        if (!$user->hasRole('super_admin')) {
            $query->where('organization_id', $user->organization_id);
        }

        // Jika ada ID yang dikirim, berarti hanya cetak 1 label
        if ($id) {
            $query->where('id', $id);
        }

        $inventaris = $query->orderBy('kode_barang', 'asc')->get();

        if ($inventaris->isEmpty()) {
            return back()->with('error', 'Gagal! Tidak ada data aset untuk dicetak.');
        }

        return view('admin.inventaris.label', compact('inventaris'));
    }
}
