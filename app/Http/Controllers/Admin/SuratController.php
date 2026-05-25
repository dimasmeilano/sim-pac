<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\NomorSuratHelper;
use App\Http\Controllers\Controller;
use App\Models\SuratKeluar;
use App\Models\SuratMasuk;
use App\Models\SuratTemplate;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;

class SuratController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:manage_surat');
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
        $templates = SuratTemplate::where('status', 'aktif')->get();
        return view('admin.surat.keluar.create', compact('templates'));
    }

    public function keluarStore(Request $request)
    {
        $request->validate([
            'nomor_surat' => 'required|string|unique:surat_keluar,nomor_surat',
            'template_id' => 'nullable|exists:surat_templates,id',
            'perihal' => 'required|string|max:200',
            'tujuan' => 'required|string',
            'tanggal_surat' => 'required|date',
            'isi_surat' => 'required|string',
            'lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $lampiranPath = null;
        if ($request->hasFile('lampiran')) {
            $file = $request->file('lampiran');
            $filename = 'lampiran_' . time() . '_' . $file->getClientOriginalName();
            $lampiranPath = $file->storeAs('surat/lampiran', $filename, 'public');
        }

        $suratKeluar = SuratKeluar::create([
            'organization_id' => auth()->user()->organization_id,
            'template_id' => $request->template_id,
            'nomor_surat' => $request->nomor_surat,
            'perihal' => $request->perihal,
            'tujuan' => $request->tujuan,
            'isi_surat' => $request->isi_surat,
            'lampiran' => $lampiranPath,
            'status' => 'selesai',
            'created_by' => auth()->id(),
            'created_at' => $request->tanggal_surat,
        ]);

        return redirect()->route('surat.keluar.show', $suratKeluar)
            ->with('success', 'Data surat manual berhasil ditambahkan ke arsip');
    }

    public function keluarShow(SuratKeluar $suratKeluar)
    {
        $suratKeluar->load('organization', 'creator', 'signer', 'template');
        $isiSurat = $suratKeluar->isi_surat;
        $org = $suratKeluar->organization;

        // 1. Ganti variabel teks biasa
        $isiSurat = str_replace('{nomor_surat}', $suratKeluar->nomor_surat, $isiSurat);

        if ($org) {
            $jenisOrg = strtolower($org->jenis_organisasi ?? 'ipnu');
            $isiSurat = str_replace('{jenis_organisasi_upper}', strtoupper($jenisOrg), $isiSurat);
            $namaOrgLower = ($jenisOrg == 'ipnu') ? "Ikatan Pelajar Nahdlatul Ulama'" : "Ikatan Pelajar Putri Nahdlatul Ulama'";
            $isiSurat = str_replace('{nama_organisasi_lower}', $namaOrgLower, $isiSurat);
            $namaOrgLengkapBaris2 = ($jenisOrg == 'ipnu') ? "IKATAN PELAJAR NAHDLATUL ULAMA" : "IKATAN PELAJAR PUTRI NAHDLATUL ULAMA";
            $isiSurat = str_replace('{nama_organisasi_lengkap_baris2}', $namaOrgLengkapBaris2, $isiSurat);
        }

        // 2. LOGIKA MENAMPILKAN GAMBAR TANDA TANGAN & STEMPEL DI WEB
        // TTD Ketua
        $ttdKetuaHtml = ($suratKeluar->status_validasi == 'selesai' && $org && $org->ttd_ketua)
            ? '<img src="' . $this->convertToBase64(storage_path('app/public/' . $org->ttd_ketua)) . '" style="max-height: 60px;">'
            : '';

        // TTD Sekretaris
        $ttdSekretarisHtml = (in_array($suratKeluar->status_validasi, ['menunggu_ttd_ketua', 'selesai']) && $org && $org->ttd_sekretaris)
            ? '<img src="' . $this->convertToBase64(storage_path('app/public/' . $org->ttd_sekretaris)) . '" style="max-height: 60px;">'
            : '';

        // Stempel
        $stempelHtml = ($suratKeluar->status_validasi == 'selesai' && $org && $org->stempel)
            ? '<img src="' . $this->convertToBase64(storage_path('app/public/' . $org->stempel)) . '" style="max-height: 85px;">'
            : '';

        // Eksekusi Replace
        $isiSurat = str_replace(
            ['[TTD_KETUA]', '[TTD_SEKRETARIS]', '[STEMPEL]'],
            [$ttdKetuaHtml, $ttdSekretarisHtml, $stempelHtml],
            $isiSurat
        );

        // 3. Kembalikan string yang sudah disisipi gambar ke dalam objek sebelum dilempar ke View Blade
        $suratKeluar->isi_surat = $isiSurat;

        return view('admin.surat.keluar.show', compact('suratKeluar'));
    }

    public function keluarEdit(SuratKeluar $suratKeluar)
    {
        if ($suratKeluar->status != 'draft') {
            return redirect()->route('surat.keluar.index')->with('error', 'Surat sudah diproses, tidak bisa diedit');
        }

        $templates = SuratTemplate::where('jenis', 'keluar')->where('status', 'aktif')->get();
        $users = User::orderBy('name')->get();
        return view('admin.surat.keluar.edit', compact('suratKeluar', 'templates', 'users'));
    }

    public function keluarUpdate(Request $request, SuratKeluar $suratKeluar)
    {
        if ($suratKeluar->status != 'draft') {
            return redirect()->route('surat.keluar.index')->with('error', 'Surat sudah diproses, tidak bisa diedit');
        }

        $request->validate([
            'template_id' => 'nullable|exists:surat_templates,id',
            'nomor_surat' => 'required|string|max:100|unique:surat_keluar,nomor_surat,' . $suratKeluar->id,
            'perihal' => 'required|string|max:200',
            'tujuan' => 'required|string',
            'isi_surat' => 'required|string',
            'lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'status' => 'required|in:draft,menunggu_ttd',
        ]);

        if ($request->hasFile('lampiran')) {
            if ($suratKeluar->lampiran && Storage::disk('public')->exists($suratKeluar->lampiran)) {
                Storage::disk('public')->delete($suratKeluar->lampiran);
            }
            $file = $request->file('lampiran');
            $filename = time() . '_' . $file->getClientOriginalName();
            $suratKeluar->lampiran = $file->storeAs('surat/lampiran', $filename, 'public');
        }

        $suratKeluar->update([
            'template_id' => $request->template_id,
            'nomor_surat' => $request->nomor_surat,
            'perihal' => $request->perihal,
            'tujuan' => $request->tujuan,
            'isi_surat' => $request->isi_surat,
            'status' => $request->status,
        ]);

        return redirect()->route('surat.keluar.index')->with('success', 'Surat keluar berhasil diupdate');
    }

    public function keluarDestroy(SuratKeluar $suratKeluar)
    {
        if ($suratKeluar->lampiran && Storage::disk('public')->exists($suratKeluar->lampiran)) {
            Storage::disk('public')->delete($suratKeluar->lampiran);
        }
        $suratKeluar->delete();
        return redirect()->route('surat.keluar.index')->with('success', 'Surat keluar berhasil dihapus');
    }

    // ==========================================
    // BAGIAN 2: ALUR VALIDASI & TANDA TANGAN 
    // ==========================================

    public function ajukanValidasi(Request $request, SuratKeluar $surat)
    {
        $user = auth()->user();

        if ($surat->created_by != $user->id) {
            return back()->with('error', 'Hanya pembuat surat yang dapat mengajukan validasi');
        }
        if ($surat->status_validasi != 'draft') {
            return back()->with('error', 'Surat sudah diajukan sebelumnya');
        }

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

    public function validasiWakil(Request $request, SuratKeluar $surat)
    {
        $user = auth()->user();

        if ($surat->divalidasi_oleh != $user->id) {
            return back()->with('error', 'Anda tidak ditunjuk untuk memvalidasi surat ini');
        }
        if ($surat->status_validasi != 'menunggu_validasi_wakil') {
            return back()->with('error', 'Surat tidak dalam status menunggu validasi');
        }

        $request->validate([
            'status' => 'required|in:disetujui,ditolak',
            'catatan' => 'nullable|string',
        ]);

        if ($request->status == 'disetujui') {
            $surat->update(['status_validasi' => 'menunggu_ttd_sekretaris', 'tanggal_validasi' => now()]);
            $message = 'Surat disetujui. Menunggu tanda tangan Sekretaris.';
        } else {
            $surat->update(['status_validasi' => 'ditolak', 'catatan_validasi' => $request->catatan]);
            $message = 'Surat ditolak.';
        }

        return redirect()->route('surat.keluar.show', $surat)->with('success', $message);
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
        $isiSuratHtml = $suratKeluar->isi_surat;

        // TTD Ketua (Gunakan storage_path agar aman)
        $ttdKetuaHtml = ($suratKeluar->status_validasi == 'selesai' && $org && $org->ttd_ketua)
            ? '<img src="' . $this->convertToBase64(storage_path('app/public/' . $org->ttd_ketua)) . '" style="max-height: 60px;">'
            : '';

        // TTD Sekretaris (Gunakan storage_path)
        $ttdSekretarisHtml = (in_array($suratKeluar->status_validasi, ['menunggu_ttd_ketua', 'selesai']) && $org && $org->ttd_sekretaris)
            ? '<img src="' . $this->convertToBase64(storage_path('app/public/' . $org->ttd_sekretaris)) . '" style="max-height: 60px;">'
            : '';

        // Stempel (Gunakan storage_path)
        $stempelHtml = ($suratKeluar->status_validasi == 'selesai' && $org && $org->stempel)
            ? '<img src="' . $this->convertToBase64(storage_path('app/public/' . $org->stempel)) . '" style="max-height: 85px;">'
            : '';

        // PERBAIKAN: Replace placeholder di HTML menggunakan KURUNG SIKU [...]
        $isiSuratHtml = str_replace(
            ['[TTD_KETUA]', '[TTD_SEKRETARIS]', '[STEMPEL]'],
            [$ttdKetuaHtml, $ttdSekretarisHtml, $stempelHtml],
            $isiSuratHtml
        );

        // Gunakan clone agar data database aman
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

        // Gabung Lampiran
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
}
