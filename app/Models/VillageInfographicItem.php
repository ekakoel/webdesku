<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VillageInfographicItem extends Model
{
    protected $fillable = [
        'village_id',
        'title',
        'value',
        'unit',
        'description',
        'icon',
        'color',
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
}

