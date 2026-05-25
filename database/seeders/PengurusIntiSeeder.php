<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Organization;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PengurusIntiSeeder extends Seeder
{
    public function run(): void
    {
        // ========== 1. PASTIKAN ROLE DAN PERMISSION ADA ==========
        $roles = [
            'ketua_pac',
            'wakil_ketua_pac',
            'sekretaris_pac',
            'wakil_sekretaris_pac',
            'bendahara_pac',
            'wakil_bendahara_pac'
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        Permission::firstOrCreate(['name' => 'view_keuangan']);

        // Assign permission ke bendahara
        $bendaharaRole = Role::findByName('bendahara_pac');
        $bendaharaRole->givePermissionTo('view_keuangan');

        // ========== 2. CARI ORGANISASI PAC ==========
        $pac = Organization::where('type', 'pac')->first();

        if (!$pac) {
            $this->command->error('Organisasi PAC tidak ditemukan! Buat dulu melalui menu Organisasi.');
            return;
        }

        // ========== 3. BUAT USER ATAU CARI USER YANG SUDAH ADA ==========

        // Ketua
        $ketua = User::firstOrCreate(
            ['email' => 'ketua@sim-pac.com'],
            [
                'name' => 'Ketua PAC',
                'password' => bcrypt('password'),
                'nik' => '1234567890123456',
                'no_hp' => '081234567890',
                'status_anggota' => 'aktif',
                'organization_id' => $pac->id,
            ]
        );
        $ketua->assignRole('ketua_pac');

        // Wakil Ketua 1
        $wakilKetua1 = User::firstOrCreate(
            ['email' => 'wakil1@sim-pac.com'],
            [
                'name' => 'Wakil Ketua 1',
                'password' => bcrypt('password'),
                'nik' => '1234567890123457',
                'no_hp' => '081234567891',
                'status_anggota' => 'aktif',
                'organization_id' => $pac->id,
            ]
        );
        $wakilKetua1->assignRole('wakil_ketua_pac');

        // Wakil Ketua 2
        $wakilKetua2 = User::firstOrCreate(
            ['email' => 'wakil2@sim-pac.com'],
            [
                'name' => 'Wakil Ketua 2',
                'password' => bcrypt('password'),
                'nik' => '1234567890123458',
                'no_hp' => '081234567892',
                'status_anggota' => 'aktif',
                'organization_id' => $pac->id,
            ]
        );
        $wakilKetua2->assignRole('wakil_ketua_pac');

        // Wakil Ketua 3
        $wakilKetua3 = User::firstOrCreate(
            ['email' => 'wakil3@sim-pac.com'],
            [
                'name' => 'Wakil Ketua 3',
                'password' => bcrypt('password'),
                'nik' => '1234567890123459',
                'no_hp' => '081234567893',
                'status_anggota' => 'aktif',
                'organization_id' => $pac->id,
            ]
        );
        $wakilKetua3->assignRole('wakil_ketua_pac');

        // Sekretaris
        $sekretaris = User::firstOrCreate(
            ['email' => 'sekretaris@sim-pac.com'],
            [
                'name' => 'Sekretaris PAC',
                'password' => bcrypt('password'),
                'nik' => '1234567890123460',
                'no_hp' => '081234567894',
                'status_anggota' => 'aktif',
                'organization_id' => $pac->id,
            ]
        );
        $sekretaris->assignRole('sekretaris_pac');

        // Wakil Sekretaris 1
        $wakilSekretaris1 = User::firstOrCreate(
            ['email' => 'wakilsek1@sim-pac.com'],
            [
                'name' => 'Wakil Sekretaris 1',
                'password' => bcrypt('password'),
                'nik' => '1234567890123461',
                'no_hp' => '081234567895',
                'status_anggota' => 'aktif',
                'organization_id' => $pac->id,
            ]
        );
        $wakilSekretaris1->assignRole('wakil_sekretaris_pac');

        // Wakil Sekretaris 2
        $wakilSekretaris2 = User::firstOrCreate(
            ['email' => 'wakilsek2@sim-pac.com'],
            [
                'name' => 'Wakil Sekretaris 2',
                'password' => bcrypt('password'),
                'nik' => '1234567890123462',
                'no_hp' => '081234567896',
                'status_anggota' => 'aktif',
                'organization_id' => $pac->id,
            ]
        );
        $wakilSekretaris2->assignRole('wakil_sekretaris_pac');

        // Wakil Sekretaris 3
        $wakilSekretaris3 = User::firstOrCreate(
            ['email' => 'wakilsek3@sim-pac.com'],
            [
                'name' => 'Wakil Sekretaris 3',
                'password' => bcrypt('password'),
                'nik' => '1234567890123463',
                'no_hp' => '081234567897',
                'status_anggota' => 'aktif',
                'organization_id' => $pac->id,
            ]
        );
        $wakilSekretaris3->assignRole('wakil_sekretaris_pac');

        // Bendahara
        $bendahara = User::firstOrCreate(
            ['email' => 'bendahara@sim-pac.com'],
            [
                'name' => 'Bendahara PAC',
                'password' => bcrypt('password'),
                'nik' => '1234567890123464',
                'no_hp' => '081234567898',
                'status_anggota' => 'aktif',
                'organization_id' => $pac->id,
            ]
        );
        $bendahara->assignRole('bendahara_pac');
        $bendahara->givePermissionTo('view_keuangan');

        // Wakil Bendahara 1
        $wakilBendahara1 = User::firstOrCreate(
            ['email' => 'wakilbendahara1@sim-pac.com'],
            [
                'name' => 'Wakil Bendahara 1',
                'password' => bcrypt('password'),
                'nik' => '1234567890123465',
                'no_hp' => '081234567899',
                'status_anggota' => 'aktif',
                'organization_id' => $pac->id,
            ]
        );
        $wakilBendahara1->assignRole('wakil_bendahara_pac');

        // ========== 4. ASSIGN KE ORGANISASI ==========
        $pac->ketua_id = $ketua->id;
        $pac->wakil_ketua_1_id = $wakilKetua1->id;
        $pac->wakil_ketua_2_id = $wakilKetua2->id;
        $pac->wakil_ketua_3_id = $wakilKetua3->id;
        $pac->sekretaris_id = $sekretaris->id;
        $pac->wakil_sekretaris_1_id = $wakilSekretaris1->id;
        $pac->wakil_sekretaris_2_id = $wakilSekretaris2->id;
        $pac->wakil_sekretaris_3_id = $wakilSekretaris3->id;
        $pac->bendahara_id = $bendahara->id;
        $pac->wakil_bendahara_1_id = $wakilBendahara1->id;
        $pac->save();

        // ========== 5. OUTPUT ==========
        $this->command->info('====================================');
        $this->command->info('✅ PENGURUS INTI BERHASIL DIASSIGN');
        $this->command->info('====================================');
        $this->command->info('Organisasi: ' . $pac->name);
        $this->command->info('Ketua: ' . $ketua->name);
        $this->command->info('Wakil Ketua: ' . $wakilKetua1->name . ', ' . $wakilKetua2->name . ', ' . $wakilKetua3->name);
        $this->command->info('Sekretaris: ' . $sekretaris->name);
        $this->command->info('Bendahara: ' . $bendahara->name);
        $this->command->info('====================================');
        $this->command->info('Email login:');
        $this->command->info('- ketua@sim-pac.com / password');
        $this->command->info('- bendahara@sim-pac.com / password');
        $this->command->info('- wakilbendahara1@sim-pac.com / password');
        $this->command->info('====================================');
    }
}
