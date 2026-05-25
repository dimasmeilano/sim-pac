<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'manage_role',
            'manage_menu',
            'view_anggota',
            'create_anggota',
            'edit_anggota',
            'delete_anggota',
            'view_progja',
            'create_progja',
            'edit_progja',
            'delete_progja',
            'view_keuangan',
            'create_keuangan',
            'view_absensi',
            'create_absensi',
            'view_surat',
            'create_surat',
            'sign_surat',
            'view_organization',
            'create_organization',
            'edit_organization',
            'delete_organization',
            'view_kegiatan',
            'create_kegiatan',
            'edit_kegiatan',
            'delete_kegiatan',
            'manage_surat',
            'edit_surat',
            'delete_surat',
            'cetak_surat',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $superAdmin = Role::create(['name' => 'super_admin']);
        $superAdmin->givePermissionTo(Permission::all());

        Role::create(['name' => 'admin_pac']);
        Role::create(['name' => 'ketua_pac']);
        Role::create(['name' => 'wakil_ketua_pac']);
        Role::create(['name' => 'sekretaris_pac']);
        Role::create(['name' => 'wakil_sekretaris_pac']);
        Role::create(['name' => 'bendahara_pac']);
        Role::create(['name' => 'wakil_bendahara_pac']);
        Role::create(['name' => 'ketua_ranting']);
        Role::create(['name' => 'wakil_ketua_ranting']);
        Role::create(['name' => 'sekretaris_ranting']);
        Role::create(['name' => 'wakil_sekretaris_ranting']);
        Role::create(['name' => 'bendahara_ranting']);
        Role::create(['name' => 'wakil_bendahara_ranting']);
        Role::create(['name' => 'anggota_biasa']);
    }
}
