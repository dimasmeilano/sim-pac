<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ModulBaruPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ========================================================
        // 1. BUAT DAFTAR PERMISSION BARU
        // ========================================================
        $permissions = [
            // Modul Alumni
            'view_alumni',
            'manage_alumni',

            // Modul Donasi & Fundraising
            'view_donasi',
            'manage_donasi',
            'verify_donasi', // Khusus Bendahara

            // Modul Akreditasi
            'view_akreditasi',
            'manage_akreditasi', // Untuk Ranting (Mengisi Borang)
            'nilai_akreditasi',  // Khusus Pimpinan (PAC/Cabang) untuk menilai

            // Modul Klasterisasi
            'view_klasterisasi',
            'manage_klasterisasi', // Untuk Ranting (Mengisi form)
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // ========================================================
        // 2. AMBIL ROLE YANG SUDAH ADA DI DATABASE
        // ========================================================
        $superAdmin = Role::where('name', 'super_admin')->first();

        $ketuaPac = Role::where('name', 'ketua_pac')->first();
        $sekretarisPac = Role::where('name', 'sekretaris_pac')->first();
        $bendaharaPac = Role::where('name', 'bendahara_pac')->first();

        $ketuaRanting = Role::where('name', 'ketua_ranting')->first();
        $sekretarisRanting = Role::where('name', 'sekretaris_ranting')->first();
        $bendaharaRanting = Role::where('name', 'bendahara_ranting')->first();

        // ========================================================
        // 3. DISTRIBUSI HAK AKSES (SESUAI SOP ORGANISASI)
        // ========================================================

        // A. Super Admin (Dapat Semua Hak)
        if ($superAdmin) {
            $superAdmin->givePermissionTo(Permission::all());
        }

        // B. Ketua & Sekretaris PAC
        $pacPimpinanRoles = array_filter([$ketuaPac, $sekretarisPac]);
        foreach ($pacPimpinanRoles as $role) {
            $role->givePermissionTo([
                'view_alumni',
                'manage_alumni',
                'view_donasi',
                'manage_donasi',
                'view_akreditasi',
                'nilai_akreditasi', // PAC MENILAI
                'view_klasterisasi'
            ]);
        }

        // C. Ketua & Sekretaris Ranting
        $rantingPimpinanRoles = array_filter([$ketuaRanting, $sekretarisRanting]);
        foreach ($rantingPimpinanRoles as $role) {
            $role->givePermissionTo([
                'view_alumni',
                'manage_alumni',
                'view_donasi',
                'manage_donasi',
                'view_akreditasi',
                'manage_akreditasi', // RANTING MENGISI BORANG
                'view_klasterisasi',
                'manage_klasterisasi' // RANTING MENGISI KLASTER
            ]);
        }

        // D. Bendahara PAC & Ranting (Khusus Verifikasi Uang Donasi)
        $bendaharaRoles = array_filter([$bendaharaPac, $bendaharaRanting]);
        foreach ($bendaharaRoles as $role) {
            $role->givePermissionTo([
                'view_donasi',
                'manage_donasi',
                'verify_donasi' // TUGAS UTAMA BENDAHARA
            ]);
        }

        $this->command->info('Hak akses untuk modul Alumni, Donasi, Akreditasi, dan Klasterisasi berhasil ditambahkan!');
    }
}
