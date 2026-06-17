<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Akreditasi;
use App\Models\SuratKeluar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class AkreditasiController extends Controller
{
    // 1. Halaman Index (Tabel Antrean untuk PAC, atau Riwayat untuk Ranting)
    public function index()
    {
        // Masukkan semua variasi teks role agar 100% terdeteksi oleh sistem
        if (auth()->user()->hasRole('super_admin') || auth()->user()->hasAnyRole(['ketua_pac', 'sekretaris_pac', 'Ketua PAC', 'Sekretaris PAC'])) {

            // Jika yang login PAC, tarik SEMUA data pengajuan ranting
            $pengajuans = Akreditasi::withoutGlobalScopes()
                ->with('organization')
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            // Jika yang login Ranting, tarik data miliknya sendiri
            $pengajuans = Akreditasi::with('organization')
                ->where('organization_id', auth()->user()->organization_id)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('admin.akreditasi.index', compact('pengajuans'));
    }

    public function create()
    {
        // 1. Blokir Penilai (Super Admin, Ketua, Sekretaris) agar tidak bisa mengakses form ini
        if (auth()->user()->hasRole('super_admin') || auth()->user()->hasAnyRole(['ketua_pac', 'sekretaris_pac'])) {
            return redirect()->route('akreditasi.index')->with('error', 'Akses Ditolak! Akun PAC/Admin bertugas menilai, bukan mengajukan akreditasi.');
        }

        $suratKeluars = SuratKeluar::where('organization_id', auth()->user()->organization_id)->get();

        // 2. Tampilkan Form Khusus IPNU / IPPNU
        $jenisOrganisasi = strtolower(auth()->user()->organization->jenis_organisasi ?? 'ipnu');

        if ($jenisOrganisasi === 'ippnu') {
            return view('admin.akreditasi.create_ippnu', compact('suratKeluars'));
        } else {
            return view('admin.akreditasi.create_ipnu', compact('suratKeluars'));
        }
    }

    // 3. Proses Simpan Borang dari Ranting
    public function store(Request $request)
    {
        // 1. Validasi Lapis 1 (Berlaku untuk IPNU & IPPNU)
        $request->validate([
            'surat_permohonan_id' => 'required',
            'surat_pernyataan_id' => 'required',
        ]);

        // Cek Jenis Organisasi (Ganti 'jenis' dengan nama kolom asli Anda jika berbeda)
        $jenisOrganisasi = auth()->user()->organization->jenis ?? 'IPNU';

        if ($jenisOrganisasi === 'IPPNU') {
            // ==========================================
            // LOGIKA PENYIMPANAN DATA IPPNU
            // ==========================================

            // --- Susun JSON BAB 1 (Organisasi) ---
            $ippnu_bab1 = [];
            if ($request->has('ippnu_bab1_dokumen')) {
                foreach ($request->ippnu_bab1_dokumen as $index => $dokumen) {
                    if (!empty($dokumen)) {
                        $ippnu_bab1[] = [
                            'dokumen' => $dokumen,
                            'link'    => $request->ippnu_bab1_link[$index] ?? null,
                        ];
                    }
                }
            }

            // --- Susun JSON BAB 2 (Kaderisasi) ---
            $ippnu_bab2 = [];
            if ($request->has('ippnu_bab2_kegiatan')) {
                foreach ($request->ippnu_bab2_kegiatan as $index => $kegiatan) {
                    if (!empty($kegiatan)) {
                        $ippnu_bab2[] = [
                            'kegiatan' => $kegiatan,
                            'tanggal'  => $request->ippnu_bab2_tanggal[$index] ?? null,
                            'link'     => $request->ippnu_bab2_link[$index] ?? null,
                        ];
                    }
                }
            }

            // --- Susun JSON BAB 3 (Kelembagaan) ---
            $ippnu_bab3 = [];
            if ($request->has('ippnu_bab3_program')) {
                foreach ($request->ippnu_bab3_program as $index => $program) {
                    if (!empty($program)) {
                        $ippnu_bab3[] = [
                            'program'   => $program,
                            'realisasi' => $request->ippnu_bab3_realisasi[$index] ?? null,
                            'link'      => $request->ippnu_bab3_link[$index] ?? null,
                        ];
                    }
                }
            }

            // --- Susun JSON BAB 4 (Aswaja) ---
            $ippnu_bab4 = [];
            if ($request->has('ippnu_bab4_kegiatan')) {
                foreach ($request->ippnu_bab4_kegiatan as $index => $kegiatan) {
                    if (!empty($kegiatan)) {
                        $ippnu_bab4[] = [
                            'kegiatan' => $kegiatan,
                            'waktu'    => $request->ippnu_bab4_waktu[$index] ?? null,
                            'link'     => $request->ippnu_bab4_link[$index] ?? null,
                        ];
                    }
                }
            }

            // --- Susun JSON BAB 5 (KPP) ---
            $ippnu_bab5 = [];
            if ($request->has('ippnu_bab5_data')) {
                foreach ($request->ippnu_bab5_data as $index => $data) {
                    if (!empty($data)) {
                        $ippnu_bab5[] = [
                            'data'       => $data,
                            'keterangan' => $request->ippnu_bab5_keterangan[$index] ?? null,
                            'link'       => $request->ippnu_bab5_link[$index] ?? null,
                        ];
                    }
                }
            }

            // --- Susun JSON BAB 6 (Media) ---
            $ippnu_bab6 = [];
            if ($request->has('ippnu_bab6_platform')) {
                foreach ($request->ippnu_bab6_platform as $index => $platform) {
                    if (!empty($platform)) {
                        $ippnu_bab6[] = [
                            'platform' => $platform,
                            'akun'     => $request->ippnu_bab6_akun[$index] ?? null,
                            'link'     => $request->ippnu_bab6_link[$index] ?? null,
                        ];
                    }
                }
            }

            // Simpan ke Database untuk IPPNU
            \App\Models\Akreditasi::create([
                'organization_id'     => auth()->user()->organization_id,
                'jenis_borang'        => 'ippnu',
                'surat_permohonan_id' => $request->surat_permohonan_id,
                'surat_pernyataan_id' => $request->surat_pernyataan_id,
                'kata_pengantar'      => $request->kata_pengantar,
                'deskripsi_singkat'   => $request->deskripsi_singkat,

                'ippnu_bab1_organisasi'  => $ippnu_bab1,
                'ippnu_bab2_kaderisasi'  => $ippnu_bab2,
                'ippnu_bab3_kelembagaan' => $ippnu_bab3,
                'ippnu_bab4_aswaja'      => $ippnu_bab4,
                'ippnu_bab5_kpp'         => $ippnu_bab5,
                'ippnu_bab6_media'       => $ippnu_bab6,

                'status'              => 'Menunggu Penilaian PAC',
                'created_at'          => now(),
            ]);
        } else {
            // ==========================================
            // LOGIKA PENYIMPANAN DATA IPNU
            // ==========================================

            // Validasi file Berita Acara khusus IPNU
            $request->validate([
                'bab5_no_sp'   => 'required|string',
                'bab5_file_ba' => 'required|mimes:pdf|max:5120',
            ]);

            $filePathBA = null;
            if ($request->hasFile('bab5_file_ba')) {
                $file = $request->file('bab5_file_ba');
                $namaFile = auth()->user()->organization_id . '_BA_' . time() . '.pdf';

                // Perhatikan perubahannya: Hapus 'public/' di depan, dan tambahkan 'public' di belakang
                $filePathBA = $file->storeAs('akreditasi/berita_acara', $namaFile, 'public');
            }

            // --- Susun JSON IPNU (BAB 1 - 7) ---
            $bab1_data = [];
            if ($request->has('bab1_kegiatan')) {
                foreach ($request->bab1_kegiatan as $index => $kegiatan) {
                    if (!empty($kegiatan)) {
                        $bab1_data[] = [
                            'kegiatan' => $kegiatan,
                            'tanggal'  => $request->bab1_tanggal[$index] ?? null,
                            'tempat'   => $request->bab1_tempat[$index] ?? null,
                            'peserta'  => $request->bab1_peserta[$index] ?? null,
                            'link'     => $request->bab1_link[$index] ?? null,
                        ];
                    }
                }
            }

            $bab2_data = [];
            if ($request->has('bab2_kegiatan')) {
                foreach ($request->bab2_kegiatan as $index => $kegiatan) {
                    if (!empty($kegiatan)) {
                        $bab2_data[] = [
                            'kegiatan'   => $kegiatan,
                            'tanggal'    => $request->bab2_tanggal[$index] ?? null,
                            'tempat'     => $request->bab2_tempat[$index] ?? null,
                            'narasumber' => $request->bab2_narasumber[$index] ?? null,
                            'peserta'    => $request->bab2_peserta[$index] ?? null,
                            'link'       => $request->bab2_link[$index] ?? null,
                        ];
                    }
                }
            }

            $bab3_data = [];
            if ($request->has('bab3_kegiatan')) {
                foreach ($request->bab3_kegiatan as $index => $kegiatan) {
                    if (!empty($kegiatan)) {
                        $bab3_data[] = [
                            'kegiatan'      => $kegiatan,
                            'tanggal'       => $request->bab3_tanggal[$index] ?? null,
                            'penyelenggara' => $request->bab3_penyelenggara[$index] ?? null,
                            'instruktur'    => $request->bab3_instruktur[$index] ?? null,
                            'link'          => $request->bab3_link[$index] ?? null,
                        ];
                    }
                }
            }

            $bab4_data = [];
            if ($request->has('bab4_nama')) {
                foreach ($request->bab4_nama as $index => $nama) {
                    if (!empty($nama)) {
                        $bab4_data[] = [
                            'nama'    => $nama,
                            'sekolah' => $request->bab4_sekolah[$index] ?? null,
                            'hp'      => $request->bab4_hp[$index] ?? null,
                            'link'    => $request->bab4_link[$index] ?? null,
                        ];
                    }
                }
            }

            $bab6_data = [];
            if ($request->has('bab6_kegiatan')) {
                foreach ($request->bab6_kegiatan as $index => $kegiatan) {
                    if (!empty($kegiatan)) {
                        $bab6_data[] = [
                            'kegiatan'   => $kegiatan,
                            'tanggal'    => $request->bab6_tanggal[$index] ?? null,
                            'tempat'     => $request->bab6_tempat[$index] ?? null,
                            'narasumber' => $request->bab6_narasumber[$index] ?? null,
                            'peserta'    => $request->bab6_peserta[$index] ?? null,
                            'link'       => $request->bab6_link[$index] ?? null,
                        ];
                    }
                }
            }

            $bab7_data = [];
            if ($request->has('bab7_nama')) {
                foreach ($request->bab7_nama as $index => $nama) {
                    if (!empty($nama)) {
                        $bab7_data[] = [
                            'nama'   => $nama,
                            'ttl'    => $request->bab7_ttl[$index] ?? null,
                            'alamat' => $request->bab7_alamat[$index] ?? null,
                            'telp'   => $request->bab7_telp[$index] ?? null,
                            'tahun'  => $request->bab7_tahun[$index] ?? null,
                            'link'   => $request->bab7_link[$index] ?? null,
                        ];
                    }
                }
            }

            // Simpan ke Database untuk IPNU
            Akreditasi::create([
                'organization_id'     => auth()->user()->organization_id,
                'jenis_borang'        => 'ipnu',
                'surat_permohonan_id' => $request->surat_permohonan_id,
                'surat_pernyataan_id' => $request->surat_pernyataan_id,
                'kata_pengantar'      => $request->kata_pengantar,
                'deskripsi_singkat'   => $request->deskripsi_singkat,

                'bab5_no_sp'          => $request->bab5_no_sp,
                'bab5_file_ba'        => $filePathBA ? str_replace('public/', '', $filePathBA) : null,

                'bab1_keaswajaan'     => $bab1_data,
                'bab2_pengkaderan'    => $bab2_data,
                'bab3_instruktur'     => $bab3_data,
                'bab4_pelajar_umum'   => $bab4_data,
                'bab6_sosial'         => $bab6_data,
                'bab7_cbp'            => $bab7_data,

                'status'              => 'Menunggu Penilaian PAC',
                'created_at'          => now(),
            ]);
        }

        return redirect()->route('akreditasi.index')->with('success', 'Borang Akreditasi berhasil diajukan!');
    }


    // 4. Proses Pemberian Nilai oleh PAC (Super Admin)
    // FUNGSI 1: Sekretaris Review (Verifikasi Teknis)
    public function reviewSekretaris(Request $request, $id)
    {
        $akreditasi = Akreditasi::withoutGlobalScopes()->findOrFail($id);

        $akreditasi->update([
            'id_sekretaris'      => auth()->id(),
            'catatan_sekretaris' => $request->catatan_sekretaris,
            'status'             => 'Menunggu Finalisasi Ketua',
        ]);

        return redirect()->back()->with('success', 'Data telah diverifikasi oleh Sekretaris.');
    }

    // FUNGSI 2: Ketua Finalisasi (Keputusan Akhir)
    public function finalisasiKetua(Request $request, $id)
    {
        $akreditasi = Akreditasi::withoutGlobalScopes()->findOrFail($id);

        $akreditasi->update([
            'id_ketua'    => auth()->id(),
            'grade_akhir' => $request->grade_akhir,
            'catatan_pac' => $request->catatan_pac,
            'status'      => 'Selesai Dinilai',
            'dinilai_pada' => now(),
        ]);

        return redirect()->back()->with('success', 'Akreditasi telah disahkan oleh Ketua.');
    }

    public function show($id)
    {
        // Mengambil data akreditasi beserta relasi user (ketua & sekretaris)
        $akreditasi = Akreditasi::withoutGlobalScopes()
            ->with(['organization', 'sekretaris', 'ketua'])
            ->findOrFail($id);

        return view('admin.akreditasi.show', compact('akreditasi'));
    }

    // ==========================================
    // FUNGSI 3: Minta Rekomendasi AI (Khusus Ketua)
    // ==========================================
    public function mintaRekomendasiAI($id)
    {
        $akreditasi = Akreditasi::withoutGlobalScopes()->findOrFail($id);

        // 1. Kumpulkan statistik data borang
        $jmlBab1 = count($akreditasi->bab1_keaswajaan ?? $akreditasi->ippnu_bab1_organisasi ?? []);
        $jmlBab2 = count($akreditasi->bab2_pengkaderan ?? $akreditasi->ippnu_bab2_kaderisasi ?? []);
        $jmlBab3 = count($akreditasi->bab3_instruktur ?? $akreditasi->ippnu_bab3_kelembagaan ?? []);
        $jmlBab4 = count($akreditasi->bab4_pelajar_umum ?? []);
        $jmlBab6 = count($akreditasi->bab6_sosial ?? []);
        $jmlBab7 = count($akreditasi->bab7_cbp ?? []);

        $totalKegiatan = $jmlBab1 + $jmlBab2 + $jmlBab3 + $jmlBab4 + $jmlBab6 + $jmlBab7;

        // 2. Buat Prompt (Instruksi untuk AI)
        $prompt = "Anda adalah Asesor Ahli untuk organisasi IPNU-IPPNU. Saya memiliki Ranting yang mengajukan akreditasi dengan data pencapaian sebagai berikut:
        - Kegiatan Keaswajaan/Organisasi: $jmlBab1 kegiatan
        - Kegiatan Pengkaderan/Kaderisasi: $jmlBab2 kegiatan
        - Kelembagaan/Instruktur: $jmlBab3 kegiatan
        - Total keseluruhan bukti kegiatan/dokumen yang dilampirkan: $totalKegiatan bukti.
        - Catatan evaluasi dari verifikator (Sekretaris): '{$akreditasi->catatan_sekretaris}'
        
        Tugas Anda: Berdasarkan jumlah kegiatan dan catatan sekretaris tersebut, berikan rekomendasi singkat (maksimal 2 paragraf) apakah ranting ini layak mendapat Grade A (Sangat Aktif), Grade B (Aktif), atau Grade C (Cukup). Akhiri dengan satu kalimat kesimpulan format: 'REKOMENDASI GRADE: [A/B/C]'.";

        // 3. Tembak ke API Google Gemini
        try {
            $apiKey = env('GEMINI_API_KEY');

            // --- TAMBAHAN BARU: Kita cek dulu model apa yang tersedia untuk API Key Anda ---
            $cekModels = Http::withoutVerifying()->get('https://generativelanguage.googleapis.com/v1beta/models?key=' . $apiKey);

            if ($cekModels->successful()) {
                $tersedia = collect($cekModels->json('models'))->filter(function ($model) {
                    return in_array('generateContent', $model['supportedGenerationMethods'] ?? []);
                })->first();

                // Ambil nama model yang terdeteksi (misal: "models/gemini-1.5-pro")
                $namaModel = $tersedia['name'] ?? 'models/gemini-1.5-pro';
            } else {
                $namaModel = 'models/gemini-1.5-pro'; // Fallback default
            }
            // -------------------------------------------------------------------------

            // Hapus kata "models/" dari nama model jika ada, agar format URL-nya pas
            $modelClean = str_replace('models/', '', $namaModel);

            // Sekarang kita tembak URL-nya menggunakan model yang sudah PASTI BENAR tersebut
            $response = Http::withoutVerifying()->withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/{$modelClean}:generateContent?key=" . $apiKey, [
                'contents' => [
                    ['parts' => [['text' => $prompt]]]
                ]
            ]);

            // Jika respons dari Google SUKSES
            if ($response->successful()) {
                $result = $response->json();
                $aiText = $result['candidates'][0]['content']['parts'][0]['text'] ?? 'Format balasan AI tidak dikenali.';

                return back()->with('ai_recommendation', $aiText);
            }
            // Jika Google MENOLAK
            else {
                $errorMessage = $response->json('error.message') ?? $response->body();
                return back()->with('error', "Gagal (Model {$modelClean}): " . $errorMessage);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Sistem error: ' . $e->getMessage());
        }
    }
}
