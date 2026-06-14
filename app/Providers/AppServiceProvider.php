<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();
        // MENGATUR ZONA WAKTU (WIB)
        $tanggal_sekarang = Carbon::now()->setTimezone('Asia/Jakarta');

        // 1. TANGGAL MASEHI
        $masehi = $tanggal_sekarang->translatedFormat('d F Y');

        // 2. PASARAN JAWA
        $hari_hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $hari_indonesia = $hari_hari[$tanggal_sekarang->dayOfWeek];
        $pasaran_array = ['Legi', 'Pahing', 'Pon', 'Wage', 'Kliwon'];

        // Rumus mencari pasaran (Acuan: 1 Jan 1970 = Kamis Wage)
        $days = floor(strtotime($tanggal_sekarang->format('Y-m-d')) / 86400);
        $pasaran = $pasaran_array[($days + 3) % 5];

        // 3. TANGGAL HIJRIAH (Algoritma Umm al-Qura)
        $y = $tanggal_sekarang->year;
        $m = $tanggal_sekarang->month;
        $d = $tanggal_sekarang->day;

        $jd = intdiv((1461 * ($y + 4800 + intdiv(($m - 14), 12))), 4) + intdiv((367 * ($m - 2 - 12 * (intdiv(($m - 14), 12)))), 12) - intdiv((3 * (intdiv(($y + 4900 + intdiv(($m - 14), 12)), 100))), 4) + $d - 32075;
        $l = $jd - 1948440 + 10632;
        $n = intdiv(($l - 1), 10631);
        $l = $l - 10631 * $n + 354;
        $j = (intdiv((10985 - $l), 5316)) * (intdiv((50 * $l), 17719)) + (intdiv($l, 5670)) * (intdiv((43 * $l), 15238));
        $l = $l - (intdiv((30 - $j), 15)) * (intdiv((17719 * $j), 50)) - (intdiv($j, 16)) * (intdiv((15238 * $j), 43)) + 29;

        $m_hijri = intdiv((24 * $l), 709);
        $d_hijri = $l - intdiv((709 * $m_hijri), 24);
        $y_hijri = 30 * $n + $j - 30;

        $bulan_hijri = ['', 'Muharram', 'Safar', 'Rabiul Awal', 'Rabiul Akhir', 'Jumadil Awal', 'Jumadil Akhir', 'Rajab', 'Sya\'ban', 'Ramadhan', 'Syawal', 'Dzulqa\'dah', 'Dzulhijjah'];
        $hijriah = $d_hijri . ' ' . $bulan_hijri[$m_hijri] . ' ' . $y_hijri . ' H';

        // 4. GABUNGKAN & BAGIKAN KE SELURUH HALAMAN WEB
        $penanggalan_nu = "$hari_indonesia $pasaran, $masehi | $hijriah";
        View::share('penanggalan_nu', $penanggalan_nu);
    }
}
