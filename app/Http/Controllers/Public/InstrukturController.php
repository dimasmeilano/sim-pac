<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\MakestaMateri;
use App\Models\MakestaNilai;
use Illuminate\Http\Request;

class InstrukturController extends Controller
{
    // 1. TAMPILKAN FORM LOGIN PIN
    public function loginForm($token)
    {
        $materi = MakestaMateri::with('event')->where('token_rahasia', $token)->firstOrFail();

        return view('public.instruktur.login', compact('materi'));
    }

    // 2. CEK PIN (AUTENTIKASI)
    public function authenticate(Request $request, $token)
    {
        $materi = MakestaMateri::where('token_rahasia', $token)->firstOrFail();

        if ($request->pin == $materi->pin_instruktur) {
            // Beri "Kunci Sesi" di browser instruktur ini
            session(['instruktur_auth_' . $token => true]);

            return redirect()->route('instruktur.penilaian', $token);
        }

        return redirect()->back()->withErrors(['PIN yang Anda masukkan salah.']);
    }

    // 3. TAMPILKAN DAFTAR PESERTA & FORM NILAI
    public function penilaian($token)
    {
        if (!session('instruktur_auth_' . $token)) {
            return redirect()->route('instruktur.login', $token)->withErrors(['Silakan masukkan PIN terlebih dahulu.']);
        }

        $materi = MakestaMateri::with(['event.pesertas' => function ($query) {
            $query->orderBy('nama_lengkap', 'asc');
        }])->where('token_rahasia', $token)->firstOrFail();

        // Tarik data nilai secara utuh, lalu jadikan id peserta sebagai kunci (key)
        $nilai_sebelumnya = MakestaNilai::where('makesta_materi_id', $materi->id)
            ->get()->keyBy('makesta_peserta_id');

        return view('public.instruktur.penilaian', compact('materi', 'nilai_sebelumnya'));
    }

    // 4. SIMPAN SEMUA NILAI KE DATABASE
    public function storePenilaian(Request $request, $token)
    {
        if (!session('instruktur_auth_' . $token)) abort(403);

        $materi = MakestaMateri::where('token_rahasia', $token)->firstOrFail();

        // $request->nilai sekarang bentuknya array 2 dimensi: [peserta_id => [kognitif => 80, keaktifan => 85...]]
        $data_nilai = $request->nilai;

        if ($data_nilai) {
            foreach ($data_nilai as $peserta_id => $data) {
                MakestaNilai::updateOrCreate(
                    ['makesta_materi_id' => $materi->id, 'makesta_peserta_id' => $peserta_id],
                    [
                        'kognitif'    => $data['kognitif'] ?? null,
                        'keaktifan'   => $data['keaktifan'] ?? null,
                        'nilai_akhir' => $data['nilai_akhir'] ?? null,
                        'abjad'       => $data['abjad'] ?? null,
                        'catatan'     => $data['catatan'] ?? null,
                    ]
                );
            }
        }

        return redirect()->back()->with('success', 'Data Observasi & Penilaian berhasil disimpan!');
    }
}
