<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VillageAsset extends Model
{
    public const TYPES = [
        'aset_desa' => [
            'label' => 'Aset Desa',
            'color' => '#1d4ed8',
        ],
        'umkm' => [
            'label' => 'UMKM',
            'color' => '#16a34a',
        ],
        'fasilitas_umum' => [
            'label' => 'Fasilitas Umum',
            'color' => '#f59e0b',
        ],
        'pasar' => [
            'label' => 'Pasar',
            'color' => '#ef4444',
        ],
        'pendidikan' => [
            'label' => 'Pendidikan',
            'color' => '#06b6d4',
        ],
        'kesehatan' => [
            'label' => 'Kesehatan',
            'color' => '#ec4899',
        ],
        'lainnya' => [
            'label' => 'Lainnya',
            'color' => '#64748b',
        ],
    ];

    protected $fillable = [
        'village_id',
        'name',
        'type',
        'subcategory',
        'description',
        'address',
        'latitude',
        'longitude',
        'map_url',
        'icon_path',
        'contact_person',
        'contact_phone',
        'is_published',
        'published_at',
        'sort_order',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->type]['label'] ?? 'Lainnya';
    }

    public function typeColor(): string
    {
        return self::TYPES[$this->type]['color'] ?? '#64748b';
    }

    public static function typeOptions(): array
    {
        return self::TYPES;
    }

    public function getIconUrlAttribute(): ?string
    {
        $path = (string) ($this->icon_path ?? '');
        if ($path === '') {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://', '//'])) {
            return $path;
        }

        return Storage::url($path);
    }

    public function hasLocalIcon(): bool
    {
        $path = (string) ($this->icon_path ?? '');

        return $path !== '' && !Str::startsWith($path, ['http://', 'https://', '//']);
    }
}
