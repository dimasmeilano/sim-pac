<?php

use App\Http\Controllers\Admin\CetakSuratController;
use App\Http\Controllers\Admin\KegiatanController;
use App\Http\Controllers\Admin\KeuanganController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\OrganizationController;
use App\Http\Controllers\Admin\OrganizationSettingController;
use App\Http\Controllers\Admin\PengajuanController;
use App\Http\Controllers\Admin\PengaturanOrganisasiController;
use App\Http\Controllers\Admin\ProgramKerjaController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SignatureController;
use App\Http\Controllers\Admin\SuratController;
use App\Http\Controllers\Admin\SuratTemplateController;
use App\Http\Controllers\Admin\TtdDigitalController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InstallerController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// ============ INSTALLER ROUTES (TANPA AUTH) ============
Route::prefix('install')->group(function () {
    Route::get('/', [InstallerController::class, 'welcome'])->name('installer.welcome');
    Route::get('/requirements', [InstallerController::class, 'requirements'])->name('installer.requirements');
    Route::get('/environment', [InstallerController::class, 'environment'])->name('installer.environment');
    Route::post('/process', [InstallerController::class, 'process'])->name('installer.process');
    Route::get('/final', [InstallerController::class, 'final'])->name('installer.final');
});
Route::get('/verifikasi-surat', [SuratController::class, 'verifikasi'])->name('verifikasi.surat');
Route::get('/lacak', [\App\Http\Controllers\Admin\SuratController::class, 'formLacak'])->name('lacak.surat');

// ============ AUTH ROUTES (DEFAULT LARAVEL) ============
Auth::routes();

// ============ ROUTES PUBLIC (TANPA LOGIN) ============
Route::prefix('absensi')->group(function () {
    Route::get('/{kegiatan}/form', [KegiatanController::class, 'absensiForm'])->name('absensi.form');
    Route::post('/{kegiatan}/public', [KegiatanController::class, 'absensiPublicStore'])->name('absensi.public.store');
});

// Root redirect
Route::get('/', function () {
    if (file_exists(storage_path('installed'))) {
        return redirect('/login');
    }
    return redirect('/install');
});

Route::get('/pengajuan-rekomendasi', [PengajuanController::class, 'create'])->name('pengajuan.create');
Route::post('/pengajuan-rekomendasi', [PengajuanController::class, 'store'])->name('pengajuan.store');

