<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ServiceRequest extends Model
{
    protected $fillable = [
        'village_id',
        'service_id',
        'ticket_code',
        'public_token',
        'applicant_name',
        'nik',
        'kk_number',
        'phone',
        'email',
        'address',
        'description',
        'attachment_path',
        'status',
        'status_note',
        'submitted_at',
        'processed_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(VillageService::class, 'service_id');
    }

    public function getAttachmentUrlAttribute(): ?string
    {
        if (!$this->attachment_path) {
            return null;
        }

        return Storage::url($this->attachment_path);
    }
}
