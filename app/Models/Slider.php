<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Slider extends Model
{
    protected $fillable = [
        'village_id',
        'title',
        'caption',
        'cta_text',
        'cta_url',
        'image_path',
        'image_alt',
        'sort_order',
        'is_active',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }
}
