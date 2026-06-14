<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SliderController extends Controller
{
    public function index()
    {
        $sliders = Slider::latest()->get();
        return view('admin.slider.index', compact('sliders'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'gambar'            => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'judul'             => 'nullable|string|max:255',
            'deskripsi_singkat' => 'nullable|string|max:255',
        ]);

        $data = $request->only(['judul', 'deskripsi_singkat']);
        $data['status_aktif'] = 1; // Otomatis aktif saat pertama dibuat

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('sliders', 'public');
        }

        Slider::create($data);

        return redirect()->route('slider.index')->with('success', 'Slider Banner berhasil ditambahkan!');
    }

    public function update(Request $request, Slider $slider)
    {
        $request->validate([
            'gambar'            => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'judul'             => 'nullable|string|max:255',
            'deskripsi_singkat' => 'nullable|string|max:255',
            'status_aktif'      => 'required|boolean',
        ]);

        $data = $request->only(['judul', 'deskripsi_singkat', 'status_aktif']);

        if ($request->hasFile('gambar')) {
            // Hapus gambar lama
            if ($slider->gambar) {
                Storage::disk('public')->delete($slider->gambar);
            }
            // Simpan gambar baru
            $data['gambar'] = $request->file('gambar')->store('sliders', 'public');
        }

        $slider->update($data);

        return redirect()->route('slider.index')->with('success', 'Slider Banner berhasil diperbarui!');
    }

    public function destroy(Slider $slider)
    {
        // Hapus gambar dari storage
        if ($slider->gambar) {
            Storage::disk('public')->delete($slider->gambar);
        }

        $slider->delete();

        return redirect()->route('slider.index')->with('success', 'Slider Banner berhasil dihapus!');
    }
}
