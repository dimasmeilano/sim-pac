<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Klasterisasi;
use Illuminate\Http\Request;
use App\Models\Organization;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB; // Jika Anda belum buat Model Klasterisasi, pakai DB query builder dulu
use Illuminate\Support\Facades\Http;

class KlasterisasiController extends Controller
{
    // ==========================================
    // 1. INDEX: Menampilkan Tabel Data
    // ==========================================
    public function index()
    {
        $user = auth()->user();

        // Jika yang login adalah PAC (punya role pac), tampilkan semua Ranting
        if ($user->hasAnyRole(['ketua_pac', 'sekretaris_pac', 'Ketua PAC', 'Sekretaris PAC'])) {
            // Gunakan withoutGlobalScopes agar PAC bisa lihat data semua ranting
            $data = Klasterisasi::withoutGlobalScopes()->with('organization')->latest()->get();
        }
        // Jika yang login adalah Ranting, tampilkan datanya sendiri saja
        else {
            $data = Klasterisasi::where('organization_id', $user->organization_id)->latest()->get();
        }

        return view('admin.klasterisasi.index', compact('data'));
    }

    // ==========================================
    // 2. CREATE: Form Pengajuan Baru
    // ==========================================
    public function create()
    {
        // LOGIKA PERIODE OTOMATIS
        $tahun = date('Y');
        $bulan = date('m');

        // Jika bulan 1-6 (Jan-Jun), maka masuk periode tahun_lalu - tahun_sekarang
        // Jika bulan 7-12 (Jul-Des), maka masuk periode tahun_sekarang - tahun_depan
        if ($bulan <= 6) {
            $periodeSekarang = ($tahun - 1) . '-' . $tahun;
        } else {
            $periodeSekarang = $tahun . '-' . ($tahun + 1);
        }

        // Blokir jika Ranting sudah pernah mengajukan di periode yang sama
        $sudahAda = \App\Models\Klasterisasi::where('organization_id', auth()->user()->organization_id)
            ->where('periode_penilaian', $periodeSekarang)
            ->first();

        if ($sudahAda) {
            return redirect()->route('klasterisasi.index')
                ->with('warning', 'Anda sudah melakukan pengajuan Klasterisasi untuk periode ' . $periodeSekarang);
        }

        // Kirim variabel periode ke tampilan Blade
        return view('admin.klasterisasi.create', compact('periodeSekarang'));
    }

