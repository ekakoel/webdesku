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
        'geografi_iklim' => 'Geografi & Iklim',
        'infrastruktur' => 'Infrastruktur',
        'ekonomi' => 'Ekonomi',
        'pemerintahan' => 'Pemerintahan',
        'sosial' => 'Sosial',
        'kesehatan_sosial' => 'Kesehatan & Sosial',
        'lingkungan' => 'Lingkungan',
    ];

    protected $fillable = [
        'village_id',
        'category',
        'year',
        'title',
        'value',
        'unit',
        'source',
        'notes',
        'description',
        'icon',
        'color',
        'sort_order',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'year' => 'integer',
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
