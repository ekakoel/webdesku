<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VillageProfilePage extends Model
{
    public const SLUG_GAMBARAN = 'gambaran';
    public const SLUG_SEJARAH = 'sejarah';
    public const SLUG_VISIMISI = 'visimisi';
    public const SLUG_ORGANISASI = 'organisasi';

    public const SLUGS = [
        self::SLUG_GAMBARAN => 'Gambaran Umum Desa',
        self::SLUG_SEJARAH => 'Sejarah Desa',
        self::SLUG_VISIMISI => 'Visi dan Misi Desa',
        self::SLUG_ORGANISASI => 'Susunan Organisasi',
    ];

    protected $fillable = [
        'village_id',
        'slug',
        'title',
        'subtitle',
        'content',
        'source_url',
        'payload',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }
}

