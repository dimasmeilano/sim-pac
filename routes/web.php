<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Controllers - Admin & Sistem
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InstallerController;
use App\Http\Middleware\AksesPengurus;
use App\Http\Middleware\TrackPengunjung;

// Controllers - Public
use App\Http\Controllers\Public\BerandaController;
use App\Http\Controllers\Public\ArtikelController as PublicArtikelController;
use App\Http\Controllers\Public\InstrukturController;
use App\Http\Controllers\Public\MakestaPublicController;
use App\Http\Controllers\Public\PesertaEvaluasiController;
use App\Http\Controllers\Public\ProfilController;

// Controllers - Admin Modules
use App\Http\Controllers\Admin\ArtikelController;
use App\Http\Controllers\Admin\AuditTrailController;
use App\Http\Controllers\Admin\CetakSuratController;
use App\Http\Controllers\Admin\DokumenArsipController;
use App\Http\Controllers\Admin\GaleriController;
use App\Http\Controllers\Admin\IdentitasWebController;
use App\Http\Controllers\Admin\InventarisController;
use App\Http\Controllers\Admin\KategoriArtikelController;
use App\Http\Controllers\Admin\KegiatanController;
use App\Http\Controllers\Admin\KeuanganController;
use App\Http\Controllers\Admin\LpjController;
use App\Http\Controllers\Admin\MakestaEventController;
use App\Http\Controllers\Admin\MediaSosialController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\NotulensiController;
use App\Http\Controllers\Admin\OrganizationController;
use App\Http\Controllers\Admin\OrganizationSettingController;
use App\Http\Controllers\Admin\PengajuanController;
use App\Http\Controllers\Admin\PengaturanOrganisasiController;
use App\Http\Controllers\Admin\ProgramKerjaController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SignatureController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\SuratController;
use App\Http\Controllers\Admin\SuratTemplateController;
use App\Http\Controllers\Admin\TeksBerjalanController;
use App\Http\Controllers\Admin\TtdDigitalController;
use App\Http\Controllers\Admin\UserRoleController;
use App\Http\Controllers\Admin\WidgetController;


// =========================================================================
// 1. INSTALLER ROUTES (TANPA AUTH)
// =========================================================================
Route::prefix('install')->group(function () {
    Route::get('/', [InstallerController::class, 'welcome'])->name('installer.welcome');
    Route::get('/requirements', [InstallerController::class, 'requirements'])->name('installer.requirements');
    Route::get('/environment', [InstallerController::class, 'environment'])->name('installer.environment');
    Route::post('/process', [InstallerController::class, 'process'])->name('installer.process');
    Route::get('/final', [InstallerController::class, 'final'])->name('installer.final');
});


// =========================================================================
// 2. PUBLIC ROUTES (TANPA LOGIN)
// =========================================================================

// --- Tracking & Beranda ---
Route::middleware([TrackPengunjung::class])->group(function () {
    Route::get('/', function () {
        if (!file_exists(storage_path('installed'))) {
            return redirect('/install');
        }
        return app(BerandaController::class)->index();
    })->name('beranda');

    Route::get('/berita/{slug}', [PublicArtikelController::class, 'show'])->name('artikel.baca');
    Route::post('/berita/{slug}/komentar', [PublicArtikelController::class, 'storeKomentar'])->name('artikel.komentar');
    Route::get('/profil-organisasi', [ProfilController::class, 'index'])->name('profil.index');
});

// --- Surat & Pengajuan Publik ---
Route::get('/verifikasi-surat', [SuratController::class, 'verifikasi'])->name('verifikasi.surat');
Route::get('/lacak', [SuratController::class, 'formLacak'])->name('lacak.surat');
Route::get('/pengajuan-rekomendasi', [PengajuanController::class, 'create'])->name('pengajuan.create');
Route::post('/pengajuan-rekomendasi', [PengajuanController::class, 'store'])->name('pengajuan.store');

