<?php

namespace App\Http\Controllers;

use App\Models\Pengunjung;
use App\Models\ProgramKerja;
use App\Models\SuratKeluar;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Data Keuangan
        // 1. Data Keuangan Terpisah
        // Kas IPNU
        $pemasukanIpnu = Transaksi::where('jenis', 'masuk')
            ->where('jenis_organisasi', 'ipnu')
            ->where('status_validasi', 'disetujui')
            ->sum('nominal');
        $pengeluaranIpnu = Transaksi::where('jenis', 'keluar')
            ->where('jenis_organisasi', 'ipnu')
            ->where('status_validasi', 'disetujui')
            ->sum('nominal');
        $saldoIpnu = $pemasukanIpnu - $pengeluaranIpnu;

        // Kas IPPNU
        $pemasukanIppnu = Transaksi::where('jenis', 'masuk')
            ->where('jenis_organisasi', 'ippnu')
            ->where('status_validasi', 'disetujui')
            ->sum('nominal');
        $pengeluaranIppnu = Transaksi::where('jenis', 'keluar')
            ->where('jenis_organisasi', 'ippnu')
            ->where('status_validasi', 'disetujui')
            ->sum('nominal');
        $saldoIppnu = $pemasukanIppnu - $pengeluaranIppnu;

        // Kas Bersama / PAC
        $pemasukanBersama = Transaksi::where('jenis', 'masuk')
            ->where('jenis_organisasi', 'bersama')
            ->where('status_validasi', 'disetujui')
            ->sum('nominal');
        $pengeluaranBersama = Transaksi::where('jenis', 'keluar')
            ->where('jenis_organisasi', 'bersama')
            ->where('status_validasi', 'disetujui')
            ->sum('nominal');
        $saldoBersama = $pemasukanBersama - $pengeluaranBersama;

        // Total Global (Untuk Grafik Chart.js)
        $pemasukanGlobal = Transaksi::where('jenis', 'masuk')
            ->where('status_validasi', 'disetujui')
            ->sum('nominal');
        $pengeluaranGlobal = Transaksi::where('jenis', 'keluar')
            ->where('status_validasi', 'disetujui')
            ->sum('nominal');
        $saldoKasGlobal = $pemasukanGlobal - $pengeluaranGlobal;

        // 2. Data Surat (Asumsi Anda punya status 'selesai' atau 'disetujui')
        $suratMenunggu = SuratKeluar::whereIn('status_validasi', [
            'menunggu_validasi_wakil',
            'menunggu_ttd_ketua',
            'menunggu_ttd_sekretaris'
        ])->count();

        // Mencari surat yang sudah tuntas
        $suratSelesai = SuratKeluar::where('status_validasi', 'selesai')->count();
        $daftarSuratMenunggu = SuratKeluar::whereIn('status_validasi', [
            'menunggu_validasi_wakil',
            'menunggu_ttd_ketua',
            'menunggu_ttd_sekretaris'
        ])->latest()->take(5)->get();
        // Catatan: Jika nama status selesainya berbeda (misal: 'disetujui'), silakan diganti ya kata 'selesai'-nya.

        // STATISTIK PENGUNJUNG WEBSITE
        $hari_ini = date('Y-m-d');
        $bulan_ini = date('m');
        $tahun_ini = date('Y');

        $statistik = [
            // Pengunjung unik (berdasarkan IP per hari)
            'unik_hari_ini' => Pengunjung::where('tanggal', $hari_ini)->count(),
            'unik_bulan_ini' => Pengunjung::whereMonth('tanggal', $bulan_ini)->whereYear('tanggal', $tahun_ini)->count(),
            'unik_total' => Pengunjung::count(),

            // Total halaman yang dibuka (Page Views / Hits)
            'hits_hari_ini' => Pengunjung::where('tanggal', $hari_ini)->sum('hits') ?? 0,
        ];

        // 3. Kirim semua data ke View
        return view('dashboard', [
            'saldo_ipnu' => $saldoIpnu,
            'saldo_ippnu' => $saldoIppnu,
            'saldo_bersama' => $saldoBersama,
            'saldo_kas' => $saldoKasGlobal,
            'pemasukan' => $pemasukanGlobal,
            'pengeluaran' => $pengeluaranGlobal,
            'total_anggota' => User::count(),
            'progja_aktif' => ProgramKerja::where('status', 'berjalan')->count(),
            'surat_menunggu' => $suratMenunggu,
            'surat_selesai' => $suratSelesai,
            'daftar_surat_menunggu' => $daftarSuratMenunggu,
            'statistik' => $statistik,
        ]);
    }
}
