<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\CampaignDonasi;
use App\Models\TransaksiDonasi;
use Illuminate\Http\Request;

class DonasiPublicController extends Controller
{
    // Menampilkan halaman detail campaign dan form donasi untuk publik
    public function show($id)
    {
        $campaign = CampaignDonasi::findOrFail($id);

        // Jika campaign sudah ditutup/dibatalkan, donatur tetap bisa melihat halamannya 
        // tapi form inputnya nanti akan kita sembunyikan di Blade
        return view('public.donasi_public.show', compact('campaign'));
    }

    // Memproses data form ketika donatur mengunggah bukti transfer
    public function store(Request $request, $id)
    {
        $campaign = CampaignDonasi::findOrFail($id);

        // Mencegah donasi jika campaign sudah tidak aktif
        if ($campaign->status != 'aktif') {
            return redirect()->back()->with('error', 'Mohon maaf, program donasi ini sudah ditutup.');
        }

        $request->validate([
            'nama_donatur'      => 'required|string|max:255',
            'nominal'           => 'required|numeric|min:10000', // Minimal donasi 10rb
            'metode_pembayaran' => 'required|in:transfer_bank,qris',
            'bukti_transfer'    => 'required|image|mimes:jpeg,png,jpg|max:2048', // Wajib upload struk
            'pesan_harapan'     => 'nullable|string'
        ]);

        $pathBukti = $request->file('bukti_transfer')->store('bukti_donasi', 'public');

        // Simpan transaksi dengan status PENDING (Menunggu verifikasi Bendahara)
        TransaksiDonasi::create([
            'campaign_donasi_id' => $campaign->id,
            'nama_donatur'       => $request->nama_donatur,
            'nominal'            => $request->nominal,
            'metode_pembayaran'  => $request->metode_pembayaran,
            'bukti_transfer'     => $pathBukti,
            'pesan_harapan'      => $request->pesan_harapan,
            'status_pembayaran'  => 'pending',
        ]);

        return redirect()->back()->with('success', 'Terima kasih, Orang Baik! Bukti donasi Anda telah kami terima dan sedang menunggu verifikasi oleh Bendahara kami.');
    }
}
