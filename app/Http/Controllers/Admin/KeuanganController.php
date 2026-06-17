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

        // ========== QUERY DAFTAR TRANSAKSI (Otomatis Filter Ranting!) ==========
        $query = Transaksi::with('programKerja', 'kegiatan', 'createdBy', 'validator');

        // Cukup tambahkan filter berdasarkan "jenis_organisasi" (IPNU/IPPNU)
        if ($user->hasRole('super_admin') && $request->filled('jenis_organisasi')) {
            $query->where('jenis_organisasi', $request->jenis_organisasi);
        } elseif (!$user->hasRole('super_admin') && $user->organization) {
            $query->whereIn('jenis_organisasi', [$user->organization->jenis_organisasi, 'bersama']);
        }

        // Filter Jenis Kas (Masuk/Keluar)
        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        // (Sisa kodingan pagination & return view tetap sama seperti aslinya)
        $keuangan = $query->orderBy('tanggal', 'desc')->paginate(10);
        return view('admin.keuangan.index', compact('keuangan'));
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

        // [FIX BUG 2] Bypass validasi organisasi jika user adalah super_admin
        if (!$user->hasRole('super_admin')) {
            $isBendaharaIpnu = $user->hasAnyRole(['bendahara_pac', 'bendahara_ranting']) && $userJenis == 'ipnu';
            $isBendaharaIppnu = $user->hasAnyRole(['bendahara_pac', 'bendahara_ranting']) && $userJenis == 'ippnu';
            $isWakilIpnu = $user->hasAnyRole(['wakil_bendahara_pac', 'wakil_bendahara_ranting']) && $userJenis == 'ipnu';
            $isWakilIppnu = $user->hasAnyRole(['wakil_bendahara_pac', 'wakil_bendahara_ranting']) && $userJenis == 'ippnu';

            if ($selectedJenis == 'ipnu' && !($isBendaharaIpnu || $isWakilIpnu)) {
                return back()->with('error', 'Anda tidak dapat membuat transaksi IPNU');
            }

            if ($selectedJenis == 'ippnu' && !($isBendaharaIppnu || $isWakilIppnu)) {
                return back()->with('error', 'Anda tidak dapat membuat transaksi IPPNU');
            }

            if ($selectedJenis == 'bersama' && !($isBendaharaIpnu || $isBendaharaIppnu || $isWakilIpnu || $isWakilIppnu)) {
                return back()->with('error', 'Anda tidak dapat membuat transaksi bersama');
            }

            $isBendahara = $isBendaharaIpnu || $isBendaharaIppnu;
        } else {
            // Super admin dianggap setara bendahara (langsung ACC)
            $isBendahara = true;
        }

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

        $kode = $this->generateKodeTransaksi($request->jenis);

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
            'status_validasi' => $statusValidasi,
            'divalidasi_oleh' => $divalidasiOleh,
            'tanggal_validasi' => $tanggalValidasi,
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

        // [FIX BUG 3] Kunci Keuangan: Jangan izinkan edit jika sudah disetujui (kecuali Super Admin)
        if ($keuangan->status_validasi == 'disetujui' && !$user->hasRole('super_admin')) {
            return back()->with('error', 'Transaksi yang sudah disetujui tidak dapat diubah. Silakan hubungi Super Admin jika terjadi kesalahan.');
        }

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

        if (!$user->hasRole('super_admin')) {
            $userJenis = $user->organization?->jenis_organisasi ?? null;

            if ($keuangan->jenis_organisasi == 'ipnu' && $userJenis != 'ipnu') {
                return back()->with('error', 'Anda tidak dapat mengedit transaksi IPNU');
            }
            if ($keuangan->jenis_organisasi == 'ippnu' && $userJenis != 'ippnu') {
                return back()->with('error', 'Anda tidak dapat mengedit transaksi IPPNU');
            }
        }

        if ($request->hasFile('bukti')) {
            if ($keuangan->bukti_file && Storage::disk('public')->exists($keuangan->bukti_file)) {
                Storage::disk('public')->delete($keuangan->bukti_file);
            }
            $file = $request->file('bukti');
            $filename = time() . '_' . $file->getClientOriginalName();
            $buktiPath = $file->storeAs('keuangan/bukti', $filename, 'public');
            $keuangan->bukti_file = $buktiPath;
        }

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

        if ($keuangan->status_validasi == 'ditolak' && auth()->user()->id == $keuangan->created_by) {
            $keuangan->update([
                'status_validasi' => 'menunggu',
                'divalidasi_oleh' => null,
                'tanggal_validasi' => null,
            ]);
            return redirect()->route('keuangan.index')->with('success', 'Transaksi berhasil diperbaiki dan diajukan ulang untuk validasi.');
        }

        return redirect()->route('keuangan.index')->with('success', 'Transaksi berhasil diupdate');
    }

    public function show(Transaksi $keuangan)
    {
        $user = auth()->user();

        if (!$user->hasRole('super_admin') && $keuangan->organization_id != $user->organization_id) {
            abort(403, 'Akses Ditolak! Transaksi ini milik organisasi lain.');
        }
        if (!$user->hasRole('super_admin')) {
            $userJenis = $user->organization?->jenis_organisasi ?? null;

            if ($keuangan->jenis_organisasi == 'ipnu' && $userJenis != 'ipnu' && $keuangan->jenis_organisasi != 'bersama') {
                abort(403, 'Anda tidak memiliki akses ke transaksi ini');
            }
            if ($keuangan->jenis_organisasi == 'ippnu' && $userJenis != 'ippnu' && $keuangan->jenis_organisasi != 'bersama') {
                abort(403, 'Anda tidak memiliki akses ke transaksi ini');
            }
        }

        return view('admin.keuangan.show', compact('keuangan'));
    }

    public function destroy(Transaksi $keuangan)
    {
        $user = auth()->user();

        // [FIX BUG 3] Cek Hak Akses Delete
        if (!$user->hasRole('super_admin') && $keuangan->created_by != $user->id) {
            return back()->with('error', 'Anda hanya dapat menghapus transaksi yang Anda buat sendiri.');
        }

        // [FIX BUG 3] Kunci Keuangan: Jangan izinkan hapus jika sudah disetujui (kecuali Super Admin)
        if ($keuangan->status_validasi == 'disetujui' && !$user->hasRole('super_admin')) {
            return back()->with('error', 'Transaksi yang sudah disetujui tidak dapat dihapus.');
        }

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

        $jenisOrganisasi = $request->jenis_organisasi ?? null;

        $query = Transaksi::with('programKerja')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->where('status_validasi', 'disetujui');

        if ($user->hasRole('super_admin')) {
            if ($jenisOrganisasi) {
                $query->where('jenis_organisasi', $jenisOrganisasi);
            }
        } elseif ($user->organization_id) {
            $jenisOrganisasi = $user->organization->jenis_organisasi;
            // [FIX BUG 4] Masukkan transaksi bersama ke dalam laporan
            $query->where(function ($q) use ($jenisOrganisasi) {
                $q->where('jenis_organisasi', $jenisOrganisasi)
                    ->orWhere('jenis_organisasi', 'bersama');
            });
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

        $query = Transaksi::with('programKerja')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->where('status_validasi', 'disetujui'); // PDF hanya mencetak yang sudah disetujui

        // [FIX BUG 4] Filter berdasarkan hak akses dan sertakan 'bersama'
        if ($user->hasRole('super_admin')) {
            if ($request->filled('jenis_organisasi')) {
                $query->where('jenis_organisasi', $request->jenis_organisasi);
            }
        } elseif ($user->organization_id) {
            $jenisOrganisasi = $user->organization->jenis_organisasi;
            $query->where(function ($q) use ($jenisOrganisasi) {
                $q->where('jenis_organisasi', $jenisOrganisasi)
                    ->orWhere('jenis_organisasi', 'bersama');
            });
        } else {
            $query->whereRaw('1 = 0');
        }

        $transaksi = $query->orderBy('tanggal', 'asc')->get();

        $totalMasuk = $transaksi->where('jenis', 'masuk')->sum('nominal');
        $totalKeluar = $transaksi->where('jenis', 'keluar')->sum('nominal');
        $saldo = $totalMasuk - $totalKeluar;

        if ($user->organization_id) {
            $organization = $user->organization;
        } else {
            $organization = Organization::where('type', 'pac')
                ->where('jenis_organisasi', $request->jenis_organisasi ?? 'bersama')
                ->first();
        }

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

        $isBendaharaIpnu = $user->hasAnyRole(['bendahara_pac', 'bendahara_ranting']) && $user->organization?->jenis_organisasi == 'ipnu';
        $isBendaharaIppnu = $user->hasAnyRole(['bendahara_pac', 'bendahara_ranting']) && $user->organization?->jenis_organisasi == 'ippnu';
        $isWakilIpnu = $user->hasAnyRole(['wakil_bendahara_pac', 'wakil_bendahara_ranting']) && $user->organization?->jenis_organisasi == 'ipnu';
        $isWakilIppnu = $user->hasAnyRole(['wakil_bendahara_pac', 'wakil_bendahara_ranting']) && $user->organization?->jenis_organisasi == 'ippnu';

        $isBendahara = $isBendaharaIpnu || $isBendaharaIppnu;

        if (!$isBendahara && !$user->hasRole('super_admin')) {
            return back()->with('error', 'Hanya Bendahara atau Super Admin yang dapat memvalidasi transaksi');
        }

        if (!$user->hasRole('super_admin')) {
            if ($keuangan->jenis_organisasi == 'ipnu' && !$isBendaharaIpnu) {
                return back()->with('error', 'Hanya Bendahara IPNU yang dapat memvalidasi transaksi IPNU');
            }

            if ($keuangan->jenis_organisasi == 'ippnu' && !$isBendaharaIppnu) {
                return back()->with('error', 'Hanya Bendahara IPPNU yang dapat memvalidasi transaksi IPPNU');
            }
        }

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
