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
        // KODE LAMA: Ada if-else cek super admin.
        // KODE BARU: Langsung panggil saja! Trait sudah menguncinya otomatis.
        $inventaris = Inventaris::with('organization')->latest()->paginate(10);
        return view('admin.inventaris.index', compact('inventaris'));
    }

    public function create()
    {
        $user = auth()->user();
        $organizations = $user->hasRole('super_admin') ? Organization::all() : [];

        $orgType = strtoupper($user->organization->type ?? 'PAC');
        $tahunBulan = date('Y/m');
        $prefix = "INV/{$orgType}/{$tahunBulan}/";

        // KODE LAMA: Ada where('organization_id') manual.
        // KODE BARU: Langsung cari ID terakhir, Trait memfilter otomatis.
        $lastBarang = Inventaris::orderBy('id', 'desc')->first();

        $nextUrutan = $lastBarang ? str_pad(((int) substr($lastBarang->kode_barang, -4)) + 1, 4, '0', STR_PAD_LEFT) : '0001';
        $kodeOtomatis = $prefix . $nextUrutan;

        return view('admin.inventaris.create', compact('kodeOtomatis', 'organizations'));
    }

    public function store(Request $request)
    {
        $rules = [
            'kode_barang' => 'required|string|unique:inventaris,kode_barang',
            'nama_barang' => 'required|string|max:200',
            'jumlah' => 'required|integer|min:1',
            'kondisi' => 'required|in:baik,rusak_ringan,rusak_berat',
            'sumber_dana' => 'nullable|string',
            'tahun_perolehan' => 'nullable|digits:4',
            'foto_barang' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];

        if (auth()->user()->hasRole('super_admin')) {
            $rules['organization_id'] = 'required|exists:organizations,id';
        }

        $request->validate($rules);

        $fotoPath = $request->hasFile('foto_barang') ? $request->file('foto_barang')->store('inventaris_foto', 'public') : null;
        $orgId = auth()->user()->hasRole('super_admin') ? $request->organization_id : auth()->user()->organization_id;

        Inventaris::create(array_merge($request->except(['foto_barang', '_token']), [
            'organization_id' => $orgId,
            'foto_barang' => $fotoPath,
        ]));

        return redirect()->route('inventaris.index')->with('success', 'Data aset berhasil ditambahkan!');
    }

    public function edit(Inventaris $inventaris)
    {
        // KODE LAMA: if (!auth()->user()->hasRole('super_admin') && $inventaris->organization_id != ...) abort(403);
        // KODE BARU: Hapus total! Laravel otomatis melempar 404 jika Ranting A mencoba akses URL edit milik Ranting B.
        return view('admin.inventaris.edit', compact('inventaris'));
    }

    public function update(Request $request, Inventaris $inventaris)
    {
        // KODE LAMA: abort(403) manual dihapus total!

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

        $inventaris->update(array_merge($request->except(['foto_barang', '_token', '_method']), [
            'foto_barang' => $fotoPath,
        ]));

        return redirect()->route('inventaris.index')->with('success', 'Data aset berhasil diperbarui!');
    }

    public function destroy(Inventaris $inventaris)
    {
        // KODE LAMA: abort(403) manual dihapus total!

        if ($inventaris->foto_barang && Storage::disk('public')->exists($inventaris->foto_barang)) {
            Storage::disk('public')->delete($inventaris->foto_barang);
        }

        $inventaris->delete();
        return redirect()->route('inventaris.index')->with('success', 'Data aset berhasil dihapus!');
    }

    public function cetakLabel($id = null)
    {
        // Otomatis terfilter berkat Trait BelongsToOrganization
        $query = Inventaris::with('organization');
        if ($id) $query->where('id', $id);

        $inventaris = $query->orderBy('kode_barang', 'asc')->get();

        if ($inventaris->isEmpty()) return back()->with('error', 'Gagal! Tidak ada data aset untuk dicetak.');
        return view('admin.inventaris.label', compact('inventaris'));
    }
}
