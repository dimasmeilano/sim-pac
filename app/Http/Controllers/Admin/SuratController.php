<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\NomorSuratHelper;
use App\Http\Controllers\Controller;
use App\Models\SuratKeluar;
use App\Models\SuratMasuk;
use App\Models\SuratTemplate;
use App\Models\User;
use App\Models\Organization;
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

    private function getTanggalHijriahOtomatis($tanggalMasehi = null)
    {
        $dateObj = $tanggalMasehi ? new \DateTime($tanggalMasehi) : new \DateTime('now');

        try {
            $hijri = new HijriDateTime($dateObj);
            $tanggalHijriah = $hijri->format('_j _F _Y');

            if (empty($tanggalHijriah)) {
                $tanggalHijriah = $hijri->date("_j _F _Y");
            }
            $tanggalHijriah = preg_replace('/\s+/', ' ', $tanggalHijriah);

            if (str_ends_with(strtoupper($tanggalHijriah), 'H')) return $tanggalHijriah;
            return $tanggalHijriah . ' H';
        } catch (\Throwable $e) {
            $formatter = new \IntlDateFormatter(
                'id_ID@calendar=islamic-umalqura',
                \IntlDateFormatter::LONG,
                \IntlDateFormatter::NONE,
                'Asia/Jakarta',
                \IntlDateFormatter::TRADITIONAL
            );

            $hasilIntl = $formatter->format($dateObj);
            $hasilIntl = str_replace(' AH', '', $hasilIntl);

            if (!str_ends_with(strtoupper($hasilIntl), 'H')) {
                $hasilIntl = $hasilIntl . ' H';
            }
            return $hasilIntl;
        }
    }

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

        // Ambil daftar organisasi untuk saklar internal
        $organizations = Organization::where('id', '!=', auth()->user()->organization_id)->get();

        return view('admin.surat.keluar.create', compact('nomorSuratOtomatis', 'indeksOptions', 'organizations'));
    }

    public function getNomorOtomatis(Request $request)
    {
        try {
            $organization = auth()->user()->organization;

            $type = $organization ? $organization->type : 'pac';
            $orgId = $organization ? $organization->id : null;
            $jenisOrg = $organization ? $organization->jenis_organisasi : 'ipnu';

            $tingkat = \App\Helpers\NomorSuratHelper::getTingkatFromType($type);
            $periode = \App\Helpers\NomorSuratHelper::getPeriodeFromOrganization($orgId);

            $kodeIndeks = $request->query('kode_indeks', 'A');
            $penerbit = $request->query('penerbit', 'mandiri');
            $bulan = \App\Helpers\NomorSuratHelper::bulanToRomawi(date('n'));
            $tahun = date('Y');

            if ($penerbit === 'bersama') {
                $nomor = \App\Helpers\NomorSuratHelper::generateBersama($tingkat, $kodeIndeks, $periode, $periode, $bulan, $tahun);
            } elseif ($penerbit === 'panitia') {
                $nomor = \App\Helpers\NomorSuratHelper::generatePanitia($kodeIndeks, $periode, $bulan, $tahun, $jenisOrg);
            } else {
                $nomor = \App\Helpers\NomorSuratHelper::generate($jenisOrg, $tingkat, $kodeIndeks, $periode, $bulan, $tahun);
            }

            return response()->json(['status' => 'success', 'nomor_surat' => $nomor]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'nomor_surat' => 'ERROR: ' . $e->getMessage()
            ], 500);
        }
    }

    public function keluarStore(Request $request)
    {
        $request->validate([
            'nomor_surat'     => 'required|string|unique:surat_keluar,nomor_surat',
            'perihal'         => 'required|string',
            'isi_surat_bebas' => 'required|string',
            'tanggal_surat'   => 'required|date',
            'penerbit_surat'  => 'required|in:mandiri,bersama,panitia',
            'kategori_tujuan' => 'nullable|in:internal,eksternal',
            'file_lampiran'   => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $template = \App\Models\SuratTemplate::where('jenis_surat', 'umum')->first();
        if (!$template) {
            return back()->withInput()->with('error', 'Gagal! Template Surat Umum belum tersedia.');
        }

        $org = auth()->user()->organization;
        if (!$org) {
            return back()->with('error', 'Gagal! Akun Anda belum terhubung dengan data organisasi.');
        }

        // Penentuan Tujuan (Internal vs Eksternal)
        $kategoriTujuan = $request->input('kategori_tujuan', 'eksternal');
        $tujuanOrgId = null;
        $tujuanTeks = $request->input('tujuan_surat') ?? $request->input('tujuan') ?? '-';

        if ($kategoriTujuan === 'internal') {
            $tujuanOrgId = $request->input('tujuan_organization_id');
            $orgTujuan = Organization::find($tujuanOrgId);
            $tujuanTeks = $orgTujuan ? $orgTujuan->name : '-';
        }

        $suratService = new \App\Services\SuratService();
        $kontenHtmlFinal = $suratService->renderTemplateUmum($template->konten, $request, $org);

        $lampiranPath = null;
        if ($request->hasFile('file_lampiran')) {
            $file = $request->file('file_lampiran');
            $lampiranPath = $file->storeAs('surat/lampiran', 'lampiran_' . time() . '_' . $file->getClientOriginalName(), 'public');
        }

        $surat = new \App\Models\SuratKeluar();
        $surat->organization_id = $org->id;
        $surat->template_id     = $template->id;
        $surat->created_by      = auth()->id();
        $surat->nomor_surat     = $request->nomor_surat;
        $surat->perihal         = $request->perihal;

        // Simpan Saklar Otomatis
        $surat->tujuan_organization_id = $tujuanOrgId;
        $surat->tujuan          = $tujuanTeks;

        $surat->tanggal_surat   = $request->tanggal_surat;
        $surat->penerbit_surat  = $request->penerbit_surat;
        $surat->file_lampiran   = $lampiranPath;
        $surat->isi_surat       = $kontenHtmlFinal;
        $surat->status          = 'draft';
        $surat->status_validasi = 'draft';
        $surat->data_surat = json_encode([
            'isi_teks_bebas'          => $request->isi_surat_bebas,
            'penerbit_surat'          => $request->penerbit_surat,
            'nama_kegiatan_panitia'   => $request->nama_kegiatan_panitia ?? null,
            'nama_ketua_panitia'      => $request->nama_ketua_panitia ?? null,
            'nama_sekretaris_panitia' => $request->nama_sekretaris_panitia ?? null,
            'tujuan_surat'            => $tujuanTeks,
            'lampiran'                => $request->file_lampiran ? '1 (Satu) Berkas' : '-',
        ]);

        $surat->save();

        return redirect()->route('surat.keluar.show', $surat->id)
            ->with('success', 'Draft Surat Umum berhasil dibuat dan disimpan!');
    }

    public function keluarShow(SuratKeluar $suratKeluar)
    {
        $suratKeluar->load('organization', 'creator', 'signer', 'template');

        if ($suratKeluar->organization_id != auth()->user()->organization_id && !auth()->user()->hasRole('super_admin')) {
            abort(403, 'Akses Ditolak! Anda tidak diizinkan melihat surat milik organisasi lain.');
        }

        if (!empty($suratKeluar->data_surat) && isset($suratKeluar->data_surat['html_lengkap'])) {
            $suratKeluar->isi_surat = $suratKeluar->data_surat['html_lengkap'];
        } else {
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

    public function keluarEdit(SuratKeluar $suratKeluar)
    {
        if ($suratKeluar->organization_id != auth()->user()->organization_id && !auth()->user()->hasRole('super_admin')) {
            abort(403, 'Akses Ditolak! Anda tidak diizinkan melihat surat milik organisasi lain.');
        }
        if ($suratKeluar->created_by != auth()->id()) {
            return redirect()->route('surat.keluar.show', $suratKeluar->id)
                ->with('error', 'Akses Ditolak! Hanya pembuat surat yang diizinkan untuk mengedit.');
        }

        if ($suratKeluar->status_validasi != 'draft') {
            return redirect()->route('surat.keluar.index')
                ->with('error', 'Surat sudah diproses, tidak bisa diedit lagi.');
        }

        $kategori = $suratKeluar->kategori_surat ?? 'umum';
        $organizations = Organization::where('id', '!=', auth()->user()->organization_id)->get();

        if ($kategori === 'khusus' || $suratKeluar->template_id) {
            $template = \App\Models\SuratTemplate::find($suratKeluar->template_id);
            return view('admin.surat.keluar.edit_khusus', compact('suratKeluar', 'template', 'organizations'));
        }

        return view('admin.surat.keluar.edit_umum', compact('suratKeluar', 'organizations'));
    }

    public function keluarUpdateUmum(Request $request, SuratKeluar $suratKeluar)
    {
        if ($suratKeluar->status_validasi != 'draft') {
            return redirect()->route('surat.keluar.index')->with('error', 'Surat sudah diajukan, tidak bisa diedit.');
        }
        if ($suratKeluar->created_by != auth()->id()) {
            return redirect()->route('surat.keluar.show', $suratKeluar->id)->with('error', 'Akses Ditolak! Hanya pembuat surat yang bisa mengedit.');
        }

        $request->validate([
            'nomor_surat'     => 'required|unique:surat_keluar,nomor_surat,' . $suratKeluar->id,
            'perihal'         => 'required|string',
            'isi_surat_bebas' => 'required|string',
            'tanggal_surat'   => 'required|date',
            'penerbit_surat'  => 'required|in:mandiri,bersama,panitia',
            'kategori_tujuan' => 'nullable|in:internal,eksternal',
            'file_lampiran'   => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $template = \App\Models\SuratTemplate::where('jenis_surat', 'umum')->first();
        if (!$template) return back()->with('error', 'Template Umum belum tersedia.');

        $org = auth()->user()->organization;

        // Penentuan Tujuan (Internal vs Eksternal)
        $kategoriTujuan = $request->input('kategori_tujuan', 'eksternal');
        $tujuanOrgId = null;
        $tujuanTeks = $request->input('tujuan_surat') ?? $request->input('tujuan') ?? '-';

        if ($kategoriTujuan === 'internal') {
            $tujuanOrgId = $request->input('tujuan_organization_id');
            $orgTujuan = Organization::find($tujuanOrgId);
            $tujuanTeks = $orgTujuan ? $orgTujuan->nama : '-';
        }

        $suratService = new \App\Services\SuratService();
        $kontenHtmlFinal = $suratService->renderTemplateUmum($template->konten, $request, $org);

        $lampiranPath = $suratKeluar->file_lampiran;
        if ($request->hasFile('file_lampiran')) {
            if ($lampiranPath && \Storage::disk('public')->exists($lampiranPath)) {
                \Storage::disk('public')->delete($lampiranPath);
            }
            $file = $request->file('file_lampiran');
            $lampiranPath = $file->storeAs('surat/lampiran', 'lampiran_' . time() . '_' . $file->getClientOriginalName(), 'public');
        }

        $suratKeluar->update([
            'nomor_surat'    => $request->nomor_surat,
            'perihal'        => $request->perihal,
            'tujuan_organization_id' => $tujuanOrgId,
            'tujuan'         => $tujuanTeks,
            'tanggal_surat'  => $request->tanggal_surat,
            'penerbit_surat' => $request->penerbit_surat,
            'file_lampiran'  => $lampiranPath,
            'isi_surat'      => $kontenHtmlFinal,
            'data_surat'     => json_encode([
                'isi_teks_bebas'          => $request->isi_surat_bebas,
                'penerbit_surat'          => $request->penerbit_surat,
                'nama_kegiatan_panitia'   => $request->nama_kegiatan_panitia ?? null,
                'nama_ketua_panitia'      => $request->nama_ketua_panitia ?? null,
                'nama_sekretaris_panitia' => $request->nama_sekretaris_panitia ?? null,
                'tujuan_surat'            => $tujuanTeks,
                'lampiran'                => $request->file_lampiran ? '1 (Satu) Berkas' : '-',
            ])
        ]);

        return redirect()->route('surat.keluar.show', $suratKeluar->id)->with('success', 'Draft Surat Umum berhasil diperbarui!');
    }

    public function keluarUpdateKhusus(Request $request, SuratKeluar $suratKeluar)
    {
        if ($suratKeluar->status_validasi != 'draft') {
            return redirect()->route('surat.keluar.index')->with('error', 'Surat sudah diajukan, tidak bisa diedit.');
        }
        if ($suratKeluar->created_by != auth()->id()) {
            return redirect()->route('surat.keluar.show', $suratKeluar->id)->with('error', 'Akses Ditolak!');
        }

        $template = \App\Models\SuratTemplate::findOrFail($suratKeluar->template_id);

        $rules = [
            'nomor_surat'   => 'required|string|unique:surat_keluar,nomor_surat,' . $suratKeluar->id,
            'perihal'       => 'required|string',
            'tanggal_surat' => 'required|date',
            'kategori_tujuan' => 'nullable|in:internal,eksternal',
            'file_lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ];

        $fieldsConfig = $template->fields ?? [];
        if (is_string($fieldsConfig)) $fieldsConfig = json_decode($fieldsConfig, true) ?? [];

        foreach ($fieldsConfig as $field => $type) {
            if ($type != 'hidden') $rules["fields.$field"] = 'nullable|string';
        }
        $request->validate($rules);

        $kategoriTujuan = $request->input('kategori_tujuan', 'eksternal');
        $tujuanOrgId = null;
        $tujuanTeks = $request->input('tujuan') ?? '-';

        if ($kategoriTujuan === 'internal') {
            $tujuanOrgId = $request->input('tujuan_organization_id');
            $orgTujuan = Organization::find($tujuanOrgId);
            $tujuanTeks = $orgTujuan ? $orgTujuan->nama : '-';
        }

        $org = auth()->user()->organization;
        $isiSuratMentah = $template->konten ?? $template->isi_surat;
        $dataSurat = $request->input('fields', []);
        $tanggalSurat = $request->tanggal_surat;

        $suratService = new \App\Services\SuratService();
        $isiSuratFinal = $suratService->renderIsiSurat($request->nomor_surat, $org, $isiSuratMentah, $dataSurat, $tanggalSurat);

        $lampiranPath = $suratKeluar->file_lampiran;
        if ($request->hasFile('file_lampiran')) {
            if ($lampiranPath && \Storage::disk('public')->exists($lampiranPath)) {
                \Storage::disk('public')->delete($lampiranPath);
            }
            $file = $request->file('file_lampiran');
            $lampiranPath = $file->storeAs('surat/lampiran', 'lampiran_' . time() . '_' . $file->getClientOriginalName(), 'public');
        }

        $suratKeluar->update([
            'nomor_surat'   => $request->nomor_surat,
            'perihal'       => $request->perihal,
            'tujuan_organization_id' => $tujuanOrgId,
            'tujuan'        => $tujuanTeks,
            'tanggal_surat' => $tanggalSurat,
            'file_lampiran' => $lampiranPath,
            'isi_surat'     => $isiSuratFinal,
            'data_surat'    => $dataSurat
        ]);

        return redirect()->route('surat.keluar.show', $suratKeluar->id)->with('success', 'Draft surat khusus berhasil diperbarui!');
    }

    public function keluarDestroy(SuratKeluar $suratKeluar)
    {
        if ($suratKeluar->created_by != auth()->id()) {
            return redirect()->route('surat.keluar.index')
                ->with('error', 'Akses Ditolak! Hanya pembuat surat yang dapat menghapus data ini.');
        }

        if ($suratKeluar->status_validasi != 'draft') {
            return redirect()->route('surat.keluar.index')
                ->with('error', 'Surat yang sudah diajukan atau selesai tidak boleh dihapus.');
        }

        if ($suratKeluar->file_lampiran && \Storage::disk('public')->exists($suratKeluar->file_lampiran)) {
            \Storage::disk('public')->delete($suratKeluar->file_lampiran);
        }

        $suratKeluar->delete();

        return redirect()->route('surat.keluar.index')->with('success', 'Surat berhasil dihapus secara permanen.');
    }

    public function ajukanValidasi(Request $request, SuratKeluar $surat)
    {
        $user = auth()->user();

        if ($surat->created_by != $user->id) return back()->with('error', 'Hanya pembuat surat yang dapat mengajukan validasi.');
        if ($surat->status_validasi != 'draft') return back()->with('error', 'Surat ini sudah diajukan sebelumnya.');

        // ---------------------------------------------------------
        // 🚀 BYPASS LOGIC: Jika yang buat adalah Sekretaris, langsung auto-TTD
        // ---------------------------------------------------------
        if ($user->hasAnyRole(['sekretaris_pac', 'sekretaris_ranting'])) {

            // Cek apakah TTD sudah diatur
            if (empty($user->organization->ttd_sekretaris)) {
                return back()->with('error', 'Tanda tangan digital Sekretaris belum diatur!');
            }

            $surat->update([
                'diajukan_oleh'                  => $user->id,
                'status_validasi'                => 'menunggu_ttd_ketua',
                'ditandatangani_sekretaris_oleh' => $user->id,
                'tanggal_ttd_sekretaris'         => now(),
            ]);

            return redirect()->route('surat.keluar.show', $surat->id)
                ->with('success', 'Surat otomatis di-TTD oleh Anda (Sekretaris). Sekarang menunggu TTD dari Ketua.');
        }

        // ---------------------------------------------------------
        // LOGIKA ASLI (Jika yang buat anggota biasa / bukan sekretaris)
        // ---------------------------------------------------------
        if ($surat->penerbit_surat === 'bersama') {
            $surat->status_validasi = 'menunggu_ttd_sekretaris';
            $surat->diajukan_oleh = $user->id;
            $surat->save();

            return redirect()->route('surat.keluar.show', $surat->id)
                ->with('success', 'Surat Bersama diajukan. Menunggu validasi Sekretaris IPNU & IPPNU.');
        } else {
            $request->validate(['pemeriksa_id' => 'required|exists:users,id'], ['pemeriksa_id.required' => 'Pilih Sekretaris pemeriksa terlebih dahulu.']);
            $surat->status_validasi = 'menunggu_ttd_sekretaris';
            $surat->diajukan_oleh = $user->id;
            $surat->divalidasi_oleh = $request->pemeriksa_id;
            $surat->save();

            $validator = \App\Models\User::find($request->pemeriksa_id);
            return redirect()->route('surat.keluar.show', $surat->id)
                ->with('success', 'Surat berhasil diajukan. Menunggu TTD dari ' . ($validator ? $validator->name : 'Sekretaris.'));
        }
    }

    // =========================================================================
    // TAHAP 1: VALIDASI WAKIL SEKRETARIS (Jika Ada)
    // =========================================================================
    public function validasiWakil(SuratKeluar $surat)
    {
        $user = auth()->user();
        if ($surat->status_validasi !== 'menunggu_validasi_wakil') abort(403);
        if ($surat->divalidasi_oleh !== $user->id) return back()->with('error', 'Hanya Wakil Sekretaris yang ditunjuk yang bisa memvalidasi surat ini.');

        $surat->update([
            'tanggal_validasi' => now(),
            'status_validasi'  => 'menunggu_ttd_sekretaris'
        ]);

        return back()->with('success', 'Validasi Wasek selesai. Surat diteruskan ke Sekretaris.');
    }

    // =========================================================================
    // TAHAP 2: PERSETUJUAN & TTD SEKRETARIS
    // =========================================================================
    public function ttdSekretaris(SuratKeluar $surat)
    {
        $user = auth()->user();
        $org = $user->organization;
        $jenisOrg = strtolower($org->jenis_organisasi ?? 'ipnu');

        if (!$user->hasAnyRole(['sekretaris_ranting', 'sekretaris_pac'])) abort(403);
        if ($surat->status_validasi !== 'menunggu_ttd_sekretaris') abort(403);

        // Pengecekan Ketersediaan TTD
        if (empty($org->ttd_sekretaris) || !\Illuminate\Support\Facades\Storage::disk('public')->exists($org->ttd_sekretaris)) {
            return back()->with('error', 'Gagal! Tanda tangan Sekretaris belum diatur di Profil.');
        }

        if ($surat->penerbit_surat !== 'bersama') {
            // Surat Mandiri / Panitia
            $surat->update([
                'ditandatangani_sekretaris_oleh' => $user->id,
                'tanggal_ttd_sekretaris'         => now(),
                'status_validasi'                => 'menunggu_ttd_ketua'
            ]);
            return back()->with('success', 'Tanda tangan Sekretaris berhasil disematkan. Surat diteruskan ke Ketua.');
        } else {
            // Surat Bersama (IPNU & IPPNU)
            if ($jenisOrg === 'ipnu') $surat->acc_sekretaris_ipnu_at = now();
            if ($jenisOrg === 'ippnu') $surat->acc_sekretaris_ippnu_at = now();

            if ($surat->acc_sekretaris_ipnu_at !== null && $surat->acc_sekretaris_ippnu_at !== null) {
                $surat->status_validasi = 'menunggu_ttd_ketua';
                $pesan = "ACC Sekretaris IPNU & IPPNU lengkap. Diteruskan ke Ketua.";
            } else {
                $rekan = ($jenisOrg == 'ipnu') ? 'IPPNU' : 'IPNU';
                $pesan = "Tanda tangan Anda berhasil. Menunggu Sekretaris $rekan.";
            }
            $surat->save();
            return back()->with('success', $pesan);
        }
    }

    // =========================================================================
    // TAHAP 3 (FINAL): PERSETUJUAN KETUA, TTE, DAN PENGIRIMAN OTOMATIS
    // =========================================================================
    public function ttdKetua(SuratKeluar $surat)
    {
        $user = auth()->user();
        $org = $user->organization;
        $jenisOrg = strtolower($org->jenis_organisasi ?? 'ipnu');

        if (!$user->hasAnyRole(['ketua_ranting', 'ketua_pac'])) abort(403);
        if ($surat->status_validasi !== 'menunggu_ttd_ketua') abort(403);

        // Pengecekan Ketersediaan TTD dan Stempel
        if (empty($org->ttd_ketua) || !\Illuminate\Support\Facades\Storage::disk('public')->exists($org->ttd_ketua)) {
            return back()->with('error', 'Gagal! Tanda Tangan Anda belum diatur atau file hilang.');
        }
        if (empty($org->stempel) || !\Illuminate\Support\Facades\Storage::disk('public')->exists($org->stempel)) {
            return back()->with('error', 'Gagal! Stempel Organisasi belum diunggah.');
        }

        $isSelesai = false;
        $pesan = '';

        // Penentuan Status Selesai (Mandiri vs Bersama)
        if ($surat->penerbit_surat !== 'bersama') {
            $surat->ditandatangani_ketua_oleh = $user->id;

            //$surat->ttd_ketua_file = $org->ttd_ketua; // <--- HAPUS ATAU JADIKAN KOMENTAR BARIS INI

            $surat->tanggal_ttd_ketua = now();
            $surat->status_validasi = 'selesai';
            $surat->status = 'selesai';
            $isSelesai = true;
            $pesan = "Sah! Dokumen disetujui Ketua dan TTE berhasil dicetak.";
        } else {
            if ($jenisOrg === 'ipnu') $surat->acc_ipnu_at = now();
            if ($jenisOrg === 'ippnu') $surat->acc_ippnu_at = now();

            if ($surat->acc_ipnu_at !== null && $surat->acc_ippnu_at !== null) {
                $surat->status_validasi = 'selesai';
                $surat->status = 'selesai';
                $isSelesai = true;
                $pesan = "Sah! Surat Bersama disetujui penuh dan TTE dicetak.";
            } else {
                $rekan = ($jenisOrg == 'ipnu') ? 'IPPNU' : 'IPNU';
                $pesan = "Tanda tangan Anda berhasil. Menunggu persetujuan Ketua $rekan.";
            }
        }

        // Eksekusi Puncak (Hanya berjalan jika status benar-benar 'selesai')
        // Eksekusi Puncak (Hanya berjalan jika status benar-benar 'selesai')
        if ($isSelesai) {
            // 1. RAKIT ULANG HTML UNTUK MENGELUARKAN GAMBAR TTD & QR CODE
            $template = \App\Models\SuratTemplate::find($surat->template_id);
            if ($template) {
                // Ekstrak data JSON dengan aman
                $dataSuratRaw = $surat->data_surat;
                $dataSuratArray = is_string($dataSuratRaw) ? (json_decode($dataSuratRaw, true) ?? []) : (is_array($dataSuratRaw) ? $dataSuratRaw : []);

                $requestData = new \Illuminate\Http\Request();
                $requestData->merge($dataSuratArray);
                $requestData->merge([
                    'isi_surat_bebas' => $dataSuratArray['isi_teks_bebas'] ?? '',
                    'tujuan_surat'    => $dataSuratArray['tujuan_surat'] ?? $surat->tujuan,
                    'lampiran'        => $dataSuratArray['lampiran'] ?? '-',
                    'perihal'         => $surat->perihal,
                    'nomor_surat'     => $surat->nomor_surat,
                    'tanggal_surat'   => $surat->tanggal_surat,
                    'penerbit_surat'  => $surat->penerbit_surat,
                ]);

                $suratService = new \App\Services\SuratService();
                $finalHtml = $suratService->renderTemplateUmum($template->konten, $requestData, $surat->organization, 'selesai');
                $surat->isi_surat = $finalHtml;
            }

            // 2. SAKLAR PENGIRIMAN OTOMATIS
            if (!empty($surat->tujuan_organization_id)) {
                $cekSudahMasuk = SuratMasuk::where('nomor_surat', $surat->nomor_surat)->where('organization_id', $surat->tujuan_organization_id)->exists();
                if (!$cekSudahMasuk) {
                    SuratMasuk::create([
                        'organization_id'  => $surat->tujuan_organization_id,
                        'nomor_surat'      => $surat->nomor_surat,
                        'pengirim'         => $surat->organization->name ?? 'Sistem SIM PAC',
                        'perihal'          => $surat->perihal,
                        'isi_surat'        => $surat->isi_surat,
                        'lampiran'         => $surat->file_lampiran,
                        'tanggal_surat'    => $surat->tanggal_surat,
                        'tanggal_diterima' => now(),
                        'status'           => 'baru',
                        'diterima_oleh'    => $user->id,
                    ]);
                }
            }
        }

        $surat->save();
        return back()->with('success', $pesan);
    }

    public function keluarDownload(SuratKeluar $suratKeluar)
    {
        $org = $suratKeluar->organization;

        if ($suratKeluar->organization_id != auth()->user()->organization_id && !auth()->user()->hasRole('super_admin')) {
            abort(403, 'Akses Ditolak! Anda tidak diizinkan melihat surat milik organisasi lain.');
        }
        if (!empty($suratKeluar->data_surat) && isset($suratKeluar->data_surat['html_lengkap'])) {
            $isiSuratHtml = $suratKeluar->data_surat['html_lengkap'];
        } else {
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

            $isiSuratHtml = str_replace(['[TTD_KETUA]', '[TTD_SEKRETARIS]', '[STEMPEL]', '[QR_TTE]'], [$ttdKetuaHtml, $ttdSekretarisHtml, $stempelHtml, $qrCodeHtml], $isiSuratHtml);
        }

        $suratUntukPdf = clone $suratKeluar;
        $suratUntukPdf->isi_surat = $isiSuratHtml;

        $pdf = new Fpdi();
        $pdfSurat = Pdf::loadView('admin.surat.keluar.pdf', ['surat' => $suratUntukPdf]);
        $pdfSuratPath = storage_path('app/temp/surat_' . $suratKeluar->id . '.pdf');

        if (!file_exists(storage_path('app/temp'))) mkdir(storage_path('app/temp'), 0775, true);
        file_put_contents($pdfSuratPath, $pdfSurat->output());

        $pageCount = $pdf->setSourceFile($pdfSuratPath);
        for ($i = 1; $i <= $pageCount; $i++) {
            $pdf->AddPage();
            $pdf->useTemplate($pdf->importPage($i));
        }

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

    public function masukIndex()
    {
        $query = SuratMasuk::with(['organization', 'penerima']);
        $user = auth()->user();

        if (!$user->hasRole('super_admin')) {
            if ($user->organization) {
                $jenisOrgUser = $user->organization->jenis_organisasi;
                $query->whereHas('organization', function ($q) use ($jenisOrgUser) {
                    if ($jenisOrgUser === 'ipnu') {
                        $q->whereIn('jenis_organisasi', ['ipnu', 'bersama']);
                    } elseif ($jenisOrgUser === 'ippnu') {
                        $q->whereIn('jenis_organisasi', ['ippnu', 'bersama']);
                    } else {
                        $q->where('jenis_organisasi', 'bersama');
                    }
                });
            } else {
                $query->whereNull('id');
            }
        }

        $surat = $query->orderBy('tanggal_diterima', 'desc')->paginate(10);
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
            $lampiranPath = $file->storeAs('surat/lampiran_masuk', time() . '_' . $file->getClientOriginalName(), 'public');
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
            if ($suratMasuk->lampiran && Storage::disk('public')->exists($suratMasuk->lampiran)) Storage::disk('public')->delete($suratMasuk->lampiran);
            $suratMasuk->lampiran = $request->file('lampiran')->storeAs('surat/lampiran_masuk', time() . '_' . $request->file('lampiran')->getClientOriginalName(), 'public');
        }

        $suratMasuk->update($request->only(['nomor_surat', 'pengirim', 'perihal', 'isi_surat', 'tanggal_surat', 'tanggal_diterima', 'status']));
        return redirect()->route('surat.masuk.index')->with('success', 'Surat masuk berhasil diupdate');
    }

    public function masukDestroy(SuratMasuk $suratMasuk)
    {
        if ($suratMasuk->lampiran && Storage::disk('public')->exists($suratMasuk->lampiran)) Storage::disk('public')->delete($suratMasuk->lampiran);
        $suratMasuk->delete();
        return redirect()->route('surat.masuk.index')->with('success', 'Surat masuk berhasil dihapus');
    }

    public function masukDisposisi(Request $request, SuratMasuk $suratMasuk)
    {
        $request->validate(['disposisi' => 'required|string']);
        $suratMasuk->update(['disposisi' => $request->disposisi, 'status' => 'diproses']);
        return redirect()->route('surat.masuk.show', $suratMasuk)->with('success', 'Disposisi berhasil ditambahkan');
    }

    public function generateNomor(Request $request)
    {
        $request->validate(['organisasi' => 'required|in:ipnu,ippnu', 'tingkat' => 'required|string', 'kode_indeks' => 'required|string', 'periode' => 'required|string', 'bulan' => 'required|string', 'tahun' => 'required|numeric']);
        return response()->json(['nomor' => NomorSuratHelper::generate($request->organisasi, $request->tingkat, $request->kode_indeks, $request->periode, $request->bulan, $request->tahun)]);
    }

    public function show(SuratTemplate $template)
    {
        return view('admin.surat.template.show', compact('template'));
    }

    public function verifikasi(Request $request)
    {
        $nomorBase64 = $request->query('nomor');
        if (!$nomorBase64) abort(404, 'Token verifikasi surat tidak ditemukan.');

        $nomorBase64 = str_replace(' ', '+', $nomorBase64);
        $nomorSurat = base64_decode($nomorBase64);

        $surat = SuratKeluar::with(['organization', 'creator', 'ditandatanganiKetuaOleh', 'ditandatanganiSekretarisOleh'])->where('nomor_surat', $nomorSurat)->first();
        if (!$surat) return view('public.verifikasi-surat', ['status' => 'palsu', 'nomor' => $nomorSurat]);
        if ($surat->status_validasi !== 'selesai') return view('public.verifikasi-surat', ['status' => 'belum_sah', 'surat' => $surat]);

        $trackingLogs = Activity::where('subject_type', SuratKeluar::class)->where('subject_id', $surat->id)->orderBy('created_at', 'asc')->get();
        return view('public.verifikasi-surat', ['status' => 'asli', 'surat' => $surat, 'logs' => $trackingLogs]);
    }

    public function formLacak()
    {
        return view('public.lacak-surat');
    }
}