    // ==========================================
    // 3. STORE: Menyimpan Data Klasterisasi
    // ==========================================
    public function store(Request $request)
    {
        $user = auth()->user();
        $jenis_organisasi = $user->organization->jenis ?? 'ipnu'; // Ambil jenis dari relasi user

        // 1. Validasi Input Dasar (Bagian yang SAMA untuk IPNU & IPPNU)
        $rules = [
            'periode_penilaian'    => 'required|string',
            'dukungan_stakeholder' => 'required|in:lemah,sedang,kuat',
            'kondisi_geografis'    => 'required|in:sulit,sedang,mudah',
            'p1_file_bukti'        => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'p4_file_peta'         => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ];

        // Validasi Input Berbeda
        if ($jenis_organisasi == 'IPNU') {
            $rules['penduduk_muslim']  = 'required|in:0-19,20-59,60-100';
            $rules['jumlah_pesantren'] = 'required|in:kurang_2,2_sampai_3,lebih_3';
        } else {
            $rules['p1_persentase_aktif']  = 'required|numeric';
            $rules['p2_persentase_proker'] = 'required|numeric';
        }

        $request->validate($rules);

        // 2. Kalkulasi Skor Parameter 1 dan 2
        $skor_p1 = 0;
        $skor_p2 = 0;

        if ($jenis_organisasi == 'IPNU') {
            // Skor IPNU (Pilihan Dropdown)
            $skor_p1 = match ($request->penduduk_muslim) {
                '60-100' => 25,
                '20-59' => 10,
                '0-19' => 5,
            };
            $skor_p2 = match ($request->jumlah_pesantren) {
                'lebih_3' => 25,
                '2_sampai_3' => 10,
                'kurang_2' => 5,
            };
        } else {
            // Skor IPPNU (Matriks Persentase)
            $p1 = $request->p1_persentase_aktif;
            if ($p1 >= 86) $skor_p1 = 25;
            elseif ($p1 >= 61) $skor_p1 = 20;
            elseif ($p1 >= 31) $skor_p1 = 15;
            else $skor_p1 = 5;

            $p2 = $request->p2_persentase_proker;
            if ($p2 >= 75) $skor_p2 = 25;
            elseif ($p2 >= 46) $skor_p2 = 15;
            else $skor_p2 = 5;
        }

        // 3. Kalkulasi Skor Parameter 3 dan 4 (Dipakai Keduanya)
        $skor_stakeholder = match ($request->dukungan_stakeholder) {
            'kuat' => 25,
            'sedang' => 10,
            'lemah' => 5,
        };

        $skor_geografis = match ($request->kondisi_geografis) {
            'mudah' => 20,
            'sedang' => 10,
            'sulit' => 5,
        };

        // 4. Hitung Total dan Tentukan Kluster
        $total_skor = $skor_p1 + $skor_p2 + $skor_stakeholder + $skor_geografis;

        if ($total_skor >= 80) $kluster = 1;
        elseif ($total_skor >= 40) $kluster = 2;
        else $kluster = 3;

        // 5. Penanganan Upload File
        $pathBps = null;
        $pathPeta = null;

        if ($request->hasFile('p1_file_bukti')) {
            $pathBps = $request->file('p1_file_bukti')->store('klasterisasi/bps', 'public');
        }

        if ($request->hasFile('p4_file_peta')) {
            $pathPeta = $request->file('p4_file_peta')->store('klasterisasi/peta', 'public');
        }

        // 6. Pembersih Array Kosong
        $tabel_lembaga   = $request->p2_tabel_lembaga ? array_filter($request->p2_tabel_lembaga, fn($item) => !empty($item['nama'])) : null;
        $tabel_pesantren = $request->p2_tabel_pesantren ? array_filter($request->p2_tabel_pesantren, fn($item) => !empty($item['nama'])) : null;
        $tabel_mou       = $request->p3_tabel_mou ? array_filter($request->p3_tabel_mou, fn($item) => !empty($item['kegiatan'])) : null;
        $struktur_alumni = $request->p3_struktur_alumni ? array_filter($request->p3_struktur_alumni, fn($item) => !empty($item['nama'])) : null;
        $kegiatan_alumni = $request->p3_kegiatan_alumni ? array_filter($request->p3_kegiatan_alumni, fn($item) => !empty($item['kegiatan'])) : null;

        // Pembersih Array IPPNU
        $tabel_pimpinan = $request->p1_tabel_pimpinan ? array_filter($request->p1_tabel_pimpinan, fn($item) => !empty($item['nama'])) : null;
        $tabel_proker   = $request->p2_tabel_proker ? array_filter($request->p2_tabel_proker, fn($item) => !empty($item['nama_proker'])) : null;

        // 7. Simpan atau Update ke Database
        Klasterisasi::updateOrCreate(
            [
                'organization_id'   => $user->organization_id,
                'periode_penilaian' => $request->periode_penilaian
            ],
            [
                'jenis_organisasi'     => $jenis_organisasi,

                // Parameter 1 & 2 (Kolom IPNU)
                'penduduk_muslim'      => $request->penduduk_muslim,
                'jumlah_pesantren'     => $request->jumlah_pesantren,
                'p2_tabel_lembaga'     => $tabel_lembaga,
                'p2_tabel_pesantren'   => $tabel_pesantren,

                // Parameter 1 & 2 (Kolom IPPNU)
                'p1_tabel_pimpinan'    => $tabel_pimpinan,
                'p1_persentase_aktif'  => $request->p1_persentase_aktif,
                'p2_tabel_proker'      => $tabel_proker,
                'p2_persentase_proker' => $request->p2_persentase_proker,

                // File Parameter 1
                'p1_file_bukti'        => $pathBps ?? $request->old_p1_file_bukti,
                'p1_link_bps'          => $request->p1_link_bps,

                // Parameter 3 (Shared)
                'dukungan_stakeholder' => $request->dukungan_stakeholder,
                'p3_tabel_mou'         => $tabel_mou,
                'p3_struktur_alumni'   => $struktur_alumni,
                'p3_kegiatan_alumni'   => $kegiatan_alumni,

                // Parameter 4 (Shared)
                'kondisi_geografis'    => $request->kondisi_geografis,
                'p4_file_peta'         => $pathPeta ?? $request->old_p4_file_peta,
                'p4_infrastruktur'     => $request->p4_infrastruktur,
                'p4_transportasi'      => $request->p4_transportasi,

                // Skor Akhir (Kolom Skor Digabung)
                'skor_penduduk'        => $skor_p1,
                'skor_pesantren'       => $skor_p2,
                'skor_stakeholder'     => $skor_stakeholder,
                'skor_geografis'       => $skor_geografis,
                'total_skor'           => $total_skor,
                'kluster'              => $kluster,

                // Status awal verifikasi
                'status'               => 'Menunggu Review Sekretaris',
            ]
        );

        return redirect()->route('klasterisasi.index')
            ->with('success', "Data Klasterisasi $jenis_organisasi periode {$request->periode_penilaian} berhasil diajukan!");
    }

