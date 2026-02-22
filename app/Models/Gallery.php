<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Gallery extends Model
{
    protected $fillable = [
        'village_id',
        'title',
        'caption',
        'image_url',
        'thumbnail_path',
        'category',
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

    public function getImageUrlAttribute(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        if (Str::startsWith($value, ['http://', 'https://', '//'])) {
            return $value;
        }

        return Storage::url($value);
    }

    public function hasLocalImage(): bool
    {
        $path = (string) $this->getRawOriginal('image_url');

        return $path !== '' && !Str::startsWith($path, ['http://', 'https://', '//']);
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        $thumb = (string) ($this->getRawOriginal('thumbnail_path') ?? '');
        if ($thumb !== '') {
            if (Str::startsWith($thumb, ['http://', 'https://', '//'])) {
                return $thumb;
            }

            return Storage::url($thumb);
        }

        return $this->image_url;
    }
}
