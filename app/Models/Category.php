<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id', 'name', 'slug', 'icon', 'post_price',
        'free_listings_unverified', 'free_listings_verified',
        'fields', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'fields' => 'array',
        'post_price' => 'integer',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function pricingOverrides(): HasMany
    {
        return $this->hasMany(CategoryPricingOverride::class);
    }

    public static function getActiveParents(): Collection
    {
        return self::cachedQuery('parents', function () {
            return static::where('is_active', true)->whereNull('parent_id')->get();
        });
    }

    public static function getAllActive(): Collection
    {
        return self::cachedQuery('all', function () {
            return static::where('is_active', true)->get();
        });
    }

    public static function findBySlugCached(string $slug): ?self
    {
        $cached = Cache::remember("cat.fbs.slug.{$slug}", 86400, function () use ($slug) {
            $cat = static::where('slug', $slug)->where('is_active', true)->first(['id']);
            return $cat?->id;
        });

        if (!$cached) return null;
        return static::find($cached);
    }

    private static function cachedQuery(string $key, callable $query): Collection
    {
        $ids = Cache::remember("cat.fbs.{$key}", 86400, function () use ($query) {
            return $query()->pluck('id')->toArray();
        });

        $results = static::whereIn('id', $ids)->where('is_active', true)->get();

        // If DB has more records than cache (categories added), refresh
        if ($results->count() !== count($ids)) {
            Cache::forget("cat.fbs.parents");
            Cache::forget("cat.fbs.all");
            $results = $query();
        }

        return $results;
    }

    public static function clearCache(): void
    {
        Cache::forget('cat.fbs.parents');
        Cache::forget('cat.fbs.all');
        Cache::forget('cat.fbs.slug.*');
    }
}
