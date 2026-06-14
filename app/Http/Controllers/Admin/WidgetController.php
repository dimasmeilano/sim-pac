<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Widget;
use Illuminate\Http\Request;

class WidgetController extends Controller
{
    public function index()
    {
        // Urutkan berdasarkan kolom 'urutan' dari yang terkecil
        $widgets = Widget::orderBy('urutan', 'asc')->get();
        return view('admin.widget.index', compact('widgets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_widget' => 'required|string|max:255',
            'isi_html'    => 'required|string',
            'urutan'      => 'required|integer'
        ]);

        Widget::create($request->all());

        return redirect()->route('widget.index')->with('success', 'Widget berhasil ditambahkan!');
    }

    public function update(Request $request, Widget $widget)
    {
        $request->validate([
            'nama_widget'  => 'required|string|max:255',
            'isi_html'     => 'required|string',
            'urutan'       => 'required|integer',
            'status_aktif' => 'required|boolean'
        ]);

        $widget->update($request->all());

        return redirect()->route('widget.index')->with('success', 'Widget berhasil diperbarui!');
    }

    public function destroy(Widget $widget)
    {
        $widget->delete();
        return redirect()->route('widget.index')->with('success', 'Widget dihapus!');
    }
}
