<?php

namespace App\Support;

use App\Models\ModuleSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class ModuleManager
{
    private const CACHE_KEY = 'module_settings_map_v1';

    public static function allModules(): array
    {
        return [
            'services' => 'Layanan Desa',
            'complaints' => 'Pengaduan Masyarakat',
            'news' => 'Berita',
            'agendas' => 'Agenda',
            'announcements' => 'Pengumuman',
            'regulations' => 'Peraturan Desa',
            'galleries' => 'Galeri',
            'transparency' => 'Transparansi',
            'infographics' => 'Infografis',
            'profile' => 'Profil Desa',
            'desil' => 'Analisis Desil',
        ];
    }

    public static function isEnabled(string $moduleKey): bool
    {
        return (bool) (self::enabledMap()[$moduleKey] ?? true);
    }

    public static function enabledMap(): array
    {
        return Cache::remember(self::CACHE_KEY, now()->addMinutes(10), function () {
            if (! Schema::hasTable('module_settings')) {
                return [];
            }

            return ModuleSetting::query()
                ->pluck('is_enabled', 'module_key')
                ->map(fn ($value) => (bool) $value)
                ->toArray();
        });
    }

    public static function setEnabled(string $moduleKey, bool $enabled): void
    {
        if (! array_key_exists($moduleKey, self::allModules()) || ! Schema::hasTable('module_settings')) {
            return;
        }

        ModuleSetting::query()->updateOrCreate(
            ['module_key' => $moduleKey],
            ['is_enabled' => $enabled]
        );

        self::clearCache();
    }

    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
