<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\MakestaEvaluasi;
use App\Models\MakestaEvent;
use App\Models\MakestaPeserta;
use Illuminate\Http\Request;

class PesertaEvaluasiController extends Controller
{
    // 1. TAMPILKAN HALAMAN LOGIN PESERTA
    public function login($event_id)
    {
        $event = MakestaEvent::with('pesertas')->findOrFail($event_id);
        return view('public.peserta.login', compact('event'));
    }

    // 2. CEK NOMOR WA SEBAGAI PASSWORD
    public function authenticate(Request $request, $event_id)
    {
        $peserta = MakestaPeserta::where('id', $request->peserta_id)
            ->where('no_wa', $request->no_wa)
            ->first();

        if ($peserta) {
            // Beri akses ke session
            session(['peserta_auth_' . $event_id => $peserta->id]);
            return redirect()->route('peserta.evaluasi.form', $event_id);
        }

        return redirect()->back()->withErrors(['Nomor WhatsApp tidak cocok dengan data pendaftaran!']);
    }

    // 3. TAMPILKAN FORM EVALUASI DIGITAL
    // 3. TAMPILKAN FORM EVALUASI DIGITAL (VERSI OTOMATIS)
    public function form($event_id)
    {
        if (!session('peserta_auth_' . $event_id)) {
            return redirect()->route('peserta.evaluasi.login', $event_id)->withErrors(['Silakan login dahulu.']);
        }

        $peserta_id = session('peserta_auth_' . $event_id);
        $peserta = MakestaPeserta::findOrFail($peserta_id);
        $event = MakestaEvent::with('materis')->findOrFail($event_id);

        // --- SISTEM OTOMATIS: HITUNG HARI KE- ---
        // Catatan: Ganti 'tgl_mulai' dengan nama kolom tanggal pelaksanaan di database Anda
        $tanggal_mulai = \Carbon\Carbon::parse($event->tgl_mulai)->startOfDay();
        $hari_ini = \Carbon\Carbon::now()->startOfDay();

        // Selisih hari (nilai minus berarti sebelum Hari H)
        $hari_ke = $tanggal_mulai->diffInDays($hari_ini, false) + 1;

        // JIKA BELUM HARI H: Blokir dan kembalikan ke halaman login
        if ($hari_ke < 1) {
            return redirect()->route('peserta.evaluasi.login', $event_id)
                ->withErrors(['Mohon maaf, evaluasi belum bisa diisi karena kegiatan Makesta belum dimulai.']);
        }

        // Jika peserta membuka link sebelum hari H, tetapkan sebagai Hari ke-1
        if ($hari_ke < 1) {
            $hari_ke = 1;
        }

        // --- SISTEM OTOMATIS: FILTER MATERI HARI INI SAJA ---
        $materi_hari_ini = $event->materis->filter(function ($materi) use ($hari_ini) {
            // Catatan: Ganti 'waktu_materi' dengan nama kolom jadwal materi Anda
            return \Carbon\Carbon::parse($materi->waktu_materi)->startOfDay()->equalTo($hari_ini);
        });

        // Cek apakah hari ini sudah mengisi
        $sudah_mengisi = MakestaEvaluasi::where('makesta_peserta_id', $peserta_id)
            ->where('hari_ke', $hari_ke)
            ->exists();

        return view('public.peserta.form', compact('event', 'peserta', 'hari_ke', 'materi_hari_ini', 'sudah_mengisi'));
    }

    // 4. SIMPAN DATA EVALUASI
    public function store(Request $request, $event_id)
    {
        $peserta_id = session('peserta_auth_' . $event_id);
        if (!$peserta_id) abort(403);

        $hari_ke = $request->hari_ke; // Menangkap inputan hari dari peserta

        // Simpan Evaluasi Pemateri (bisa banyak pemateri dalam 1 hari)
        if ($request->pemateri) {
            MakestaEvaluasi::updateOrCreate(
                ['makesta_peserta_id' => $peserta_id, 'tipe_evaluasi' => 'pemateri', 'hari_ke' => $hari_ke],
                ['data_evaluasi' => $request->pemateri]
            );
        }

        // Simpan Evaluasi Panitia hari itu
        if ($request->panitia) {
            MakestaEvaluasi::updateOrCreate(
                ['makesta_peserta_id' => $peserta_id, 'tipe_evaluasi' => 'panitia', 'hari_ke' => $hari_ke],
                ['data_evaluasi' => $request->panitia]
            );
        }

        // Simpan Evaluasi Instruktur hari itu
        if ($request->instruktur) {
            MakestaEvaluasi::updateOrCreate(
                ['makesta_peserta_id' => $peserta_id, 'tipe_evaluasi' => 'instruktur', 'hari_ke' => $hari_ke],
                ['data_evaluasi' => $request->instruktur]
            );
        }

        // Simpan Refleksi Harian
        if ($request->refleksi) {
            MakestaEvaluasi::updateOrCreate(
                ['makesta_peserta_id' => $peserta_id, 'tipe_evaluasi' => 'refleksi', 'hari_ke' => $hari_ke],
                ['data_evaluasi' => $request->refleksi]
            );
        }

        return redirect()->route('peserta.evaluasi.form', $event_id)->with('success', 'Evaluasi berhasil disimpan!');
    }
}
