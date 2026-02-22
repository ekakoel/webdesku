<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VillagePopulation extends Model
{
    protected $fillable = [
        'village_id',
        'year',
        'male',
        'female',
        'households',
        'notes',
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

    public function total(): int
    {
        return (int) $this->male + (int) $this->female;
    }
}

