<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Models\Lpj;
use App\Models\ProgramKerja;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class LpjController extends Controller
{
    // 1. Tampilkan Form Buat LPJ
    public function create(Request $request)
    {
        // Ambil data progja beserta seluruh galerinya untuk dipilih
        $progja = ProgramKerja::with(['kegiatans.folders.galeris'])->findOrFail($request->progja_id);

        return view('admin.lpj.create', compact('progja'));
    }

    // 2. Simpan Data LPJ ke Database
    public function store(Request $request)
    {
        $request->validate([
            'program_kerja_id' => 'required|exists:program_kerja,id',
            'tema_kegiatan' => 'required|string|max:255',
            'nama_ketua_panitia' => 'required|string|max:100',
            'nama_sekretaris' => 'required|string|max:100',
            'file_lampiran_panitia' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'file_lampiran_acara' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'foto_dokumentasi_terpilih' => 'nullable|array|max:4', // Maksimal 4 foto
        ]);

        $data = $request->except(['_token', 'file_lampiran_panitia', 'file_lampiran_acara']);

        // Proses Upload Gambar Lampiran Panitia (SK)
        if ($request->hasFile('file_lampiran_panitia')) {
            $data['file_lampiran_panitia'] = $request->file('file_lampiran_panitia')->store('lpj/lampiran', 'public');
        }

        // Proses Upload Gambar Lampiran Acara (Rundown)
        if ($request->hasFile('file_lampiran_acara')) {
            $data['file_lampiran_acara'] = $request->file('file_lampiran_acara')->store('lpj/lampiran', 'public');
        }

        Lpj::create($data);

        return redirect()->route('progja.index')->with('success', 'Mantap! Data LPJ berhasil disimpan dan siap dicetak.');
    }

    // 3. Cetak LPJ ke PDF
    public function cetakLpjPdf(Lpj $lpj)
    {
        // 1. Sedot seluruh data terkait agar proses cetak lancar
        $lpj->load([
            'programKerja.organization',
            'programKerja.transaksis.createdBy',
            'programKerja.kegiatans.absensi.user' // Data kegiatan dan absensi SUDAH TERSEDOT di sini
        ]);

        $programKerja = $lpj->programKerja;

        // 2. SOLUSI ERROR: Ambil data kegiatan dari relasi yang sudah diload di atas
        // Menggunakan ->first() jika 1 Proker = 1 Kegiatan Utama
        $kegiatan = $programKerja->kegiatans->first();

        // Pengecekan Keamanan Multi-Tenant
        $user = auth()->user();
        if (!$user->hasRole('super_admin') && $programKerja->organization_id != $user->organization_id) {
            abort(403, 'Akses Ditolak!');
        }

        // Rekap Keuangan
        $pemasukan = $programKerja->transaksis->where('jenis', 'masuk')->where('status_validasi', 'disetujui')->sum('nominal');
        $pengeluaran = $programKerja->transaksis->where('jenis', 'keluar')->where('status_validasi', 'disetujui')->sum('nominal');
        $saldo_akhir = $pemasukan - $pengeluaran;

        $pdf = Pdf::loadView('admin.lpj.pdf', compact(
            'lpj',
            'programKerja',
            'pemasukan',
            'pengeluaran',
            'saldo_akhir',
            'kegiatan'
        ));

        // Format kertas A4
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('LPJ_' . str_replace(' ', '_', $programKerja->nama) . '.pdf');
    }

    public function cetakRingkasanPdf($id)
    {
        // Tarik data kegiatan beserta relasi yang dibutuhkan
        $kegiatan = \App\Models\Kegiatan::with(['proker.organization', 'keuangan', 'absensis'])->findOrFail($id);

        // Hitung rekap singkat
        $totalPengeluaran = $kegiatan->keuangan->where('jenis', 'pengeluaran')->sum('nominal');
        $totalPeserta = $kegiatan->absensis->count();

        // Load view PDF
        $pdf = Pdf::loadView('admin.kegiatan.ringkasan_pdf', compact('kegiatan', 'totalPengeluaran', 'totalPeserta'));

        // Format kertas A4
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('Ringkasan_Kegiatan_' . str_replace(' ', '_', $kegiatan->nama_kegiatan) . '.pdf');
    }
}
