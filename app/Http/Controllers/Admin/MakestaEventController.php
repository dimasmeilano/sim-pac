<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MakestaEvaluasi;
use App\Models\MakestaEvent;
use App\Models\MakestaMateri;
use App\Models\MakestaPeserta;
use App\Models\Organization;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MakestaEventController extends Controller
{
    // Menampilkan Daftar Semua Event Makesta
    public function index()
    {
        // 1. Cek apakah user yang login adalah PAC
        if (auth()->user()->hasRole('sekretaris_pac') || auth()->user()->hasRole('super_admin')) {

            // Jika PAC, tampilkan SEMUA data event
            $events = MakestaEvent::with('organization')
                ->orderBy('created_at', 'desc')
                ->get();
        } else {

            // 2. Jika bukan PAC (berarti Ranting), filter datanya!
            // Tampilkan HANYA data yang organization_id-nya sama dengan akun yang sedang login
            $events = MakestaEvent::with('organization')
                ->where('organization_id', auth()->user()->organization_id)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('admin.makesta.index', compact('events'));
    }

    // Menampilkan Form Tambah Event
    public function create()
    {
        // Mengambil data organisasi (PAC dan Ranting) untuk pilihan penyelenggara
        $organizations = Organization::all();

        return view('admin.makesta.create', compact('organizations'));
    }

    // Menyimpan Data dari Form ke Database
    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'tema'            => 'required|string|max:255',
            'lokasi'          => 'required|string|max:255',
            'tgl_mulai'       => 'required|date',
            'tgl_selesai'     => 'required|date|after_or_equal:tgl_mulai',
            'kuota_peserta'   => 'nullable|numeric',
            'berkas_proposal' => 'nullable|mimes:pdf|max:2048' // Maks 2MB, PDF saja
        ]);

        // 2. Siapkan data untuk disimpan
        $data = $request->except('berkas_proposal');

        $data['organization_id'] = auth()->user()->organization_id;
        // 3. Cek jika ada file proposal yang diupload (Khusus Ranting)
        if ($request->hasFile('berkas_proposal')) {
            $file = $request->file('berkas_proposal');
            $filename = time() . '_Proposal_' . $file->getClientOriginalName();
            // Simpan ke folder public/storage/makesta/proposal
            $path = $file->storeAs('makesta/proposal', $filename, 'public');
            $data['berkas_proposal'] = $path;
        }

        // 4. Set Status otomatis (Bisa disesuaikan logikanya nanti)
        // Misal: Jika PAC yang buat (ID PAC), status langsung 'Disetujui'
        // Set status otomatis berdasarkan role
        if (auth()->user()->hasRole('sekretaris_pac') || auth()->user()->hasRole('super_admin')) {
            $data['status'] = 'Disetujui'; // PAC buat, otomatis disetujui
        } else {
            $data['status'] = 'Menunggu Verifikasi'; // Ranting buat, nunggu verifikasi
        }

        // 5. Simpan ke Database
        MakestaEvent::create($data);

        return redirect()->route('makesta-event.index')->with('success', 'Event Makesta berhasil didaftarkan!');
    }

    // 3. TAMPILKAN FORM EDIT
    public function edit($id)
    {
        $event = MakestaEvent::findOrFail($id);
        return view('admin.makesta.edit', compact('event'));
    }

    // 4. SIMPAN DATA EDIT
    public function update(Request $request, $id)
    {
        $event = MakestaEvent::findOrFail($id);

        $request->validate([
            'tema'        => 'required|string|max:255',
            'lokasi'      => 'required|string|max:255',
            'tgl_mulai'   => 'required|date',
            'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
        ]);

        $data = $request->except(['berkas_proposal', '_method', '_token']);

        if ($request->hasFile('berkas_proposal')) {
            $file = $request->file('berkas_proposal');
            $filename = time() . '_Proposal_' . $file->getClientOriginalName();
            $path = $file->storeAs('makesta/proposal', $filename, 'public');
            $data['berkas_proposal'] = $path;
        }

        $event->update($data);

        return redirect()->route('makesta-event.index')->with('success', 'Data Event berhasil diperbarui!');
    }

    // Menampilkan Halaman Detail Event
    public function show($id)
    {
        $event = MakestaEvent::with('organization')->findOrFail($id);
        return view('admin.makesta.show', compact('event'));
    }

    // Fungsi untuk menyetujui event dari Ranting
    public function verifikasi($id)
    {
        $event = MakestaEvent::findOrFail($id);
        $event->status = 'Disetujui';
        $event->save();

        return redirect()->back()->with('success', 'Event Makesta berhasil diverifikasi dan disahkan!');
    }

    // SIMPAN MATERI & GENERATE MAGIC LINK
    public function storeMateri(Request $request, $id)
    {
        $event = MakestaEvent::findOrFail($id);

        $request->validate([
            'nama_materi'     => 'required|string|max:255',
            'nama_pemateri'   => 'required|string|max:255',
            'nama_instruktur' => 'required|string|max:255',
            'waktu_materi'    => 'required',
        ]);

        // "Mantra" Pembuat Token dan PIN Acak
        $token = Str::slug($request->nama_materi) . '-' . Str::random(5); // Contoh: aswaja-8f7A2
        $pin = rand(1000, 9999); // Menghasilkan PIN 4 digit (Contoh: 4821)

        $event->materis()->create([
            'nama_materi'     => $request->nama_materi,
            'nama_pemateri'   => $request->nama_pemateri,
            'nama_instruktur' => $request->nama_instruktur,
            'waktu_materi'    => $request->waktu_materi,
            'token_rahasia'   => $token,
            'pin_instruktur'  => $pin,
        ]);

        return redirect()->back()->with('success', 'Materi dan Magic Link Instruktur berhasil dibuat!');
    }

    // HAPUS MATERI
    public function destroyMateri($id)
    {
        $materi = MakestaMateri::findOrFail($id);
        $materi->delete();

        return redirect()->back()->with('success', 'Jadwal materi berhasil dihapus!');
    }

    // Menampilkan Daftar Peserta untuk Event Tertentu
    public function peserta($id)
    {
        // Tarik data event beserta relasi pesertanya
        $event = MakestaEvent::with('pesertas')->findOrFail($id);

        // KUNCI KEAMANAN: Pastikan Ranting hanya bisa melihat peserta eventnya sendiri
        if (!auth()->user()->hasRole('sekretaris_pac') && $event->organization_id != auth()->user()->organization_id) {
            abort(403, 'Akses Ditolak! Anda tidak berhak melihat data peserta dari Ranting lain.');
        }

        return view('admin.makesta.peserta', compact('event'));
    }

    // UBAH STATUS KELULUSAN PESERTA
    public function updateStatusPeserta(Request $request, $id)
    {
        $request->validate([
            'status_kelulusan' => 'required|in:Menunggu,Mengikuti,Lulus,Tidak Lulus'
        ]);

        $peserta = MakestaPeserta::findOrFail($id);
        $peserta->update([
            'status_kelulusan' => $request->status_kelulusan
        ]);

        return redirect()->back()->with('success', 'Status peserta berhasil diperbarui!');
    }

    // HAPUS DATA PESERTA
    public function destroyPeserta($id)
    {
        $peserta = MakestaPeserta::findOrFail($id);

        // Hapus juga file persyaratan dari penyimpanan agar server tidak penuh
        if ($peserta->berkas_syarat) {
            Storage::disk('public')->delete($peserta->berkas_syarat);
        }

        $peserta->delete();

        return redirect()->back()->with('success', 'Data peserta berhasil dihapus!');
    }

    public function cetakIdCard($id)
    {
        // Tarik data peserta beserta relasi event dan organisasinya
        $peserta = MakestaPeserta::with('event.organization')->findOrFail($id);

        return view('admin.makesta.idcard', compact('peserta'));
    }

    // CETAK ID CARD SEMUA PESERTA SEKALIGUS
    public function cetakIdCardMasal($id)
    {
        // DOWNLOAD PDF ID CARD SEMUA PESERTA

        $event = MakestaEvent::with(['organization', 'pesertas'])->findOrFail($id);

        if ($event->pesertas->count() == 0) {
            return redirect()->back()->withErrors(['Belum ada peserta yang bisa dicetak ID Card-nya.']);
        }

        // Memanggil View khusus PDF (kita buat file baru nanti)
        $pdf = Pdf::loadView('admin.makesta.idcard_masal', compact('event'));

        // Atur ukuran kertas menjadi A4
        $pdf->setPaper('A4', 'portrait');

        // Langsung Download File PDF-nya!
        return $pdf->download('ID_Card_Makesta_' . $event->tema . '.pdf');
    }

    // HALAMAN REKAP NILAI
    public function rekapNilai($id)
    {
        // Tarik data event, peserta, dan materi terkait
        $event = MakestaEvent::with(['pesertas', 'materis.nilais'])->findOrFail($id);

        return view('admin.makesta.rekap', compact('event'));
    }

    public function exportPesertaPdf($id)
    {
        $event = MakestaEvent::with(['organization', 'pesertas'])->findOrFail($id);

        if ($event->pesertas->count() == 0) {
            return redirect()->back()->withErrors(['Belum ada peserta yang bisa diexport.']);
        }

        // Load view PDF
        $pdf = Pdf::loadView('admin.makesta.export_peserta_pdf', compact('event'));

        // Format kertas: Folio (F4) atau A4, posisi Landscape
        $pdf->setPaper('A4', 'potrait');

        return $pdf->download('Biodata_Peserta_' . $event->tema . '.pdf');
    }

    public function exportRekapPdf($id)
    {
        $event = MakestaEvent::with(['pesertas', 'materis.nilais'])->findOrFail($id);

        if ($event->pesertas->count() == 0) {
            return redirect()->back()->withErrors(['Belum ada peserta yang bisa diexport.']);
        }

        // Load view khusus export
        $pdf = Pdf::loadView('admin.makesta.export_rekap_pdf', compact('event'));

        // Format kertas: Folio/F4 atau Legal, posisi Landscape agar tabel panjangnya muat
        $pdf->setPaper('legal', 'landscape');

        return $pdf->download('Rekap_Nilai_' . $event->tema . '.pdf');
    }

    public function exportRekapExcel($id)
    {
        $event = MakestaEvent::with(['pesertas', 'materis.nilais'])->findOrFail($id);

        if ($event->pesertas->count() == 0) {
            return redirect()->back()->withErrors(['Belum ada peserta yang bisa diexport.']);
        }

        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\RekapNilaiExport($event), 'Rekap_Nilai_' . $event->tema . '.xlsx');
    }

    public function rekapEvaluasi($id)
    {
        // Tarik data event beserta materi dan semua evaluasi yang masuk
        $event = MakestaEvent::with(['materis'])->findOrFail($id);

        $evaluasis = MakestaEvaluasi::whereHas('peserta', function ($query) use ($id) {
            $query->where('makesta_event_id', $id);
        })->get();

        // Kelompokkan data berdasarkan tipe evaluasi untuk mempermudah visualisasi di Blade
        $evaluasiPemateri = $evaluasis->where('tipe_evaluasi', 'pemateri');
        $evaluasiPanitia = $evaluasis->where('tipe_evaluasi', 'panitia');
        $evaluasiInstruktur = $evaluasis->where('tipe_evaluasi', 'instruktur');
        $evaluasiRefleksi = $evaluasis->where('tipe_evaluasi', 'refleksi')->load('peserta');

        return view('admin.makesta.rekap_evaluasi', compact(
            'event',
            'evaluasiPemateri',
            'evaluasiPanitia',
            'evaluasiInstruktur',
            'evaluasiRefleksi'
        ));
    }
}
