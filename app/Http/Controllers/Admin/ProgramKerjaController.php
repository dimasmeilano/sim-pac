<?php

namespace App\Http\Controllers\Admin;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\ProgramKerja;
use App\Models\SubTugas;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgramKerjaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // 1. Update status otomatis (tetap pertahankan kode asli Anda)
        ProgramKerja::where('status', 'active')
            ->where('tgl_selesai', '<', now()->toDateString())
            ->update(['status' => 'completed']);

        // 2. Siapkan query dasar
        $query = ProgramKerja::with('organization', 'tugas');
        $user = auth()->user();

        // 3. Jalankan filter jika yang login BUKAN Super Admin
        if (!$user->hasRole('super_admin')) {
            if ($user->organization) {
                // KUNCI UTAMA: Hanya tampilkan proker milik organisasinya sendiri
                $query->where('organization_id', $user->organization_id);

                // Filter tambahan untuk jenis organisasi (IPNU/IPPNU)
                $jenisOrgUser = $user->organization->jenis_organisasi;
                $query->where(function ($q) use ($jenisOrgUser) {
                    if ($jenisOrgUser === 'ipnu') {
                        $q->whereIn('jenis', ['ipnu', 'bersama']);
                    } elseif ($jenisOrgUser === 'ippnu') {
                        $q->whereIn('jenis', ['ippnu', 'bersama']);
                    } else {
                        $q->where('jenis', 'bersama');
                    }
                });
            } else {
                $query->whereNull('id');
            }
        }

        // 4. Eksekusi query dengan urutan terbaru
        $programKerja = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.progja.index', compact('programKerja'));
    }

    public function create()
    {
        // Super Admin butuh daftar organisasi untuk ditempatkan progjanya
        $organizations = auth()->user()->hasRole('super_admin') ? \App\Models\Organization::all() : [];
        return view('admin.progja.create', compact('organizations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:200',
            'deskripsi' => 'nullable|string',
            'jenis' => 'required|in:ipnu,ippnu,bersama',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
            'status' => 'required|in:planning,active,completed,cancelled',
            'estimasi_anggaran' => 'nullable|numeric', // Validasi anggaran
        ]);

        $progja = ProgramKerja::create([
            // Logika Pintar: Jika ada input organization_id dari Super Admin, pakai itu. Jika tidak, pakai milik user.
            'organization_id' => $request->organization_id ?? auth()->user()->organization_id,
            'nama' => $request->nama,
            'jenis' => $request->jenis,
            'deskripsi' => $request->deskripsi,
            'tgl_mulai' => $request->tgl_mulai,
            'tgl_selesai' => $request->tgl_selesai,
            'status' => $request->status,
            'estimasi_anggaran' => $request->estimasi_anggaran ?? 0,
        ]);

        return redirect()->route('progja.show', $progja)->with('success', 'Program Kerja berhasil dibuat');
    }

    public function edit(ProgramKerja $progja)
    {
        if (!auth()->user()->hasRole('super_admin') && $progja->organization_id != auth()->user()->organization_id) {
            abort(403, 'Akses Ditolak! Ini adalah program kerja milik organisasi lain.');
        }

        $organizations = auth()->user()->hasRole('super_admin') ? \App\Models\Organization::all() : [];
        return view('admin.progja.edit', compact('progja', 'organizations'));
    }

    public function update(Request $request, ProgramKerja $progja)
    {
        if (!auth()->user()->hasRole('super_admin') && $progja->organization_id != auth()->user()->organization_id) {
            abort(403, 'Akses Ditolak! Ini adalah program kerja milik organisasi lain.');
        }

        $request->validate([
            'nama' => 'required|string|max:200',
            'deskripsi' => 'nullable|string',
            'jenis' => 'required|in:ipnu,ippnu,bersama',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
            'status' => 'required|in:planning,active,completed,cancelled',
            'estimasi_anggaran' => 'nullable|numeric',
        ]);

        // Super Admin boleh memindah kepemilikan progja
        if (auth()->user()->hasRole('super_admin') && $request->has('organization_id')) {
            $progja->organization_id = $request->organization_id;
        }

        $progja->update([
            'nama' => $request->nama,
            'jenis' => $request->jenis,
            'deskripsi' => $request->deskripsi,
            'tgl_mulai' => $request->tgl_mulai,
            'tgl_selesai' => $request->tgl_selesai,
            'status' => $request->status,
            'estimasi_anggaran' => $request->estimasi_anggaran ?? 0,
        ]);

        return redirect()->route('progja.show', $progja)->with('success', 'Program Kerja berhasil diupdate');
    }

    public function show(ProgramKerja $progja)
    {
        if (!auth()->user()->hasRole('super_admin') && $progja->organization_id != auth()->user()->organization_id) {
            abort(403, 'Akses Ditolak! Ini adalah program kerja milik organisasi lain.');
        }

        $progja->load('tugas.assignee', 'messages.user', 'organization');

        $todos = $progja->tugas->where('status', 'todo');
        $progress = $progja->tugas->where('status', 'progress');
        $done = $progja->tugas->where('status', 'done');
        $revisi = $progja->tugas->where('status', 'revisi');

        $users = User::where('organization_id', $progja->organization_id)->orderBy('name')->get();

        return view('admin.progja.show', compact('progja', 'todos', 'progress', 'done', 'revisi', 'users'));
    }

    public function destroy(ProgramKerja $progja)
    {
        if (!auth()->user()->hasRole('super_admin') && $progja->organization_id != auth()->user()->organization_id) {
            abort(403, 'Akses Ditolak! Ini adalah program kerja milik organisasi lain.');
        }

        $progja->delete();
        return redirect()->route('progja.index')->with('success', 'Program Kerja berhasil dihapus');
    }

    // ============ TUGAS METHODS ============

    public function tugasStore(Request $request, ProgramKerja $progja)
    {
        $request->validate([
            'nama' => 'required|string|max:200',
            'assignee_id' => 'nullable|exists:users,id',
            'deadline' => 'nullable|date',
        ]);

        $urutan = SubTugas::where('progja_id', $progja->id)->max('urutan') + 1;

        $tugas = SubTugas::create([
            'progja_id' => $progja->id,
            'nama' => $request->nama,
            'assignee_id' => $request->assignee_id,
            'deadline' => $request->deadline,
            'status' => 'todo',
            'urutan' => $urutan,
        ]);

        return response()->json(['success' => true, 'tugas' => $tugas]);
    }

    public function tugasUpdateStatus(Request $request, SubTugas $tugas)
    {
        $request->validate([
            'status' => 'required|in:todo,progress,done,revisi',
        ]);

        $tugas->update(['status' => $request->status]);

        return response()->json(['success' => true]);
    }

    public function tugasUpdateOrder(Request $request)
    {
        foreach ($request->order as $index => $tugasId) {
            SubTugas::where('id', $tugasId)->update(['urutan' => $index]);
        }

        return response()->json(['success' => true]);
    }

    public function tugasDestroy(SubTugas $tugas)
    {
        $tugas->delete();
        return response()->json(['success' => true]);
    }

    // ============ CHAT METHODS ============

    public function sendMessage(Request $request, ProgramKerja $progja)
    {
        // Validasi: Pesan boleh kosong asalkan ada file, atau sebaliknya
        $request->validate([
            'message' => 'nullable|string',
            'file'    => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx|max:5120',
        ]);

        if (!$request->message && !$request->hasFile('file')) {
            return response()->json(['error' => 'Pesan atau file tidak boleh kosong.'], 422);
        }

        // =========================================================
        // (Logika Regex @mention Anda taruh kembali di sini jika ada)
        // =========================================================

        // Proses Upload File jika ada
        $filePath = null;
        $fileName = null;
        $fileType = null;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = $file->getClientOriginalName();
            $fileType = $file->getClientOriginalExtension();
            $filePath = $file->storeAs('chat_files/' . $progja->id, time() . '_' . $fileName, 'public');
        }

        $messageData = Message::create([
            'progja_id'    => $progja->id,
            'user_id'      => Auth::id(),
            'message'      => $request->message ?? '',
            'tagged_users' => null, // Sesuaikan dengan logika tag Anda
            'file_path'    => $filePath,
            'file_name'    => $fileName,
            'file_type'    => $fileType,
            'reply_to_id'  => $request->reply_to_id, // <-- TAMBAHKAN INI
        ]);

        $messageData->load('user');

        // Trigger Broadcast (Jika Reverb/Pusher sudah jalan)
        if (class_exists(\App\Events\MessageSent::class)) {
            broadcast(new \App\Events\MessageSent($messageData))->toOthers();
        }

        return response()->json([
            'success' => true,
            'message' => $messageData
        ]);
    }


    public function getMessages(ProgramKerja $progja)
    {
        // 1. KITA AKTIFKAN KEMBALI 'repliedMessage.user'
        $messages = $progja->messages()
            ->with(['user', 'repliedMessage.user'])
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'messages' => $messages->map(function ($msg) {

                $date = $msg->created_at;
                if ($date->isToday()) {
                    $dateLabel = 'Hari ini';
                } elseif ($date->isYesterday()) {
                    $dateLabel = 'Kemarin';
                } else {
                    $dateLabel = $date->translatedFormat('d F Y');
                }

                // 2. KITA AKTIFKAN KEMBALI DATA REPLY-NYA
                $replyData = null;
                if ($msg->reply_to_id && $msg->repliedMessage) {
                    $replyData = [
                        'name'    => $msg->repliedMessage->user ? $msg->repliedMessage->user->name : 'User',
                        'message' => $msg->repliedMessage->message ? $msg->repliedMessage->message : '📂 Lampiran File'
                    ];
                }

                return [
                    'id'         => $msg->id,
                    'user_id'    => $msg->user_id,
                    'message'    => $msg->message,
                    'file_path'  => $msg->file_path,
                    'file_name'  => $msg->file_name,
                    'file_type'  => $msg->file_type,
                    'user'       => [
                        'id'   => $msg->user->id ?? 0,
                        'name' => $msg->user->name ?? 'Unknown'
                    ],
                    'time'       => $date->format('H:i'),
                    'date_group' => $date->format('Y-m-d'),
                    'date_label' => $dateLabel,
                    'reply_to'   => $replyData, // Data reply dikirim ke Javascript
                ];
            })
        ]);
    }

    public function cetakLpj(ProgramKerja $programKerja)
    {
        // Pengecekan Keamanan Multi-Tenant
        $user = auth()->user();
        if (!$user->hasRole('super_admin') && $programKerja->organization_id != $user->organization_id) {
            abort(403, 'Akses Ditolak!');
        }

        // Sedot SEMUA data terkait dengan Eager Loading agar database tidak jebol (N+1 safe)
        $programKerja->load([
            'organization',
            'transaksis.createdBy',
            // Sedot kegiatan beserta absensi, notulensi, dan galeri/foldernya
            'kegiatans.absensi.user',
            'kegiatans.notulensis.notulis',
            'kegiatans.folders.galeris' // Berdasarkan arsitektur Google Drive kita sebelumnya
        ]);

        // Rekap Keuangan
        $pemasukan = $programKerja->transaksis->where('jenis', 'masuk')->where('status_validasi', 'disetujui')->sum('nominal');
        $pengeluaran = $programKerja->transaksis->where('jenis', 'keluar')->where('status_validasi', 'disetujui')->sum('nominal');
        $saldo_akhir = $pemasukan - $pengeluaran;

        // Render ke PDF
        $pdf = Pdf::loadView('admin.progja.lpj_pdf', compact(
            'programKerja',
            'pemasukan',
            'pengeluaran',
            'saldo_akhir'
        ));

        // Atur ukuran kertas A4, margin standar laporan
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('LPJ_' . str_replace(' ', '_', $programKerja->nama) . '.pdf');
    }

    public function cetakRingkasanPdf($id)
    {
        // 1. Tarik data Progja beserta seluruh "anak-anaknya"
        $programKerja = ProgramKerja::with([
            'organization',
            'kegiatans.absensi',
            'transaksis'
        ])->findOrFail($id);

        // 2. TAMBAHAN BARU: Tarik data LPJ yang berelasi dengan Program Kerja ini
        $lpj = \App\Models\Lpj::where('program_kerja_id', $id)->first();

        // 3. Rekap Keuangan Tingkat Progja
        $pemasukan = $programKerja->transaksis->where('jenis', 'masuk')->where('status_validasi', 'disetujui')->sum('nominal');
        $pengeluaran = $programKerja->transaksis->where('jenis', 'keluar')->where('status_validasi', 'disetujui')->sum('nominal');
        $saldo = $pemasukan - $pengeluaran;

        // 4. Rekap Total Peserta
        $totalPeserta = 0;
        foreach ($programKerja->kegiatans as $kegiatan) {
            $totalPeserta += $kegiatan->absensi->count();
        }

        // 5. Load View PDF (JANGAN LUPA TAMBAHKAN 'lpj' DI DALAM COMPACT)
        $pdf = Pdf::loadView('admin.progja.ringkasan_pdf', compact(
            'programKerja',
            'pemasukan',
            'pengeluaran',
            'saldo',
            'totalPeserta',
            'lpj' // <-- Variabel ini yang ditunggu-tunggu oleh file PDF
        ));

        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('Ringkasan_Progja_' . str_replace(' ', '_', $programKerja->nama) . '.pdf');
    }
}
