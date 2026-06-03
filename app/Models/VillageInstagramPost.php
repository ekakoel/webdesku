<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VillageInstagramPost extends Model
{
    protected $fillable = [
        'village_id',
        'instagram_post_id',
        'media_type',
        'caption',
        'media_url',
        'thumbnail_url',
        'permalink',
        'posted_at',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
        'posted_at' => 'datetime',
    ];

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }
}
