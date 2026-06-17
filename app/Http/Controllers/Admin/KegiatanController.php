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
        // SUPER CLEAN: Query rumit 15 baris Anda terpangkas jadi 1 baris.
        // Trait otomatis memfilter data sesuai organisasi user!
        $kegiatan = Kegiatan::with('programKerja', 'organization')
            ->orderBy('tgl_mulai', 'desc')
            ->paginate(10);

        return view('admin.kegiatan.index', compact('kegiatan'));
    }

    public function create()
    {
        $user = auth()->user();
        $organizations = $user->hasRole('super_admin') ? Organization::all() : [];

        // Jika ProgramKerja sudah dipasang Trait, ini otomatis terfilter!
        $programKerja = ProgramKerja::where('status', 'active')->get();

        $userQuery = User::orderBy('name');
        if (!$user->hasRole('super_admin')) {
            $userQuery->where('organization_id', $user->organization_id);
        }
        $users = $userQuery->get();

        return view('admin.kegiatan.create', compact('programKerja', 'users', 'organizations'));
    }

    public function store(Request $request)
    {
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

        $kegiatan = Kegiatan::create(array_merge($request->except('_token'), [
            'organization_id' => $request->organization_id ?? auth()->user()->organization_id,
        ]));

        $this->generateQrCode($kegiatan);

        return redirect()->route('kegiatan.index')->with('success', 'Kegiatan berhasil ditambahkan');
    }

    public function show(Kegiatan $kegiatan)
    {
        // ABORT(403) MANUAL DIHAPUS
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
        // ABORT(403) MANUAL DIHAPUS
        $user = auth()->user();
        $organizations = $user->hasRole('super_admin') ? Organization::all() : [];
        $programKerja = ProgramKerja::where('status', 'active')->get();

        $userQuery = User::orderBy('name');
        if (!$user->hasRole('super_admin')) {
            $userQuery->where('organization_id', $user->organization_id);
        }
        $users = $userQuery->get();

        return view('admin.kegiatan.edit', compact('kegiatan', 'programKerja', 'users', 'organizations'));
    }

    public function update(Request $request, Kegiatan $kegiatan)
    {
        // ABORT(403) MANUAL DIHAPUS
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

        $data = $request->except('_token', '_method');
        if (auth()->user()->hasRole('super_admin') && $request->has('organization_id')) {
            $data['organization_id'] = $request->organization_id;
        }

        $kegiatan->update($data);

        if ($kegiatan->wasChanged('mode_absensi')) {
            $this->generateQrCode($kegiatan);
        }

        return redirect()->route('kegiatan.index')->with('success', 'Kegiatan berhasil diupdate');
    }

    public function destroy(Kegiatan $kegiatan)
    {
        // ABORT(403) MANUAL DIHAPUS
        if ($kegiatan->qr_code && Storage::disk('public')->exists($kegiatan->qr_code)) {
            Storage::disk('public')->delete($kegiatan->qr_code);
        }
        $kegiatan->delete();
        return redirect()->route('kegiatan.index')->with('success', 'Kegiatan berhasil dihapus');
    }

    // =========================================================================
    // KODE LAINNYA DI BAWAH INI TETAP SAMA (TIDAK PERLU DIUBAH)
    // generateQrCode, downloadQrCode, scanForm, scanProcess, absensiForm, dll.
    // =========================================================================

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
        $kegiatan->saveQuietly();
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

        if (preg_match('/absensi\/(\d+)\/form/', $request->qr_data, $matches)) {
            $kegiatanId = $matches[1];
        } else {
            $kegiatanId = (int) $request->qr_data;
        }

        $kegiatan = Kegiatan::find($kegiatanId);

        if (!$kegiatan) {
            return redirect()->back()->with('error', 'QR Code tidak valid atau kegiatan tidak ditemukan!');
        }

        if ($kegiatan->status == 'batal') return redirect()->back()->with('error', 'Kegiatan ini dibatalkan');
        if ($kegiatan->status == 'selesai') return redirect()->back()->with('error', 'Kegiatan sudah selesai');

        return redirect()->route('absensi.form', $kegiatan);
    }

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
            'ip_address' => $request->ip(),
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
