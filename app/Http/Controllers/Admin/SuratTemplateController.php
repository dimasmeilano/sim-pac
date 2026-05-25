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
        $templates = SuratTemplate::orderBy('nama')->paginate(10);
        return view('admin.surat.template.index', compact('templates'));
    }

    public function create()
    {
        return view('admin.surat.template.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'kode' => 'required|string|max:50|unique:surat_templates',
            'konten' => 'required|string',
            'jenis' => 'required|in:keluar,masuk',
        ]);

        // Extract placeholder dari konten {contoh}
        preg_match_all('/\{([a-zA-Z0-9_]+)\}/', $request->konten, $matches);
        $placeholders = $matches[1] ?? [];

        SuratTemplate::create([
            'nama' => $request->nama,
            'kode' => $request->kode,
            'konten' => $request->konten,
            'placeholder' => $placeholders,
            'jenis' => $request->jenis,
            'status' => $request->status ?? 'aktif',
        ]);

        return redirect()->route('surat.template.index')->with('success', 'Template berhasil ditambahkan');
    }

    public function edit(SuratTemplate $template)
    {
        return view('admin.surat.template.edit', compact('template'));
    }

    public function update(Request $request, SuratTemplate $template)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'kode' => 'required|string|max:50|unique:surat_templates,kode,' . $template->id,
            'konten' => 'required|string',
            'jenis' => 'required|in:keluar,masuk',
        ]);

        preg_match_all('/\{([a-zA-Z0-9_]+)\}/', $request->konten, $matches);
        $placeholders = $matches[1] ?? [];

        $template->update([
            'nama' => $request->nama,
            'kode' => $request->kode,
            'konten' => $request->konten,
            'placeholder' => $placeholders,
            'jenis' => $request->jenis,
            'status' => $request->status ?? 'aktif',
        ]);

        return redirect()->route('surat.template.index')->with('success', 'Template berhasil diupdate');
    }

    public function destroy(SuratTemplate $template)
    {
        $template->delete();
        return redirect()->route('surat.template.index')->with('success', 'Template berhasil dihapus');
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
