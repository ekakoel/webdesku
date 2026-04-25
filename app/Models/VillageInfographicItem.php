<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VillageInfographicItem extends Model
{
    public const CATEGORIES = [
        'umum' => 'Umum',
        'layanan' => 'Layanan',
        'kelembagaan' => 'Kelembagaan',
        'ekonomi' => 'Ekonomi',
        'sosial' => 'Sosial',
        'lingkungan' => 'Lingkungan',
    ];

    protected $fillable = [
        'village_id',
        'category',
        'title',
        'value',
        'unit',
        'description',
        'icon',
        'color',
        'sort_order',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    public static function categoryOptions(): array
    {
        return self::CATEGORIES;
    }

    public function categoryLabel(): string
    {
        return self::CATEGORIES[$this->category] ?? ucfirst((string) $this->category);
    }
}
