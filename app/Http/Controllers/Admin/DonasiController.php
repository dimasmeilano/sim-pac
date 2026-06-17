<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CampaignDonasi;
use App\Models\TransaksiDonasi;
use App\Models\Organization; // <-- Wajib ditambahkan
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DonasiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $campaigns = CampaignDonasi::latest()->paginate(10);
        return view('admin.donasi.index', compact('campaigns'));
    }

    public function create()
    {
        // Ambil data organisasi jika Super Admin
        $organizations = auth()->user()->hasRole('super_admin') ? Organization::all() : [];
        return view('admin.donasi.create', compact('organizations'));
    }

    public function store(Request $request)
    {
        $rules = [
            'judul_campaign' => 'required|string|max:255',
            'deskripsi'      => 'required|string',
            'informasi_rekening' => 'required|string',
            'target_donasi'  => 'nullable|numeric|min:0',
            'tgl_mulai'      => 'nullable|date',
            'tgl_selesai'    => 'nullable|date|after_or_equal:tgl_mulai',
            'gambar_banner'  => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status'         => 'required|in:aktif,selesai,dibatalkan'
        ];

        // Validasi ekstra untuk Super Admin
        if (auth()->user()->hasRole('super_admin')) {
            $rules['organization_id'] = 'required|exists:organizations,id';
        }

        $request->validate($rules);

        $data = $request->except(['_token', 'gambar_banner', 'organization_id']);

        // Penentuan ID Organisasi
        $data['organization_id'] = auth()->user()->hasRole('super_admin') ? $request->organization_id : auth()->user()->organization_id;

        if ($request->hasFile('gambar_banner')) {
            $data['gambar_banner'] = $request->file('gambar_banner')->store('campaign_donasi', 'public');
        }

        CampaignDonasi::create($data);

        return redirect()->route('donasi.index')->with('success', 'Campaign Donasi berhasil dibuat!');
    }

    public function show(CampaignDonasi $donasi)
    {
        $donasi->load(['transaksis.alumni', 'transaksis.verifikator']);
        $totalDonatur = $donasi->transaksis()->where('status_pembayaran', 'verified')->count();

        return view('admin.donasi.show', compact('donasi', 'totalDonatur'));
    }

    public function edit(CampaignDonasi $donasi)
    {
        $organizations = auth()->user()->hasRole('super_admin') ? Organization::all() : [];
        return view('admin.donasi.edit', compact('donasi', 'organizations'));
    }

    public function update(Request $request, CampaignDonasi $donasi)
    {
        $rules = [
            'judul_campaign' => 'required|string|max:255',
            'deskripsi'      => 'required|string',
            'informasi_rekening' => 'required|string',
            'target_donasi'  => 'nullable|numeric|min:0',
            'tgl_mulai'      => 'nullable|date',
            'tgl_selesai'    => 'nullable|date|after_or_equal:tgl_mulai',
            'gambar_banner'  => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status'         => 'required|in:aktif,selesai,dibatalkan'
        ];

        if (auth()->user()->hasRole('super_admin')) {
            $rules['organization_id'] = 'required|exists:organizations,id';
        }

        $request->validate($rules);

        $data = $request->except(['_token', '_method', 'gambar_banner', 'organization_id']);

        if (auth()->user()->hasRole('super_admin')) {
            $data['organization_id'] = $request->organization_id;
        }

        if ($request->hasFile('gambar_banner')) {
            if ($donasi->gambar_banner && Storage::disk('public')->exists($donasi->gambar_banner)) {
                Storage::disk('public')->delete($donasi->gambar_banner);
            }
            $data['gambar_banner'] = $request->file('gambar_banner')->store('campaign_donasi', 'public');
        }

        $donasi->update($data);

        return redirect()->route('donasi.index')->with('success', 'Campaign Donasi berhasil diperbarui!');
    }

    public function destroy(CampaignDonasi $donasi)
    {
        if ($donasi->gambar_banner && Storage::disk('public')->exists($donasi->gambar_banner)) {
            Storage::disk('public')->delete($donasi->gambar_banner);
        }

        $donasi->delete();
        return redirect()->route('donasi.index')->with('success', 'Campaign berhasil dihapus!');
    }

    // =========================================================================
    // FITUR VERIFIKASI TRANSAKSI DONASI (Khusus Bendahara)
    // =========================================================================
    public function verifyTransaksi(Request $request, TransaksiDonasi $transaksi)
    {
        $request->validate([
            'status_pembayaran' => 'required|in:verified,rejected'
        ]);

        // LOGIKA OTOMATIS: 
        // Jika statusnya pending dan sekarang diverifikasi, tambahkan nominal ke total terkumpul di Campaign
        if ($transaksi->status_pembayaran == 'pending' && $request->status_pembayaran == 'verified') {
            $transaksi->campaign->increment('terkumpul', $transaksi->nominal);

            // Note: Nanti di sini kita bisa tambahkan logika untuk menyuntikkan data ke Kas (KeuanganController) 
            // agar saldo total organisasi juga bertambah otomatis.
        }

        // Jika statusnya sudah diverifikasi tapi tiba-tiba ditolak/dibatalkan, kurangi uang yang terkumpul
        if ($transaksi->status_pembayaran == 'verified' && $request->status_pembayaran == 'rejected') {
            $transaksi->campaign->decrement('terkumpul', $transaksi->nominal);
        }

        $transaksi->update([
            'status_pembayaran' => $request->status_pembayaran,
            'verified_by' => auth()->id(),
            'verified_at' => now()
        ]);

        return redirect()->back()->with('success', 'Status transaksi donasi berhasil diperbarui.');
    }

    public function storeTransaksi(Request $request, CampaignDonasi $donasi)
    {
        $request->validate([
            'nama_donatur'      => 'required|string|max:255',
            'nominal'           => 'required|numeric|min:1',
            'metode_pembayaran' => 'required|in:tunai,transfer_bank,qris',
            'pesan_harapan'     => 'nullable|string'
        ]);

        // Karena yang menginput ini adalah Bendahara/Super Admin, 
        // kita buat statusnya otomatis "verified" dan saldo langsung bertambah.
        $donasi->transaksis()->create([
            'nama_donatur'      => $request->nama_donatur,
            'nominal'           => $request->nominal,
            'metode_pembayaran' => $request->metode_pembayaran,
            'pesan_harapan'     => $request->pesan_harapan,
            'status_pembayaran' => 'verified',
            'verified_by'       => auth()->id(),
            'verified_at'       => now()
        ]);

        // Tambahkan nominal ke total donasi terkumpul
        $donasi->increment('terkumpul', $request->nominal);

        return redirect()->back()->with('success', 'Transaksi manual berhasil ditambahkan dan otomatis terverifikasi!');
    }
}
