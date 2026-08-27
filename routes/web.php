<?php

use App\Http\Controllers\Admin\AgendaController as AdminAgendaController;
use App\Http\Controllers\Admin\AnnouncementController as AdminAnnouncementController;
use App\Http\Controllers\Admin\ComplaintController as AdminComplaintController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DataLineageController as AdminDataLineageController;
use App\Http\Controllers\Admin\GalleryController as AdminGalleryController;
use App\Http\Controllers\Admin\NewsController as AdminNewsController;
use App\Http\Controllers\Admin\RegulationController as AdminRegulationController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\ServiceRequestController as AdminServiceRequestController;
use App\Http\Controllers\Admin\SliderController as AdminSliderController;
use App\Http\Controllers\Admin\VillageApbdesDocumentController as AdminVillageApbdesDocumentController;
use App\Http\Controllers\Admin\VillageApbdesItemController as AdminVillageApbdesItemController;
use App\Http\Controllers\Admin\VillageAssetController as AdminVillageAssetController;
use App\Http\Controllers\Admin\VillageHamletController as AdminVillageHamletController;
use App\Http\Controllers\Admin\VillageHeadMessageController as AdminVillageHeadMessageController;
use App\Http\Controllers\Admin\VillageHouseholdWelfareController as AdminVillageHouseholdWelfareController;
use App\Http\Controllers\Admin\VillageInfographicItemController as AdminVillageInfographicItemController;
use App\Http\Controllers\Admin\VillageLandUseAreaController as AdminVillageLandUseAreaController;
use App\Http\Controllers\Admin\VillageMapController;
use App\Http\Controllers\Admin\VillageOfficialController as AdminVillageOfficialController;
use App\Http\Controllers\Admin\VillagePopulationController as AdminVillagePopulationController;
use App\Http\Controllers\Admin\VillagePopulationStatController as AdminVillagePopulationStatController;
use App\Http\Controllers\Admin\VillageProfilePageController as AdminVillageProfilePageController;
use App\Http\Controllers\Admin\VillageSettingController as AdminVillageSettingController;
use App\Http\Controllers\Admin\VillageTransparencyDocumentController as AdminVillageTransparencyDocumentController;
use App\Http\Controllers\Admin\VillageTransparencyItemController as AdminVillageTransparencyItemController;
use App\Http\Controllers\DesilAnalysisController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SuperAdmin\ModuleController as SuperAdminModuleController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::middleware('identifyVillage')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::middleware('module:profile')->group(function () {
        Route::get('/profil', [HomeController::class, 'profil'])->name('profil');
        Route::get('/profil/gambaran-umum-desa', [HomeController::class, 'profilGambaran'])->name('profil.gambaran');
        Route::get('/profil/sejarah-desa', [HomeController::class, 'profilSejarah'])->name('profil.sejarah');
        Route::get('/profil/visi-misi', [HomeController::class, 'profilVisiMisi'])->name('profil.visimisi');
        Route::get('/profil/susunan-organisasi', [HomeController::class, 'profilOrganisasi'])->name('profil.organisasi');
    });
    Route::middleware('module:news')->group(function () {
        Route::get('/berita', [HomeController::class, 'news'])->name('berita');
        Route::get('/berita/{slug}', [HomeController::class, 'newsShow'])->name('berita.show');
        Route::redirect('/news', '/berita', 301)->name('news');
        Route::get('/news/{slug}', function (string $slug) {
            return redirect()->route('berita.show', $slug, 301);
        })->name('news.show');
    });
    Route::middleware('module:agendas')->group(function () {
        Route::get('/agenda', [HomeController::class, 'agenda'])->name('agenda');
        Route::get('/agenda/{agenda}', [HomeController::class, 'agendaShow'])->name('agenda.show');
    });
    Route::middleware('module:services')->group(function () {
        Route::get('/layanan', [HomeController::class, 'services'])->name('services');
        Route::get('/layanan/cek-status', [HomeController::class, 'serviceStatus'])->name('services.status');
        Route::get('/layanan/{slug}', [HomeController::class, 'serviceShow'])->name('services.show');
        Route::post('/layanan/{slug}/ajukan', [HomeController::class, 'serviceApply'])->name('services.apply');
        Route::get('/layanan/pengajuan/{token}/cetak', [HomeController::class, 'serviceReceipt'])->name('services.receipt');
    });
    Route::redirect('/services', '/layanan', 301);
    Route::middleware('module:complaints')->group(function () {
        Route::get('/pengaduan', [HomeController::class, 'complaints'])->name('complaints.index');
        Route::post('/pengaduan', [HomeController::class, 'complaintStore'])->name('complaints.store');
        Route::get('/pengaduan/cek-status', [HomeController::class, 'complaintStatus'])->name('complaints.status');
    });
    Route::middleware('module:desil')->group(function () {
        Route::get('/analisis-desil', [DesilAnalysisController::class, 'index'])->name('desil.index');
        Route::get('/analisis-desil/pdf', [DesilAnalysisController::class, 'pdf'])->name('desil.pdf');
        Route::get('/analisis-desil/excel', [DesilAnalysisController::class, 'excel'])->name('desil.excel');
    });

    Route::get('/statistik', [HomeController::class, 'statistik'])->name('statistik');
    Route::get('/statistik/pdf', [HomeController::class, 'statistikPdf'])->name('statistik.pdf');
    Route::get('/statistik/excel', [HomeController::class, 'statistikExcel'])->name('statistik.excel');
    Route::get('/transparansi', [HomeController::class, 'transparansi'])->middleware('module:transparency')->name('transparansi');
    Route::get('/infografis', [HomeController::class, 'infografis'])->middleware('module:infographics')->name('infografis');
    Route::get('/galeri', [HomeController::class, 'galeri'])->middleware('module:galleries')->name('galeri');
    Route::get('/pengumuman', [HomeController::class, 'pengumuman'])->middleware('module:announcements')->name('pengumuman');
    Route::middleware('module:regulations')->group(function () {
        Route::get('/peraturan', [HomeController::class, 'peraturan'])->name('regulations.index');
        Route::get('/peraturan/{announcement}/download', [HomeController::class, 'regulationDownload'])->name('regulations.download');
    });
    Route::get('/kontak', [HomeController::class, 'kontak'])->name('kontak');
});

