<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Models\Organization;
use App\Models\ProgramKerja;
use App\Models\Transaksi;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class KeuanganController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        // ========== QUERY UNTUK DAFTAR TRANSAKSI (SEMUA STATUS) ==========
        $query = Transaksi::with('programKerja', 'kegiatan', 'createdBy', 'validator');

        // Filter untuk daftar transaksi
        if ($user->hasRole('super_admin')) {
            if ($request->filled('jenis_organisasi')) {
                $query->where('jenis_organisasi', $request->jenis_organisasi);
            }
        } elseif ($user->organization_id) {
            $query->where(function ($q) use ($user) {
                $q->where('jenis_organisasi', $user->organization->jenis_organisasi)
                    ->orWhere('jenis_organisasi', 'bersama');
            });
        } else {
            $query->whereRaw('1 = 0');
        }

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal', $request->tahun);
        }

        // Daftar transaksi (bisa lihat semua status)
        $transaksi = $query->orderBy('tanggal', 'desc')->paginate(10);

        // ========== HITUNG SALDO PER JENIS ORGANISASI ==========
        // Gunakan raw query dengan kutip SATU ('')
        $saldoIpnu = Transaksi::where('jenis_organisasi', 'ipnu')
            ->where('status_validasi', 'disetujui')
            ->select(DB::raw("SUM(CASE WHEN jenis = 'masuk' THEN nominal ELSE -nominal END) as total"))
            ->value('total') ?? 0;

        $saldoIppnu = Transaksi::where('jenis_organisasi', 'ippnu')
            ->where('status_validasi', 'disetujui')
            ->select(DB::raw("SUM(CASE WHEN jenis = 'masuk' THEN nominal ELSE -nominal END) as total"))
            ->value('total') ?? 0;

        $saldoBersama = Transaksi::where('jenis_organisasi', 'bersama')
            ->where('status_validasi', 'disetujui')
            ->select(DB::raw("SUM(CASE WHEN jenis = 'masuk' THEN nominal ELSE -nominal END) as total"))
            ->value('total') ?? 0;

        // Total saldo gabungan (semua transaksi yang disetujui)
        $totalMasuk = Transaksi::where('status_validasi', 'disetujui')->where('jenis', 'masuk')->sum('nominal');
        $totalKeluar = Transaksi::where('status_validasi', 'disetujui')->where('jenis', 'keluar')->sum('nominal');
        $saldoGabungan = $totalMasuk - $totalKeluar;

        $programKerja = ProgramKerja::all();
        $kegiatan = Kegiatan::all();

        return view('admin.keuangan.index', compact(
            'transaksi',
            'totalMasuk',
            'totalKeluar',
            'saldoGabungan',
            'programKerja',
            'kegiatan',
            'saldoIpnu',
            'saldoIppnu',
            'saldoBersama'
        ));
    }

    public function create()
    {
        $programKerja = ProgramKerja::where('status', 'active')->get();
        $kegiatan = Kegiatan::all();
        return view('admin.keuangan.create', compact('programKerja', 'kegiatan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:200',
            'jenis' => 'required|in:masuk,keluar',
            'jenis_organisasi' => 'required|in:ipnu,ippnu,bersama',
            'nominal' => 'required|numeric|min:0',
            'tanggal' => 'required|date',
            'kategori' => 'nullable|string|max:50',
            'keterangan' => 'nullable|string',
            'program_kerja_id' => 'nullable|exists:program_kerja,id',
            'kegiatan_id' => 'nullable|exists:kegiatan,id',
            'bukti' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        $user = auth()->user();
        $userJenis = $user->organization?->jenis_organisasi ?? null;
        $selectedJenis = $request->jenis_organisasi;

        // ========== VALIDASI AKSES JENIS ORGANISASI ==========
        // Cek apakah user berhak membuat transaksi jenis ini
        $isBendaharaIpnu = $user->hasRole('bendahara_pac') && $user->organization?->jenis_organisasi == 'ipnu';
        $isBendaharaIppnu = $user->hasRole('bendahara_pac') && $user->organization?->jenis_organisasi == 'ippnu';
        $isWakilIpnu = $user->hasRole('wakil_bendahara_pac') && $user->organization?->jenis_organisasi == 'ipnu';
        $isWakilIppnu = $user->hasRole('wakil_bendahara_pac') && $user->organization?->jenis_organisasi == 'ippnu';

        // Validasi akses berdasarkan jenis transaksi
        if ($selectedJenis == 'ipnu' && !($isBendaharaIpnu || $isWakilIpnu)) {
            return back()->with('error', 'Anda tidak dapat membuat transaksi IPNU');
        }

        if ($selectedJenis == 'ippnu' && !($isBendaharaIppnu || $isWakilIppnu)) {
            return back()->with('error', 'Anda tidak dapat membuat transaksi IPPNU');
        }

        // Transaksi bersama: bisa dibuat oleh semua bendahara/wakil
        if ($selectedJenis == 'bersama' && !($isBendaharaIpnu || $isBendaharaIppnu || $isWakilIpnu || $isWakilIppnu)) {
            return back()->with('error', 'Anda tidak dapat membuat transaksi bersama');
        }

        // Tentukan status validasi
        $isBendahara = $isBendaharaIpnu || $isBendaharaIppnu;

        if ($isBendahara) {
            $statusValidasi = 'disetujui';
            $divalidasiOleh = $user->id;
            $tanggalValidasi = now();
            $successMessage = 'Transaksi berhasil ditambahkan dan langsung disetujui';
        } else {
            $statusValidasi = 'menunggu';
            $divalidasiOleh = null;
            $tanggalValidasi = null;
            $successMessage = 'Transaksi disimpan, menunggu validasi Bendahara';
        }
        // =================================================

        // Generate kode transaksi
        $kode = $this->generateKodeTransaksi($request->jenis);

        // Upload bukti
        $buktiPath = null;
        if ($request->hasFile('bukti')) {
            $file = $request->file('bukti');
            $filename = time() . '_' . $file->getClientOriginalName();
            $buktiPath = $file->storeAs('keuangan/bukti', $filename, 'public');
        }

        $transaksi = Transaksi::create([
            'organization_id' => $user->organization_id,
            'jenis_organisasi' => $request->jenis_organisasi,
            'program_kerja_id' => $request->program_kerja_id,
            'kegiatan_id' => $request->kegiatan_id,
            'kode_transaksi' => $kode,
            'judul' => $request->judul,
            'jenis' => $request->jenis,
            'nominal' => $request->nominal,
            'tanggal' => $request->tanggal,
            'kategori' => $request->kategori,
            'keterangan' => $request->keterangan,
            'bukti_file' => $buktiPath,
            'created_by' => $user->id,
            'status_validasi' => $statusValidasi,        // <-- TAMBAHKAN
            'divalidasi_oleh' => $divalidasiOleh,        // <-- TAMBAHKAN
            'tanggal_validasi' => $tanggalValidasi,      // <-- TAMBAHKAN
        ]);

        return redirect()->route('keuangan.index')->with('success', $successMessage);
    }

    public function edit(Transaksi $keuangan)
    {
        $programKerja = ProgramKerja::where('status', 'active')->get();
        $kegiatan = Kegiatan::all();

        return view('admin.keuangan.edit', compact('keuangan', 'programKerja', 'kegiatan'));
    }

    public function update(Request $request, Transaksi $keuangan)
    {
        $user = auth()->user();

        $request->validate([
            'judul' => 'required|string|max:200',
            'jenis' => 'required|in:masuk,keluar',
            'nominal' => 'required|numeric|min:0',
            'tanggal' => 'required|date',
            'kategori' => 'nullable|string|max:50',
            'keterangan' => 'nullable|string',
            'program_kerja_id' => 'nullable|exists:program_kerja,id',
            'kegiatan_id' => 'nullable|exists:kegiatan,id',
            'bukti' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        // ========== CEK AKSES UPDATE ==========
        if (!$user->hasRole('super_admin')) {
            $userJenis = $user->organization?->jenis_organisasi ?? null;

            if ($keuangan->jenis_organisasi == 'ipnu' && $userJenis != 'ipnu') {
                return back()->with('error', 'Anda tidak dapat mengedit transaksi IPNU');
            }
            if ($keuangan->jenis_organisasi == 'ippnu' && $userJenis != 'ippnu') {
                return back()->with('error', 'Anda tidak dapat mengedit transaksi IPPNU');
            }
        }
        // ======================================

        // Upload bukti baru
        if ($request->hasFile('bukti')) {
            if ($keuangan->bukti_file && Storage::disk('public')->exists($keuangan->bukti_file)) {
                Storage::disk('public')->delete($keuangan->bukti_file);
            }
            $file = $request->file('bukti');
            $filename = time() . '_' . $file->getClientOriginalName();
            $buktiPath = $file->storeAs('keuangan/bukti', $filename, 'public');
            $keuangan->bukti_file = $buktiPath;
        }

        // Update data
        $keuangan->update([
            'program_kerja_id' => $request->program_kerja_id,
            'kegiatan_id' => $request->kegiatan_id,
            'judul' => $request->judul,
            'jenis' => $request->jenis,
            'nominal' => $request->nominal,
            'tanggal' => $request->tanggal,
            'kategori' => $request->kategori,
            'keterangan' => $request->keterangan,
        ]);

        // ========== RESET STATUS JIKA DITOLAK DAN DIEDIT OLEH PEMBUAT ==========
        if ($keuangan->status_validasi == 'ditolak' && auth()->user()->id == $keuangan->created_by) {
            $keuangan->update([
                'status_validasi' => 'menunggu',
                'divalidasi_oleh' => null,
                'tanggal_validasi' => null,
                // Catatan penolakan tetap disimpan sebagai history (tidak dihapus)
                // Bisa disimpan di kolom terpisah jika perlu
            ]);
            return redirect()->route('keuangan.index')->with('success', 'Transaksi berhasil diperbaiki dan diajukan ulang untuk validasi.');
        }
        // =========================================================================

        return redirect()->route('keuangan.index')->with('success', 'Transaksi berhasil diupdate');
    }

    public function show(Transaksi $keuangan)
    {
        $user = auth()->user();

        // Cek akses: user hanya bisa lihat transaksi dari jenis organisasinya
        if (!$user->hasRole('super_admin')) {
            $userJenis = $user->organization?->jenis_organisasi ?? null;

            // Jika transaksi IPNU dan user bukan IPNU, tolak akses
            if ($keuangan->jenis_organisasi == 'ipnu' && $userJenis != 'ipnu') {
                abort(403, 'Anda tidak memiliki akses ke transaksi ini');
            }

            // Jika transaksi IPPNU dan user bukan IPPNU, tolak akses
            if ($keuangan->jenis_organisasi == 'ippnu' && $userJenis != 'ippnu') {
                abort(403, 'Anda tidak memiliki akses ke transaksi ini');
            }
        }

        return view('admin.keuangan.show', compact('keuangan'));
    }

    public function destroy(Transaksi $keuangan)
    {
        if ($keuangan->bukti_file && Storage::disk('public')->exists($keuangan->bukti_file)) {
            Storage::disk('public')->delete($keuangan->bukti_file);
        }
        $keuangan->delete();
        return redirect()->route('keuangan.index')->with('success', 'Transaksi berhasil dihapus');
    }

    public function laporan(Request $request)
    {
        $user = auth()->user();
        $startDate = $request->start_date ?? date('Y-m-01');
        $endDate = $request->end_date ?? date('Y-m-t');

        // Inisialisasi variabel
        $jenisOrganisasi = $request->jenis_organisasi ?? null;

        $query = Transaksi::whereBetween('tanggal', [$startDate, $endDate])
            ->where('status_validasi', 'disetujui');

        if ($user->hasRole('super_admin')) {
            if ($jenisOrganisasi) {
                $query->where('jenis_organisasi', $jenisOrganisasi);
            }
        } elseif ($user->organization_id) {
            $query->where('jenis_organisasi', $user->organization->jenis_organisasi);
            $jenisOrganisasi = $user->organization->jenis_organisasi;
        } else {
            $query->whereRaw('1 = 0');
        }

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        $transaksi = $query->orderBy('tanggal', 'asc')->get();

        $totalMasuk = $transaksi->where('jenis', 'masuk')->sum('nominal');
        $totalKeluar = $transaksi->where('jenis', 'keluar')->sum('nominal');
        $saldo = $totalMasuk - $totalKeluar;

        if ($user->hasRole('super_admin') && $jenisOrganisasi) {
            $organization = Organization::where('type', 'pac')
                ->where('jenis_organisasi', $jenisOrganisasi)
                ->first();
        } elseif ($user->organization_id) {
            $organization = $user->organization;
        } else {
            $organization = Organization::where('type', 'pac')->first();
        }

        return view('admin.keuangan.laporan', compact(
            'transaksi',
            'totalMasuk',
            'totalKeluar',
            'saldo',
            'startDate',
            'endDate',
            'organization',
            'jenisOrganisasi'
        ));
    }

    private function generateKodeTransaksi($jenis)
    {
        $prefix = $jenis == 'masuk' ? 'KM' : 'KK';
        $bulan = date('Ym');
        $last = Transaksi::whereYear('created_at', date('Y'))
            ->whereMonth('created_at', date('m'))
            ->orderBy('id', 'desc')
            ->first();

        if ($last) {
            $lastNumber = (int) substr($last->kode_transaksi, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $prefix . '/' . $bulan . '/' . $newNumber;
    }

    public function exportPdf(Request $request)
    {

        $user = auth()->user();
        $startDate = $request->start_date ?? date('Y-m-01');
        $endDate = $request->end_date ?? date('Y-m-t');

        $query = Transaksi::whereBetween('tanggal', [$startDate, $endDate]);

        // ========== FILTER BERDASARKAN ROLE & ORGANISASI ==========
        if ($user->hasRole('super_admin')) {
            // Super admin: bisa filter berdasarkan jenis organisasi
            if ($request->filled('jenis_organisasi')) {
                $orgIds = Organization::where('jenis_organisasi', $request->jenis_organisasi)->pluck('id');
                $query->whereIn('organization_id', $orgIds);
            }
        } elseif ($user->organization_id) {
            // Bendahara/ketua: hanya lihat transaksi organisasinya sendiri
            $query->where('organization_id', $user->organization_id);
        } else {
            // User tidak punya organisasi dan bukan super admin
            $query->whereRaw('1 = 0');
        }
        // ===========================================================

        $transaksi = $query->orderBy('tanggal', 'asc')->get();

        $totalMasuk = $transaksi->where('jenis', 'masuk')->sum('nominal');
        $totalKeluar = $transaksi->where('jenis', 'keluar')->sum('nominal');
        $saldo = $totalMasuk - $totalKeluar;

        // Ambil organisasi untuk kop surat
        if ($user->organization_id) {
            $organization = $user->organization;
        } else {
            $organization = Organization::where('type', 'pac')
                ->where('jenis_organisasi', $request->jenis_organisasi ?? 'bersama')
                ->first();
        }

        // Generate PDF
        $pdf = PDF::loadView('admin.keuangan.laporan-pdf', compact(
            'transaksi',
            'totalMasuk',
            'totalKeluar',
            'saldo',
            'startDate',
            'endDate',
            'organization'
        ));

        return $pdf->download('laporan-keuangan-' . date('Y-m-d') . '.pdf');
    }

    public function validateTransaction(Request $request, Transaksi $keuangan)
    {
        $user = auth()->user();

        $isBendaharaIpnu = $user->hasRole('bendahara_pac') && $user->organization?->jenis_organisasi == 'ipnu';
        $isBendaharaIppnu = $user->hasRole('bendahara_pac') && $user->organization?->jenis_organisasi == 'ippnu';

        $isBendahara = $isBendaharaIpnu || $isBendaharaIppnu;

        if (!$isBendahara) {
            return back()->with('error', 'Hanya Bendahara yang dapat memvalidasi transaksi');
        }

        // Cek apakah user berhak memvalidasi jenis transaksi ini
        if ($keuangan->jenis_organisasi == 'ipnu' && !$isBendaharaIpnu) {
            return back()->with('error', 'Hanya Bendahara IPNU yang dapat memvalidasi transaksi IPNU');
        }

        if ($keuangan->jenis_organisasi == 'ippnu' && !$isBendaharaIppnu) {
            return back()->with('error', 'Hanya Bendahara IPPNU yang dapat memvalidasi transaksi IPPNU');
        }

        // TRANSAKSI BERSAMA: bisa divalidasi oleh bendahara IPNU ATAU IPPNU
        // (tidak ada pengecekan tambahan)

        // Cek apakah transaksi sudah divalidasi
        if ($keuangan->status_validasi != 'menunggu') {
            return back()->with('error', 'Transaksi sudah divalidasi sebelumnya');
        }

        $request->validate([
            'status_validasi' => 'required|in:disetujui,ditolak',
            'catatan_validasi' => 'nullable|string',
        ]);

        $keuangan->update([
            'status_validasi' => $request->status_validasi,
            'divalidasi_oleh' => $user->id,
            'catatan_validasi' => $request->catatan_validasi,
            'tanggal_validasi' => now(),
        ]);

        $statusText = $request->status_validasi == 'disetujui' ? 'disetujui' : 'ditolak';
        return redirect()->back()->with('success', "Transaksi berhasil {$statusText}");
    }
}
