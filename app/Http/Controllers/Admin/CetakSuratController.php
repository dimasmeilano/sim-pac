<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\NomorSuratHelper;
use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\SuratTemplate;
use App\Models\SuratKeluar;
use App\Services\SuratService; // <-- [BARU] Import Service yang baru dibuat
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

    private function getLastNomorUrut($kode, $organisasi = null, $tahun = null)
    {
        $tahun = $tahun ?? date('Y');
        $query = SuratKeluar::where('nomor_surat', 'LIKE', $kode . '/%');

        if ($organisasi) {
            $query->where('jenis_surat', 'LIKE', $organisasi . '%');
        }

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

    private function getLastNomorSurat($kode)
    {
        return $this->getLastNomorUrut($kode);
    }

    private function formatTanggalIndonesia($tanggal)
    {
        if (empty($tanggal)) return '';

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
        if (!$timestamp) return $tanggal;

        return date('d', $timestamp) . ' ' . $bulan[(int) date('m', $timestamp)] . ' ' . date('Y', $timestamp);
    }

    private function getTanggalHijriahOtomatis()
    {
        try {
            $hijri = new HijriDateTime(new \DateTime('now'));
            $tanggalHijriah = $hijri->format('_j _F _Y');

            if (empty($tanggalHijriah)) {
                $tanggalHijriah = $hijri->date("_j _F _Y");
            }
            $tanggalHijriah = preg_replace('/\s+/', ' ', $tanggalHijriah);

            if (str_ends_with(strtoupper($tanggalHijriah), 'H')) return $tanggalHijriah;
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

            $hasilIntl = $formatter->format($dateObj);
            if (!str_ends_with(strtoupper($hasilIntl), 'H')) {
                $hasilIntl = $hasilIntl . ' H';
            }
            return $hasilIntl;
        }
    }

    public function generateNomor(Request $request)
    {
        try {
            $request->validate(['jenis_surat' => 'required|string']);
            $nomor = NomorSuratHelper::generateWithCurrentMonth(
                'ipnu',
                'PAC',
                $request->jenis_surat,
                'XVI'
            );
            return response()->json(['status' => 'success', 'nomor' => $nomor]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // ========== MAIN METHODS ==========

    public function index()
    {
        $templates = SuratTemplate::where('jenis_surat', '!=', 'umum')->get();
        return view('admin.cetak-surat.index', compact('templates'));
    }

    public function create($id)
    {
        $template = SuratTemplate::findOrFail($id);
        $organisasi = auth()->user()->organization ?? Organization::where('type', 'pac')->first();
        $lastNumber = $this->getLastNomorUrut($template->kode, $organisasi->jenis_organisasi ?? 'ipnu');
        $organizations = auth()->user()->hasRole('super_admin') ? Organization::orderBy('name')->get() : [];

        $defaultData = [
            'kop_organisasi' => $organisasi->nama_organisasi_kop ?? '',
            'tingkat_organisasi' => $organisasi->tingkat_text ?? '',
            'nama_wilayah' => $organisasi->nama_wilayah ?? '',
            'nama_organisasi_lengkap' => $organisasi->nama_organisasi_lengkap ?? '',
            'pembuka_surat' => $organisasi->pembuka_surat ?? '',
            'alamat_organisasi' => $organisasi->alamat ?? '',
            'email_organisasi' => $organisasi->email ?? '',
            'nama_ketua' => $organisasi->ketua?->name ?? '',
            'nia_ketua' => $organisasi->ketua?->nik ?? '',
            'nama_sekretaris' => $organisasi->sekretaris?->name ?? '',
            'nia_sekretaris' => $organisasi->sekretaris?->nik ?? '',
            'periode' => $organisasi->periode ?? 'XVI',
            'tahun_berdiri_ipnu' => '7354',
            'tahun_berdiri_ippnu' => '7455',
            'status_desa' => 'DESA',
            'nama_desa' => '',
            'nama_desa_lower' => '',
            'masa_bhakti' => '',
            'tanggal_hijriah' => $this->getTanggalHijriahOtomatis(),
            'tanggal_masehi' => date('Y-m-d'),
            'tanggal_masehi_formatted' => $this->formatTanggalIndonesia(date('Y-m-d')),
            'tingkat_organisasi_upper' => strtoupper($organisasi->tingkat_text),
            'nama_wilayah_upper' => strtoupper($organisasi->nama_wilayah),
            'nama_organisasi_lengkap_baris2' => $organisasi->jenis_organisasi == 'ipnu' ? 'IKATAN PELAJAR NAHDLATUL ULAMA' : ($organisasi->jenis_organisasi == 'ippnu' ? 'IKATAN PELAJAR PUTRI NAHDLATUL ULAMA' : 'IKATAN PELAJAR NAHDLATUL ULAMA - IPPNU'),
            'jenis_organisasi_upper' => $organisasi->jenis_organisasi == 'ipnu' ? 'IPNU' : ($organisasi->jenis_organisasi == 'ippnu' ? 'IPPNU' : 'IPNU - IPPNU'),
        ];

        return view('admin.cetak-surat.create', compact('template', 'lastNumber', 'organisasi', 'organizations', 'defaultData'));
    }

    public function store(Request $request, $id)
    {
        $template = \App\Models\SuratTemplate::findOrFail($id);
        $user = auth()->user();
        $org = $user->organization ?? \App\Models\Organization::where('type', 'pac')->first();

        // 1. Validasi Hak Akses (Sesuai Aturan Anda)
        if (!$user->isWakil() && !$user->hasRole('super_admin')) {
            return back()->with('error', 'Hanya Wakil yang dapat mengajukan surat ini');
        }

        if ($user->hasRole('super_admin') && $request->filled('organization_override')) {
            $overrideOrg = \App\Models\Organization::find($request->organization_override);
            if ($overrideOrg) $org = $overrideOrg;
        }

        // 2. Generator Nomor Surat Otomatis
        $organisasi = strtolower($org->jenis_organisasi ?? 'bersama');
        $tingkat = strtoupper($org->type ?? 'pac');
        $periode = strtoupper($org->periode ?? 'XVII');

        $nomorSurat = $request->nomor_surat;
        if (empty($nomorSurat)) {
            // Asumsi class NomorSuratHelper sudah di-use di atas
            $nomorSurat = NomorSuratHelper::generate(
                $organisasi,
                $tingkat,
                $template->kode,
                $periode,
                NomorSuratHelper::bulanToRomawi(date('n')),
                ($organisasi == 'ipnu') ? date('y') : date('Y')
            );
        }

        // 3. Validasi Form Dinamis
        $fields = $template->fields ?? [];
        if (is_string($fields)) {
            $fields = json_decode($fields, true) ?? [];
        }

        $rules = ['nomor_surat' => 'required|string'];
        foreach ($fields as $field => $type) {
            $rules["fields.$field"] = 'nullable|string';
        }
        $request->validate($rules);

        // 4. Persiapan Data untuk Service
        $dataSurat = $request->input('fields', []);

        // Tetapkan tanggal surat (dari form, atau hari ini jika kosong)
        $tanggalSurat = $dataSurat['tanggal_masehi'] ?? date('Y-m-d');
        if (empty($dataSurat['tanggal_masehi'])) {
            $dataSurat['tanggal_masehi'] = $tanggalSurat;
        }

        // Ambil isi mentah (Jika ada hasil edit dari TinyMCE, gunakan itu. Jika tidak, gunakan template asli)
        $isiSuratMentah = $request->input('edited_content', $template->konten ?? $template->isi_surat);

        // ====================================================================
        // 5. PANGGIL MESIN CERDAS (SuratService)
        // ====================================================================
        $suratService = new \App\Services\SuratService();
        // Cukup lemparkan 5 datanya, Service yang akan mengurus semua str_replace dan tanggal Hijriahnya!
        $isiSuratFinal = $suratService->renderIsiSurat(
            $nomorSurat,
            $org,
            $isiSuratMentah,
            $dataSurat,
            $tanggalSurat
        );

        // 6. Upload Lampiran Fisik (Jika ada)
        $lampiranPath = null;
        if ($request->hasFile('file_lampiran')) {
            $file = $request->file('file_lampiran');
            $lampiranPath = $file->storeAs('surat/lampiran', 'lampiran_' . time() . '_' . $file->getClientOriginalName(), 'public');
        }

        // 7. Simpan Ke Database
        $perihal = $dataSurat['perihal'] ?? $template->nama;
        $tujuan = $dataSurat['tujuan'] ?? '-';
        $jenisSuratOtomatis = strtolower(trim($template->jenis_surat ?? $template->jenis ?? 'biasa'));

        $surat = \App\Models\SuratKeluar::create([
            'organization_id' => $org->id,
            'template_id'     => $template->id,
            'jenis_surat'     => $jenisSuratOtomatis,
            'nomor_surat'     => $nomorSurat,
            'perihal'         => $perihal,
            'tujuan'          => $tujuan,
            'isi_surat'       => $isiSuratFinal, // Gunakan hasil render final dari service
            'file_lampiran'   => $lampiranPath,
            'data_surat'      => $dataSurat,     // JSON disimpan utuh untuk Edit
            'status_validasi' => 'draft',
            'diajukan_oleh'   => null,
            'divalidasi_oleh' => null,
            'status'          => 'draft',
            'created_by'      => $user->id,
        ]);

        // 8. Respon Sukses
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Surat berhasil disimpan!']);
        }

        return redirect()->route('surat.keluar.show', $surat)
            ->with('success', 'Draft surat berhasil disimpan! Silakan periksa kembali detailnya, lalu pilih Wakil dan klik "Ajukan Validasi" pada panel di sebelah kanan.');
    }

    public function previewSurat(Request $request)
    {
        try {
            $templateId = $request->template_id;
            $nomorSurat = $request->nomor_surat ?? '[Nomor Belum Di-generate]';
            $dataSurat = $request->input('fields', []);

            // Ambil Organisasi
            $organisasi = auth()->user()->organization ?? \App\Models\Organization::where('type', 'pac')->first();

            // Keamanan jika organisasi tidak ditemukan
            if (!$organisasi) {
                throw new \Exception("Data Organisasi tidak ditemukan untuk user ini.");
            }

            if ($organisasi->jenis_organisasi == 'ipnu') {
                $namaOrganisasiBaris2 = "IKATAN PELAJAR NAHDLATUL ULAMA";
                $jenisOrganisasiUpper = 'IPNU';
                $dataSurat['nama_organisasi_lower'] = "Ikatan Pelajar Nahdlatul Ulama'";
            } elseif ($organisasi->jenis_organisasi == 'ippnu') {
                $namaOrganisasiBaris2 = "IKATAN PELAJAR PUTRI NAHDLATUL ULAMA";
                $jenisOrganisasiUpper = 'IPPNU';
                $dataSurat['nama_organisasi_lower'] = "Ikatan Pelajar Putri Nahdlatul Ulama'";
            } else {
                $namaOrganisasiBaris2 = "IKATAN PELAJAR NAHDLATUL ULAMA - IPPNU";
                $jenisOrganisasiUpper = 'IPNU - IPPNU';
                $dataSurat['nama_organisasi_lower'] = "Ikatan Pelajar Nahdlatul Ulama' - IPPNU";
            }

            $dataSurat['tingkat_organisasi'] = $organisasi->tingkat_text ?? '';
            $dataSurat['tingkat_organisasi_upper'] = strtoupper($organisasi->tingkat_text ?? '');
            $dataSurat['nama_wilayah'] = $organisasi->nama_wilayah ?? '';
            $dataSurat['nama_wilayah_upper'] = strtoupper($organisasi->nama_wilayah ?? '');
            $dataSurat['alamat_organisasi'] = $organisasi->alamat ?? '';
            $dataSurat['email_organisasi'] = $organisasi->email ?? '';
            $dataSurat['kop_organisasi'] = $organisasi->nama_organisasi_kop ?? '';
            $dataSurat['nama_organisasi_lengkap_baris2'] = $namaOrganisasiBaris2;
            $dataSurat['nama_organisasi_lengkap'] = $organisasi->nama_organisasi_lengkap ?? '';
            $dataSurat['pembuka_surat'] = $organisasi->pembuka_surat ?? '';
            $dataSurat['nama_ketua'] = $organisasi->ketua?->name ?? '';
            $dataSurat['nia_ketua'] = $organisasi->ketua?->nik ?? '';
            $dataSurat['nama_sekretaris'] = $organisasi->sekretaris?->name ?? '';
            $dataSurat['nia_sekretaris'] = $organisasi->sekretaris?->nik ?? '';
            $dataSurat['jenis_organisasi_upper'] = $jenisOrganisasiUpper;

            if (isset($dataSurat['status_desa'])) $dataSurat['status_desa_lower'] = ucwords(strtolower($dataSurat['status_desa']));
            if (isset($dataSurat['nama_desa'])) $dataSurat['nama_desa_lower'] = ucwords(strtolower($dataSurat['nama_desa']));

            $tanggalMasehiTarget = $dataSurat['tanggal_masehi'] ?? $dataSurat['tanggal_penetapan'] ?? date('Y-m-d');
            if (empty($dataSurat['tanggal_masehi'])) $dataSurat['tanggal_masehi'] = $tanggalMasehiTarget;

            // PERBAIKAN 1: Panggil formatTanggalIndonesia melalui Helper
            $dataSurat['tanggal_masehi_formatted'] = \App\Helpers\NomorSuratHelper::formatTanggalIndonesia($dataSurat['tanggal_masehi']);

            if (isset($dataSurat['surat_ranting_tanggal']) && !empty($dataSurat['surat_ranting_tanggal'])) {
                // PERBAIKAN 2: Panggil Helper
                $dataSurat['surat_ranting_tanggal_formatted'] = \App\Helpers\NomorSuratHelper::formatTanggalIndonesia($dataSurat['surat_ranting_tanggal']);
            }

            // PERBAIKAN 3: Cek apakah fungsi ada, jika tidak isi default
            if (empty($dataSurat['tanggal_hijriah'])) {
                $dataSurat['tanggal_hijriah'] = method_exists($this, 'getTanggalHijriahOtomatis')
                    ? $this->getTanggalHijriahOtomatis()
                    : '....... Hijriah';
            }

            $dataSurat['tanggal_masehi'] = $dataSurat['tanggal_masehi_formatted'] . ' M';

            // Ambil Template
            $template = \App\Models\SuratTemplate::findOrFail($templateId);
            $isiSurat = $template->konten;

            // Eksekusi Replace Teks
            $isiSurat = str_replace('{nomor_surat}', $nomorSurat, $isiSurat);
            if (isset($dataSurat['surat_ranting_tanggal_formatted'])) {
                $isiSurat = str_replace('{surat_ranting_tanggal}', $dataSurat['surat_ranting_tanggal_formatted'], $isiSurat);
            }

            foreach ($dataSurat as $field => $value) {
                if (is_string($value) || is_numeric($value)) {
                    $isiSurat = str_replace('{' . $field . '}', $value, $isiSurat);
                }
            }

            return view('admin.cetak-surat.preview', compact('isiSurat', 'template', 'dataSurat', 'nomorSurat'));
        } catch (\Exception $e) {
            // Jika ada error, kirim ke browser agar terlihat letak salahnya (bukan hanya error 500 blank)
            return response("Terdapat Error di Server: <br><b>" . $e->getMessage() . "</b><br>Pada file: " . $e->getFile() . " baris " . $e->getLine(), 500);
        }
    }

    public function savePreview(Request $request)
    {
        try {
            $template = \App\Models\SuratTemplate::findOrFail($request->template_id);
            $surat = new \App\Models\SuratKeluar();
            $surat->template_id     = $request->template_id;
            $surat->nomor_surat     = $request->nomor_surat;
            $surat->isi_surat       = $request->edited_content;
            $surat->organization_id = auth()->user()->organization_id;
            $surat->created_by      = auth()->id();
            $surat->data_surat      = $request->input('fields', []);
            $surat->jenis_surat     = strtolower(trim($template->jenis_surat ?? 'biasa'));
            $surat->status          = 'selesai';
            $surat->perihal         = $request->input('perihal', $template->nama);
            $surat->tujuan          = $request->input('tujuan', '-');
            $surat->save();

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
