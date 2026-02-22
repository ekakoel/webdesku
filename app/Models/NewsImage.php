<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NewsImage extends Model
{
    protected $fillable = [
        'news_id',
        'image_path',
        'caption',
        'sort_order',
    ];

    public function news(): BelongsTo
    {
        return $this->belongsTo(News::class);
    }

    public function getImageUrlAttribute(): string
    {
        if (Str::startsWith($this->image_path, ['http://', 'https://', '//'])) {
            return $this->image_path;
        }

        return Storage::url($this->image_path);
    }
}
