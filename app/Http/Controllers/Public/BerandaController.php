<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Artikel;
use App\Models\Galeri;
use App\Models\Kegiatan;
use App\Models\Organization;
use App\Models\ProgramKerja;
use App\Models\Slider;
use App\Models\SuratKeluar;
use App\Models\TeksBerjalan;
use App\Models\User;
use App\Models\Widget;
use Illuminate\Http\Request;

class BerandaController extends Controller
{
    public function index()
    {
        // 1. Ambil data Slider yang aktif
        $sliders = Slider::where('status_aktif', 1)->latest()->get();

        // 2. Ambil data Teks Berjalan yang aktif
        $pengumumans = TeksBerjalan::where('status_aktif', 1)->latest()->get();

        // 3. Ambil 6 Artikel terbaru yang berstatus 'publish'
        $artikels = Artikel::with(['kategori', 'user', 'organization'])
            ->where('status', 'publish')
            ->latest()
            ->take(6)
            ->get();

        $widgets = Widget::where('status_aktif', 1)->orderBy('urutan', 'asc')->get();

        // HITUNG STATISTIK ORGANISASI (DIPISAH IPNU & IPPNU)
        $statistik_org = [
            // Menghitung Jumlah Ranting/Komisariat IPNU
            'ranting_ipnu' => Organization::whereIn('type', ['ranting'])
                ->where('jenis_organisasi', 'ipnu')
                ->count(),

            // Menghitung Jumlah Ranting/Komisariat IPPNU
            'ranting_ippnu' => Organization::whereIn('type', ['ranting', 'komisariat'])
                ->where('jenis_organisasi', 'ippnu')
                ->count(),

            // Menghitung Anggota IPNU (Asumsi berdasarkan relasi ke organisasi IPNU, ATAU kolom jenis kelamin Laki-laki)
            // Jika di tabel members ada kolom 'jenis_kelamin', bisa pakai: where('jenis_kelamin', 'L')->count()
            'anggota_ipnu' => User::whereHas('organization', function ($query) {
                $query->where('jk', 'L');
            })->count() ?? 0,

            // Menghitung Anggota IPPNU (Putri)
            // Jika di tabel members ada kolom 'jenis_kelamin', bisa pakai: where('jenis_kelamin', 'P')->count()
            'anggota_ippnu' => User::whereHas('organization', function ($query) {
                $query->where('jk', 'P');
            })->count() ?? 0,

            // Contoh: Menghitung total kegiatan yang sudah dijalankan
            'total_kegiatan' => ProgramKerja::where('status', 'completed')->count() ?? 0,

            // Contoh: Menghitung total surat keluar (sebagai bukti aktifnya administrasi)
            'total_surat' => SuratKeluar::where('status', 'selesai')->count() ?? 0,

        ];
        $agendas = ProgramKerja::where('tgl_mulai', '>=', now()->toDateString())
            ->orderBy('tgl_mulai', 'asc')
            ->take(4)
            ->get();

        $kegiatan_galeri = ProgramKerja::has('fotoPublik')
            ->with('fotoPublik')
            ->orderBy('tgl_mulai', 'desc')
            ->take(8)
            ->get();
        return view('public.beranda', compact('sliders', 'pengumumans', 'artikels', 'widgets', 'statistik_org', 'agendas', 'kegiatan_galeri'));
    }
}