// ============ ROUTES YANG MEMERLUKAN AUTH ============
Route::middleware(['auth'])->group(function () {

    // Dashboard & Profile
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/perpanjangan-sk', [PengajuanController::class, 'perpanjanganCreate'])->name('perpanjangan.create');
    Route::post('/perpanjangan-sk', [PengajuanController::class, 'perpanjanganStore'])->name('perpanjangan.store');

    Route::get('/profile', function () {
        return view('profile');
    })->name('profile');
    Route::put('/profile/update', [OrganizationController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [OrganizationController::class, 'updatePassword'])->name('profile.password');
    Route::post('/signature/save', [SignatureController::class, 'save'])->name('signature.save');
    Route::post('/signature/stempel', [SignatureController::class, 'uploadStempel'])->name('signature.stempel');

    Route::middleware(['permission:manage_pengajuan'])->group(function () {
        // Routes Pengajuan Rekomendasi Pengesahan Organisasi
        Route::prefix('pengajuan')->group(function () {
            Route::get('/', [PengajuanController::class, 'index'])->name('pengajuan.index');
            Route::get('/{id}', [PengajuanController::class, 'show'])->name('pengajuan.show');
            Route::post('/{id}/approve', [PengajuanController::class, 'approve'])->name('pengajuan.approve');
            Route::post('/{id}/reject', [PengajuanController::class, 'reject'])->name('pengajuan.reject');
        });
    });
    Route::middleware(['permission:manage_progja'])->group(function () {
        // Program Kerja (Kanban)
        Route::resource('progja', ProgramKerjaController::class);

        // Routes Tugas
        Route::post('/progja/{progja}/tugas', [ProgramKerjaController::class, 'tugasStore'])->name('progja.tugas.store');
        Route::put('/progja/tugas/{tugas}/status', [ProgramKerjaController::class, 'tugasUpdateStatus'])->name('progja.tugas.update-status');
        Route::delete('/progja/tugas/{tugas}', [ProgramKerjaController::class, 'tugasDestroy'])->name('progja.tugas.destroy');

        // Routes Chat
        Route::post('/progja/{progja}/chat', [ProgramKerjaController::class, 'sendMessage'])->name('progja.send-message');
        Route::get('/progja/{progja}/messages', [ProgramKerjaController::class, 'getMessages'])->name('progja.messages');

        // Routes Absensi (Internal - butuh login)
        Route::get('/absensi/scan', [KegiatanController::class, 'scanForm'])->name('absensi.scan.form');
        Route::post('/absensi/scan', [KegiatanController::class, 'scanProcess'])->name('absensi.scan.process');
        Route::post('/absensi/{kegiatan}', [KegiatanController::class, 'absensiStore'])->name('absensi.store');
    });

    Route::middleware(['permission:manage_kegiatan'])->group(function () {
        // Kegiatan & Absensi
        Route::resource('kegiatan', KegiatanController::class);
        Route::get('/kegiatan/{kegiatan}/download-qr', [KegiatanController::class, 'downloadQrCode'])->name('kegiatan.download-qr');
        Route::get('/kegiatan/{kegiatan}/laporan', [KegiatanController::class, 'laporan'])->name('kegiatan.laporan');
        Route::post('/kegiatan/{kegiatan}/regenerate-qr', [KegiatanController::class, 'regenerateQrCode'])->name('kegiatan.regenerate-qr');
    });

    // Keuangan
    Route::middleware(['role_or_permission:super_admin|bendahara_pac|bendahara_ranting'])->group(function () {
        Route::resource('keuangan', KeuanganController::class);
        Route::get('/keuangan-laporan', [KeuanganController::class, 'laporan'])->name('keuangan.laporan');
        Route::get('/keuangan-laporan/pdf', [KeuanganController::class, 'exportPdf'])->name('keuangan.laporan.pdf');
        Route::post('/keuangan/{keuangan}/validate', [KeuanganController::class, 'validateTransaction'])->name('keuangan.validate');
    });

    // Settings (Role & Menu Management)
    Route::middleware(['role:super_admin'])->group(function () {
        Route::prefix('settings')->group(function () {
            Route::resource('roles', RoleController::class);
            Route::resource('menus', MenuController::class);
        });
        Route::resource('organizations', OrganizationController::class);
        Route::get('/organizations/{organization}/setting', [OrganizationSettingController::class, 'edit'])->name('organizations.setting');
        Route::put('/organizations/{organization}/setting', [OrganizationSettingController::class, 'update'])->name('organizations.setting.update');
    });
    // ============ SURAT & ARSIP ==========

    Route::middleware(['role_or_permission:super_admin|sekretaris_pac|sekretaris_ranting|ketua_pac|ketua_ranting|wakil_sekretaris_pac|wakil_sekretaris_ranting'])->group(function () {
        Route::prefix('cetak-surat')->group(function () {
            Route::get('/', [CetakSuratController::class, 'index'])->name('cetak-surat.index');
            Route::get('/{id}/create', [CetakSuratController::class, 'create'])->name('cetak-surat.create');
            Route::post('/{id}/store', [CetakSuratController::class, 'store'])->name('cetak-surat.store');

            // PERBAIKAN 1: Ubah dari Route::get menjadi Route::post agar sinkron dengan Fetch API di Blade
            Route::post('/preview', [CetakSuratController::class, 'previewSurat'])->name('cetak-surat.preview');

            Route::get('/download/{surat}', [CetakSuratController::class, 'download'])->name('cetak-surat.download');

            // PERBAIKAN 2: Hapus awalan '/cetak-surat' karena sudah dicakup oleh Prefix Group di atasnya
            Route::post('/generate-nomor', [CetakSuratController::class, 'generateNomor'])->name('cetak-surat.generate-nomor');

            Route::get('/preview', function () {
                return redirect()->route('cetak-surat.index')->with('error', 'Sesi preview berakhir karena halaman diperbarui.');
            });
        });
        Route::post('/members/import', [MemberController::class, 'importExcel'])->name('members.import');
        Route::get('/members/template-import', [MemberController::class, 'downloadTemplate'])->name('members.template');
        Route::get('/members/export', [MemberController::class, 'exportExcel'])->name('members.export');
        Route::get('/members/export/pdf', [MemberController::class, 'exportPdf'])->name('members.export.pdf');
        Route::resource('members', MemberController::class);
        Route::prefix('surat')->name('surat.')->group(function () {

            // Sekarang ini akan otomatis memiliki nama 'surat.template.index'
            Route::get('surat/template/get-placeholder', [SuratTemplateController::class, 'getPlaceholder'])->name('template.get-placeholder');
            Route::resource('template', SuratTemplateController::class)->except(['show']);
            Route::get('keluar/get-nomor-otomatis', [SuratController::class, 'getNomorOtomatis'])->name('keluar.nomor-otomatis');



            // Untuk route manual di bawahnya, hapus teks 'surat.' pada ->name() karena sudah otomatis ditambahkan
            // Contoh: yang tadinya ->name('surat.keluar.index') diganti menjadi ->name('keluar.index')
            Route::get('keluar', [SuratController::class, 'keluarIndex'])->name('keluar.index');
            Route::get('keluar/create', [SuratController::class, 'keluarCreate'])->name('keluar.create');
            Route::post('keluar', [SuratController::class, 'keluarStore'])->name('keluar.store');
            Route::get('keluar/{suratKeluar}', [SuratController::class, 'keluarShow'])->name('keluar.show');
            Route::get('keluar/{suratKeluar}/edit', [SuratController::class, 'keluarEdit'])->name('keluar.edit');
            Route::put('keluar/{suratKeluar}', [SuratController::class, 'keluarUpdate'])->name('keluar.update');
            Route::delete('keluar/{suratKeluar}', [SuratController::class, 'keluarDestroy'])->name('keluar.destroy');
            Route::post('keluar/{suratKeluar}/ttd', [SuratController::class, 'keluarTtd'])->name('keluar.ttd');
            Route::get('keluar/{suratKeluar}/download', [SuratController::class, 'keluarDownload'])->name('keluar.download');
            Route::post('keluar/generate-nomor', [SuratController::class, 'generateNomor'])->name('keluar.generate-nomor');
            Route::post('keluar/{surat}/validasi-wakil', [SuratController::class, 'validasiWakil'])->name('keluar.validasi-wakil');
            Route::post('keluar/{surat}/ttd-ketua', [SuratController::class, 'ttdKetua'])->name('keluar.ttd-ketua');
            Route::post('keluar/{surat}/ttd-sekretaris', [SuratController::class, 'ttdSekretaris'])->name('keluar.ttd-sekretaris');
            Route::post('keluar/{surat}/ajukan', [SuratController::class, 'ajukanValidasi'])->name('keluar.ajukan');
            Route::post('keluar/{id}/approve', [SuratController::class, 'approve'])->name('keluar.approve');
            Route::get('/surat/keluar/{suratKeluar}/edit', [SuratController::class, 'keluarEdit'])->name('surat.keluar.edit');

            // Rute Update Dipisah agar logikanya tidak bertabrakan
            Route::put('/surat/keluar/{suratKeluar}/update-umum', [SuratController::class, 'keluarUpdateUmum'])->name('keluar.update.umum');
            Route::put('/surat/keluar/{suratKeluar}/update-khusus', [SuratController::class, 'keluarUpdateKhusus'])->name('keluar.update.khusus');

            // Surat Masuk
            Route::get('masuk', [SuratController::class, 'masukIndex'])->name('masuk.index');
            Route::get('masuk/create', [SuratController::class, 'masukCreate'])->name('masuk.create');
            Route::post('masuk', [SuratController::class, 'masukStore'])->name('masuk.store');
            Route::get('masuk/{suratMasuk}', [SuratController::class, 'masukShow'])->name('masuk.show');
            Route::get('masuk/{suratMasuk}/edit', [SuratController::class, 'masukEdit'])->name('masuk.edit');
            Route::put('masuk/{suratMasuk}', [SuratController::class, 'masukUpdate'])->name('masuk.update');
            Route::delete('masuk/{suratMasuk}', [SuratController::class, 'masukDestroy'])->name('masuk.destroy');
            Route::post('masuk/{suratMasuk}/disposisi', [SuratController::class, 'masukDisposisi'])->name('masuk.disposisi');
        });

        // Tanda Tangan Digital
        Route::resource('ttd-digital', TtdDigitalController::class);

        Route::group(['middleware' => ['role:sekretaris_ranting|sekretaris_pac']], function () {
            Route::get('/organisasi-saya', [PengaturanOrganisasiController::class, 'edit'])->name('organisasi.saya.edit');
            Route::put('/organisasi-saya', [PengaturanOrganisasiController::class, 'update'])->name('organisasi.saya.update');
        });
    });
});
