<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VillageLandUseArea extends Model
{
    protected $fillable = [
        'village_id',
        'fiscal_year',
        'label',
        'area_value',
        'unit',
        'sort_order',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'area_value' => 'decimal:2',
        'fiscal_year' => 'integer',
        'sort_order' => 'integer',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }
}
