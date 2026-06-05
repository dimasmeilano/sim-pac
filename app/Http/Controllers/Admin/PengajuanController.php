<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AkunRantingMail;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\PengajuanRekomendasi;
use App\Models\SuratMasuk;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Laravel\Reverb\Loggers\Log;

class PengajuanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['create', 'store']);
        $this->middleware('permission:manage_surat')->except(['create', 'store']);
    }

    // Menampilkan halaman form publik
    public function create()
    {
        return view('public.pengajuan'); // Sesuaikan dengan letak file blade form Anda
    }

    // Memproses data yang dikirim dari form
    public function store(Request $request)
    {
        // 1. VALIDASI KETAT
        $request->validate([
            // Validasi Step 1 (Organisasi)
            'type'             => 'required|in:ranting,komisariat',
            'jenis_organisasi' => 'required|in:ipnu,ippnu,bersama',
            'name'             => 'required|string|max:255',
            'periode'          => 'required|string|max:50',
            'alamat'           => 'required|string',
            'email_organisasi' => 'nullable|email',

            // Validasi Step 2 (Pengurus)
            'ketua_name'       => 'required|string|max:255',
            'ketua_email'      => 'required|email',
            'ketua_no_hp'      => 'required|string|max:20',
            'ketua_jk'         => 'required|in:Laki-laki,Perempuan',
            'sekretaris_name'  => 'required|string|max:255',
            'sekretaris_email' => 'required|email',
            'sekretaris_no_hp' => 'required|string|max:20',
            'sekretaris_jk'    => 'required|in:Laki-laki,Perempuan',

            // Validasi Step 3 (10 File Wajib PDF, Maksimal 2MB per file)
            'file_surat_permohonan'     => 'required|file|mimes:pdf|max:2048',
            'file_sk_konferensi'        => 'required|file|mimes:pdf|max:2048',
            'file_ba_formatur'          => 'required|file|mimes:pdf|max:2048',
            'file_sk_formatur'          => 'required|file|mimes:pdf|max:2048',
            'file_susunan_pengurus'     => 'required|file|mimes:pdf|max:2048',
            'file_rekomendasi_nu'       => 'required|file|mimes:pdf|max:2048',
            'file_biodata_pengurus'     => 'required|file|mimes:pdf|max:2048',
            'file_hasil_konferensi_lpj' => 'required|file|mimes:pdf|max:2048',
            'file_dokumentasi'          => 'required|file|mimes:pdf|max:2048',
            'file_profil_organisasi'    => 'required|file|mimes:pdf|max:2048',
        ], [
            'required' => 'Kolom :attribute wajib diisi.',
            'mimes'    => 'File :attribute harus berupa format PDF.',
            'max'      => 'Ukuran file :attribute maksimal adalah 2MB.',
        ]);

        // 2. PERSIAPAN FOLDER PENYIMPANAN BERKAS
        // Kita buatkan folder khusus per organisasi agar file tidak tercampur
        $folderName = 'pengajuan/' . time() . '_' . Str::slug($request->name);

        $filePaths = [];
        $fileFields = [
            'file_surat_permohonan',
            'file_sk_konferensi',
            'file_ba_formatur',
            'file_sk_formatur',
            'file_susunan_pengurus',
            'file_rekomendasi_nu',
            'file_biodata_pengurus',
            'file_hasil_konferensi_lpj',
            'file_dokumentasi',
            'file_profil_organisasi'
        ];

        // 3. PROSES UPLOAD 10 FILE
        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                // Ubah nama file menjadi lebih rapi
                $filename = $field . '_' . time() . '.' . $file->getClientOriginalExtension();
                // Simpan ke storage/app/public/pengajuan/...
                $filePaths[$field] = $file->storeAs($folderName, $filename, 'public');
            }
        }

        // 4. SIMPAN SEMUA DATA KE DATABASE
        PengajuanRekomendasi::create(array_merge(
            $request->except($fileFields), // Ambil semua data teks
            $filePaths,                    // Gabungkan dengan data path file
            ['status' => 'menunggu_validasi'] // Set status default
        ));

        // 5. KEMBALIKAN RESPON SUKSES KE RANTING
        return redirect()->back()->with('success_pengajuan', 'Alhamdulillah! Pengajuan Rekomendasi Pengesahan Ranting/Komisariat Anda berhasil dikirim. Silakan tunggu proses verifikasi oleh Admin PAC. Notifikasi persetujuan beserta akses login akan dikirimkan melalui email Ketua/Sekretaris yang didaftarkan.');
    }

    public function index()
    {
        // Menampilkan yang terbaru, diutamakan yang 'menunggu_validasi'
        $pengajuan = PengajuanRekomendasi::orderByRaw("
            CASE status
                WHEN 'menunggu_validasi' THEN 1
                WHEN 'revisi' THEN 2
                WHEN 'ditolak' THEN 3
                WHEN 'disetujui' THEN 4
                ELSE 5
            END
        ")
            ->latest() // Mengurutkan yang terbaru di dalam masing-masing kelompok status
            ->paginate(15);

        return view('admin.pengajuan.index', compact('pengajuan'));
    }

    // Menampilkan detail berkas untuk direview Admin
    public function show($id)
    {
        $pengajuan = PengajuanRekomendasi::findOrFail($id);
        return view('admin.pengajuan.show', compact('pengajuan'));
    }

    // Tombol Sakti: Terima & Sahkan (Generate Organisasi & User)
    public function approve($id)
    {
        $pengajuan = PengajuanRekomendasi::findOrFail($id);

        if ($pengajuan->status == 'disetujui') {
            return back()->with('error', 'Pengajuan ini sudah pernah disahkan.');
        }

        // Mulai Transaksi Database agar aman jika terjadi error di tengah jalan
        DB::beginTransaction();
        try {
            // 1. Buat Data Organisasi Baru
            $org = Organization::create([
                'name'             => $pengajuan->name,
                'type'             => $pengajuan->type,
                'jenis_organisasi' => $pengajuan->jenis_organisasi,
                'periode'          => $pengajuan->periode,
                'alamat'           => $pengajuan->alamat,
                'email'            => $pengajuan->email_organisasi,
                'parent_id'        => auth()->user()->organization_id,
            ]);

            $jkKetua = ($pengajuan->ketua_jk == 'Laki-laki') ? 'L' : 'P';
            $jkSekretaris = ($pengajuan->sekretaris_jk == 'Laki-laki') ? 'L' : 'P';
            // 2. Buat Akun Ketua (Password Default: ranting123)
            $ketua = User::create([
                'name'            => $pengajuan->ketua_name,
                'email'           => $pengajuan->ketua_email,
                'password'        => Hash::make('ranting123'),
                'organization_id' => $org->id,
                'jk'              => $jkKetua, // Gunakan variabel hasil konversi
                'no_hp'           => $pengajuan->ketua_no_hp,
            ]);

            $ketua->assignRole('ketua_ranting');

            // 3. Buat Akun Sekretaris (Password Default: ranting123)
            $sekretaris = User::create([
                'name'            => $pengajuan->sekretaris_name,
                'email'           => $pengajuan->sekretaris_email,
                'password'        => Hash::make('ranting123'),
                'organization_id' => $org->id,
                'jk'              => $jkSekretaris,
                'no_hp'           => $pengajuan->sekretaris_no_hp,
            ]);
            $sekretaris->assignRole('sekretaris_ranting');
            // 4. Update Organisasi: Kaitkan ID Ketua & Sekretaris yang baru dibuat
            $org->update([
                'ketua_id'      => $ketua->id,
                'sekretaris_id' => $sekretaris->id,
            ]);

            // 5. Ubah Status Pengajuan Menjadi Disetujui
            $pengajuan->update([
                'status'          => 'disetujui',
                'catatan_admin'   => 'Pengajuan telah disahkan dan akun telah diterbitkan.',
                'divalidasi_oleh' => auth()->id(),
                'waktu_validasi'  => now(),
            ]);

            // 6. RECORD KE SURAT MASUK (Dieksekusi setelah berkas dinyatakan sah)
            SuratMasuk::create([
                'organization_id'  => auth()->user()->organization_id,

                'nomor_surat'      => '-',
                'pengirim'         => $pengajuan->name . ' (' . ucfirst($pengajuan->type) . ')',
                'perihal'          => 'Permohonan Rekomendasi Pengesahan Pengurus',
                'isi_surat'        => 'Berkas pengajuan rekomendasi pengesahan pengurus beserta lampiran 10 persyaratan administrasi.',
                'lampiran'         => $pengajuan->file_surat_permohonan, // Tarik PDF dari tabel pengajuan
                'tanggal_surat'    => $pengajuan->created_at->format('Y-m-d'),
                'tanggal_diterima' => now()->format('Y-m-d'),
                'status'           => 'baru',
                'disposisi'        => null,

                // Gunakan ID user admin yang login sesuai format masukStore Anda
                'diterima_oleh'    => auth()->id(),
            ]);
            // TODO NEXT: Fungsi Generate Draf Surat & Kirim Email dimasukkan di sini nanti
            // 7. KIRIM EMAIL KE KETUA DAN SEKRETARIS
            // Kita bungkus pakai try-catch agar kalaupun SMTP (server email) gagal/lelet, 
            // proses validasi di database tetap sukses dan tidak ter-rollback.
            try {
                // Kirim ke Ketua
                Mail::to($ketua->email)->send(new AkunRantingMail(
                    $ketua->name,
                    $org->name,
                    $ketua->email,
                    'ranting123'
                ));

                // Kirim ke Sekretaris
                Mail::to($sekretaris->email)->send(new AkunRantingMail(
                    $sekretaris->name,
                    $org->name,
                    $sekretaris->email,
                    'ranting123'
                ));
            } catch (\Exception $e) {
                // Catat ke log jika email gagal terkirim (misal karena jaringan)
                Log::error('Gagal kirim email akun ranting: ' . $e->getMessage());
            }

            DB::commit();
            return redirect()->route('pengajuan.index')->with('success', 'Ajaib! Data Organisasi dan Akun Pengurus berhasil di-generate secara otomatis.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    // Tombol Tolak / Minta Revisi
    public function reject(Request $request, $id)
    {
        $request->validate([
            'status'        => 'required|in:ditolak,revisi',
            'catatan_admin' => 'required|string',
        ]);

        $pengajuan = PengajuanRekomendasi::findOrFail($id);
        $pengajuan->update([
            'status'          => $request->status,
            'catatan_admin'   => $request->catatan_admin,
            'divalidasi_oleh' => auth()->id(),
            'waktu_validasi'  => now(),
        ]);

        return redirect()->route('pengajuan.index')->with('success', 'Status pengajuan berhasil diubah menjadi ' . ucfirst($request->status));
    }

    public function perpanjanganCreate()
    {
        // Ambil data organisasi dari user yang sedang login
        $organisasi = auth()->user()->organization;

        // Cegah Ranting membuat pengajuan baru jika masih ada yang belum diproses/menunggu revisi
        $pengajuanAktif = \App\Models\PengajuanRekomendasi::where('organization_id', $organisasi->id)
            ->whereIn('status', ['menunggu_validasi', 'revisi'])
            ->first();

        if ($pengajuanAktif) {
            return redirect()->route('dashboard')->with('error', 'Organisasi Anda masih memiliki pengajuan perpanjangan yang sedang diproses. Silakan tunggu konfirmasi PAC.');
        }

        return view('pengajuan.create', compact('organisasi'));
    }

    public function perpanjanganStore(Request $request)
    {
        $organisasi = auth()->user()->organization;

        // Validasi input (Kita tidak memvalidasi nama/alamat/email organisasi lagi karena otomatis diambil dari database)
        $request->validate([
            'periode'                 => 'required|string|max:50',
            'ketua_name'              => 'required|string|max:150',
            'ketua_email'             => 'required|email|max:150',
            'ketua_jk'                => 'required|in:Laki-laki,Perempuan',
            'ketua_no_hp'             => 'required|string|max:20',
            'sekretaris_name'         => 'required|string|max:150',
            'sekretaris_email'        => 'required|email|max:150',
            'sekretaris_jk'           => 'required|in:Laki-laki,Perempuan',
            'sekretaris_no_hp'        => 'required|string|max:20',

            // Validasi 10 File
            'file_surat_permohonan'       => 'required|file|mimes:pdf|max:2048',
            'file_sk_konferensi'          => 'required|file|mimes:pdf|max:2048',
            'file_ba_formatur'            => 'required|file|mimes:pdf|max:2048',
            'file_sk_formatur'            => 'required|file|mimes:pdf|max:2048',
            'file_susunan_pengurus'       => 'required|file|mimes:pdf|max:2048',
            'file_rekomendasi_nu'         => 'required|file|mimes:pdf|max:2048',
            'file_biodata_pengurus'       => 'required|file|mimes:pdf|max:2048',
            'file_hasil_konferensi_lpj'   => 'required|file|mimes:pdf|max:2048',
            'file_dokumentasi'            => 'required|file|mimes:pdf|max:5120',
            'file_profil_organisasi'      => 'required|file|mimes:pdf|max:2048',
        ]);

        // Proses Upload File
        $fileFields = [
            'file_surat_permohonan',
            'file_sk_konferensi',
            'file_ba_formatur',
            'file_sk_formatur',
            'file_susunan_pengurus',
            'file_rekomendasi_nu',
            'file_biodata_pengurus',
            'file_hasil_konferensi_lpj',
            'file_dokumentasi',
            'file_profil_organisasi'
        ];

        $filePaths = [];
        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $filename = time() . '_' . $field . '.' . $file->getClientOriginalExtension();
                $filePaths[$field] = $file->storeAs('pengajuan/' . $organisasi->name, $filename, 'public');
            }
        }

        // Simpan Data Pengajuan Perpanjangan
        $pengajuan = PengajuanRekomendasi::create(array_merge(
            $request->except($fileFields),
            $filePaths,
            [
                'jenis_pengajuan'  => 'perpanjangan',
                'organization_id'  => $organisasi->id,
                // Ambil data profil statis dari database organisasi
                'name'             => $organisasi->name,
                'type'             => $organisasi->type,
                'jenis_organisasi' => $organisasi->jenis_organisasi,
                'alamat'           => $organisasi->alamat,
                'email_organisasi' => $organisasi->email,

                'status'           => 'menunggu_validasi',
            ]
        ));

        // Redirect kembali ke Dashboard Ranting dengan notifikasi
        return redirect()->route('dashboard')->with('success', 'Pengajuan Perpanjangan SK berhasil dikirim! Silakan tunggu proses verifikasi oleh PAC.');
    }
}
