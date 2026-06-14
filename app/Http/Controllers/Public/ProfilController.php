<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\IdentitasWeb;
use App\Models\Organization;
use Illuminate\Http\Request;

class ProfilController extends Controller
{
    public function index()
    {
        // 1. Ambil data teks profil dari Identitas Web
        $profil = IdentitasWeb::first();

        // 2. Ambil data struktur pengurus PAC (Pimpinan Anak Cabang)
        // Sesuaikan 'jenis_organisasi' dengan yang ada di database Anda
        $pac_ipnu = Organization::where('type', 'pac')->where('jenis_organisasi', 'ipnu')->first();
        $pac_ippnu = Organization::where('type', 'pac')->where('jenis_organisasi', 'ippnu')->first();

        return view('public.profil', compact('profil', 'pac_ipnu', 'pac_ippnu'));
    }
}
