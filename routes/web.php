<?php

use App\Http\Controllers\Admin\AgendaController as AdminAgendaController;
use App\Http\Controllers\Admin\AnnouncementController as AdminAnnouncementController;
use App\Http\Controllers\Admin\GalleryController as AdminGalleryController;
use App\Http\Controllers\Admin\NewsController as AdminNewsController;
use App\Http\Controllers\Admin\ServiceRequestController as AdminServiceRequestController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\SliderController as AdminSliderController;
use App\Http\Controllers\Admin\VillageAssetController as AdminVillageAssetController;
use App\Http\Controllers\Admin\VillageApbdesItemController as AdminVillageApbdesItemController;
use App\Http\Controllers\Admin\VillageHeadMessageController as AdminVillageHeadMessageController;
use App\Http\Controllers\Admin\VillageInfographicItemController as AdminVillageInfographicItemController;
use App\Http\Controllers\Admin\VillagePopulationController as AdminVillagePopulationController;
use App\Http\Controllers\Admin\VillageOfficialController as AdminVillageOfficialController;
use App\Http\Controllers\Admin\VillageMapController;
use App\Http\Controllers\Admin\VillageProfilePageController as AdminVillageProfilePageController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware('identifyVillage')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/profil', [HomeController::class, 'profil'])->name('profil');
    Route::get('/profil/gambaran-umum-desa', [HomeController::class, 'profilGambaran'])->name('profil.gambaran');
    Route::get('/profil/sejarah-desa', [HomeController::class, 'profilSejarah'])->name('profil.sejarah');
    Route::get('/profil/visi-misi', [HomeController::class, 'profilVisiMisi'])->name('profil.visimisi');
    Route::get('/profil/susunan-organisasi', [HomeController::class, 'profilOrganisasi'])->name('profil.organisasi');
    Route::get('/berita', [HomeController::class, 'news'])->name('berita');
    Route::get('/berita/{slug}', [HomeController::class, 'newsShow'])->name('berita.show');
    Route::redirect('/news', '/berita', 301)->name('news');
    Route::get('/news/{slug}', function (string $slug) {
        return redirect()->route('berita.show', $slug, 301);
    })->name('news.show');
    Route::get('/agenda', [HomeController::class, 'agenda'])->name('agenda');
    Route::get('/agenda/{agenda}', [HomeController::class, 'agendaShow'])->name('agenda.show');
    Route::get('/layanan', [HomeController::class, 'services'])->name('services');
    Route::get('/layanan/cek-status', [HomeController::class, 'serviceStatus'])->name('services.status');
    Route::get('/layanan/{slug}', [HomeController::class, 'serviceShow'])->name('services.show');
    Route::post('/layanan/{slug}/ajukan', [HomeController::class, 'serviceApply'])->name('services.apply');
    Route::get('/layanan/pengajuan/{token}/cetak', [HomeController::class, 'serviceReceipt'])->name('services.receipt');
    Route::redirect('/services', '/layanan', 301);
    Route::get('/transparansi', [HomeController::class, 'transparansi'])->name('transparansi');
    Route::get('/infografis', [HomeController::class, 'infografis'])->name('infografis');
    Route::get('/galeri', [HomeController::class, 'galeri'])->name('galeri');
    Route::get('/pengumuman', [HomeController::class, 'pengumuman'])->name('pengumuman');
    Route::get('/kontak', [HomeController::class, 'kontak'])->name('kontak');
});

Route::get('/dashboard', function () {
    if (auth()->user()?->isAparat()) {
        return redirect()->route('admin.dashboard');
    }

    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified', 'role:aparat'])->prefix('admin')->name('admin.')->group(function () {
    Route::view('/', 'admin.dashboard')->name('dashboard');
    Route::resource('berita', AdminNewsController::class)->except(['show'])->names('news');
    Route::resource('agendas', AdminAgendaController::class)->except(['show']);
    Route::post('agendas/resolve-map-link', [AdminAgendaController::class, 'resolveMapLink'])->name('agendas.resolve-map-link');
    Route::resource('announcements', AdminAnnouncementController::class)->except(['show']);
    Route::resource('services', AdminServiceController::class)->except(['show']);
    Route::resource('service-requests', AdminServiceRequestController::class)->only(['index', 'show', 'update', 'destroy']);
    Route::get('service-requests-export/excel', [AdminServiceRequestController::class, 'exportExcel'])->name('service-requests.export.excel');
    Route::get('service-requests-export/pdf', [AdminServiceRequestController::class, 'exportPdf'])->name('service-requests.export.pdf');
    Route::resource('galleries', AdminGalleryController::class)->except(['show']);
    Route::resource('village-assets', AdminVillageAssetController::class)->except(['show']);
    Route::post('village-assets/resolve-map-link', [AdminVillageAssetController::class, 'resolveMapLink'])->name('village-assets.resolve-map-link');
    Route::resource('village-populations', AdminVillagePopulationController::class)->except(['show']);
    Route::resource('village-apbdes-items', AdminVillageApbdesItemController::class)->except(['show']);
    Route::resource('village-infographic-items', AdminVillageInfographicItemController::class)->except(['show']);
    Route::resource('profile-pages', AdminVillageProfilePageController::class)
        ->except(['show'])
        ->parameters(['profile-pages' => 'profilePage']);
    Route::resource('sliders', AdminSliderController::class)->except(['show']);
    Route::resource('head-messages', AdminVillageHeadMessageController::class)->except(['show']);
    Route::resource('officials', AdminVillageOfficialController::class)->except(['show']);
    Route::get('village-map', [VillageMapController::class, 'edit'])->name('village-map.edit');
    Route::put('village-map', [VillageMapController::class, 'update'])->name('village-map.update');
    Route::post('village-map/import-big', [VillageMapController::class, 'importBig'])->name('village-map.import-big');
});

require __DIR__.'/auth.php';
