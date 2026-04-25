<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VillagePopulationStat extends Model
{
    public const CATEGORY_UMUR = 'umur';
    public const CATEGORY_PENDIDIKAN = 'pendidikan';
    public const CATEGORY_PEKERJAAN = 'pekerjaan';
    public const CATEGORY_AGAMA = 'agama';
    public const CATEGORY_STATUS_KAWIN = 'status_kawin';

    public const CATEGORIES = [
        self::CATEGORY_UMUR => [
            'label' => 'Kelompok Umur',
            'icon' => 'fa-solid fa-children',
            'color' => '#2563eb',
        ],
        self::CATEGORY_PENDIDIKAN => [
            'label' => 'Pendidikan',
            'icon' => 'fa-solid fa-graduation-cap',
            'color' => '#16a34a',
        ],
        self::CATEGORY_PEKERJAAN => [
            'label' => 'Pekerjaan',
            'icon' => 'fa-solid fa-briefcase',
            'color' => '#f59e0b',
        ],
        self::CATEGORY_AGAMA => [
            'label' => 'Agama',
            'icon' => 'fa-solid fa-place-of-worship',
            'color' => '#8b5cf6',
        ],
        self::CATEGORY_STATUS_KAWIN => [
            'label' => 'Status Perkawinan',
            'icon' => 'fa-solid fa-heart',
            'color' => '#ec4899',
        ],
    ];

    protected $fillable = [
        'village_id',
        'year',
        'category',
        'label',
        'value',
        'unit',
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
        return self::CATEGORIES[$this->category]['label'] ?? ucfirst(str_replace('_', ' ', $this->category));
    }
}