// --- Absensi Publik ---
Route::prefix('absensi')->group(function () {
    Route::get('/{kegiatan}/form', [KegiatanController::class, 'absensiForm'])->name('absensi.form');
    Route::post('/{kegiatan}/public', [KegiatanController::class, 'absensiPublicStore'])->name('absensi.public.store');
});

// --- Makesta & Pelatihan Publik ---
Route::prefix('makesta')->group(function () {
    Route::get('/daftar/{id}', [MakestaPublicController::class, 'daftar'])->name('makesta.daftar');
    Route::post('/daftar/{id}', [MakestaPublicController::class, 'storePeserta'])->name('makesta.daftar.store');
});

Route::prefix('instruktur/materi/{token}')->group(function () {
    Route::get('/', [InstrukturController::class, 'loginForm'])->name('instruktur.login');
    Route::post('/', [InstrukturController::class, 'authenticate'])->name('instruktur.authenticate');
    Route::get('/penilaian', [InstrukturController::class, 'penilaian'])->name('instruktur.penilaian');
    Route::post('/penilaian', [InstrukturController::class, 'storePenilaian'])->name('instruktur.penilaian.store');
});

Route::prefix('evaluasi-makesta/{event_id}')->group(function () {
    Route::get('/', [PesertaEvaluasiController::class, 'login'])->name('peserta.evaluasi.login');
    Route::post('/', [PesertaEvaluasiController::class, 'authenticate'])->name('peserta.evaluasi.authenticate');
    Route::get('/form', [PesertaEvaluasiController::class, 'form'])->name('peserta.evaluasi.form');
    Route::post('/form', [PesertaEvaluasiController::class, 'store'])->name('peserta.evaluasi.store');
});

// --- Galeri Publik ---
Route::get('drive/f/{token}', [GaleriController::class, 'publicFolder'])->name('galeri.public_folder');
Route::post('drive/f/{token}/upload', [GaleriController::class, 'publicUpload'])->name('galeri.public_upload');


// =========================================================================
// 3. AUTHENTICATION (DEFAULT LARAVEL)
// =========================================================================
Auth::routes();


// =========================================================================
// 4. ANGGOTA BIASA (PORTAL)
// =========================================================================
Route::middleware(['auth'])->group(function () {
    Route::get('/portal-anggota', function () {
        return view('portal');
    })->name('anggota.portal');
});