Route::get('/dashboard', function () {
    if (Auth::user()?->isAparat() || Auth::user()?->isSuperAdmin()) {
        return redirect()->route('admin.dashboard');
    }

    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified', 'role:aparat,super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', AdminDashboardController::class)->name('dashboard');
    Route::resource('berita', AdminNewsController::class)->middleware('module:news')->except(['show'])->names('news');
    Route::resource('agendas', AdminAgendaController::class)->middleware('module:agendas')->except(['show']);
    Route::post('agendas/resolve-map-link', [AdminAgendaController::class, 'resolveMapLink'])->middleware('module:agendas')->name('agendas.resolve-map-link');
    Route::resource('announcements', AdminAnnouncementController::class)->middleware('module:announcements')->except(['show']);
    Route::resource('regulations', AdminRegulationController::class)->middleware('module:regulations')->except(['show']);
    Route::post('announcements/resolve-map-link', [AdminAnnouncementController::class, 'resolveMapLink'])->middleware('module:announcements')->name('announcements.resolve-map-link');
    Route::middleware('module:services')->group(function () {
        Route::resource('services', AdminServiceController::class)->except(['show']);
        Route::resource('service-requests', AdminServiceRequestController::class)->only(['index', 'show', 'update', 'destroy']);
    });
    Route::get('complaints/{complaint}', [AdminComplaintController::class, 'showRedirect'])
        ->middleware('module:complaints')
        ->name('complaints.show');
    Route::resource('complaints', AdminComplaintController::class)->middleware('module:complaints')->only(['index', 'update']);
    Route::get('service-requests-export/excel', [AdminServiceRequestController::class, 'exportExcel'])->name('service-requests.export.excel');
    Route::get('service-requests-export/pdf', [AdminServiceRequestController::class, 'exportPdf'])->name('service-requests.export.pdf');
    Route::resource('galleries', AdminGalleryController::class)->middleware('module:galleries')->except(['show']);
    Route::resource('village-assets', AdminVillageAssetController::class)->middleware('module:infographics')->except(['show']);
    Route::post('village-assets/resolve-map-link', [AdminVillageAssetController::class, 'resolveMapLink'])->name('village-assets.resolve-map-link');
    Route::resource('village-populations', AdminVillagePopulationController::class)->middleware('module:infographics')->except(['show']);
    Route::resource('village-population-stats', AdminVillagePopulationStatController::class)->middleware('module:infographics')->except(['show']);
    Route::resource('village-land-use-areas', AdminVillageLandUseAreaController::class)->middleware('module:profile')->except(['show']);

    Route::resource('village-hamlets', AdminVillageHamletController::class)->except(['show']);

    Route::resource('village-household-welfares', AdminVillageHouseholdWelfareController::class)->middleware('module:desil')->except(['show']);
    Route::resource('village-transparency-items', AdminVillageTransparencyItemController::class)->middleware('module:transparency')->except(['show']);
    Route::resource('village-transparency-documents', AdminVillageTransparencyDocumentController::class)->middleware('module:transparency')->except(['show']);
    Route::resource('village-apbdes-items', AdminVillageApbdesItemController::class)->middleware('module:transparency')->except(['show']);
    Route::resource('village-apbdes-documents', AdminVillageApbdesDocumentController::class)->middleware('module:transparency')->except(['show']);
    Route::resource('village-infographic-items', AdminVillageInfographicItemController::class)->middleware('module:infographics')->except(['show']);
    Route::resource('profile-pages', AdminVillageProfilePageController::class)->middleware('module:profile')
        ->except(['show'])
        ->parameters(['profile-pages' => 'profilePage']);
    Route::resource('sliders', AdminSliderController::class)->except(['show']);
    Route::resource('head-messages', AdminVillageHeadMessageController::class)->except(['show']);
    Route::resource('officials', AdminVillageOfficialController::class)->except(['show']);
    Route::get('village-settings', [AdminVillageSettingController::class, 'edit'])->name('village-settings.edit');
    Route::put('village-settings', [AdminVillageSettingController::class, 'update'])->name('village-settings.update');
    Route::post('village-settings/sync-instagram', [AdminVillageSettingController::class, 'syncInstagram'])->name('village-settings.sync-instagram');
    Route::get('data-lineage', [AdminDataLineageController::class, 'index'])->name('data-lineage.index');
    Route::get('village-map', [VillageMapController::class, 'edit'])->name('village-map.edit');
    Route::put('village-map', [VillageMapController::class, 'update'])->name('village-map.update');
    Route::post('village-map/import-big', [VillageMapController::class, 'importBig'])->name('village-map.import-big');
});

Route::middleware(['auth', 'verified', 'role:super_admin'])->prefix('super-admin')->name('super-admin.')->group(function () {
    Route::get('/modules', [SuperAdminModuleController::class, 'index'])->name('modules.index');
    Route::put('/modules/{module}', [SuperAdminModuleController::class, 'update'])->name('modules.update');
});

require __DIR__.'/auth.php';
