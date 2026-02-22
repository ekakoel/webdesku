<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VillageApbdesItem extends Model
{
    public const TYPES = [
        'pendapatan' => 'Pendapatan',
        'belanja' => 'Belanja',
        'pembiayaan' => 'Pembiayaan',
    ];

    protected $fillable = [
        'village_id',
        'fiscal_year',
        'type',
        'category',
        'amount',
        'notes',
        'sort_order',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'amount' => 'integer',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->type] ?? ucfirst((string) $this->type);
    }
}

