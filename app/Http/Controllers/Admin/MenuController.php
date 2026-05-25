<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $menus = Menu::with('parent')
            ->orderBy('parent_id')
            ->orderBy('urutan')
            ->get();

        return view('admin.menus.index', compact('menus'));
    }

    public function create()
    {
        $parents = Menu::whereNull('parent_id')->get();
        return view('admin.menus.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:50',
            'icon' => 'nullable|string|max:50',
            'route' => 'required|string|max:100',
            'permission_required' => 'nullable|string|max:100',
            'urutan' => 'nullable|integer',
            'status' => 'required|in:active,inactive',
            'parent_id' => 'nullable|exists:menus,id'
        ]);

        Menu::create([
            'parent_id' => $request->parent_id,
            'title' => $request->title,
            'icon' => $request->icon ?? 'fas fa-circle',
            'route' => $request->route,
            'permission_required' => $request->permission_required,
            'urutan' => $request->urutan ?? 0,
            'status' => $request->status
        ]);

        return redirect()->route('menus.index')->with('success', 'Menu berhasil ditambahkan');
    }

    public function edit(Menu $menu)
    {
        $parents = Menu::whereNull('parent_id')
            ->where('id', '!=', $menu->id)
            ->get();
        return view('admin.menus.edit', compact('menu', 'parents'));
    }

    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'title' => 'required|string|max:50',
            'icon' => 'nullable|string|max:50',
            'route' => 'required|string|max:100',
            'permission_required' => 'nullable|string|max:100',
            'urutan' => 'nullable|integer',
            'status' => 'required|in:active,inactive',
            'parent_id' => 'nullable|exists:menus,id'
        ]);

        // Cegah parent_id指向自己
        if ($request->parent_id == $menu->id) {
            return back()->with('error', 'Menu tidak bisa menjadi parent dari dirinya sendiri');
        }

        $menu->update([
            'parent_id' => $request->parent_id,
            'title' => $request->title,
            'icon' => $request->icon ?? 'fas fa-circle',
            'route' => $request->route,
            'permission_required' => $request->permission_required,
            'urutan' => $request->urutan ?? 0,
            'status' => $request->status
        ]);

        return redirect()->route('menus.index')->with('success', 'Menu berhasil diupdate');
    }

    public function destroy(Menu $menu)
    {
        // Hapus semua submenu terlebih dahulu
        Menu::where('parent_id', $menu->id)->delete();
        $menu->delete();

        return redirect()->route('menus.index')->with('success', 'Menu berhasil dihapus');
    }
}
