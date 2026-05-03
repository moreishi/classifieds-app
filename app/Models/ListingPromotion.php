<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $listing_id
 * @property int $user_id
 * @property string $plan
 * @property int $amount_paid
 * @property \Carbon\Carbon $starts_at
 * @property \Carbon\Carbon $expires_at
 * @property bool $is_active
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @property-read Listing $listing
 * @property-read User $user
 */
class ListingPromotion extends Model
{
    protected $fillable = [
        'listing_id',
        'user_id',
        'plan',
        'amount_paid',
        'starts_at',
        'expires_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'amount_paid' => 'integer',
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Available bump plans with their duration and price.
     * Prices in centavos (e.g. 5000 = ₱50).
     */
    public const array PLANS = [
        'bump_7d' => [
            'label' => '7 Days',
            'price' => 5000,     // ₱50
            'days' => 7,
        ],
        'bump_14d' => [
            'label' => '14 Days',
            'price' => 8000,     // ₱80 (₱40 off)
            'days' => 14,
        ],
        'bump_30d' => [
            'label' => '30 Days',
            'price' => 14000,    // ₱140 (₱60 off)
            'days' => 30,
        ],
    ];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: only active promotions (not expired, marked active).
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where('expires_at', '>', now());
    }

    /**
     * Deactivate expired promotions and sync the listings table.
     */
    public static function expireOld(): int
    {
        $expired = static::where('is_active', true)
            ->where('expires_at', '<=', now())
            ->update(['is_active' => false]);

        // Sync: clear featured_until on listings with no active promotions
        Listing::where('featured_until', '<=', now())
            ->orWhere(function ($q) {
                $q->whereNotNull('featured_until')
                    ->whereDoesntHave('activePromotion');
            })
            ->update(['featured_until' => null]);

        return $expired;
    }
}
