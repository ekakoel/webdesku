<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

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
    ];

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

    public function apbdesItems(): HasMany
    {
        return $this->hasMany(VillageApbdesItem::class);
    }

    public function infographicItems(): HasMany
    {
        return $this->hasMany(VillageInfographicItem::class);
    }
}
