<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SuratTemplate;
use Illuminate\Http\Request;

class SuratTemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:manage_surat');
    }

    public function index()
    {
        $templates = SuratTemplate::orderBy('urutan')->orderBy('nama')->paginate(10);
        return view('admin.surat.template.index', compact('templates'));
    }

    public function create()
    {
        return view('admin.surat.template.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'        => 'required|string|max:100',
            'kode'        => 'required|string|max:50|unique:surat_templates',
            'jenis'       => 'required|in:keluar,masuk',
            'jenis_surat' => 'required|string', // khusus, umum, keputusan, dll
            'konten'      => 'required|string',
            'fields'      => 'nullable|string', // Teks JSON dari textarea
            'urutan'      => 'nullable|integer',
        ]);

        // 1. Proses Fields JSON
        $fieldsData = null;
        if ($request->filled('fields')) {
            $fieldsData = json_decode($request->fields, true);
            // Cek apakah format JSON valid
            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withInput()->with('error', 'Gagal menyimpan! Format Aturan Form Input (JSON) tidak valid. Silakan periksa kembali tanda kutip atau koma.');
            }
        }

        // 2. Extract placeholder otomatis dari konten {contoh_variabel}
        preg_match_all('/\{([a-zA-Z0-9_]+)\}/', $request->konten, $matches);
        $placeholders = $matches[1] ?? [];

        // 3. Simpan ke Database
        SuratTemplate::create([
            'nama'        => $request->nama,
            'kode'        => $request->kode,
            'jenis'       => $request->jenis,
            'jenis_surat' => $request->jenis_surat,
            'klasifikasi' => $request->klasifikasi,
            'lampiran'    => $request->lampiran,
            'urutan'      => $request->urutan ?? 0,
            'konten'      => $request->konten,
            'fields'      => $fieldsData,
            'placeholder' => $placeholders,
            'status'      => $request->status ?? 'aktif',
        ]);

        return redirect()->route('surat.template.index')->with('success', 'Template berhasil ditambahkan!');
    }

    public function edit(SuratTemplate $template)
    {
        return view('admin.surat.template.edit', compact('template'));
    }

    public function update(Request $request, SuratTemplate $template)
    {
        $request->validate([
            'nama'        => 'required|string|max:100',
            'kode'        => 'required|string|max:50|unique:surat_templates,kode,' . $template->id,
            'jenis'       => 'required|in:keluar,masuk',
            'jenis_surat' => 'required|string',
            'konten'      => 'required|string',
            'fields'      => 'nullable|string',
            'urutan'      => 'nullable|integer',
        ]);

        // 1. Proses Fields JSON
        $fieldsData = null;
        if ($request->filled('fields')) {
            $fieldsData = json_decode($request->fields, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withInput()->with('error', 'Gagal memperbarui! Format Aturan Form Input (JSON) tidak valid. Pastikan formatnya benar.');
            }
        }

        // 2. Extract placeholder otomatis
        preg_match_all('/\{([a-zA-Z0-9_]+)\}/', $request->konten, $matches);
        $placeholders = $matches[1] ?? [];

        // 3. Update Database
        $template->update([
            'nama'        => $request->nama,
            'kode'        => $request->kode,
            'jenis'       => $request->jenis,
            'jenis_surat' => $request->jenis_surat,
            'klasifikasi' => $request->klasifikasi,
            'lampiran'    => $request->lampiran,
            'urutan'      => $request->urutan ?? 0,
            'konten'      => $request->konten,
            'fields'      => $fieldsData,
            'placeholder' => $placeholders,
            'status'      => $request->status ?? 'aktif',
        ]);

        return redirect()->route('surat.template.index')->with('success', 'Template berhasil diperbarui!');
    }

    public function destroy(SuratTemplate $template)
    {
        $template->delete();
        return redirect()->route('surat.template.index')->with('success', 'Template berhasil dihapus!');
    }

    public function getPlaceholder(Request $request)
    {
        try {
            $id = $request->input('id');

            if (!$id) {
                return response()->json(['error' => 'ID template tidak ditemukan'], 400);
            }

            $template = SuratTemplate::find($id);

            if (!$template) {
                return response()->json(['error' => 'Template tidak ditemukan'], 404);
            }

            // Pastikan placeholder dalam bentuk array
            $placeholders = $template->placeholder;
            if (is_string($placeholders)) {
                $placeholders = json_decode($placeholders, true) ?? [];
            }

            return response()->json([
                'konten' => $template->konten,
                'placeholder' => $placeholders
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
