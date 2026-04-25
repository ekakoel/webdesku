<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VillageOfficial extends Model
{
    protected $fillable = [
        'village_id',
        'name',
        'position',
        'unit',
        'bio',
        'photo_path',
        'sort_order',
        'is_published',
        'is_highlighted',
        'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'is_highlighted' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }
}