// =========================================================================
// 5. ADMIN / ERP (HANYA PENGURUS)
// =========================================================================
Route::middleware(['auth', AksesPengurus::class])->group(function () {

    // --- Dashboard & Profil Diri ---
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', function () {
        return view('profile');
    })->name('profile');
    Route::put('/profile/update', [OrganizationController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [OrganizationController::class, 'updatePassword'])->name('profile.password');
    Route::post('/signature/save', [SignatureController::class, 'save'])->name('signature.save');
    Route::post('/signature/stempel', [SignatureController::class, 'uploadStempel'])->name('signature.stempel');

    // --- Perpanjangan SK ---
    Route::get('/perpanjangan-sk', [PengajuanController::class, 'perpanjanganCreate'])->name('perpanjangan.create');
    Route::post('/perpanjangan-sk', [PengajuanController::class, 'perpanjanganStore'])->name('perpanjangan.store');

    // --- Cetak Ringkasan ---
    Route::get('/proker/{id}/cetak-ringkasan', [ProgramKerjaController::class, 'cetakRingkasanPdf'])->name('progja.cetak-ringkasan');


    // ---------------------------------------------------------
    // A. MODULE: PENGATURAN SUPER ADMIN
    // ---------------------------------------------------------
    Route::middleware(['role:super_admin'])->group(function () {
        Route::prefix('settings')->group(function () {
            Route::resource('user-role', UserRoleController::class)->parameters(['user-role' => 'user_role'])->only(['index', 'edit', 'update']);
            Route::resource('roles', RoleController::class);
            Route::resource('menus', MenuController::class);
        });
        Route::resource('organizations', OrganizationController::class);
        Route::get('/organizations/{organization}/setting', [OrganizationSettingController::class, 'edit'])->name('organizations.setting');
        Route::put('/organizations/{organization}/setting', [OrganizationSettingController::class, 'update'])->name('organizations.setting.update');
        Route::get('/audit-trail', [AuditTrailController::class, 'index'])->name('audit.index');
    });


    // ---------------------------------------------------------
    // B. MODULE: PENGAJUAN REKOMENDASI
    // ---------------------------------------------------------
    Route::middleware(['permission:manage_pengajuan'])->group(function () {
        Route::prefix('pengajuan')->group(function () {
            Route::get('/', [PengajuanController::class, 'index'])->name('pengajuan.index');
            Route::get('/{id}', [PengajuanController::class, 'show'])->name('pengajuan.show');
            Route::post('/{id}/approve', [PengajuanController::class, 'approve'])->name('pengajuan.approve');
            Route::post('/{id}/reject', [PengajuanController::class, 'reject'])->name('pengajuan.reject');
        });
    });


    // ---------------------------------------------------------
    // C. MODULE: PROGRAM KERJA & LPJ
    // ---------------------------------------------------------
    Route::middleware(['permission:manage_progja'])->group(function () {
        Route::resource('progja', ProgramKerjaController::class);

        // Tugas & Chat Kanban
        Route::post('/progja/{progja}/tugas', [ProgramKerjaController::class, 'tugasStore'])->name('progja.tugas.store');
        Route::put('/progja/tugas/{tugas}/status', [ProgramKerjaController::class, 'tugasUpdateStatus'])->name('progja.tugas.update-status');
        Route::delete('/progja/tugas/{tugas}', [ProgramKerjaController::class, 'tugasDestroy'])->name('progja.tugas.destroy');
        Route::post('/progja/{progja}/chat', [ProgramKerjaController::class, 'sendMessage'])->name('progja.send-message');
        Route::get('/progja/{progja}/messages', [ProgramKerjaController::class, 'getMessages'])->name('progja.messages');

        // LPJ
        Route::get('lpj/create', [LpjController::class, 'create'])->name('lpj.create');
        Route::post('lpj/store', [LpjController::class, 'store'])->name('lpj.store');
        Route::get('lpj/{lpj}/edit', [LpjController::class, 'edit'])->name('lpj.edit');
        Route::put('lpj/{lpj}', [LpjController::class, 'update'])->name('lpj.update');
        Route::get('lpj/{lpj}/cetak', [LpjController::class, 'cetakLpjPdf'])->name('lpj.cetak');

        // Absensi Internal Scan
        Route::get('/absensi/scan', [KegiatanController::class, 'scanForm'])->name('absensi.scan.form');
        Route::post('/absensi/scan', [KegiatanController::class, 'scanProcess'])->name('absensi.scan.process');
        Route::post('/absensi/{kegiatan}', [KegiatanController::class, 'absensiStore'])->name('absensi.store');
    });


    // ---------------------------------------------------------
    // D. MODULE: KEGIATAN
    // ---------------------------------------------------------
    Route::middleware(['permission:manage_kegiatan'])->group(function () {
        Route::resource('kegiatan', KegiatanController::class);
        Route::get('/kegiatan/{kegiatan}/download-qr', [KegiatanController::class, 'downloadQrCode'])->name('kegiatan.download-qr');
        Route::get('/kegiatan/{kegiatan}/laporan', [KegiatanController::class, 'laporan'])->name('kegiatan.laporan');
        Route::post('/kegiatan/{kegiatan}/regenerate-qr', [KegiatanController::class, 'regenerateQrCode'])->name('kegiatan.regenerate-qr');
    });


    // ---------------------------------------------------------
    // E. MODULE: KEUANGAN
    // ---------------------------------------------------------
    Route::middleware(['role_or_permission:super_admin|bendahara_pac|bendahara_ranting'])->group(function () {
        Route::resource('keuangan', KeuanganController::class);
        Route::get('/keuangan-laporan', [KeuanganController::class, 'laporan'])->name('keuangan.laporan');
        Route::get('/keuangan-laporan/pdf', [KeuanganController::class, 'exportPdf'])->name('keuangan.laporan.pdf');
        Route::post('/keuangan/{keuangan}/validate', [KeuanganController::class, 'validateTransaction'])->name('keuangan.validate');
    });


    // ---------------------------------------------------------
    // F. MODULE: SEKRETARIAT (SURAT, ARSIP, NOTULENSI, ANGGOTA)
    // ---------------------------------------------------------
    Route::middleware(['role_or_permission:super_admin|sekretaris_pac|sekretaris_ranting|ketua_pac|ketua_ranting|wakil_sekretaris_pac|wakil_sekretaris_ranting'])->group(function () {

        // Members / Anggota
        Route::post('/members/import', [MemberController::class, 'importExcel'])->name('members.import');
        Route::get('/members/template-import', [MemberController::class, 'downloadTemplate'])->name('members.template');
        Route::get('/members/export', [MemberController::class, 'exportExcel'])->name('members.export');
        Route::get('/members/export/pdf', [MemberController::class, 'exportPdf'])->name('members.export.pdf');
        Route::resource('members', MemberController::class);

        // Inventaris & Notulensi
        Route::get('inventaris/cetak-label/{id?}', [InventarisController::class, 'cetakLabel'])->name('inventaris.cetak_label');
        Route::resource('inventaris', InventarisController::class);
        Route::get('notulensi/{notulensi}/pdf', [NotulensiController::class, 'cetakPdf'])->name('notulensi.pdf');
        Route::patch('notulensi/{notulensi}/finalize', [NotulensiController::class, 'finalize'])->name('notulensi.finalize');
        Route::resource('notulensi', NotulensiController::class);

        // Tanda Tangan Digital
        Route::resource('ttd-digital', TtdDigitalController::class);

        // Cetak Surat Khusus
        Route::prefix('cetak-surat')->group(function () {
            Route::get('/', [CetakSuratController::class, 'index'])->name('cetak-surat.index');
            Route::get('/{id}/create', [CetakSuratController::class, 'create'])->name('cetak-surat.create');
            Route::post('/{id}/store', [CetakSuratController::class, 'store'])->name('cetak-surat.store');
            Route::post('/preview', [CetakSuratController::class, 'previewSurat'])->name('cetak-surat.preview');
            Route::get('/download/{surat}', [CetakSuratController::class, 'download'])->name('cetak-surat.download');
            Route::post('/generate-nomor', [CetakSuratController::class, 'generateNomor'])->name('cetak-surat.generate-nomor');
            Route::get('/preview', function () {
                return redirect()->route('cetak-surat.index')->with('error', 'Sesi preview berakhir karena halaman diperbarui.');
            });
        });

        // Manajemen Surat Masuk & Keluar
        Route::prefix('surat')->name('surat.')->group(function () {
            Route::get('surat/template/get-placeholder', [SuratTemplateController::class, 'getPlaceholder'])->name('template.get-placeholder');
            Route::resource('template', SuratTemplateController::class)->except(['show']);

            // Surat Keluar
            Route::get('keluar/get-nomor-otomatis', [SuratController::class, 'getNomorOtomatis'])->name('keluar.nomor-otomatis');
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
            Route::get('/surat/keluar/{suratKeluar}/edit', [SuratController::class, 'keluarEdit'])->name('surat.keluar.edit'); // Alias
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

        // E-Library & Repository
        Route::get('/dokumen', [DokumenArsipController::class, 'index'])->name('dokumen.index');
        Route::post('/dokumen', [DokumenArsipController::class, 'store'])->name('dokumen.store');
        Route::delete('/dokumen/{dokumen}', [DokumenArsipController::class, 'destroy'])->name('dokumen.destroy');
        Route::get('/dokumen/{dokumen}/download', [DokumenArsipController::class, 'download'])->name('dokumen.download');

        // Makesta Event & Evaluasi
        Route::resource('makesta-event', MakestaEventController::class);
        Route::get('/makesta-event/{id}/export-rekap-excel', [MakestaEventController::class, 'exportRekapExcel'])->name('makesta-event.export-rekap-excel');
        Route::get('/makesta-event/{id}/rekap-evaluasi', [MakestaEventController::class, 'rekapEvaluasi'])->name('makesta-event.rekap-evaluasi');
        Route::get('/makesta-event/{id}/export-rekap-pdf', [MakestaEventController::class, 'exportRekapPdf'])->name('makesta-event.export-rekap-pdf');
        Route::get('/makesta-event/{id}/export-peserta', [MakestaEventController::class, 'exportPesertaPdf'])->name('makesta-event.export-peserta');
        Route::get('/makesta-event/{id}/rekap-nilai', [MakestaEventController::class, 'rekapNilai'])->name('makesta-event.rekap');
        Route::get('/makesta-event/{id}/idcard-masal', [MakestaEventController::class, 'cetakIdCardMasal'])->name('makesta-event.idcard-masal');
        Route::get('/makesta-event/{id}/peserta', [MakestaEventController::class, 'peserta'])->name('makesta-event.peserta');
        Route::post('/makesta-event/{id}/materi', [MakestaEventController::class, 'storeMateri'])->name('makesta-materi.store');
        Route::post('/makesta-event/{id}/verifikasi', [MakestaEventController::class, 'verifikasi'])->name('makesta-event.verifikasi');
        Route::get('/makesta-peserta/{id}/idcard', [MakestaEventController::class, 'cetakIdCard'])->name('makesta-peserta.idcard');
        Route::put('/makesta-peserta/{id}/status', [MakestaEventController::class, 'updateStatusPeserta'])->name('makesta-peserta.update-status');
        Route::delete('/makesta-peserta/{id}', [MakestaEventController::class, 'destroyPeserta'])->name('makesta-peserta.destroy');
        Route::delete('/makesta-materi/{id}', [MakestaEventController::class, 'destroyMateri'])->name('makesta-materi.destroy');

        // Pengaturan Organisasi Khusus Sekretaris
        Route::group(['middleware' => ['role:sekretaris_ranting|sekretaris_pac']], function () {
            Route::get('/organisasi-saya', [PengaturanOrganisasiController::class, 'edit'])->name('organisasi.saya.edit');
            Route::put('/organisasi-saya', [PengaturanOrganisasiController::class, 'update'])->name('organisasi.saya.update');
        });
    });


    // ---------------------------------------------------------
    // G. MODULE: WORKSPACE / GALERI (UMUM UNTUK PENGURUS)
    // ---------------------------------------------------------
    Route::get('workspace', [GaleriController::class, 'index'])->name('galeri.index');
    Route::post('workspace/folder', [GaleriController::class, 'storeFolder'])->name('galeri.folder.store');
    Route::put('workspace/folder/{folder}', [GaleriController::class, 'updateFolder'])->name('galeri.folder.update');
    Route::post('workspace/file', [GaleriController::class, 'storeFile'])->name('galeri.file.store');
    Route::delete('workspace/file/{file}', [GaleriController::class, 'destroyFile'])->name('galeri.file.destroy');
    Route::post('/galeri/toggle-publik', [GaleriController::class, 'togglePublik'])->name('galeri.toggle');


    // ---------------------------------------------------------
    // H. MODULE: ADMIN WEB & PUBLIKASI
    // ---------------------------------------------------------
    Route::resource('artikel', ArtikelController::class);
    Route::resource('kategori', KategoriArtikelController::class)->except(['create', 'show', 'edit']);
    Route::resource('media-sosial', MediaSosialController::class)->except(['create', 'show', 'edit']);

    Route::group(['middleware' => ['role:editor|super_admin']], function () {
        Route::get('/identitas-web', [IdentitasWebController::class, 'index'])->name('identitas-web.index');
        Route::put('/identitas-web/update', [IdentitasWebController::class, 'update'])->name('identitas-web.update');
        Route::resource('slider', SliderController::class)->except(['create', 'show', 'edit']);
        Route::resource('teks-berjalan', TeksBerjalanController::class)->except(['create', 'show', 'edit']);
        Route::resource('widgets', WidgetController::class)->except(['create', 'show', 'edit']);
    });
});
