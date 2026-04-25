<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VillageService extends Model
{
    protected $table = 'services';

    protected $fillable = [
        'village_id',
        'name',
        'slug',
        'description',
        'requirements',
        'process',
        'sla_target_hours',
        'icon',
        'is_featured',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
        'sla_target_hours' => 'integer',
        'published_at' => 'datetime',
    ];

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    public function requests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class, 'service_id');
    }

    public function requirementsList(): array
    {
        if (!$this->requirements) {
            return [];
        }

        $lines = preg_split('/\r\n|\r|\n/', (string) $this->requirements) ?: [];

        return array_values(array_filter(array_map(static function (string $line): string {
            return trim(ltrim($line, "-\t 0123456789.)"));
        }, $lines)));
    }

    public function processList(): array
    {
        if (!$this->process) {
            return [];
        }

        $lines = preg_split('/\r\n|\r|\n/', (string) $this->process) ?: [];

        return array_values(array_filter(array_map(static function (string $line): string {
            return trim(ltrim($line, "-\t 0123456789.)"));
        }, $lines)));
    }
}
