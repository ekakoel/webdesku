<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class News extends Model
{
    protected $fillable = [
        'village_id',
        'created_by',
        'title',
        'slug',
        'content',
        'thumbnail',
        'view_count',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'view_count' => 'integer',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function images(): HasMany
    {
        return $this->hasMany(NewsImage::class)->orderBy('sort_order')->orderBy('id');
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        if (!$this->thumbnail) {
            return null;
        }

        if (Str::startsWith($this->thumbnail, ['http://', 'https://', '//'])) {
            return $this->thumbnail;
        }

        return Storage::url($this->thumbnail);
    }

    public function hasLocalThumbnail(): bool
    {
        return (bool) $this->thumbnail && !Str::startsWith($this->thumbnail, ['http://', 'https://', '//']);
    }
}
