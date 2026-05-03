<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Listing extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia, HasFactory;

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photos')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->onlyKeepLatest(5);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(300)
            ->sharpen(10)
            ->nonQueued();

        $this->addMediaConversion('card')
            ->width(600)
            ->height(400)
            ->nonQueued();
    }

    protected $fillable = [
        'user_id', 'category_id', 'city_id',
        'title', 'slug', 'description', 'price',
        'condition', 'status', 'is_featured',
        'total_views', 'unique_views',
        'expires_at', 'sold_at',
        'reference_id',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'expires_at' => 'datetime',
            'sold_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class);
    }

    public function transactionReceipts(): HasMany
    {
        return $this->hasMany(TransactionReceipt::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function viewLogs(): HasMany
    {
        return $this->hasMany(ListingViewLog::class);
    }

    public function creditTransactions(): MorphMany
    {
        return $this->morphMany(CreditTransaction::class, 'reference');
    }

    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'listing_user');
    }

    public function activePromotion(): HasMany
    {
        return $this->hasMany(ListingPromotion::class)
            ->where('is_active', true)
            ->where('expires_at', '>', now());
    }

    public function promotions(): HasMany
    {
        return $this->hasMany(ListingPromotion::class);
    }

    public function scopeFavoritedBy(Builder $query, int $userId): Builder
    {
        return $query->whereHas('favoritedBy', fn($q) => $q->where('user_id', $userId));
    }

    public function isFavoritedByAuth(): bool
    {
        if (!auth()->check()) return false;
        return $this->favoritedBy()->where('user_id', auth()->id())->exists();
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            return $query->whereRaw(
                'MATCH (title, description) AGAINST (? IN BOOLEAN MODE)',
                [$term . '*']
            );
        }

        // SQLite fallback — LIKE search
        $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $term) . '%';
        $normalized = preg_replace('/\s+/', '', $term);
        $normalizedLike = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $normalized) . '%';

        return $query->where(function (Builder $q) use ($like, $normalizedLike) {
            $q->where('title', 'like', $like)
              ->orWhere('description', 'like', $like)
              ->orWhereRaw("REPLACE(title, ' ', '') LIKE ?", [$normalizedLike])
              ->orWhereRaw("REPLACE(description, ' ', '') LIKE ?", [$normalizedLike]);
        });
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }
    public function scopeInCity(Builder $query, string $citySlug): Builder
    {
        if (blank($citySlug)) {
            return $query;
        }

        return $query->whereHas('city', fn($q) => $q->where('slug', $citySlug));
    }
    public function scopeInCategory(Builder $query, string $categorySlug): Builder
    {
        return $query->whereHas('category', function ($q) use ($categorySlug) {
            $q->where('slug', $categorySlug)
              ->orWhereHas('parent', fn ($p) => $p->where('slug', $categorySlug));
        });
    }

    public function scopePriceBetween(Builder $query, ?int $min, ?int $max): Builder
    {
        return $query
            ->when($min, fn($q) => $q->where('price', '>=', $min * 100))
            ->when($max, fn($q) => $q->where('price', '<=', $max * 100));
    }

    public function scopeWithCondition(Builder $query, ?string $condition): Builder
    {
        return $query->when($condition, fn($q) => $q->where('condition', $condition));
    }

    public function scopeSortBy(Builder $query, string $sort): Builder
    {
        // Promoted listings always float to the top
        $query->orderByRaw('CASE WHEN featured_until IS NOT NULL AND featured_until > NOW() THEN 0 ELSE 1 END');

        return match ($sort) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'oldest' => $query->oldest(),
            default => $query->latest(),
        };
    }

    protected static function booted(): void
    {
        static::creating(function (Listing $listing) {
            if (empty($listing->reference_id)) {
                $listing->reference_id = 'ISK-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 7));
            }
        });
    }
}
