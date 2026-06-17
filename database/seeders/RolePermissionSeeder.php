<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cache Spatie
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ==========================================
        // 1. DAFTAR SELURUH HAK AKSES (PERMISSIONS)
        // ==========================================
        $permissions = [
            // Core & Sistem
            'manage_role',
            'manage_menu',
            'view_audit',

            // Keanggotaan & Kaderisasi
            'view_anggota',
            'create_anggota',
            'edit_anggota',
            'delete_anggota',
            'view_makesta',
            'manage_makesta',

            // Program Kerja & Kegiatan
            'view_progja',
            'create_progja',
            'edit_progja',
            'delete_progja',
            'manage_progja',
            'view_kegiatan',
            'create_kegiatan',
            'edit_kegiatan',
            'delete_kegiatan',
            'manage_kegiatan',
            'view_absensi',
            'create_absensi',

            // Administrasi & Kesekretariatan
            'view_surat',
            'create_surat',
            'edit_surat',
            'delete_surat',
            'sign_surat',
            'manage_surat',
            'cetak_surat',
            'manage_pengajuan',
            'view_inventaris',
            'manage_inventaris',
            'view_notulensi',
            'manage_notulensi',

            // Arsip & Dokumen
            'view_dokumen',
            'manage_dokumen',
            'view_galeri',
            'manage_galeri',

            // Keuangan
            'view_keuangan',
            'create_keuangan',

            // Organisasi & Multi-Tenant
            'view_organization',
            'create_organization',
            'edit_organization',
            'delete_organization',

            // Admin Web Profil
            'view_web',
            'manage_web',
        ];


        // Masukkan Permission ke Database
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // ==========================================
        // 2. PEMBUATAN ROLE & DISTRIBUSI HAK AKSES
        // ==========================================

        // --- SUPER ADMIN ---
        // Memiliki akses absolut ke seluruh sistem
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->givePermissionTo(Permission::all());


        // --- KETUA PAC ---
        // Fokus: Monitoring, Persetujuan (Tanda Tangan), dan Evaluasi Organisasi
        $ketua = Role::firstOrCreate(['name' => 'ketua_pac']);
        $ketua->syncPermissions([
            'view_anggota',
            'view_makesta',
            'view_progja',
            'view_kegiatan',
            'view_absensi',
            'view_surat',
            'sign_surat',
            'manage_pengajuan',
            'view_notulensi',
            'view_dokumen',
            'view_keuangan',
            'view_organization'
        ]);

        // ==========================================
        // WAKIL KETUA PAC BERDASARKAN PEMBIDANGAN
        // ==========================================

        // Wakil Ketua 1 (Bidang Organisasi)
        // Memantau jalannya roda organisasi, ranting, dan persuratan
        $waka1 = Role::firstOrCreate(['name' => 'wakil_ketua_1_pac']);
        $waka1->syncPermissions([
            'view_organization',
            'view_anggota',
            'view_progja',
            'view_kegiatan',
            'view_surat'
        ]);

        // Wakil Ketua 2 (Bidang Kaderisasi)
        // Memantau pelaksanaan Makesta dan peningkatan kualitas anggota
        $waka2 = Role::firstOrCreate(['name' => 'wakil_ketua_2_pac']);
        $waka2->syncPermissions([
            'view_makesta',
            'view_anggota',
            'view_progja',
            'view_kegiatan'
        ]);

        // Wakil Ketua 3 (Bidang CBP-KPP / Jaringan / Olahraga)
        // Memantau program kerja operasional lainnya
        $waka3 = Role::firstOrCreate(['name' => 'wakil_ketua_3_pac']);
        $waka3->syncPermissions([
            'view_progja',
            'view_kegiatan',
            'view_galeri'
        ]);


        // --- SEKRETARIS PAC ---
        // Fokus: Persuratan, Inventaris, Notulensi, Dokumen, dan Data Organisasi
        $sekretaris = Role::firstOrCreate(['name' => 'sekretaris_pac']);
        $sekretaris->syncPermissions([
            'view_anggota',
            'create_anggota',
            'edit_anggota',
            'view_surat',
            'create_surat',
            'edit_surat',
            'manage_surat',
            'cetak_surat',
            'manage_pengajuan',
            'view_inventaris',
            'manage_inventaris',
            'view_notulensi',
            'manage_notulensi',
            'view_dokumen',
            'manage_dokumen',
            'view_organization',
            'edit_organization'
        ]);


        // --- BENDAHARA PAC ---
        // Fokus: Eksklusif Manajemen Keuangan Organisasi
        $bendahara = Role::firstOrCreate(['name' => 'bendahara_pac']);
        $bendahara->syncPermissions([
            'view_keuangan',
            'create_keuangan',
            'view_progja',
            'view_kegiatan' // Butuh melihat kegiatan untuk sinkronisasi LPJ
        ]);


        // --- WAKIL SEKRETARIS BERDASARKAN PEMBIDANGAN ---

        // Wakil Sekretaris 1 (Bidang Organisasi)
        // Tupoksi: Administrasi struktural, pendataan ranting/komisariat, dan persuratan organisasi
        $wasek1 = Role::firstOrCreate(['name' => 'wakil_sekretaris_1_pac']);
        $wasek1->syncPermissions([
            'view_organization',
            'edit_organization',
            'create_organization',
            'view_anggota',
            'view_surat',
            'create_surat',
            'cetak_surat',
            'manage_surat'
        ]);

        // Wakil Sekretaris 2 (Bidang Kaderisasi)
        // Tupoksi: Mengawal Makesta, database tingkatan kader, dan kegiatan pengkaderan
        $wasek2 = Role::firstOrCreate(['name' => 'wakil_sekretaris_2_pac']);
        $wasek2->syncPermissions([
            'view_anggota',
            'create_anggota',
            'edit_anggota',
            'view_makesta',
            'manage_makesta',
            'view_kegiatan',
            'create_kegiatan' // Akses untuk membuat LPJ khusus kegiatan Makesta/Lakmud
        ]);

        // Wakil Sekretaris 3 (Misal: Bidang Jaringan Sekolah / CBP-KPP)
        $wasek3 = Role::firstOrCreate(['name' => 'wakil_sekretaris_3_pac']);
        $wasek3->syncPermissions([
            'view_surat',
            'view_kegiatan',
            'view_progja'
        ]);


        // --- ADMIN OPERASIONAL PAC ---
        // Fokus: Upload Kegiatan, Program Kerja, Galeri, dan Mengelola Web Profil
        $adminPac = Role::firstOrCreate(['name' => 'admin_pac']);
        $adminPac->syncPermissions([
            'view_progja',
            'create_progja',
            'edit_progja',
            'manage_progja',
            'view_kegiatan',
            'create_kegiatan',
            'edit_kegiatan',
            'manage_kegiatan',
            'view_absensi',
            'create_absensi',
            'view_galeri',
            'manage_galeri',
            'view_web',
            'manage_web'
        ]);


        // --- WAKIL KETUA / WAKIL SEKRETARIS / WAKIL BENDAHARA ---
        // Biasanya diberi akses turunan (view) atau sesuai pendelegasian
        Role::firstOrCreate(['name' => 'wakil_ketua_pac'])->syncPermissions(['view_progja', 'view_kegiatan', 'view_surat']);
        Role::firstOrCreate(['name' => 'wakil_sekretaris_pac'])->syncPermissions(['view_surat', 'create_surat', 'cetak_surat', 'view_notulensi']);
        Role::firstOrCreate(['name' => 'wakil_bendahara_pac'])->syncPermissions(['view_keuangan']);


        // --- ANGGOTA BIASA ---
        // Hanya bisa melihat program kerja, kegiatan, dan galeri yang sudah dipublish
        Role::firstOrCreate(['name' => 'anggota_biasa'])->syncPermissions([
            'view_progja',
            'view_kegiatan',
            'view_galeri'
        ]);

        // ==========================================
        // 3. TINGKAT RANTING / KOMISARIAT
        // ==========================================

        // Ketua Ranting: Monitoring & Persetujuan di tingkat desa/sekolah
        Role::firstOrCreate(['name' => 'ketua_ranting'])->syncPermissions([
            'view_anggota',
            'view_progja',
            'view_kegiatan',
            'view_absensi',
            'view_surat',
            'sign_surat',
            'view_keuangan',
            'view_dokumen',
            'manage_surat',
        ]);

        // --- WAKIL KETUA RANTING ---

        // Wakil Ketua 1 Ranting (Bidang Organisasi)
        Role::firstOrCreate(['name' => 'wakil_ketua_1_ranting'])->syncPermissions([
            'view_organization',
            'view_anggota',
            'view_progja',
            'view_kegiatan',
            'view_surat'
        ]);

        // Wakil Ketua 2 Ranting (Bidang Kaderisasi)
        Role::firstOrCreate(['name' => 'wakil_ketua_2_ranting'])->syncPermissions([
            'view_makesta',
            'view_anggota',
            'view_progja',
            'view_kegiatan'
        ]);

        // Wakil Ketua 3 Ranting (Bidang CBP-KPP / Jaringan / Olahraga)
        Role::firstOrCreate(['name' => 'wakil_ketua_3_ranting'])->syncPermissions([
            'view_progja',
            'view_kegiatan',
            'view_galeri'
        ]);


        // --- SEKRETARIS & WAKIL SEKRETARIS RANTING ---

        // Sekretaris Ranting: Administrasi penuh tingkat bawah
        Role::firstOrCreate(['name' => 'sekretaris_ranting'])->syncPermissions([
            'view_anggota',
            'create_anggota',
            'edit_anggota',
            'view_surat',
            'create_surat',
            'edit_surat',
            'manage_surat',
            'cetak_surat',
            'view_inventaris',
            'manage_inventaris',
            'view_notulensi',
            'manage_notulensi',
            'view_dokumen',
            'manage_dokumen'
        ]);

        // Wakil Sekretaris 1 Ranting (Organisasi)
        Role::firstOrCreate(['name' => 'wakil_sekretaris_1_ranting'])->syncPermissions([
            'view_organization',
            'view_anggota',
            'view_surat',
            'create_surat',
            'cetak_surat',
            'manage_surat'
        ]);

        // Wakil Sekretaris 2 Ranting (Kaderisasi)
        Role::firstOrCreate(['name' => 'wakil_sekretaris_2_ranting'])->syncPermissions([
            'view_anggota',
            'create_anggota',
            'edit_anggota',
            'view_makesta',
            'manage_makesta',
            'view_kegiatan',
            'create_kegiatan'
        ]);

        // Wakil Sekretaris 3 Ranting
        Role::firstOrCreate(['name' => 'wakil_sekretaris_3_ranting'])->syncPermissions([
            'view_surat',
            'view_kegiatan',
            'view_progja'
        ]);


        // --- BENDAHARA & WAKIL BENDAHARA RANTING ---

        // Bendahara Ranting: Keuangan Ranting
        Role::firstOrCreate(['name' => 'bendahara_ranting'])->syncPermissions([
            'view_keuangan',
            'create_keuangan',
            'view_progja',
            'view_kegiatan'
        ]);

        // Wakil Bendahara Ranting
        Role::firstOrCreate(['name' => 'wakil_bendahara_ranting'])->syncPermissions([
            'view_keuangan'
        ]);

        Role::firstOrCreate(['name' => 'anggota_biasa'])->syncPermissions([
            'view_anggota',  // Bisa melihat struktur kepengurusan / daftar anggota
            'view_progja',   // Bisa memantau program kerja organisasi
            'view_kegiatan', // Bisa melihat jadwal dan deskripsi kegiatan (untuk ikut serta)
            'view_dokumen',  // Bisa mengunduh dokumen publik (Modul Makesta, PD/PRT, Peraturan Organisasi)
            'view_galeri',   // Bisa melihat album foto dokumentasi acara
            'view_web'       // Bisa membaca artikel di portal web
        ]);
    }
}