    public function show($id)
    {
        // Mengambil data dengan relasi organisasi
        $klasterisasi = Klasterisasi::withoutGlobalScopes()
            ->with('organization')
            ->findOrFail($id);

        // Tambahan kecil: Memastikan jenis organisasi terisi jika di DB kosong
        if (empty($klasterisasi->jenis_organisasi)) {
            $klasterisasi->jenis_organisasi = $klasterisasi->organization->jenis ?? 'ipnu';
        }

        return view('admin.klasterisasi.show', compact('klasterisasi'));
    }

    // ==========================================
    // 4. VERIFIKASI LAPIS 1: Oleh Sekretaris
    // ==========================================
    public function reviewSekretaris(Request $request, $id)
    {
        $klasterisasi = Klasterisasi::withoutGlobalScopes()->findOrFail($id);

        $request->validate([
            'catatan_sekretaris' => 'required|string',
            'keputusan'          => 'required|in:Lanjut,Revisi'
        ]);

        $statusBaru = ($request->keputusan == 'Revisi') ? 'Revisi' : 'Menunggu Finalisasi Ketua';

        $klasterisasi->update([
            'id_sekretaris'      => auth()->id(),
            'catatan_sekretaris' => $request->catatan_sekretaris,
            'status'             => $statusBaru,
        ]);

        return redirect()->back()->with('success', 'Dokumen klasterisasi telah diperiksa dan diteruskan!');
    }

    // ==========================================
    // 5. VERIFIKASI LAPIS 2: Oleh Ketua PAC
    // ==========================================
    public function finalisasiKetua(Request $request, $id)
    {
        $klasterisasi = Klasterisasi::withoutGlobalScopes()->findOrFail($id);

        // Validasi input dari Ketua PAC
        $request->validate([
            'penduduk_muslim'      => 'required|in:0-19,20-59,60-100',
            'jumlah_pesantren'     => 'required|in:kurang_2,2_sampai_3,lebih_3',
            'dukungan_stakeholder' => 'required|in:lemah,sedang,kuat',
            'kondisi_geografis'    => 'required|in:sulit,sedang,mudah',
            'catatan_ketua'        => 'required|string',
        ]);

        // Hitung ulang skor berdasarkan PENETAPAN KETUA
        $skor_penduduk = match ($request->penduduk_muslim) {
            '60-100' => 25,
            '20-59' => 10,
            '0-19' => 5,
        };

        $skor_pesantren = match ($request->jumlah_pesantren) {
            'lebih_3' => 25,
            '2_sampai_3' => 10,
            'kurang_2' => 5,
        };

        $skor_stakeholder = match ($request->dukungan_stakeholder) {
            'kuat' => 25,
            'sedang' => 10,
            'lemah' => 5,
        };

        $skor_geografis = match ($request->kondisi_geografis) {
            'mudah' => 20,
            'sedang' => 10,
            'sulit' => 5,
        };

        $total_skor = $skor_penduduk + $skor_pesantren + $skor_stakeholder + $skor_geografis;

        // Tentukan kluster final
        if ($total_skor >= 80) $kluster = 1;
        elseif ($total_skor >= 40) $kluster = 2;
        else $kluster = 3;

        // Update database dengan nilai fiks dari Ketua
        $klasterisasi->update([
            'penduduk_muslim'      => $request->penduduk_muslim,
            'jumlah_pesantren'     => $request->jumlah_pesantren,
            'dukungan_stakeholder' => $request->dukungan_stakeholder,
            'kondisi_geografis'    => $request->kondisi_geografis,
            'skor_penduduk'        => $skor_penduduk,
            'skor_pesantren'       => $skor_pesantren,
            'skor_stakeholder'     => $skor_stakeholder,
            'skor_geografis'       => $skor_geografis,
            'total_skor'           => $total_skor,
            'kluster'              => $kluster,
            'id_ketua'             => auth()->id(),
            'catatan_ketua'        => $request->catatan_ketua,
            'status'               => 'Selesai', // Selesai & Terkunci
        ]);

        return redirect()->back()->with('success', "Sah! Klasterisasi ditetapkan pada Kluster $kluster dengan Total Skor $total_skor.");
    }

