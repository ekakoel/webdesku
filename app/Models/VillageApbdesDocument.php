<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class VillageApbdesDocument extends Model
{
    protected $fillable = [
        'village_id',
        'fiscal_year',
        'title',
        'description',
        'document_path',
        'document_url',
        'sort_order',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'fiscal_year' => 'integer',
        'sort_order' => 'integer',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    public function documentLink(): ?string
    {
        if ($this->document_url) {
            return $this->document_url;
        }
        if ($this->document_path) {
            return Storage::url($this->document_path);
        }

        return null;
    }
}
