<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class LiveBeacon extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'description',
        'latitude',
        'longitude',
        'location_name',
        'city_id',
        'status',
        'started_at',
        'ended_at',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('snapshot')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(300)
            ->nonQueued();

        $this->addMediaConversion('card')
            ->width(600)
            ->height(400)
            ->nonQueued();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'live')
            ->where('started_at', '>', now()->subHours(2));
    }

    public function scopeLive(Builder $query): Builder
    {
        return $query->where('status', 'live');
    }

    public function end(): void
    {
        $this->update([
            'status' => 'ended',
            'ended_at' => now(),
        ]);
    }

    public function isExpired(): bool
    {
        return $this->started_at && $this->started_at->lte(now()->subHours(2));
    }

    public static function expireStale(): int
    {
        $count = static::where('status', 'live')
            ->where('started_at', '<=', now()->subHours(2))
            ->update([
                'status' => 'ended',
                'ended_at' => now(),
            ]);

        return $count;
    }

    protected static function booted(): void
    {
        static::creating(function (LiveBeacon $beacon) {
            if (empty($beacon->started_at)) {
                $beacon->started_at = now();
            }
        });
    }
}
