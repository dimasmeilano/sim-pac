<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Kegiatan;
use App\Models\ProgramKerja;
use App\Models\User;
use App\Models\Organization;
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
        $query = Kegiatan::with('programKerja', 'organization');
        $user = auth()->user();

        if (!$user->hasRole('super_admin')) {
            $query->where('organization_id', $user->organization_id);
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

        $kegiatan = $query->orderBy('tgl_mulai', 'desc')->paginate(10);
        return view('admin.kegiatan.index', compact('kegiatan'));
    }

    public function create()
    {
        // 1. Super Admin butuh daftar organisasi
        $organizations = auth()->user()->hasRole('super_admin') ? Organization::all() : [];

        // 2. Filter Progja berdasarkan organisasi (kecuali Super Admin)
        $progjaQuery = ProgramKerja::where('status', 'active');
        if (!auth()->user()->hasRole('super_admin')) {
            $progjaQuery->where('organization_id', auth()->user()->organization_id);
        }
        $programKerja = $progjaQuery->get();

        // 3. Filter User untuk Ketua Pelaksana
        $userQuery = User::orderBy('name');
        if (!auth()->user()->hasRole('super_admin')) {
            $userQuery->where('organization_id', auth()->user()->organization_id);
        }
        $users = $userQuery->get();

        return view('admin.kegiatan.create', compact('programKerja', 'users', 'organizations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:200',
            'program_kerja_id' => 'nullable|exists:program_kerjas,id', // Sesuaikan nama tabel progja Anda (program_kerja atau program_kerjas)
            'tempat' => 'required|string|max:255',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date|after:tgl_mulai',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:rencana,berlangsung,selesai,batal',
            'ketua_pelaksana_id' => 'nullable|exists:users,id',
            'mode_absensi' => 'required|in:internal,public',
        ]);

        $kegiatan = Kegiatan::create([
            // Logika Kepemilikan Organisasi
            'organization_id' => $request->organization_id ?? auth()->user()->organization_id,
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

        $this->generateQrCode($kegiatan);

        return redirect()->route('kegiatan.index')->with('success', 'Kegiatan berhasil ditambahkan');
    }

    public function show(Kegiatan $kegiatan)
    {
        if (!auth()->user()->hasRole('super_admin') && $kegiatan->organization_id != auth()->user()->organization_id) {
            abort(403, 'Akses Ditolak! Ini adalah kegiatan milik organisasi lain.');
        }
        $kegiatan->load('programKerja', 'absensi.user', 'organization');

        $hadir = $kegiatan->absensi->where('status', 'hadir')->count();
        $izin = $kegiatan->absensi->where('status', 'izin')->count();
        $sakit = $kegiatan->absensi->where('status', 'sakit')->count();
        $alpha = $kegiatan->absensi->where('status', 'alpha')->count();
        $total = $hadir + $izin + $sakit + $alpha;

        return view('admin.kegiatan.show', compact('kegiatan', 'hadir', 'izin', 'sakit', 'alpha', 'total'));
    }

    public function edit(Kegiatan $kegiatan)
    {
        if (!auth()->user()->hasRole('super_admin') && $kegiatan->organization_id != auth()->user()->organization_id) {
            abort(403, 'Akses Ditolak! Ini adalah kegiatan milik organisasi lain.');
        }

        $organizations = auth()->user()->hasRole('super_admin') ? Organization::all() : [];

        $progjaQuery = ProgramKerja::where('status', 'active');
        if (!auth()->user()->hasRole('super_admin')) {
            $progjaQuery->where('organization_id', auth()->user()->organization_id);
        }
        $programKerja = $progjaQuery->get();

        $userQuery = User::orderBy('name');
        if (!auth()->user()->hasRole('super_admin')) {
            $userQuery->where('organization_id', auth()->user()->organization_id);
        }
        $users = $userQuery->get();

        return view('admin.kegiatan.edit', compact('kegiatan', 'programKerja', 'users', 'organizations'));
    }

    public function update(Request $request, Kegiatan $kegiatan)
    {
        if (!auth()->user()->hasRole('super_admin') && $kegiatan->organization_id != auth()->user()->organization_id) {
            abort(403, 'Akses Ditolak! Ini adalah kegiatan milik organisasi lain.');
        }

        $request->validate([
            'nama' => 'required|string|max:200',
            'program_kerja_id' => 'nullable|exists:program_kerjas,id',
            'tempat' => 'required|string|max:255',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date|after:tgl_mulai',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:rencana,berlangsung,selesai,batal',
            'ketua_pelaksana_id' => 'nullable|exists:users,id',
            'mode_absensi' => 'required|in:internal,public',
        ]);

        if (auth()->user()->hasRole('super_admin') && $request->has('organization_id')) {
            $kegiatan->organization_id = $request->organization_id;
        }

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

        if ($kegiatan->wasChanged('mode_absensi')) {
            $this->generateQrCode($kegiatan);
        }

        return redirect()->route('kegiatan.index')->with('success', 'Kegiatan berhasil diupdate');
    }

    public function destroy(Kegiatan $kegiatan)
    {
        if (!auth()->user()->hasRole('super_admin') && $kegiatan->organization_id != auth()->user()->organization_id) {
            abort(403, 'Akses Ditolak!');
        }

        if ($kegiatan->qr_code && Storage::disk('public')->exists($kegiatan->qr_code)) {
            Storage::disk('public')->delete($kegiatan->qr_code);
        }
        $kegiatan->delete();
        return redirect()->route('kegiatan.index')->with('success', 'Kegiatan berhasil dihapus');
    }

    private function generateQrCode(Kegiatan $kegiatan)
    {
        $qrData = url('/absensi/' . $kegiatan->id . '/form');
        $qrFileName = 'kegiatan/qrcode/qrcode_' . $kegiatan->id . '.png';
        $qrPath = storage_path('app/public/' . $qrFileName);

        if (!Storage::disk('public')->exists('kegiatan/qrcode')) {
            Storage::disk('public')->makeDirectory('kegiatan/qrcode');
        }

        QrCode::format('png')->size(300)->margin(2)->generate($qrData, $qrPath);

        $kegiatan->qr_code = $qrFileName;
        $kegiatan->saveQuietly(); // saveQuietly agar tidak trigger event update berulang
        return $qrFileName;
    }

    public function downloadQrCode(Kegiatan $kegiatan)
    {
        if (!$kegiatan->qr_code || !Storage::disk('public')->exists($kegiatan->qr_code)) {
            $this->generateQrCode($kegiatan);
        }
        return response()->download(storage_path('app/public/' . $kegiatan->qr_code));
    }

    public function scanForm()
    {
        return view('admin.kegiatan.scan');
    }

    public function scanProcess(Request $request)
    {
        $request->validate(['qr_data' => 'required|string']);

        // BUG FIX: Menggunakan Regex untuk mencari angka ID di dalam URL
        // Pola: mencari angka setelah kata "absensi/" dan sebelum "/form"
        if (preg_match('/absensi\/(\d+)\/form/', $request->qr_data, $matches)) {
            $kegiatanId = $matches[1];
        } else {
            // Fallback jika yang discan hanya angka ID biasa
            $kegiatanId = (int) $request->qr_data;
        }

        $kegiatan = Kegiatan::find($kegiatanId);

        if (!$kegiatan) {
            return redirect()->back()->with('error', 'QR Code tidak valid atau kegiatan tidak ditemukan!');
        }

        if ($kegiatan->status == 'batal') {
            return redirect()->back()->with('error', 'Kegiatan ini dibatalkan');
        }

        if ($kegiatan->status == 'selesai') {
            return redirect()->back()->with('error', 'Kegiatan sudah selesai');
        }

        return redirect()->route('absensi.form', $kegiatan);
    }

    // ... (Fungsi absensiForm, absensiStore, absensiPublicStore, laporan, regenerateQrCode biarkan sama seperti asli Anda) ...
    public function absensiForm(Kegiatan $kegiatan)
    {
        if ($kegiatan->mode_absensi == 'public') {
            $ipAddress = request()->ip();
            $sudahAbsen = Absensi::where('kegiatan_id', $kegiatan->id)
                ->where('ip_address', $ipAddress)
                ->exists();
            return view('admin.kegiatan.absensi-form-public', compact('kegiatan', 'sudahAbsen'));
        }

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
            return redirect()->route('kegiatan.show', $kegiatan)->with('error', 'Anda sudah absensi.');
        }

        Absensi::create([
            'kegiatan_id' => $kegiatan->id,
            'user_id' => Auth::id(),
            'waktu_absen' => now(),
            'status' => $request->status,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('kegiatan.show', $kegiatan)->with('success', 'Absensi berhasil dicatat');
    }

    public function absensiPublicStore(Request $request, Kegiatan $kegiatan)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'asal' => 'nullable|string|max:100',
            'no_hp' => 'nullable|string|max:15',
            'status' => 'required|in:hadir,izin,sakit',
        ]);

        $sudahAbsen = Absensi::where('kegiatan_id', $kegiatan->id)
            ->where('nama_peserta', $request->nama)
            ->where('no_hp_peserta', $request->no_hp)
            ->exists();

        if ($sudahAbsen) {
            return redirect()->route('absensi.form', $kegiatan)->with('error', 'Nama & No HP ini sudah tercatat.');
        }

        Absensi::create([
            'kegiatan_id' => $kegiatan->id,
            'user_id' => null,
            'nama_peserta' => $request->nama,
            'asal_peserta' => $request->asal,
            'no_hp_peserta' => $request->no_hp,
            'waktu_absen' => now(),
            'status' => $request->status,
            'keterangan' => 'Absen publik',
            'ip_address' => $request->ip(), // Tambahan ip address agar pengecekan form public berfungsi
        ]);

        return redirect()->route('absensi.form', $kegiatan)->with('success', 'Absensi Anda telah tercatat!');
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
