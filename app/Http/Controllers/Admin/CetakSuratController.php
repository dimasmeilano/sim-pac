<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\NomorSuratHelper;
use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\SuratTemplate;
use App\Models\SuratKeluar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Biladina\HijriDateTime\HijriDateTime;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;

class CetakSuratController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:cetak_surat')->except(['index', 'preview']);
    }

    // ========== HELPER METHODS ==========

    /**
     * Get last nomor urut berdasarkan kode surat dan organisasi
     */
    private function getLastNomorUrut($kode, $organisasi = null, $tahun = null)
    {
        $tahun = $tahun ?? date('Y');

        $query = SuratKeluar::where('nomor_surat', 'LIKE', $kode . '/%');

        if ($organisasi) {
            $query->where('jenis_surat', 'LIKE', $organisasi . '%');
        }

        // Filter tahun
        if ($organisasi == 'ipnu') {
            $tahunDuaDigit = substr($tahun, -2);
            $query->where('nomor_surat', 'LIKE', "%/{$tahunDuaDigit}");
        } else {
            $query->where('nomor_surat', 'LIKE', "%/{$tahun}");
        }

        $lastSurat = $query->orderBy('id', 'desc')->first();

        if ($lastSurat && $lastSurat->nomor_surat) {
            $parts = explode('/', $lastSurat->nomor_surat);
            return isset($parts[1]) ? (int)$parts[1] : 0;
        }
        return 0;
    }

    /**
     * Get last nomor surat (alias)
     */
    private function getLastNomorSurat($kode)
    {
        return $this->getLastNomorUrut($kode);
    }

    private function formatTanggalIndonesia($tanggal)
    {
        if (empty($tanggal)) {
            return '';
        }

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
        if (!$timestamp) {
            return $tanggal;
        }

        $hari = date('d', $timestamp);
        $bulanIndex = (int) date('m', $timestamp);
        $tahun = date('Y', $timestamp);

        return $hari . ' ' . $bulan[$bulanIndex] . ' ' . $tahun;
    }

    /**
     * Mendapatkan tanggal Hijriah otomatis dari tanggal Masehi hari ini
     * Format: 26 Rabiul Akhir 1445 H
     */
    private function getTanggalHijriahOtomatis()
    {
        try {
            $hijri = new HijriDateTime(new \DateTime('now'));
            $tanggalHijriah = $hijri->format('_j _F _Y');

            if (empty($tanggalHijriah)) {
                $tanggalHijriah = $hijri->date("_j _F _Y");
            }

            $tanggalHijriah = preg_replace('/\s+/', ' ', $tanggalHijriah);

            // PERBAIKAN 1: Cek apakah hasil library sudah membawa huruf 'H' atau belum
            if (str_ends_with(strtoupper($tanggalHijriah), 'H')) {
                return $tanggalHijriah;
            }
            return $tanggalHijriah . ' H';
        } catch (\Throwable $e) {
            $dateObj = new \DateTime('now');
            $formatter = new \IntlDateFormatter(
                'id_ID@calendar=islamic-umalqura',
                \IntlDateFormatter::LONG,
                \IntlDateFormatter::NONE,
                'Asia/Jakarta',
                \IntlDateFormatter::TRADITIONAL
            );

            $hasilIntl = $formatter->format($dateObj); // Ini biasanya menghasilkan: "7 Zulhijah 1447" (tanpa H)

            // PERBAIKAN 2: Pastikan kondisinya aman, jika belum ada 'H', baru kita tambahkan
            if (!str_ends_with(strtoupper($hasilIntl), 'H')) {
                $hasilIntl = $hasilIntl . ' H';
            }

            return $hasilIntl;
        }
    }


    /**
     * Generate nomor surat otomatis via AJAX
     */
    public function generateNomor(Request $request)
    {
        // Tambahkan header JSON agar browser tahu ini adalah JSON
        try {
            $request->validate([
                'jenis_surat' => 'required|string',
            ]);

            // Ganti dengan logika sesungguhnya
            $nomor = NomorSuratHelper::generateWithCurrentMonth(
                'ipnu', // Pastikan variabel ini terdefinisi
                'PAC',  // Pastikan variabel ini terdefinisi
                $request->jenis_surat,
                'XVI'   // Pastikan variabel ini terdefinisi
            );

            return response()->json(['status' => 'success', 'nomor' => $nomor]);
        } catch (\Exception $e) {
            // Penting: Kembalikan JSON, bukan redirect atau error HTML
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // ========== MAIN METHODS ==========

    /**
     * Daftar template surat
     */
    public function index()
    {
        $templates = SuratTemplate::where('is_active', true)
            ->orderBy('urutan')
            ->get();
        return view('admin.cetak-surat.index', compact('templates'));
    }

    /**
     * Form create surat berdasarkan template
     */
    public function create($id)
    {
        $template = SuratTemplate::findOrFail($id);

        // Ambil data organisasi user yang login
        $organisasi = auth()->user()->organization;

        // Jika user tidak punya organisasi, ambil PAC default
        if (!$organisasi) {
            $organisasi = Organization::where('type', 'pac')->first();
        }

        // Hitung nomor urut terakhir untuk template ini
        $lastNumber = $this->getLastNomorUrut($template->kode, $organisasi->jenis_organisasi ?? 'ipnu');

        // Ambil semua organisasi untuk pilihan (jika super admin)
        $organizations = [];
        if (auth()->user()->hasRole('super_admin')) {
            $organizations = Organization::orderBy('name')->get();
        }

        // Data default untuk fields
        $defaultData = [
            // Data organisasi untuk kop surat
            'kop_organisasi' => $organisasi->nama_organisasi_kop ?? '',
            'tingkat_organisasi' => $organisasi->tingkat_text ?? '',
            'nama_wilayah' => $organisasi->nama_wilayah ?? '',
            'nama_organisasi_lengkap' => $organisasi->nama_organisasi_lengkap ?? '',
            'pembuka_surat' => $organisasi->pembuka_surat ?? '',
            'alamat_organisasi' => $organisasi->alamat ?? '',
            'email_organisasi' => $organisasi->email ?? '',

            // Data ketua
            'nama_ketua' => $organisasi->ketua?->name ?? '',
            'nia_ketua' => $organisasi->ketua?->nik ?? '',

            // Data sekretaris
            'nama_sekretaris' => $organisasi->sekretaris?->name ?? '',
            'nia_sekretaris' => $organisasi->sekretaris?->nik ?? '',

            // Periode dan tahun berdiri
            'periode' => $organisasi->periode ?? 'XVI',
            'tahun_berdiri_ipnu' => '7354',   // DIPERBAIKI
            'tahun_berdiri_ippnu' => '7455',

            // Field desa (untuk SRP)
            'status_desa' => 'DESA',  // Default huruf kapital
            'nama_desa' => '',
            'nama_desa_lower' => '',
            'masa_bhakti' => '',
            // Tanggal
            'tanggal_hijriah' => $this->getTanggalHijriahOtomatis(), // buat method helper
            'tanggal_masehi' => date('Y-m-d'),  // format untuk disimpan
            'tanggal_masehi_formatted' => $this->formatTanggalIndonesia(date('Y-m-d')), // untuk tampilan
            'tingkat_organisasi_upper' => strtoupper($organisasi->tingkat_text),
            'nama_wilayah_upper' => strtoupper($organisasi->nama_wilayah),
            'nama_organisasi_lengkap_baris2' => $organisasi->jenis_organisasi == 'ipnu' ? 'IKATAN PELAJAR NAHDLATUL ULAMA' : ($organisasi->jenis_organisasi == 'ippnu' ? 'IKATAN PELAJAR PUTRI NAHDLATUL ULAMA' : 'IKATAN PELAJAR NAHDLATUL ULAMA - IPPNU'),
            'jenis_organisasi_upper' => $organisasi->jenis_organisasi == 'ipnu' ? 'IPNU' : ($organisasi->jenis_organisasi == 'ippnu' ? 'IPPNU' : 'IPNU - IPPNU'),
        ];

        return view('admin.cetak-surat.create', compact(
            'template',
            'lastNumber',
            'organisasi',
            'organizations',
            'defaultData'
        ));
    }

    /**
     * Simpan surat yang sudah diisi
     */
    public function store(Request $request, $id)
    {
        $template = \App\Models\SuratTemplate::findOrFail($id);
        $user = auth()->user();

        // 1. Tangkap isi surat yang sudah diedit via editor preview jika ada (misal nama inputnya 'edited_content')
        // Jika tidak lewat preview editor, gunakan isi asli template untuk diproses
        $isiSurat = $request->input('edited_content', $template->konten ?? $template->isi_surat);

        // Ambil organisasi user
        $org = auth()->user()->organization;

        // Jika user tidak punya organisasi, cari PAC default
        if (!$org) {
            $org = Organization::where('type', 'pac')->first();
        }

        // Jika super admin pilih override
        if (!$user->isWakil() && !$user->hasRole('super_admin')) {
            return back()->with('error', 'Hanya Wakil yang dapat mengajukan surat ini');
        }

        // Cek pasangan wakil untuk validasi silang
        $partnerWakil = $user->getPartnerWakil();

        if ($partnerWakil) {
            \Log::info('Partner ditemukan: ' . $partnerWakil->name . ' (ID: ' . $partnerWakil->id . ')');
        } else {
            \Log::info('Partner tidak ditemukan');
        }

        // 2. Logika override organisasi untuk super_admin
        if ($user->hasRole('super_admin') && $request->filled('organization_override')) {
            $overrideOrg = Organization::find($request->organization_override);
            if ($overrideOrg) {
                $org = $overrideOrg;
            }
        }
        // ==========================================
        // FIX TAMBAHAN: Menerjemahkan Variabel Organisasi Otomatis
        // ==========================================
        $jenisOrg = strtolower($org->jenis_organisasi ?? 'ipnu'); // 'ipnu' atau 'ippnu'

        // 1. Mengganti {jenis_organisasi_upper} -> IPNU / IPPNU
        $jenisOrgUpper = strtoupper($jenisOrg);
        $isiSurat = str_replace('{jenis_organisasi_upper}', $jenisOrgUpper, $isiSurat);

        // 2. Mengganti {nama_organisasi_lower} -> Ikatan Pelajar Nahdlatul Ulama'
        $namaOrgLower = ($jenisOrg == 'ipnu')
            ? "Ikatan Pelajar Nahdlatul Ulama'"
            : "Ikatan Pelajar Putri Nahdlatul Ulama'";
        $isiSurat = str_replace('{nama_organisasi_lower}', $namaOrgLower, $isiSurat);

        // 3. Mengganti {nama_organisasi_lengkap_baris2} jika ada yang nyelip di ttd / tembusan
        $namaOrgLengkapBaris2 = ($jenisOrg == 'ipnu')
            ? "IKATAN PELAJAR NAHDLATUL ULAMA"
            : "IKATAN PELAJAR PUTRI NAHDLATUL ULAMA";
        $isiSurat = str_replace('{nama_organisasi_lengkap_baris2}', $namaOrgLengkapBaris2, $isiSurat);
        // ==========================================

        $organisasi = $org->jenis_organisasi ?? 'bersama';
        $tingkat = strtoupper($org->type ?? 'pac');
        $periode = strtoupper($org->periode ?? 'XVII');

        // Generate nomor surat
        $nomorSurat = $request->nomor_surat;
        if (empty($nomorSurat)) {
            $kodeIndeks = $template->kode;
            $bulan = NomorSuratHelper::bulanToRomawi(date('n'));
            $tahun = date('Y');

            if ($organisasi == 'ipnu') {
                $tahun = date('y');
            }

            $nomorSurat = NomorSuratHelper::generate(
                $organisasi,
                $tingkat,
                $kodeIndeks,
                $periode,
                $bulan,
                $tahun
            );
        }

        $fields = $template->fields ?? [];

        // Validasi dinamis
        $rules = [];
        foreach ($fields as $field => $type) {
            $rules["fields.$field"] = 'nullable|string';
        }
        $rules['nomor_surat'] = 'required|string';
        $request->validate($rules);

        $dataSurat = $request->input('fields', []);

        // 2. PROSES REPLACING (Hanya berjalan jika isinya masih menggunakan template mentah)
        if (!$request->has('edited_content')) {
            foreach ($dataSurat as $key => $value) {
                $isiSurat = str_replace('{' . $key . '}', $value, $isiSurat);
            }

            // Jaga-jaga jika ada format tanggal khusus yang belum ter-replace otomatis
            if (isset($dataSurat['surat_ranting_tanggal'])) {
                $tanggalFormatted = \Carbon\Carbon::parse($dataSurat['surat_ranting_tanggal'])->translatedFormat('d F Y');
                $isiSurat = str_replace('{surat_ranting_tanggal_formatted}', $tanggalFormatted, $isiSurat);
            }
            if (isset($dataSurat['tanggal_masehi'])) {
                $tanggalMasehiFormatted = \Carbon\Carbon::parse($dataSurat['tanggal_masehi'])->translatedFormat('d F Y');
                $isiSurat = str_replace('{tanggal_masehi_formatted}', $tanggalMasehiFormatted, $isiSurat);
            }

            // Jika ada field nama_ketua/nama_sekretaris dari organisasi, pastikan terisi
            if (empty($dataSurat['nama_ketua']) && $org->ketua) {
                $isiSurat = str_replace('{nama_ketua}', $org->ketua->name, $isiSurat);
                $dataSurat['nama_ketua'] = $org->ketua->name;
            }
            if (empty($dataSurat['nama_sekretaris']) && $org->sekretaris) {
                $isiSurat = str_replace('{nama_sekretaris}', $org->sekretaris->name, $isiSurat);
                $dataSurat['nama_sekretaris'] = $org->sekretaris->name;
            }
        }

        if (empty($dataSurat['tanggal_masehi'])) {
            $dataSurat['tanggal_masehi'] = date('Y-m-d');
        }
        // Upload file lampiran
        $lampiranPath = null;
        if ($request->hasFile('file_lampiran')) {
            $file = $request->file('file_lampiran');
            $filename = 'lampiran_' . time() . '_' . $file->getClientOriginalName();
            $lampiranPath = $file->storeAs('surat/lampiran', $filename, 'public');
        }

        // Ambil perihal & tujuan default jika tidak ada di fields
        $perihal = $dataSurat['perihal'] ?? $template->nama;
        $tujuan = $dataSurat['tujuan'] ?? '-';

        // Logika otomatis tipe surat berdasarkan data master template
        $jenisSuratOtomatis = $template->jenis_surat ?? $template->jenis ?? 'biasa';
        $jenisSuratOtomatis = strtolower(trim($jenisSuratOtomatis));
        $jenisOrg = strtolower($org->jenis_organisasi ?? 'ipnu');
        $isiSurat = str_replace('{jenis_organisasi_upper}', strtoupper($jenisOrg), $isiSurat);
        $namaOrgLower = ($jenisOrg == 'ipnu') ? "Ikatan Pelajar Nahdlatul Ulama'" : "Ikatan Pelajar Putri Nahdlatul Ulama'";
        $isiSurat = str_replace('{nama_organisasi_lower}', $namaOrgLower, $isiSurat);
        $namaOrgLengkapBaris2 = ($jenisOrg == 'ipnu') ? "IKATAN PELAJAR NAHDLATUL ULAMA" : "IKATAN PELAJAR PUTRI NAHDLATUL ULAMA";
        $isiSurat = str_replace('{nama_organisasi_lengkap_baris2}', $namaOrgLengkapBaris2, $isiSurat);

        // 5. KUNCI UTAMA: Terjemahkan nomor surat yang baru saja di-generate!
        $isiSurat = str_replace('{nomor_surat}', $nomorSurat, $isiSurat);
        // 3. Eksekusi Simpan Permanen ke Database
        $surat = SuratKeluar::create([
            'organization_id' => $org->id,
            'template_id'     => $template->id,
            'jenis_surat'     => $jenisSuratOtomatis,
            'nomor_surat'     => $nomorSurat,
            'perihal'         => $perihal,
            'tujuan'          => $tujuan,
            'isi_surat'       => $isiSurat, // Menyimpan teks HTML matang hasil olahan
            'file_lampiran'   => $lampiranPath, // simpan path file
            'data_surat'      => $dataSurat,
            // PERBAIKAN: Kembalikan ke 'draft' agar user bisa klik tombol "Ajukan Validasi" sendiri
            'status_validasi' => 'draft',
            'diajukan_oleh'   => null,    // Kosongkan dulu
            'divalidasi_oleh' => null,    // Kosongkan dulu (dipilih via dropdown nanti)

            'status'          => 'draft',
            'created_by'      => $user->id,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Surat berhasil disimpan!'
            ]);
        }
        // 4. PERBAIKAN ALUR ALIH HALAMAN:
        // Alihkan langsung ke halaman detail surat yang baru saja disimpan agar user bisa melihat hasilnya
        return redirect()->route('surat.keluar.show', $surat)
            ->with('success', 'Surat diajukan. Menunggu validasi dari ' . ($partnerWakil ? $partnerWakil->name : 'Wakil lain'));
    }

    /**
     * Preview surat sebelum cetak
     */
    public function previewSurat(Request $request)
    {
        // 1. Ambil data utama dari POST
        $templateId = $request->template_id;
        $nomorSurat = $request->nomor_surat;
        $dataSurat = $request->input('fields', []);

        // 2. Ambil organisasi dari user yang login
        $organisasi = auth()->user()->organization;
        if (!$organisasi) {
            $organisasi = Organization::where('type', 'pac')->first();
        }

        // 3. Set data organisasi bawaan (Tetap seperti kode aslimu)
        if ($organisasi->jenis_organisasi == 'ipnu') {
            $namaOrganisasiBaris2 = "IKATAN PELAJAR NAHDLATUL ULAMA";
            $jenisOrganisasiUpper = 'IPNU';
        } elseif ($organisasi->jenis_organisasi == 'ippnu') {
            $namaOrganisasiBaris2 = "IKATAN PELAJAR PUTRI NAHDLATUL ULAMA";
            $jenisOrganisasiUpper = 'IPPNU';
        } else {
            $namaOrganisasiBaris2 = "IKATAN PELAJAR NAHDLATUL ULAMA - IPPNU";
            $jenisOrganisasiUpper = 'IPNU - IPPNU';
        }

        if ($organisasi->jenis_organisasi == 'ipnu') {
            $dataSurat['nama_organisasi_lower'] = "Ikatan Pelajar Nahdlatul Ulama'";
        } elseif ($organisasi->jenis_organisasi == 'ippnu') {
            $dataSurat['nama_organisasi_lower'] = "Ikatan Pelajar Putri Nahdlatul Ulama'";
        } else {
            $dataSurat['nama_organisasi_lower'] = "Ikatan Pelajar Nahdlatul Ulama' - IPPNU";
        }

        $dataSurat['tingkat_organisasi'] = $organisasi->tingkat_text;
        $dataSurat['tingkat_organisasi_upper'] = strtoupper($organisasi->tingkat_text);
        $dataSurat['nama_wilayah'] = $organisasi->nama_wilayah;
        $dataSurat['nama_wilayah_upper'] = strtoupper($organisasi->nama_wilayah);
        $dataSurat['alamat_organisasi'] = $organisasi->alamat ?? '';
        $dataSurat['email_organisasi'] = $organisasi->email ?? '';
        $dataSurat['kop_organisasi'] = $organisasi->nama_organisasi_kop;
        $dataSurat['nama_organisasi_lengkap_baris2'] = $namaOrganisasiBaris2;
        $dataSurat['nama_organisasi_lengkap'] = $organisasi->nama_organisasi_lengkap;
        $dataSurat['pembuka_surat'] = $organisasi->pembuka_surat;
        $dataSurat['nama_ketua'] = $organisasi->ketua?->name ?? '';
        $dataSurat['nia_ketua'] = $organisasi->ketua?->nik ?? '';
        $dataSurat['nama_sekretaris'] = $organisasi->sekretaris?->name ?? '';
        $dataSurat['nia_sekretaris'] = $organisasi->sekretaris?->nik ?? '';
        $dataSurat['jenis_organisasi_upper'] = $jenisOrganisasiUpper;

        // 4. PERBAIKAN: Otomatisasi Penjagaan Variabel Lower Case
        // Jika user menginput 'status_desa' atau 'nama_desa', otomatis buat versi lower-nya di sini
        if (isset($dataSurat['status_desa'])) {
            // ucwords(strtolower(...)) akan mengubah "desa" atau "DESA" menjadi "Desa"
            $dataSurat['status_desa_lower'] = ucwords(strtolower($dataSurat['status_desa']));
        }

        if (isset($dataSurat['nama_desa'])) {
            // ucwords(strtolower(...)) akan mengubah "SUKOREJO" atau "sukorejo" menjadi "Sukorejo"
            $dataSurat['nama_desa_lower'] = ucwords(strtolower($dataSurat['nama_desa']));
        }

        // 5. Standarisasi Format Tanggal Masehi & Ranting
        $tanggalMasehiTarget = $dataSurat['tanggal_masehi'] ?? $dataSurat['tanggal_penetapan'] ?? date('Y-m-d');
        if (empty($dataSurat['tanggal_masehi'])) {
            $dataSurat['tanggal_masehi'] = $tanggalMasehiTarget;
        }
        $dataSurat['tanggal_masehi_formatted'] = $this->formatTanggalIndonesia($dataSurat['tanggal_masehi']);

        if (isset($dataSurat['surat_ranting_tanggal']) && !empty($dataSurat['surat_ranting_tanggal'])) {
            $dataSurat['surat_ranting_tanggal_formatted'] = $this->formatTanggalIndonesia($dataSurat['surat_ranting_tanggal']);
        }

        // 6. Pengaman Tanggal Hijriah (Bebas dari double H H)
        if (empty($dataSurat['tanggal_hijriah'])) {
            $dataSurat['tanggal_hijriah'] = $this->getTanggalHijriahOtomatis();
        }

        // Sinkronisasi rujukan data teks final masehi untuk template
        $dataSurat['tanggal_masehi'] = $dataSurat['tanggal_masehi_formatted'] . ' M';

        // 7. Ambil Template Surat murni dari DB
        $template = SuratTemplate::findOrFail($templateId);
        $isiSurat = $template->konten;

        // 8. PERBAIKAN UTAMA: Logika Pengganti Kata Bersih & Terstruktur
        // Ganti nomor surat dan rujukan tanggal surat ranting terlebih dahulu
        $isiSurat = str_replace('{nomor_surat}', $nomorSurat, $isiSurat);
        if (isset($dataSurat['surat_ranting_tanggal_formatted'])) {
            $isiSurat = str_replace('{surat_ranting_tanggal}', $dataSurat['surat_ranting_tanggal_formatted'], $isiSurat);
        }

        // Lakukan loop replacement linier (satu jalur lurus) menggunakan variabel penampung tunggal
        foreach ($dataSurat as $field => $value) {
            if (is_string($value) || is_numeric($value)) {
                $isiSurat = str_replace('{' . $field . '}', $value, $isiSurat);
            }
        }

        // Kirim ke view blade preview
        return view('admin.cetak-surat.preview', compact('isiSurat', 'template', 'dataSurat', 'nomorSurat'));
    }


    public function savePreview(Request $request)
    {
        // Gunakan try-catch untuk menangkap error
        try {
            $template = \App\Models\SuratTemplate::findOrFail($request->template_id);

            $surat = new \App\Models\SuratKeluar();
            $surat->template_id     = $request->template_id;
            $surat->nomor_surat     = $request->nomor_surat;
            $surat->isi_surat       = $request->edited_content; // Pastikan 'edited_content' dikirim dari JS
            $surat->organization_id = auth()->user()->organization_id;
            $surat->created_by      = auth()->id();
            $surat->data_surat      = $request->input('fields', []);
            $surat->jenis_surat     = strtolower(trim($template->jenis_surat ?? 'biasa'));
            $surat->status          = 'selesai';
            $surat->perihal         = $request->input('perihal', $template->nama);
            $surat->tujuan          = $request->input('tujuan', '-');

            $surat->save();

            // RETURN JSON, BUKAN REDIRECT
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            \Log::error('Error savePreview: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Download surat sebagai PDF
     */
    public function download(SuratKeluar $surat)
    {
        // === 1. PROSES GAMBAR TTD DAN STEMPEL (DEFINISIKAN DULU) ===
        $org = $surat->organization;
        $isiSuratHtml = $surat->isi_surat;

        // TTD Ketua
        if ($surat->status_validasi == 'selesai' && $org && $org->ttd_ketua) {
            $imgPath = public_path('storage/' . $org->ttd_ketua);
            $ttdKetuaHtml = '<img src="' . $this->convertToBase64($imgPath) . '" style="max-height: 60px;">';
        } else {
            $ttdKetuaHtml = '<br><br>'; // Beri jarak jika kosong
        }

        // TTD Sekretaris
        if (in_array($surat->status_validasi, ['menunggu_ttd_ketua', 'selesai']) && $org && $org->ttd_sekretaris) {
            $imgPath = public_path('storage/' . $org->ttd_sekretaris);
            $ttdSekretarisHtml = '<img src="' . $this->convertToBase64($imgPath) . '" style="max-height: 60px;">';
        } else {
            $ttdSekretarisHtml = '<br><br>';
        }

        // Stempel
        if ($surat->status_validasi == 'selesai' && $org && $org->stempel) {
            $imgPath = public_path('storage/' . $org->stempel);
            $stempelHtml = '<img src="' . $this->convertToBase64($imgPath) . '" style="max-height: 85px;">';
        } else {
            $stempelHtml = '';
        }

        // === 2. BARU LAKUKAN REPLACE PLACEHOLDER ===
        // DEBUG: Tampilkan hasilnya ke layar untuk melihat apakah ada tag <img>
        $isiSuratHtml = str_replace(['{ttd_ketua}', '{ttd_sekretaris}', '{stempel}'], [$ttdKetuaHtml, $ttdSekretarisHtml, $stempelHtml], $isiSuratHtml);

        dd($isiSuratHtml); // <--- TAMBAHKAN INI UNTUK MELIHAT HASILNYA SEBELUM KE PDF

        // Gunakan clone agar tidak mengubah objek $surat di database/halaman lain
    }

    private function getTanggalHijriah()
    {
        // Sederhana: ambil dari setting atau buat manual
        // Bisa juga pakai library seperti hijri-date
        $bulanHijriah = [
            1 => 'Muharram',
            2 => 'Safar',
            3 => 'Rabiul Awal',
            4 => 'Rabiul Akhir',
            5 => 'Jumadil Awal',
            6 => 'Jumadil Akhir',
            7 => 'Rajab',
            8 => 'Sya\'ban',
            9 => 'Ramadhan',
            10 => 'Syawal',
            11 => 'Dzulqa\'dah',
            12 => 'Dzulhijjah'
        ];

        // Perhitungan sederhana (tidak akurat, untuk contoh)
        $tahunHijriah = date('Y') - 579;
        $bulan = date('n');
        $tanggal = date('j');

        return "{$tanggal} {$bulanHijriah[$bulan]} {$tahunHijriah} H";
    }

    private function convertToBase64($path)
    {
        // Cek apakah file ada secara fisik
        if (!file_exists($path)) return '';

        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }
}
