<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Agenda extends Model
{
    protected $fillable = [
        'village_id',
        'title',
        'description',
        'location',
        'poster_path',
        'latitude',
        'longitude',
        'map_url',
        'start_at',
        'end_at',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'latitude' => 'float',
        'longitude' => 'float',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    public function getPosterUrlAttribute(): ?string
    {
        if (!$this->poster_path) {
            return null;
        }

        if (Str::startsWith($this->poster_path, ['http://', 'https://', '//'])) {
            return $this->poster_path;
        }

        return Storage::url($this->poster_path);
    }

    public function hasLocalPoster(): bool
    {
        return (bool) $this->poster_path && !Str::startsWith($this->poster_path, ['http://', 'https://', '//']);
    }
}
