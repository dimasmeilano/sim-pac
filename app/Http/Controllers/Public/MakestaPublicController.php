<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\MakestaEvent;
use App\Models\MakestaPeserta;
use Illuminate\Http\Request;

class MakestaPublicController extends Controller
{
    // 1. Menampilkan Halaman Formulir Pendaftaran Publik
    public function daftar($id)
    {
        $event = MakestaEvent::with('organization')->findOrFail($id);

        // KUNCI: Tolak akses jika Event masih "Menunggu Verifikasi" atau sudah selesai
        if (!in_array($event->status, ['Disetujui', 'Berjalan'])) {
            abort(403, 'Mohon Maaf, pendaftaran untuk kegiatan Makesta ini belum dibuka atau sudah ditutup.');
        }

        return view('public.makesta.daftar', compact('event'));
    }

    // 2. Menyimpan Data Peserta Baru
    public function storePeserta(Request $request, $id)
    {
        $event = MakestaEvent::findOrFail($id);

        // Validasi inputan peserta
        $request->validate([
            'nama_lengkap'  => 'required|string|max:255',
            'tempat_lahir'  => 'required|string|max:255',
            'tgl_lahir'     => 'required|date',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'no_wa'         => 'required|string|max:20',
            'alamat'        => 'required|string',
            'utusan'        => 'required|string|max:255',
            'berkas_syarat' => 'nullable|mimes:pdf,jpg,jpeg,png|max:2048', // Boleh gambar atau PDF (Maks 2MB)
        ]);

        $data = $request->except('berkas_syarat');
        $data['makesta_event_id'] = $event->id; // Sambungkan ke event ini

        // Proses unggah berkas (jika ada)
        if ($request->hasFile('berkas_syarat')) {
            $file = $request->file('berkas_syarat');
            // Penamaan file yang rapi: Waktu_Syarat_NamaPeserta.ekstensi
            $filename = time() . '_Syarat_' . str_replace(' ', '', $request->nama_lengkap) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('makesta/peserta', $filename, 'public');
            $data['berkas_syarat'] = $path;
        }

        MakestaPeserta::create($data);

        return redirect()->back()->with('success', 'Alhamdulillah! Pendaftaran Anda berhasil. Silakan tunggu informasi selanjutnya dari Panitia melalui nomor WhatsApp Anda.');
    }
}
