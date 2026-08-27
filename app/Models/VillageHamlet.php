<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VillageHamlet extends Model
{
    protected $fillable = ['village_id', 'name', 'normalized_name', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    public function householdWelfares(): HasMany
    {
        return $this->hasMany(VillageHouseholdWelfare::class);
    }
}
