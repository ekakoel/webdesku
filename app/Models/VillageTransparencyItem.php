<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VillageTransparencyItem extends Model
{
    public const CATEGORIES = [
        'apbdes' => 'APBDes',
        'realisasi' => 'Realisasi Anggaran',
        'program' => 'Program/Kegiatan',
        'pengadaan' => 'Pengadaan',
        'peraturan' => 'Peraturan Desa',
        'laporan' => 'Laporan Publik',
    ];

    protected $fillable = [
        'village_id',
        'fiscal_year',
        'category',
        'title',
        'amount',
        'description',
        'document_url',
        'sort_order',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'amount' => 'integer',
        'fiscal_year' => 'integer',
    ];

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    public static function categoryOptions(): array
    {
        return self::CATEGORIES;
    }

    public function categoryLabel(): string
    {
        return self::CATEGORIES[$this->category] ?? ucfirst((string) $this->category);
    }
}

