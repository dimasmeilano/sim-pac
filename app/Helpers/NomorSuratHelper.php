<?php

namespace App\Helpers;

use App\Models\Organization;
use App\Models\SuratKeluar;

class NomorSuratHelper
{
    /**
     * Mendapatkan nomor urut terakhir secara GLOBAL di tahun berjalan.
     * Menggunakan filter tahun agar nomor kembali ke 001 setiap ganti tahun.
     */
    private static function getLastGlobalNomorUrut($tahun)
    {
        $lastSurat = SuratKeluar::whereYear('created_at', $tahun)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastSurat && $lastSurat->nomor_surat) {
            $parts = explode('/', $lastSurat->nomor_surat);
            return isset($parts[0]) ? (int)$parts[0] : 0;
        }
        return 0;
    }

    /**
     * Mendapatkan nomor urut berikutnya (global)
     */
    public static function getNextGlobalNomor($tahun = null)
    {
        $tahun = $tahun ?? date('Y');
        $lastNumber = self::getLastGlobalNomorUrut($tahun);
        return str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Generate nomor surat sesuai aturan IPNU (Tetap berurutan global)
     * Format: NOURUT/TINGKAT/KODEINDEKS/7354/PERIODE/BULAN/TAHUNDUADIGIT
     */
    public static function generateIpnu($tingkat, $kodeIndeks, $periode, $bulan, $tahun = null)
    {
        $tingkat = strtoupper($tingkat);
        $tahun = $tahun ?? date('Y');
        $tahunKelahiran = '7354';
        $tahunDuaDigit = substr($tahun, -2);

        // Mengambil nomor urut global (Tidak peduli ST, SK, atau SRP)
        $nomorUrut = self::getNextGlobalNomor($tahun);

        return implode('/', [
            $nomorUrut,
            $tingkat,
            $kodeIndeks,
            $tahunKelahiran,
            strtoupper($periode),
            $bulan,
            $tahunDuaDigit
        ]);
    }

    /**
     * Generate nomor surat sesuai aturan IPPNU (Tetap berurutan global)
     * Format: NOURUT/TINGKAT/KODEINDEKS/7455/PERIODE/BULAN/TAHUN
     */
    public static function generateIppnu($tingkat, $kodeIndeks, $periode, $bulan, $tahun = null)
    {
        $tingkat = strtoupper($tingkat);
        $tahun = $tahun ?? date('Y');
        $tahunKelahiran = '7455';

        // Mengambil nomor urut global (Tidak peduli ST, SK, atau SRP)
        $nomorUrut = self::getNextGlobalNomor($tahun);

        return implode('/', [
            $nomorUrut,
            $tingkat,
            $kodeIndeks,
            $tahunKelahiran,
            strtoupper($periode),
            $bulan,
            $tahun
        ]);
    }

    /**
     * Auto detect organisasi dan generate nomor
     */
    public static function generate($organisasi, $tingkat, $kodeIndeks, $periode, $bulan, $tahun = null)
    {
        if (strtolower($organisasi) == 'ipnu') {
            return self::generateIpnu($tingkat, $kodeIndeks, $periode, $bulan, $tahun);
        }
        return self::generateIppnu($tingkat, $kodeIndeks, $periode, $bulan, $tahun);
    }

    /**
     * Generate dengan bulan sekarang
     */
    public static function generateWithCurrentMonth($organisasi, $tingkat, $kodeIndeks, $periode, $tahun = null)
    {
        $bulan = self::bulanToRomawi(date('n'));
        $tahun = $tahun ?? date('Y');
        return self::generate($organisasi, $tingkat, $kodeIndeks, $periode, $bulan, $tahun);
    }

    /**
     * Konversi tipe organisasi ke kode tingkat
     */
    public static function getTingkatFromType($type)
    {
        $map = [
            'pac' => 'PAC',
            'ranting' => 'PR',
            'cabang' => 'PC',
            'wilayah' => 'PW',
            'pusat' => 'PP',
            'komisariat' => 'PK',
            'cabang_istimewa' => 'PCI',
        ];

        $typeLower = strtolower($type);
        $result = $map[$typeLower] ?? strtoupper($type);

        return strtoupper($result);
    }

    /**
     * Daftar kode indeks umum
     */
    public static function getIndeksUmumOptions()
    {
        return [
            'A' => 'Surat untuk lingkungan internal',
            'B' => 'Surat untuk lingkungan eksternal',
            'C' => 'Surat untuk NU, Banom, lembaga',
        ];
    }

    /**
     * Daftar kode indeks khusus
     */
    public static function getIndeksKhususOptions()
    {
        return [
            'SK' => 'Surat Keputusan',
            'SP' => 'Surat Pengesahan',
            'SPk' => 'Surat Pengangkatan',
            'SPh' => 'Surat Pemberhentian',
            'SRP' => 'Surat Rekomendasi Pengesahan',
            'SM' => 'Surat Mandat',
            'ST' => 'Surat Tugas',
            'SPt' => 'Surat Pengantar',
            'SKt' => 'Surat Keterangan',
            'SR' => 'Surat Rekomendasi',
            'SI' => 'Surat Instruksi',
            'SPy' => 'Surat Pernyataan',
            'SE' => 'Surat Edaran',
            'SKu' => 'Surat Kuasa',
        ];
    }

    /**
     * Konversi bulan ke angka romawi
     */
    public static function bulanToRomawi($bulan)
    {
        $romawi = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII'
        ];
        return $romawi[(int)$bulan];
    }

    /**
     * Mendapatkan periode kepengurusan saat ini
     */
    public static function getPeriodeFromOrganization($organizationId = null)
    {
        if ($organizationId) {
            $org = Organization::find($organizationId);
            if ($org && $org->periode) {
                return strtoupper($org->periode);
            }
        }

        // Fallback: cari organisasi PAC default
        $pac = Organization::where('type', 'pac')->first();
        if ($pac && $pac->periode) {
            return strtoupper($pac->periode);
        }

        return 'XVI'; // Default
    }

    /**
     * Mendapatkan informasi ketua dari organisasi
     */
    public static function getKetuaInfo($organizationId)
    {
        $org = Organization::find($organizationId);
        if ($org && $org->ketua) {
            return [
                'nama' => $org->ketua->name,
                'nia' => $org->ketua->nik ?? '-',
            ];
        }
        return null;
    }

    /**
     * Mendapatkan informasi sekretaris dari organisasi
     */
    public static function getSekretarisInfo($organizationId)
    {
        $org = Organization::find($organizationId);
        if ($org && $org->sekretaris) {
            return [
                'nama' => $org->sekretaris->name,
                'nia' => $org->sekretaris->nik ?? '-',
            ];
        }
        return null;
    }

    /**
     * Format Tanggal Indonesia (Ditambahkan keyword 'public static')
     */
    public static function formatTanggalIndonesia($tanggal)
    {
        $bulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        $timestamp = strtotime($tanggal);
        $hari = date('d', $timestamp);
        $bulanIndex = (int)date('m', $timestamp);
        $tahun = date('Y', $timestamp);

        return $hari . ' ' . $bulan[$bulanIndex] . ' ' . $tahun;
    }
}
