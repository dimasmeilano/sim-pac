<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class ModulBaruMenuSeeder extends Seeder
{
    public function run(): void
    {
        // ========================================================
        // 1. PARENT MENU: ALUMNI & DONASI
        // ========================================================
        // Cari berdasarkan nama menu, biarkan ID terisi otomatis
        $parentAlumni = Menu::firstOrCreate(
            ['title' => 'Jaringan & Donasi'],
            [
                'icon'                => 'fas fa-handshake',
                'route'               => '#',
                'permission_required' => 'view_alumni',
                'urutan'              => 8,
                'status'              => 'active' // Mengikuti struktur MenuController Anda
            ]
        );

        // Sub-Menu: Data Alumni
        Menu::firstOrCreate(
            ['route' => '/alumni'],
            [
                'parent_id'           => $parentAlumni->id,
                'title'               => 'Data Alumni',
                'icon'                => 'fas fa-user-graduate',
                'permission_required' => 'view_alumni',
                'urutan'              => 1,
                'status'              => 'active'
            ]
        );

        // Sub-Menu: Fundraising / Donasi
        Menu::firstOrCreate(
            ['route' => '/donasi'],
            [
                'parent_id'           => $parentAlumni->id,
                'title'               => 'Fundraising',
                'icon'                => 'fas fa-donate',
                'permission_required' => 'view_donasi',
                'urutan'              => 2,
                'status'              => 'active'
            ]
        );

        // ========================================================
        // 2. PARENT MENU: PENILAIAN ORGANISASI (AKREDITASI)
        // ========================================================
        $parentPenilaian = Menu::firstOrCreate(
            ['title' => 'Penilaian Organisasi'],
            [
                'icon'                => 'fas fa-star',
                'route'               => '#',
                'permission_required' => 'view_akreditasi',
                'urutan'              => 9,
                'status'              => 'active'
            ]
        );

        // Sub-Menu 1: Pengajuan Akreditasi
        Menu::updateOrCreate(
            ['title' => 'Akreditasi & Klaster'], // Cari nama yang lama
            [
                'parent_id'           => $parentPenilaian->id,
                'title'               => 'Pengajuan Akreditasi', // Ubah namanya
                'icon'                => 'fas fa-award',
                'route'               => '/akreditasi/pengajuan', // Pastikan URL sesuai
                'permission_required' => 'view_akreditasi',
                'urutan'              => 1,
                'status'              => 'active'
            ]
        );

        // Sub-Menu 2: Klasterisasi (Baru)
        Menu::firstOrCreate(
            ['route' => '/klasterisasi'],
            [
                'parent_id'           => $parentPenilaian->id,
                'title'               => 'Data Klasterisasi',
                'icon'                => 'fas fa-map-marked-alt',
                'permission_required' => 'view_klasterisasi',
                'urutan'              => 2,
                'status'              => 'active'
            ]
        );

        $this->command->info('Menu Jaringan & Penilaian berhasil ditambahkan (Tanpa bentrok ID)!');
    }
}
