<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Village extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'logo',
        'description',
        'head_name',
        'address',
        'phone',
        'email',
        'website',
        'postal_code',
        'district',
        'city',
        'province',
        'country',
        'area_km2',
        'population',
        'population_male',
        'population_female',
        'households',
        'rt_count',
        'rw_count',
        'history',
        'vision',
        'mission',
        'head_greeting',
        'instagram_enabled',
        'instagram_username',
        'instagram_user_id',
        'instagram_access_token',
        'instagram_connected_at',
        'instagram_last_sync_at',
        'instagram_last_error',
        'quick_info',
        'apb_income',
        'apb_expense',
        'apb_financing',
        'latitude',
        'longitude',
        'boundary_geojson',
    ];

    protected $casts = [
        'quick_info' => 'array',
        'boundary_geojson' => 'array',
        'instagram_enabled' => 'boolean',
        'instagram_connected_at' => 'datetime',
        'instagram_last_sync_at' => 'datetime',
    ];

    public function getLogoUrlAttribute(): ?string
    {
        $logo = trim((string) ($this->logo ?? ''));
        if ($logo === '') {
            return null;
        }

        if (Str::startsWith($logo, ['http://', 'https://', '//'])) {
            return $logo;
        }

        return Storage::url($logo);
    }

    public function news(): HasMany
    {
        return $this->hasMany(News::class);
    }

    public function agendas(): HasMany
    {
        return $this->hasMany(Agenda::class);
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(VillageService::class, 'village_id');
    }

    public function galleries(): HasMany
    {
        return $this->hasMany(Gallery::class);
    }

    public function sliders(): HasMany
    {
        return $this->hasMany(Slider::class);
    }

    public function officials(): HasMany
    {
        return $this->hasMany(VillageOfficial::class);
    }

    public function headMessages(): HasMany
    {
        return $this->hasMany(VillageHeadMessage::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(VillageAsset::class);
    }

    public function populations(): HasMany
    {
        return $this->hasMany(VillagePopulation::class);
    }

    public function populationStats(): HasMany
    {
        return $this->hasMany(VillagePopulationStat::class);
    }

    public function landUseAreas(): HasMany
    {
        return $this->hasMany(VillageLandUseArea::class);
    }

    public function apbdesItems(): HasMany
    {
        return $this->hasMany(VillageApbdesItem::class);
    }

    public function apbdesDocuments(): HasMany
    {
        return $this->hasMany(VillageApbdesDocument::class);
    }

    public function infographicItems(): HasMany
    {
        return $this->hasMany(VillageInfographicItem::class);
    }

    public function transparencyItems(): HasMany
    {
        return $this->hasMany(VillageTransparencyItem::class);
    }

    public function transparencyDocuments(): HasMany
    {
        return $this->hasMany(VillageTransparencyDocument::class);
    }

    public function complaints(): HasMany
    {
        return $this->hasMany(Complaint::class);
    }

    public function instagramPosts(): HasMany
    {
        return $this->hasMany(VillageInstagramPost::class)
            ->orderByDesc('posted_at')
            ->orderByDesc('id');
    }
}
