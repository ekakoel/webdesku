<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AnnouncementImage extends Model
{
    protected $fillable = [
        'announcement_id',
        'image_path',
        'sort_order',
    ];

    public function announcement(): BelongsTo
    {
        return $this->belongsTo(Announcement::class);
    }

    public function getImageUrlAttribute(): string
    {
        if (Str::startsWith($this->image_path, ['http://', 'https://', '//'])) {
            return $this->image_path;
        }

        return Storage::url($this->image_path);
    }
}

