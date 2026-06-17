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
        // KODE BARU: Filter otomatis dari Trait!
        $notulensi = Notulensi::with('kegiatan', 'notulis')->orderBy('tanggal', 'desc')->paginate(10);
        return view('admin.notulensi.index', compact('notulensi'));
    }

    public function create()
    {
        // KODE BARU: Kegiatan otomatis terfilter berkat Trait di model Kegiatan
        $kegiatan = Kegiatan::orderBy('tgl_mulai', 'desc')->get();
        $organizations = auth()->user()->hasRole('super_admin') ? \App\Models\Organization::all() : [];

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

        if (auth()->user()->hasRole('super_admin')) {
            $rules['organization_id'] = 'required|exists:organizations,id';
        }

        $request->validate($rules);

        $orgId = auth()->user()->hasRole('super_admin') ? $request->organization_id : auth()->user()->organization_id;

        Notulensi::create(array_merge($request->except(['_token', 'organization_id']), [
            'organization_id' => $orgId,
            'notulis_id' => auth()->id(),
            'status' => 'draft',
        ]));

        return redirect()->route('notulensi.index')->with('success', 'Notulensi rapat berhasil disimpan!');
    }

    public function show(Notulensi $notulensi)
    {
        // KODE LAMA: abort(403) dibuang!
        $absensi = $notulensi->kegiatan_id
            ? Absensi::where('kegiatan_id', $notulensi->kegiatan_id)->where('status', 'hadir')->with('user')->get()
            : [];

        return view('admin.notulensi.show', compact('notulensi', 'absensi'));
    }

    public function edit(Notulensi $notulensi)
    {
        // KODE LAMA: abort(403) dibuang!
        $kegiatan = Kegiatan::orderBy('tgl_mulai', 'desc')->get();
        return view('admin.notulensi.edit', compact('notulensi', 'kegiatan'));
    }

    public function update(Request $request, Notulensi $notulensi)
    {
        // KODE LAMA: abort(403) dibuang!
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

        $notulensi->update($request->except(['status', '_token', '_method']));

        return redirect()->route('notulensi.show', $notulensi)->with('success', 'Notulensi berhasil diperbarui!');
    }

    public function destroy(Notulensi $notulensi)
    {
        // KODE LAMA: abort(403) dibuang!
        $notulensi->delete();
        return redirect()->route('notulensi.index')->with('success', 'Notulensi berhasil dihapus!');
    }

    public function cetakPdf(Notulensi $notulensi)
    {
        // KODE LAMA: abort(403) dibuang!
        $absensi = $notulensi->kegiatan_id
            ? Absensi::where('kegiatan_id', $notulensi->kegiatan_id)->where('status', 'hadir')->get()
            : [];

        $pdf = Pdf::loadView('admin.notulensi.pdf', compact('notulensi', 'absensi'));
        return $pdf->download('Notulensi_Rapat_' . date('d_m_Y', strtotime($notulensi->tanggal)) . '.pdf');
    }

    public function finalize(Notulensi $notulensi)
    {
        // KODE LAMA: abort(403) dibuang!
        $notulensi->update(['status' => 'final']);
        return redirect()->route('notulensi.index')->with('success', 'Notulensi dikunci menjadi Final dan siap dicetak!');
    }
}
