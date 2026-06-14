<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        // ==========================================
        // 1. MENU UTAMA (TANPA PARENT)
        // ==========================================

        Menu::firstOrCreate(['id' => 1], [
            'title' => 'Dashboard',
            'icon' => 'fas fa-tachometer-alt',
            'route' => '/dashboard',
            'permission_required' => null,
            'urutan' => 1,
            'is_active' => 1
        ]);

        Menu::firstOrCreate(['id' => 13], [
            'title' => 'Organisasi',
            'icon' => 'fas fa-building',
            'route' => '/organizations',
            'permission_required' => 'view_organization',
            'urutan' => 1,
            'is_active' => 1
        ]);

        Menu::firstOrCreate(['id' => 2], [
            'title' => 'Anggota',
            'icon' => 'fas fa-users',
            'route' => '/members',
            'permission_required' => 'view_anggota',
            'urutan' => 2,
            'is_active' => 1
        ]);

        Menu::firstOrCreate(['id' => 3], [
            'title' => 'Program Kerja',
            'icon' => 'fas fa-list',
            'route' => '/progja',
            'permission_required' => 'manage_progja',
            'urutan' => 3,
            'is_active' => 1
        ]);

        Menu::firstOrCreate(['id' => 4], [
            'title' => 'Keuangan',
            'icon' => 'fas fa-money-bill',
            'route' => '/keuangan',
            'permission_required' => 'view_keuangan',
            'urutan' => 4,
            'is_active' => 1
        ]);

        Menu::firstOrCreate(['id' => 14], [
            'title' => 'Kegiatan',
            'icon' => 'fas fa-calendar-check',
            'route' => '/kegiatan',
            'permission_required' => 'manage_kegiatan',
            'urutan' => 4,
            'is_active' => 1
        ]);

        Menu::firstOrCreate(['id' => 5], [
            'title' => 'Absensi',
            'icon' => 'fas fa-clipboard-check',
            'route' => '/absensi/scan',
            'permission_required' => 'view_absensi',
            'urutan' => 5,
            'is_active' => 1
        ]);

        Menu::firstOrCreate(['id' => 20], [
            'title' => 'Pengajuan Rekomendasi',
            'icon' => 'fas fa-file-alt',
            'route' => '/pengajuan',
            'permission_required' => 'manage_pengajuan',
            'urutan' => 6,
            'is_active' => 1
        ]);

        Menu::firstOrCreate(['id' => 23], [
            'title' => 'Inventaris',
            'icon' => 'fas fa-briefcase',
            'route' => '/inventaris',
            'permission_required' => null,
            'urutan' => 10,
            'is_active' => 1
        ]);

        Menu::firstOrCreate(['id' => 24], [
            'title' => 'Notulensi Rapat',
            'icon' => 'fas fa-file-signature',
            'route' => '/notulensi',
            'permission_required' => null,
            'urutan' => 11,
            'is_active' => 1
        ]);

        Menu::firstOrCreate(['id' => 25], [
            'title' => 'Audit Sistem',
            'icon' => 'fas fa-code-branch',
            'route' => '/audit-trail',
            'permission_required' => null,
            'urutan' => 12,
            'is_active' => 1
        ]);

        Menu::firstOrCreate(['id' => 26], [
            'title' => 'E - Library & Repository',
            'icon' => 'fas fa-book',
            'route' => '/dokumen',
            'permission_required' => null,
            'urutan' => 13,
            'is_active' => 1
        ]);

        Menu::firstOrCreate(['id' => 27], [
            'title' => 'Galeri',
            'icon' => 'fas fa-archive',
            'route' => '/workspace',
            'permission_required' => null,
            'urutan' => 14,
            'is_active' => 1
        ]);

        Menu::firstOrCreate(['id' => 34], [
            'title' => 'Makesta',
            'icon' => 'fas fa-circle',
            'route' => '/makesta-event',
            'permission_required' => null,
            'urutan' => 15,
            'is_active' => 1
        ]);

        // ==========================================
        // 2. PARENT MENUS (Menampung Sub-Menu)
        // ==========================================

        $suratArsip = Menu::firstOrCreate(['id' => 15], [
            'title' => 'Surat & Arsip',
            'icon' => 'fas fa-envelope',
            'route' => '#',
            'permission_required' => null,
            'urutan' => 5,
            'is_active' => 1
        ]);

        $adminWeb = Menu::firstOrCreate(['id' => 28], [
            'title' => 'Admin Web',
            'icon' => 'fas fa-globe',
            'route' => '#',
            'permission_required' => null,
            'urutan' => 15,
            'is_active' => 1
        ]);

        $pengaturan = Menu::firstOrCreate(['id' => 10], [
            'title' => 'Pengaturan',
            'icon' => 'fas fa-cog',
            'route' => '/settings',
            'permission_required' => null,
            'urutan' => 99,
            'is_active' => 1
        ]);

        // ==========================================
        // 3. SUB-MENU (ANAK DARI PARENT)
        // ==========================================

        // --- Sub Surat & Arsip ---
        Menu::firstOrCreate(['id' => 16], [
            'parent_id' => $suratArsip->id,
            'title' => 'Surat Keluar',
            'icon' => 'fas fa-paper-plane',
            'route' => '/surat/keluar',
            'permission_required' => 'view_surat',
            'urutan' => 1,
            'is_active' => 1
        ]);

        Menu::firstOrCreate(['id' => 17], [
            'parent_id' => $suratArsip->id,
            'title' => 'Surat Masuk',
            'icon' => 'fas fa-inbox',
            'route' => '/surat/masuk',
            'permission_required' => 'view_surat',
            'urutan' => 2,
            'is_active' => 1
        ]);

        // --- Sub Admin Web ---
        Menu::firstOrCreate(['id' => 29], [
            'parent_id' => $adminWeb->id,
            'title' => 'Artikel',
            'icon' => 'fas fa-calculator',
            'route' => '/artikel',
            'permission_required' => null,
            'urutan' => 1,
            'is_active' => 1
        ]);

        Menu::firstOrCreate(['id' => 31], [
            'parent_id' => $adminWeb->id,
            'title' => 'Kategori',
            'icon' => 'fas fa-rss',
            'route' => '/kategori',
            'permission_required' => null,
            'urutan' => 2,
            'is_active' => 1
        ]);

        Menu::firstOrCreate(['id' => 32], [
            'parent_id' => $adminWeb->id,
            'title' => 'Identitas Web',
            'icon' => 'fas fa-cogs',
            'route' => '/identitas-web',
            'permission_required' => null,
            'urutan' => 3,
            'is_active' => 1
        ]);

        Menu::firstOrCreate(['id' => 33], [
            'parent_id' => $adminWeb->id,
            'title' => 'Media Sosial',
            'icon' => 'fas fa-hashtag',
            'route' => '/media-sosial',
            'permission_required' => null,
            'urutan' => 4,
            'is_active' => 1
        ]);

        // --- Sub Pengaturan ---
        Menu::firstOrCreate(['id' => 11], [
            'parent_id' => $pengaturan->id,
            'title' => 'Role & Permissions',
            'icon' => 'fas fa-lock',
            'route' => '/settings/roles',
            'permission_required' => 'manage_role',
            'urutan' => 1,
            'is_active' => 1
        ]);

        Menu::firstOrCreate(['id' => 21], [
            'parent_id' => $pengaturan->id,
            'title' => 'Profil Saya',
            'icon' => 'fas fa-user',
            'route' => '/profile',
            'permission_required' => null,
            'urutan' => 1,
            'is_active' => 1
        ]);

        Menu::firstOrCreate(['id' => 12], [
            'parent_id' => $pengaturan->id,
            'title' => 'Menu Manager',
            'icon' => 'fas fa-bars',
            'route' => '/settings/menus',
            'permission_required' => 'manage_menu',
            'urutan' => 2,
            'is_active' => 1
        ]);

        Menu::firstOrCreate(['id' => 22], [
            'parent_id' => $pengaturan->id,
            'title' => 'Profil Organisasi Saya',
            'icon' => 'fas fa-building',
            'route' => '/organisasi-saya',
            'permission_required' => 'manage_surat',
            'urutan' => 2,
            'is_active' => 1
        ]);

        Menu::firstOrCreate(['id' => 30], [
            'parent_id' => $pengaturan->id,
            'title' => 'Role User',
            'icon' => 'fas fa-user',
            'route' => '/settings/user-role',
            'permission_required' => 'manage_role',
            'urutan' => 5,
            'is_active' => 1
        ]);
    }
}