    public function mintaRekomendasiAI($id)
    {
        $klasterisasi = \App\Models\Klasterisasi::withoutGlobalScopes()->findOrFail($id);

        // 1. Kumpulkan data bukti riil (hitung jumlah baris tabel)
        $jmlLembaga   = count($klasterisasi->p2_tabel_lembaga ?? []);
        $jmlPesantren = count($klasterisasi->p2_tabel_pesantren ?? []);
        $totalLembaga = $jmlLembaga + $jmlPesantren;

        $jmlMou       = count($klasterisasi->p3_tabel_mou ?? []);
        $jmlKegiatan  = count($klasterisasi->p3_kegiatan_alumni ?? []);

        $infrastruktur = $klasterisasi->p4_infrastruktur ?? 'Tidak ada penjelasan rinci.';
        $transportasi  = $klasterisasi->p4_transportasi ?? 'Tidak ada penjelasan rinci.';

        // 2. Buat Prompt Analisis untuk AI
        $prompt = "Anda adalah Asesor Ahli Organisasi. Ranting ini mengajukan Klasterisasi dengan klaim berikut:
        - Klaim Parameter 2 (Lembaga): Memilih kategori '{$klasterisasi->jumlah_pesantren}'.
          Faktanya, mereka hanya melampirkan {$totalLembaga} data lembaga/pesantren di tabel bukti.
        - Klaim Parameter 3 (Stakeholder): Memilih tingkat dukungan '{$klasterisasi->dukungan_stakeholder}'.
          Faktanya, mereka melampirkan {$jmlMou} dokumen MOU dan {$jmlKegiatan} kegiatan alumni.
        - Klaim Parameter 4 (Geografis): Memilih akses '{$klasterisasi->kondisi_geografis}'.
          Faktanya, deskripsi infrastrukturnya: '{$infrastruktur}'. Transportasi: '{$transportasi}'.

        Berdasarkan perbandingan klaim vs fakta di atas, berikan rekomendasi singkat untuk Ketua PAC (maksimal 2 paragraf). Apakah klaim ranting ini bisa dipercaya, atau Ketua PAC harus menurunkan nilainya? Akhiri dengan kesimpulan: 'REKOMENDASI: [Setujui Klaim / Turunkan Nilai]'.";

        // 3. Tembak ke API Google Gemini (Dengan deteksi model otomatis seperti sebelumnya)
        try {
            $apiKey = env('GEMINI_API_KEY');

            // Cek ketersediaan model
            $cekModels = Http::withoutVerifying()->get('https://generativelanguage.googleapis.com/v1beta/models?key=' . $apiKey);
            $namaModel = 'models/gemini-1.5-pro'; // Default fallback

            if ($cekModels->successful()) {
                $tersedia = collect($cekModels->json('models'))->filter(function ($model) {
                    return in_array('generateContent', $model['supportedGenerationMethods'] ?? []);
                })->first();
                $namaModel = $tersedia['name'] ?? $namaModel;
            }
            $modelClean = str_replace('models/', '', $namaModel);

            // Tembak API Utama
            $response = Http::withoutVerifying()->withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/{$modelClean}:generateContent?key=" . $apiKey, [
                'contents' => [['parts' => [['text' => $prompt]]]]
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $aiText = $result['candidates'][0]['content']['parts'][0]['text'] ?? 'AI tidak memberikan balasan.';
                return back()->with('ai_recommendation', $aiText);
            } else {
                $errorMessage = $response->json('error.message') ?? $response->body();
                return back()->with('error', "Gagal (Model {$modelClean}): " . $errorMessage);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Sistem error: ' . $e->getMessage());
        }
    }

    public function cetakSertifikat($id)
    {
        $klasterisasi = Klasterisasi::withoutGlobalScopes()->findOrFail($id);

        // Mengatur orientasi kertas ke A4 Portrait
        $pdf = Pdf::loadView('admin.klasterisasi.sertifikat', compact('klasterisasi'))
            ->setPaper('a4', 'potrait');

        return $pdf->stream('Sertifikat_Klasterisasi_' . $klasterisasi->organization->nama . '.pdf');
    }
}
