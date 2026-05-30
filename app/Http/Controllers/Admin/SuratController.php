<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\NomorSuratHelper;
use App\Http\Controllers\Controller;
use App\Models\SuratKeluar;
use App\Models\SuratMasuk;
use App\Models\SuratTemplate;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use biladina\hijridatetime\HijriDateTime;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Spatie\Activitylog\Models\Activity;

class SuratController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['verifikasi', 'formLacak']);
        $this->middleware('permission:manage_surat')->except(['verifikasi', 'formLacak']);
    }

    /**
     * Konversi Tanggal Masehi ke Hijriah menggunakan Library/IntlFormatter
     */
    private function getTanggalHijriahOtomatis($tanggalMasehi = null)
    {
        // Gunakan tanggal yang dikirim, atau hari ini jika kosong
        $dateObj = $tanggalMasehi ? new \DateTime($tanggalMasehi) : new \DateTime('now');

        try {
            // Mencoba menggunakan library HijriDateTime
            $hijri = new HijriDateTime($dateObj); // Pastikan namespace-nya sesuai dengan aplikasi Anda
            $tanggalHijriah = $hijri->format('_j _F _Y');

            if (empty($tanggalHijriah)) {
                $tanggalHijriah = $hijri->date("_j _F _Y");
            }
            $tanggalHijriah = preg_replace('/\s+/', ' ', $tanggalHijriah);

            if (str_ends_with(strtoupper($tanggalHijriah), 'H')) return $tanggalHijriah;
            return $tanggalHijriah . ' H';
        } catch (\Throwable $e) {
            // FALLBACK: Menggunakan bawaan PHP jika library gagal/tidak ditemukan
            $formatter = new \IntlDateFormatter(
                'id_ID@calendar=islamic-umalqura',
                \IntlDateFormatter::LONG,
                \IntlDateFormatter::NONE,
                'Asia/Jakarta',
                \IntlDateFormatter::TRADITIONAL
            );

            $hasilIntl = $formatter->format($dateObj);

            // PHP Intl kadang menambahkan kata "AH", kita bersihkan dulu
            $hasilIntl = str_replace(' AH', '', $hasilIntl);

            if (!str_ends_with(strtoupper($hasilIntl), 'H')) {
                $hasilIntl = $hasilIntl . ' H';
            }
            return $hasilIntl;
        }
    }

    // ==========================================
    // BAGIAN 1: SURAT KELUAR (CRUD)
    // ==========================================

    public function keluarIndex()
    {
        $surat = SuratKeluar::with('organization', 'creator', 'signer')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('admin.surat.keluar.index', compact('surat'));
    }

    public function keluarCreate()
    {
        $organization = auth()->user()->organization;
        $tingkat = NomorSuratHelper::getTingkatFromType($organization->type ?? 'pac');
        $periode = NomorSuratHelper::getPeriodeFromOrganization($organization->id ?? null);
        $jenisOrg = $organization->jenis_organisasi ?? 'ipnu';

        $nomorSuratOtomatis = NomorSuratHelper::generateWithCurrentMonth($jenisOrg, $tingkat, 'A', $periode);
        $indeksOptions = NomorSuratHelper::getIndeksUmumOptions();

        return view('admin.surat.keluar.create', compact('nomorSuratOtomatis', 'indeksOptions'));
    }

    // 2. RESPON AJAX UNTUK LIVE UPDATE NOMOR SURAT
    public function getNomorOtomatis(Request $request)
    {
        try {
            $organization = auth()->user()->organization;

            // 1. Amankan variabel dengan pengecekan apakah organisasi ada
            $type = $organization ? $organization->type : 'pac';
            $orgId = $organization ? $organization->id : null;
            $jenisOrg = $organization ? $organization->jenis_organisasi : 'ipnu';

            // 2. Panggil Helper menggunakan namespace absolut (\App\Helpers\...)
            $tingkat = \App\Helpers\NomorSuratHelper::getTingkatFromType($type);
            $periode = \App\Helpers\NomorSuratHelper::getPeriodeFromOrganization($orgId);

            $kodeIndeks = $request->query('kode_indeks', 'A');
            $penerbit = $request->query('penerbit', 'mandiri');
            $bulan = \App\Helpers\NomorSuratHelper::bulanToRomawi(date('n'));

            // Format tahun 2 digit (misal: 26) atau 4 digit (2026) sesuai kebutuhan.
            $tahun = date('Y');
            // 3. Logika Generate Nomor
            if ($penerbit === 'bersama') {
                $nomor = \App\Helpers\NomorSuratHelper::generateBersama($tingkat, $kodeIndeks, $periode, $periode, $bulan, $tahun);
            } elseif ($penerbit === 'panitia') {
                $nomor = \App\Helpers\NomorSuratHelper::generatePanitia($kodeIndeks, $periode, $bulan, $tahun, $jenisOrg);
            } else {
                $nomor = \App\Helpers\NomorSuratHelper::generate($jenisOrg, $tingkat, $kodeIndeks, $periode, $bulan, $tahun);
            }

            return response()->json(['status' => 'success', 'nomor_surat' => $nomor]);
        } catch (\Exception $e) {
            // Jika ada error, sistem tidak lagi mengirim halaman 500 HTML, melainkan pesan JSON ini
            return response()->json([
                'status' => 'error',
                'nomor_surat' => 'ERROR: ' . $e->getMessage()
            ], 500);
        }
    }

    // 3. MENYIMPAN DRAFT SURAT UMUM
    public function keluarStore(Request $request)
    {
        // 1. Validasi Input Dasar
        $request->validate([
            'nomor_surat'     => 'required|string|unique:surat_keluar,nomor_surat',
            'perihal'         => 'required|string',
            'tujuan_surat'    => 'required|string',
            'isi_surat_bebas' => 'required|string', // Input dari TinyMCE / WYSIWYG
            'tanggal_surat'   => 'required|date',
            'penerbit_surat'  => 'required|in:mandiri,bersama,panitia',
            'file_lampiran'   => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // Maksimal 5MB
        ]);

        // 2. Ambil Master Template Umum
        $template = \App\Models\SuratTemplate::where('jenis_surat', 'umum')->first();
        if (!$template) {
            return back()->withInput()->with('error', 'Gagal! Template Surat Umum belum tersedia di Master Data. Silakan buat terlebih dahulu.');
        }

        $org = auth()->user()->organization;
        if (!$org) {
            return back()->with('error', 'Gagal! Akun Anda belum terhubung dengan data organisasi.');
        }

        // ====================================================================
        // 3. PANGGIL SERVICE (Render Kop, Teks Bebas, dan Tanda Tangan)
        // ====================================================================
        $suratService = new \App\Services\SuratService();
        // Asumsi renderTemplateUmum merakit HTML dari $template->konten + teks bebas + tanda tangan
        $kontenHtmlFinal = $suratService->renderTemplateUmum($template->konten, $request, $org);

        // 4. Upload Lampiran Fisik (Jika ada)
        $lampiranPath = null;
        if ($request->hasFile('file_lampiran')) {
            $file = $request->file('file_lampiran');
            $lampiranPath = $file->storeAs('surat/lampiran', 'lampiran_' . time() . '_' . $file->getClientOriginalName(), 'public');
        }

        // ====================================================================
        // 5. SIMPAN KE DATABASE
        // ====================================================================
        $surat = new \App\Models\SuratKeluar();

        // Data Relasi
        $surat->organization_id = $org->id;
        $surat->template_id     = $template->id;
        $surat->created_by      = auth()->id();

        // Data Dasar Surat
        $surat->nomor_surat     = $request->nomor_surat;
        $surat->perihal         = $request->perihal;
        $surat->tujuan          = $request->tujuan_surat;
        $surat->tanggal_surat   = $request->tanggal_surat;
        $surat->penerbit_surat  = $request->penerbit_surat;
        $surat->file_lampiran   = $lampiranPath;

        // STANDAR BARU: isi_surat selalu menyimpan HTML Final yang sudah utuh
        $surat->isi_surat       = $kontenHtmlFinal;

        // Status Dokumen
        $surat->status          = 'draft';
        $surat->status_validasi = 'draft';

        // JSON untuk kebutuhan Halaman Edit (Menyimpan data mentah sebelum di-render)
        $surat->data_surat = [
            'isi_teks_bebas'          => $request->isi_surat_bebas,
            'penerbit_surat'          => $request->penerbit_surat,
            'nama_kegiatan_panitia'   => $request->nama_kegiatan_panitia ?? null,
            'nama_ketua_panitia'      => $request->nama_ketua_panitia ?? null,
            'nama_sekretaris_panitia' => $request->nama_sekretaris_panitia ?? null,
        ];

        $surat->save();

        // Redirect ke halaman Show untuk melihat preview PDF dan mengajukan validasi
        return redirect()->route('surat.keluar.show', $surat->id)
            ->with('success', 'Draft Surat Umum berhasil dibuat dan disimpan!');
    }

    public function keluarShow(SuratKeluar $suratKeluar)
    {
        $suratKeluar->load('organization', 'creator', 'signer', 'template');

        // ==========================================
        // CEK: APAKAH INI SURAT UMUM ATAU SURAT KHUSUS?
        // ==========================================
        if (!empty($suratKeluar->data_surat) && isset($suratKeluar->data_surat['html_lengkap'])) {

            // JIKA SURAT UMUM: Langsung lempar HTML matang ke Blade
            $suratKeluar->isi_surat = $suratKeluar->data_surat['html_lengkap'];
        } else {
            // JIKA SURAT KHUSUS: Gunakan logika Replace lawas Anda
            $isiSurat = $suratKeluar->isi_surat;
            $org = $suratKeluar->organization;

            $isiSurat = str_replace('{nomor_surat}', $suratKeluar->nomor_surat, $isiSurat);

            if ($org) {
                $jenisOrg = strtolower($org->jenis_organisasi ?? 'ipnu');
                $isiSurat = str_replace('{jenis_organisasi_upper}', strtoupper($jenisOrg), $isiSurat);
                $namaOrgLower = ($jenisOrg == 'ipnu') ? "Ikatan Pelajar Nahdlatul Ulama'" : "Ikatan Pelajar Putri Nahdlatul Ulama'";
                $isiSurat = str_replace('{nama_organisasi_lower}', $namaOrgLower, $isiSurat);
                $namaOrgLengkapBaris2 = ($jenisOrg == 'ipnu') ? "IKATAN PELAJAR NAHDLATUL ULAMA" : "IKATAN PELAJAR PUTRI NAHDLATUL ULAMA";
                $isiSurat = str_replace('{nama_organisasi_lengkap_baris2}', $namaOrgLengkapBaris2, $isiSurat);
            }

            // TTD & Stempel
            $ttdKetuaHtml = ($suratKeluar->status_validasi == 'selesai' && $org && $org->ttd_ketua)
                ? '<img src="' . $this->convertToBase64(storage_path('app/public/' . $org->ttd_ketua)) . '" style="max-height: 60px;">' : '';
            $ttdSekretarisHtml = (in_array($suratKeluar->status_validasi, ['menunggu_ttd_ketua', 'selesai']) && $org && $org->ttd_sekretaris)
                ? '<img src="' . $this->convertToBase64(storage_path('app/public/' . $org->ttd_sekretaris)) . '" style="max-height: 60px;">' : '';
            $stempelHtml = ($suratKeluar->status_validasi == 'selesai' && $org && $org->stempel)
                ? '<img src="' . $this->convertToBase64(storage_path('app/public/' . $org->stempel)) . '" style="max-height: 85px;">' : '';

            $isiSurat = str_replace(
                ['[TTD_KETUA]', '[TTD_SEKRETARIS]', '[STEMPEL]'],
                [$ttdKetuaHtml, $ttdSekretarisHtml, $stempelHtml],
                $isiSurat
            );

            $suratKeluar->isi_surat = $isiSurat;
        }

        return view('admin.surat.keluar.show', compact('suratKeluar'));
    }

    // =========================================================================
    // 1. MENGARAHKAN KE HALAMAN EDIT (UMUM ATAU KHUSUS)
    // =========================================================================
    public function keluarEdit(SuratKeluar $suratKeluar)
    {
        // 1. KUNCI KEAMANAN: Cek apakah user yang login adalah pembuat surat
        if ($suratKeluar->created_by != auth()->id()) {
            return redirect()->route('surat.keluar.show', $suratKeluar->id)
                ->with('error', 'Akses Ditolak! Hanya pembuat surat yang diizinkan untuk mengedit.');
        }

        // 2. Pastikan hanya draft yang bisa diedit
        if ($suratKeluar->status_validasi != 'draft') {
            return redirect()->route('surat.keluar.index')
                ->with('error', 'Surat sudah diproses, tidak bisa diedit lagi.');
        }

        $kategori = $suratKeluar->kategori_surat ?? 'umum';

        if ($kategori === 'khusus' || $suratKeluar->template_id) {
            // Ambil template berdasarkan ID template surat tersebut
            $template = \App\Models\SuratTemplate::find($suratKeluar->template_id);
            return view('admin.surat.keluar.edit_khusus', compact('suratKeluar', 'template'));
        }

        return view('admin.surat.keluar.edit_umum', compact('suratKeluar'));
    }

    // =========================================================================
    // 2. PROSES UPDATE SURAT UMUM
    // =========================================================================
    // =========================================================================
    // UPDATE SURAT UMUM (TEKS BEBAS)
    // =========================================================================
    public function keluarUpdateUmum(Request $request, SuratKeluar $suratKeluar)
    {
        // 1. Kunci Keamanan
        if ($suratKeluar->status_validasi != 'draft') {
            return redirect()->route('surat.keluar.index')->with('error', 'Surat sudah diajukan, tidak bisa diedit.');
        }
        if ($suratKeluar->created_by != auth()->id()) {
            return redirect()->route('surat.keluar.show', $suratKeluar->id)->with('error', 'Akses Ditolak! Hanya pembuat surat yang bisa mengedit.');
        }

        // 2. Validasi
        $request->validate([
            'nomor_surat'     => 'required|unique:surat_keluar,nomor_surat,' . $suratKeluar->id,
            'perihal'         => 'required|string',
            'tujuan_surat'    => 'required|string',
            'isi_surat_bebas' => 'required|string',
            'tanggal_surat'   => 'required|date',
            'penerbit_surat'  => 'required|in:mandiri,bersama,panitia',
            'file_lampiran'   => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $template = \App\Models\SuratTemplate::where('jenis_surat', 'umum')->first();
        if (!$template) {
            return back()->with('error', 'Template Umum belum tersedia.');
        }

        $org = auth()->user()->organization;

        // 3. PANGGIL SERVICE (Tidak perlu menulis ulang logika HTML)
        $suratService = new \App\Services\SuratService();
        $kontenHtmlFinal = $suratService->renderTemplateUmum($template->konten, $request, $org);

        // 4. Update Lampiran (Jika ada file baru)
        $lampiranPath = $suratKeluar->file_lampiran;
        if ($request->hasFile('file_lampiran')) {
            if ($lampiranPath && \Storage::disk('public')->exists($lampiranPath)) {
                \Storage::disk('public')->delete($lampiranPath); // Hapus file lama
            }
            $file = $request->file('file_lampiran');
            $lampiranPath = $file->storeAs('surat/lampiran', 'lampiran_' . time() . '_' . $file->getClientOriginalName(), 'public');
        }

        // 5. Update Database (Konsisten: isi_surat menyimpan HTML final, data_surat menyimpan raw teks)
        $suratKeluar->update([
            'nomor_surat'    => $request->nomor_surat,
            'perihal'        => $request->perihal,
            'tujuan'         => $request->tujuan_surat,
            'tanggal_surat'  => $request->tanggal_surat,
            'penerbit_surat' => $request->penerbit_surat,
            'file_lampiran'  => $lampiranPath,
            'isi_surat'      => $kontenHtmlFinal,
            'data_surat'     => [
                'isi_teks_bebas'          => $request->isi_surat_bebas,
                'penerbit_surat'          => $request->penerbit_surat,
                'nama_kegiatan_panitia'   => $request->nama_kegiatan_panitia ?? null,
                'nama_ketua_panitia'      => $request->nama_ketua_panitia ?? null,
                'nama_sekretaris_panitia' => $request->nama_sekretaris_panitia ?? null,
            ]
        ]);

        return redirect()->route('surat.keluar.show', $suratKeluar->id)->with('success', 'Draft Surat Umum berhasil diperbarui!');
    }

    // =========================================================================
    // UPDATE SURAT KHUSUS (TEMPLATE DINAMIS)
    // =========================================================================
    public function keluarUpdateKhusus(Request $request, SuratKeluar $suratKeluar)
    {
        // 1. Kunci Keamanan
        if ($suratKeluar->status_validasi != 'draft') {
            return redirect()->route('surat.keluar.index')->with('error', 'Surat sudah diajukan, tidak bisa diedit.');
        }
        if ($suratKeluar->created_by != auth()->id()) {
            return redirect()->route('surat.keluar.show', $suratKeluar->id)->with('error', 'Akses Ditolak! Hanya pembuat surat yang bisa mengedit.');
        }

        // 2. Ambil template aslinya
        $template = \App\Models\SuratTemplate::findOrFail($suratKeluar->template_id);

        // 3. Buat aturan validasi dinamis
        $rules = [
            'nomor_surat'   => 'required|string|unique:surat_keluar,nomor_surat,' . $suratKeluar->id,
            'perihal'       => 'required|string',
            'tanggal_surat' => 'required|date',
            'file_lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ];

        // Konversi format fields JSON (Jika berbentuk string saat ditarik dari DB)
        $fieldsConfig = $template->fields ?? [];
        if (is_string($fieldsConfig)) {
            $fieldsConfig = json_decode($fieldsConfig, true) ?? [];
        }

        foreach ($fieldsConfig as $field => $type) {
            if ($type != 'hidden') {
                $rules["fields.$field"] = 'nullable|string';
            }
        }
        $request->validate($rules);

        // 4. Persiapan Data (Ambil konten HTML murni langsung dari tabel master template)
        $org = auth()->user()->organization;
        $isiSuratMentah = $template->konten ?? $template->isi_surat;
        $dataSurat = $request->input('fields', []);
        $tanggalSurat = $request->tanggal_surat;

        // ====================================================================
        // 5. PANGGIL SERVICE (Semua logika replace teks, TTD, Hijriah diurus disini)
        // ====================================================================
        $suratService = new \App\Services\SuratService();
        $isiSuratFinal = $suratService->renderIsiSurat(
            $request->nomor_surat,
            $org,
            $isiSuratMentah,
            $dataSurat,
            $tanggalSurat
        );

        // 6. Update Lampiran (Jika ada file baru)
        $lampiranPath = $suratKeluar->file_lampiran;
        if ($request->hasFile('file_lampiran')) {
            if ($lampiranPath && \Storage::disk('public')->exists($lampiranPath)) {
                \Storage::disk('public')->delete($lampiranPath);
            }
            $file = $request->file('file_lampiran');
            $lampiranPath = $file->storeAs('surat/lampiran', 'lampiran_' . time() . '_' . $file->getClientOriginalName(), 'public');
        }

        // 7. Simpan Perubahan ke Database
        $suratKeluar->update([
            'nomor_surat'   => $request->nomor_surat,
            'perihal'       => $request->perihal,
            'tujuan'        => $request->tujuan ?? $suratKeluar->tujuan,
            'tanggal_surat' => $tanggalSurat,
            'file_lampiran' => $lampiranPath,
            'isi_surat'     => $isiSuratFinal, // HTML Final hasil render Service
            'data_surat'    => $dataSurat      // Simpan input form mentahnya
        ]);

        return redirect()->route('surat.keluar.show', $suratKeluar->id)->with('success', 'Draft surat khusus berhasil diperbarui!');
    }

    public function keluarDestroy(SuratKeluar $suratKeluar)
    {
        // 1. KUNCI KEAMANAN: Cek apakah user yang login adalah pembuat surat
        if ($suratKeluar->created_by != auth()->id()) {
            return redirect()->route('surat.keluar.index')
                ->with('error', 'Akses Ditolak! Hanya pembuat surat yang dapat menghapus data ini.');
        }

        // 2. KUNCI KEAMANAN (Opsional tapi disarankan): 
        // Cegah penghapusan jika surat sudah divalidasi/selesai
        if ($suratKeluar->status_validasi != 'draft') {
            return redirect()->route('surat.keluar.index')
                ->with('error', 'Surat yang sudah diajukan atau selesai tidak boleh dihapus.');
        }

        // Hapus file lampiran jika ada
        if ($suratKeluar->file_lampiran && \Storage::disk('public')->exists($suratKeluar->file_lampiran)) {
            \Storage::disk('public')->delete($suratKeluar->file_lampiran);
        }

        // Eksekusi hapus data dari database
        $suratKeluar->delete();

        return redirect()->route('surat.keluar.index')->with('success', 'Surat berhasil dihapus secara permanen.');
    }

    // ==========================================
    // BAGIAN 2: ALUR VALIDASI & TANDA TANGAN 
    // ==========================================

    public function ajukanValidasi(Request $request, SuratKeluar $surat)
    {
        $user = auth()->user();

        // Pengecekan Hak Akses
        if ($surat->created_by != $user->id) {
            return back()->with('error', 'Hanya pembuat surat yang dapat mengajukan validasi.');
        }
        if ($surat->status_validasi != 'draft') {
            return back()->with('error', 'Surat ini sudah diajukan sebelumnya.');
        }

        // ==========================================
        // CEK JALUR SURAT & SIMPAN DATA
        // ==========================================
        if ($surat->penerbit_surat === 'bersama') {

            // JALUR BERSAMA: Langsung tembak ke Sekretaris IPNU & IPPNU
            $surat->status_validasi = 'menunggu_ttd_sekretaris';
            $surat->diajukan_oleh = $user->id;
            $surat->save();

            return redirect()->route('surat.keluar.show', $surat->id)
                ->with('success', 'Surat Bersama diajukan. Menunggu validasi Sekretaris IPNU & IPPNU.');
        } else {

            // JALUR MANDIRI / PANITIA: Validasi input dropdown (HARUS pemeriksa_id)
            $request->validate([
                'pemeriksa_id' => 'required|exists:users,id'
            ], [
                'pemeriksa_id.required' => 'Pilih Sekretaris pemeriksa terlebih dahulu.'
            ]);

            // Isikan data (Status langsung lompat ke Sekretaris sesuai permintaan)
            $surat->status_validasi = 'menunggu_ttd_sekretaris';
            $surat->diajukan_oleh = $user->id;

            // Simpan siapa sekretaris yang ditugaskan untuk memeriksa (opsional jika dibutuhkan di show.blade)
            $surat->divalidasi_oleh = $request->pemeriksa_id;
            $surat->save();

            $validator = \App\Models\User::find($request->pemeriksa_id);
            return redirect()->route('surat.keluar.show', $surat->id)
                ->with('success', 'Surat berhasil diajukan. Menunggu TTD dari ' . ($validator ? $validator->name : 'Sekretaris.'));
        }
    }

    public function validasiWakil(Request $request, SuratKeluar $surat)
    {
        $user = auth()->user();

        if ($surat->created_by != $user->id) {
            return back()->with('error', 'Hanya pembuat surat yang dapat mengajukan validasi');
        }
        if ($surat->status_validasi != 'draft') {
            return back()->with('error', 'Surat sudah diajukan sebelumnya');
        }

        // CEK JALUR SURAT
        if ($surat->penerbit_surat === 'bersama') {
            // JALUR BERSAMA: Langsung ke Sekretaris (tanpa pilih Wasek lain)
            $surat->update([
                'status_validasi' => 'menunggu_ttd_sekretaris',
                'diajukan_oleh'   => $user->id,
            ]);
            return redirect()->route('surat.keluar.show', $surat)
                ->with('success', 'Surat Bersama diajukan. Menunggu validasi Sekretaris IPNU & IPPNU.');
        } else {
            // JALUR MANDIRI / PANITIA: Wajib pilih Wasek peninjau
            $request->validate(['divalidasi_oleh' => 'required|exists:users,id']);

            $surat->update([
                'status_validasi' => 'menunggu_validasi_wakil',
                'diajukan_oleh'   => $user->id,
                'divalidasi_oleh' => $request->divalidasi_oleh,
            ]);

            $validator = User::find($request->divalidasi_oleh);
            return redirect()->route('surat.keluar.show', $surat)
                ->with('success', 'Surat berhasil diajukan. Menunggu persetujuan dari ' . ($validator ? $validator->name : 'Wakil yang dipilih'));
        }
    }

    public function ttdSekretaris(Request $request, SuratKeluar $surat)
    {
        $user = auth()->user();
        $org = $user->organization;

        if (!$user->hasRole('sekretaris_pac')) {
            abort(403, 'Anda tidak memiliki akses.');
        }
        if (empty($org->ttd_sekretaris)) {
            return back()->with('error', 'Tanda tangan digital Sekretaris belum diatur!');
        }

        $surat->update([
            'ditandatangani_sekretaris_oleh' => $user->id,
            'tanggal_ttd_sekretaris' => now(),
            'status_validasi' => 'menunggu_ttd_ketua',
        ]);

        return back()->with('success', 'Surat berhasil ditandatangani oleh Sekretaris, sekarang menunggu TTD Ketua.');
    }

    public function ttdKetua(SuratKeluar $surat)
    {
        $user = auth()->user();
        $org = $user->organization;

        if (!$org || $org->ketua_id != $user->id) {
            return back()->with('error', 'Hanya Ketua yang dapat menandatangani');
        }
        if ($surat->status_validasi != 'menunggu_ttd_ketua') {
            return back()->with('error', 'Surat tidak dalam status menunggu tanda tangan ketua');
        }
        if (empty($org->ttd_ketua)) {
            return back()->with('error', 'Tanda tangan digital Anda belum diatur! Silakan isi di menu Profil terlebih dahulu.');
        }

        $surat->update([
            'status_validasi' => 'selesai',
            'status' => 'selesai',
            'ditandatangani_ketua_oleh' => $user->id,
            'ttd_ketua_file' => $org->ttd_ketua,
            'tanggal_ttd_ketua' => now(),
        ]);

        return redirect()->route('surat.keluar.show', $surat)->with('success', 'Surat ditandatangani Ketua. Proses Selesai!');
    }

    // Fungsi TTD Keluar lama (opsional jika masih dipakai untuk surat manual)
    public function keluarTtd(Request $request, SuratKeluar $suratKeluar)
    {
        $user = Auth::user();

        if (!$user->hasRole('ketua_pac') && !$user->hasRole('sekretaris_pac')) {
            return redirect()->back()->with('error', 'Hanya Ketua atau Sekretaris yang dapat menandatangani');
        }
        if ($suratKeluar->status != 'menunggu_ttd') {
            return redirect()->back()->with('error', 'Surat tidak dalam status menunggu tanda tangan');
        }

        $suratKeluar->update([
            'status' => 'selesai',
            'ditandatangani_oleh' => $user->id,
            'tanggal_ttd' => now(),
            'tanggal_kirim' => now(),
        ]);

        return redirect()->route('surat.keluar.show', $suratKeluar)->with('success', 'Surat berhasil ditandatangani');
    }

    // ==========================================
    // BAGIAN 3: DOWNLOAD PDF SURAT KELUAR
    // ==========================================

    public function keluarDownload(SuratKeluar $suratKeluar)
    {
        $org = $suratKeluar->organization;

        // ==========================================
        // CEK: APAKAH INI SURAT UMUM ATAU SURAT KHUSUS?
        // ==========================================
        if (!empty($suratKeluar->data_surat) && isset($suratKeluar->data_surat['html_lengkap'])) {
            // JIKA SURAT UMUM: Ambil HTML yang sudah utuh (QR Code sudah tertanam otomatis oleh fungsi approve)
            $isiSuratHtml = $suratKeluar->data_surat['html_lengkap'];
        } else {
            // JIKA SURAT KHUSUS (LAMA): Gunakan logika manual Anda
            $isiSuratHtml = $suratKeluar->isi_surat;

            $ttdKetuaHtml = ($suratKeluar->status_validasi == 'selesai' && $org && $org->ttd_ketua)
                ? '<img src="' . $this->convertToBase64(storage_path('app/public/' . $org->ttd_ketua)) . '" style="max-height: 60px;">' : '<br><br>';

            $ttdSekretarisHtml = (in_array($suratKeluar->status_validasi, ['menunggu_ttd_ketua', 'selesai']) && $org && $org->ttd_sekretaris)
                ? '<img src="' . $this->convertToBase64(storage_path('app/public/' . $org->ttd_sekretaris)) . '" style="max-height: 60px;">' : '<br><br>';

            $stempelHtml = ($suratKeluar->status_validasi == 'selesai' && $org && $org->stempel)
                ? '<img src="' . $this->convertToBase64(storage_path('app/public/' . $org->stempel)) . '" style="max-height: 85px;">' : '';

            $qrCodeHtml = '';
            if ($suratKeluar->status_validasi == 'selesai') {
                $linkVerifikasi = route('verifikasi.surat', ['nomor' => base64_encode($suratKeluar->nomor_surat)]);
                $qrImage = QrCode::format('png')->size(80)->margin(0)->generate($linkVerifikasi);
                $qrCodeHtml = '<img src="data:image/png;base64,' . base64_encode($qrImage) . '" alt="QR Verifikasi" style="max-height: 80px;">';
            }

            $isiSuratHtml = str_replace(
                ['[TTD_KETUA]', '[TTD_SEKRETARIS]', '[STEMPEL]', '[QR_TTE]'],
                [$ttdKetuaHtml, $ttdSekretarisHtml, $stempelHtml, $qrCodeHtml],
                $isiSuratHtml
            );
        }

        // Lanjutkan ke proses PDF...
        $suratUntukPdf = clone $suratKeluar;
        $suratUntukPdf->isi_surat = $isiSuratHtml;

        $pdf = new Fpdi();
        $pdfSurat = Pdf::loadView('admin.surat.keluar.pdf', ['surat' => $suratUntukPdf]);
        $pdfSuratPath = storage_path('app/temp/surat_' . $suratKeluar->id . '.pdf');

        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0775, true);
        }
        file_put_contents($pdfSuratPath, $pdfSurat->output());

        $pageCount = $pdf->setSourceFile($pdfSuratPath);
        for ($i = 1; $i <= $pageCount; $i++) {
            $pdf->AddPage();
            $pdf->useTemplate($pdf->importPage($i));
        }

        // Gabung Lampiran (Tetap dipertahankan karena ini fitur canggih)
        if ($suratKeluar->file_lampiran && Storage::disk('public')->exists($suratKeluar->file_lampiran)) {
            $lampiranPath = storage_path('app/public/' . $suratKeluar->file_lampiran);
            $fileExt = pathinfo($lampiranPath, PATHINFO_EXTENSION);

            if (in_array(strtolower($fileExt), ['jpg', 'jpeg', 'png'])) {
                $pdf->AddPage();
                $pdf->Image($lampiranPath, 10, 10, 190);
            } elseif (strtolower($fileExt) == 'pdf') {
                $pageCountLampiran = $pdf->setSourceFile($lampiranPath);
                for ($i = 1; $i <= $pageCountLampiran; $i++) {
                    $pdf->AddPage();
                    $pdf->useTemplate($pdf->importPage($i));
                }
            }
        }

        @unlink($pdfSuratPath);

        $namaFileDownload = 'Surat_Keluar_' . str_replace('/', '_', $suratKeluar->nomor_surat) . '.pdf';
        return response($pdf->Output('S', $namaFileDownload))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $namaFileDownload . '"');
    }

    private function convertToBase64($path)
    {
        if (!file_exists($path)) return '';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }

    // ==========================================
    // BAGIAN 4: SURAT MASUK (CRUD)
    // ==========================================

    public function masukIndex()
    {
        $surat = SuratMasuk::with('organization', 'penerima')
            ->orderBy('tanggal_diterima', 'desc')
            ->paginate(10);
        return view('admin.surat.masuk.index', compact('surat'));
    }

    public function masukCreate()
    {
        $users = User::orderBy('name')->get();
        return view('admin.surat.masuk.create', compact('users'));
    }

    public function masukStore(Request $request)
    {
        $request->validate([
            'nomor_surat' => 'required|string|max:100',
            'pengirim' => 'required|string|max:200',
            'perihal' => 'required|string|max:200',
            'isi_surat' => 'nullable|string',
            'tanggal_surat' => 'required|date',
            'tanggal_diterima' => 'required|date',
            'lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'status' => 'required|in:baru,diproses,selesai',
        ]);

        $lampiranPath = null;
        if ($request->hasFile('lampiran')) {
            $file = $request->file('lampiran');
            $filename = time() . '_' . $file->getClientOriginalName();
            $lampiranPath = $file->storeAs('surat/lampiran_masuk', $filename, 'public');
        }

        SuratMasuk::create([
            'organization_id' => Auth::user()->organization_id,
            'nomor_surat' => $request->nomor_surat,
            'pengirim' => $request->pengirim,
            'perihal' => $request->perihal,
            'isi_surat' => $request->isi_surat,
            'lampiran' => $lampiranPath,
            'tanggal_surat' => $request->tanggal_surat,
            'tanggal_diterima' => $request->tanggal_diterima,
            'status' => $request->status,
            'diterima_oleh' => Auth::id(),
        ]);

        return redirect()->route('surat.masuk.index')->with('success', 'Surat masuk berhasil disimpan');
    }

    public function masukShow(SuratMasuk $suratMasuk)
    {
        $suratMasuk->load('organization', 'penerima');
        return view('admin.surat.masuk.show', compact('suratMasuk'));
    }

    public function masukEdit(SuratMasuk $suratMasuk)
    {
        $users = User::orderBy('name')->get();
        return view('admin.surat.masuk.edit', compact('suratMasuk', 'users'));
    }

    public function masukUpdate(Request $request, SuratMasuk $suratMasuk)
    {
        $request->validate([
            'nomor_surat' => 'required|string|max:100',
            'pengirim' => 'required|string|max:200',
            'perihal' => 'required|string|max:200',
            'isi_surat' => 'nullable|string',
            'tanggal_surat' => 'required|date',
            'tanggal_diterima' => 'required|date',
            'lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'status' => 'required|in:baru,diproses,selesai',
        ]);

        if ($request->hasFile('lampiran')) {
            if ($suratMasuk->lampiran && Storage::disk('public')->exists($suratMasuk->lampiran)) {
                Storage::disk('public')->delete($suratMasuk->lampiran);
            }
            $file = $request->file('lampiran');
            $filename = time() . '_' . $file->getClientOriginalName();
            $suratMasuk->lampiran = $file->storeAs('surat/lampiran_masuk', $filename, 'public');
        }

        $suratMasuk->update([
            'nomor_surat' => $request->nomor_surat,
            'pengirim' => $request->pengirim,
            'perihal' => $request->perihal,
            'isi_surat' => $request->isi_surat,
            'tanggal_surat' => $request->tanggal_surat,
            'tanggal_diterima' => $request->tanggal_diterima,
            'status' => $request->status,
        ]);

        return redirect()->route('surat.masuk.index')->with('success', 'Surat masuk berhasil diupdate');
    }

    public function masukDestroy(SuratMasuk $suratMasuk)
    {
        if ($suratMasuk->lampiran && Storage::disk('public')->exists($suratMasuk->lampiran)) {
            Storage::disk('public')->delete($suratMasuk->lampiran);
        }
        $suratMasuk->delete();
        return redirect()->route('surat.masuk.index')->with('success', 'Surat masuk berhasil dihapus');
    }

    public function masukDisposisi(Request $request, SuratMasuk $suratMasuk)
    {
        $request->validate(['disposisi' => 'required|string']);

        $suratMasuk->update([
            'disposisi' => $request->disposisi,
            'status' => 'diproses',
        ]);

        return redirect()->route('surat.masuk.show', $suratMasuk)->with('success', 'Disposisi berhasil ditambahkan');
    }

    // ==========================================
    // BAGIAN 5: LAIN-LAIN (HELPER & TEMPLATE)
    // ==========================================

    public function generateNomor(Request $request)
    {
        $request->validate([
            'organisasi' => 'required|in:ipnu,ippnu',
            'tingkat' => 'required|string',
            'kode_indeks' => 'required|string',
            'periode' => 'required|string',
            'bulan' => 'required|string',
            'tahun' => 'required|numeric',
        ]);

        $nomor = NomorSuratHelper::generate(
            $request->organisasi,
            $request->tingkat,
            $request->kode_indeks,
            $request->periode,
            $request->bulan,
            $request->tahun
        );

        return response()->json(['nomor' => $nomor]);
    }

    public function show(SuratTemplate $template)
    {
        return view('admin.surat.template.show', compact('template'));
    }

    public function verifikasi(Request $request)
    {
        $nomorBase64 = $request->query('nomor');

        if (!$nomorBase64) {
            abort(404, 'Token verifikasi surat tidak ditemukan.');
        }

        // [PERBAIKAN] Kembalikan spasi menjadi tanda plus (+) 
        // yang hilang akibat konversi URL browser
        $nomorBase64 = str_replace(' ', '+', $nomorBase64);

        // Decode kembali nomor surat dari base64
        $nomorSurat = base64_decode($nomorBase64);

        // Cari surat di database beserta relasinya
        $surat = SuratKeluar::with(['organization', 'creator', 'ditandatanganiKetuaOleh', 'ditandatanganiSekretarisOleh'])
            ->where('nomor_surat', $nomorSurat)
            ->first();

        // Jika surat tidak ditemukan di database
        if (!$surat) {
            return view('public.verifikasi-surat', ['status' => 'palsu', 'nomor' => $nomorSurat]);
        }

        // Jika ketemu tapi statusnya belum selesai (TTE belum sah)
        if ($surat->status_validasi !== 'selesai') {
            return view('public.verifikasi-surat', ['status' => 'belum_sah', 'surat' => $surat]);
        }

        // KUNCI LETTER TRACKING: Ambil semua log audit trail untuk surat ini
        $trackingLogs = Activity::where('subject_type', SuratKeluar::class)
            ->where('subject_id', $surat->id)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('public.verifikasi-surat', [
            'status' => 'asli',
            'surat' => $surat,
            'logs' => $trackingLogs
        ]);
    }

    public function formLacak()
    {
        return view('public.lacak-surat');
    }


    public function approve($id)
    {
        $surat = SuratKeluar::findOrFail($id);
        $user = auth()->user();
        $jenisOrg = strtolower($user->organization->jenis_organisasi ?? 'ipnu');
        $pesan = '';

        // DETEKSI JABATAN USER (Sesuaikan dengan cara aplikasi Anda mengecek Role)
        // Contoh di bawah menggunakan Spatie Permission
        $isWasek = $user->hasRole('wakil_sekretaris');
        $isSekretaris = $user->hasRole('sekretaris_pac');
        $isKetua = $user->hasRole('ketua_pac');

        // ==================================================
        // JALUR 1: SURAT MANDIRI / PANITIA
        // ==================================================
        if ($surat->penerbit_surat !== 'bersama') {

            // TAHAP 1: VALIDASI WAKIL SEKRETARIS
            if ($surat->status_validasi === 'menunggu_validasi_wakil') {
                if ($surat->divalidasi_oleh !== $user->id) {
                    return back()->with('error', 'Hanya Wakil Sekretaris yang ditunjuk yang bisa memvalidasi surat ini.');
                }
                $surat->tanggal_validasi = now();
                $surat->status_validasi = 'menunggu_ttd_sekretaris';
                $pesan = "Validasi Wasek selesai. Surat diteruskan ke Sekretaris.";
            }

            // TAHAP 2: VALIDASI SEKRETARIS
            elseif ($surat->status_validasi === 'menunggu_ttd_sekretaris') {
                if (!$isSekretaris) return back()->with('error', 'Hanya Sekretaris yang bisa melakukan aksi ini.');

                $surat->ditandatangani_sekretaris_oleh = $user->id;
                $surat->tanggal_ttd_sekretaris = now();
                $surat->status_validasi = 'menunggu_ttd_ketua';
                $pesan = "Tanda tangan Sekretaris berhasil. Surat diteruskan ke Ketua.";
            }

            // TAHAP 3: VALIDASI KETUA (FINAL)
            elseif ($surat->status_validasi === 'menunggu_ttd_ketua') {
                if (!$isKetua) return back()->with('error', 'Hanya Ketua yang bisa melakukan aksi ini.');

                $surat->ditandatangani_ketua_oleh = $user->id;
                $surat->tanggal_ttd_ketua = now();
                $surat->status_validasi = 'selesai';
                $surat->status = 'selesai'; // Update status utama juga
                $pesan = "Sah! Dokumen disetujui Ketua dan QR Code TTE berhasil dicetak.";
            }
        }

        // ==================================================
        // JALUR 2: SURAT BERSAMA (DUA PINTU)
        // ==================================================
        else {

            // TAHAP 1: VALIDASI SEKRETARIS BERSAMA
            if ($surat->status_validasi === 'menunggu_ttd_sekretaris') {
                if (!$isSekretaris) return back()->with('error', 'Hanya Sekretaris yang bisa melakukan aksi ini.');

                if ($jenisOrg === 'ipnu') $surat->acc_sekretaris_ipnu_at = now();
                if ($jenisOrg === 'ippnu') $surat->acc_sekretaris_ippnu_at = now();

                if ($surat->acc_sekretaris_ipnu_at !== null && $surat->acc_sekretaris_ippnu_at !== null) {
                    $surat->status_validasi = 'menunggu_ttd_ketua';
                    $pesan = "ACC Sekretaris IPNU & IPPNU lengkap. Diteruskan ke Ketua.";
                } else {
                    $rekan = ($jenisOrg == 'ipnu') ? 'IPPNU' : 'IPNU';
                    $pesan = "Tanda tangan Anda berhasil. Menunggu Sekretaris $rekan.";
                }
            }

            // TAHAP 2: VALIDASI KETUA BERSAMA (FINAL)
            elseif ($surat->status_validasi === 'menunggu_ttd_ketua') {
                if (!$isKetua) return back()->with('error', 'Hanya Ketua yang bisa melakukan aksi ini.');

                if ($jenisOrg === 'ipnu') $surat->acc_ipnu_at = now();
                if ($jenisOrg === 'ippnu') $surat->acc_ippnu_at = now();

                if ($surat->acc_ipnu_at !== null && $surat->acc_ippnu_at !== null) {
                    $surat->status_validasi = 'selesai';
                    $surat->status = 'selesai';
                    $pesan = "Sah! Surat Bersama disetujui penuh dan QR Code TTE berhasil dicetak.";
                } else {
                    $rekan = ($jenisOrg == 'ipnu') ? 'IPPNU' : 'IPNU';
                    $pesan = "Tanda tangan Anda berhasil. Menunggu persetujuan Ketua $rekan.";
                }
            }
        }

        // ==================================================
        // EKSEKUSI PEMBUATAN QR CODE (JIKA STATUS SELESAI)
        // ==================================================
        if ($surat->status_validasi === 'selesai') {
            $urlVerifikasi = route('verifikasi.surat', ['nomor' => base64_encode($surat->nomor_surat)]);
            $qrCodeImage = base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(80)->errorCorrection('H')->generate($urlVerifikasi));
            $qrHtml = '<img src="data:image/svg+xml;base64,' . $qrCodeImage . '" alt="QR TTE" width="80" height="80">';

            $dataSurat = $surat->data_surat;
            $htmlLengkap = $dataSurat['html_lengkap'] ?? '';

            // Sisipkan QR Code ke template
            $htmlLengkap = str_replace('[QR_TTE]', $qrHtml, $htmlLengkap);

            $dataSurat['html_lengkap'] = $htmlLengkap;
            $surat->data_surat = $dataSurat;
        }

        $surat->save();

        return redirect()->back()->with('success', $pesan ?: 'Status tidak berubah. Pastikan Anda berada di tahap yang tepat.');
    }
}
