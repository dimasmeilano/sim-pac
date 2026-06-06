<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Kegiatan;
use App\Models\Notulensi;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class NotulensiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        $query = Notulensi::with('kegiatan', 'notulis');

        // Gembok Multi-Tenant
        if (!$user->hasRole('super_admin')) {
            $query->where('organization_id', $user->organization_id);
        }

        $notulensi = $query->orderBy('tanggal', 'desc')->paginate(10);
        return view('admin.notulensi.index', compact('notulensi'));
    }

    public function create()
    {
        $user = auth()->user();

        // Ambil daftar kegiatan untuk di-link-kan
        $kegiatanQuery = Kegiatan::orderBy('tgl_mulai', 'desc');
        if (!$user->hasRole('super_admin')) {
            $kegiatanQuery->where('organization_id', $user->organization_id);
        }
        $kegiatan = $kegiatanQuery->get();

        // TAMBAHAN: Ambil data organisasi khusus Super Admin
        $organizations = $user->hasRole('super_admin') ? \App\Models\Organization::all() : [];

        return view('admin.notulensi.create', compact('kegiatan', 'organizations'));
    }

    public function store(Request $request)
    {
        $rules = [
            'agenda' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'waktu_mulai' => 'nullable|date_format:H:i',
            'waktu_selesai' => 'nullable|date_format:H:i|after:waktu_mulai',
            'tempat' => 'required|string|max:255',
            'pemimpin_rapat' => 'required|string|max:255',
            'pembahasan' => 'required|string',
            'kesimpulan' => 'nullable|string',
            'kegiatan_id' => 'nullable|exists:kegiatan,id'
        ];

        // Jika Super Admin, wajib pilih organisasi
        if (auth()->user()->hasRole('super_admin')) {
            $rules['organization_id'] = 'required|exists:organizations,id';
        }

        $request->validate($rules);

        // Tentukan Pemilik Notulensi
        $orgId = auth()->user()->hasRole('super_admin') ? $request->organization_id : auth()->user()->organization_id;

        Notulensi::create([
            'organization_id' => $orgId,
            'kegiatan_id' => $request->kegiatan_id,
            'agenda' => $request->agenda,
            'tanggal' => $request->tanggal,
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'tempat' => $request->tempat,
            'pemimpin_rapat' => $request->pemimpin_rapat,
            'notulis_id' => auth()->user()->id,
            'pembahasan' => $request->pembahasan,
            'kesimpulan' => $request->kesimpulan,
            'status' => 'draft',
        ]);

        return redirect()->route('notulensi.index')->with('success', 'Notulensi rapat berhasil disimpan!');
    }

    public function show(Notulensi $notulensi)
    {
        if (!auth()->user()->hasRole('super_admin') && $notulensi->organization_id != auth()->user()->organization_id) {
            abort(403, 'Akses Ditolak!');
        }

        // Ambil data absensi jika notulensi ini di-link ke Kegiatan
        $absensi = [];
        if ($notulensi->kegiatan_id) {
            $absensi = Absensi::where('kegiatan_id', $notulensi->kegiatan_id)
                ->where('status', 'hadir')
                ->with('user')
                ->get();
        }

        return view('admin.notulensi.show', compact('notulensi', 'absensi'));
    }

    public function edit(Notulensi $notulensi)
    {
        if (!auth()->user()->hasRole('super_admin') && $notulensi->organization_id != auth()->user()->organization_id) {
            abort(403, 'Akses Ditolak!');
        }

        $user = auth()->user();
        $kegiatanQuery = Kegiatan::orderBy('tgl_mulai', 'desc');
        if (!$user->hasRole('super_admin')) {
            $kegiatanQuery->where('organization_id', $user->organization_id);
        }
        $kegiatan = $kegiatanQuery->get();

        return view('admin.notulensi.edit', compact('notulensi', 'kegiatan'));
    }

    public function update(Request $request, Notulensi $notulensi)
    {
        if (!auth()->user()->hasRole('super_admin') && $notulensi->organization_id != auth()->user()->organization_id) {
            abort(403, 'Akses Ditolak!');
        }

        $request->validate([
            'agenda' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'waktu_mulai' => 'nullable|date_format:H:i',
            'waktu_selesai' => 'nullable|date_format:H:i|after:waktu_mulai',
            'tempat' => 'required|string|max:255',
            'pemimpin_rapat' => 'required|string|max:255',
            'pembahasan' => 'required|string',
            'kesimpulan' => 'nullable|string',
            'kegiatan_id' => 'nullable|exists:kegiatan,id'
        ]);

        $notulensi->update($request->except(['status']));

        return redirect()->route('notulensi.show', $notulensi)->with('success', 'Notulensi berhasil diperbarui!');
    }

    public function destroy(Notulensi $notulensi)
    {
        if (!auth()->user()->hasRole('super_admin') && $notulensi->organization_id != auth()->user()->organization_id) {
            abort(403, 'Akses Ditolak!');
        }

        $notulensi->delete();
        return redirect()->route('notulensi.index')->with('success', 'Notulensi berhasil dihapus!');
    }

    // Fitur Cetak PDF Otomatis
    public function cetakPdf(Notulensi $notulensi)
    {
        if (!auth()->user()->hasRole('super_admin') && $notulensi->organization_id != auth()->user()->organization_id) {
            abort(403, 'Akses Ditolak!');
        }

        $absensi = [];
        if ($notulensi->kegiatan_id) {
            $absensi = Absensi::where('kegiatan_id', $notulensi->kegiatan_id)->where('status', 'hadir')->get();
        }

        $pdf = Pdf::loadView('admin.notulensi.pdf', compact('notulensi', 'absensi'));
        return $pdf->download('Notulensi_Rapat_' . date('d_m_Y', strtotime($notulensi->tanggal)) . '.pdf');
    }

    // Fitur Tombol Ajaib: Mengubah status menjadi Final
    public function finalize(Notulensi $notulensi)
    {
        // Proteksi Keamanan
        if (!auth()->user()->hasRole('super_admin') && $notulensi->organization_id != auth()->user()->organization_id) {
            abort(403, 'Akses Ditolak!');
        }

        $notulensi->update(['status' => 'final']);

        return redirect()->route('notulensi.index')->with('success', 'Notulensi dikunci menjadi Final dan siap dicetak!');
    }
}
