<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'station_id',
        'name',
        'advertiser',
        'image_path',
        'target_url',
        'placement',
        'sort_order',
        'starts_at',
        'ends_at',
        'is_active',
        'impressions',
        'clicks',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
        'impressions' => 'integer',
        'clicks' => 'integer',
    ];

    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }

    public function scopeCurrentlyActive(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->where(function (Builder $query) {
                $query->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function (Builder $query) {
                $query->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            });
    }

    public function getCtrAttribute(): float
    {
        if ($this->impressions < 1) {
            return 0;
        }

        return round(($this->clicks / $this->impressions) * 100, 2);
    }
}
