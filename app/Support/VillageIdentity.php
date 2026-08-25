<?php

namespace App\Support;

use App\Models\Village;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class VillageIdentity
{
    public static function village(): ?Village
    {
        if (app()->bound('currentVillage')) {
            return app('currentVillage');
        }

        if (! Schema::hasTable('villages')) {
            return null;
        }

        return Village::query()->first();
    }

    public static function name(?Village $village = null): string
    {
        $name = trim((string) (($village ?? self::village())?->name ?? ''));

        return $name !== '' ? $name : 'Pemerintah Desa';
    }

    public static function governmentName(?Village $village = null): string
    {
        $name = self::name($village);

        return Str::startsWith(Str::lower($name), 'pemerintah desa')
            ? $name
            : 'Pemerintah Desa '.$name;
    }

    public static function title(?string $pageTitle = null, ?Village $village = null): string
    {
        $siteName = self::name($village);
        $pageTitle = trim((string) $pageTitle);

        if ($pageTitle === '' || Str::lower($pageTitle) === Str::lower($siteName)) {
            return $siteName;
        }

        return $pageTitle.' | '.$siteName;
    }

    public static function defaultPageTitle(): ?string
    {
        $routeName = request()->route()?->getName();

        if (! $routeName || $routeName === 'home') {
            return null;
        }

        return match (true) {
            $routeName === 'login' => 'Login',
            $routeName === 'register' => 'Daftar',
            $routeName === 'password.request' => 'Lupa Password',
            $routeName === 'password.reset' => 'Reset Password',
            $routeName === 'verification.notice' => 'Verifikasi Email',
            $routeName === 'password.confirm' => 'Konfirmasi Password',
            $routeName === 'dashboard' => 'Dashboard',
            $routeName === 'profile.edit' => 'Profil Akun',
            Str::startsWith($routeName, 'admin.') => self::adminPageTitle($routeName),
            default => self::publicPageTitle($routeName),
        };
    }

    private static function publicPageTitle(string $routeName): ?string
    {
        return match ($routeName) {
            'profil', 'profil.gambaran' => 'Gambaran Umum Desa',
            'profil.sejarah' => 'Sejarah Desa',
            'profil.visimisi' => 'Visi dan Misi',
            'profil.organisasi' => 'Susunan Organisasi',
            'berita', 'news' => 'Berita',
            'agenda' => 'Agenda',
            'services' => 'Layanan Desa',
            'services.status' => 'Cek Status Layanan',
            'complaints.index' => 'Pengaduan Masyarakat',
            'complaints.status' => 'Cek Status Pengaduan',
            'statistik' => 'Statistik Desa',
            'transparansi' => 'Transparansi',
            'infografis' => 'Infografis',
            'galeri' => 'Galeri',
            'pengumuman' => 'Pengumuman',
            'regulations.index' => 'Peraturan Desa',
            'kontak' => 'Kontak',
            default => null,
        };
    }

    private static function adminPageTitle(string $routeName): string
    {
        return match (true) {
            $routeName === 'admin.dashboard' => 'Dashboard Admin',
            Str::startsWith($routeName, 'admin.news.') => 'Kelola Berita',
            Str::startsWith($routeName, 'admin.agendas.') => 'Kelola Agenda',
            Str::startsWith($routeName, 'admin.announcements.') => 'Kelola Pengumuman',
            Str::startsWith($routeName, 'admin.regulations.') => 'Kelola Peraturan',
            Str::startsWith($routeName, 'admin.services.') => 'Kelola Layanan',
            Str::startsWith($routeName, 'admin.service-requests.') => 'Pengajuan Layanan',
            Str::startsWith($routeName, 'admin.complaints.') => 'Kelola Pengaduan',
            Str::startsWith($routeName, 'admin.galleries.') => 'Kelola Galeri',
            Str::startsWith($routeName, 'admin.village-assets.') => 'Kelola Aset Desa',
            Str::startsWith($routeName, 'admin.village-populations.') => 'Data Penduduk',
            Str::startsWith($routeName, 'admin.village-population-stats.') => 'Statistik Penduduk',
            Str::startsWith($routeName, 'admin.village-land-use-areas.') => 'Luas Wilayah',
            Str::startsWith($routeName, 'admin.village-transparency-items.') => 'Transparansi',
            Str::startsWith($routeName, 'admin.village-transparency-documents.') => 'Dokumen Transparansi',
            Str::startsWith($routeName, 'admin.village-apbdes-items.') => 'APBDes',
            Str::startsWith($routeName, 'admin.village-apbdes-documents.') => 'Dokumen APBDes',
            Str::startsWith($routeName, 'admin.village-infographic-items.') => 'Infografis Desa',
            Str::startsWith($routeName, 'admin.profile-pages.') => 'Halaman Profil Desa',
            Str::startsWith($routeName, 'admin.sliders.') => 'Slider',
            Str::startsWith($routeName, 'admin.head-messages.') => 'Sambutan Kepala Desa',
            Str::startsWith($routeName, 'admin.officials.') => 'Perangkat Desa',
            $routeName === 'admin.village-settings.edit' => 'Pengaturan Desa',
            $routeName === 'admin.data-lineage.index' => 'Jejak Data',
            $routeName === 'admin.village-map.edit' => 'Peta Desa',
            default => 'Admin',
        };
    }
}
