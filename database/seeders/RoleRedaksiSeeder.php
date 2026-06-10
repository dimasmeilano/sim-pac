<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleRedaksiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cache permission Spatie agar tidak bentrok
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. BUAT DAFTAR PERMISSION (HAK AKSES)
        $permissions = [
            'lihat artikel',
            'tulis artikel',
            'upload foto artikel',
            'review artikel',
            'publish artikel',
            'hapus artikel'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 2. BUAT ROLE & BAGIKAN PERMISSION-NYA

        // ROLE KONTRIBUTOR: Hanya bisa menulis dan melihat artikel
        $roleKontributor = Role::firstOrCreate(['name' => 'kontributor']);
        $roleKontributor->syncPermissions(['lihat artikel', 'tulis artikel']);

        // ROLE FOTOGRAFER: Hanya bisa melihat dan mengunggah foto
        $roleFotografer = Role::firstOrCreate(['name' => 'fotografer']);
        $roleFotografer->syncPermissions(['lihat artikel', 'upload foto artikel']);

        // ROLE EDITOR: Pemimpin Redaksi (Bisa melakukan segalanya di modul artikel)
        $roleEditor = Role::firstOrCreate(['name' => 'editor']);
        $roleEditor->syncPermissions([
            'lihat artikel',
            'tulis artikel',
            'upload foto artikel',
            'review artikel',
            'publish artikel',
            'hapus artikel'
        ]);

        // (Opsional) ROLE SUPER ADMIN: Bisa akses semua fitur aplikasi
        $roleSuperAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        // Super admin otomatis bypass semua permission (biasanya diatur di AuthServiceProvider)
    }
}
