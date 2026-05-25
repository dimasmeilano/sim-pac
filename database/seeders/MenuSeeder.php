<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Main Menus
        $dashboard = Menu::create(['title' => 'Dashboard', 'icon' => 'fas fa-tachometer-alt', 'route' => '/dashboard', 'urutan' => 1]);
        $anggota = Menu::create(['title' => 'Anggota', 'icon' => 'fas fa-users', 'route' => '/anggota', 'urutan' => 2]);
        $progja = Menu::create(['title' => 'Program Kerja', 'icon' => 'fas fa-tasks', 'route' => '/program-kerja', 'urutan' => 3]);
        $keuangan = Menu::create(['title' => 'Keuangan', 'icon' => 'fas fa-money-bill', 'route' => '/keuangan', 'urutan' => 4]);
        $absensi = Menu::create(['title' => 'Absensi', 'icon' => 'fas fa-calendar-check', 'route' => '/absensi', 'urutan' => 5]);
        $surat = Menu::create(['title' => 'Surat', 'icon' => 'fas fa-envelope', 'route' => '/surat', 'urutan' => 6]);
        $kegiatan = Menu::create(['title' => 'Kegiatan & Absensi', 'icon' => 'fas fa-calendar-check', 'route' => '/kegiatan', 'permission_required' => 'view_kegiatan', 'urutan' => 4,]);
        $pengaturan = Menu::create(['title' => 'Pengaturan', 'icon' => 'fas fa-cog', 'route' => '#', 'urutan' => 99]);

        // Sub menus Pengaturan
        Menu::create(['parent_id' => $pengaturan->id, 'title' => 'Role & Permission', 'icon' => 'fas fa-lock', 'route' => '/settings/roles', 'permission_required' => 'manage_role', 'urutan' => 1]);
        Menu::create(['parent_id' => $pengaturan->id, 'title' => 'Menu Manager', 'icon' => 'fas fa-bars', 'route' => '/settings/menus', 'permission_required' => 'manage_menu', 'urutan' => 2]);
    }
}
