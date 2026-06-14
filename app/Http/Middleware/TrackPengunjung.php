<?php

namespace App\Http\Middleware;

use App\Models\Pengunjung;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackPengunjung
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ambil IP Address pengunjung
        $ip_address = $request->ip();
        $hari_ini = date('Y-m-d');

        // Cari apakah IP ini sudah berkunjung hari ini. 
        // Jika belum, buat data baru.
        $pengunjung = Pengunjung::firstOrCreate(
            ['ip_address' => $ip_address, 'tanggal' => $hari_ini],
            ['hits' => 0] // Set hits awal 0 karena akan di-increment di bawah
        );

        // Tambah jumlah hits (klik halaman) untuk IP ini
        $pengunjung->increment('hits');

        // Lanjutkan request pengunjung ke halaman yang dituju
        return $next($request);
    }
}
