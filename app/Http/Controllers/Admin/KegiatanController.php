<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Kegiatan;
use App\Models\ProgramKerja;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class KegiatanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except([
            'absensiForm',
            'absensiPublicStore'
        ]);
    }

    public function index()
    {
        $kegiatan = Kegiatan::with('programKerja', 'organization')
            ->orderBy('tgl_mulai', 'desc')
            ->paginate(10);
        return view('admin.kegiatan.index', compact('kegiatan'));
    }

    public function create()
    {
        $programKerja = ProgramKerja::where('status', 'active')->get();
        $users = User::orderBy('name')->get();
        return view('admin.kegiatan.create', compact('programKerja', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:200',
            'program_kerja_id' => 'nullable|exists:program_kerja,id',
            'tempat' => 'required|string|max:255',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date|after:tgl_mulai',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:rencana,berlangsung,selesai,batal',
            'ketua_pelaksana_id' => 'nullable|exists:users,id',
            'mode_absensi' => 'required|in:internal,public',
        ]);

        $kegiatan = Kegiatan::create([
            'organization_id' => auth()->user()->organization_id,
            'program_kerja_id' => $request->program_kerja_id,
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi,
            'tempat' => $request->tempat,
            'tgl_mulai' => $request->tgl_mulai,
            'tgl_selesai' => $request->tgl_selesai,
            'status' => $request->status,
            'ketua_pelaksana_id' => $request->ketua_pelaksana_id,
            'mode_absensi' => $request->mode_absensi,
        ]);

        // Generate QR Code untuk absensi
        $this->generateQrCode($kegiatan);

        return redirect()->route('kegiatan.index')->with('success', 'Kegiatan berhasil ditambahkan');
    }

    public function show(Kegiatan $kegiatan)
    {
        $kegiatan->load('programKerja', 'absensi.user');

        $hadir = $kegiatan->absensi->where('status', 'hadir')->count();
        $izin = $kegiatan->absensi->where('status', 'izin')->count();
        $sakit = $kegiatan->absensi->where('status', 'sakit')->count();
        $alpha = $kegiatan->absensi->where('status', 'alpha')->count();
        $total = $hadir + $izin + $sakit + $alpha;

        return view('admin.kegiatan.show', compact('kegiatan', 'hadir', 'izin', 'sakit', 'alpha', 'total'));
    }

    public function edit(Kegiatan $kegiatan)
    {
        $programKerja = ProgramKerja::where('status', 'active')->get();
        $users = User::orderBy('name')->get();
        return view('admin.kegiatan.edit', compact('kegiatan', 'programKerja', 'users'));
    }

    public function update(Request $request, Kegiatan $kegiatan)
    {
        $request->validate([
            'nama' => 'required|string|max:200',
            'program_kerja_id' => 'nullable|exists:program_kerja,id',
            'tempat' => 'required|string|max:255',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date|after:tgl_mulai',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:rencana,berlangsung,selesai,batal',
            'ketua_pelaksana_id' => 'nullable|exists:users,id',
            'mode_absensi' => 'required|in:internal,public',
        ]);

        // Update hanya field yang diizinkan
        $kegiatan->update([
            'nama' => $request->nama,
            'program_kerja_id' => $request->program_kerja_id,
            'tempat' => $request->tempat,
            'tgl_mulai' => $request->tgl_mulai,
            'tgl_selesai' => $request->tgl_selesai,
            'deskripsi' => $request->deskripsi,
            'status' => $request->status,
            'ketua_pelaksana_id' => $request->ketua_pelaksana_id,
            'mode_absensi' => $request->mode_absensi,
        ]);

        // Regenerate QR jika mode berubah? (opsional)
        if ($kegiatan->wasChanged('mode_absensi')) {
            $this->generateQrCode($kegiatan);
        }

        return redirect()->route('kegiatan.index')->with('success', 'Kegiatan berhasil diupdate');
    }

    public function destroy(Kegiatan $kegiatan)
    {
        if ($kegiatan->qr_code && Storage::disk('public')->exists($kegiatan->qr_code)) {
            Storage::disk('public')->delete($kegiatan->qr_code);
        }
        $kegiatan->delete();
        return redirect()->route('kegiatan.index')->with('success', 'Kegiatan berhasil dihapus');
    }

    // Generate QR Code
    private function generateQrCode(Kegiatan $kegiatan)
    {
        // Pakai URL lengkap
        $qrData = url('/absensi/' . $kegiatan->id . '/form');

        $qrFileName = 'kegiatan/qrcode/qrcode_' . $kegiatan->id . '.png';
        $qrPath = storage_path('app/public/' . $qrFileName);

        if (!Storage::disk('public')->exists('kegiatan/qrcode')) {
            Storage::disk('public')->makeDirectory('kegiatan/qrcode');
        }

        QrCode::format('png')->size(300)->generate($qrData, $qrPath);

        if ($kegiatan->qr_code && Storage::disk('public')->exists($kegiatan->qr_code)) {
            Storage::disk('public')->delete($kegiatan->qr_code);
        }

        $kegiatan->qr_code = $qrFileName;
        $kegiatan->save();

        return $qrFileName;
    }

    public function downloadQrCode(Kegiatan $kegiatan)
    {
        if (!$kegiatan->qr_code || !Storage::disk('public')->exists($kegiatan->qr_code)) {
            $this->generateQrCode($kegiatan);
        }
        return response()->download(storage_path('app/public/' . $kegiatan->qr_code));
    }

    // Absensi Methods
    public function scanForm()
    {
        return view('admin.kegiatan.scan');
    }

    public function scanProcess(Request $request)
    {
        $request->validate([
            'qr_data' => 'required|string',
        ]);

        // Extract kegiatan id dari QR data
        $url = $request->qr_data;
        $kegiatanId = basename($url);

        $kegiatan = Kegiatan::findOrFail($kegiatanId);

        // Cek apakah kegiatan masih berlangsung atau belum dimulai
        if ($kegiatan->status == 'batal') {
            return redirect()->back()->with('error', 'Kegiatan ini dibatalkan');
        }

        if ($kegiatan->status == 'selesai') {
            return redirect()->back()->with('error', 'Kegiatan sudah selesai');
        }

        return redirect()->route('absensi.form', $kegiatan);
    }

    public function absensiForm(Kegiatan $kegiatan)
    {
        // Jika mode publik, langsung tampilkan form tanpa login
        if ($kegiatan->mode_absensi == 'public') {
            // Cek apakah sudah absen berdasarkan IP
            $ipAddress = request()->ip();
            $sudahAbsen = Absensi::where('kegiatan_id', $kegiatan->id)
                ->where('ip_address', $ipAddress)
                ->exists();

            return view('admin.kegiatan.absensi-form-public', compact('kegiatan', 'sudahAbsen'));
        }

        // Mode internal: harus login
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login untuk absen');
        }

        $sudahAbsen = Absensi::where('kegiatan_id', $kegiatan->id)
            ->where('user_id', Auth::id())
            ->exists();

        return view('admin.kegiatan.absensi-form', compact('kegiatan', 'sudahAbsen'));
    }

    public function absensiStore(Request $request, Kegiatan $kegiatan)
    {
        $request->validate([
            'status' => 'required|in:hadir,izin,sakit,alpha',
            'keterangan' => 'nullable|string',
        ]);

        $sudahAbsen = Absensi::where('kegiatan_id', $kegiatan->id)
            ->where('user_id', Auth::id())
            ->exists();

        if ($sudahAbsen) {
            return redirect()->route('kegiatan.show', $kegiatan)
                ->with('error', 'Anda sudah melakukan absensi untuk kegiatan ini');
        }

        Absensi::create([
            'kegiatan_id' => $kegiatan->id,
            'user_id' => Auth::id(),
            'waktu_absen' => now(),
            'status' => $request->status,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('kegiatan.show', $kegiatan)
            ->with('success', 'Absensi berhasil dicatat');
    }

    public function absensiPublicStore(Request $request, Kegiatan $kegiatan)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'asal' => 'nullable|string|max:100',
            'no_hp' => 'nullable|string|max:15',
            'status' => 'required|in:hadir,izin,sakit',
        ]);

        // Cek apakah sudah absen dari IP yang sama
        $ipAddress = $request->ip();
        $sudahAbsen = Absensi::where('kegiatan_id', $kegiatan->id)
            ->where('ip_address', $ipAddress)
            ->exists();

        if ($sudahAbsen) {
            return redirect()->route('absensi.form', $kegiatan)
                ->with('error', 'Anda sudah melakukan absensi dari perangkat ini');
        }

        // Simpan ke tabel absensi (user_id = NULL karena publik)
        Absensi::create([
            'kegiatan_id' => $kegiatan->id,
            'user_id' => null, // <-- EXPLICITLY SET NULL
            'nama_peserta' => $request->nama,
            'asal_peserta' => $request->asal,
            'no_hp_peserta' => $request->no_hp,
            'waktu_absen' => now(),
            'status' => $request->status,
            'keterangan' => 'Absen publik',
            'ip_address' => $ipAddress,
        ]);

        return redirect()->route('absensi.form', $kegiatan)
            ->with('success', 'Terima kasih, absensi Anda telah tercatat!');
    }

    public function laporan(Kegiatan $kegiatan)
    {
        $absensi = $kegiatan->absensi()->with('user')->get();
        return view('admin.kegiatan.laporan', compact('kegiatan', 'absensi'));
    }
    public function regenerateQrCode(Kegiatan $kegiatan)
    {
        $this->generateQrCode($kegiatan);
        return redirect()->route('kegiatan.show', $kegiatan)->with('success', 'QR Code berhasil digenerate ulang');
    }
}
