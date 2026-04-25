<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Announcement extends Model
{
    public const TYPE_UMUM = 'umum';
    public const TYPE_EVENT = 'event';
    public const TYPE_ADAT = 'adat';
    public const TYPE_BUDAYA = 'budaya';
    public const TYPE_ACARA = 'acara';
    public const TYPE_LAYANAN = 'layanan';
    public const TYPE_PERATURAN = 'peraturan';
    public const TYPE_KEAMANAN = 'keamanan';
    public const TYPE_BENCANA = 'bencana';
    public const TYPE_LAINNYA = 'lainnya';

    public const TYPES = [
        self::TYPE_UMUM => ['label' => 'Umum', 'color' => '#64748b', 'icon' => 'fa-solid fa-circle-info'],
        self::TYPE_EVENT => ['label' => 'Event', 'color' => '#2563eb', 'icon' => 'fa-solid fa-calendar-days'],
        self::TYPE_ADAT => ['label' => 'Adat', 'color' => '#7c3aed', 'icon' => 'fa-solid fa-landmark-dome'],
        self::TYPE_BUDAYA => ['label' => 'Budaya', 'color' => '#0f766e', 'icon' => 'fa-solid fa-masks-theater'],
        self::TYPE_ACARA => ['label' => 'Acara', 'color' => '#ea580c', 'icon' => 'fa-solid fa-bullhorn'],
        self::TYPE_LAYANAN => ['label' => 'Layanan', 'color' => '#1d4ed8', 'icon' => 'fa-solid fa-handshake'],
        self::TYPE_PERATURAN => ['label' => 'Peraturan', 'color' => '#334155', 'icon' => 'fa-solid fa-scale-balanced'],
        self::TYPE_KEAMANAN => ['label' => 'Keamanan', 'color' => '#b91c1c', 'icon' => 'fa-solid fa-shield-halved'],
        self::TYPE_BENCANA => ['label' => 'Kebencanaan', 'color' => '#be123c', 'icon' => 'fa-solid fa-triangle-exclamation'],
        self::TYPE_LAINNYA => ['label' => 'Lainnya', 'color' => '#475569', 'icon' => 'fa-solid fa-tag'],
    ];

    protected $fillable = [
        'village_id',
        'type',
        'title',
        'content',
        'reference_url',
        'location_name',
        'latitude',
        'longitude',
        'map_url',
        'attachment_path',
        'attachment_name',
        'is_published',
        'published_at',
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

    public function images(): HasMany
    {
        return $this->hasMany(AnnouncementImage::class)->orderBy('sort_order')->orderBy('id');
    }

    public function getAttachmentUrlAttribute(): ?string
    {
        if (!$this->attachment_path) {
            return null;
        }

        if (Str::startsWith($this->attachment_path, ['http://', 'https://', '//'])) {
            return $this->attachment_path;
        }

        return Storage::url($this->attachment_path);
    }

    public function hasLocalAttachment(): bool
    {
        return (bool) $this->attachment_path && !Str::startsWith($this->attachment_path, ['http://', 'https://', '//']);
    }

    public static function typeOptions(): array
    {
        return self::TYPES;
    }

    public function typeMeta(): array
    {
        return self::TYPES[$this->type] ?? self::TYPES[self::TYPE_UMUM];
    }

    public function typeLabel(): string
    {
        return $this->typeMeta()['label'];
    }

    public function typeColor(): string
    {
        return $this->typeMeta()['color'];
    }

    public function typeIcon(): string
    {
        return $this->typeMeta()['icon'];
    }
}
