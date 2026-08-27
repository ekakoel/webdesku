<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VillageHouseholdWelfare extends Model
{
    public const DECILES = ['D1', 'D2', 'D3', 'D4', 'D5'];

    public const GENDERS = ['laki_laki' => 'Laki-laki', 'perempuan' => 'Perempuan'];

    protected $fillable = ['village_id', 'village_hamlet_id', 'year', 'reference_code', 'decile', 'head_gender', 'is_outside_village', 'requires_verification', 'source', 'notes', 'is_published', 'published_at'];

    protected $casts = ['is_outside_village' => 'boolean', 'requires_verification' => 'boolean', 'is_published' => 'boolean', 'published_at' => 'datetime'];

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    public function hamlet(): BelongsTo
    {
        return $this->belongsTo(VillageHamlet::class, 'village_hamlet_id');
    }
}
